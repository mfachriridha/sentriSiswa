<!-- Loading Overlay -->
<div id="loading-overlay" class="hidden fixed inset-0 z-[250] bg-black/40 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl px-10 py-8 flex flex-col items-center gap-4 anim-up">
        <div class="w-12 h-12 border-4 border-[#1c6880] border-t-transparent rounded-full animate-spin"></div>
        <p id="loading-text" class="text-sm font-bold text-[#1c6880]">Memproses...</p>
    </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
.animate-spin { animation: spin .8s linear infinite; }
</style>

<script>
let loadingActive = false;

function showLoading(message = 'Memproses...') {
    const overlay = document.getElementById('loading-overlay');
    const text = document.getElementById('loading-text');
    if (overlay) {
        text.textContent = message;
        overlay.classList.remove('hidden');
        loadingActive = true;
        // Disable all submit buttons to prevent double-submission
        document.querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.disabled = true;
            btn.dataset.wasDisabled = 'false';
        });
    }
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.classList.add('hidden');
        loadingActive = false;
        // Re-enable submit buttons
        document.querySelectorAll('button[type="submit"]').forEach(btn => {
            if (btn.dataset.wasDisabled === 'false') {
                btn.disabled = false;
                delete btn.dataset.wasDisabled;
            }
        });
    }
}

// Auto-show loading on form submit
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form && !form.dataset.noLoading) {
        showLoading('Menyimpan data...');
    }
});
</script>
