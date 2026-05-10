document.addEventListener('DOMContentLoaded', function() {
    const modalOverlay = document.getElementById('modal-overlay');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function() {
            document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                if (!modal.classList.contains('hidden')) modal.classList.add('hidden');
            });
            modalOverlay.classList.add('hidden');
        });
    }

    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', function(e) { e.stopPropagation(); });
    });

    document.querySelectorAll('[data-tab-target]').forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.getAttribute('data-tab-target');
            document.querySelectorAll('[data-tab-target]').forEach(t => {
                t.classList.remove('bg-blue-600', 'text-white');
                t.classList.add('text-slate-300');
            });
            this.classList.add('bg-blue-600', 'text-white');
            this.classList.remove('text-slate-300');
            document.querySelectorAll('[data-tab-content]').forEach(content => content.classList.add('hidden'));
            document.querySelector(target).classList.remove('hidden');
        });
    });

    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name;
            const previewEl = this.parentElement.querySelector('.file-name');
            if (previewEl && fileName) {
                previewEl.textContent = fileName;
                previewEl.classList.remove('hidden');
            }
        });
    });

    document.querySelectorAll('.alert-auto-hide').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    });
});

function simulateGeolocation(callback) {
    setTimeout(() => {
        callback({ latitude: -6.1189071, longitude: 106.5879572, accuracy: 10 });
    }, 1000);
}

window.simulateGeolocation = simulateGeolocation;
