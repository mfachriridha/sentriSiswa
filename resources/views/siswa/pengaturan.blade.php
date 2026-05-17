@extends('layouts.app')

@section('title', 'Pengaturan - SentriSiswa')

@section('page-title', 'Pengaturan')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-700 flex items-center gap-2 anim-up">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="bi bi-x"></i></button>
</div>
@endif

<div class="flex gap-1 p-1 bg-[#edeeef] rounded-lg w-fit mb-6">
    <button onclick="switchTab('profil')" id="tab-profil" class="px-4 py-2 rounded-lg text-sm font-bold bg-white shadow-sm text-[#1c6880] transition">Profil</button>
    <button onclick="switchTab('biodata')" id="tab-biodata" class="px-4 py-2 rounded-lg text-sm font-bold text-muted hover:text-[#191c1d] transition">Biodata</button>
</div>

<!-- Panel: Profil -->
<div id="panel-profil">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="card text-center anim-up">
            <div class="w-28 h-28 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto border-4 border-white shadow-lg">
                <i class="bi bi-person-fill text-5xl text-blue-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mt-4">{{ $student->name }}</h3>
            <p class="text-sm text-slate-500">{{ $student->schoolClass?->name ?? '—' }} &middot; NIS: {{ $student->nis }}</p>
            <div class="mt-3 flex justify-center gap-2">
                <span class="badge badge-green">Aktif</span>
                <span class="badge badge-blue">{{ $student->poin }} Poin</span>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-5">
            <div class="card anim-up">
                <h3 class="font-bold text-slate-900 mb-4">Informasi Akun</h3>
                <form action="{{ route('siswa.pengaturan.update') }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="input-field" required>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="input-field" required>
                    </div>
                    <hr class="border-[#c3c6d1]/20">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ganti Password</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Password Baru</label>
                            <input type="password" name="password" class="input-field" placeholder="Minimal 8 karakter" minlength="8">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="input-field" placeholder="Ulangi password">
                        </div>
                    </div>
                    <p class="text-[10px] text-muted">Kosongkan jika tidak ingin mengganti password.</p>
                    <button type="submit" class="btn-brand"><i class="bi bi-check-circle"></i> Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Panel: Biodata -->
