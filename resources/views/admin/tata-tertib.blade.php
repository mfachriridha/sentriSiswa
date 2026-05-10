@extends('layouts.app')

@section('title', 'Tata Tertib - Admin')

@section('page-title', 'Tata Tertib')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-700 flex items-center gap-2">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="bi bi-x"></i></button>
</div>
@endif

@if($hasPdf)
<div class="card anim-up mb-5">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-bold text-slate-900 flex items-center gap-2"><i class="bi bi-file-earmark-pdf-fill text-red-400"></i> PDF Tata Tertib Aktif</h3>
            <p class="text-xs text-slate-400 mt-0.5">
                Diunggah {{ date('d M Y, H:i', $lastModified) }} &middot; {{ number_format($fileSize / 1024, 1) }} KB
            </p>
        </div>
        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full"><i class="bi bi-check-circle-fill mr-1"></i>Tampil di Landing</span>
    </div>

    <div class="border border-slate-200 rounded-lg overflow-hidden mb-4">
        <iframe src="{{ asset($pdfPath) }}" class="w-full h-[500px] border-0"></iframe>
    </div>

    <div class="flex gap-3">
        <label class="btn-brand cursor-pointer flex-1 text-center !py-2.5">
            <i class="bi bi-arrow-repeat"></i> Ganti PDF
            <input type="file" name="pdf" accept=".pdf" class="hidden" id="pdfFile" onchange="document.getElementById('replaceForm').submit()">
        </label>
        <form id="replaceForm" action="{{ route('admin.tata-tertib.upload') }}" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
        </form>
        <form action="{{ route('admin.tata-tertib.delete') }}" method="POST" class="flex-1" onsubmit="return confirm('Hapus PDF Tata Tertib? Siswa tidak akan bisa melihat dokumen di landing page.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger w-full !py-2.5"><i class="bi bi-trash"></i> Hapus</button>
        </form>
    </div>
</div>
@else
<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Upload PDF Tata Tertib</h3>
    <p class="text-sm text-slate-500 mb-5">File PDF akan ditampilkan di landing page untuk dilihat dan diunduh siswa.</p>
    <form action="{{ route('admin.tata-tertib.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="border-2 border-dashed border-slate-200 rounded-xl p-10 text-center mb-4">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="bi bi-file-earmark-pdf-fill text-red-400 text-3xl"></i></div>
            <p class="font-medium text-slate-600 mb-1">Upload file PDF</p>
            <p class="text-xs text-slate-400 mb-4">Maksimal 10MB</p>
            <input type="file" name="pdf" accept=".pdf" class="hidden" id="pdfFile" onchange="document.getElementById('pdfLabel').textContent=this.files[0]?.name||'Pilih PDF'">
            <label for="pdfFile" id="pdfLabel" class="btn-brand !py-2.5 cursor-pointer"><i class="bi bi-upload"></i> Pilih PDF</label>
        </div>
        <button type="submit" class="btn-brand w-full !py-3"><i class="bi bi-cloud-upload"></i> Upload</button>
    </form>
</div>
@endif
@endsection