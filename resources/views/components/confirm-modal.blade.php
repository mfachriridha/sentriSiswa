<div
    x-data="{ open: false, title: '', message: '', formId: '' }"
    x-on:open-confirm-modal.window="
        open = true;
        title = $event.detail.title;
        message = $event.detail.message;
        formId = $event.detail.formId;
    ">

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.outside="open = false"
             class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="title"></h3>
                    <p class="mt-2 text-base text-gray-600" x-text="message"></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button @click="open = false"
                        class="rounded-lg border border-gray-300 px-5 py-2.5 text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button @click="document.getElementById(formId).submit()"
                        class="rounded-lg bg-red-600 px-5 py-2.5 text-base font-semibold text-white hover:bg-red-700 transition-colors">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>
