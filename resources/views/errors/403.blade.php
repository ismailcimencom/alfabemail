<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Erişim Engellendi | ALFABE</title>
  <link rel="icon" type="image/png" href="/favicon.png">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      min-height: 100vh;
      font-family: 'Nunito', 'Segoe UI', system-ui, sans-serif;
      background: rgba(0,0,0,0.4);
      backdrop-filter: blur(6px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .modal {
      background: #fff;
      border-radius: 24px;
      padding: 48px 40px 36px;
      max-width: 440px;
      width: 100%;
      text-align: center;
      box-shadow: 0 25px 80px rgba(0,0,0,0.35);
      animation: fadeIn 0.35s ease-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.92) translateY(12px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .icon {
      width: 72px;
      height: 72px;
      margin: 0 auto 18px;
      background: linear-gradient(135deg, #fecaca, #f87171);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 34px;
    }
    h1 {
      font-size: 22px;
      color: #1e293b;
      margin-bottom: 8px;
    }
    p {
      font-size: 15px;
      color: #64748b;
      margin-bottom: 28px;
      line-height: 1.5;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 13px 32px;
      border: none;
      border-radius: 999px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      transition: opacity 0.2s, transform 0.15s;
    }
    .btn:hover { opacity: 0.88; transform: scale(1.03); }
    .btn-primary {
      background: linear-gradient(135deg, #5e8df7, #4a7ae8);
      color: #fff;
    }
    .btn-secondary {
      background: #f1f5f9;
      color: #475569;
      margin-top: 10px;
    }
    .footer {
      margin-top: 20px;
      font-size: 12px;
      color: #94a3b8;
    }
  </style>
</head>
<body>
  <div class="modal">
    <div class="icon">🔒</div>
    <h1>Buraya girmeye yetkiniz yok!</h1>
    <p>Bu panele erişim izniniz bulunmamaktadır. Hesabınızla başka bir panele giriş yapmayı deneyebilirsiniz.</p>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-primary">🚪 Çıkış Yap</button>
    </form>

    <div style="margin-top:12px;">
      <a href="/" class="btn btn-secondary">← Ana Sayfa</a>
    </div>

    <div class="footer">ALFABE &mdash; Çocuklar için Güvenli E-posta</div>
  </div>
</body>
</html>
