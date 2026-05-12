@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('page-title', 'Dashboard Wali Kelas')

@section('content')
@if(session('error'))
<div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs font-semibold text-amber-700 flex items-center gap-2 anim-up">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-amber-400 hover:text-amber-600"><i class="bi bi-x"></i></button>
</div>
@endif

@if(!$teacherClass)
    <div class="card text-center py-12">
        <i class="bi bi-exclamation-triangle text-4xl text-amber-400 mb-3 block"></i>
        <p class="font-bold text-slate-700">Anda belum di-assign ke kelas manapun.</p>
        <p class="text-sm text-slate-500">Hubungi admin untuk mengatur kelas Anda.</p>
    </div>
@else
    <div class="flex items-center gap-3 mb-6 anim-up">
        <div class="w-10 h-10 bg-[#1c6880] rounded-xl flex items-center justify-center">
            <i class="bi bi-diagram-3 text-white"></i>
        </div>
        <div>
            <h2 class="font-bold text-slate-900">Kelas {{ $teacherClass->schoolClass->name }}</h2>
            <p class="text-xs text-muted">{{ $students->count() }} siswa terdaftar &middot; {{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    <div class="card anim-up">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <h3 class="font-bold text-slate-900">Presensi Hari Ini</h3>
            <div class="flex gap-2">
                <span class="badge badge-green">{{ $attendances->where('status', 'hadir')->count() }} Hadir</span>
                <span class="badge badge-amber">{{ $attendances->where('status', 'izin')->count() }} Izin</span>
                <span class="badge badge-red">{{ $attendances->where('status', 'sakit')->count() }} Sakit</span>
                <span class="badge badge-red !bg-[#e8e8e8] !text-[#717680]">{{ $students->count() - $attendances->count() }} Belum</span>
            </div>
        </div>

        <div class="table-container">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="table-header">NIS</th>
                        <th class="table-header">Nama</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Selfie</th>
                        <th class="table-header text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php $att = $attendances->get($student->id); @endphp
                    <tr>
                        <td class="table-cell text-xs">{{ $student->nis }}</td>
                        <td class="table-cell font-semibold">{{ $student->name }}</td>
                        <td class="table-cell">
                            @if($att)
                                @if($att->status === 'hadir')
                                    <span class="badge badge-green">Hadir</span>
                                @elseif($att->status === 'izin')
                                    <span class="badge badge-amber">Izin</span>
                                @elseif($att->status === 'sakit')
                                    <span class="badge badge-red">Sakit</span>
                                @else
                                    <span class="badge" style="background:#e8e8e8;color:#717680">Alfa</span>
                                @endif
                                @if(!$att->is_inside_geofence)
                                    <span class="badge badge-amber !ml-1" title="Di luar area sekolah">⚠ Luar</span>
                                @endif
                            @else
                                <span class="badge" style="background:#e8e8e8;color:#717680">Belum Presensi</span>
                            @endif
                        </td>
                        <td class="table-cell">
                            @if($att && $att->selfie_path)
                                <a href="{{ asset($att->selfie_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                                    <i class="bi bi-camera-fill mr-1"></i>Lihat
                                </a>
                            @elseif($att)
                                <span class="text-xs text-muted">—</span>
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </td>
                        <td class="table-cell-aksi">
                            @if(!$att)
                                <span class="text-xs text-muted italic">Menunggu</span>
                            @elseif(!$att->confirmed_by)
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="confirmAttendance({{ $att->id }})" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition cursor-pointer">
                                        <i class="bi bi-check-circle"></i> Konfirmasi
                                    </button>
                                    <button onclick="editStatus({{ $att->id }}, '{{ $att->status }}')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold border border-[#c3c6d1] text-[#43474f] hover:bg-[#edeeef] transition cursor-pointer">
                                        <i class="bi bi-pencil"></i> Ubah
                                    </button>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600">
                                    <i class="bi bi-check-circle-fill"></i> Terkonfirmasi
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Status Modal -->
    <div id="statusModalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeStatusModal()"></div>
    <div id="statusModal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 anim-up">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg">Ubah Status Presensi</h3>
            <button onclick="closeStatusModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button>
        </div>
        <form id="statusForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="statusAttendanceId">
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Status</label>
                <select id="newStatus" class="input-field">
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alfa">Alfa</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Catatan (opsional)</label>
                <textarea id="statusNote" class="input-field" rows="2" placeholder="Alasan perubahan status..."></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeStatusModal()" class="btn-outline flex-1 !py-2.5">Batal</button>
                <button type="submit" class="btn-brand flex-1 !py-2.5">
                    <span class="btn-text"><i class="bi bi-check-circle"></i> Simpan</span>
                    <span class="btn-loading hidden"><i class="bi bi-hourglass-split animate-spin"></i> Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
@endif

@push('scripts')
<script>
function setLoading(btn, loading) {
    const t = btn.querySelector('.btn-text'), l = btn.querySelector('.btn-loading');
    if (t) t.classList.toggle('hidden', loading);
    if (l) l.classList.toggle('hidden', !loading);
    btn.disabled = loading;
}

function confirmAttendance(id) {
    const btn = event.target.closest('button');
    if (!confirm('Konfirmasi kehadiran siswa? Notifikasi akan dikirim ke orang tua.')) return;
    setLoading(btn, true);
    fetch('/wali-kelas/confirm/' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else showToast(data.message || 'Gagal.', 'error');
    });
}

function editStatus(id, currentStatus) {
    document.getElementById('statusAttendanceId').value = id;
    document.getElementById('newStatus').value = currentStatus;
    document.getElementById('statusNote').value = '';
    document.getElementById('statusModalOverlay').classList.remove('hidden');
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModalOverlay').classList.add('hidden');
    document.getElementById('statusModal').classList.add('hidden');
}

document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('[type="submit"]');
    setLoading(btn, true);
    const id = document.getElementById('statusAttendanceId').value;
    const body = {
        status: document.getElementById('newStatus').value,
        note: document.getElementById('statusNote').value,
    };
    fetch('/wali-kelas/update-status/' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
        body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else showToast(data.message || 'Gagal.', 'error');
    });
});
</script>
@endpush
@endsection
