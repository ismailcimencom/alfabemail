<div id="kayitModal" style="display:none;position:fixed;z-index:9999;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:#fff;border-radius:20px;padding:30px 30px 20px;width:100%;max-width:440px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;">
    <button type="button" onclick="document.getElementById('kayitModal').style.display='none'" style="position:absolute;top:12px;right:16px;border:none;background:none;font-size:28px;cursor:pointer;color:#888;">&times;</button>

    {{-- Step 1: Email --}}
    <div id="kayitStep1">
      <div style="font-size:40px;text-align:center;margin-bottom:4px;">🐧</div>
      <h3 style="text-align:center;margin:0 0 4px;font-size:20px;color:#1a202c;">Hesap Oluştur</h3>
      <p style="text-align:center;color:#6586a7;margin:0 0 20px;font-size:14px;">Başlamak için e-posta adresini gir!</p>
      <div style="display:grid;gap:12px;">
        <label style="font-size:13px;font-weight:600;color:#1a202c;">Öğretmen Ad Soyad :</label>
        <input type="text" id="kayitName" placeholder="Adın Soyadın" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <input type="email" id="kayitEmail" placeholder="E-posta adresin" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <label style="font-size:13px;font-weight:600;color:#1a202c;">Telefon :</label>
        <input type="tel" id="kayitPhone" placeholder="Telefon numaran" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <label style="font-size:13px;font-weight:600;color:#1a202c;">Sınıf Adı :</label>
        <input type="text" id="kayitSchool" placeholder="Sınıf adı" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <div id="kayitEmailError" style="color:#ef4444;font-size:13px;display:none;"></div>
        <button type="button" onclick="kayitSendCode()" id="kayitBtn1" style="background:#5e8df7;color:#fff;border:none;border-radius:999px;padding:14px;font-size:16px;font-weight:700;cursor:pointer;">Doğrulama Kodu Gönder</button>
      </div>
    </div>

    {{-- Step 2: Code --}}
    <div id="kayitStep2" style="display:none;">
      <div style="font-size:40px;text-align:center;margin-bottom:4px;">📧</div>
      <h3 style="text-align:center;margin:0 0 4px;font-size:20px;color:#1a202c;">Kodu Gir</h3>
      <p style="text-align:center;color:#6586a7;margin:0 0 4px;font-size:14px;">E-postana gelen 6 haneli kodu gir</p>
      <p style="text-align:center;color:#eab308;margin:0 0 16px;font-size:13px;font-weight:600;">📧 Kod gelmezse spam/gereksiz klasörünü kontrol et</p>
      <div style="display:grid;gap:12px;">
        <input type="text" id="kayitCode" placeholder="6 haneli kod" maxlength="6" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:24px;width:100%;text-align:center;letter-spacing:8px;font-weight:700;color:#1a202c;">
        <div id="kayitCodeError" style="color:#ef4444;font-size:13px;display:none;"></div>
        <button type="button" onclick="kayitVerifyCode()" id="kayitBtn2" style="background:#5e8df7;color:#fff;border:none;border-radius:999px;padding:14px;font-size:16px;font-weight:700;cursor:pointer;">Doğrula</button>
        <button type="button" onclick="kayitSendCode()" style="background:transparent;border:none;color:#5e8df7;font-size:13px;cursor:pointer;text-align:center;">Kodu tekrar gönder</button>
      </div>
    </div>

    {{-- Step 3: Role Selection --}}
    <div id="kayitStep3" style="display:none;">
      <div style="font-size:40px;text-align:center;margin-bottom:4px;">🤔</div>
      <h3 style="text-align:center;margin:0 0 4px;font-size:20px;color:#1a202c;">Hesap Türünü Seç</h3>
      <p style="text-align:center;color:#6586a7;margin:0 0 20px;font-size:14px;">Sana uygun olan rolü seç</p>
      <div style="display:grid;gap:10px;">
        <div class="kayit-role-card" data-role="ogretmen" onclick="kayitSelectRole('ogretmen')" style="display:flex;align-items:center;gap:14px;padding:14px;border:2px solid #e2e8f0;border-radius:14px;cursor:pointer;transition:.2s;">
          <span style="font-size:32px;">🧑‍🏫</span>
          <div><div style="font-weight:700;color:#1a202c;">Öğretmen</div><div style="font-size:13px;color:#6586a7;">Toplu öğrenci mail kaydı, sınıf süreç yönetimi</div></div>
        </div>
        <div class="kayit-role-card" data-role="veli" onclick="kayitSelectRole('veli')" style="display:flex;align-items:center;gap:14px;padding:14px;border:2px solid #e2e8f0;border-radius:14px;cursor:pointer;transition:.2s;">
          <span style="font-size:32px;">👨‍👩‍👧</span>
          <div><div style="font-weight:700;color:#1a202c;">Veli</div><div style="font-size:13px;color:#6586a7;">Öğrenci mail gelişim takibi ve etkinlik özet raporları</div></div>
        </div>
        <div id="kayitRoleError" style="color:#ef4444;font-size:13px;display:none;"></div>
      </div>
    </div>

    {{-- Step 4: Password + Role-specific --}}
    <div id="kayitStep4" style="display:none;">
      <div style="font-size:40px;text-align:center;margin-bottom:4px;">🔑</div>
      <h3 style="text-align:center;margin:0 0 4px;font-size:20px;color:#1a202c;">Şifre Belirle</h3>
      <p style="text-align:center;color:#6586a7;margin:0 0 20px;font-size:14px;" id="kayitStep4Desc">Hesabın için bir şifre belirle</p>
      <div style="display:grid;gap:12px;">
        <div id="kayitRoleFields"></div>
        <input type="password" id="kayitPassword" placeholder="Şifre (en az 6 karakter)" minlength="6" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <input type="password" id="kayitPasswordConfirm" placeholder="Şifre Tekrar" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <div id="kayitCompleteError" style="color:#ef4444;font-size:13px;display:none;"></div>
        <button type="button" onclick="kayitComplete()" id="kayitBtn4" style="background:#5e8df7;color:#fff;border:none;border-radius:999px;padding:14px;font-size:16px;font-weight:700;cursor:pointer;">Kaydol</button>
      </div>
    </div>

    {{-- Success --}}
    <div id="kayitSuccess" style="display:none;text-align:center;padding:20px 0;">
      <div style="font-size:60px;margin-bottom:10px;">🎉</div>
      <p style="font-size:18px;font-weight:700;margin:0;color:#1a202c;" id="kayitSuccessTitle">Kaydın alındı!</p>
      <p style="color:#6586a7;margin:6px 0 0;" id="kayitSuccessText">Yönetici onayından sonra giriş yapabileceksin.</p>
    </div>
  </div>
