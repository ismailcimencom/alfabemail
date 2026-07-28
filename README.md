# Alfabe Mail — Çocuklar için Güvenli E-posta Sistemi

> **v1.8** — Kapsül Serix Teknoloji Platformu

Çocukların güvenli, reklamsız, kötü söz içermeyen ve kontrollü bir ortamda e-posta kullanmasını sağlayan eğitim odaklı mail platformu.

---

## 🚀 Özellikler

### Paneller
| Panel | URL | Kullanıcı | Açıklama |
|-------|-----|-----------|----------|
| Admin | `/admin` | admin | Tüm yönetim |
| Öğretmen | `/ogretmen` | ogretmen | Sınıf ve öğrenci yönetimi |
| Veli | `/veli` | veli | Akademik takip, AI raporları |
| Öğrenci | `/giris` | ...@alfabe.co | Karekodla giriş, mail kullanımı, ödev takibi |

---

## ✅ Tamamlanan Özellikler

### Giriş & Kimlik Doğrulama
- [x] Admin/Portal panel girişi (Filament auth)
- [x] Öğrenci karekod ile giriş
- [x] Öğrenci normal giriş
- [x] Aktivasyon linki ile ilk giriş (`/aktivasyon/{token}`)
- [x] Kayıt sistemi (e-posta doğrulama → şifre belirleme → admin onayı)
- [x] Onay bekleyen kullanıcılar için bulanık + penguen overlay
- [x] Admin onay sonrası ApprovalMail gönderimi (role göre içerik)

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
- [x] Mailcow mailbox senkronizasyonu (admin panelden)

### Öğretmen Paneli (`/ogretmen`)
- [x] Dashboard: sınıf sayısı, öğrenci sayıları (aktif/pasif), gönderilen/alınan mail istatistikleri
- [x] **Öğrenci Yönetimi**: CRUD, toplu yapıştırma (modal), Mailcow mailbox oluşturma, toplu yaka kartı
- [x] **Öğretmen Listesi**: Aynı sınıftaki öğretmenlerin iletişim bilgilerini görme
- [x] **Sınıf Yönetimi**: CRUD, öğretmen pivot ataması, sınıfa öğretmen davet/çıkarma
- [x] **Ödev Yönetimi**: CRUD, sınıfa/öğrenciye ödev atama, teslim tarihi, tamamlanma takibi
- [x] **Haftalık Program**: Ders programı görüntüleme
- [x] **Mail İstatistikleri**: Tüm zamanlar toplam grafiği

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
- [x] Sponsor yönetimi
- [x] Aktivite logları
- [x] Hata Bildirisi yönetimi
- [x] Yeni Kullanıcı Onay Sistemi (kayıt → onay → kullanıcıya taşıma)
- [x] Onay Bekleyen Kullanıcı listesi (PendingUserResource, varsayılan filtre "Onay Bekleyen")
- [x] Admin onay action: kullanıcıyı onaylama (is_active=true) veya silme
- [x] Yetki/rol yönetimi (YetkiManagement resource)
- [x] Mailcow Ayarları sayfası (API bağlantı yapılandırması, test bağlantı, şifreli depolama)
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
| POST | `/kayit/complete` | Kaydı tamamla (User+auto-login) |

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

Merkezi bir **nginx proxy** tüm alt projeleri yönetir.

### Canlı Ortam
```
alfabe.co ──► Cloudflare (Full strict SSL)
                │
                ▼
         alfabe-proxy (nginx:alpine, port 80/443)
                │
                ▼
           alfabemail:80
           (Laravel 13)
```

| Servis | Container | Ağ |
|--------|-----------|-----|
| **Proxy (nginx)** | alfabe-proxy | `alfabe_net` |
| **Alfabe Mail** | alfabemail | `sail`, `alfabe_net` |
| **Veritabanı** | mysql | `sail` |
| **Redis** | redis | `sail` |

