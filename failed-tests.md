
>    FAIL  Tests\Feature\CekAbsensiPublikep
    Γ£ô nisn yang tidak dikenal ditolak beserta alasannya                                                                                                                              0.17s  
    Γ£ô nisn anak dari kelas lain tidak bisa dilihat lewat link kelas ini                                                                                                              0.15s  
    Γ¿» nisn yang dikosongkan ditolak                                                                                                                                                  0.19s  
    Γ£ô link yang tidak dikenal tidak membuka absensi siapa pun                                                                                                                        0.17s  
  
     PASS  Tests\Feature\GantiSandiep
    Γ£ô kode otp yang salah ditolak                                                                                                                                                    0.20s  
    Γ£ô kode otp yang belum diisi ditolak                                                                                                                                              0.26s  
    Γ£ô konfirmasi kata sandi baru yang tidak sama ditolak                                                                                                                             0.35s  
    Γ£ô kata sandi baru tanpa angka ditolak                                                                                                                                            0.21s  
  
     PASS  Tests\Feature\Guruep
    Γ£ô admin berhasil menambah guru wali kelas dengan data yang benar                                                                                                                 0.24s  
    Γ£ô admin berhasil menambah guru bk beserta tingkat yang dipegang                                                                                                                  0.17s  
    Γ£ô admin berhasil menambah guru kesiswaan                                                                                                                                         0.18s  
    Γ£ô admin berhasil menambah wali kelas sekaligus memilihkan kelasnya                                                                                                               0.24s  
    Γ£ô admin gagal menambah guru karena guru bk tidak memilih tingkat                                                                                                                 0.16s  
    Γ£ô admin gagal menambah guru karena nip sudah dipakai guru lain                                                                                                                   0.17s  
    Γ£ô admin gagal menambah guru karena nama mengandung angka                                                                                                                         0.16s  
    Γ£ô admin berhasil mengubah data guru yang sudah ada                                                                                                                               0.24s  
>    FAIL  Tests\Feature\LokasiAbsenep
    Γ¿» admin berhasil mengunggah berkas area absensi                                                                                                                                  0.16s  
    Γ£ô admin gagal mengunggah berkas yang bukan berkas area                                                                                                                           0.17s  
    Γ£ô admin gagal mengunggah berkas area yang tidak memuat gambar area                                                                                                               0.17s  
    Γ£ô admin gagal mengunggah berkas area yang titiknya kurang dari tiga                                                                                                              0.16s  
    Γ£ô admin berhasil menyimpan toleransi jarak                                                                                                                                       0.17s  
    Γ£ô admin gagal menyimpan toleransi jarak yang bukan angka                                                                                                                         0.16s  
  
     PASS  Tests\Feature\LupaSandiep
    Γ£ô pengguna meminta tautan pemulihan dengan email terdaftar                                                                                                                       0.36s  
    Γ£ô email yang belum terdaftar tetap diberi jawaban yang sama                                                                                                                      0.33s  
    Γ£ô konfirmasi kata sandi yang tidak sama ditolak                                                                                                                                  0.35s  
    Γ£ô tautan pemulihan yang tidak dikenali ditolak                                                                                                                                   0.32s  
  
