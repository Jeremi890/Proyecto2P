<?php
require_once __DIR__ . '/Controller.php';

/**
 * CONTROLADOR: ProveedorController
 * Módulo de Gestión de Proveedores (Directorio comercial y validación RUC/NIT)
 */
class ProveedorController extends Controller {
    /** @var Proveedor */
    private $model;

    public function __construct() {
        $this->model = new Proveedor();
    }

    /**
     * Listado principal de proveedores
     */
    public function index() {
        $proveedores = $this->model->getAll();
        
        $this->view('proveedores/index', [
            'page_title' => 'Gestión de Proveedores | NexusStock',
            'active_menu' => 'proveedores',
            'proveedores' => $proveedores
        ]);
    }

    /**
     * Crear nuevo proveedor
     */
    public function create() {
        $error = null;
        $data = [
            'ruc' => '', 'nombre_empresa' => '', 'contacto_persona' => '', 
            'email' => '', 'telefono' => '', 'direccion' => '', 'estado' => 1
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ruc' => trim($_POST['ruc'] ?? ''),
                'nombre_empresa' => trim($_POST['nombre_empresa'] ?? ''),
                'contacto_persona' => trim($_POST['contacto_persona'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'estado' => isset($_POST['estado']) ? 1 : 0
            ];

            // 1. Validaciones Backend obligatorias
            if (empty($data['ruc']) || empty($data['nombre_empresa']) || empty($data['email']) || empty($data['telefono'])) {
                $error = 'Por favor completa todos los campos obligatorios (*): RUC, Empresa, Email y Teléfono.';
            } 
            // 2. Validación de formato de correo electrónico
            elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'El formato del correo electrónico ingresado no es válido.';
            } 
            // 3. Validación de RUC duplicado en base de datos
            elseif ($this->model->existsByRuc($data['ruc'])) {
                $error = 'El RUC/NIT "' . htmlspecialchars($data['ruc']) . '" ya se encuentra registrado para otra empresa.';
            } 
            else {
                if ($this->model->create($data)) {
                    set_flash_message('success', '✅ Proveedor "' . htmlspecialchars($data['nombre_empresa']) . '" registrado satisfactoriamente.');
                    redirect('proveedor/index');
                } else {
                    $error = 'Ocurrió un error al intentar guardar el proveedor en la base de datos.';
                }
            }
        }

        $this->view('proveedores/create', [
            'page_title' => 'Nuevo Proveedor | NexusStock',
            'active_menu' => 'proveedores',
            'data' => $data,
            'error' => $error
        ]);
    }

    /**
     * Editar datos del proveedor
     */
    public function edit($id = null) {
        if (!$id) {
            redirect('proveedor/index');
        }

        $proveedor = $this->model->getById($id);
        if (!$proveedor) {
            set_flash_message('danger', '❌ El proveedor especificado no existe.');
            redirect('proveedor/index');
        }

        $error = null;
        $data = (array)$proveedor;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ruc' => trim($_POST['ruc'] ?? ''),
                'nombre_empresa' => trim($_POST['nombre_empresa'] ?? ''),
                'contacto_persona' => trim($_POST['contacto_persona'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'estado' => isset($_POST['estado']) ? 1 : 0
            ];

            if (empty($data['ruc']) || empty($data['nombre_empresa']) || empty($data['email']) || empty($data['telefono'])) {
                $error = 'Por favor completa los campos obligatorios.';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'El correo electrónico no tiene un formato válido.';
            } elseif ($this->model->existsByRuc($data['ruc'], $id)) {
                $error = 'El RUC "' . htmlspecialchars($data['ruc']) . '" pertenece a otro proveedor registrado.';
            } else {
                if ($this->model->update($id, $data)) {
                    set_flash_message('success', '✅ Información del proveedor actualizada correctamente.');
                    redirect('proveedor/index');
                } else {
                    $error = 'Error al actualizar el registro en la base de datos.';
                }
            }
        }

        $this->view('proveedores/edit', [
            'page_title' => 'Editar Proveedor | NexusStock',
            'active_menu' => 'proveedores',
            'data' => $data,
            'id' => $id,
            'error' => $error
        ]);
    }

    /**
     * Eliminar proveedor del sistema
     */
    public function delete($id = null) {
        if ($id) {
            $proveedor = $this->model->getById($id);
            if ($proveedor) {
                if ($this->model->delete($id)) {
                    set_flash_message('success', '🗑️ Proveedor "' . htmlspecialchars($proveedor->nombre_empresa) . '" eliminado correctamente.');
                } else {
                    set_flash_message('danger', '❌ No se pudo eliminar el proveedor.');
                }
            }
        }
        redirect('proveedor/index');
    }
}
