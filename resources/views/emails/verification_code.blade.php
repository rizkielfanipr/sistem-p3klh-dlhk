<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Email Anda</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 20px auto; padding: 20px; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #0056b3; text-align: center; margin-bottom: 20px;">Verifikasi Alamat Email Anda</h2>
        <p>Halo, <strong style="color: #0056b3;">{{ $name }}</strong>!</p>
        <p>Terima kasih telah mendaftar. Untuk menyelesaikan pendaftaran Anda, silakan gunakan kode verifikasi di bawah ini:</p>
        <div style="text-align: center; margin: 30px 0;">
            <span style="display: inline-block; font-size: 32px; font-weight: bold; color: #007bff; background-color: #e7f3ff; padding: 15px 25px; border: 2px dashed #007bff; border-radius: 8px;">
                {{ $code }}
            </span>
        </div>
        <p style="text-align: center; font-size: 14px; color: #666;">Kode ini berlaku selama 10 menit.</p>
        <p>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>
        <p>Salam Hormat,<br>Tim Kami</p>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">
        <p style="font-size: 12px; color: #999; text-align: center;">Ini adalah email otomatis, mohon jangan balas.</p>
    </div>
</body>
</html>