### Veritabanı Hiyerarşisi
```
siniflar → ogrenciler → ogrenci_veli (pivot)
  → ogretmen_sinif (pivot)
  → users → (roles: admin, ogretmen, veli, ogrenci)
  → veliler
  → pending_users (kayıt onay bekleme)
  → settings (key-value yapılandırma, value alanı AES şifreli)
  → aktivasyon_tokens (e-posta doğrulama)
```

### Roller
1. **admin** — Tüm yönetim
2. **ogretmen** — Sınıf, öğrenci, ödev yönetimi
3. **veli** — Akademik takip, AI raporları
4. **ogrenci** — Mail kullanımı

### Multi-Tenant Veri İzolasyonu
- `HasTenantScope` trait ile role göre veri filtreleme
- **admin**: Tüm verileri görür
- **ogretmen**: Kendi sınıflarının (pivot veya direkt atama) verilerini görür
- **veli**: Kendi çocuklarının verilerini görür

### Mailcow API
- API key DB'de (`settings` tablosu) Laravel encrypted cast ile şifreli, düz metin olarak hiçbir yerde saklanmaz
- Admin panelden sadece "Bağlantıyı Test Et" yapılabilir, kaydet butonu yok
- Değiştirmek gerekirse: `Setting::updateOrCreate(['key' => 'mailcow_api_key'], ['value' => 'yeni-key'])` ile tinker üzerinden

### Yedekleme
| Zamanlama | Script | Açıklama |
|-----------|--------|----------|
| Günlük `03:00` | `mysqldump` → gzip | DB yedekleme, 7 gün sakla |

### Pratik Komutlar
```bash
# Loglar
docker compose logs -f laravel.test

# Artisan
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test php artisan optimize:clear
docker compose exec laravel.test php artisan tinker

# Container içine gir
docker compose exec laravel.test bash
```

---

## 📁 Önemli Dosyalar

