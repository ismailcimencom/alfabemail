<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div style="display:flex;align-items:center;gap:8px;">
                <span>🏫 Yeni Kayıt Olan Yöneticiler</span>
                <span style="background:#e0ecff;color:#5e8df7;border-radius:999px;padding:2px 10px;font-size:12px;font-weight:600;">Toplam {{ $toplam }} okul</span>
            </div>
        </x-slot>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <thead>
                    <tr style="border-bottom:2px solid #e2e8f0;">
                        <th style="text-align:left;padding:8px 12px;color:#64748b;font-weight:600;">Okul</th>
                        <th style="text-align:left;padding:8px 12px;color:#64748b;font-weight:600;">Yönetici</th>
                        <th style="text-align:left;padding:8px 12px;color:#64748b;font-weight:600;">E-posta</th>
                        <th style="text-align:left;padding:8px 12px;color:#64748b;font-weight:600;">Kayıt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($okullar as $okul)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px 12px;font-weight:500;">{{ $okul['okul'] }}</td>
                            <td style="padding:10px 12px;">{{ $okul['yonetici'] }}</td>
                            <td style="padding:10px 12px;color:#5e8df7;">{{ $okul['email'] }}</td>
                            <td style="padding:10px 12px;color:#94a3b8;font-size:13px;">{{ $okul['tarih'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;padding:20px;color:#94a3b8;">Henüz kayıt yok</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:12px;text-align:right;">
            <a href="{{ url('/admin/users?role=yonetici') }}" style="color:#5e8df7;font-size:13px;text-decoration:none;font-weight:600;">Tüm yöneticileri gör →</a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
