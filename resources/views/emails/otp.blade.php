<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kode Verifikasi Sentri Siswa</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .wrapper { max-width: 480px; margin: 40px auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; }
        .header { background: linear-gradient(135deg, #1c6880, #0f766e); padding: 32px 32px 24px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0; font-weight: 700; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.75); font-size: 13px; margin: 4px 0 0; }
        .body { padding: 32px; }
        .body p { color: #475569; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
        .otp-box { text-align: center; margin: 28px 0; background: #f1f5f9; border-radius: 12px; padding: 24px; }
        .otp-code { font-size: 42px; font-weight: 800; letter-spacing: 12px; color: #1c6880; font-family: monospace; }
        .expiry { display: inline-block; margin-top: 10px; font-size: 12px; color: #64748b; background: #e2e8f0; border-radius: 20px; padding: 4px 12px; }
        .note { background: #fef9c3; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #92400e; margin-top: 16px; }
        .footer { padding: 20px 32px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Sentri Siswa</h1>
            <p>SMAN 11 Kabupaten Tangerang</p>
        </div>
        <div class="body">
            <p>Halo,</p>
            <p>Anda menerima kode verifikasi untuk melanjutkan proses di <strong>Sentri Siswa</strong>. Masukkan kode berikut:</p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <span class="expiry">Berlaku selama {{ $expiryMinutes }} menit</span>
            </div>

            <div class="note">
                Jika Anda tidak meminta kode ini, abaikan email ini. Jangan bagikan kode ini kepada siapapun.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Sentri Siswa &mdash; SMAN 11 Kabupaten Tangerang
        </div>
    </div>
</body>
</html>
