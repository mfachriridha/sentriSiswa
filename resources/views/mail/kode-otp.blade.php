<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #f9fafb;
            border-radius: 8px;
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 30px;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #1f2937;
            background: #fff;
            padding: 20px 40px;
            border-radius: 8px;
            display: inline-block;
            margin: 20px 0;
            border: 2px dashed #4f46e5;
        }
        .message {
            color: #6b7280;
            font-size: 14px;
            margin-top: 20px;
        }
        .warning {
            color: #dc2626;
            font-size: 12px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">SentriSiswa</div>
        
        <p>Halo,</p>
        
        <p>Berikut kode verifikasi Anda untuk <strong>{{ $tujuan }}</strong>:</p>
        
        <div class="otp-code">{{ $otp }}</div>
        
        <p class="message">
            Kode ini berlaku selama 5 menit. Jangan bagikan kode ini kepada siapapun.
        </p>
        
        <p class="warning">
            Jika Anda tidak meminta kode ini, abaikan email ini.
        </p>
    </div>
</body>
</html>
