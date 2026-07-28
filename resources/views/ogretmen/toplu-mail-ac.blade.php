<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Toplu Mail Aç | Alfabe</title>
  <link rel="icon" type="image/png" href="/favicon.png" />
  <link rel="stylesheet" href="/css/portal.css" />
  <style>
    body { background: linear-gradient(135deg, #f8fafc, #f1f5f9); min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; font-family: system-ui, -apple-system, sans-serif; }
    .container { background: #fff; border-radius: 24px; padding: 40px; max-width: 800px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,.08); }
    h1 { margin: 0 0 6px; font-size: 26px; color: #1a202c; text-align: center; }
    .sub { color: #6586a7; text-align: center; margin: 0 0 28px; font-size: 14px; }
    .field { margin-bottom: 18px; }
    label { display: block; font-weight: 600; font-size: 14px; color: #1a202c; margin-bottom: 4px; }
    input, select { width: 100%; padding: 11px 14px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; box-sizing: border-box; color: #1a202c; }
    input:focus { border-color: #5e8df7; outline: none; box-shadow: 0 0 0 3px rgba(94,141,247,.15); }
    .student-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
    .student-row input { flex: 1; }
    .student-row .remove-btn { background: #fee2e2; color: #ef4444; border: none; border-radius: 10px; width: 38px; height: 38px; font-size: 18px; cursor: pointer; flex-shrink: 0; }
    .add-btn { background: #f0f5ff; color: #5e8df7; border: 2px dashed #c7ddff; border-radius: 12px; padding: 10px; font-size: 14px; cursor: pointer; width: 100%; font-weight: 600; }
    .add-btn:hover { background: #e0ecff; }
    .btn-primary { background: #5e8df7; color: #fff; border: none; border-radius: 999px; padding: 14px; font-size: 16px; font-weight: 700; cursor: pointer; width: 100%; }
    .btn-primary:disabled { opacity: .5; cursor: not-allowed; }
    .btn-primary:hover:not(:disabled) { background: #4c72e5; }
    .error { color: #ef4444; font-size: 13px; display: none; margin-top: 4px; }
    .success-msg { display: none; text-align: center; padding: 20px 0; }
    .success-msg .icon { font-size: 60px; margin-bottom: 10px; }
    .results-table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px; }
    .results-table th, .results-table td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
    .results-table .ok { color: #16a34a; }
    .results-table .err { color: #ef4444; }
    .step { display: none; }
    .step.active { display: block; }
    .badge { display: inline-block; background: #e0ecff; color: #5e8df7; border-radius: 999px; padding: 2px 10px; font-size: 12px; font-weight: 600; }
    .back-link { display: block; text-align: center; margin-top: 16px; color: #6586a7; font-size: 14px; text-decoration: none; }
    .back-link:hover { color: #5e8df7; }
    .mail-hint { font-size: 13px; color: #94a3b8; margin-top: 2px; }
  </style>
</head>
<body>
  <div class="container" id="app">
    <h1>📧 Toplu Öğrenci Mail Aç</h1>
    <p class="sub">Öğrencileriniz için toplu e-posta hesabı oluşturun</p>

    {{-- Step 1: Teacher email + student list --}}
    <div class="step active" id="step1">
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
        <input type="text" id="ogretmenSchool" placeholder="Sınıfınızın adı" />
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <label style="margin:0;">Öğrenci Listesi</label>
        <span class="badge" id="studentCount">0 öğrenci</span>
      </div>
      <div id="studentList"></div>
      <button type="button" class="add-btn" onclick="addStudent()">+ Öğrenci Ekle</button>
      <div class="error" id="studentsError" style="margin-top:8px;"></div>

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
      <div class="success-msg" style="display:block;">
        <div class="icon">✅</div>
        <h3 style="margin:0 0 4px;">Mailler Oluşturuldu!</h3>
        <p style="color:#6586a7;margin:0 0 8px;" id="resultSummary"></p>
        <div id="resultDetails"></div>
      </div>
    </div>
  </div>

  <script>
    let studentCount = 0;

    function addStudent(data) {
      const list = document.getElementById('studentList');
      const idx = studentCount++;
      const div = document.createElement('div');
      div.className = 'student-row';
      div.id = 'student_' + idx;
      div.innerHTML = `
        <input type="text" placeholder="Ad" class="s-ad" value="${data?.ad || ''}" />
        <input type="text" placeholder="Soyad" class="s-soyad" value="${data?.soyad || ''}" />
        <div style="position:relative;flex:1;">
          <input type="text" placeholder="mail" class="s-mail" value="${data?.mail || ''}" style="padding-right:90px;" />
          <span style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;pointer-events:none;">@alfabe.co</span>
        </div>
        <button type="button" class="remove-btn" onclick="removeStudent(${idx})">✕</button>
      `;
      list.appendChild(div);
      updateCount();
    }

    function removeStudent(idx) {
      const el = document.getElementById('student_' + idx);
      if (el) el.remove();
      updateCount();
    }

    function updateCount() {
      const rows = document.querySelectorAll('.student-row');
      document.getElementById('studentCount').textContent = rows.length + ' öğrenci';
    }

    function getStudents() {
      const rows = document.querySelectorAll('.student-row');
      return Array.from(rows).map(row => ({
        ad: row.querySelector('.s-ad').value.trim(),
        soyad: row.querySelector('.s-soyad').value.trim(),
        mail: row.querySelector('.s-mail').value.trim(),
      })).filter(s => s.ad && s.soyad && s.mail);
    }

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
      hideError('studentsError');

      const name = document.getElementById('ogretmenName').value.trim();
      const email = document.getElementById('ogretmenEmail').value.trim();
      const phone = document.getElementById('ogretmenPhone').value.trim();
      const school = document.getElementById('ogretmenSchool').value.trim();
      if (!name) { showError('emailError', 'Adınızı soyadınızı girin.'); return; }
      if (!email) { showError('emailError', 'E-posta adresinizi girin.'); return; }

      const students = getStudents();
      if (students.length === 0) { showError('studentsError', 'En az bir öğrenci ekleyin.'); return; }

      const btn = document.getElementById('sendCodeBtn');
      btn.disabled = true; btn.textContent = 'Gönderiliyor...';

      try {
        const res = await fetch('{{ route("ogretmen.toplu-mail.send-code") }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ name, email, phone, school, students })
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
          showResults(data.results);
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
      const students = getStudents();
      if (!email || students.length === 0) { showError('codeError', 'Lütfen formu tekrar doldurun.'); return; }

      try {
        await fetch('{{ route("ogretmen.toplu-mail.send-code") }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ name, email, phone, school, students })
        });
      } catch (e) {}
      document.getElementById('codeSentText').textContent = 'Kod tekrar ' + email + ' adresine gönderildi.';
    }

    function showResults(results) {
      const successCount = results.success?.length || 0;
      const errorCount = results.errors?.length || 0;
      const summary = document.getElementById('resultSummary');
      summary.textContent = successCount + ' öğrenci maili başarıyla açıldı' + (errorCount > 0 ? ', ' + errorCount + ' öğrencide hata oluştu.' : '.');

      let html = '';
      html += '<p style="font-weight:600;margin:16px 0 4px;">📁 Sınıf: <strong>' + results.sinif.ad + '</strong> (' + results.sinif.okul + ')</p>';

      if (results.success.length > 0) {
        html += '<table class="results-table"><thead><tr><th>Öğrenci</th><th>Mail</th><th>Şifre</th></tr></thead><tbody>';
        results.success.forEach(s => {
          html += '<tr class="ok"><td>' + s.ad + '</td><td>' + s.mail + '</td><td><code>' + s.sifre + '</code></td></tr>';
        });
        html += '</tbody></table>';
      }

      if (results.errors.length > 0) {
        html += '<p style="color:#ef4444;font-weight:600;margin:16px 0 4px;">⚠️ Hatalı Kayıtlar</p>';
        html += '<table class="results-table"><thead><tr><th>Öğrenci</th><th>Mail</th><th>Sebep</th></tr></thead><tbody>';
        results.errors.forEach(e => {
          html += '<tr class="err"><td>' + e.ad + '</td><td>' + e.mail + '</td><td>' + e.sebep + '</td></tr>';
        });
        html += '</tbody></table>';
      }

      html += '<div style="text-align:center;margin-top:20px;">';
      html += '<a href="' + '{{ route("ogretmen.toplu-mail.sifre-belirle") }}' + '" class="btn-primary" style="display:inline-block;text-decoration:none;padding:12px 32px;">Devam Et →</a>';
      html += '</div>';

      document.getElementById('resultDetails').innerHTML = html;
    }

    // Add 3 initial rows
    for (let i = 0; i < 3; i++) addStudent();
  </script>
</body>
</html>
