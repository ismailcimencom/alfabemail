# Alfabe Mail — Çocuklar için Güvenli E-posta Sistemi

> **v1.7** — Kapsül Serix Teknoloji Platformu

Çocukların güvenli, reklamsız, kötü söz içermeyen ve kontrollü bir ortamda e-posta kullanmasını sağlayan eğitim odaklı mail platformu.

---

## 🚀 Özellikler

### Paneller
| Panel | URL | Kullanıcı | Açıklama |
|-------|-----|-----------|----------|
| Admin | `/admin` | admin | Tüm yönetim |
| Yönetici | `/yonetici` | yonetici | Okul, öğretmen, sınıf, öğrenci, ödev yönetimi |
| Öğretmen | `/ogretmen` | ogretmen | Sınıf ve öğrenci yönetimi |
| Veli | `/veli` | veli | Akademik takip, AI raporları |
| Öğrenci | `/giris` | ...@alfabe.co | Karekodla giriş, mail kullanımı, ödev takibi |

---

## ✅ v1.1 Tamamlanan Özellikler

### Giriş & Kimlik Doğrulama
- [x] Admin/Portal panel girişi (Filament auth)
- [x] Öğrenci karekod ile giriş
- [x] Öğrenci normal giriş
- [x] Aktivasyon linki ile ilk giriş (`/aktivasyon/{token}`)
- [x] Kayıt sistemi (e-posta doğrulama → şifre belirleme → admin onayı)
- [x] Öğrenci oluştururken okul seçimi + okula göre sınıf seçimi

### Öğrenci Mail Sistemi
- [x] Gelen kutusu (IMAP)
- [x] Giden kutusu
- [x] Mail gönderme (SMTP)
- [x] Mail detay modalı
- [x] UTF-8 Türkçe destek
- [x] Çocuk dostu UI (penguen maskot 🐧 + baykuş maskot 🦉)
- [x] Yaka kartı oluşturma (karekodlu)
- [x] Toplu yaka kartı yazdırma (sınıf seçerek veya tablodan seçerek)
- [x] Dosya ekleme (attachment upload)
- [x] E-posta istatistikleri
- [x] Ödev/Teslim sistemi (öğretmen atar, öğrenci tamamlar)
- [x] Haftalık ödev takvimi
- [x] Baykuş maskot ile ödev bildirimleri
- [x] Kota yönetimi: öğrenciye 100 MB başlangıç, admin 1024 MB'a kadar yükseltebilir

### Yönetici Paneli (`/yonetici`)
- [x] Role göre özelleşmiş dashboard
- [x] **Öğrenci Yönetimi**: CRUD, toplu yapıştırma (modal), Mailcow mailbox oluşturma, toplu yaka kartı
- [x] **Öğretmen Yönetimi**: CRUD, sınıf atama, inline sınıf oluşturma
- [x] **Sınıf Yönetimi**: CRUD, okul bazlı filtreleme, öğretmen otomatik atama
- [x] **Ödev Yönetimi**: CRUD, sınıfa/öğrenciye ödev atama, teslim tarihi, tamamlanma takibi
- [x] **Okul Yönetimi** (admin): CRUD

### Öğretmen Paneli (`/ogretmen`)
- [x] Role göre özelleşmiş dashboard
- [x] **Öğrenci Yönetimi**: CRUD, toplu yapıştırma (modal), Mailcow mailbox oluşturma, toplu yaka kartı
- [x] **Sınıf Yönetimi**: CRUD, okul bazlı filtreleme, öğretmen pivot ataması
- [x] **Ödev Yönetimi**: CRUD, sınıfa/öğrenciye ödev atama, teslim tarihi, tamamlanma takibi

### Veli Paneli
- [x] AI Haftalık Özet Raporu (VeliAnalizService)
- [x] Aktivite Takvimi (son 7 gün)
- [x] Kota/Uyarı Bildirimleri (Mailcow API)
- [x] Veli-Öğretmen Mesajlaşma
- [x] Öğrenci Şifre Sıfırlama
- [x] Çoklu Öğrenci Karşılaştırma
- [x] Öğrenci e-posta istatistik grafikleri (Chart.js)
- [x] Dashboard widget'ları (istatistik kartları, aktivite grafiği)

