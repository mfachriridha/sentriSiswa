<div id="confirmOverlay" class="hidden fixed inset-0 z-[70] bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 mx-4 anim-up">
        <div class="text-center mb-4">
            <i class="bi bi-exclamation-triangle-fill text-amber-500 text-3xl mb-2 block"></i>
            <p id="confirmMessage" class="text-sm font-semibold text-slate-800"></p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeConfirm()" class="btn-outline flex-1 !py-2.5">Batal</button>
            <button id="confirmOk" class="btn-brand flex-1 !py-2.5 !bg-red-600 hover:!bg-red-700">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<script>
let confirmCallback = null;
function confirmAction(msg, cb) {
    document.getElementById('confirmMessage').textContent = msg;
    confirmCallback = cb;
    document.getElementById('confirmOverlay').classList.remove('hidden');
}
function closeConfirm() {
    document.getElementById('confirmOverlay').classList.add('hidden');
    confirmCallback = null;
}
document.getElementById('confirmOk').addEventListener('click', function() {
    if (confirmCallback) confirmCallback();
    closeConfirm();
});
</script>
