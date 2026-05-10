@extends('layouts.app')

@section('title', 'Tata Tertib - Admin')

@section('page-title', 'Upload Tata Tertib')

@section('content')
<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Upload PDF Tata Tertib</h3>
    <p class="text-sm text-slate-500 mb-5">File PDF akan ditampilkan di landing page untuk dilihat dan diunduh siswa.</p>

    <div class="border-2 border-dashed border-slate-200 rounded-xl p-10 text-center mb-4">
        <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-file-earmark-pdf-fill text-red-400 text-3xl"></i>
        </div>
        <p class="font-medium text-slate-600 mb-1">Belum ada file PDF</p>
        <p class="text-xs text-slate-400 mb-4">Upload file PDF tata tertib resmi sekolah</p>
        <button onclick="alert('UI Only: Pilih file PDF!')" class="btn-brand !py-2.5"><i class="bi bi-upload"></i> Pilih PDF</button>
    </div>

    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-700">
        <i class="bi bi-info-circle-fill mr-1"></i> File yang sudah diupload akan tampil di halaman utama portal.
    </div>
</div>
@endsection