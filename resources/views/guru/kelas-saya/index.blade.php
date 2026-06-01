@extends('layouts.app')

@section('title', 'Kelas Saya')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Kelas Saya</h1>
    <p class="mt-1 text-base text-gray-500">Pantau absensi hari ini untuk kelas {{ $class->name }}</p>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

@unless($isWeekday)
    <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
        Hari ini bukan hari aktif absensi. Absensi siswa hanya tersedia pada hari Senin sampai Jumat.
    </div>
@endunless

<div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
    @php
        $summaryCards = [
            ['status' => 'hadir', 'label' => 'Hadir', 'class' => 'border-green-200 bg-green-50 text-green-700'],
            ['status' => 'terlambat', 'label' => 'Terlambat', 'class' => 'border-amber-200 bg-amber-50 text-amber-700'],
            ['status' => 'izin', 'label' => 'Izin', 'class' => 'border-blue-200 bg-blue-50 text-blue-700'],
            ['status' => 'sakit', 'label' => 'Sakit', 'class' => 'border-purple-200 bg-purple-50 text-purple-700'],
            ['status' => 'alpha', 'label' => 'Alpha', 'class' => 'border-red-200 bg-red-50 text-red-700'],
            ['status' => 'belum_absen', 'label' => 'Belum Absen', 'class' => 'border-gray-200 bg-gray-50 text-gray-700'],
        ];
    @endphp

    @foreach($summaryCards as $card)
        <div class="rounded-lg border p-4 text-center {{ $card['class'] }}">
            <p class="text-2xl font-bold">{{ $stats[$card['status']] }}</p>
            <p class="mt-1 text-xs font-medium">{{ $card['label'] }}</p>
        </div>
    @endforeach
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">{{ $class->name }}</h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ now()->locale('id')->translatedFormat('l, d F Y') }} • {{ $students->count() }} siswa
            </p>
        </div>

        <form method="GET" action="{{ route('guru.kelas-saya') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama atau NIS..."
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                Cari
            </button>
        </form>
    </div>

    @if($students->isEmpty())
        <div class="py-12 text-center">
            <h3 class="text-sm font-medium text-gray-900">Tidak ada siswa</h3>
            <p class="mt-1 text-sm text-gray-500">Belum ada siswa atau pencarian tidak menemukan hasil.</p>
        </div>
    @else
        @php
            $statusConfig = [
                'hadir' => ['bg-green-100 text-green-800', 'Hadir'],
                'terlambat' => ['bg-amber-100 text-amber-800', 'Terlambat'],
                'izin' => ['bg-blue-100 text-blue-800', 'Izin'],
                'sakit' => ['bg-purple-100 text-purple-800', 'Sakit'],
                'alpha' => ['bg-red-100 text-red-800', 'Alpha'],
                'belum_absen' => ['bg-gray-100 text-gray-800', 'Belum Absen'],
            ];
        @endphp

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Jam Absen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Selfie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($students as $index => $student)
                        @php
                            $attendance = $attendances->get($student->id);
                            $status = $attendance?->status ?? 'belum_absen';
                            [$badgeClass, $statusLabel] = $statusConfig[$status];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $student->nis ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $student->user->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $attendance?->check_in_time?->format('H:i') ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                @if($attendance?->selfie_path)
                                    <button type="button"
                                            data-selfie-url="{{ asset('storage/'.$attendance->selfie_path) }}"
                                            data-student-name="{{ $student->user->name }}"
                                            onclick="openSelfieModal(this.dataset.selfieUrl, this.dataset.studentName)"
                                            class="block overflow-hidden rounded-lg border border-gray-200 transition hover:border-primary">
                                        <img src="{{ asset('storage/'.$attendance->selfie_path) }}"
                                             alt="Selfie {{ $student->user->name }}"
                                             class="h-14 w-12 object-cover">
                                    </button>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                @if($isWeekday)
                                    <button type="button"
                                            data-update-url="{{ route('guru.kelas-saya.absensi.update', $student) }}"
                                            data-current-status="{{ $status === 'belum_absen' ? 'hadir' : $status }}"
                                            data-student-name="{{ $student->user->name }}"
                                            onclick="openEditModal(this.dataset.updateUrl, this.dataset.currentStatus, this.dataset.studentName)"
                                            class="font-medium text-primary hover:text-primary-dark">
                                        Edit
                                    </button>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-xl">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Edit Status Absensi Hari Ini</h3>
            <p id="modalStudentName" class="mt-1 text-sm text-gray-500"></p>
        </div>

        <form id="editForm" method="POST" class="px-6 py-5">
            @csrf
            @method('PUT')

            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status"
                    class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                <option value="hadir">Hadir</option>
                <option value="terlambat">Terlambat</option>
                <option value="izin">Izin</option>
                <option value="sakit">Sakit</option>
                <option value="alpha">Alpha</option>
            </select>

            <p class="mt-3 text-xs text-gray-500">Perubahan manual tidak mengisi jam absen atau selfie yang belum tersedia.</p>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<div id="selfieModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4">
    <div class="w-full max-w-sm rounded-2xl bg-white p-4 shadow-xl">
        <div class="mb-3 flex items-center justify-between gap-3">
            <p id="selfieStudentName" class="text-sm font-semibold text-gray-900"></p>
            <button type="button" onclick="closeSelfieModal()" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                <span class="sr-only">Tutup</span>
                &times;
            </button>
        </div>
        <img id="selfieImage" src="" alt="Selfie absensi" class="aspect-[3/4] w-full rounded-lg object-cover">
    </div>
</div>

@push('scripts')
<script>
    function openEditModal(updateUrl, currentStatus, studentName) {
        document.getElementById('editForm').action = updateUrl;
        document.getElementById('status').value = currentStatus;
        document.getElementById('modalStudentName').textContent = studentName;
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
    }

    function openSelfieModal(selfieUrl, studentName) {
        document.getElementById('selfieImage').src = selfieUrl;
        document.getElementById('selfieStudentName').textContent = studentName;
        document.getElementById('selfieModal').classList.remove('hidden');
        document.getElementById('selfieModal').classList.add('flex');
    }

    function closeSelfieModal() {
        document.getElementById('selfieModal').classList.add('hidden');
        document.getElementById('selfieModal').classList.remove('flex');
        document.getElementById('selfieImage').src = '';
    }

    document.getElementById('editModal').addEventListener('click', function (event) {
        if (event.target === this) {
            closeEditModal();
        }
    });

    document.getElementById('selfieModal').addEventListener('click', function (event) {
        if (event.target === this) {
            closeSelfieModal();
        }
    });
</script>
@endpush
@endsection