### Admin Paneli
- [x] Kullanıcı yönetimi (CRUD, rol atama, telefon alanı)
- [x] Okul yönetimi ve onay sistemi (beklemede/onaylı/red)
- [x] Sponsor yönetimi
- [x] Aktivite logları
- [x] Hata Bildirisi yönetimi
- [x] Yeni Kullanıcı Onay Sistemi (kayıt → onay → kullanıcıya taşıma)
- [x] Yetki/rol yönetimi (YetkiManagement resource)
- [x] Mailcow Ayarları sayfası (API bağlantı yapılandırması, test bağlantı, şifreli depolama)
- [x] Admin Dashboard widget'ları (istatistik özeti, kayıt grafiği, bildirimler)
- [x] Kullanıcı rollerine göre panel erişim kontrolü (canAccessPanel)
- [x] Öğrenci kota yönetimi: 100-1024 MB arası değiştirme (change_quota butonu)
- [x] **Fikir Paneli** butonu: Admin panelde sol altta 💡 buton (`yolharitasi.alfabe.co`)

### Hata Bildir Sistemi
- [x] Tüm sayfalarda floating ⚠️ butonu
- [x] AJAX ile form gönderimi
- [x] Ekran görüntüsü yükleme
- [x] Admin panelinde yönetim (çözüldü/çözülmedi)

### E-posta Güvenliği (DNS)
- [x] SPF kaydı (`v=spf1 mx a:mail.alfabe.co -all`)
- [x] DKIM imzası (RSA 2048 bit)
- [x] DMARC politikası (`p=quarantine`)
- [x] PTR kaydı (`45.94.4.39 → mail.alfabe.co`)

---

## 📡 API Endpoint'leri

### Öğrenci
| Method | Route | Açıklama |
|--------|-------|----------|
| POST | `/ogrenci/login` | Giriş |
| POST | `/ogrenci/qr-login` | Karekod girişi |
| POST | `/ogrenci/logout` | Çıkış |
| GET | `/ogrenci/inbox` | Gelen kutusu |
| GET | `/ogrenci/sent` | Giden kutusu |
| POST | `/ogrenci/send-mail` | Mail gönderme |
| POST | `/ogrenci/log-read` | Okundu kaydı |
| GET | `/ogrenci/yaka-karti/{id}` | Yaka kartı |
| GET | `/ogrenci/yaka-karti-bulk` | Toplu yaka kartı |
| POST | `/ogrenci/upload-attachment` | Dosya yükleme |
| GET | `/ogrenci/stats` | E-posta istatistikleri |

### Kayıt
| Method | Route | Açıklama |
|--------|-------|----------|
| POST | `/kayit/send-code` | Doğrulama kodu gönder |
| POST | `/kayit/verify-code` | Kodu doğrula |
| POST | `/kayit/complete` | Kaydı tamamla |

### Veli
| Method | Route | Açıklama |
|--------|-------|----------|
| POST | `/veli/mesaj-gonder` | Öğretmene mesaj |
| POST | `/veli/sifre-sifirla` | Öğrenci şifre sıfırlama |
| GET | `/veli/dashboard` | Veli dashboard sayfası |

### Aktivasyon
| Method | Route | Açıklama |
|--------|-------|----------|
| GET | `/aktivasyon/{token}` | Aktivasyon linki ile giriş |

### Mailcow Proxy API (Sanctum auth)
| Method | Route | Açıklama |
|--------|-------|----------|
| GET | `/api/mailcow/status` | Mailcow bağlantı durumu |
| GET | `/api/mailcow/mailboxes` | Mailbox listesi |
| GET | `/api/mailcow/quota/{email}` | Kota sorgulama |
| POST | `/api/mailcow/mailbox` | Mailbox oluşturma |
| DELETE | `/api/mailcow/mailbox/{email}` | Mailbox silme |

### Docker IMAP Workaround
| Method | Route | Açıklama |
|--------|-------|----------|
| GET | `/api/mails/inbox` | Docker exec ile inbox çekme |
| GET | `/api/mails/sent` | Docker exec ile sent çekme |

### Ödev Sistemi
| Method | Route | Açıklama |
|--------|-------|----------|
| GET | `/ogrenci/odevler` | Öğrencinin ödev listesi (bekleyen/tamamlanan) |
| POST | `/ogrenci/odev-tamamla` | Ödevi tamamlandı olarak işaretle |

