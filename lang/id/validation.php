<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Pesan Validasi
|--------------------------------------------------------------------------
|
| Seluruh tampilan aplikasi ini berbahasa Indonesia, jadi pesan validasi pun
| harus berbahasa Indonesia. Tanpa berkas ini, pengguna akan melihat kunci
| mentah seperti "validation.digits" di layar.
|
*/

return [
    'accepted' => ':attribute wajib disetujui.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus tanggal setelah :date.',
    'after_or_equal' => ':attribute harus tanggal setelah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'array' => ':attribute harus berupa daftar pilihan.',
    'before' => ':attribute harus tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => ':attribute harus berisi antara :min sampai :max pilihan.',
        'file' => 'Ukuran :attribute harus antara :min sampai :max kilobita.',
        'numeric' => ':attribute harus bernilai antara :min sampai :max.',
        'string' => ':attribute harus terdiri dari :min sampai :max karakter.',
    ],
    'boolean' => ':attribute hanya boleh bernilai ya atau tidak.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Kata sandi tidak sesuai.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus tanggal yang sama dengan :date.',
    'date_format' => 'Format :attribute tidak sesuai.',
    'declined' => ':attribute wajib ditolak.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus terdiri dari :digits angka.',
    'digits_between' => ':attribute harus terdiri dari :min sampai :max angka.',
    'dimensions' => 'Ukuran gambar :attribute tidak sesuai.',
    'distinct' => ':attribute berisi pilihan yang sama lebih dari sekali.',
    'email' => 'Format :attribute tidak valid.',
    'ends_with' => ':attribute harus diakhiri salah satu dari: :values.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'file' => ':attribute harus berupa berkas.',
    'filled' => ':attribute wajib diisi.',
    'gt' => [
        'array' => ':attribute harus berisi lebih dari :value pilihan.',
        'file' => 'Ukuran :attribute harus lebih dari :value kilobita.',
        'numeric' => ':attribute harus lebih besar dari :value.',
        'string' => ':attribute harus lebih dari :value karakter.',
    ],
    'gte' => [
        'array' => ':attribute harus berisi :value pilihan atau lebih.',
        'file' => 'Ukuran :attribute harus :value kilobita atau lebih.',
        'numeric' => ':attribute harus :value atau lebih.',
        'string' => ':attribute harus :value karakter atau lebih.',
    ],
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'in_array' => ':attribute tidak ada di dalam :other.',
    'integer' => ':attribute harus berupa angka bulat.',
    'lt' => [
        'array' => ':attribute harus berisi kurang dari :value pilihan.',
        'file' => 'Ukuran :attribute harus kurang dari :value kilobita.',
        'numeric' => ':attribute harus lebih kecil dari :value.',
        'string' => ':attribute harus kurang dari :value karakter.',
    ],
    'lte' => [
        'array' => ':attribute harus berisi tidak lebih dari :value pilihan.',
        'file' => 'Ukuran :attribute harus :value kilobita atau kurang.',
        'numeric' => ':attribute harus :value atau kurang.',
        'string' => ':attribute harus :value karakter atau kurang.',
    ],
    'max' => [
        'array' => ':attribute maksimal berisi :max pilihan.',
        'file' => 'Ukuran :attribute maksimal :max kilobita.',
        'numeric' => ':attribute maksimal bernilai :max.',
        'string' => ':attribute maksimal :max karakter.',
    ],
    'mimes' => ':attribute harus berupa berkas bertipe: :values.',
    'mimetypes' => ':attribute harus berupa berkas bertipe: :values.',
    'min' => [
        'array' => ':attribute minimal berisi :min pilihan.',
        'file' => 'Ukuran :attribute minimal :min kilobita.',
        'numeric' => ':attribute minimal bernilai :min.',
        'string' => ':attribute minimal :min karakter.',
    ],
    'not_in' => ':attribute yang dipilih tidak valid.',
    'not_regex' => 'Format :attribute tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'present' => ':attribute wajib ada.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi bila :other bernilai :value.',
    'required_unless' => ':attribute wajib diisi kecuali :other bernilai :values.',
    'required_with' => ':attribute wajib diisi bila ada :values.',
    'required_with_all' => ':attribute wajib diisi bila ada :values.',
    'required_without' => ':attribute wajib diisi bila tidak ada :values.',
    'required_without_all' => ':attribute wajib diisi bila tidak ada satupun :values.',
    'same' => ':attribute dan :other harus sama.',
    'size' => [
        'array' => ':attribute harus berisi :size pilihan.',
        'file' => 'Ukuran :attribute harus :size kilobita.',
        'numeric' => ':attribute harus bernilai :size.',
        'string' => ':attribute harus :size karakter.',
    ],
    'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal diunggah.',
    'url' => 'Format :attribute tidak valid.',

    /*
    |--------------------------------------------------------------------------
    | Nama Kolom
    |--------------------------------------------------------------------------
    |
    | Supaya pesan terbaca wajar, nama kolom teknis diganti dengan sebutan yang
    | dipakai di layar. Tanpa ini pesan akan berbunyi seperti
    | "nisn wajib diisi." alih-alih "NISN wajib diisi."
    |
    */

    'attributes' => [
        'alamat' => 'Alamat',
        'attendance_active_days' => 'Hari Aktif Absensi',
        'catatan' => 'Catatan',
        'email' => 'Email',
        'jenis_pelanggaran_id' => 'Jenis Pelanggaran',
        'judul' => 'Judul',
        'jumlah_poin' => 'Jumlah Poin',
        'kategori' => 'Kategori',
        'kelas_id' => 'Kelas',
        'keterangan' => 'Keterangan',
        'kml_file' => 'Berkas KML',
        'nama' => 'Nama',
        'nip' => 'NIP',
        'nis' => 'NIS',
        'nisn' => 'NISN',
        'password' => 'Kata Sandi',
        'password_confirmation' => 'Ulangi Kata Sandi',
        'pengurangan_poin' => 'Pengurangan Poin',
        'peran' => 'Peran',
        'profil_siswa_id' => 'Siswa',
        'selfie' => 'Selfie',
        'siswa_nisn' => 'Siswa',
        // Kolom pilihan siswa berupa daftar, sehingga kuncinya bernomor
        // (siswa_nisn.0, siswa_nisn.1, ...). Tanpa baris ini, nomor itu ikut
        // terbaca pengguna sebagai "siswa_nisn.0 yang dipilih tidak valid."
        'siswa_nisn.*' => 'Siswa',
        'status' => 'Status',
        'tanggal_pelanggaran' => 'Tanggal Pelanggaran',
        'telepon' => 'Nomor HP',
        'tingkat' => 'Tingkat',
        'wali_kelas_id' => 'Wali Kelas',
    ],
];