</div>

<script>
let kayitEmail = '';
let kayitRole = '';

function kayitShowError(id, msg) {
  const el = document.getElementById(id);
  el.textContent = msg;
  el.style.display = 'block';
}

function kayitClearErrors() {
  document.querySelectorAll('[id$=Error]').forEach(e => e.style.display = 'none');
}

function kayitSelectRole(role) {
  kayitRole = role;
  document.querySelectorAll('.kayit-role-card').forEach(c => {
    c.style.borderColor = '#e2e8f0';
    c.style.background = '#fff';
  });
  const card = document.querySelector(`.kayit-role-card[data-role="${role}"]`);
  card.style.borderColor = '#5e8df7';
  card.style.background = '#f0f5ff';
}

async function kayitSendCode() {
  kayitClearErrors();
  const name = document.getElementById('kayitName').value.trim();
  const email = document.getElementById('kayitEmail').value.trim();
  const phone = document.getElementById('kayitPhone').value.trim();
  const school = document.getElementById('kayitSchool').value.trim();
  if (!name) { kayitShowError('kayitEmailError', 'Adını soyadını gir.'); return; }
  if (!email) { kayitShowError('kayitEmailError', 'E-posta adresini gir.'); return; }
  if (!phone) { kayitShowError('kayitEmailError', 'Telefon numaranı gir.'); return; }
  if (!school) { kayitShowError('kayitEmailError', 'Sınıf adını gir.'); return; }
  const btn = document.getElementById('kayitBtn1');
  btn.disabled = true; btn.textContent = 'Gönderiliyor...';
  try {
    const res = await fetch('{{ route("kayit.send-code") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ name, email, phone, school })
    });
    const data = await res.json();
    if (data.success) {
      kayitEmail = email;
      document.getElementById('kayitStep1').style.display = 'none';
      document.getElementById('kayitStep2').style.display = 'block';
      document.getElementById('kayitCode').value = '';
      document.getElementById('kayitCode').focus();
    } else {
      kayitShowError('kayitEmailError', data.message || 'Bir hata oluştu.');
    }
  } catch(e) { kayitShowError('kayitEmailError', 'Bir hata oluştu.'); }
  finally { btn.disabled = false; btn.textContent = 'Doğrulama Kodu Gönder'; }
}