>    FAIL  Tests\Feature\MonitorAbsensiep
    Γ¿» wali kelas menetapkan status absensi siswa secara manual with dataset "hadir"                                                                                                  0.23s  
    Γ¿» wali kelas menetapkan status absensi siswa secara manual with dataset "izin"                                                                                                   0.21s  
    Γ¿» wali kelas menetapkan status absensi siswa secara manual with dataset "sakit"                                                                                                  0.20s  
    Γ¿» wali kelas menetapkan status absensi siswa secara manual with dataset "alpha"                                                                                                  0.22s  
    Γ£ô wali kelas mencari siswa di kelasnya berdasarkan nama                                                                                                                          0.21s  
  
     PASS  Tests\Feature\MonitoringAbsensiBkep
    Γ£ô guru bk mencari siswa berdasarkan nama                                                                                                                                         0.22s  
    Γ£ô guru bk menyaring siswa berdasarkan kelas di tingkatnya                                                                                                                        0.20s  
    Γ£ô pencarian yang tidak menemukan siswa menampilkan keterangannya                                                                                                                 0.19s  
  
     PASS  Tests\Feature\MonitoringSiswaep
    Γ£ô kesiswaan mencari siswa berdasarkan nama                                                                                                                                       0.21s  
    Γ£ô kesiswaan mencari siswa berdasarkan NIS                                                                                                                                        0.21s  
    Γ£ô kesiswaan menyaring siswa berdasarkan kelas                                                                                                                                    0.20s  
    Γ£ô daftar monitoring yang tidak menemukan siswa menampilkan keterangannya                                                                                                         0.20s  
  
     PASS  Tests\Feature\PelanggaranSiswaKesiswaanep
    Γ£ô kesiswaan mencatat pelanggaran seorang siswa                                                                                                                                   0.32s  
    Γ£ô pelanggaran ditolak ketika siswanya belum mendaftarkan akun                                                                                                                    0.22s  
>    FAIL  Tests\Feature\WaktuAbsenep
    Γ¿» admin berhasil menyimpan konfigurasi waktu absen yang sah                                                                                                                      0.17s  
    Γ£ô admin berhasil mengatur hari aktif absensi termasuk hari sabtu                                                                                                                 0.33s  
    Γ£ô admin gagal menyimpan karena jam selesai sama dengan jam mulai                                                                                                                 0.18s  
    Γ£ô admin gagal menyimpan karena jam selesai lebih awal dari jam mulai                                                                                                             0.20s  
    Γ£ô admin gagal menyimpan karena tidak memilih satu pun hari aktif                                                                                                                 0.35s  
    Γ£ô admin gagal menyimpan karena jam yang dipilih di luar rentang yang tersedia                                                                                                    0.19s  
  
     PASS  Tests\Feature\WhatsappApiep
    Γ£ô admin berhasil menyimpan token layanan pengirim whatsapp                                                                                                                       0.29s  
    Γ£ô token yang ditolak layanan pengirim tidak ikut tersimpan                                                                                                                       0.17s  
    Γ£ô admin menyaring riwayat pesan berdasarkan status pengiriman                                                                                                                    0.16s  
  
>    FAIL  Tests\Feature\AbsensiSiswabva
    Γ¿» absen pukul 06:29 ditolak                                                                                                                                                      0.22s  
    Γ£ô absen pukul 06:30 diterima dan tercatat hadir                                                                                                                                  0.22s  
    Γ£ô absen pukul 07:00 masih diterima dan tercatat hadir                                                                                                                            0.24s  
    Γ¿» absen pukul 07:01 ditolak                                                                                                                                                      0.25s  
    Γ£ô selfie berukuran tepat 300 KB diterima                                                                                                                                         0.23s  
    Γ£ô selfie berukuran lebih dari 1 mb ditolak                                                                                                                                       0.23s  
  
>    FAIL  Tests\Feature\CekAbsensiPublikbva
    Γ¿» link masih bisa dibuka semenit sebelum hari itu berakhir                                                                                                                       0.18s  
    Γ£ô link tidak bisa dibuka lagi setelah harinya berganti                                                                                                                           0.14s  
  
     PASS  Tests\Feature\GantiSandibva
    Γ£ô kode otp sepanjang 5 angka ditolak                                                                                                                                             0.18s  
    Γ£ô kode otp sepanjang 6 angka diterima                                                                                                                                            0.18s  
    Γ£ô kode otp masih diterima sesaat sebelum 10 menit                                                                                                                                0.20s  
    Γ£ô kode otp ditolak tepat setelah 10 menit                                                                                                                                        0.17s  
    Γ£ô kata sandi baru sepanjang 7 karakter ditolak                                                                                                                                   0.21s  
    Γ£ô kata sandi baru sepanjang 8 karakter diterima                                                                                                                                  0.22s  
  
     PASS  Tests\Feature\Gurubva
    Γ£ô nama guru dua karakter ditolak karena kurang dari batas minimum                                                                                                                0.18s  
    Γ£ô nama guru tiga karakter diterima karena tepat di batas minimum                                                                                                                 0.19s  
    Γ£ô nama guru seratus karakter diterima karena tepat di batas maksimum                                                                                                             0.20s  
    Γ£ô nama guru seratus satu karakter ditolak karena melebihi batas maksimum                                                                                                         0.18s  
    Γ£ô nip tiga puluh karakter diterima karena tepat di batas maksimum                                                                                                                0.18s  
    Γ£ô nip tiga puluh satu karakter ditolak karena melebihi batas maksimum                                                                                                            0.16s  
  
     PASS  Tests\Feature\JenisPelanggaranbva
