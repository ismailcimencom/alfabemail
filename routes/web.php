<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OgrenciController;
use App\Http\Controllers\VeliController;

// Ana sayfa — penguen animasyonu
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', function () {
    return redirect('/');
})->name('login');

// Öğrenci girişi (QR + form)
Route::get('/giris', function () {
    return view('ogrenci.giris');
})->name('ogrenci.giris');

Route::get('/ogrenci/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/giris');
    }
    return view('ogrenci.dashboard');
})->name('ogrenci.dashboard');

Route::post('/ogrenci/login', [OgrenciController::class, 'login'])->name('ogrenci.login');
Route::post('/ogrenci/qr-login', [OgrenciController::class, 'qrLogin'])->name('ogrenci.qr-login');
Route::post('/ogrenci/logout', [OgrenciController::class, 'logout'])->name('ogrenci.logout');

// Genel çıkış (tüm paneller için)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');
Route::get('/ogrenci/yaka-karti/{ogrenci}', [OgrenciController::class, 'yakaKarti'])->name('ogrenci.yaka-karti');
Route::get('/ogrenci/yaka-karti-bulk', [OgrenciController::class, 'yakaKartiBulk'])->name('ogrenci.yaka-karti.bulk');

Route::post('/ogrenci/send-mail', [OgrenciController::class, 'sendMail'])->name('ogrenci.send-mail');
Route::post('/ogrenci/upload-attachment', [OgrenciController::class, 'uploadAttachment'])->name('ogrenci.upload-attachment');
Route::get('/ogrenci/inbox', [OgrenciController::class, 'getInbox'])->name('ogrenci.inbox');
Route::get('/ogrenci/sent', [OgrenciController::class, 'getSent'])->name('ogrenci.sent');
Route::get('/ogrenci/stats', [OgrenciController::class, 'getStats'])->name('ogrenci.stats');
Route::get('/ogrenci/quota', [OgrenciController::class, 'getQuota'])->name('ogrenci.quota');
Route::get('/ogrenci/mail/{id}/fetch', [OgrenciController::class, 'fetchMail'])->name('ogrenci.mail.fetch');
Route::post('/ogrenci/log-read', [OgrenciController::class, 'logRead'])->name('ogrenci.log-read');
Route::post('/ogrenci/zip/extract', [App\Http\Controllers\ZipController::class, 'extract'])->name('ogrenci.zip.extract');
Route::post('/ogrenci/zip/extract-url', [App\Http\Controllers\ZipController::class, 'extractFromUrl'])->name('ogrenci.zip.extract-url');

// Docker'dan IMAP verisi çek (tmp sorunu için workaround)
Route::get('/api/mails/inbox', function () {
    $output = shell_exec("docker compose exec -T laravel.test php artisan fetch:imail inbox 2>/dev/null");
    $data = json_decode($output, true);
    return response()->json($data ?: ['success' => false, 'mails' => []]);
});

Route::get('/api/mails/sent', function () {
    $output = shell_exec("docker compose exec -T laravel.test php artisan fetch:imail sent 2>/dev/null");
    $data = json_decode($output, true);
    return response()->json($data ?: ['success' => false, 'mails' => []]);
});

Route::get('/debug/mailcow-test', function () {
    $mailcow = app(\App\Services\MailcowService::class);
    $mailcow->refreshConfig();
    
    $settings = \App\Models\Setting::where('key', 'like', 'mailcow_%')->get();
    
    return response()->json([
        'settings_in_db' => $settings->pluck('value', 'key'),
        'configured' => $mailcow->isConfigured(),
        'test_result' => $mailcow->testConnection(),
    ]);
});

Route::get('/debug/mailcow-create', function () {
    $mailcow = app(\App\Services\MailcowService::class);
    $mailcow->refreshConfig();
    
    try {
        $result = $mailcow->createStudentMailbox('Test', 'Ogrenci', null);
        return response()->json(['success' => true, 'result' => $result]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});

Route::get('/debug/cleanup-ogrenciler', function () {
    // Ogrenci rolü + ogrenci kaydı olmayan
    $ogrenciOrphans = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'ogrenci'))
        ->whereDoesntHave('ogrenci')
        ->pluck('id');
    
    $deletedOgrenci = \App\Models\User::whereIn('id', $ogrenciOrphans)->delete();
    
    // Ogrenciler tablosunda user_id'si olmayan kayıtlar (orphaned ogrenci)
    $orphanedOgrenciler = \App\Models\Ogrenci::whereNull('user_id')->delete();
    
    // Mailbox oluşturulmuş ama kullanıcı kaydı başarısız olmuş - ali, veli, öğretmen prefixli
    $deletedGhost = \App\Models\User::where(function($q) {
        $q->where('email', 'like', 'ali%@alfabe.co')
          ->orWhere('email', 'like', 'veli%@alfabe.co')
          ->orWhere('email', 'like', 'ogretmen%@alfabe.co');
    })->delete();
    
    // Veli kaydı olmayan veli kullanıcıları
    $veliOrphans = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'veli'))
        ->whereRaw('id NOT IN (SELECT user_id FROM veliler WHERE user_id IS NOT NULL)')
        ->pluck('id');
    $deletedVeli = \App\Models\User::whereIn('id', $veliOrphans)->delete();
    
    return response()->json([
        'deleted_ogrenci_orphans' => $deletedOgrenci,
        'orphaned_ogrenciler' => $orphanedOgrenciler,
        'deleted_ghost' => $deletedGhost,
        'deleted_veli_orphans' => $deletedVeli,
    ]);
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/ogrenci/mail', [OgrenciController::class, 'redirectToMail'])->name('ogrenci.mail');
    Route::get('/ogrenci/dashboard', [OgrenciController::class, 'dashboard'])->name('ogrenci.dashboard');
    Route::get('/veli/dashboard', function () {
        return view('veli.dashboard');
    })->name('veli.dashboard');

    Route::get('/ogrenci/odevler', [OgrenciController::class, 'getOdevler'])->name('ogrenci.odevler');
    Route::post('/ogrenci/odev-tamamla', [OgrenciController::class, 'odevTamamla'])->name('ogrenci.odev-tamamla');
});

