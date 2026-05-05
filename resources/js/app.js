//
// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            if (overlay) {
                overlay.classList.toggle('hidden');
            }
        });
    }

    if (overlay && sidebar) {
        overlay.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    }

    // Close modals when clicking overlay
    const modalOverlay = document.getElementById('modal-overlay');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function() {
            // Find all modals and hide them
            document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                }
            });
            modalOverlay.classList.add('hidden');
        });
    }

    // Prevent modal close when clicking inside modal
    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    // Tab functionality (if any)
    document.querySelectorAll('[data-tab-target]').forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.getAttribute('data-tab-target');

            // Remove active class from all tabs
            document.querySelectorAll('[data-tab-target]').forEach(t => {
                t.classList.remove('bg-blue-600', 'text-white');
                t.classList.add('text-slate-300');
            });

            // Add active class to clicked tab
            this.classList.add('bg-blue-600', 'text-white');
            this.classList.remove('text-slate-300');

            // Hide all tab contents
            document.querySelectorAll('[data-tab-content]').forEach(content => {
                content.classList.add('hidden');
            });

            // Show target content
            document.querySelector(target).classList.remove('hidden');
        });
    });

    // File upload preview (if any)
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

    // Auto-hide alerts after 3 seconds
    document.querySelectorAll('.alert-auto-hide').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    });
});

// Geolocation simulation for demo (UI only)
function simulateGeolocation(callback) {
    // Simulate getting geolocation
    setTimeout(() => {
        callback({
            latitude: -6.1189071,
            longitude: 106.5879572,
            accuracy: 10
        });
    }, 1000);
}

// Export for use in Blade templates
window.simulateGeolocation = simulateGeolocation;