### Servisler
- `app/Services/MailcowService.php` — Mailcow API (DB'den yapılandırma okuma, quota yönetimi, şifre güncelleme)
- `app/Services/VeliAnalizService.php` — AI haftalık özet
- `app/Services/ActivityLogger.php` — Aktivite loglama
- `app/Services/PermissionService.php` — İzin yönetimi
- `app/Services/DynamicMailer.php` — Dinamik mail gönderimi
- `app/Services/StudentCreationService.php` — Merkezi öğrenci oluşturma (Mailcow mailbox + User + Ogrenci + QR kod + veli)

### Controllers
- `app/Http/Controllers/OgrenciController.php` — Öğrenci işlemleri
- `app/Http/Controllers/VeliController.php` — Veli işlemleri
- `app/Http/Controllers/HataBildirController.php` — Hata bildirimi
- `app/Http/Controllers/KayitController.php` — Kayıt işlemleri (User+auto-login)
- `app/Http/Controllers/ActivationController.php` — Aktivasyon linki yönetimi
- `app/Http/Controllers/MailcowProxyController.php` — Mailcow API proxy (Sanctum korumalı)

### Modeller
- `app/Models/User.php` (roller, ogrenci/veli/ogretmen ilişkileri, `canAccessPanel`)
- `app/Models/Odev.php` — Ödev/takvim sistemi (ogretmen, sinif, ogrenci pivot)
- `app/Models/Ogrenci.php`, `Veli.php`, `Sinif.php`
- `app/Models/PendingUser.php`, `HataBildirisi.php`, `VeliMesaj.php`
- `app/Models/ActivityLog.php`, `MailAktiviteLog.php`, `Sponsor.php`
- `app/Models/Setting.php` — KV değer depolama (şifrelenmiş)
- `app/Models/AktivasyonToken.php` — Aktivasyon token yönetimi

### Traits
- `app/Traits/HasTenantScope.php` — Multi-tenant veri izolasyonu

### Console Commands
- `app/Console/Commands/FetchInboxMails.php` — `fetch:imail {type}` IMAP mail çekme
- `app/Console/Commands/CheckQuotaAndNotify.php` — `quota:check-notify` Günlük kota kontrolü (saat 09:00)
- `app/Console/Commands/SyncMailcowMailboxes.php` — `mailcow:sync-mailboxes` Mailcow mailbox'ları senkronize etme

### Filament Kaynakları (Admin - `/admin`)
- `app/Filament/Resources/PendingUserResource.php` — Yeni Kullanıcı Onayı (onayla/reddet action, ApprovalMail)
- `app/Filament/Resources/HataBildirisis/` — Hata Bildirisi Yönetimi
- `app/Filament/Resources/Users/` — Kullanıcı Yönetimi
- `app/Filament/Resources/ActivityLogs/` — Aktivite Logları
- `app/Filament/Resources/Sponsors/` — Sponsor Yönetimi
- `app/Filament/Resources/Yetki/` — Yetki/Rol Yönetimi

### Filament Kaynakları (Öğretmen Paneli - `/ogretmen`)
- `app/Filament/Portal/Resources/Ogrencis/` — Öğrenci yönetimi (toplu yapıştırma, Mailcow senkronizasyonu)
- `app/Filament/Portal/Resources/Ogretmenler/` — Öğretmen listesi (sadece aynı sınıftakiler)
- `app/Filament/Portal/Resources/Sinifs/` — Sınıf yönetimi (öğretmen iletişim bilgileriyle birlikte)
- `app/Filament/Portal/Resources/Odevler/` — Ödev yönetimi
- `app/Filament/Portal/Resources/HaftalikProgramlar/` — Haftalık ders programı

### Filament Widget'lar & Sayfalar
- `app/Filament/Pages/AdminDashboard.php` — Admin dashboard
- `app/Filament/Pages/MailcowSettings.php` — Mailcow API ayarları
- `app/Filament/Portal/Pages/PortalDashboard.php` — Portal dashboard
- `app/Filament/Portal/Widgets/VeliDashboardWidget.php` — Veli dashboard
- `app/Filament/Portal/Widgets/OgrenciIstatistikWidget.php` — Mail istatistikleri grafiği (bar)
- `app/Filament/Portal/Widgets/OgrenciIstatistikKartlariWidget.php` — İstatistik kartları (toplam/aktif/pasif öğrenci, sınıflar, gönderilen/alınan)
- `app/Filament/Portal/Widgets/HaftalikProgramWidget.php` — Haftalık ders programı tablosu
- `app/Filament/Portal/Widgets/OgrenciAktiviteWidget.php` — Öğrenci aktivite grafiği

### Görünümler
- `resources/views/welcome.blade.php` — Anasayfa
- `resources/views/ogrenci/` — Öğrenci dashboard, yaka kartı
- `resources/views/filament/portal/widgets/veli-dashboard.blade.php` — Veli dashboard
- `resources/views/filament/portal/widgets/mesaj-kutusu.blade.php` — Admin mesaj widget
- `resources/views/filament/pages/mailcow-settings.blade.php` — Mailcow ayar formu
- `resources/views/partials/hata-bildir.blade.php` — Hata bildir modalı
- `resources/views/partials/kayit.blade.php` — Kayıt modalı
- `resources/views/partials/onay-bekleniyor.blade.php` — Onay bekleyen overlay (bulanık + penguen)
- `resources/views/emails/verification-code.blade.php` — Doğrulama e-postası
- `resources/views/emails/approval.blade.php` — Onay e-postası
- `resources/views/legal/kvkk.blade.php` — KVKK sayfası
- `resources/views/legal/gizlilik.blade.php` — Gizlilik politikası
- `resources/views/legal/kullanim-sartlari.blade.php` — Kullanım şartları
- `resources/views/legal/cerez-politikasi.blade.php` — Çerez politikası

### Mail Sınıfları
- `app/Mail/ApprovalMail.php` — Kullanıcı onay maili (role göre içerik)
- `app/Mail/OgretmenSifreMail.php` — Öğretmen şifre belirleme maili

---

## 🔧 Geliştirme Notları

### Port Kullanımı
- Web: `80` (Docker), `APP_URL=http://127.0.0.1:80`
- Vite: `5173`
- MySQL: `3306` (Docker)
- Redis: `6379` (Docker)
- Storage symlink: `public/storage → /var/www/html/storage/app/public`

### Koyu Tema Uyumluluğu
Filament widget view'larında Tailwind kullanılmaz (purge sorunu). Tüm stiller inline olarak yazılır.

### Scheduled Tasks
```php
$schedule->command('quota:check-notify')->dailyAt('09:00');
```

---

## 📋 Değişiklik Geçmişi

### v1.8 — 2026-07-29
- **Yönetici rolü kaldırıldı**, özellikleri öğretmen paneline taşındı
- **Okul modeli tamamen kaldırıldı**, sınıf bazlı yapıya geçildi
- **Kayıt akışı yenilendi**: KayitController::complete() ile User(is_active=false) oluşturup auto-login
- **Onay bekleyen overlay** (bulanık + penguen animasyonu) eklendi
- **PendingUserResource** eklendi (admin onay/red action, ApprovalMail)
- **ApprovalMail** sınıfı ve görünümü oluşturuldu (role göre içerik)
- `canAccessPanel` güncellendi: onay bekleyen kullanıcılar panele girebilir
- **Öğrenci formu**: Admin tüm sınıfları görebilir, öğretmen kendi sınıflarını
- **Sınıf tablosu**: Öğretmen isim, e-posta, telefon bilgileri görünür
- **Öğretmen listesi**: Sadece aynı sınıftaki öğretmenler gösterilir
- **PortalStatsOverview** kaldırıldı, istatistikler OgrenciIstatistikKartlariWidget'a birleştirildi
- **Pasif Öğrenci** sayısı eklendi
- **Mailcow sync** timeout sorunu çözüldü (set_time_limit(0))
- Dashboard sıralaması: Kartlar → Haftalık Program → Mail Grafiği
- `SinifResource::canEdit()` hatası giderildi (`okul_id` referansı kaldırıldı)

### v1.7 — 2026-07-12
- **Sınıf seçerek toplu yaka kartı** aksiyonu eklendi
- Yaka kartında **şifre alanı** gösterilmeye başlandı
- **Toplu öğrenci ekleme** bildirim aksiyonu hatası giderildi
- **Öğretmen sınıf listesi** düzeltildi: pivot atamalarına fallback

### v1.6 — 2026-07-12
- **403 hata sayfası** popup tasarımıyla yenilendi
- Genel çıkış (`/logout`) route'u eklendi
- Öğretmen sınıf oluşturduğunda otomatik olarak pivot'a atanıyor
- Admin onay formu iyileştirildi

### v1.5 — 2026-07-12
- Toplu öğrenci ekleme metin yapıştırma (paste) sistemine geçildi
- Admin onayında Veli modeli oluşturma eklendi

### v1.4 — 2026-06-13
- **Fikir Paneli** butonu eklendi

### v1.3 — 2026-06-12
- QR kod boyutu 200 → 400 px
- Mailcow Ayarları sayfası salt okunur yapıldı
- Admin onayı ile kullanıcı ekleme sistemi

---

## 🚧 Gelecek Planları

- [ ] Öğrenci mail paneli UI redesign
- [ ] Arama ve filtreleme
- [ ] Mobil uyum
- [ ] Ödevlerde dosya ekleme desteği
- [ ] Öğretmen-öğrenci mesajlaşma (baykuş üzerinden direkt)
- [ ] Ödev hatırlatma bildirimleri

---

*Kapsül Serix Teknoloji Platformu Destek Ofisi — Konya*