### Hata Bildir
| Method | Route | Açıklama |
|--------|-------|----------|
| POST | `/hata-bildir` | Hata bildirisi gönder |

### Debug
| Method | Route | Açıklama |
|--------|-------|----------|
| GET | `/debug/mailcow-test` | Mailcow bağlantı testi |
| GET | `/debug/mailcow-create` | Mailbox oluşturma debug |
| GET | `/debug/cleanup-ogrenciler` | Orphan temizlik |

### Yasal Sayfalar
| Method | Route | Açıklama |
|--------|-------|----------|
| GET | `/kvkk` | KVKK Aydınlatma Metni |
| GET | `/gizlilik` | Gizlilik Politikası |
| GET | `/kullanim-sartlari` | Kullanım Şartları |
| GET | `/cerez-politikasi` | Çerez Politikası |

---

## 🛠 Teknik Mimari

### Stack
- **Backend**: Laravel 13 + Filament 4
- **Veritabanı**: MySQL 8.4 (Docker)
- **Mail Sunucusu**: Mailcow (Docker)
- **Cache/Queue**: Redis Alpine
- **Yetki**: Spatie Laravel Permission
- **Frontend**: Blade + Chart.js + IMAP/SMTP

### Docker (Laravel Sail)
- PHP 8.5 (Sail runtime), MySQL 8.4, Redis Alpine
- `compose.yaml` (Sail) ile yönetilir
- `alfabemail_sail` network + `mailcowdockerized_mailcow-network` (shared Mailcow access)

---

## 🌐 Dağıtım Mimarisi (Sunucu)

Merkezi bir **nginx proxy** tüm alt projeleri yönetir. Her proje kendi `docker-compose`'u ile ayrı ayrı çalışır, ortak bir Docker network'ü üzerinden proxy'e bağlanır.

### Mimari Şeması

```
                    ┌─────────────────────┐
                    │   alfabe-proxy      │
                    │  (nginx + SSL)      │
                    │  80 / 443           │
                    └────────┬────────────┘
                             │ alfabe_net (shared network)
          ┌──────────────────┼──────────────────┐
          ▼                  ▼                   ▼
   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
   │ alfabemail   │  │ alfabe-forum │  │ alfabe-oyun  │
   │ Laravel:8001 │  │   WP:8080    │  │  ?? :XXXX    │
   └──────────────┘  └──────────────┘  └──────────────┘
```

### Bileşenler

#### 1. `alfabe-proxy` (Merkezi Nginx)
- Ayrı bir repo/dizin, sadece nginx + SSL içerir
- Port 80 (HTTP) ve 443 (HTTPS) üzerinden dinler
- `alfabe_net` adlı external Docker network'üne bağlıdır
- Her domain için ayrı `server_name` bloğu içerir

#### 2. `alfabe_net` (Paylaşımlı Docker Network)
```bash
docker network create alfabe_net
```
Tüm projeler bu network'e bağlanır. Böylece nginx, projelere container adıyla erişebilir.

#### 3. Her Proje (alfabemail, alfabe-forum, ...)
- Kendi `docker-compose.yml`'si ile bağımsız çalışır
- Kendi portunda yayın yapar (alfabemail: 8001, forum: 8080)
- `alfabe_net` network'üne ek olarak bağlanır

### Alfabemail Sunucu Yapılandırması

`.env` (sunucu):
```env
APP_PORT=8001
APP_ENV=production
APP_URL=https://alfabe.co
NGINX_PORT=80    # alfabe-proxy için, 80'de dinler
```

`compose.yaml`'a eklenecek network:
```yaml
networks:
  alfabe_net:
    external: true
    name: alfabe_net

services:
  laravel.test:
    networks:
      - sail
      - alfabe_net
```

### Nginx Proxy Config Örneği (`alfabe-proxy/nginx/default.conf`)

