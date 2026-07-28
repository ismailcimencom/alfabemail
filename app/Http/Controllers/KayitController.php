<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class KayitController extends Controller
{
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'school' => 'required|string|max:255',
        ]);

        $email = $request->email;

        if (User::where('email', $email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu e-posta adresi zaten kayıtlı.',
            ], 422);
        }

        $pending = PendingUser::where('email', $email)->first();

        if ($pending && $pending->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Bu e-posta adresi ile ilgili işlem zaten tamamlanmış.',
            ], 422);
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $data = [
            'email' => $email,
            'phone' => $request->phone,
            'school' => $request->school,
            'verification_code' => $code,
            'verification_code_sent_at' => now(),
            'status' => 'pending',
        ];

        if ($pending) {
            $pending->update($data);
        } else {
            PendingUser::create($data);
        }

        try {
            Mail::to($email)->send(new VerificationCodeMail($code));
        } catch (\Exception $e) {
            logger()->warning('Doğrulama kodu e-postası gönderilemedi: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Doğrulama kodu e-posta adresinize gönderildi.',
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'code' => 'required|string|size:6',
        ]);

        $pending = PendingUser::where('email', $request->email)->where('status', 'pending')->first();

        if (!$pending) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz işlem.',
            ], 422);
        }

        if ($pending->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama kodunun süresi doldu. Lütfen yeni kod isteyin.',
            ], 422);
        }

        if ($pending->verification_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama kodu hatalı.',
            ], 422);
        }

        $pending->update([
            'email_verified_at' => now(),
            'verification_code' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'E-posta adresiniz doğrulandı!',
        ]);
    }

    public function complete(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:ogretmen,veli',
        ]);

        $pending = PendingUser::where('email', $request->email)
            ->whereNotNull('email_verified_at')
            ->where('status', 'pending')
            ->first();

        if (!$pending) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz işlem. Lütfen önce e-posta doğrulaması yapın.',
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $pending->phone,
            'password' => Hash::make($request->password),
            'is_active' => false,
        ]);
        $user->assignRole($request->role);

        if ($request->role === 'veli') {
            \App\Models\Veli::create(['user_id' => $user->id]);
        }

        Auth::login($user);

        $pending->update([
            'name' => $request->name,
            'password' => $request->password,
            'assigned_role' => $request->role,
            'status' => 'completed',
        ]);

        $panelPath = $request->role === 'ogretmen' ? '/ogretmen' : '/veli';

        return response()->json([
            'success' => true,
            'title' => 'Kaydın alındı!',
            'message' => 'Yönetici onayından sonra giriş yapabileceksin.',
            'redirect' => url($panelPath),
        ]);
    }
}
