@props([
    'title',
    'action',
    'previewUrl',
    'entityLabel',
    'disabled' => false,
])

<div x-data="{
        open: false,
        step: 1,
        loading: false,
        checking: false,
        count: null,
        requestId: 0,
        checkedCount() {
            return this.$refs.fields ? this.$refs.fields.querySelectorAll('input[type=checkbox]:checked').length : 0;
        },
        async refreshCount() {
            // Setiap panggilan dapat nomor urut sendiri - kalau ada request lama
            // yang responsnya baru balik SETELAH request yang lebih baru (network
            // gak terjamin urut), hasilnya dibuang, bukan menimpa angka terkini.
            const requestId = ++this.requestId;

            if (this.checkedCount() === 0) {
                this.count = 0;
                return;
            }

            this.checking = true;

            try {
                // $refs.form juga bawa field _method=DELETE (buat submit hapus
                // yang sebenarnya) - kalau ikut dikirim, Laravel nge-spoof
                // request pratinjau ini jadi DELETE dan salah rute. Dibuang dulu
                // di sini, method HTTP asli tetap POST sesuai fetch() di bawah.
                const body = new FormData(this.$refs.form);
                body.delete('_method');

                const response = await fetch(@js($previewUrl), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body,
                });
                const data = await response.json();

                if (requestId === this.requestId) {
                    this.count = data.count ?? 0;
                }
            } catch (e) {
                if (requestId === this.requestId) {
                    this.count = null;
                }
            } finally {
                if (requestId === this.requestId) {
                    this.checking = false;
                }
            }
        },
        openModal() {
            this.open = true;
            this.step = 1;
            this.count = null;
            this.refreshCount();
        },
        pilihSemua() {
            // dispatchEvent('change') dipaksa di sini karena set .checked lewat
            // JS gak otomatis nembak event 'change' - kalau gak dipaksa, binding
            // x-model punya checkbox lain (mis. bkChecked) gak ikut ke-update.
            // Checkbox bertanda [data-conditional] (refinement yang cuma muncul
            // kalau kriteria lain dicentang, mis. Tingkat khusus BK) sengaja
            // dilewati - itu bukan kriteria dasar, dan kalau ikut tercentang
            // massal dia malah mengecualikan data yang gak punya nilai itu.
            this.$refs.fields.querySelectorAll('input[type=checkbox]:not([data-conditional])').forEach(cb => {
                cb.checked = true;
                cb.dispatchEvent(new Event('change', {bubbles: true}));
            });
            this.refreshCount();
        },
        kosongkanSemua() {
            this.$refs.fields.querySelectorAll('input[type=checkbox]').forEach(cb => {
                cb.checked = false;
                cb.dispatchEvent(new Event('change', {bubbles: true}));
            });
            this.refreshCount();
        },
    }">
    <button type="button"
            @click="openModal()"
            @disabled($disabled)
            class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-4 py-2.5 text-sm font-medium text-red-600 shadow-sm
                   hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors cursor-pointer
                   disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Hapus Lanjutan
    </button>

    <div x-show="open" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4"
         @keydown.escape.window="open = false">
        <div x-show="open" x-transition
             class="w-full max-w-lg rounded-2xl bg-white shadow-xl"
             @click.outside="open = false">
            <form method="POST" action="{{ $action }}" x-ref="form" @submit="loading = true">
                @csrf
                @method('DELETE')

                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-gray-500">Centang kriteria {{ $entityLabel }} yang ingin dihapus.
                        Biarkan kosong jika tidak ingin membatasi kriteria pada grup tersebut.</p>
                </div>

                <div class="max-h-[60vh] overflow-y-auto px-5 py-4">
                    <div class="mb-4 flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <button type="button" @click="pilihSemua()"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-primary/30 px-3 py-1.5 text-primary transition-colors hover:bg-primary/5">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Centang Semua Kriteria
                        </button>
                        <button type="button" @click="kosongkanSemua()"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-1.5 text-gray-600 transition-colors hover:bg-gray-50">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Kosongkan
                        </button>
                    </div>

                    <div x-ref="fields" @change="refreshCount()" class="space-y-5">
                        {{ $slot }}
                    </div>

                    <div x-show="step === 1" class="mt-5 rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-700">
                        <span x-show="checking">Menghitung...</span>
                        <template x-if="!checking && count > 0">
                            <span><strong x-text="count"></strong> {{ $entityLabel }} cocok dengan kriteria ini dan akan terhapus.</span>
                        </template>
                        <span x-show="!checking && count === 0">Belum ada kriteria dicentang, atau tidak ada yang cocok.</span>
                    </div>

                    <div x-show="step === 2" class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <p class="font-semibold">PERINGATAN: Tindakan ini tidak dapat diurungkan!</p>
                        <p class="mt-1"><strong x-text="count"></strong> {{ $entityLabel }} beserta data terkaitnya akan hilang permanen.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 px-5 py-4">
                    <button type="button" @click="open = false"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="button" x-show="step === 1" :disabled="!count" @click="step = 2"
                            class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors disabled:cursor-not-allowed disabled:opacity-50">
                        Lanjutkan
                    </button>
                    <button type="submit" x-show="step === 2" :disabled="loading"
                            class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors disabled:opacity-60">
                        <span x-cloak x-show="loading">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </span>
                        Hapus Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