>    FAIL  Tests\Feature\PengajuanPoinWalibva
    Γ¿» alasan kosong ditolak                                                                                                                                                          0.20s  
    Γ£ô alasan sepanjang satu karakter diterima                                                                                                                                        0.43s  
    Γ£ô alasan sepanjang 999 karakter diterima                                                                                                                                         0.39s  
    Γ£ô alasan sepanjang 1000 karakter diterima                                                                                                                                        0.39s  
    Γ¿» alasan sepanjang 1001 karakter ditolak                                                                                                                                         0.19s  
  
     PASS  Tests\Feature\PersetujuanPoinKesiswaanbva
    Γ£ô jumlah poin 1 diterima                                                                                                                                                         0.22s  
    Γ£ô jumlah poin 100 diterima                                                                                                                                                       0.22s  
    Γ£ô alasan penolakan sepanjang 1000 karakter diterima                                                                                                                              0.19s  
    Γ£ô alasan penolakan sepanjang 1001 karakter ditolak                                                                                                                               0.43s  
  
     PASS  Tests\Feature\PoinSiswabva
    Γ£ô poin 76 diberi keterangan Baik                                                                                                                                                 0.42s  
    Γ£ô poin 75 diberi keterangan Cukup                                                                                                                                                0.38s  
    Γ£ô poin 51 diberi keterangan Cukup                                                                                                                                                0.36s  
    Γ£ô poin 50 diberi keterangan Perhatian                                                                                                                                            0.37s  
    Γ£ô poin berhenti di nol ketika potongannya pas menghabiskan poin                                                                                                                  0.37s  
    Γ£ô poin tetap nol dan tidak minus ketika potongannya melebihi poin yang tersisa                                                                                                   0.40s  
  
>    FAIL  Tests\Feature\AbsensiSiswastt
    Γ¿» siswa gagal absen sebelum jam absen dibuka                                                                                                                                     0.23s  
    Γ¿» siswa gagal absen setelah jam absen berakhir                                                                                                                                   0.21s  
    Γ£ô siswa gagal absen di hari yang bukan hari absensi                                                                                                                              0.21s  
    Γ£ô siswa tidak bisa absen dua kali dalam sehari                                                                                                                                   0.23s  
    Γ¿» siswa yang tidak absen sampai jam absen berakhir tercatat alpha                                                                                                                0.21s  
    Γ£ô siswa yang terlanjur dicap alpha tetap bisa absen selama jam absennya masih dibuka                                                                                             0.58s  
    Γ£ô siswa tidak bisa menimpa status izin yang sudah ditetapkan wali kelasnya                                                                                                       0.20s  
    Γ£ô siswa yang sudah absen tidak ikut berubah jadi alpha saat jam absen berakhir                                                                                                   0.58s  
  
     PASS  Tests\Feature\GantiSandistt
    Γ£ô pengguna meminta kode otp untuk mengganti kata sandi                                                                                                                           0.17s  
    Γ£ô pengguna mengganti kata sandi setelah kode otp benar                                                                                                                           0.24s  
    Γ£ô pengguna bisa masuk memakai kata sandi barunya                                                                                                                                 5.30s  
    Γ£ô kode otp yang sudah kedaluwarsa ditolak                                                                                                                                        0.18s  
    Γ£ô halaman kata sandi baru tidak terbuka sebelum kode otp diverifikasi                                                                                                            0.19s  
    Γ£ô kode otp tidak bisa dikirim ulang sebelum jedanya lewat                                                                                                                        0.17s  
    Γ£ô kode otp bisa dikirim ulang setelah jedanya lewat                                                                                                                              0.19s  
    Γ£ô kode otp yang sudah terpakai tidak bisa dipakai untuk kedua kalinya                                                                                                            0.24s  
  
     PASS  Tests\Feature\Gurustt