<div id="panel-biodata" class="hidden">
    <div class="grid lg:grid-cols-2 gap-5">
        <div class="card lg:col-span-2 anim-up">
            <div class="flex flex-col sm:flex-row gap-6 items-start">
                <div class="shrink-0">
                    <div class="w-28 h-36 bg-slate-100 border-2 border-slate-300 rounded flex items-center justify-center">
                        <div class="text-center"><i class="bi bi-person-fill text-4xl text-slate-300"></i><p class="text-[9px] text-slate-400 mt-1 font-medium">3 x 4</p></div>
                    </div>
                </div>
                <div class="flex-1 space-y-2">
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</label><p class="text-base font-bold text-slate-900">{{ $student->name }}</p></div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIS</label><p class="text-sm font-semibold text-slate-900">{{ $student->nis }}</p></div>
                        <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NISN</label><p class="text-sm font-semibold text-slate-900">{{ $student->nisn ?: '—' }}</p></div>
                        <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</label><p class="text-sm font-semibold text-slate-900">{{ $student->schoolClass?->name ?? '—' }}</p></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card anim-up">
            <div class="flex items-center gap-2 mb-5"><div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center"><i class="bi bi-person-fill text-blue-600"></i></div><h3 class="font-bold text-slate-900">Identitas</h3></div>
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Tempat Lahir</label><p class="text-sm font-semibold mt-0.5">{{ $student->birth_place ?: '—' }}</p></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Tanggal Lahir</label><p class="text-sm font-semibold mt-0.5">{{ $student->birth_date ?: '—' }}</p></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Jenis Kelamin</label><p class="text-sm font-semibold mt-0.5">{{ $student->gender ?: '—' }}</p></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Agama</label><p class="text-sm font-semibold mt-0.5">{{ $student->religion ?: '—' }}</p></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Status Keluarga</label><p class="text-sm font-semibold mt-0.5">{{ $student->family_status ?: '—' }}</p></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Anak ke</label><p class="text-sm font-semibold mt-0.5">{{ $student->birth_order ?: '—' }}</p></div>
                </div>
            </div>
        </div>

        <div class="card anim-up">
            <div class="flex items-center gap-2 mb-5"><div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center"><i class="bi bi-geo-alt-fill text-emerald-600"></i></div><h3 class="font-bold text-slate-900">Alamat & Kontak</h3></div>
            <div class="space-y-3">
                <div><label class="text-[10px] font-bold text-slate-400 uppercase">Alamat</label><p class="text-sm font-semibold mt-0.5">{{ $student->address ?: '—' }}</p></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Telepon</label><p class="text-sm font-semibold mt-0.5">{{ $student->home_phone ?: '—' }}</p></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Sekolah Asal</label><p class="text-sm font-semibold mt-0.5">{{ $student->prev_school ?: '—' }}</p></div>
                </div>
                <div><label class="text-[10px] font-bold text-slate-400 uppercase">Diterima</label><p class="text-sm font-semibold mt-0.5">{{ $student->admission_class ?: '—' }} &mdash; {{ $student->admission_date ?: '—' }}</p></div>
            </div>
        </div>

        @php $father = $student->father; $mother = $student->mother; $guardian = $student->guardian; @endphp
        <div class="card anim-up">
            <div class="flex items-center gap-2 mb-5"><div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center"><i class="bi bi-people-fill text-purple-600"></i></div><h3 class="font-bold text-slate-900">Orang Tua</h3></div>
            <div class="space-y-3">
                @if($father)
                <div class="p-3 bg-slate-50 rounded-xl"><p class="text-[10px] font-bold text-purple-500 uppercase">Ayah</p><p class="text-sm font-bold mt-1">{{ $father->name }}</p><p class="text-xs text-slate-500">{{ $father->job ?: '' }} @if($father->phone)&middot; <span class="text-[#43474f]"><i class="bi bi-telephone"></i> {{ $father->phone }}</span>@endif</p></div>
                @endif
                @if($mother)
                <div class="p-3 bg-slate-50 rounded-xl"><p class="text-[10px] font-bold text-purple-500 uppercase">Ibu</p><p class="text-sm font-bold mt-1">{{ $mother->name }}</p><p class="text-xs text-slate-500">{{ $mother->job ?: '' }} @if($mother->phone)&middot; <span class="text-[#43474f]"><i class="bi bi-telephone"></i> {{ $mother->phone }}</span>@endif</p></div>
                @endif
                @if(!$father && !$mother)<p class="text-sm text-slate-400 italic">Data orang tua belum tersedia.</p>@endif
            </div>
        </div>

        <div class="card anim-up">
            <div class="flex items-center gap-2 mb-5"><div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center"><i class="bi bi-person-badge text-amber-600"></i></div><h3 class="font-bold text-slate-900">Wali</h3></div>
            @if($guardian)
            <div class="p-4 bg-slate-50 rounded-xl">
                <p class="text-sm font-bold">{{ $guardian->name }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $guardian->job ?: '' }} @if($guardian->phone)&middot; <i class="bi bi-telephone"></i> {{ $guardian->phone }}@endif</p>
                @if($guardian->address)<p class="text-xs text-slate-500 mt-1"><i class="bi bi-geo-alt"></i> {{ $guardian->address }}</p>@endif
            </div>
            @else
            <div class="p-4 bg-slate-50 rounded-xl"><p class="text-sm text-slate-400 italic">Tidak ada wali.</p></div>
            @endif
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    ['profil','biodata'].forEach(t => {
        document.getElementById('panel-'+t).classList.add('hidden');
        const btn = document.getElementById('tab-'+t);
        btn.classList.remove('bg-white','shadow-sm','text-[#1c6880]');
        btn.classList.add('text-muted');
    });
    document.getElementById('panel-'+tab).classList.remove('hidden');
    const btn = document.getElementById('tab-'+tab);
    btn.classList.add('bg-white','shadow-sm','text-[#1c6880]');
    btn.classList.remove('text-muted');
}
</script>
@endsection
