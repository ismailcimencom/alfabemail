<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Şifre Belirle | Alfabe</title>
  <link rel="icon" type="image/png" href="/favicon.png" />
  <link rel="stylesheet" href="/css/portal.css" />
  <style>
    body { background: linear-gradient(135deg, #f8fafc, #f1f5f9); min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; font-family: system-ui, -apple-system, sans-serif; }
    .container { background: #fff; border-radius: 24px; padding: 40px; max-width: 460px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,.08); text-align: center; }
    h1 { margin: 0 0 6px; font-size: 24px; color: #1a202c; }
    .sub { color: #6586a7; margin: 0 0 24px; font-size: 14px; }
    .field { margin-bottom: 16px; text-align: left; }
    label { display: block; font-weight: 600; font-size: 14px; color: #1a202c; margin-bottom: 4px; }
    input { width: 100%; padding: 12px 14px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; box-sizing: border-box; color: #1a202c; }
    input:focus { border-color: #5e8df7; outline: none; box-shadow: 0 0 0 3px rgba(94,141,247,.15); }
    .btn-primary { background: #5e8df7; color: #fff; border: none; border-radius: 999px; padding: 14px; font-size: 16px; font-weight: 700; cursor: pointer; width: 100%; margin-top: 8px; }
    .btn-primary:hover { background: #4c72e5; }
    .error { color: #ef4444; font-size: 13px; display: none; margin-top: 4px; }
    .success-msg { display: none; }
    .success-msg .icon { font-size: 60px; margin-bottom: 10px; }
    .info-box { background: #f0f5ff; border-radius: 12px; padding: 14px; margin-bottom: 20px; font-size: 14px; color: #1a202c; text-align: left; }
    .info-box strong { color: #5e8df7; }
  </style>
</head>
<body>
  <div class="container">
    <div id="formArea">
      <h1>🔐 Şifre Belirleyin</h1>
      <p class="sub">Öğretmen panelinize giriş yapmak için şifrenizi belirleyin</p>

      <div class="info-box">
        ✅ <strong>{{ count($results['success'] ?? []) }} öğrenci</strong> için mail başarıyla açıldı.<br>
        📁 Sınıf: <strong>{{ $results['sinif']['ad'] ?? '' }}</strong> — Demo Okul<br>
        📧 Hesabınız: <strong>{{ session('toplu_mail_ogretmen_email') }}</strong>
      </div>

      <div class="field">
        <label>Yeni Şifre (en az 6 karakter)</label>
        <input type="password" id="password" minlength="6" placeholder="••••••••" />
      </div>
      <div class="field">
        <label>Şifre Tekrar</label>
        <input type="password" id="passwordConfirm" placeholder="••••••••" />
      </div>
      <div class="error" id="passError"></div>
      <button type="button" class="btn-primary" onclick="setPassword()" id="passBtn">Şifreyi Kaydet</button>
    </div>

    <div class="success-msg" id="successArea">
      <div class="icon">🎉</div>
      <h3 style="margin:0 0 4px;">Şifreniz Belirlendi!</h3>
      <p style="color:#6586a7;margin:0 0 20px;">Giriş yapmak için yönlendiriliyorsunuz...</p>
    </div>
  </div>

  <script>
    async function setPassword() {
      const password = document.getElementById('password').value;
      const confirm = document.getElementById('passwordConfirm').value;
      const errEl = document.getElementById('passError');

      if (password.length < 6) { errEl.textContent = 'Şifre en az 6 karakter olmalı.'; errEl.style.display = 'block'; return; }
      if (password !== confirm) { errEl.textContent = 'Şifreler eşleşmiyor.'; errEl.style.display = 'block'; return; }
      errEl.style.display = 'none';

      const btn = document.getElementById('passBtn');
      btn.disabled = true; btn.textContent = 'Kaydediliyor...';

      try {
        const res = await fetch('{{ route("ogretmen.toplu-mail.set-password") }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ password, password_confirmation: confirm })
        });
        const data = await res.json();
        if (data.success) {
          document.getElementById('formArea').style.display = 'none';
          document.getElementById('successArea').style.display = 'block';
          setTimeout(() => { window.location.href = data.redirect; }, 2000);
        } else {
          errEl.textContent = data.message || 'Bir hata oluştu.';
          errEl.style.display = 'block';
        }
      } catch (e) {
        errEl.textContent = 'Bir hata oluştu.';
        errEl.style.display = 'block';
      }
      finally { btn.disabled = false; btn.textContent = 'Şifreyi Kaydet'; }
    }
  </script>
</body>
</html>