>    FAIL  Tests\Feature\MonitorAbsensistt
    Γ¿» wali kelas mengubah status absensi yang sudah tercatat sebelumnya                                                                                                              0.24s  
    Γ¿» wali kelas gagal menetapkan status absensi di hari yang bukan hari absensi                                                                                                     0.22s  
  
     PASS  Tests\Feature\MonitoringPoinBkstt
    Γ£ô poin siswa bertambah setelah pengajuan poin disetujui                                                                                                                          0.52s  
    Γ£ô pengajuan poin yang belum disetujui belum menambah poin siswa                                                                                                                  0.50s  
  
     PASS  Tests\Feature\MonitoringSiswastt
    Γ£ô poin siswa bertambah setelah pengajuan poin disetujui                                                                                                                          0.57s  
    Γ£ô siswa yang belum mendaftarkan akun tidak ikut dipantau                                                                                                                         0.20s  
  
     PASS  Tests\Feature\PelanggaranSiswaKesiswaanstt
    Γ£ô poin siswa berkurang sesuai jenis pelanggaran yang dipilih                                                                                                                     0.54s  
    Γ£ô pelanggaran ditolak ketika jenis pelanggarannya sudah dinonaktifkan                                                                                                            0.22s  
    Γ£ô poin siswa kembali utuh setelah catatan pelanggarannya dihapus                                                                                                                 0.66s  
  
     PASS  Tests\Feature\PersetujuanPoinKesiswaanstt
    Γ£ô kesiswaan menyetujui pengajuan poin sambil menentukan besar poinnya                                                                                                            0.20s  
    Γ£ô poin siswa bertambah setelah pengajuannya disetujui                                                                                                                            0.54s  
    Γ£ô kesiswaan menolak pengajuan poin dengan menuliskan alasannya                                                                                                                   0.19s  
>    FAILED  Tests\Feature\CekAbsensiPublikep > nisn yang dikosongkan ditolak                                                                                                               
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="MRxUuZskRgcn3TnJislxxUeXsf2FfWMzGkfl9r4w">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Sentri Siswa</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="relative min-h-screen overflow-x-hidden bg-slate-50 font-sans antialiased flex items-center justify-center py-12 px-4">\n
      <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">\n
          <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-primary/10 blur-3xl"></div>\n
          <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-teal-500/5 blur-3xl"></div>\n
      </div>\n
  \n
      <div class="relative z-10 w-full max-w-md mx-auto">\n
>    FAILED  Tests\Feature\LokasiAbsenep > admin berhasil mengunggah berkas area absensi                                                                                                    
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="azj7WUvPXSWdCpxrub5jYjomtU4uWEzjBGevpN3C">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Lokasi Presensi</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="min-h-screen bg-gray-50 font-sans antialiased">\n
      <div class="flex min-h-screen">\n
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">\n
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">\n
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">\n
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>\n
      </div>\n
>    FAILED  Tests\Feature\MonitorAbsensiep > wali kelas menetapkan status absensi siswa secara manual with dataset "hadir"                                                                 
>   Failed asserting that '<!DOCTYPE html>
  <html lang="id">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="NSXbAU3TW9b7oVvMFEw6Bm4kMBJWms18BjToRs0K">
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">
      <title>Kelas Saya</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>
  <body class="min-h-screen bg-gray-50 font-sans antialiased">
      <div class="flex min-h-screen">
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>
      </div>
  