```nginx
server {
    listen 80;
    server_name alfabe.co www.alfabe.co;

    location / {
        proxy_pass http://alfabemail-laravel.test-1:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}

server {
    listen 80;
    server_name forum.alfabe.co;

    location / {
        proxy_pass http://wordpress:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto https;
    }
}

server {
    listen 80;
    server_name oyun.alfabe.co;

    location / {
        proxy_pass http oyun-container:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Yeni Site Ekleme (ör: oyun.alfabe.co)

```
1. Yeni proje klasörü oluştur, docker-compose.yml yaz
2. Tüm servislerine alfabe_net network'ünü ekle
3. alfabe-proxy/nginx/default.conf'a server block ekle
4. docker compose restart proxy (nginx yeniden yükle)
5. DNS'e oyun.alfabe.co → sunucu IP
```

### Avantajları

| Özellik | Açıklama |
|---------|----------|
| **Ölçeklenebilir** | Sınırsız sayıda alt site eklenebilir |
| **Bağımsız** | Her proje ayrı ayrı güncellenir, birbirini etkilemez |
| **Tek giriş noktası** | Sadece nginx 80/443'ten dışarı açık, diğer portlar kapalı |
| **SSL tek noktada** | Certbot veya Cloudflare ile SSL tek nginx'te yönetilir |
| **Container adıyla erişim** | Nginx proxy'den direkt container ismiyle erişim (DNS) |

### Veritabanı Hiyerarşisi
```
okullar → siniflar → ogrenciler → ogrenci_veli (pivot)
       → users → (roles: admin, yonetici, ogretmen, veli, ogrenci)
       → veliler
       → pending_users (kayıt onay bekleme)
       → settings (key-value yapılandırma, value alanı AES şifreli)
       → aktivasyon_tokens (e-posta doğrulama)
```

### Roller
1. **admin** — Tüm yönetim
2. **yonetici** — Okul yönetimi, öğretmen/sınıf yönetimi
3. **ogretmen** — Öğrenci yönetimi
4. **veli** — Akademik takip, AI raporları
5. **ogrenci** — Mail kullanımı

### Multi-Tenant Veri İzolasyonu
- `HasTenantScope` trait ile role göre veri filtreleme
- **admin**: Tüm verileri görür
- **yonetici**: Kendi okulunun verilerini görür
- **ogretmen**: Kendi sınıflarının verilerini görür
- **veli**: Kendi çocuklarının verilerini görür

---

## 🌐 Sunucu Bilgileri

### Mailcow
| Servis | Adres |
|--------|-------|
| API | `https://mail.alfabe.co/api/v1/` |
| SMTP | `mail.alfabe.co:587` (TLS) |
| IMAP | `mail.alfabe.co:993` (SSL) |
| Domain | `alfabe.co` |

### DNS Kayıtları
| Kayıt | Değer | Durum |
|-------|-------|-------|
| SPF | `v=spf1 mx a:mail.alfabe.co -all` | ✅ |
| DKIM | `dkim._domainkey.alfabe.co` | ✅ |
| DMARC | `v=DMARC1; p=quarantine; rua=mailto:postmaster@alfabe.co` | ✅ |
| PTR | `45.94.4.39 → mail.alfabe.co` | ✅ |

### Development
- Local (Docker): `http://127.0.0.1:80`

---

## 📁 Önemli Dosyalar

