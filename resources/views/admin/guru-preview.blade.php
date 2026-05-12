@extends('layouts.app')

@section('title', 'Preview CSV Guru - Admin')

@section('page-title', 'Preview Import CSV Guru')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <p class="text-sm text-muted">{{ $total }} baris data siap diimpor</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.guru') }}" onclick="return confirm('Batalkan import?')" class="btn-outline !text-sm"><i class="bi bi-x-circle"></i> Batal</a>
        <form action="{{ route('admin.guru.import.confirm') }}" method="POST" id="confirmForm">
            @csrf
            <button type="submit" class="btn-brand !text-sm" onclick="showLoading('Mengimpor data...')"><i class="bi bi-check-circle"></i> Simpan Semua</button>
        </form>
    </div>
</div>

<div class="card anim-up">
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">No.</th><th class="table-header">Nama</th><th class="table-header">NIP</th></tr></thead>
            <tbody>
                @foreach($rows as $i => $r)
                <tr>
                    <td class="table-cell">{{ $i + 1 }}</td>
                    <td class="table-cell font-semibold">{{ $r['nama'] }}</td>
                    <td class="table-cell">{{ $r['nip'] ?: '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
