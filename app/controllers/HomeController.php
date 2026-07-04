<?php
require_once __DIR__ . '/Controller.php';

/**
 * ==============================================================================
 * CONTROLADOR: HomeController
 * Módulo de Inicio / Dashboard Principal
 * Muestra métricas financieras, alertas de inventario y actividad reciente
 * ==============================================================================
 */
class HomeController extends Controller {
    /** @var Producto */
    private $productoModel;
    
    /** @var Categoria */
    private $categoriaModel;
    
    /** @var Proveedor */
    private $proveedorModel;
    
    /** @var Movimiento */
    private $movimientoModel;

    public function __construct() {
        $this->productoModel = new Producto();
        $this->categoriaModel = new Categoria();
        $this->proveedorModel = new Proveedor();
        $this->movimientoModel = new Movimiento();
    }

    /**
     * Acción principal del Dashboard (GET / o /home/index)
     */
    public function index() {
        // Obtener estadísticas consolidadas del almacén
        $stats = $this->productoModel->getSummaryStats();
        
        // Obtener productos en estado crítico de stock (alerta roja)
        $alertasStock = $this->productoModel->getLowStock();
        
        // Obtener los últimos 5 movimientos transaccionales
        $movimientosRecientes = $this->movimientoModel->getAll(5);
        
        // Conteo de categorías y proveedores para las tarjetas
        $categorias = $this->categoriaModel->getAll();
        $proveedores = $this->proveedorModel->getAll();

        // Renderizar la vista principal enviando todas las métricas
        $this->view('home/index', [
            'page_title' => 'Dashboard | Control Inteligente de Inventario',
            'active_menu' => 'home',
            'stats' => $stats,
            'alertasStock' => $alertasStock,
            'movimientosRecientes' => $movimientosRecientes,
            'total_categorias' => count($categorias),
            'total_proveedores' => count($proveedores)
        ]);
    }
}
