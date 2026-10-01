/**
 * Green River Enterprise Core - Frontend Controller
 * Modals, phone input formatting, and double-submission protection.
 */

// Modal open/close handlers
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Close modals when clicking the dark backdrop
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('modal-backdrop')) {
        e.target.classList.add('hidden');
        document.body.style.overflow = '';
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal-backdrop');
        modals.forEach(m => m.classList.add('hidden'));
        document.body.style.overflow = '';
    }
});

// US Phone Number Auto-Formatter: (XXX) XXX-XXXX
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            if (!x[2]) {
                e.target.value = x[1];
            } else if (!x[3]) {
                e.target.value = '(' + x[1] + ') ' + x[2];
            } else {
                e.target.value = '(' + x[1] + ') ' + x[2] + '-' + x[3];
            }
        });
    }

    // Form submit state & double-submission guard
    const form = document.getElementById('leadForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            const tcpa = document.getElementById('tcpa_consent');
            if (tcpa && !tcpa.checked) {
                e.preventDefault();
                alert(form.getAttribute('data-lang') === 'es' 
                    ? 'Por favor marca la casilla de consentimiento para continuar.' 
                    : 'Please check the consent box to proceed.');
                tcpa.focus();
                return;
            }

            // Lock button to prevent duplicate clicks
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.75';
            submitBtn.style.pointerEvents = 'none';

            const isSpanish = form.getAttribute('data-lang') === 'es';
            submitBtn.innerHTML = `
                <div class="flex items-center justify-center gap-2 py-2">
                    <svg class="animate-spin h-6 w-6 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span class="font-heading text-2xl font-black uppercase text-black">
                        ${isSpanish ? 'Enviando...' : 'Submitting...'}
                    </span>
                </div>
            `;
        });
    }
});
