            </main>

<!-- PIE DE PÁGINA DEL SISTEMA -->
            <footer class="footer">
                <div style="padding: 0 1.5rem; display: flex; flex-direction: column; align-items: center;">
                    <p><strong>NexusStock MVC</strong> &copy; <?= date('Y') ?> | Proyecto de Segundo Parcial</p>
                    <p style="font-size: 0.75rem; margin-top: 0.35rem; color: var(--text-muted);">
                        Desarrollado con Patrón MVC nativo.
                    </p>
                </div>
            </footer>
        </div> <!-- End main-wrapper -->
    </div> <!-- End app-layout -->

    <!-- VENTANA MODAL DE CONFIRMACIÓN PARA ELIMINAR REGISTROS (CRUD) -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div style="font-size: 3.5rem; text-align: center; margin-bottom: 0.5rem; color: var(--warning);"><i class='bx bx-error-circle'></i></div>
            <h3 style="color: var(--text-main); text-align: center; font-size: 1.4rem; margin-bottom: 0.5rem;">¿Confirmar Eliminación?</h3>
            <p style="color: var(--text-muted); text-align: center; margin-bottom: 1.75rem; line-height: 1.5;">
                Estás a punto de eliminar <strong id="deleteItemName" style="color: var(--danger);">este registro</strong> del sistema. Esta acción no se puede deshacer.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button type="button" class="btn btn-secondary" id="cancelDeleteBtn" style="flex: 1;">
                    <i class='bx bx-x'></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" style="flex: 1;">
                    <i class='bx bx-trash'></i> Sí, Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts del Sistema -->
    <script src="<?= url('js/app.js?v=' . time()) ?>"></script>
    <script src="<?= url('js/validations.js') ?>"></script>
</body>
</html>
