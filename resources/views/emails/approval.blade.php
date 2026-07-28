<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paneliniz Aktifleştirildi</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" style="width:100%;max-width:600px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
        <tr>
            <td style="padding:40px 30px 20px;text-align:center;background:linear-gradient(135deg,#5e8df7,#7c5cfc);">
                <div style="font-size:48px;margin-bottom:10px;">🐧</div>
                <h1 style="color:#fff;margin:0;font-size:22px;">
                    @if($user->hasRole('ogretmen'))
                        Öğretmen Paneli Aktifleştirildi!
                    @else
                        Veli Paneli Aktifleştirildi!
                    @endif
                </h1>
            </td>
        </tr>
        <tr>
            <td style="padding:30px;">
                <p style="font-size:16px;color:#333;line-height:1.6;">Merhaba <strong>{{ $user->name }}</strong>,</p>
                <p style="font-size:14px;color:#555;line-height:1.6;">
                    @if($user->hasRole('ogretmen'))
                        Öğretmen paneliniz başarıyla onaylanmıştır. Artık sınıflarınızı oluşturabilir, öğrencilerinize mail açabilir ve ödev atayabilirsiniz.
                    @else
                        Veli paneliniz başarıyla onaylanmıştır. Artık çocuğunuzun mail gelişimini takip edebilir, aktivite raporlarını görüntüleyebilirsiniz.
                    @endif
                </p>
                <div style="text-align:center;margin:30px 0;">
                    <a href="{{ url($user->hasRole('ogretmen') ? '/ogretmen' : '/veli') }}"
                       style="display:inline-block;background:#5e8df7;color:#fff;text-decoration:none;padding:14px 36px;border-radius:999px;font-size:16px;font-weight:700;">
                        Paneli Aç
                    </a>
                </div>
                <p style="font-size:13px;color:#888;line-height:1.5;margin-top:20px;">
                    Hesabınızla ilgili herhangi bir sorunuz varsa bizimle iletişime geçebilirsiniz.<br>
                    — Alfabe Mail Ekibi
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