>    FAILED  Tests\Feature\MonitorAbsensiep > wali kelas menetapkan status absensi siswa secara manual with dataset "izin"                                                                  
>   Failed asserting that '<!DOCTYPE html>
  <html lang="id">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="gUgkXi3et7x1ALU9A2weBUBC1lPEh3VJQcg37e4L">
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">
      <title>Kelas Saya</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>
  <body class="min-h-screen bg-gray-50 font-sans antialiased">
      <div class="flex min-h-screen">
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>
      </div>
  
>    FAILED  Tests\Feature\MonitorAbsensiep > wali kelas menetapkan status absensi siswa secara manual with dataset "sakit"                                                                 
>   Failed asserting that '<!DOCTYPE html>
  <html lang="id">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="tadMqGs0VeqImWrQCJqQwRz1wu9Rx81hu4568npC">
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">
      <title>Kelas Saya</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>
  <body class="min-h-screen bg-gray-50 font-sans antialiased">
      <div class="flex min-h-screen">
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>
      </div>
  
>    FAILED  Tests\Feature\MonitorAbsensiep > wali kelas menetapkan status absensi siswa secara manual with dataset "alpha"                                                                 
>   Failed asserting that '<!DOCTYPE html>
  <html lang="id">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="AcSLI0Be9v2AJUtEJZciqKu48UkvoXzJovyhBWdy">
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">
      <title>Kelas Saya</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>
  <body class="min-h-screen bg-gray-50 font-sans antialiased">
      <div class="flex min-h-screen">
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>
      </div>
  
>    FAILED  Tests\Feature\WaktuAbsenep > admin berhasil menyimpan konfigurasi waktu absen yang sah                                                                                         
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="LdBU8MPQswV2qimqHUBlgduituY7ePdVUIU5ey6b">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Waktu Presensi</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="min-h-screen bg-gray-50 font-sans antialiased">\n
      <div class="flex min-h-screen">\n
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">\n
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">\n
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">\n
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>\n
      </div>\n
>    FAILED  Tests\Feature\AbsensiSiswabva > absen pukul 06:29 ditolak                                                                                                                      
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="BQNbPb2gQcpCZCMcYgWYROmNO8CZSB1ukmx5NqCs">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Presensi</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="min-h-screen bg-gray-50 font-sans antialiased">\n
      <div class="flex min-h-screen">\n
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">\n
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">\n
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">\n
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>\n
      </div>\n
>    FAILED  Tests\Feature\AbsensiSiswabva > absen pukul 07:01 ditolak                                                                                                                      
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="llDY94XnCyaMS8GaImOHc6wvNbbKDitJ4yPt4lOB">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Presensi</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="min-h-screen bg-gray-50 font-sans antialiased">\n
      <div class="flex min-h-screen">\n
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">\n
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">\n
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">\n
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>\n
      </div>\n
>    FAILED  Tests\Feature\CekAbsensiPublikbva > link masih bisa dibuka semenit sebelum hari itu berakhir                                                                                   
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="3F9bSTQKs2j6zkZf4yvzHxlDqoCAE0PFQo5iQZlt">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Sentri Siswa</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="relative min-h-screen overflow-x-hidden bg-slate-50 font-sans antialiased flex items-center justify-center py-12 px-4">\n
      <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">\n
          <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-primary/10 blur-3xl"></div>\n
          <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-teal-500/5 blur-3xl"></div>\n
      </div>\n
  \n
      <div class="relative z-10 w-full max-w-md mx-auto">\n
>    FAILED  Tests\Feature\PengajuanPoinWalibva > alasan kosong ditolak                                                                                                                     
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="InuCZO7VMEoMCtMUv9wzbXC6wCs03hGovqGaqvjG">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Buat Pengajuan Poin</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="min-h-screen bg-gray-50 font-sans antialiased">\n
      <div class="flex min-h-screen">\n
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">\n
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">\n
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">\n
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>\n
      </div>\n
