<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;padding:30px;background:#f4f7fb;">
  <div style="max-width:480px;margin:auto;background:#fff;border-radius:16px;padding:32px;box-shadow:0 4px 20px rgba(0,0,0,0.06);">
    <div style="font-size:40px;text-align:center;margin-bottom:8px;">🧑‍🏫</div>
    <h2 style="text-align:center;margin:0 0 6px;">Öğretmen Hesabın Oluşturuldu</h2>
    <p style="text-align:center;color:#6586a7;margin:0 0 20px;font-size:14px;">
      Alfabe Mail yönetim paneline hoş geldin!
    </p>
    <p style="font-size:14px;color:#1a202c;margin:0 0 16px;">
      Hesabın yöneticin tarafından oluşturuldu. Giriş yapabilmek için önce şifreni belirlemelisin.
    </p>
    <div style="text-align:center;margin:24px 0;">
      <a href="{{ url('/ogretmen/sifre-belirle/' . $token) }}" style="display:inline-block;background:#5e8df7;color:#fff;border-radius:999px;padding:14px 32px;font-size:16px;font-weight:700;text-decoration:none;">
        Şifre Belirle
      </a>
    </div>
    <p style="text-align:center;color:#94a3b8;font-size:13px;margin:0;">
      E-posta: <strong>{{ $email }}</strong><br>
      Bu link 24 saat süreyle geçerlidir.
    </p>
  </div>
</body>
</html>
