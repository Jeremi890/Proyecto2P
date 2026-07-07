<?php
require_once __DIR__ . '/Controller.php';

/**
 * CONTROLADOR: ProductoController
 * Módulo Central de Inventario (Gestión de Productos, Precios, Stock y Alertas)
 */
class ProductoController extends Controller {
    /** @var Producto */
    private $model;
    
    /** @var Categoria */
    private $categoriaModel;

    public function __construct() {
        $this->model = new Producto();
        $this->categoriaModel = new Categoria();
    }

    /**
     * Catálogo principal de productos (Soporta filtrado por categoría y búsqueda web)
     */
    public function index() {
        $categoria_id = !empty($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : null;
        $search = !empty($_GET['search']) ? trim($_GET['search']) : null;

        $productos = $this->model->getAll($categoria_id, $search);
        $categorias = $this->categoriaModel->getAll();

        $this->view('productos/index', [
            'page_title' => 'Catálogo de Productos | NexusStock',
            'active_menu' => 'productos',
            'productos' => $productos,
            'categorias' => $categorias,
            'filtro_cat' => $categoria_id,
            'filtro_search' => $search
        ]);
    }

    /**
     * Registrar nuevo producto al catálogo
     */
    public function create() {
        $error = null;
        $categorias = $this->categoriaModel->getAll();

        if (empty($categorias)) {
            set_flash_message('warning', '⚠️ Debes crear al menos una categoría antes de registrar productos.');
            redirect('categoria/create');
        }

        $data = [
            'categoria_id' => $categorias[0]->id ?? '',
            'codigo' => '', 'nombre' => '', 'descripcion' => '',
            'precio_compra' => '0.00', 'precio_venta' => '0.00',
            'stock' => 0, 'stock_minimo' => 5, 'unidad_medida' => 'Unidad',
            'imagen_url' => 'default.png', 'estado' => 1
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'categoria_id' => (int)($_POST['categoria_id'] ?? 0),
                'codigo' => strtoupper(trim($_POST['codigo'] ?? '')),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'precio_compra' => (float)($_POST['precio_compra'] ?? 0),
                'precio_venta' => (float)($_POST['precio_venta'] ?? 0),
                'stock' => (int)($_POST['stock'] ?? 0),
                'stock_minimo' => (int)($_POST['stock_minimo'] ?? 5),
                'unidad_medida' => trim($_POST['unidad_medida'] ?? 'Unidad'),
                'imagen_url' => trim($_POST['imagen_url'] ?? 'default.png'),
                'estado' => isset($_POST['estado']) ? 1 : 0
            ];

            // 1. Validaciones de obligatoriedad
            if (empty($data['codigo']) || empty($data['nombre']) || empty($data['categoria_id'])) {
                $error = 'Por favor completa el código SKU, nombre y categoría del producto.';
            } 
            // 2. Validación de coherencia en números y precios
            elseif ($data['precio_compra'] < 0 || $data['precio_venta'] < 0 || $data['stock'] < 0) {
                $error = 'Los precios y el stock no pueden ser valores negativos.';
            } 
            // 3. Validación de código duplicado en el catálogo
            elseif ($this->model->existsByCodigo($data['codigo'])) {
                $error = 'El código SKU/Barras "' . htmlspecialchars($data['codigo']) . '" ya está asignado a otro producto.';
            } 
            else {
                if ($this->model->create($data)) {
                    set_flash_message('success', '✅ Producto "' . htmlspecialchars($data['nombre']) . '" ingresado al inventario exitosamente.');
                    redirect('producto/index');
                } else {
                    $error = 'Ocurrió un error en la base de datos al guardar el producto.';
                }
            }
        }

        $this->view('productos/create', [
            'page_title' => 'Nuevo Producto | NexusStock',
            'active_menu' => 'productos',
            'data' => $data,
            'categorias' => $categorias,
            'error' => $error
        ]);
    }

    /**
     * Editar producto existente en el catálogo
     */
    public function edit($id = null) {
        if (!$id) {
            redirect('producto/index');
        }

        $producto = $this->model->getById($id);
        if (!$producto) {
            set_flash_message('danger', '❌ El producto solicitado no fue encontrado.');
            redirect('producto/index');
        }

        $error = null;
        $categorias = $this->categoriaModel->getAll();
        $data = (array)$producto;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'categoria_id' => (int)($_POST['categoria_id'] ?? 0),
                'codigo' => strtoupper(trim($_POST['codigo'] ?? '')),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'precio_compra' => (float)($_POST['precio_compra'] ?? 0),
                'precio_venta' => (float)($_POST['precio_venta'] ?? 0),
                'stock' => (int)($_POST['stock'] ?? 0),
                'stock_minimo' => (int)($_POST['stock_minimo'] ?? 5),
                'unidad_medida' => trim($_POST['unidad_medida'] ?? 'Unidad'),
                'imagen_url' => trim($_POST['imagen_url'] ?? 'default.png'),
                'estado' => isset($_POST['estado']) ? 1 : 0
            ];

            if (empty($data['codigo']) || empty($data['nombre']) || empty($data['categoria_id'])) {
                $error = 'Por favor completa el código SKU, nombre y categoría.';
            } elseif ($data['precio_compra'] < 0 || $data['precio_venta'] < 0 || $data['stock'] < 0) {
                $error = 'Precios y stock deben ser mayores o iguales a cero.';
            } elseif ($this->model->existsByCodigo($data['codigo'], $id)) {
                $error = 'El código "' . htmlspecialchars($data['codigo']) . '" pertenece a otro artículo del catálogo.';
            } else {
                if ($this->model->update($id, $data)) {
                    set_flash_message('success', '✅ Datos del producto actualizados correctamente.');
                    redirect('producto/index');
                } else {
                    $error = 'No se pudieron actualizar los cambios en la base de datos.';
                }
            }
        }

        $this->view('productos/edit', [
            'page_title' => 'Editar Producto | NexusStock',
            'active_menu' => 'productos',
            'data' => $data,
            'categorias' => $categorias,
            'id' => $id,
            'error' => $error
        ]);
    }

    /**
     * Eliminar producto del inventario
     */
    public function delete($id = null) {
        if ($id) {
            $producto = $this->model->getById($id);
            if ($producto) {
                if ($this->model->delete($id)) {
                    set_flash_message('success', '🗑️ Producto "' . htmlspecialchars($producto->nombre) . '" eliminado del catálogo.');
                } else {
                    set_flash_message('danger', '❌ No se pudo eliminar el producto porque tiene transacciones de inventario registradas.');
                }
            }
        }
        redirect('producto/index');
    }
}
