<?php

namespace App\Http\Controllers;

use App\Models\Sinif;
use App\Models\User;
use App\Models\Ogrenci;
use App\Models\PendingUser;
use App\Services\MailcowService;
use App\Services\StudentCreationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\VerificationCodeMail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OgretmenTopluMailController extends Controller
{
    public function showForm()
    {
        return view('ogretmen.toplu-mail-ac');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'school' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'students' => 'required|array|min:1',
            'students.*.ad' => 'required|string|max:100',
            'students.*.soyad' => 'required|string|max:100',
            'students.*.mail' => 'required|string|max:50',
        ]);

        $email = $request->email;

        if (User::where('email', $email)->exists()) {
            return response()->json(['success' => false, 'message' => 'Bu e-posta adresi zaten kayıtlı.'], 422);
        }

        $pending = PendingUser::updateOrCreate(
            ['email' => $email],
            ['status' => 'pending']
        );

        $pending->name = $request->name;
        $pending->verification_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $pending->verification_code_sent_at = now();
        $pending->save();

        try {
            Mail::to($email)->send(new VerificationCodeMail($pending->verification_code));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Doğrulama kodu gönderilemedi: ' . $e->getMessage()], 500);
        }

        session(['toplu_mail_ogretmen_email' => $email]);
        session(['toplu_mail_ogretmen_name' => $request->name]);
        session(['toplu_mail_ogretmen_phone' => $request->phone]);
        session(['toplu_mail_ogretmen_school' => $request->school]);
        session(['toplu_mail_students' => $request->students]);

        return response()->json(['success' => true, 'message' => 'Doğrulama kodu e-posta adresinize gönderildi.']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $email = session('toplu_mail_ogretmen_email');
        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Oturum süresi doldu. Lütfen tekrar başlayın.'], 422);
        }

        $pending = PendingUser::where('email', $email)->where('status', 'pending')->first();
        if (!$pending || !$pending->verification_code) {
            return response()->json(['success' => false, 'message' => 'Doğrulama kodu bulunamadı.'], 422);
        }

        if ($pending->isExpired()) {
            return response()->json(['success' => false, 'message' => 'Doğrulama kodunun süresi doldu. Lütfen tekrar gönderin.'], 422);
        }

        if ($pending->verification_code !== $request->code) {
            return response()->json(['success' => false, 'message' => 'Doğrulama kodu hatalı.'], 422);
        }

        $pending->email_verified_at = now();
        $pending->verification_code = null;
        $pending->save();

        $students = session('toplu_mail_students', []);
        $results = $this->createAccounts($email, $students);

        session(['toplu_mail_results' => $results]);

        return response()->json([
            'success' => true,
            'message' => 'Mailler başarıyla oluşturuldu!',
            'results' => $results,
            'redirect' => route('ogretmen.toplu-mail.sifre-belirle'),
        ]);
    }

    public function showPasswordForm()
    {
        $results = session('toplu_mail_results');
        if (!$results) {
            return redirect()->route('ogretmen.toplu-mail.form');
        }
        return view('ogretmen.sifre-belirle', compact('results'));
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $email = session('toplu_mail_ogretmen_email');
        $results = session('toplu_mail_results');

        if (!$email || !$results) {
            return response()->json(['success' => false, 'message' => 'Oturum süresi doldu.'], 422);
        }

        $teacher = User::where('email', $email)->first();
        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Öğretmen hesabı bulunamadı.'], 422);
        }

        $teacher->password = Hash::make($request->password);
        $teacher->save();

        session()->forget(['toplu_mail_ogretmen_email', 'toplu_mail_ogretmen_name', 'toplu_mail_ogretmen_phone', 'toplu_mail_ogretmen_school', 'toplu_mail_students', 'toplu_mail_results']);

        return response()->json([
            'success' => true,
            'message' => 'Şifreniz belirlendi! Giriş yapabilirsiniz.',
            'redirect' => url('/ogretmen/login'),
        ]);
    }

    private function createAccounts(string $teacherEmail, array $students): array
    {
        $schoolName = session('toplu_mail_ogretmen_school', 'Demo Okul');
        $phone = session('toplu_mail_ogretmen_phone');

        $sinifKodu = now()->format('Ymd');
        $sinif = Sinif::firstOrCreate(
            ['ad' => $sinifKodu]
        );

        $results = [
            'sinif' => ['id' => $sinif->id, 'ad' => $sinif->ad, 'okul' => $schoolName],
            'success' => [],
            'errors' => [],
        ];

        $mailcow = app(MailcowService::class);

        foreach ($students as $student) {
            $ad = trim($student['ad']);
            $soyad = trim($student['soyad']);
            $mailLocal = trim($student['mail']);
            $fullEmail = strtolower($mailLocal) . '@alfabe.co';

            try {
                if (User::where('email', $fullEmail)->exists() || Ogrenci::where('mailbox_local_part', strtolower($mailLocal))->exists()) {
                    $results['errors'][] = ['ad' => "$ad $soyad", 'mail' => $fullEmail, 'sebep' => 'Bu mail adresi zaten kullanılıyor.'];
                    continue;
                }

                $password = 'Alfabe123!';

                $mailbox = $mailcow->createStudentMailbox($ad, $soyad, $mailLocal, 100, $password);

                $user = User::create([
                    'name' => "$ad $soyad",
                    'email' => $fullEmail,
                    'password' => Hash::make($password),
                    'is_active' => true,
                ]);
                $user->assignRole('ogrenci');

                $qrToken = Str::random(32);
                $qrContent = json_encode([
                    'email' => $fullEmail,
                    'password' => $password,
                    'token' => $qrToken,
                ]);
                $qrSvg = QrCode::size(400)->generate($qrContent);

                Ogrenci::create([
                    'user_id' => $user->id,
                    'sinif_id' => $sinif->id,
                    'mailbox_local_part' => strtolower($mailLocal),
                    'mailbox_quota_mb' => 100,
                    'qr_token' => $qrContent,
                    'qr_svg' => (string) $qrSvg,
                ]);

                $results['success'][] = ['ad' => "$ad $soyad", 'mail' => $fullEmail, 'sifre' => $password];
            } catch (\Exception $e) {
                $results['errors'][] = ['ad' => "$ad $soyad", 'mail' => $fullEmail, 'sebep' => $e->getMessage()];
            }
        }

        $teacherPassword = Str::random(16);
        $teacherName = session('toplu_mail_ogretmen_name', explode('@', $teacherEmail)[0]);
        $teacher = User::create([
            'name' => $teacherName,
            'email' => $teacherEmail,
            'phone' => $phone,
            'password' => Hash::make($teacherPassword),
            'is_active' => true,
        ]);
        $teacher->assignRole('ogretmen');

        $sinif->ogretmenler()->syncWithoutDetaching([$teacher->id]);

        $results['ogretmen'] = ['email' => $teacherEmail, 'id' => $teacher->id];
        $results['sinif']['ogrenci_sayisi'] = count($results['success']);

        return $results;
    }
}
