<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Yönlendiriliyor... | ALFABE</title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0; min-height: 100vh;
      font-family: 'Nunito','Segoe UI',sans-serif;
      background: radial-gradient(circle at 20% 20%, #e8f4ff, #f4f8ff 60%, #e8fff5);
      display: flex; align-items: center; justify-content: center;
      flex-direction: column; gap: 20px; padding: 20px;
    }
    .card {
      background: #fff; border-radius: 20px;
      box-shadow: 0 16px 40px rgba(34,66,97,.13);
      padding: 40px 48px; text-align: center; max-width: 400px;
    }
    .spinner {
      width: 48px; height: 48px;
      border: 5px solid #e2e8f0;
      border-top-color: #5e8df7;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin: 0 auto 16px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    h2 { color: #224261; margin: 0 0 6px; font-size: 20px; }
    p { color: #6586a7; font-size: 14px; margin: 0; }
    .error-box {
      background: #fef2f2; border: 1px solid #fecaca;
      border-radius: 12px; padding: 16px; margin-top: 16px;
      color: #b91c1c; font-size: 13px; display: none;
    }
    .error-box.show { display: block; }
  </style>
</head>
<body>
  <div class="card">
    <div class="spinner" id="spinner"></div>
    <h2>Mail paneline yönlendiriliyor...</h2>
    <p>Lütfen bekleyin, birkaç saniye içinde mail hesabınıza aktarılacaksınız.</p>
    <div class="error-box" id="errorBox">
      Otomatik yönlendirme başarısız oldu.
      <br><a href="https://mail.alfabe.co/" style="color:#5e8df7;font-weight:600;">Mail paneline gitmek için tıklayın →</a>
    </div>
  </div>

  <form id="autoLoginForm" method="POST" action="https://mail.alfabe.co/">
    <input type="hidden" name="login_user" value="{{ $email }}" />
    <input type="hidden" name="pass_user" value="{{ $password }}" />
  </form>

  <script>
    const form = document.getElementById('autoLoginForm');
    const spinner = document.getElementById('spinner');
    const errorBox = document.getElementById('errorBox');

    let submitted = false;

    function submitForm() {
      if (submitted) return;
      submitted = true;
      form.submit();

      // Eğer 5 saniye içinde yönlenmezse hata göster
      setTimeout(function () {
        spinner.style.display = 'none';
        errorBox.classList.add('show');
      }, 5000);
    }

    // Sayfa yüklenir yüklenmez formu gönder
    window.addEventListener('load', submitForm);

    // 1 saniye içinde submit olmazsa zorla dene (failover)
    setTimeout(function () {
      if (!submitted) submitForm();
    }, 1000);
  </script>
</body>
</html>
