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
      display: flex; align-items: center; gap: 8px;
      padding: 10px 12px; border-radius: 10px;
      background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
      margin-bottom: 6px; opacity: 0.4; transition: all 0.5s ease;
    }
    .demo-row .avatar { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
    .demo-row .info { flex: 1; min-width: 0; }
    .demo-row .info .name { font-size: 13px; font-weight: 600; color: #c7d2fe; }
    .demo-row .info .mail { font-size: 11px; color: #6366f1; margin-top: 1px; }
    .demo-row .status { font-size: 11px; font-weight: 600; white-space: nowrap; display: flex; align-items: center; gap: 3px; padding: 3px 8px; border-radius: 999px; background: rgba(34,197,94,0.08); color: #22c55e; opacity: 0; transition: opacity 0.4s ease; }

    .demo-row.highlight { opacity: 1; border-color: rgba(129,140,248,0.3); background: rgba(129,140,248,0.08); }
    .demo-row.highlight .status { opacity: 0; }
    .demo-row.done { opacity: 1; border-color: rgba(34,197,94,0.3); background: rgba(34,197,94,0.06); }
    .demo-row.done .status { opacity: 1; }
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
          <div class="demo-row" id="demo1">
            <div class="avatar" style="background:#818cf8;color:#fff;">A</div>
            <div class="info">
              <div class="name">Ahmet Yılmaz</div>
              <div class="mail">ahmet.yilmaz@alfabe.co</div>
            </div>
            <div class="status">✅ Açıldı</div>
          </div>
          <div class="demo-row" id="demo2">
            <div class="avatar" style="background:#f59e0b;color:#fff;">A</div>
            <div class="info">
              <div class="name">Ayşe Demir</div>
              <div class="mail">ayse.demir@alfabe.co</div>
            </div>
            <div class="status">✅ Açıldı</div>
          </div>
          <div class="demo-row" id="demo3">
            <div class="avatar" style="background:#10b981;color:#fff;">M</div>
            <div class="info">
              <div class="name">Mehmet Kaya</div>
              <div class="mail">mehmet.kaya@alfabe.co</div>
            </div>
            <div class="status">✅ Açıldı</div>
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

    // Demo animation cycle
    function runDemo() {
      const rows = [
        document.getElementById('demo1'),
        document.getElementById('demo2'),
        document.getElementById('demo3'),
      ];
      rows.forEach(r => { r.classList.remove('highlight', 'done'); });
      let i = 0;
      function next() {
        if (i > 0) rows[i - 1].classList.remove('highlight');
        if (i < rows.length) {
          rows[i].classList.add('highlight');
          setTimeout(() => {
            rows[i].classList.remove('highlight');
            rows[i].classList.add('done');
            i++;
            setTimeout(next, 400);
          }, 1200);
        } else {
          setTimeout(() => { rows.forEach(r => r.classList.remove('done')); runDemo(); }, 3000);
        }
      }
      next();
    }
    runDemo();

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
