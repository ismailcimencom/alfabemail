<?php

namespace App\Http\Controllers;

use App\Models\AktivasyonToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ActivationController extends Controller
{
    public function activate(string $token): RedirectResponse
    {
        $aktivasyon = AktivasyonToken::where('token', $token)
            ->whereNull('kullanildi_at')
            ->where('expires_at', '>', now())
            ->with('user')
            ->firstOrFail();

        $user = $aktivasyon->user;

        $aktivasyon->update(['kullanildi_at' => now()]);

        $user->update(['is_active' => true, 'email_verified_at' => now()]);

        Auth::login($user);

        return redirect()->intended(
            $user->hasRole('admin')
                ? route('filament.admin.pages.dashboard')
                : route('filament.portal.pages.dashboard')
        )->with('success', 'Hesabınız başarıyla aktive edildi.');
    }

    public function ogretmenSifreBelirle(string $token)
    {
        $aktivasyon = AktivasyonToken::where('token', $token)
            ->where('tip', 'ogretmen_sifre')
            ->whereNull('kullanildi_at')
            ->where('expires_at', '>', now())
            ->with('user')
            ->first();

        if (!$aktivasyon) {
            return redirect('/ogretmen/login')->with('error', 'Geçersiz veya süresi dolmuş bağlantı.');
        }

        return view('ogretmen.email-sifre-belirle', [
            'token' => $token,
            'email' => $aktivasyon->user->email,
        ]);
    }

    public function ogretmenSifreKaydet(Request $request, string $token)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $aktivasyon = AktivasyonToken::where('token', $token)
            ->where('tip', 'ogretmen_sifre')
            ->whereNull('kullanildi_at')
            ->where('expires_at', '>', now())
            ->with('user')
            ->first();

        if (!$aktivasyon) {
            return response()->json(['success' => false, 'message' => 'Geçersiz veya süresi dolmuş bağlantı.'], 422);
        }

        $user = $aktivasyon->user;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->is_active = true;
        $user->save();

        $aktivasyon->update(['kullanildi_at' => now()]);

        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Şifreniz belirlendi! Yönlendiriliyorsunuz...',
            'redirect' => url('/ogretmen'),
        ]);
    }
}
