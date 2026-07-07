/**
 * ARCHIVO DE VALIDACIONES FRONTEND - NEXUSSTOCK MVC
 * Validaciones en tiempo real para formularios
 */

document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.needs-validation');

    forms.forEach(form => {
        // Validar en tiempo real al cambiar o salir del campo (blur / input)
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => validateInput(input));
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid')) {
                    validateInput(input);
                }
            });
        });

        // Validar todo el formulario al intentar enviar (submit)
        form.addEventListener('submit', (e) => {
            let isValid = true;
            inputs.forEach(input => {
                if (!validateInput(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
                // Enfocar el primer input con error
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
    });
});

/**
 * Función central de validación por campo
 */
function validateInput(input) {
    // Ignorar botones o campos deshabilitados
    if (input.type === 'submit' || input.type === 'button' || input.disabled) {
        return true;
    }

    const value = input.value.trim();
    const type = input.getAttribute('data-type') || input.type;
    const isRequired = input.hasAttribute('required') || input.getAttribute('data-required') === 'true';
    
    let valid = true;
    let message = '';

    // 1. Validación de obligatoriedad
    if (isRequired && value === '') {
        valid = false;
        message = 'Este campo es obligatorio.';
    } 
    // 2. Validación por tipo de dato (solo si tiene valor)
    else if (value !== '') {
        if (type === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                valid = false;
                message = 'Ingresa un correo electrónico válido.';
            }
        } else if (type === 'number' || input.classList.contains('input-number')) {
            const num = parseFloat(value);
            if (isNaN(num)) {
                valid = false;
                message = 'Debe ser un valor numérico.';
            } else if (input.hasAttribute('min') && num < parseFloat(input.getAttribute('min'))) {
                valid = false;
                message = `El valor mínimo permitido es ${input.getAttribute('min')}.`;
            }
        } else if (input.getAttribute('data-validate') === 'ruc') {
            const rucRegex = /^[0-9]{13}$/;
            if (!rucRegex.test(value)) {
                valid = false;
                message = 'El RUC debe constar exactamente de 13 dígitos numéricos.';
            }
        }
    }

    // Aplicar estilos visuales al input (rojo para error, verde para éxito)
    applyValidationFeedback(input, valid, message);
    return valid;
}

/**
 * Aplicar clases visuales al DOM (Bootstrap/Custom style)
 */
function applyValidationFeedback(input, isValid, message) {
    // Limpiar estados previos
    input.classList.remove('is-valid', 'is-invalid');
    
    // Buscar o crear contenedor de mensaje de error
    let feedback = input.parentNode.querySelector('.invalid-feedback');
    if (!feedback && !isValid) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        input.parentNode.appendChild(feedback);
    }

    if (!isValid) {
        input.classList.add('is-invalid');
        if (feedback) feedback.textContent = message;
    } else {
        if (input.value.trim() !== '') {
            input.classList.add('is-valid');
        }
        if (feedback) feedback.textContent = '';
    }
}
