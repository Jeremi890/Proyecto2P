<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Categoria.php';

/**
 * CONTROLADOR: MovimientoController
 * Módulo de Operaciones Transaccionales (Entradas, Salidas y Control Automático de Stock)
 */
class MovimientoController extends Controller {
    /** @var Movimiento */
    private $model;
    
    /** @var Producto */
    private $productoModel;
    
    /** @var Proveedor */
    private $proveedorModel;
    
    /** @var Categoria */
    private $categoriaModel;

    public function __construct() {
        $this->model = new Movimiento();
        $this->productoModel = new Producto();
        $this->proveedorModel = new Proveedor();
        $this->categoriaModel = new Categoria();
    }

    /**
     * Historial de transacciones del almacén
     */
    public function index() {
        $movimientos = $this->model->getAll(100); // Obtener los últimos 100 movimientos
        
        $this->view('movimientos/index', [
            'page_title' => 'Historial de Movimientos de Stock | NexusStock',
            'active_menu' => 'movimientos',
            'movimientos' => $movimientos
        ]);
    }

    /**
     * Registrar nueva transacción (Entrada de compra o Salida de venta/retiro)
     */
    public function create() {
        $error = null;
        $productos = $this->productoModel->getAll();
        $proveedores = $this->proveedorModel->getAll();
        $categorias = $this->categoriaModel->getAll();

        if (empty($categorias)) {
            set_flash_message('warning', '⚠️ Debes crear al menos una categoría antes de registrar ingresos en Movimientos.');
            redirect('categoria/create');
        }

        $data = [
            'producto_id' => $productos[0]->id ?? '',
            'proveedor_id' => $proveedores[0]->id ?? '',
            'tipo_movimiento' => 'ENTRADA',
            'cantidad' => 1,
            'costo_unitario' => '0.00',
            'notas' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'producto_id' => (int)($_POST['producto_id'] ?? 0),
                'proveedor_id' => !empty($_POST['proveedor_id']) ? (int)$_POST['proveedor_id'] : null,
                'tipo_movimiento' => trim($_POST['tipo_movimiento'] ?? 'ENTRADA'),
                'cantidad' => (int)($_POST['cantidad'] ?? 0),
                'costo_unitario' => (float)($_POST['costo_unitario'] ?? 0),
                'notas' => trim($_POST['notas'] ?? ''),
                'comprobante_url' => null
            ];

            $productoSeleccionado = null;
            $modo_ingreso = $_POST['modo_ingreso'] ?? 'existente';

            if ($data['tipo_movimiento'] === 'ENTRADA' && $modo_ingreso === 'nuevo') {
                // El usuario está creando un artículo nuevo desde el ingreso de mercadería
                $codigo = strtoupper(trim($_POST['nuevo_codigo'] ?? ''));
                $nombre = trim($_POST['nuevo_nombre'] ?? '');
                $categoria_id = (int)($_POST['nuevo_categoria_id'] ?? 0);
                $unidad = trim($_POST['nuevo_unidad'] ?? 'Unidad');
                $precio_venta = (float)($_POST['nuevo_precio_venta'] ?? 0);
                
                if (empty($codigo) || empty($nombre) || empty($categoria_id)) {
                    $error = 'Por favor completa el código SKU, nombre y categoría para el nuevo producto.';
                } elseif ($this->productoModel->existsByCodigo($codigo)) {
                    $error = 'El código SKU "' . htmlspecialchars($codigo) . '" ya existe en el catálogo. Selecciona "Reponer Producto Existente".';
                } else {
                    $nuevoProductoData = [
                        'categoria_id' => $categoria_id,
                        'codigo' => $codigo,
                        'nombre' => $nombre,
                        'descripcion' => trim($_POST['nuevo_descripcion'] ?? ''),
                        'precio_compra' => $data['costo_unitario'],
                        'precio_venta' => $precio_venta,
                        'stock' => 0,
                        'stock_minimo' => (int)($_POST['nuevo_stock_minimo'] ?? 5),
                        'unidad_medida' => $unidad,
                        'imagen_url' => 'default.png',
                        'estado' => 1
                    ];
                    
                    if ($this->productoModel->create($nuevoProductoData)) {
                        $prodCreado = $this->productoModel->query("SELECT id, stock, nombre FROM productos WHERE codigo = :cod LIMIT 1", [':cod' => $codigo])->fetch();
                        if ($prodCreado) {
                            $data['producto_id'] = (int)$prodCreado->id;
                            $productoSeleccionado = $prodCreado;
                        } else {
                            $error = 'Error al recuperar el ID del producto recién creado.';
                        }
                    } else {
                        $error = 'Error al guardar el nuevo producto en la base de datos.';
                    }
                }
            } else {
                $productoSeleccionado = $this->productoModel->getById($data['producto_id']);
            }

            // Manejo de subida de archivo (comprobante)
            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/movimientos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Limpiar nombre del archivo
                $safeName = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['comprobante']['name']));
                $fileName = time() . '_' . $safeName;
                $targetFilePath = $uploadDir . $fileName;
                
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                $allowTypes = array('jpg', 'png', 'jpeg', 'pdf');
                
                if (in_array($fileType, $allowTypes)) {
                    if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $targetFilePath)) {
                        $data['comprobante_url'] = $fileName;
                    } else {
                        $error = 'Error al guardar físicamente el archivo adjunto.';
                    }
                } else {
                    $error = 'Formato de archivo no permitido. Solo PDF, JPG, PNG.';
                }
            }

            // 1. Validaciones Backend básicas
            if ($error !== null) {
                // Si hubo error en la subida, se mantiene el mensaje
            } elseif (empty($data['producto_id']) || empty($data['tipo_movimiento']) || $data['cantidad'] <= 0) {
                $error = 'Por favor selecciona un producto e indica una cantidad válida mayor a cero.';
            } 
            // 2. Validación de Lógica de Negocio: Evitar stock negativo al realizar una SALIDA
            elseif ($data['tipo_movimiento'] === 'SALIDA' && $productoSeleccionado && $data['cantidad'] > $productoSeleccionado->stock) {
                $error = '🚫 Stock Insuficiente: Intentas retirar ' . $data['cantidad'] . ' unidades de "' . htmlspecialchars($productoSeleccionado->nombre) . '", pero el almacén solo dispone de ' . $productoSeleccionado->stock . ' unidades.';
            } 
            else {
                try {
                    // Ejecutar registro con transacción SQL
                    if ($this->model->registrar($data)) {
                        $tipoTexto = ($data['tipo_movimiento'] === 'ENTRADA') ? '📥 Entrada (Compra)' : '📤 Salida (Venta/Retiro)';
                        set_flash_message('success', '✅ Transacción registrada con éxito: ' . $tipoTexto . ' de ' . $data['cantidad'] . ' unidades. Stock actualizado.');
                        redirect('movimiento/index');
                    } else {
                        $error = 'No se pudo procesar la transacción en el servidor.';
                    }
                } catch (Exception $e) {
                    $error = 'Error crítico en base de datos: ' . $e->getMessage();
                }
            }
        }

        $this->view('movimientos/create', [
            'page_title' => 'Nueva Transacción de Inventario | NexusStock',
            'active_menu' => 'movimientos',
            'data' => $data,
            'productos' => $productos,
            'proveedores' => $proveedores,
            'categorias' => $categorias,
            'error' => $error
        ]);
    }
}