// Öğretmen Toplu Mail Aç
use App\Http\Controllers\OgretmenTopluMailController;

Route::prefix('ogretmen')->group(function () {
    Route::get('/toplu-mail-ac', [OgretmenTopluMailController::class, 'showForm'])->name('ogretmen.toplu-mail.form');
    Route::post('/toplu-mail-ac/send-code', [OgretmenTopluMailController::class, 'sendCode'])->name('ogretmen.toplu-mail.send-code');
    Route::post('/toplu-mail-ac/verify-code', [OgretmenTopluMailController::class, 'verifyCode'])->name('ogretmen.toplu-mail.verify-code');
});

// Hata bildir
Route::post('/hata-bildir', [App\Http\Controllers\HataBildirController::class, 'store'])->name('hata-bildir.store');

// Kayit
Route::post('/kayit/send-code', [App\Http\Controllers\KayitController::class, 'sendCode'])->name('kayit.send-code');
Route::post('/kayit/verify-code', [App\Http\Controllers\KayitController::class, 'verifyCode'])->name('kayit.verify-code');
Route::post('/kayit/complete', [App\Http\Controllers\KayitController::class, 'complete'])->name('kayit.complete');

// Veli işlemleri
Route::middleware(['auth'])->prefix('veli')->group(function () {
    Route::post('/mesaj-gonder', [App\Http\Controllers\VeliController::class, 'mesajGonder']);
    Route::post('/sifre-sifirla', [App\Http\Controllers\VeliController::class, 'sifreSifirla']);
});

// Admin Chat
Route::middleware(['auth'])->prefix('admin-chat')->group(function () {
    Route::get('/messages', [App\Http\Controllers\AdminChatController::class, 'getMessages']);
    Route::post('/send', [App\Http\Controllers\AdminChatController::class, 'sendMessage']);
    Route::get('/online', [App\Http\Controllers\AdminChatController::class, 'getOnlineAdmins']);
});

// Yasal sayfalar
Route::view('/kvkk', 'legal.kvkk')->name('kvkk');
Route::view('/gizlilik', 'legal.gizlilik')->name('gizlilik');
Route::view('/kullanim-sartlari', 'legal.kullanim-sartlari')->name('kullanim-sartlari');
Route::view('/cerez-politikasi', 'legal.cerez-politikasi')->name('cerez-politikasi');

// Aktivasyon linkleri
Route::get('/aktivasyon/{token}', [App\Http\Controllers\ActivationController::class, 'activate'])->name('activation.activate');

// Öğretmen şifre belirleme (email linki ile)
Route::get('/ogretmen/sifre-belirle/{token}', [App\Http\Controllers\ActivationController::class, 'ogretmenSifreBelirle'])->name('ogretmen.sifre-belirle');
Route::post('/ogretmen/sifre-belirle/{token}', [App\Http\Controllers\ActivationController::class, 'ogretmenSifreKaydet']);

// API routes (Mailcow Proxy) - using /api prefix
use App\Http\Controllers\MailcowProxyController;

Route::prefix('api/mailcow')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/status',              [MailcowProxyController::class, 'status']);
    Route::get('/mailboxes',           [MailcowProxyController::class, 'listMailboxes'])->middleware('permission:kota-sor');
    Route::get('/quota/{email}',       [MailcowProxyController::class, 'getQuota'])->middleware('permission:kota-sor');
    Route::post('/mailbox',            [MailcowProxyController::class, 'createMailbox'])->middleware('permission:mailbox-olustur');
    Route::delete('/mailbox/{email}',  [MailcowProxyController::class, 'deleteMailbox'])->middleware('permission:mailbox-sil');
});

// Basit Admin Giriş (Filament atlamak için)
Route::get('/admin-giris', function () {
    return view('admin-giris');
});

Route::post('/admin-giris', function () {
    $credentials = request()->only('email', 'password');
    if (Auth::attempt($credentials)) {
        request()->session()->regenerate();
        return redirect('/admin');
    }
    return back()->with('error', 'Geçersiz giriş bilgileri');
});

// Örnek Excel indir
Route::get('/ornek-ogrenci-excel', function () {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Ad');
    $sheet->setCellValue('B1', 'Soyad');
    $sheet->setCellValue('C1', 'Şifre');
    $sheet->setCellValue('A2', 'Ahmet');
    $sheet->setCellValue('B2', 'Yılmaz');
    $sheet->setCellValue('C2', 'Alfabe123!');
    $sheet->setCellValue('A3', 'Ayşe');
    $sheet->setCellValue('B3', 'Demir');
    $sheet->setCellValue('C3', 'BenimSifrem123');
    $sheet->setCellValue('A4', 'Mehmet');
    $sheet->setCellValue('B4', 'Kaya');
    $sheet->setCellValue('C4', 'Alfabe123!');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $tempPath = storage_path('app/tmp-imports/' . uniqid('ornek-', true) . '.xlsx');
    $writer->save($tempPath);

    return response()->download($tempPath, 'ornek-ogrenci-listesi.xlsx', [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ])->deleteFileAfterSend(true);
})->name('ornek-ogrenci-excel');
