<?php
require_once __DIR__ . '/Controller.php';

/**
 * CONTROLADOR: CategoriaController
 * Módulo de Gestión de Categorías (CRUD completo + Validaciones Backend)
 */
class CategoriaController extends Controller {
    /** @var Categoria */
    private $model;

    public function __construct() {
        $this->model = new Categoria();
    }

    /**
     * Listado principal de categorías (GET /categoria/index)
     */
    public function index() {
        $categorias = $this->model->getAll();
        
        $this->view('categorias/index', [
            'page_title' => 'Gestión de Categorías | NexusStock',
            'active_menu' => 'categorias',
            'categorias' => $categorias
        ]);
    }

    /**
     * Crear nueva categoría (GET para mostrar form, POST para procesar)
     */
    public function create() {
        $error = null;
        $data = ['nombre' => '', 'descripcion' => '', 'icono' => 'folder', 'estado' => 1];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'icono' => 'bx-folder',
                'estado' => isset($_POST['estado']) ? 1 : 0
            ];

            // 1. Validación Backend: Campos obligatorios
            if (empty($data['nombre'])) {
                $error = 'El nombre de la categoría es obligatorio.';
            } 
            // 2. Validación Backend: Evitar nombres duplicados
            elseif ($this->model->existsByName($data['nombre'])) {
                $error = 'Ya existe una categoría registrada con el nombre "' . htmlspecialchars($data['nombre']) . '".';
            } 
            else {
                // Guardar en MySQL
                if ($this->model->create($data)) {
                    set_flash_message('success', '✅ Categoría "' . htmlspecialchars($data['nombre']) . '" creada con éxito.');
                    redirect('categoria/index');
                } else {
                    $error = 'Ocurrió un error inesperado al guardar en la base de datos.';
                }
            }
        }

        $this->view('categorias/create', [
            'page_title' => 'Nueva Categoría | NexusStock',
            'active_menu' => 'categorias',
            'data' => $data,
            'error' => $error
        ]);
    }

    /**
     * Editar categoría existente (GET para mostrar form, POST para actualizar)
     */
    public function edit($id = null) {
        if (!$id) {
            redirect('categoria/index');
        }

        $categoria = $this->model->getById($id);
        if (!$categoria) {
            set_flash_message('danger', '❌ La categoría solicitada no existe.');
            redirect('categoria/index');
        }

        $error = null;
        $data = (array)$categoria;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'icono' => 'bx-folder',
                'estado' => isset($_POST['estado']) ? 1 : 0
            ];

            // Validaciones Backend
            if (empty($data['nombre'])) {
                $error = 'El nombre de la categoría no puede estar vacío.';
            } elseif ($this->model->existsByName($data['nombre'], $id)) {
                $error = 'El nombre "' . htmlspecialchars($data['nombre']) . '" ya está siendo utilizado por otra categoría.';
            } else {
                if ($this->model->update($id, $data)) {
                    set_flash_message('success', '✅ Categoría actualizada correctamente.');
                    redirect('categoria/index');
                } else {
                    $error = 'No se pudo actualizar la información en la base de datos.';
                }
            }
        }

        $this->view('categorias/edit', [
            'page_title' => 'Editar Categoría | NexusStock',
            'active_menu' => 'categorias',
            'data' => $data,
            'id' => $id,
            'error' => $error
        ]);
    }

    /**
     * Eliminar categoría (POST o GET /categoria/delete/id)
     */
    public function delete($id = null) {
        if ($id) {
            $categoria = $this->model->getById($id);
            if ($categoria) {
                // Intentar eliminar respetando clave foránea
                if ($this->model->delete($id)) {
                    set_flash_message('success', '🗑️ Categoría "' . htmlspecialchars($categoria->nombre) . '" eliminada correctamente.');
                } else {
                    set_flash_message('warning', '⚠️ No se puede eliminar la categoría "' . htmlspecialchars($categoria->nombre) . '" porque tiene productos asignados. Primero reasigna o elimina los productos.');
                }
            }
        }
        redirect('categoria/index');
    }
}
