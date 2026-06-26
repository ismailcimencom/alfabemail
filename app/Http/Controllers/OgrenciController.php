<?php

namespace App\Http\Controllers;

use App\Models\Odev;
use App\Models\Ogrenci;
use App\Models\User;
use App\Services\MailcowService;
use App\Services\ProfanityFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class OgrenciController extends Controller
{
    public function yakaKarti(Ogrenci $ogrenci)
    {
        $mailboxPassword = null;
        
        if ($ogrenci->qr_token) {
            $qrData = json_decode($ogrenci->qr_token, true);
            $mailboxPassword = $qrData['password'] ?? null;
        }
        
        if (!$mailboxPassword && $ogrenci->user) {
            $mailboxPassword = 'Şifre bilgisi bulunamadı';
        }
        
        return view('ogrenci.yaka-karti', compact('ogrenci', 'mailboxPassword'));
    }

    public function yakaKartiBulk(Request $request)
    {
        $ids = explode(',', $request->ids);
        $ogrenciler = Ogrenci::whereIn('id', $ids)->get();
        
        $ogrencilerData = $ogrenciler->map(function ($ogrenci) {
            $mailboxPassword = null;
            if ($ogrenci->qr_token) {
                $qrData = json_decode($ogrenci->qr_token, true);
                $mailboxPassword = $qrData['password'] ?? null;
            }
            return [
                'ogrenci' => $ogrenci,
                'mailboxPassword' => $mailboxPassword,
            ];
        });
        
        return view('ogrenci.yaka-karti-bulk', compact('ogrencilerData'));
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->with('ogrenci')
            ->first();

        if (!$user || !$user->hasRole('ogrenci') || !$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Bu e-posta adresiyle kayıtlı aktif bir öğrenci hesabı bulunamadı.',
            ]);
        }

        $dbPasswordValid = Hash::check($request->password, $user->password);
        $mailcowPasswordValid = $this->verifyMailcowPassword($request->email, $request->password);

        if (!$dbPasswordValid && !$mailcowPasswordValid) {
            throw ValidationException::withMessages([
                'password' => 'Şifre hatalı. Lütfen tekrar deneyin.',
            ]);
        }

        if ($mailcowPasswordValid && !$dbPasswordValid) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        Auth::login($user);

        session([
            'ogrenci_email' => $user->email,
            'ogrenci_password' => $request->password,
        ]);

        $veliEmails = [];
        if ($user->ogrenci) {
            if ($user->ogrenci->anne_email) {
                $veliEmails[] = ['name' => 'Anne', 'email' => $user->ogrenci->anne_email, 'icon' => '👩'];
            }
            if ($user->ogrenci->baba_email) {
                $veliEmails[] = ['name' => 'Baba', 'email' => $user->ogrenci->baba_email, 'icon' => '👨'];
            }
        }

        return response()->json([
            'message' => 'Giriş başarılı.', 
            'redirect' => route('ogrenci.dashboard'),
            'veli_emails' => $veliEmails
        ]);
    }

    private function verifyMailcowPassword(string $email, string $password): bool
    {
        try {
            $smtp = @fsockopen('mail.alfabe.co', 587, $errno, $errstr, 10);
            if (!$smtp) {
                return false;
            }

            fgets($smtp, 512);
            fwrite($smtp, "EHLO localhost\r\n");
            $this->readSmtpResponse($smtp);

            fwrite($smtp, "STARTTLS\r\n");
            $response = fgets($smtp, 512);
            if (substr($response, 0, 3) === '220') {
                stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                fwrite($smtp, "EHLO localhost\r\n");
                $this->readSmtpResponse($smtp);
            }

            $auth = base64_encode("\0" . $email . "\0" . $password);
            fwrite($smtp, "AUTH PLAIN $auth\r\n");
            $response = fgets($smtp, 512);
            fwrite($smtp, "QUIT\r\n");
            fclose($smtp);

            return substr($response, 0, 3) === '235';
        } catch (\Exception $e) {
            return false;
        }
    }

    public function qrLogin(Request $request): JsonResponse
    {
        $request->validate(['qr_token' => 'required|string']);

        try {
            $qrData = json_decode($request->qr_token, true);
            
            if (!$qrData || !isset($qrData['email']) || !isset($qrData['password'])) {
                return response()->json(['message' => 'Geçersiz karekod formatı.'], 400);
            }

            $user = User::where('email', $qrData['email'])
                ->with('ogrenci')
                ->first();

            if (!$user || !$user->hasRole('ogrenci') || !$user->is_active) {
                return response()->json(['message' => 'Geçersiz veya süresi dolmuş karekod.'], 401);
            }

            $dbPasswordValid = Hash::check($qrData['password'], $user->password);
            $mailcowPasswordValid = $this->verifyMailcowPassword($qrData['email'], $qrData['password']);

            if (!$dbPasswordValid && !$mailcowPasswordValid) {
                return response()->json(['message' => 'Karekod doğrulaması başarısız. Lütfen tekrar deneyin.'], 401);
            }

            if ($mailcowPasswordValid && !$dbPasswordValid) {
                $user->password = Hash::make($qrData['password']);
                $user->save();
            }

            Auth::login($user);

            session([
                'ogrenci_email' => $user->email,
                'ogrenci_password' => $qrData['password'],
            ]);

            return response()->json(['redirect' => route('ogrenci.dashboard')]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Karekod işlenirken hata oluştu.'], 500);
        }
    }

    public function dashboard()
    {
        if (!Auth::check() || !Auth::user()->hasRole('ogrenci')) {
            return redirect()->route('home');
        }

        $ogrenci = Auth::user()->ogrenci;
        return view('ogrenci.dashboard', compact('ogrenci'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }

    public function sendMail(Request $request): JsonResponse
    {
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        $user = Auth::user();
        
        if (!$user || !$user->hasRole('ogrenci')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 401);
        }

        $password = session('ogrenci_password');
        
        if (!$password) {
            return response()->json(['message' => 'Oturum süresi dolmuş. Lütfen tekrar giriş yapın.'], 401);
        }

        $profanity = app(ProfanityFilter::class);
        $checkText = $request->subject . ' ' . $request->body;
        if ($profanity->containsProfanity($checkText)) {
            return response()->json([
                'message' => 'Mesajınız uygunsuz ifadeler içeriyor. Lütfen içeriği düzenleyin.',
            ], 422);
        }

        try {
            $result = $this->sendSmtpMail(
                'mail.alfabe.co',
                587,
                $user->email,
                $password,
                $request->to,
                $user->email,
                $user->name ?? $user->email,
                $request->subject,
                $request->body
            );

            if ($result['success']) {
                if ($user->ogrenci) {
                    \App\Models\MailAktiviteLog::create([
                        'ogrenci_id' => $user->ogrenci->id,
                        'tip' => 'gonderilen',
                        'konu' => $request->subject,
                        'kime' => $request->to,
                        'kimden' => $user->email,
                        'tarih' => now(),
                    ]);
                }

                return response()->json([
                    'message' => 'Mail gönderildi.',
                    'success' => true
                ]);
            }

            return response()->json([
                'message' => 'Mail gönderilemedi: ' . $result['error'],
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Mail gönderilemedi: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function sendSmtpMail(string $host, int $port, string $username, string $password, string $to, string $fromEmail, string $fromName, string $subject, string $body): array
    {
        $smtp = fsockopen($host, $port, $errno, $errstr, 30);
        if (!$smtp) {
            return ['success' => false, 'error' => "Bağlantı hatası: $errstr ($errno)"];
        }

        $response = fgets($smtp, 512);
        if (substr($response, 0, 3) !== '220') {
            fclose($smtp);
            return ['success' => false, 'error' => 'SMTP handshake başarısız'];
        }

        fwrite($smtp, "EHLO localhost\r\n");
        $this->readSmtpResponse($smtp);

        fwrite($smtp, "STARTTLS\r\n");
        $response = fgets($smtp, 512);
        if (substr($response, 0, 3) === '220') {
            stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            fwrite($smtp, "EHLO localhost\r\n");
            $this->readSmtpResponse($smtp);
        }

        $auth = base64_encode("\0" . $username . "\0" . $password);
        fwrite($smtp, "AUTH PLAIN $auth\r\n");
        $response = fgets($smtp, 512);
        if (substr($response, 0, 3) !== '235') {
            fclose($smtp);
            return ['success' => false, 'error' => 'Kimlik doğrulama başarısız'];
        }

        fwrite($smtp, "MAIL FROM:<$fromEmail>\r\n");
        $this->readSmtpResponse($smtp);

        fwrite($smtp, "RCPT TO:<$to>\r\n");
        $this->readSmtpResponse($smtp);

        fwrite($smtp, "DATA\r\n");
        $response = fgets($smtp, 512);
        if (substr($response, 0, 3) !== '354') {
            fclose($smtp);
            return ['success' => false, 'error' => 'DATA başlatılamadı'];
        }

        $message = "From: $fromName <$fromEmail>\r\n";
        $message .= "To: $to\r\n";
        $message .= "Bcc: $fromEmail\r\n";
        $message .= "Subject: $subject\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Date: " . date('r') . "\r\n";
        $message .= "\r\n";
        $message .= $body;
        $message .= "\r\n.\r\n";

        fwrite($smtp, $message);
        $this->readSmtpResponse($smtp);

        fwrite($smtp, "QUIT\r\n");
        fclose($smtp);

        return ['success' => true];
    }

    private function readSmtpResponse($smtp): string
    {
        $response = '';
        while ($line = fgets($smtp, 512)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') break;
        }
        return $response;
    }

    public function getInbox(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user || !$user->hasRole('ogrenci')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 401);
        }

        $password = session('ogrenci_password');
        
        if (!$password) {
            return response()->json(['message' => 'Oturum süresi dolmuş.'], 401);
        }

        try {
            $hostname = '{mail.alfabe.co:993/imap/ssl/novalidate-cert}INBOX';
            
            imap_timeout(IMAP_OPENTIMEOUT, 10);
            
            $inbox = @imap_open($hostname, $user->email, $password, OP_READONLY);
            
            if (!$inbox) {
                $error = imap_last_error();
                \Log::error('IMAP Error: ' . ($error ?: 'Bilinmeyen hata') . ' for user: ' . $user->email);
                return response()->json([
                    'message' => 'IMAP bağlantısı kurulamadı. Lütfen mail şifrenizi kontrol edin.',
                    'debug' => $error,
                    'mails' => [],
                ], 500);
            }

            \Log::info('IMAP connected for: ' . $user->email);
            
            $totalMsgs = imap_num_msg($inbox);
            
            $mails = [];
            
            if ($totalMsgs > 0) {
                for ($i = 1; $i <= min($totalMsgs, 20); $i++) {
                    $header = @imap_headerinfo($inbox, $i);
                    if ($header) {
                        // Decode from address
                        $from = '';
                        if (isset($header->fromaddress)) {
                            $from = $this->decodeMimeHeader($header->fromaddress);
                        } elseif (isset($header->from[0])) {
                            $from = $header->from[0]->mailbox . '@' . $header->from[0]->host;
                        }
                        
                        // Decode subject
                        $subject = '(Konu yok)';
                        if (isset($header->subject)) {
                            $subject = $this->decodeMimeHeader($header->subject);
                        }
                        
                        $mails[] = [
                            'id' => $i,
                            'from' => $from,
                            'subject' => $subject,
                            'date' => isset($header->date) ? $header->date : '',
                            'seen' => isset($header->Unseen) && $header->Unseen === 'U' ? false : true,
                        ];
                    }
                }
            }
            
            usort($mails, function($a, $b) {
                return strtotime($b['date'] ?? '0') - strtotime($a['date'] ?? '0');
            });
            
            imap_close($inbox);

            return response()->json([
                'mails' => $mails,
                'success' => true
            ]);
        } catch (\Exception $e) {
            \Log::error('Inbox Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gelen kutusu okunamadı.',
                'debug' => $e->getMessage(),
                'mails' => [],
            ], 500);
        }
    }

    public function getSent(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user || !$user->hasRole('ogrenci')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 401);
        }

        $password = session('ogrenci_password');
        
        if (!$password) {
            return response()->json(['message' => 'Oturum süresi dolmuş.'], 401);
        }

        try {
            $hostname = '{mail.alfabe.co:993/imap/ssl/novalidate-cert}Sent';
            
            $inbox = @imap_open($hostname, $user->email, $password, OP_READONLY);
            
            if (!$inbox) {
                $hostname = '{mail.alfabe.co:993/imap/ssl/novalidate-cert}[Gönderilen Öğeler]';
                $inbox = @imap_open($hostname, $user->email, $password, OP_READONLY);
            }
            
            if (!$inbox) {
                $hostname = '{mail.alfabe.co:993/imap/ssl/novalidate-cert}[Sent Items]';
                $inbox = @imap_open($hostname, $user->email, $password, OP_READONLY);
            }
            
            if (!$inbox) {
                $hostname = '{mail.alfabe.co:993/imap/ssl/novalidate-cert}INBOX.Sent';
                $inbox = @imap_open($hostname, $user->email, $password, OP_READONLY);
            }
            
            if (!$inbox) {
                return response()->json(['mails' => [], 'success' => true, 'debug' => 'Sent klasörü bulunamadı']);
            }

            $totalMsgs = imap_num_msg($inbox);
            $mails = [];
            
            if ($totalMsgs > 0) {
                for ($i = 1; $i <= min($totalMsgs, 20); $i++) {
                    $header = @imap_headerinfo($inbox, $i);
                    if ($header) {
                        // Decode to address
                        $to = '';
                        if (isset($header->toaddress)) {
                            $to = $this->decodeMimeHeader($header->toaddress);
                        } elseif (isset($header->to[0])) {
                            $to = $header->to[0]->mailbox . '@' . $header->to[0]->host;
                        }
                        
                        // Decode subject
                        $subject = '(Konu yok)';
                        if (isset($header->subject)) {
                            $subject = $this->decodeMimeHeader($header->subject);
                        }
                        
                        $mails[] = [
                            'id' => $i,
                            'from' => $user->email,
                            'to' => $to,
                            'subject' => $subject,
                            'date' => isset($header->date) ? $header->date : '',
                        ];
                    }
                }
            }
            
            imap_close($inbox);

            return response()->json([
                'mails' => $mails,
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gönderilenler okunamadı: ' . $e->getMessage(),
                'mails' => [],
            ], 500);
        }
    }

    public function uploadAttachment(Request $request): JsonResponse
    {
        $user = User::where('email', session('ogrenci_email'))->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Oturum bulunamadı'], 401);
        }

        if (!$request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'Dosya seçilmedi'], 400);
        }

        $file = $request->file('file');
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'mp3', 'wav', 'ogg', 'webm', 'mpga'];
        $ext = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($ext, $allowed)) {
            return response()->json(['success' => false, 'message' => 'İzin verilmeyen dosya türü'], 400);
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return response()->json(['success' => false, 'message' => 'Dosya 5MB\'dan büyük'], 400);
        }

        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('attachments', $filename, 'public');
        
        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $path),
            'filename' => $filename
        ]);
    }

    public function getStats(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user || !$user->hasRole('ogrenci')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 401);
        }

        $ogrenci = $user->ogrenci;
        
        if (!$ogrenci) {
            return response()->json(['sent' => 0, 'received' => 0, 'replied' => 0]);
        }

        $sent = \App\Models\MailAktiviteLog::where('ogrenci_id', $ogrenci->id)
            ->where('tip', 'gonderilen')
            ->count();

        $received = \App\Models\MailAktiviteLog::where('ogrenci_id', $ogrenci->id)
            ->where('tip', 'okunan')
            ->count();

        return response()->json([
            'sent' => $sent,
            'received' => $received,
            'replied' => 0
        ]);
    }

    public function logRead(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user || !$user->hasRole('ogrenci')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 401);
        }

        $ogrenci = $user->ogrenci;
        
        if (!$ogrenci) {
            return response()->json(['success' => false]);
        }

        $tip = $request->input('tip', 'okunan');

        \App\Models\MailAktiviteLog::create([
            'ogrenci_id' => $ogrenci->id,
            'tip' => $tip,
            'konu' => 'Otomatik kayıt',
            'tarih' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function getQuota(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('ogrenci')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 401);
        }

        $ogrenci = $user->ogrenci;

        if (!$ogrenci || !$ogrenci->mailbox_local_part) {
            return response()->json(['quota_used' => 0, 'quota' => 100, 'percent_used' => 0]);
        }

        try {
            $mailService = new MailcowService();
            $email = $ogrenci->mailbox_local_part . '@' . config('mailcow.domain', 'alfabe.co');
            $info = $mailService->getMailboxQuota($email);

            return response()->json([
                'quota_used' => (int) ($info['quota_used'] ?? 0),
                'quota' => (int) ($info['quota'] ?? $ogrenci->mailbox_quota_mb ?? 100),
                'percent_used' => (float) ($info['percent_used'] ?? 0),
            ]);
        } catch (\Exception $e) {
            return response()->json(['quota_used' => 0, 'quota' => 100, 'percent_used' => 0]);
        }
    }

    public function getOdevler(): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('ogrenci')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 401);
        }

        $ogrenci = $user->ogrenci;
        if (!$ogrenci) {
            return response()->json(['odevler' => []]);
        }

        $odevler = $ogrenci->odevler()
            ->with('ogretmen')
            ->wherePivotNull('tamamlanma_tarihi')
            ->orderBy('teslim_tarihi')
            ->get()
            ->map(function ($odev) use ($ogrenci) {
                $bugun = now()->startOfDay();
                $teslim = $odev->teslim_tarihi ? \Carbon\Carbon::parse($odev->teslim_tarihi)->startOfDay() : null;
                $gecikti = $teslim && $teslim->lt($bugun);

                return [
                    'id' => $odev->id,
                    'baslik' => $odev->baslik,
                    'aciklama' => $odev->aciklama,
                    'teslim_tarihi' => $odev->teslim_tarihi?->format('d/m/Y'),
                    'ogretmen' => $odev->ogretmen?->name ?? 'Öğretmen',
                    'tamamlandi' => (bool) $odev->pivot->tamamlandi,
                    'gecikti' => $gecikti,
                    'created_at' => $odev->created_at->format('d/m/Y'),
                ];
            });

        $tamamlanan = $ogrenci->odevler()
            ->with('ogretmen')
            ->wherePivot('tamamlandi', true)
            ->orderByDesc('odev_ogrenci.tamamlanma_tarihi')
            ->get()
            ->map(function ($odev) {
                return [
                    'id' => $odev->id,
                    'baslik' => $odev->baslik,
                    'ogretmen' => $odev->ogretmen?->name ?? 'Öğretmen',
                    'tamamlanma_tarihi' => $odev->pivot->tamamlanma_tarihi
                        ? \Carbon\Carbon::parse($odev->pivot->tamamlanma_tarihi)->format('d/m/Y')
                        : null,
                ];
            });

        return response()->json([
            'bekleyen' => $odevler,
            'tamamlanan' => $tamamlanan,
        ]);
    }

    public function odevTamamla(Request $request): JsonResponse
    {
        $request->validate(['odev_id' => 'required|integer|exists:odevler,id']);

        $user = Auth::user();
        if (!$user || !$user->hasRole('ogrenci')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 401);
        }

        $ogrenci = $user->ogrenci;
        if (!$ogrenci) {
            return response()->json(['message' => 'Öğrenci bulunamadı.'], 404);
        }

        $odev = Odev::findOrFail($request->odev_id);
        $exists = $odev->ogrenciler()->where('ogrenci_id', $ogrenci->id)->exists();

        if (!$exists) {
            return response()->json(['message' => 'Bu ödev size ait değil.'], 403);
        }

        $odev->ogrenciler()->updateExistingPivot($ogrenci->id, [
            'tamamlandi' => true,
            'tamamlanma_tarihi' => now(),
        ]);

        $toplam = $odev->ogrenciler()->count();
        $tamamlanan = $odev->ogrenciler()->wherePivot('tamamlandi', true)->count();

        return response()->json([
            'message' => 'Ödev tamamlandı olarak işaretlendi! 🎉',
            'toplam' => $toplam,
            'tamamlanan' => $tamamlanan,
        ]);
    }

    public function fetchMail(int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('ogrenci')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 401);
        }

        $password = session('ogrenci_password');
        if (!$password) {
            return response()->json(['message' => 'Oturum süresi dolmuş.'], 401);
        }

        try {
            $hostname = '{mail.alfabe.co:993/imap/ssl/novalidate-cert}INBOX';
            $inbox = @imap_open($hostname, $user->email, $password, OP_READONLY);

            if (!$inbox) {
                return response()->json(['message' => 'IMAP bağlantı hatası.'], 500);
            }

            $totalMsgs = imap_num_msg($inbox);

            if ($id < 1 || $id > $totalMsgs) {
                imap_close($inbox);
                return response()->json(['message' => 'Mail bulunamadı.'], 404);
            }

            $header = @imap_headerinfo($inbox, $id);
            $structure = @imap_fetchstructure($inbox, $id);

            $body = '';
            $attachments = [];
            $bodyType = 'text';

            if ($structure) {
                $this->parseMimeParts($inbox, $id, $structure, '', $body, $attachments, $bodyType);
            }

            if (empty($body) && $structure) {
                $body = @imap_fetchbody($inbox, $id, 1);
                if ($structure->encoding === 3) {
                    $body = base64_decode($body);
                } elseif ($structure->encoding === 4) {
                    $body = quoted_printable_decode($body);
                }
            }

            $from = '';
            if (isset($header->fromaddress)) {
                $from = $this->decodeMimeHeader($header->fromaddress);
            } elseif (isset($header->from[0])) {
                $from = $header->from[0]->mailbox . '@' . $header->from[0]->host;
            }

            $subject = '(Konu yok)';
            if (isset($header->subject)) {
                $subject = $this->decodeMimeHeader($header->subject);
            }

            if ($bodyType === 'html' && !str_contains($body, '<html')) {
                $bodyType = 'text';
            }

            $body = $this->cleanMailBody($body);

            imap_close($inbox);

            return response()->json([
                'success' => true,
                'id' => $id,
                'from' => $from,
                'subject' => $subject,
                'date' => $header->date ?? '',
                'body' => $body,
                'body_type' => $bodyType,
                'attachments' => $attachments,
            ]);
        } catch (\Exception $e) {
            \Log::error('FetchMail Error: ' . $e->getMessage());
            return response()->json(['message' => 'Mail alınamadı.', 'error' => $e->getMessage()], 500);
        }
    }

    private function parseMimeParts($inbox, int $msgno, object $structure, string $partPrefix, string &$body, array &$attachments, string &$bodyType): void
    {
        if (isset($structure->parts) && count($structure->parts) > 0) {
            foreach ($structure->parts as $partNum => $part) {
                $prefix = $partPrefix ? $partPrefix . '.' . ($partNum + 1) : (string)($partNum + 1);

                if (isset($part->parts) && count($part->parts) > 0) {
                    $this->parseMimeParts($inbox, $msgno, $part, $prefix, $body, $attachments, $bodyType);
                    continue;
                }

                $filename = '';
                if (isset($part->dparameters) && is_array($part->dparameters)) {
                    foreach ($part->dparameters as $dp) {
                        if (strtolower($dp->attribute) === 'filename') {
                            $filename = $this->decodeMimeHeader($dp->value);
                        }
                    }
                }
                if (empty($filename) && isset($part->parameters) && is_array($part->parameters)) {
                    foreach ($part->parameters as $p) {
                        if (strtolower($p->attribute) === 'name') {
                            $filename = $this->decodeMimeHeader($p->value);
                        }
                    }
                }

                $isAttachment = !empty($filename);
                $subtype = strtolower($part->subtype ?? '');
                $type = $part->type ?? 0;

                // TEXT part (body)
                if (!$isAttachment && ($type === 0 || $type === 1)) {
                    $content = @imap_fetchbody($inbox, $msgno, $prefix);

                    if (($part->encoding ?? 0) === 3) {
                        $content = base64_decode($content);
                    } elseif (($part->encoding ?? 0) === 4) {
                        $content = quoted_printable_decode($content);
                    }

                    $charset = 'UTF-8';
                    if (isset($part->parameters) && is_array($part->parameters)) {
                        foreach ($part->parameters as $p) {
                            if (strtolower($p->attribute) === 'charset') {
                                $charset = $p->value;
                            }
                        }
                    }

                    if (strtoupper($charset) !== 'UTF-8') {
                        $content = mb_convert_encoding($content, 'UTF-8', $charset);
                    }

                    if ($subtype === 'html') {
                        $bodyType = 'html';
                        $body .= $content;
                    } elseif ($subtype === 'plain' && $bodyType !== 'html') {
                        $body .= $content;
                    }
                    continue;
                }

                // Attachment
                if ($isAttachment || ($type === 3 && $subtype === 'octet-stream')) {
                    $content = @imap_fetchbody($inbox, $msgno, $prefix);
                    if (($part->encoding ?? 0) === 3) {
                        $content = base64_decode($content);
                    } elseif (($part->encoding ?? 0) === 4) {
                        $content = quoted_printable_decode($content);
                    }

                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._\-]/', '_', $filename);
                    $filePath = 'attachments/' . $safeName;
                    Storage::disk('public')->put($filePath, $content);

                    $isZip = $ext === 'zip';
                    $zipExtracted = null;

                    if ($isZip) {
                        $localPath = storage_path('app/public/' . $filePath);
                        $zipExtracted = $this->extractZipFile($localPath);
                    }

                    $attachments[] = [
                        'filename' => $filename,
                        'size' => strlen($content),
                        'url' => asset('storage/' . $filePath),
                        'is_zip' => $isZip,
                        'zip_extracted' => $zipExtracted,
                    ];
                }
            }
        }
    }

    private function extractZipFile(string $zipPath): ?array
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return null;
        }

        $sessionId = bin2hex(random_bytes(8)) . '_' . time();
        $extractDir = 'zip-extracted/' . $sessionId . '/';
        $fullDir = storage_path('app/public/' . $extractDir);

        $zip->extractTo($fullDir);
        $zip->close();

        $files = [];
        $fileCount = 0;
        $totalSize = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $f) {
            $relativePath = ltrim(str_replace($fullDir, '', $f->getPathname()), '\\/');
            $isDir = $f->isDir();
            $size = $f->isFile() ? $f->getSize() : 0;

            $files[] = [
                'path' => $relativePath,
                'size' => $size,
                'isDir' => $isDir,
                'url' => $isDir ? null : asset('storage/' . $extractDir . str_replace('\\', '/', $relativePath)),
            ];

            if (!$isDir) {
                $fileCount++;
                $totalSize += $size;
            }
        }

        return [
            'session_id' => $sessionId,
            'files' => $files,
            'file_count' => $fileCount,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
        ];
    }

    private function cleanMailBody(string $body): string
    {
        $body = trim($body);
        if (empty($body)) {
            return '';
        }

        if (str_starts_with($body, '<')) {
            return $body;
        }

        return nl2br(e($body));
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    private function decodeMimeHeader(string $header): string
    {
        if (empty($header)) {
            return '';
        }
        
        $decoded = '';
        $parts = imap_mime_header_decode($header);
        
        foreach ($parts as $part) {
            $charset = $part->charset === 'default' ? 'ISO-8859-1' : $part->charset;
            if (strtoupper($charset) === 'UTF-8') {
                $decoded .= $part->text;
            } else {
                $decoded .= mb_convert_encoding($part->text, 'UTF-8', $charset);
            }
        }
        
        return $decoded;
    }
}