### Servisler
- `app/Services/MailcowService.php` — Mailcow API (DB'den yapılandırma okuma, quota yönetimi, şifre güncelleme)
- `app/Services/VeliAnalizService.php` — AI haftalık özet
- `app/Services/ActivityLogger.php` — Aktivite loglama
- `app/Services/PermissionService.php` — İzin yönetimi (rol grupları: okul, ogretmen, ogrenci, mailbox, rapor, sistem)
- `app/Services/DynamicMailer.php` — Dinamik mail gönderimi
- `app/Services/StudentCreationService.php` — Merkezi öğrenci oluşturma (Mailcow mailbox + User + Ogrenci + QR kod + veli)

### Controllers
- `app/Http/Controllers/OgrenciController.php` — Öğrenci işlemleri
- `app/Http/Controllers/VeliController.php` — Veli işlemleri
- `app/Http/Controllers/HataBildirController.php` — Hata bildirimi
- `app/Http/Controllers/KayitController.php` — Kayıt işlemleri
- `app/Http/Controllers/ActivationController.php` — Aktivasyon linki yönetimi
- `app/Http/Controllers/MailcowProxyController.php` — Mailcow API proxy (Sanctum korumalı)

### Modeller
- `app/Models/User.php` (roller, `okul()`/`bagli_okul()`/ogrenci/veli/ogretmen ilişkileri, `canAccessPanel`, `olusturulan_odevler`)
- `app/Models/Odev.php` — Ödev/takvim sistemi (ogretmen, sinif, ogrenci pivot)
- `app/Models/Ogrenci.php`, `Veli.php`, `Sinif.php`, `Okul.php`
- `app/Models/PendingUser.php`, `HataBildirisi.php`, `VeliMesaj.php`
- `app/Models/ActivityLog.php`, `MailAktiviteLog.php`, `Sponsor.php`
- `app/Models/Setting.php` — KV değer depolama (şifrelenmiş)
- `app/Models/AktivasyonToken.php` — Aktivasyon token yönetimi

### Traits
- `app/Traits/HasTenantScope.php` — Multi-tenant veri izolasyonu

### Console Commands
- `app/Console/Commands/FetchInboxMails.php` — `fetch:imail {type}` IMAP mail çekme
- `app/Console/Commands/CheckQuotaAndNotify.php` — `quota:check-notify` Günlük kota kontrolü (saat 09:00)

### Filament Kaynakları (Admin - `/admin`)
- `app/Filament/Resources/PendingUserResource.php` — Yeni Kullanıcı Onayı
- `app/Filament/Resources/HataBildirisis/` — Hata Bildirisi Yönetimi
- `app/Filament/Resources/Users/` — Kullanıcı Yönetimi
- `app/Filament/Resources/ActivityLogs/` — Aktivite Logları
- `app/Filament/Resources/Sponsors/` — Sponsor Yönetimi
- `app/Filament/Resources/Yetki/` — Yetki/Rol Yönetimi
- `app/Filament/Resources/Okuls/Pages/OkulOnay/` — Okul Onayları

### Filament Kaynakları (Yönetici Paneli - `/yonetici`)
- `app/Filament/Portal/Resources/Ogrencis/` — Öğrenci yönetimi (toplu yapıştırma modalı ile `ListOgrencis.php`)
- `app/Filament/Portal/Resources/Ogretmenler/` — Öğretmen yönetimi
- `app/Filament/Portal/Resources/Sinifs/` — Sınıf yönetimi
- `app/Filament/Portal/Resources/Okuls/` — Okul yönetimi
- `app/Filament/Portal/Resources/Odevler/` — Ödev yönetimi (CRUD, sınıfa atama, tamamlanma takibi)

### Filament Widget'lar & Sayfalar
- `app/Filament/Pages/AdminDashboard.php` — Admin dashboard
- `app/Filament/Pages/MailcowSettings.php` — Mailcow API ayarları
- `app/Filament/Portal/Pages/PortalDashboard.php` — Portal dashboard
- `app/Filament/Portal/Widgets/VeliDashboardWidget.php` — Veli dashboard
- `app/Filament/Portal/Widgets/PortalStatsOverview.php` — Portal istatistik kartları
- `app/Filament/Portal/Widgets/OgrenciIstatistikWidget.php` — Öğrenci istatistik grafiği
- `app/Filament/Portal/Widgets/OgrenciIstatistikKartlariWidget.php` — Öğrenci istatistik kartları
- `app/Filament/Portal/Widgets/OgrenciAktiviteWidget.php` — Öğrenci aktivite grafiği

### Görünümler
- `resources/views/welcome.blade.php` — Anasayfa
- `resources/views/ogrenci/` — Öğrenci dashboard, yaka kartı
- `resources/views/filament/portal/widgets/veli-dashboard.blade.php` — Veli dashboard (386 satır)
- `resources/views/filament/portal/widgets/mesaj-kutusu.blade.php` — Admin mesaj widget
- `resources/views/filament/portal/pages/okul-istek.blade.php` — Okul talep sayfası (UI)
- `resources/views/filament/pages/mailcow-settings.blade.php` — Mailcow ayar formu
- `resources/views/filament/admin/widgets/bildirim-widget.blade.php` — Admin bildirim widget
- `resources/views/filament/admin/widgets/mesajlar-widget.blade.php` — Admin mesajlar widget
- `resources/views/partials/hata-bildir.blade.php` — Hata bildir modalı
- `resources/views/partials/kayit.blade.php` — Kayıt modalı
- `resources/views/partials/kayit-link.blade.php` — Kayıt link partial
- `resources/views/emails/verification-code.blade.php` — Doğrulama e-postası
- `resources/views/legal/kvkk.blade.php` — KVKK sayfası
- `resources/views/legal/gizlilik.blade.php` — Gizlilik politikası
- `resources/views/legal/kullanim-sartlari.blade.php` — Kullanım şartları
- `resources/views/legal/cerez-politikasi.blade.php` — Çerez politikası

---

## 🏗 Altyapı

### Canlı Ortam (2.59.119.28)
```
alfabe.co ──► Cloudflare (Full strict SSL)
                │
                ▼
         alfabe-proxy (nginx:alpine, port 80/443)
                │
         ┌──────┴──────┐
         ▼              ▼
   alfabemail:80    wordpress:80
   (Laravel 13)    (WordPress + bbPress)
```

| Servis | Container | Port | Ağ |
|--------|-----------|------|-----|
| **Proxy (nginx)** | alfabe-proxy | `80`/`443` | `internal`, `alfabe_net` |
| **Alfabe Mail** | alfabemail | `8001` → `80` | `sail`, `alfabe_net` |
| **Alfabe Forum** | wordpress | `8080` → `80` | `internal` |
| **Veritabanı (Mail)** | mysql | `3306` | `sail` |
| **Veritabanı (Forum)** | mariadb | `3306` | `internal` |
| **Redis** | redis | `6379` | `sail` |

### Ağ Yapısı
- Tüm servisler ortak `alfabe_net` (external Docker network) üzerinden container ismiyle birbirini görür
- Proxy dışında hiçbir servis dışarıya port açmaz (forum wordpress hariç `8080`)
- SSL: Let's Encrypt (DNS Cloudflare challenge) + Certbot otomatik yenileme
- Sertifikalar: `/etc/letsencrypt/live/alfabe.co/` (alfabe.co, www.alfabe.co, forum.alfabe.co)

### Yedekleme
| Zamanlama | Script | Açıklama |
|-----------|--------|----------|
| Günlük `03:00` | `/opt/alfabe-forum/scripts/backup.sh` | Her iki DB'yi `mysqldump` → gzip, 7 gün sakla |
| Günlük `03:30` | `/opt/alfabe-forum/scripts/renew-cert.sh` | SSL sertifika yenileme |

### Mailcow API
- API key DB'de (`settings` tablosu) Laravel encrypted cast ile şifreli, düz metin olarak hiçbir yerde saklanmaz
- Admin panelden sadece "Bağlantıyı Test Et" yapılabilir, kaydet butonu yok
- Değiştirmek gerekirse: `Setting::updateOrCreate(['key' => 'mailcow_api_key'], ['value' => 'yeni-key'])` ile tinker üzerinden

### ♻️ Yedekten Geri Yükleme
```bash
gunzip -c /backup/alfabemail_2026-05-23.sql.gz \
  | docker compose exec -T mysql \
    mysql -u sail -p"$DB_PASSWORD" alfabemail
```

### 🛠 Pratik Komutlar
```bash
# Loglar
docker compose logs -f laravel.test

# Artisan
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test php artisan cache:clear
docker compose exec laravel.test php artisan tinker

# Container içine gir
docker compose exec laravel.test bash
```

## 🔧 Geliştirme Notları

### Port Kullanımı
- Web: `80` (Docker), `APP_URL=http://127.0.0.1:80`
- Vite: `5173`
- MySQL: `3306` (Docker)
- Redis: `6379` (Docker)
- Storage symlink: `public/storage → /var/www/html/storage/app/public` (host mutlak yol değil)

### Koyu Tema Uyumluluğu
Filament widget view'larında Tailwind kullanılmaz (purge sorunu). Tüm stiller inline olarak yazılır. Input alanlarında `color:#1a202c` ile koyu temada okunabilirlik sağlanır.

### Scheduled Tasks
```php
// app/Console/Kernel.php
$schedule->command('quota:check-notify')->dailyAt('09:00');
```

### Yeni Resource Eklerken
```php
use BackedEnum;

protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';
protected static ?string $model = MyModel::class;
protected static bool $shouldRegisterNavigation = true;

public static function canAccess(): bool
{
    return auth()->user()?->hasRole('admin') ?? false;
}

public static function getPages(): array
{
    return ['index' => Pages\ListMyModel::route('/')];
}
```

---

## 📋 Değişiklik Geçmişi

### v1.7 — 2026-07-12
- **Sınıf seçerek toplu yaka kartı** aksiyonu eklendi (header butonu, sınıf seçme modalı)
- Yaka kartında **şifre alanı** gösterilmeye başlandı
- **Toplu öğrenci ekleme** bildirim aksiyonu hatası giderildi (`mountAction` bulunamıyordu)
- **Öğretmen sınıf listesi** düzeltildi: `okul_id` null ise pivot atamalarına fallback yapılıyor
- **Yönetici okul bağlantısı** null olduğunda tüm kaynaklar (sınıf, öğrenci, ödev, program) görüntüleme hatası giderildi
- Yönetici sınıf oluşturunca okul bağlantısı otomatik kuruluyor

### v1.6 — 2026-07-12
- **403 hata sayfası** popup tasarımıyla yenilendi ("Buraya girmeye yetkiniz yok!" + Çıkış Yap butonu)
- Genel çıkış (`/logout`) route'u eklendi (panel kullanıcıları için)
- **Öğretmen sınıf listesi** düzeltildi: öğretmenler artık okuldaki tüm sınıfları görebiliyor
- Öğretmen sınıf oluşturduğunda otomatik olarak pivot'a atanıyor
- **Admin onay formu** iyileştirildi: Ad Soyad ve Okul Adı alanları eklendi, mükerrer e-posta kontrolü eklendi
- "Oluştur & yeni oluştur" butonu "Oluştur & devam et" olarak değiştirildi
- Footer metni tek paragraf haline getirildi
- Filament çeviri override mekanizması kuruldu (`lang/vendor/filament-actions/tr/`)

### v1.5 — 2026-07-12
- **Toplu öğrenci ekleme** dosya yüklemeden çıkarıldı, metin yapıştırma (paste) sistemine geçildi
- Öğretmen artık Excel'den kopyaladığı öğrenci listesini doğrudan textarea'a yapıştırıyor
- Desteklenen ayraçlar: tab, virgül, noktalı virgül, pipe
- Başlık satırı opsiyonel hale getirildi (ad, soyad bulunamazsa 1. sütun ad, 2. sütun soyad kabul edilir)
- Şifre boş bırakılırsa varsayılan `Alfabe123!` atanıyor
- `parseFile()` metodu kaldırıldı, yerine `parseText()` yazıldı
- Örnek Excel indirme route'u temizlendi (`ob_start` hatası giderildi, temp dosya ile download yapılıyor)
- Eski `toplu-ogrenci-ekle.blade.php` view'i kullanımdan kaldırıldı
- Veli giriş sayfası kayıt metinleri güncellendi
- Admin onayında Veli modeli oluşturma eklendi
- **Fikir Paneli** sidebar menüsü ve sağ alt amber 💡 buton kaldırıldı (sol alttaki 💡 korundu)

### v1.4 — 2026-06-13
- **Fikir Paneli** butonu eklendi: Admin panel sidebar'da "Dış Bağlantılar" grubu altında `yolharitasi.alfabe.co` linki, sağ alt köşede floating 💡 buton (amber renk)
- Güncellemeler README.md dosyasına işlenmeye başlandı

### v1.3 — 2026-06-12
- **QR kod** boyutu 200 → 400 px (daha okunabilir yaka kartları)
- **Yaka kartı** yazdırma iyileştirmeleri (`shape-rendering: crispEdges`, print CSS)
- **Mailcow Ayarları** sayfası salt okunur yapıldı, API key artık ön doldurulmuyor
- **Admin onayı** ile kullanıcı ekleme sistemi
- Mailcow API yapılandırması `.env` üzerinden yapılıyor

---

## 🚧 Gelecek Planları

- [ ] Öğrenci mail paneli UI redesign
- [ ] Arama ve filtreleme
- [ ] Gamification rozetleri — kısmen tamamlandı
- [ ] Mobil uyum
- [ ] Ödevlerde dosya ekleme desteği
- [ ] Öğretmen-öğrenci mesajlaşma (baykuş üzerinden direkt)
- [ ] Ödev hatırlatma bildirimleri
- [ ] Ana sayfada Mailcow API'den anlık posta kutusu sayısı ✅

---

*Kapsül Serix Teknoloji Platformu Destek Ofisi — Konya*