>    FAILED  Tests\Feature\PengajuanPoinWalibva > alasan sepanjang 1001 karakter ditolak                                                                                                    
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="36475sHDYCv2stcsXXTusjBnFZ8FbTJLOFmdwIdS">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Buat Pengajuan Poin</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="min-h-screen bg-gray-50 font-sans antialiased">\n
      <div class="flex min-h-screen">\n
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">\n
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">\n
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">\n
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>\n
      </div>\n
>    FAILED  Tests\Feature\AbsensiSiswastt > siswa gagal absen sebelum jam absen dibuka                                                                                                     
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="TiA8rsq99Uf1qBxd3XDdRuPOChcuCHTb89DvG3OE">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Presensi</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="min-h-screen bg-gray-50 font-sans antialiased">\n
      <div class="flex min-h-screen">\n
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">\n
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">\n
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">\n
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>\n
      </div>\n
>    FAILED  Tests\Feature\AbsensiSiswastt > siswa gagal absen setelah jam absen berakhir                                                                                                   
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="B2wVzcsWMvXVsuivMXhLVl1h3FaejzpGXtWfh3Jy">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Presensi</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="min-h-screen bg-gray-50 font-sans antialiased">\n
      <div class="flex min-h-screen">\n
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">\n
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">\n
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">\n
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>\n
      </div>\n
>    FAILED  Tests\Feature\AbsensiSiswastt > siswa yang tidak absen sampai jam absen berakhir tercatat alpha                                                                                
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="37zeubMSTHimLO8Onb7BAw0wGwSnBSY7XHsXm2QM">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Presensi</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="min-h-screen bg-gray-50 font-sans antialiased">\n
      <div class="flex min-h-screen">\n
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">\n
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">\n
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">\n
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>\n
      </div>\n
>    FAILED  Tests\Feature\MonitorAbsensistt > wali kelas mengubah status absensi yang sudah tercatat sebelumnya                                                                            
>   Failed asserting that '<!DOCTYPE html>
  <html lang="id">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="OZPiP8u8Y9krA2xyk6BR0c7sqxogaaoIS7rr8qMA">
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">
      <title>Kelas Saya</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>
  <body class="min-h-screen bg-gray-50 font-sans antialiased">
      <div class="flex min-h-screen">
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>
      </div>
  
>    FAILED  Tests\Feature\MonitorAbsensistt > wali kelas gagal menetapkan status absensi di hari yang bukan hari absensi                                                                   
    Expected: <!DOCTYPE html>\n
  <html lang="id">\n
  <head>\n
      <meta charset="utf-8">\n
      <meta name="viewport" content="width=device-width, initial-scale=1">\n
      <meta name="csrf-token" content="Q2elEbMj1sGk6QRuUZJHC9w2Cu3uQW6SCDAMvLNT">\n
      <link rel="icon" href="https://app.fachriridha.me/favicon.ico" type="image/x-icon">\n
      <link rel="preload" as="image" href="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png">\n
      <title>Kelas Saya</title>\n
      <link rel="preconnect" href="https://fonts.googleapis.com">\n
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">\n
      <link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="preload" as="style" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><link rel="modulepreload" as="script" href="https://app.fachriridha.me/build/assets/app-C3xHUTok.js" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-BoekwtjQ.css" /><link rel="stylesheet" href="https://app.fachriridha.me/build/assets/app-1DTFk8zz.css" /><script type="module" src="https://app.fachriridha.me/build/assets/app-C3xHUTok.js"></script></head>\n
  <body class="min-h-screen bg-gray-50 font-sans antialiased">\n
      <div class="flex min-h-screen">\n
          <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 shrink-0 flex-col border-r border-slate-100 bg-slate-50/90 backdrop-blur-md lg:flex">\n
      <div class="flex h-14 items-center gap-3 border-b border-slate-100 px-4 bg-white/50">\n
          <img src="https://app.fachriridha.me/storage/assets/logo/logo-sidebar.png" alt="Sentri Siswa" class="h-8 w-auto rounded-lg border border-gray-200">\n
          <span class="text-base font-semibold text-gray-900">Sentri Siswa</span>\n
      </div>\n
>   Tests:    17 failed, 475 passed (942 assertions)
    Duration: 157.93s
  

