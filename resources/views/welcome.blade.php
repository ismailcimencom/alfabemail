<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ALFABE Portal | Mail </title>
  <link rel="icon" type="image/png" href="/favicon.png" />
  <link rel="alternate icon" href="/favicon.ico" />
  <link rel="stylesheet" href="/css/portal.css" />
</head>
<body>

  <!-- Penguen Animasyonu -->
  <section class="scene" aria-label="Kayıp penguen animasyonu">
    <div class="ice-ground"></div>
    <div class="door" title="Portal Giriş Kapısı">
      <div class="door-inside"><span class="door-test">TEST</span></div>
      <div class="door-glow"></div>
      <div class="door-brand">alfabe.co</div>
      <div class="door-frame"></div>
      <div class="door-leaf" id="doorLeaf"><div class="door-knob"></div></div>
    </div>
    <div class="penguin" id="penguin">
      <div class="penguin-inner">

        <!-- SVG Penguin Character — matches alfabelogo.png -->
        <svg class="penguin-svg" viewBox="0 0 140 180" xmlns="http://www.w3.org/2000/svg">
          <!-- Shadow -->
          <ellipse class="p-shadow" cx="70" cy="174" rx="40" ry="7" fill="rgba(27,43,68,.18)"/>

          <!-- Left foot (symmetrical yellow oval) -->
          <ellipse class="p-foot-l" cx="48" cy="166" rx="17" ry="9" fill="#f5c518"/>

          <!-- Right foot (symmetrical yellow oval) -->
          <ellipse class="p-foot-r" cx="92" cy="166" rx="17" ry="9" fill="#f5c518"/>

          <!-- Body (black egg shape) -->
          <ellipse cx="70" cy="112" rx="42" ry="52" fill="#1a1a2e"/>

          <!-- Belly (white front) -->
          <ellipse cx="70" cy="120" rx="28" ry="36" fill="#f0f0f5"/>

          <!-- Left wing (small rounded flipper) -->
          <g class="p-wing-l-g">
            <ellipse cx="24" cy="108" rx="10" ry="28" fill="#1a1a2e" transform="rotate(8,24,108)"/>
          </g>

          <!-- Right wing (small rounded flipper) -->
          <g class="p-wing-r-g">
            <ellipse cx="116" cy="108" rx="10" ry="28" fill="#1a1a2e" transform="rotate(-8,116,108)"/>
          </g>

          <!-- Head (large round black circle) -->
          <circle cx="70" cy="48" r="36" fill="#1a1a2e"/>

          <!-- White face mask -->
          <ellipse cx="70" cy="52" rx="26" ry="22" fill="#f0f0f5"/>

          <!-- Left eye (large expressive oval) -->
          <ellipse cx="57" cy="44" rx="11" ry="13" fill="#ffffff"/>
          <!-- Left pupil (prominent black) -->
          <circle cx="59" cy="47" r="7" fill="#111111"/>
          <!-- Left eye shine -->
          <circle cx="56" cy="43" r="2.5" fill="#ffffff" opacity="0.8"/>

          <!-- Right eye (large expressive oval) -->
          <ellipse cx="83" cy="44" rx="11" ry="13" fill="#ffffff"/>
          <!-- Right pupil (prominent black) -->
          <circle cx="85" cy="47" r="7" fill="#111111"/>
          <!-- Right eye shine -->
          <circle cx="82" cy="43" r="2.5" fill="#ffffff" opacity="0.8"/>

          <!-- Beak (small triangular pointed yellow) -->
          <polygon points="64,56 76,56 70,67" fill="#f5c518" stroke="#d4a800" stroke-width="1" stroke-linejoin="round"/>
        </svg>

        <!-- Dynamic floating email swarm (5 envelopes) -->
        <div class="mail-swarm">
          <div class="ew w1"><div class="env e1"></div></div>
          <div class="ew w2"><div class="env e2"></div></div>
          <div class="ew w3"><div class="env e3"></div></div>
          <div class="ew w4"><div class="env e4"></div></div>
          <div class="ew w5"><div class="env e5"></div></div>
        </div>

      </div>
    </div>

    <!-- Alfabe Logo — fades in on the left after penguin enters door -->
    <img class="alfabe-logo" src="{{ asset('images/alfabelogo.png') }}" alt="Alfabe Mail Logo" />

    <!-- CTA text — fades in after penguin disappears -->
    <div class="cta-trail" id="ctaTrail">
      <span>Hemen bugün başlayın! Alfabe Mail'i ücretsiz deneyebilirsiniz.</span>
    </div>
    @php
      try {
        $mc = app(\App\Services\MailcowService::class);
        $mailboxes = $mc->listMailboxes();
        $totalMailboxes = is_array($mailboxes) ? count($mailboxes) : 0;
      } catch (\Throwable $e) {
        $totalMailboxes = \App\Models\Ogrenci::whereNotNull('mailbox_local_part')->count();
      }
    @endphp
    <div id="mailCounter" class="mail-counter-banner" style="display:none;">
      <span>📬 <strong id="mailboxCountDisplay">0</strong> posta kutusu açıldı!</span>
      <button onclick="dismissMailCounter()" title="Kapat">&times;</button>
    </div>
    <script>
      (function() {
        var hidden = localStorage.getItem('mail_counter_hidden');
        if (!hidden) {
          document.getElementById('mailCounter').style.display = '';
          var target = {{ $totalMailboxes }};
          var el = document.getElementById('mailboxCountDisplay');
          var start = 0, duration = 1500, step = Math.ceil(target / 30);
          if (step < 1) step = 1;
          var timer = setInterval(function() {
            start += step;
            if (start >= target) { start = target; clearInterval(timer); }
            el.textContent = start.toLocaleString('tr-TR');
          }, duration / 30);
        }
        function dismissMailCounter() {
          document.getElementById('mailCounter').style.display = 'none';
          localStorage.setItem('mail_counter_hidden', '1');
        }
        window.dismissMailCounter = dismissMailCounter;
      })();
    </script>

  </section>

  <!-- Giriş Kartları -->
  <main class="entry">
    <h1>🐧 Kayıp Penguen ALFABE Portalı Buldu!</h1>
    <p class="sub">Alfabe — Çocukların güvenle kullanabilecekleri eMail okul yönetim sistemidir.</p>

    <div class="grid">
      <div class="left-col">
        <article class="card manager" style="opacity:0.6;pointer-events:none;">
          <h2>🔐 EBA ile Giriş Yap</h2>
          <p>EBA hesabınla hızlı ve güvenli giriş. (Çok yakında)</p>
          <button style="cursor:not-allowed;">EBA ile Giriş Yap</button>
        </article>
        <article class="card teacher">
          <h2>🧑🏫 Öğretmen</h2>
          <p>Toplu öğrenci mail kaydı, sınıf süreç yönetimi.</p>
          <button onclick="go('/ogretmen')">Öğretmen Paneli</button>
        </article>
      </div>

      <article class="card student">
        <h2>🎒 Öğrenci Girişi</h2>
        <p>Karekod ile hızlı ve eğlenceli erişim.</p>
        <button onclick="go('{{ route('ogrenci.giris') }}')">Öğrenci Girişi Yap</button>
      </article>

      <div class="right-col">
        <article class="card parent">
          <h2>👨👩👧 Veli</h2>
          <p>Öğrenci mail gelişim takibi ve etkinlik özet raporları.</p>
          <button onclick="go('/veli')">Veli Girişi</button>
        </article>
        <article class="card portal-info">
          <h2>🤝 Admin</h2>
          <p>Sistemi yöneten öğrenci merkezli admin paneli.</p>
          <button onclick="go('/admin')">Admin Paneli</button>
        </article>
      </div>
    </div>
  </main>

  <footer class="footer">
    <div class="footer-wrap">
      <section>
        <h3>Alfabe.Co Mail Eğitim Paneli</h3>
        <p>Çocuklar için güvenli e-Mail dünyasına giriş yapın. Öğrencilerinizin dijital adımlarını güvenle atmasını sağlayın. Okul yöneticileri, öğretmenler ve velilerin el birliğiyle takip edebildiği bu güvenli iletişim platformuna, Alfabe dünyasına hoş geldiniz.</p>
      </section>
      <section>
        <h3>Hızlı Linkler</h3>
        <ul>
          <li><a href="{{ url('/') }}">Ana Sayfa</a></li>
          <li><a href="{{ route('ogrenci.giris') }}">Öğrenci Girişi</a></li>
          <li><a href="/ogretmen">Öğretmen Paneli</a></li>
          <li><a href="/veli">Veli Paneli</a></li>
          <li><a href="/admin">Admin Paneli</a></li>
          <li><a href="https://yolharitasi.alfabe.co/">Fikir Paneli</a></li>
           <li><a href="https://mail.alfabe.co/">Mail Paneli</a></li>
        </ul>
      </section>
      <section>
        <h3>Adres</h3>
        <p>Projemizi Konya Kapsül Teknoloji Platformu Destek Ofisi'nde hayata geçiriyoruz.</p>
        <p>E-posta: iletisim@alfabe.co</p>
      </section>
      <section>
        <h3>Yasal</h3>
        <ul>
          <li><a href="{{ route('kvkk') }}">KVKK</a></li>
          <li><a href="{{ route('gizlilik') }}">Gizlilik Politikası</a></li>
          <li><a href="{{ route('kullanim-sartlari') }}">Kullanım Şartları</a></li>
          <li><a href="{{ route('cerez-politikasi') }}">Çerez Politikası</a></li>
        </ul>
                <p><a href="#" onclick="event.preventDefault();document.getElementById('hataModal').style.display='flex'" style="color:#7fa7ff;text-decoration:none;cursor:pointer;">⚠️ Hata Bildir</a></p>

      </section>
    </div>
  </footer>

  <!-- Sponsor Marquee -->
  @php
    $sponsors = \App\Models\Sponsor::where('aktif', true)->orderBy('sira')->get();
  @endphp
  <section class="sponsors" aria-label="Sponsorlar">
    <div class="marquee-wrap">
      <div class="marquee-track">
        @php $sponsorItems = $sponsors->toArray(); @endphp
        @for ($i = 0; $i < 6; $i++)
          @foreach($sponsorItems as $sponsor)
            <a href="{{ $sponsor['website'] ?? '#' }}" target="_blank" class="sponsor-circle" title="{{ $sponsor['ad'] }}" style="padding:3px;">
              <img src="{{ asset('storage/' . $sponsor['logo']) }}" alt="{{ $sponsor['ad'] }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            </a>
          @endforeach
          <div class="sponsor-circle"><span class="sc-emoji">❤️</span><span class="sc-label">Sponsor ol</span></div>
          <div class="sponsor-circle"><span class="sc-emoji">❤️</span><span class="sc-label">Sponsor ol</span></div>
          <div class="sponsor-circle"><span class="sc-emoji">❤️</span><span class="sc-label">Sponsor ol</span></div>
          <div class="sponsor-circle"><span class="sc-emoji">❤️</span><span class="sc-label">Sponsor ol</span></div>
        @endfor
      </div>
    </div>
  </section>

  <!-- Güvenlik Mesajı + Kahve -->
  <div style="text-align:center;margin:30px auto;max-width:700px;">
    <p style="font-style:italic;color:#94a3b8;font-size:14px;line-height:1.6;">"Mail sistemimizin güvenliği için başvuruyu sadece okul yöneticisi, öğretmen veya veli yapabilir. Unutmayın! Bu sadece bir mail test sistemidir. Çocuklarımızın güvenliği en önemli önceliğimizdir. Bu konuda hassasiyetiniz için teşekkür eder ve sadece teste tabi olacaklar başvurursa seviniriz. Bizler iletişimin özgür, reklamsız ve güvenli olması için çalışıyoruz. — Kapsül Serix Yazılım Ekibi"</p>
          <script type="text/javascript" src="https://cdnjs.buymeacoffee.com/1.0.0/button.prod.min.js" data-name="bmc-button" data-slug="proacademy" data-color="#FFDD00" data-emoji="☕" data-font="Cookie" data-text="Bize kahve ısmarlayın." data-outline-color="#000000" data-font-color="#000000" data-coffee-color="#ffffff" ></script>

  </div>

  <div class="copyright-bar">Copyright &copy; {{ date('Y') }} Alfabe Mail. Tüm hakları saklıdır.</div>

  <!-- Hata Bildir Modal -->
  <div id="hataModal" style="display:none;position:fixed;z-index:9999;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)document.getElementById('hataModal').style.display='none'">
    <div style="background:#fff;border-radius:20px;padding:30px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;">
      <button type="button" onclick="document.getElementById('hataModal').style.display='none'" style="position:absolute;top:12px;right:16px;border:none;background:none;font-size:28px;cursor:pointer;color:#888;">&times;</button>
      <h3 style="margin:0 0 6px;font-size:22px;">🐧 Hata Bildir</h3>
      <p style="margin:0 0 18px;color:#6586a7;font-size:14px;">Karşılaştığın sorunu bize anlat, ekran görüntüsü eklemeyi unutma!</p>
      <form id="hataForm" enctype="multipart/form-data">
        @csrf
        <div style="display:grid;gap:14px;">
          <input type="text" name="ad_soyad" placeholder="Adın Soyadın" required style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;">
          <input type="email" name="email" placeholder="E-posta adresin" required style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;">
          <input type="text" name="konu" placeholder="Konu" required style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;">
          <textarea name="aciklama" placeholder="Açıklama" required rows="4" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;resize:vertical;"></textarea>
          <div>
            <label style="display:block;font-size:13px;color:#6586a7;margin-bottom:4px;">Ekran Görüntüsü (isteğe bağlı)</label>
            <input type="file" name="ekran_goruntusu" accept="image/*" style="font-size:14px;">
          </div>
          <input type="hidden" name="sayfa" id="hataSayfa">
          <input type="hidden" name="tarayici" id="hataTarayici">
          <button type="submit" style="background:#5e8df7;color:#fff;border:none;border-radius:999px;padding:14px;font-size:16px;font-weight:700;cursor:pointer;">Gönder</button>
        </div>
      </form>
      <div id="hataSuccess" style="display:none;text-align:center;padding:30px 0;">
        <div style="font-size:60px;margin-bottom:10px;">✅</div>
        <p style="font-size:18px;font-weight:700;margin:0;">Hata bildirimin alındı!</p>
        <p style="color:#6586a7;margin:6px 0 0;">Teşekkürler, en kısa sürede inceliyoruz.</p>
      </div>
    </div>
  </div>

  <script>
    const penguin  = document.getElementById('penguin');
    const doorLeaf = document.getElementById('doorLeaf');

    function restartAnim(el, anim) {
      el.style.animation = 'none';
      requestAnimationFrame(() => requestAnimationFrame(() => { el.style.animation = anim; }));
    }

    function restartPenguin() {
      restartAnim(penguin,  'penguin-walk 12s linear forwards');
      restartAnim(doorLeaf, 'door-open 12s ease-in-out forwards');
    }

    function go(path) { window.location.href = path; }

    document.getElementById('hataSayfa').value = window.location.href;
    document.getElementById('hataTarayici').value = navigator.userAgent;

    document.getElementById('hataForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = this.querySelector('button[type=submit]');
      btn.disabled = true;
      btn.textContent = 'Gönderiliyor...';
      try {
        const res = await fetch('{{ route("hata-bildir.store") }}', {
          method: 'POST',
          body: new FormData(this),
          headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
          document.getElementById('hataForm').style.display = 'none';
          document.getElementById('hataSuccess').style.display = 'block';
          setTimeout(() => { document.getElementById('hataModal').style.display = 'none'; }, 2500);
        } else {
          alert('Bir hata oluştu, lütfen tekrar dene.');
        }
      } catch(e) {
        alert('Bir hata oluştu, lütfen tekrar dene.');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Gönder';
      }
    });
  </script>
  @include('partials.kayit')
</body>
</html>
