<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-[300] flex flex-col gap-2 max-w-sm pointer-events-none"></div>

<!-- Toast Template (hidden) -->
<template id="toast-template">
    <div class="toast-item pointer-events-auto anim-up flex items-start gap-3 px-4 py-3 rounded-xl shadow-lg text-sm font-medium text-white min-w-[280px]">
        <i class="toast-icon bi text-lg mt-0.5 shrink-0"></i>
        <span class="toast-message flex-1 leading-snug"></span>
        <button onclick="this.parentElement.remove()" class="shrink-0 opacity-70 hover:opacity-100 transition">
            <i class="bi bi-x text-base"></i>
        </button>
    </div>
</template>

<script>
const toastIcons = {
    success: 'bi-check-circle-fill',
    error: 'bi-x-circle-fill',
    warning: 'bi-exclamation-triangle-fill',
    info: 'bi-info-circle-fill'
};
const toastColors = {
    success: 'bg-emerald-600',
    error: 'bg-red-600',
    warning: 'bg-amber-600',
    info: 'bg-blue-600'
};

function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toast-container');
    const template = document.getElementById('toast-template');
    const clone = template.content.cloneNode(true);
    const item = clone.querySelector('.toast-item');

    item.classList.add(toastColors[type] || toastColors.info);
    clone.querySelector('.toast-icon').classList.add(toastIcons[type] || toastIcons.info);
    clone.querySelector('.toast-message').textContent = message;

    container.appendChild(clone);

    if (duration > 0) {
        setTimeout(() => {
            if (item.parentElement) {
                item.style.opacity = '0';
                item.style.transform = 'translateX(100%)';
                item.style.transition = 'all 0.3s ease';
                setTimeout(() => item.remove(), 300);
            }
        }, duration);
    }
}

// Auto-display Laravel session flash messages
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
        showToast(@json(session('error')), 'error');
    @endif
    @if(session('warning'))
        showToast(@json(session('warning')), 'warning');
    @endif
    @if(session('info'))
        showToast(@json(session('info')), 'info');
    @endif
});
</script>
