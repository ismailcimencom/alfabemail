<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="/favicon.png" />
  <title>Öğrenci Girişi | ALFABE</title>
  <style>
    :root { --ink: #224261; --primary: #5e8df7; --bg: #f4f8ff; }
    * { box-sizing: border-box; }
    body {
      margin: 0; min-height: 100vh; font-family: 'Nunito','Segoe UI',sans-serif;
      background: radial-gradient(circle at 20% 20%, #e8f4ff, var(--bg) 60%, #e8fff5);
      display: grid; place-items: center; padding: 16px;
    }
    .container {
      width: min(900px, 100%); display: grid;
      grid-template-columns: 1fr 1fr; gap: 24px;
    }
    .card {
      background: #fff; border-radius: 20px;
      box-shadow: 0 16px 40px rgba(34,66,97,.13); padding: 28px;
    }
    h2 { margin: 0 0 6px; color: var(--ink); font-size: 22px; }
    p.sub { margin: 0 0 20px; color: #6586a7; font-size: 14px; }
    label { display: block; font-size: 12px; color: #475569; margin-bottom: 4px; font-weight: 600; }
    input {
      width: 100%; padding: 11px 14px; border: 1.5px solid #cbd5e1;
      border-radius: 10px; margin-bottom: 14px; font-size: 15px;
      outline: none; transition: border .2s;
    }
    input:focus { border-color: var(--primary); }
    .btn {
      width: 100%; border: none; border-radius: 12px; padding: 13px;
      background: var(--primary); color: #fff; font-size: 16px;
      font-weight: 700; cursor: pointer; transition: opacity .2s;
    }
    .btn:hover { opacity: .88; }
    .divider {
      display: flex; align-items: center; gap: 10px;
      color: #94a3b8; font-size: 13px; margin: 18px 0;
    }
    .divider::before,.divider::after {
      content: ''; flex: 1; height: 1px; background: #e2e8f0;
    }
    #qr-reader { width: 100%; border-radius: 12px; overflow: hidden; }
    #qr-status { margin-top: 10px; font-size: 14px; color: #475569; text-align: center; }
    .back { display: inline-block; margin-top: 16px; color: var(--primary); text-decoration: none; font-size: 14px; }
    .beta-btn {
      position: fixed; top: 16px; right: 16px; z-index: 1000;
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #fff; border: none; border-radius: 10px;
      padding: 8px 18px; font-size: 13px; font-weight: 700;
      cursor: pointer; box-shadow: 0 4px 12px rgba(245,158,11,.35);
      transition: opacity .2s, transform .2s; text-decoration: none;
      font-family: inherit;
    }
    .beta-btn:hover { opacity: .9; transform: scale(1.05); }
    @media (max-width: 640px) { .container { grid-template-columns: 1fr; } }

    /* Loading overlay */
    #loadingOverlay {
      display: none;
      position: fixed; top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.5);
      z-index: 999;
      align-items: center; justify-content: center;
      backdrop-filter: blur(4px);
    }
    #loadingOverlay.show { display: flex; }
    .loading-box {
      background: white;
      border-radius: 24px;
      padding: 40px 48px;
      text-align: center;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .spinner {
      width: 48px; height: 48px;
      border: 5px solid #e2e8f0;
      border-top-color: var(--primary);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin: 0 auto 16px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .loading-text { color: var(--ink); font-size: 18px; font-weight: 700; }
    .loading-sub { color: #6586a7; font-size: 14px; margin-top: 4px; }
  </style>
</head>
<body>
  <!-- Loading Overlay -->
  <div id="loadingOverlay">
    <div class="loading-box">
      <div class="spinner"></div>
      <div class="loading-text">Giriş yapılıyor...</div>
      <div class="loading-sub">Lütfen bekleyin</div>
    </div>
  </div>

  <a href="https://beta.alfabe.co" class="beta-btn" target="_blank" rel="noopener">🚀 Beta Sürümü</a>

  <div>
    <a href="{{ route('home') }}" class="back">← Ana Sayfa</a>
    <div class="container" style="margin-top:12px;">

      <!-- Form girişi -->
      <div class="card">
        <h2>🎒 Öğrenci Girişi</h2>
        <p class="sub">E-posta ve şifreni girerek giriş yap.</p>
        <form id="loginForm">
          @csrf
          <label>E-posta Adresi</label>
          <input type="email" id="email" placeholder="ad.soyad@alfabe.co" required />
          <label>Şifre</label>
          <input type="password" id="password" placeholder="••••••••" required />
          <button class="btn" type="submit">Giriş Yap</button>
          <div id="formError" style="color:#b91c1c;font-size:13px;margin-top:8px;display:none;"></div>
        </form>
        <div class="divider">veya</div>
        <p style="text-align:center;color:#6586a7;font-size:13px;">Yaka kartındaki karekodu okutarak da giriş yapabilirsin.</p>
      </div>

      <!-- QR Kod girişi -->
      <div class="card">
        <h2>📷 Karekod ile Giriş</h2>
        <p class="sub">Yaka kartındaki karekodu kameraya göster.</p>
        <div id="qr-reader"></div>
        <div id="qr-status">Kamera yükleniyor...</div>
      </div>

    </div>
  </div>

  <!-- html5-qrcode CDN -->
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <script>
    const loadingOverlay = document.getElementById('loadingOverlay');

    function showLoading(msg) {
      document.querySelector('.loading-text').textContent = msg || 'Giriş yapılıyor...';
      loadingOverlay.classList.add('show');
    }

    function hideLoading() {
      loadingOverlay.classList.remove('show');
    }

    // QR Kod okuyucu
    const html5QrCode = new Html5Qrcode("qr-reader");
    const qrStatus = document.getElementById('qr-status');

    html5QrCode.start(
      { facingMode: "environment" },
      { fps: 10, qrbox: { width: 220, height: 220 } },
      (decodedText) => {
        qrStatus.textContent = 'Karekod okundu, giriş yapılıyor...';
        html5QrCode.stop();
        showLoading('Karekod doğrulanıyor...');
        fetch('{{ route('ogrenci.qr-login') }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ qr_token: decodedText })
        })
        .then(r => r.json())
        .then(data => {
          if (data.redirect) window.location.href = data.redirect;
          else { hideLoading(); qrStatus.textContent = 'Hata: ' + (data.message || 'Geçersiz karekod.'); }
        })
        .catch(() => { hideLoading(); qrStatus.textContent = 'Bağlantı hatası, tekrar dene.'; });
      },
      (err) => { /* scan hatası sessizce geç */ }
    ).catch(() => { qrStatus.textContent = 'Kamera erişimi reddedildi.'; });

    // Form girişi
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const errEl = document.getElementById('formError');
      errEl.style.display = 'none';
      showLoading('Giriş yapılıyor...');
      try {
        const res = await fetch('{{ route('ogrenci.login') }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ email: document.getElementById('email').value, password: document.getElementById('password').value })
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.redirect) {
          window.location.href = data.redirect;
        } else {
          hideLoading();
          errEl.textContent = data.message || 'Hatalı e-posta veya şifre.';
          errEl.style.display = 'block';
        }
      } catch {
        hideLoading();
        errEl.textContent = 'Bağlantı hatası, tekrar dene.';
        errEl.style.display = 'block';
      }
    });
  </script>
</body>
</html>
