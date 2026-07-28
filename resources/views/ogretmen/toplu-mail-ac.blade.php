<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kaydol | Alfabe</title>
  <link rel="icon" type="image/png" href="/favicon.png" />
  <link rel="stylesheet" href="/css/portal.css" />
  <style>
    body { background: linear-gradient(135deg, #f8fafc, #f1f5f9); min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; font-family: system-ui, -apple-system, sans-serif; }
    .container { background: #fff; border-radius: 24px; padding: 40px; max-width: 800px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,.08); }
    h1 { margin: 0 0 6px; font-size: 26px; color: #1a202c; text-align: center; }
    .sub { color: #6586a7; text-align: center; margin: 0 0 28px; font-size: 14px; }
    .field { margin-bottom: 18px; }
    label { display: block; font-weight: 600; font-size: 14px; color: #1a202c; margin-bottom: 4px; }
    input { width: 100%; padding: 11px 14px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; box-sizing: border-box; color: #1a202c; }
    input:focus { border-color: #5e8df7; outline: none; box-shadow: 0 0 0 3px rgba(94,141,247,.15); }
    .btn-primary { background: #5e8df7; color: #fff; border: none; border-radius: 999px; padding: 14px; font-size: 16px; font-weight: 700; cursor: pointer; width: 100%; }
    .btn-primary:disabled { opacity: .5; cursor: not-allowed; }
    .btn-primary:hover:not(:disabled) { background: #4c72e5; }
    .error { color: #ef4444; font-size: 13px; display: none; margin-top: 4px; }
    .step { display: none; }
    .step.active { display: block; }
    .back-link { display: block; text-align: center; margin-top: 16px; color: #6586a7; font-size: 14px; text-decoration: none; }
    .back-link:hover { color: #5e8df7; }

    .demo-row {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 14px; border-radius: 12px;
      background: #f8fafc; border: 1px solid #e2e8f0;
      opacity: 0; transform: translateY(12px);
      margin-bottom: 8px;
    }
    .demo-row .avatar { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0; }
    .demo-row .name { flex: 1; font-size: 14px; color: #1a202c; font-weight: 500; }
    .demo-row .status { font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px; }

    @keyframes demoSlideIn {
      0%   { opacity: 0; transform: translateY(12px); }
      15%  { opacity: 1; transform: translateY(0); }
      85%  { opacity: 1; transform: translateY(0); }
      100% { opacity: 0; transform: translateY(-4px); }
    }
    @keyframes statusAppear {
      0%   { opacity: 0; }
      40%  { opacity: 0; }
      50%  { opacity: 1; }
      100% { opacity: 1; }
    }
    .demo-row:nth-child(1) { animation: demoSlideIn 4s ease-in-out infinite; animation-delay: 0s; }
    .demo-row:nth-child(2) { animation: demoSlideIn 4s ease-in-out infinite; animation-delay: 1.3s; }
    .demo-row:nth-child(3) { animation: demoSlideIn 4s ease-in-out infinite; animation-delay: 2.6s; }
    .demo-row .status { animation: statusAppear 4s ease-in-out infinite; }
    .demo-row:nth-child(1) .status { animation-delay: 0s; }
    .demo-row:nth-child(2) .status { animation-delay: 1.3s; }
    .demo-row:nth-child(3) .status { animation-delay: 2.6s; }
  </style>
</head>
<body>
  <div class="container" id="app">
    <h1>📧 Öğretmen Kaydı</h1>
    <p class="sub">Sınıfına mail açmak için kaydol, panelden öğrencilerini yönet</p>

    {{-- Step 1: Teacher info + demo --}}
    <div class="step active" id="step1">
      <div style="display:flex;gap:24px;flex-wrap:wrap;">
        {{-- Left: Teacher fields --}}
        <div style="flex:1;min-width:200px;">
          <div class="field">
            <label>Öğretmen Ad Soyad</label>
            <input type="text" id="ogretmenName" placeholder="Adın Soyadın" />
          </div>
          <div class="field">
            <label>E-posta Adresiniz</label>
            <input type="email" id="ogretmenEmail" placeholder="ornek@mail.com" />
            <div class="error" id="emailError"></div>
          </div>
          <div class="field">
            <label>Telefon</label>
            <input type="tel" id="ogretmenPhone" placeholder="05XX XXX XX XX" />
          </div>
          <div class="field">
            <label>Sınıf Adı</label>
            <input type="text" id="ogretmenSchool" placeholder="Örn: 4-A" />
          </div>
        </div>

        {{-- Right: Animated demo --}}
        <div style="flex:1;min-width:200px;background:#1e1b4b;border-radius:16px;padding:20px;">
          <div style="display:flex;align-items:center;gap:6px;margin-bottom:14px;">
            <span style="font-size:10px;color:#22c55e;background:rgba(34,197,94,0.15);padding:3px 8px;border-radius:999px;font-weight:600;">CANLI</span>
            <span style="font-size:12px;color:#a5b4fc;">Öğrenci mailleri açılıyor...</span>
          </div>
          <div class="demo-row">
            <div class="avatar" style="background:#818cf8;color:#fff;">A</div>
            <span class="name">Ahmet Yılmaz</span>
            <span class="status" style="color:#22c55e;">📬 Açıldı ✅</span>
          </div>
          <div class="demo-row">
            <div class="avatar" style="background:#f59e0b;color:#fff;">A</div>
            <span class="name">Ayşe Demir</span>
            <span class="status" style="color:#22c55e;">📬 Açıldı ✅</span>
          </div>
          <div class="demo-row">
            <div class="avatar" style="background:#10b981;color:#fff;">M</div>
            <span class="name">Mehmet Kaya</span>
            <span class="status" style="color:#22c55e;">📬 Açıldı ✅</span>
          </div>
          <div style="text-align:center;margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.08);">
            <span style="font-size:12px;color:#818cf8;">👆 Panelden dilediğin kadar öğrenci ekleyebilirsin</span>
          </div>
        </div>
      </div>

      <button type="button" class="btn-primary" id="sendCodeBtn" onclick="sendCode()" style="margin-top:20px;">Doğrulama Kodu Gönder</button>
    </div>

    {{-- Step 2: Verification code --}}
    <div class="step" id="step2">
      <div style="font-size:40px;text-align:center;margin-bottom:8px;">📧</div>
      <h3 style="text-align:center;margin:0 0 4px;">Doğrulama Kodu</h3>
      <p style="text-align:center;color:#6586a7;font-size:14px;margin:0 0 20px;" id="codeSentText">Kodu gönderiliyor...</p>
      <div class="field">
        <input type="text" id="verificationCode" placeholder="6 haneli kod" maxlength="6" style="text-align:center;font-size:24px;letter-spacing:8px;font-weight:700;" />
        <div class="error" id="codeError"></div>
      </div>
      <button type="button" class="btn-primary" onclick="verifyCode()" id="verifyBtn">Doğrula</button>
      <button type="button" onclick="resendCode()" style="background:transparent;border:none;color:#5e8df7;font-size:13px;cursor:pointer;text-align:center;width:100%;margin-top:10px;">Kodu tekrar gönder</button>
    </div>

    {{-- Step 3: Results --}}
    <div class="step" id="step3">
      <div style="text-align:center;padding:20px 0;">
        <div style="font-size:60px;margin-bottom:10px;">✅</div>
        <h3 style="margin:0 0 4px;">Kaydın Alındı!</h3>
        <p style="color:#6586a7;margin:0 0 8px;" id="resultSummary"></p>
        <div id="resultDetails"></div>
      </div>
    </div>
  </div>

  <script>
    const DEMO_STUDENTS = [
      { ad: 'Ahmet', soyad: 'Yılmaz', mail: 'ahmet.yilmaz' },
      { ad: 'Ayşe', soyad: 'Demir', mail: 'ayse.demir' },
      { ad: 'Mehmet', soyad: 'Kaya', mail: 'mehmet.kaya' },
    ];

    function showStep(n) {
      document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
      document.getElementById('step' + n).classList.add('active');
    }

    function showError(id, msg) {
      const el = document.getElementById(id);
      el.textContent = msg;
      el.style.display = 'block';
    }

    function hideError(id) {
      const el = document.getElementById(id);
      el.style.display = 'none';
    }

    async function sendCode() {
      hideError('emailError');

      const name = document.getElementById('ogretmenName').value.trim();
      const email = document.getElementById('ogretmenEmail').value.trim();
      const phone = document.getElementById('ogretmenPhone').value.trim();
      const school = document.getElementById('ogretmenSchool').value.trim();
      if (!name) { showError('emailError', 'Adınızı soyadınızı girin.'); return; }
      if (!email) { showError('emailError', 'E-posta adresinizi girin.'); return; }

      const btn = document.getElementById('sendCodeBtn');
      btn.disabled = true; btn.textContent = 'Gönderiliyor...';

      try {
        const res = await fetch('{{ route("ogretmen.toplu-mail.send-code") }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ name, email, phone, school, students: DEMO_STUDENTS })
        });
        const data = await res.json();
        if (data.success) {
          document.getElementById('codeSentText').textContent = 'Kod ' + email + ' adresine gönderildi.';
          showStep(2);
          document.getElementById('verificationCode').value = '';
          document.getElementById('verificationCode').focus();
        } else {
          showError('emailError', data.message || 'Bir hata oluştu.');
        }
      } catch (e) { showError('emailError', 'Bir hata oluştu.'); }
      finally { btn.disabled = false; btn.textContent = 'Doğrulama Kodu Gönder'; }
    }

    async function verifyCode() {
      hideError('codeError');
      const code = document.getElementById('verificationCode').value.trim();
      if (!code || code.length !== 6) { showError('codeError', '6 haneli kodu girin.'); return; }

      const btn = document.getElementById('verifyBtn');
      btn.disabled = true; btn.textContent = 'Doğrulanıyor...';

      try {
        const res = await fetch('{{ route("ogretmen.toplu-mail.verify-code") }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ code })
        });
        const data = await res.json();
        if (data.success) {
          document.getElementById('resultSummary').textContent = 'Öğretmen kaydın oluşturuldu! Yönetici onayından sonra panele erişebileceksin.';
          let html = '<div style="text-align:center;margin-top:20px;">';
          html += '<a href="/ogretmen" class="btn-primary" style="display:inline-block;text-decoration:none;padding:12px 32px;">Panele Git →</a>';
          html += '</div>';
          document.getElementById('resultDetails').innerHTML = html;
          showStep(3);
        } else {
          showError('codeError', data.message || 'Kod hatalı.');
        }
      } catch (e) { showError('codeError', 'Bir hata oluştu.'); }
      finally { btn.disabled = false; btn.textContent = 'Doğrula'; }
    }

    async function resendCode() {
      const name = document.getElementById('ogretmenName').value.trim();
      const email = document.getElementById('ogretmenEmail').value.trim();
      const phone = document.getElementById('ogretmenPhone').value.trim();
      const school = document.getElementById('ogretmenSchool').value.trim();
      if (!email) { showError('codeError', 'Lütfen formu tekrar doldurun.'); return; }

      try {
        await fetch('{{ route("ogretmen.toplu-mail.send-code") }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ name, email, phone, school, students: DEMO_STUDENTS })
        });
      } catch (e) {}
      document.getElementById('codeSentText').textContent = 'Kod tekrar ' + email + ' adresine gönderildi.';
    }
  </script>
</body>
</html>