async function kayitVerifyCode() {
  kayitClearErrors();
  const code = document.getElementById('kayitCode').value.trim();
  if (!code || code.length !== 6) { kayitShowError('kayitCodeError', '6 haneli kodu gir.'); return; }
  const btn = document.getElementById('kayitBtn2');
  btn.disabled = true; btn.textContent = 'Doğrulanıyor...';
  try {
    const res = await fetch('{{ route("kayit.verify-code") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ email: kayitEmail, code })
    });
    const data = await res.json();
    if (data.success) {
      document.getElementById('kayitStep2').style.display = 'none';
      document.getElementById('kayitStep3').style.display = 'block';
    } else {
      kayitShowError('kayitCodeError', data.message || 'Kod hatalı.');
    }
  } catch(e) { kayitShowError('kayitCodeError', 'Bir hata oluştu.'); }
  finally { btn.disabled = false; btn.textContent = 'Doğrula'; }
}

function kayitGoToStep4() {
  if (!kayitRole) { kayitShowError('kayitRoleError', 'Lütfen bir rol seç.'); return; }
  kayitClearErrors();
  document.getElementById('kayitStep3').style.display = 'none';
  document.getElementById('kayitStep4').style.display = 'block';
}

setInterval(() => {
  if (document.getElementById('kayitStep3').style.display !== 'none') {
    const cards = document.querySelectorAll('.kayit-role-card');
    cards.forEach(c => {
      c.removeEventListener('click', c._listener);
      c._listener = () => {
        const role = c.dataset.role;
        kayitSelectRole(role);
        setTimeout(kayitGoToStep4, 300);
      };
      c.addEventListener('click', c._listener);
    });
  }
}, 500);

async function kayitComplete() {
  kayitClearErrors();
  const name = document.getElementById('kayitName').value.trim();
  const password = document.getElementById('kayitPassword').value;
  const passwordConfirm = document.getElementById('kayitPasswordConfirm').value;
  if (!kayitRole) { kayitShowError('kayitCompleteError', 'Lütfen önce rol seç.'); return; }
  if (!name) { kayitShowError('kayitCompleteError', 'Adını soyadını gir.'); return; }

  if (password.length < 6) { kayitShowError('kayitCompleteError', 'Şifre en az 6 karakter olmalı.'); return; }
  if (password !== passwordConfirm) { kayitShowError('kayitCompleteError', 'Şifreler eşleşmiyor.'); return; }
  const btn = document.getElementById('kayitBtn4');
  btn.disabled = true; btn.textContent = 'Kaydediliyor...';
  try {
    const res = await fetch('{{ route("kayit.complete") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ email: kayitEmail, name, password, password_confirmation: passwordConfirm, role: kayitRole })
    });
    const data = await res.json();
    if (data.success) {
      document.getElementById('kayitStep4').style.display = 'none';
      document.getElementById('kayitSuccess').style.display = 'block';
      if (data.redirect) {
        document.getElementById('kayitSuccessTitle').textContent = data.title || 'Kaydın alındı!';
        document.getElementById('kayitSuccessText').textContent = data.message || 'Yönetici onayından sonra giriş yapabileceksin.';
        setTimeout(() => { location.href = data.redirect; }, 2000);
      } else {
        setTimeout(() => { location.href = '{{ request()->url() }}'; }, 3000);
      }
    } else {
      kayitShowError('kayitCompleteError', data.message || 'Bir hata oluştu.');
    }
  } catch(e) { kayitShowError('kayitCompleteError', 'Bir hata oluştu.'); }
  finally { btn.disabled = false; btn.textContent = 'Kaydol'; }
}
</script>
