<?php

namespace App\Http\Controllers;

use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

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
        $pending->phone = $request->phone;
        $pending->school = $request->school;
        $pending->verification_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $pending->verification_code_sent_at = now();
        $pending->save();

        try {
            Mail::to($email)->send(new VerificationCodeMail($pending->verification_code));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Doğrulama kodu gönderilemedi: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Doğrulama kodu e-posta adresinize gönderildi.']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $pending = PendingUser::where('status', 'pending')
            ->whereNotNull('verification_code')
            ->where('verification_code', $request->code)
            ->first();

        if (!$pending) {
            return response()->json(['success' => false, 'message' => 'Doğrulama kodu hatalı.'], 422);
        }

        if ($pending->isExpired()) {
            return response()->json(['success' => false, 'message' => 'Doğrulama kodunun süresi doldu. Lütfen tekrar gönderin.'], 422);
        }

        $pending->email_verified_at = now();
        $pending->verification_code = null;
        $pending->save();

        $password = \Illuminate\Support\Str::random(16);

        $user = User::create([
            'name' => $pending->name ?: explode('@', $pending->email)[0],
            'email' => $pending->email,
            'phone' => $pending->phone,
            'password' => Hash::make($password),
            'is_active' => false,
        ]);
        $user->assignRole('ogretmen');

        $pending->update([
            'password' => $password,
            'assigned_role' => 'ogretmen',
            'status' => 'completed',
        ]);

        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Kaydınız alındı! Yönetici onayından sonra panele erişebileceksiniz.',
        ]);
    }
}
