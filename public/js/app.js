/**
 * ARCHIVO DE INTERACTIVIDAD JS
 * Manejo de Modales de Confirmación, Alertas Toast flotantes y UX Dinámica
 */

document.addEventListener('DOMContentLoaded', () => {
    initFlashMessages();
    initDeleteModals();
    initLiveSearch();
    initThemeToggle();
});

/**
 * 1. Ocultar automáticamente las alertas Flash tras 4.5 segundos
 */
function initFlashMessages() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 4500);
    });
}

/**
 * 2. Sistema de Modales para confirmar eliminación en operaciones CRUD
 */
let currentDeleteUrl = null;

function initDeleteModals() {
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    const modalOverlay = document.getElementById('deleteModal');
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const itemNameSpan = document.getElementById('deleteItemName');

    if (!modalOverlay) return;

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            currentDeleteUrl = btn.getAttribute('href');
            const itemName = btn.getAttribute('data-name') || 'este registro';
            
            if (itemNameSpan) {
                itemNameSpan.textContent = itemName;
            }
            
            modalOverlay.classList.add('active');
        });
    });

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            modalOverlay.classList.remove('active');
            currentDeleteUrl = null;
        });
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            if (currentDeleteUrl) {
                window.location.href = currentDeleteUrl;
            }
        });
    }

    // Cerrar al hacer clic fuera del cuadro de modal
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.classList.remove('active');
            currentDeleteUrl = null;
        }
    });
}

/**
 * 3. Filtro interactivo rápido en tablas del cliente
 */
function initLiveSearch() {
    const searchInput = document.getElementById('tableQuickFilter');
    const table = document.querySelector('.table');

    if (!searchInput || !table) return;

    const tbody = table.querySelector('tbody');
    const rows = tbody.getElementsByTagName('tr');

    searchInput.addEventListener('input', function() {
        const filter = this.value.toLowerCase().trim();

        for (let i = 0; i < rows.length; i++) {
            const rowText = rows[i].textContent || rows[i].innerText;
            if (rowText.toLowerCase().indexOf(filter) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    });
}

/**
 * 4. Toggle de Tema Oscuro/Claro
 */
function initThemeToggle() {
    const themeBtn = document.getElementById('themeToggleBtn');
    if (!themeBtn) return;

    const icon = themeBtn.querySelector('i');
    const textSpan = themeBtn.querySelector('span');

    // Inicializar UI basada en tema actual
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    if (isDark) {
        icon.classList.replace('bx-moon', 'bx-sun');
        textSpan.textContent = 'Modo Claro';
    }

    themeBtn.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        let targetTheme = 'light';
        
        if (currentTheme !== 'dark') {
            targetTheme = 'dark';
            icon.classList.replace('bx-moon', 'bx-sun');
            textSpan.textContent = 'Modo Claro';
        } else {
            icon.classList.replace('bx-sun', 'bx-moon');
            textSpan.textContent = 'Modo Oscuro';
            document.documentElement.removeAttribute('data-theme'); // Elimina el atributo para usar el default
        }

        if (targetTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
        localStorage.setItem('nexusTheme', targetTheme);
    });
}

