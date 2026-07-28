<div style="font-family: 'Nunito', 'Segoe UI', Tahoma, sans-serif; max-width: 1200px; margin: 0 auto;">

    <!-- CHART + AI ÖZET -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 20px;">
        <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                <span style="font-size: 20px;">📊</span>
                <h2 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Öğrenci Mail Etkinliği</h2>
            </div>
            <div style="height:250px"><canvas id="weeklyMailChart"></canvas></div>
        </div>
        @if($weekly_summary)
        <div style="background: linear-gradient(135deg, #7c3aed, #4f46e5); border-radius: 16px; padding: 20px; box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25); position: relative; overflow: hidden;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <span style="font-size: 24px;">🤖</span>
                <div>
                    <h2 style="font-size: 15px; font-weight: 800; color: white; margin: 0;">Haftalık AI Özet</h2>
                    <p style="font-size: 10px; color: #c4b5fd; margin: 2px 0 0;">Hafta {{ $weekly_summary['week'] }} · {{ $weekly_summary['period'] }}</p>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px; text-align: center;">
                    <div style="font-size: 22px; font-weight: 900; color: white;">{{ $combined_analiz['total_emails'] }}</div>
                    <div style="font-size: 10px; color: #c4b5fd;">Toplam Mail</div>
                </div>
                <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px; text-align: center;">
                    <div style="font-size: 22px; font-weight: 900; color: white;">{{ $combined_analiz['incoming'] }}</div>
                    <div style="font-size: 10px; color: #c4b5fd;">Gelen</div>
                </div>
                <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px; text-align: center;">
                    <div style="font-size: 22px; font-weight: 900; color: white;">{{ $combined_analiz['outgoing'] }}</div>
                    <div style="font-size: 10px; color: #c4b5fd;">Giden</div>
                </div>
                <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px; text-align: center;">
                    <div style="font-size: 22px; font-weight: 900; color: white;">{{ $combined_analiz['unique_contacts'] }}</div>
                    <div style="font-size: 10px; color: #c4b5fd;">Kişi Sayısı</div>
                </div>
            </div>
            @if($combined_analiz['busiest_hour'] || count($combined_analiz['top_contacts']) > 0)
            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; font-size: 10px; color: #c4b5fd;">
                @if($combined_analiz['busiest_hour'])
                    <span>⏰ En aktif: <strong style="color:white;">{{ $combined_analiz['busiest_hour'] }}</strong></span>
                @endif
                @if(count($combined_analiz['top_contacts']) > 0)
                    <span>📬 En çok: <strong style="color:white;">{{ implode(', ', array_slice($combined_analiz['top_contacts'], 0, 2)) }}</strong></span>
                @endif
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- STATS CARDS -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px;">
        <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <span style="font-size: 28px;">📧</span>
                <span style="font-size: 11px; background: #eff6ff; color: #2563eb; padding: 4px 10px; border-radius: 50px; font-weight: 700;">Haftalık</span>
            </div>
            <div style="font-size: 30px; font-weight: 900; color: #1e293b;">{{ $stats['weekly_mail_count'] }}</div>
            <div style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Toplam Mail</div>
        </div>
        <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <span style="font-size: 28px;">👥</span>
                <span style="font-size: 11px; background: #f0fdf4; color: #16a34a; padding: 4px 10px; border-radius: 50px; font-weight: 700;">İletişim</span>
            </div>
            <div style="font-size: 18px; font-weight: 900; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $stats['top_contact'] }}</div>
            <div style="font-size: 13px; color: #94a3b8; margin-top: 4px;">En Çok İletişim</div>
        </div>
        <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <span style="font-size: 28px;">🎒</span>
                <span style="font-size: 11px; background: #fefce8; color: #ca8a04; padding: 4px 10px; border-radius: 50px; font-weight: 700;">Öğrenci</span>
            </div>
            <div style="font-size: 30px; font-weight: 900; color: #1e293b;">{{ $stats['student_count'] }}</div>
            <div style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Kayıtlı Öğrenci</div>
        </div>
    </div>

    <!-- OGRENCI YÖNETIMI -->
    @if(count($students_data) > 0)
    <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #f0f0f0; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
            <span style="font-size: 20px;">⚙️</span>
            <h2 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Öğrenci Yönetimi</h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
            @foreach($students_data as $sd)
                @php $s = $sd['student']; @endphp
                <div style="border-radius: 16px; border: 2px solid {{ $sd['quota']['ok'] ? '#e5e7eb' : '#fca5a5' }}; padding: 20px; background: {{ $sd['quota']['ok'] ? 'white' : '#fef2f2' }};">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                        <div>
                            <div style="font-weight: 800; color: #1e293b;">{{ $s->user->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $s->user->email ?? '-' }}</div>
                        </div>
                        <span style="font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 50px; {{ $sd['quota']['ok'] ? 'background:#d1fae5; color:#059669;' : 'background:#fee2e2; color:#dc2626;' }}">
                            {{ $sd['quota']['ok'] ? '✅ İyi' : '⚠️ Dolu' }}
                        </span>
                    </div>
                    @if(!$sd['quota']['ok'])
                        <div style="background: #fee2e2; color: #dc2626; font-size: 12px; font-weight: 700; border-radius: 12px; padding: 10px; margin-bottom: 12px;">
                            ⚠️ Kota %{{ $sd['quota']['percent'] }} doldu!
                        </div>
                    @endif
                    <div style="background: #f1f5f9; border-radius: 10px; height: 10px; margin-bottom: 6px; overflow: hidden;">
                        <div style="height: 10px; border-radius: 10px; transition: width 0.5s; background: {{ $sd['quota']['percent'] > 80 ? '#ef4444' : ($sd['quota']['percent'] > 50 ? '#eab308' : '#10b981') }}; width: {{ min($sd['quota']['percent'], 100) }}%;"></div>
                    </div>
                    <div style="font-size: 12px; color: #94a3b8; display: flex; justify-content: space-between; margin-bottom: 16px;">
                        <span>Kota Kullanımı</span>
                        <span style="font-weight: 700;">%{{ $sd['quota']['percent'] }}</span>
                    </div>
                    <button onclick="openPasswordModal('{{ $s->id }}', '{{ $s->user->name }}')"
                            style="width: 100%; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; font-weight: 800; padding: 10px; border: none; border-radius: 12px; cursor: pointer; font-size: 14px; box-shadow: 0 4px 12px rgba(59,130,246,0.3);">
                        🔑 Şifre Sıfırla
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- MAIL OZETI + AKTIVITE -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
        <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                <span style="font-size: 20px;">📬</span>
                <h2 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Mail Özeti</h2>
            </div>
            <div style="max-height: 380px; overflow-y: auto;">
                @forelse($chartData['mail_summary'] as $mail)
                    <div style="display: flex; align-items: flex-start; gap: 12px; padding: 12px; border-radius: 12px; margin-bottom: 6px; {{ str_contains($mail['type'], 'Gelen') ? 'background:#f0fdf4;' : 'background:#eff6ff;' }}">
                        <span style="font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 50px; white-space: nowrap; {{ str_contains($mail['type'], 'Gelen') ? 'background:#bbf7d0; color:#166534;' : 'background:#bfdbfe; color:#1e40af;' }}">
                            {{ $mail['type'] }}
                        </span>
                        <div style="min-width: 0; flex: 1;">
                            <div style="font-size: 14px; font-weight: 700; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $mail['subject'] ?? '-' }}</div>
                            <div style="font-size: 12px; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $mail['from'] }}</div>
                            <div style="font-size: 11px; color: #cbd5e1;">{{ $mail['time'] }} · {{ $mail['student'] ?? '' }}</div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 40px 0; color: #94a3b8;">
                        <div style="font-size: 40px; margin-bottom: 8px;">📭</div>
                        <p style="font-size: 14px; margin: 0;">Henüz mail kaydı yok</p>
                    </div>
                @endforelse
            </div>
        </div>
        <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                <span style="font-size: 20px;">📅</span>
                <h2 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Son Aktiviteler</h2>
            </div>
            @php
                $allActivities = collect();
                foreach($students_data as $sd) {
                    foreach($sd['aktivite_loglari'] as $log) {
                        $allActivities->push($log);
                    }
                }
                $allActivities = $allActivities->sortByDesc('created_at')->take(10);
            @endphp
            <div style="max-height: 380px; overflow-y: auto;">
                @forelse($allActivities as $activity)
                    <div style="display: flex; align-items: flex-start; gap: 12px; padding: 12px; border-radius: 12px; margin-bottom: 6px; background: linear-gradient(135deg, #eff6ff, #eef2ff);">
                        <div style="width: 10px; height: 10px; margin-top: 6px; border-radius: 50%; background: #3b82f6; flex-shrink: 0;"></div>
                        <div style="min-width: 0;">
                            <div style="font-size: 14px; font-weight: 700; color: #1e293b;">{{ $activity['description'] ?? 'Aktivite' }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ \Carbon\Carbon::parse($activity['created_at'])->format('d.m.Y H:i') }}</div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 40px 0; color: #94a3b8;">
                        <div style="font-size: 40px; margin-bottom: 8px;">📭</div>
                        <p style="font-size: 14px; margin: 0;">Henüz aktivite kaydı yok</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- MESAJ + ILETISIM -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
        <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                <span style="font-size: 20px;">💬</span>
                <h2 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Öğretmene Mesaj</h2>
            </div>
            <form id="veliMesajForm">
                @csrf
                <input type="hidden" name="ogrenci_id" value="{{ $students->first()?->id }}">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="konu" placeholder="Konu" required
                           style="width:100%; padding:10px 14px; border:2px solid #e5e7eb; border-radius:12px; font-size:14px; outline:none;">
                    <select name="kime" required
                            style="width:100%; padding:10px 14px; border:2px solid #e5e7eb; border-radius:12px; font-size:14px; outline:none; background:white;">
                        <option value="">Öğretmen seçin</option>
                        @foreach($students as $student)
                            @if($student->sinif && $student->sinif->ogretmenler)
                                @foreach($student->sinif->ogretmenler as $ogretmen)
                                    <option value="{{ $ogretmen->id }}">{{ $ogretmen->name }} ({{ $student->user->name }})</option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                </div>
                <textarea name="mesaj" placeholder="Mesajınız..." required rows="3"
                          style="width:100%; padding:10px 14px; border:2px solid #e5e7eb; border-radius:12px; font-size:14px; outline:none; resize:none; margin-bottom:10px;"></textarea>
                <button type="submit"
                        style="width:100%; background:linear-gradient(135deg,#3b82f6,#2563eb); color:white; font-weight:800; padding:12px; border:none; border-radius:12px; cursor:pointer; font-size:15px; box-shadow:0 4px 12px rgba(59,130,246,0.3);">
                    📤 Mesajı Gönder
                </button>
            </form>
            <div id="veliMesajSuccess" style="display:none; color:#059669; font-size:14px; font-weight:700; text-align:center; background:#d1fae5; border-radius:12px; padding:12px; margin-top:12px;">✅ Mesajınız gönderildi!</div>
        </div>
        <div style="background: linear-gradient(135deg, #facc15, #f97316); border-radius: 16px; padding: 24px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 12px;">📧</div>
            <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0 0 8px;">Öğretmenle İletişim</h3>
            <p style="font-size: 14px; color: #4b5563; margin: 0 0 16px;">E-posta yoluyla doğrudan ulaşın</p>
            <button onclick="contactTeacher()"
                    style="background: white; color: #1e293b; font-weight: 800; padding: 12px 32px; border: none; border-radius: 50px; cursor: pointer; font-size: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                📧 E-posta Gönder
            </button>
        </div>
    </div>

    <!-- KARSILASTIRMA -->
    @if(count($students) > 1)
    <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
            <span style="font-size: 20px;">📊</span>
            <h2 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Öğrenci Karşılaştırma</h2>
        </div>
        <div style="overflow-x: auto;">
            <table style="width:100%; font-size:14px; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:2px solid #f1f5f9;">
                        <th style="text-align:left; padding:12px 16px; font-weight:800; color:#64748b;">Öğrenci</th>
                        <th style="text-align:center; padding:12px 16px; font-weight:800; color:#64748b;">Toplam</th>
                        <th style="text-align:center; padding:12px 16px; font-weight:800; color:#64748b;">Gelen</th>
                        <th style="text-align:center; padding:12px 16px; font-weight:800; color:#64748b;">Giden</th>
                        <th style="text-align:center; padding:12px 16px; font-weight:800; color:#64748b;">Kişi</th>
                        <th style="text-align:center; padding:12px 16px; font-weight:800; color:#64748b;">Kota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students_data as $sd)
                    @php $a = $sd['analiz']['insights']; @endphp
                    <tr style="border-bottom:1px solid #f8fafc;">
                        <td style="padding:12px 16px; font-weight:700; color:#1e293b;">{{ $sd['student']->user->name }}</td>
                        <td style="text-align:center; padding:12px 16px; font-weight:600;">{{ $a['total_emails'] }}</td>
                        <td style="text-align:center; padding:12px 16px; font-weight:600; color:#16a34a;">{{ $a['incoming'] }}</td>
                        <td style="text-align:center; padding:12px 16px; font-weight:600; color:#2563eb;">{{ $a['outgoing'] }}</td>
                        <td style="text-align:center; padding:12px 16px; font-weight:600;">{{ $a['unique_contacts'] }}</td>
                        <td style="text-align:center; padding:12px 16px;">
                            <span style="padding:4px 12px; border-radius:50px; font-size:12px; font-weight:700; {{ $sd['quota']['ok'] ? 'background:#d1fae5; color:#059669;' : 'background:#fee2e2; color:#dc2626;' }}">
                                %{{ $sd['quota']['percent'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<!-- SIFRE MODAL -->
<div id="passwordModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:9999; padding:16px;"
     onclick="if(event.target===this)closePasswordModal()">
    <div style="background:white; border-radius:20px; padding:24px; width:100%; max-width:420px; box-shadow:0 20px 60px rgba(0,0,0,0.2);" onclick="event.stopPropagation()">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px;">
            <span style="font-size:24px;">🔑</span>
            <h3 style="font-size:18px; font-weight:800; color:#1e293b; margin:0;">Şifre Sıfırla</h3>
        </div>
        <p style="font-size:14px; color:#94a3b8; margin-bottom:20px;" id="passwordModalStudent"></p>
        <form id="passwordResetForm">
            @csrf
            <input type="hidden" name="ogrenci_id" id="passwordOgrenciId">
            <div>
                <input type="password" name="yeni_sifre" placeholder="Yeni şifre" required minlength="6"
                       style="width:100%; padding:12px 14px; border:2px solid #e5e7eb; border-radius:12px; font-size:14px; outline:none; margin-bottom:10px;">
                <input type="password" name="yeni_sifre_tekrar" placeholder="Yeni şifre (tekrar)" required minlength="6"
                       style="width:100%; padding:12px 14px; border:2px solid #e5e7eb; border-radius:12px; font-size:14px; outline:none; margin-bottom:10px;">
                <input type="password" name="veli_sifre" placeholder="Kendi şifreniz (onay)" required
                       style="width:100%; padding:12px 14px; border:2px solid #e5e7eb; border-radius:12px; font-size:14px; outline:none; margin-bottom:10px;">
                <button type="submit"
                        style="width:100%; background:linear-gradient(135deg,#3b82f6,#2563eb); color:white; font-weight:800; padding:12px; border:none; border-radius:12px; cursor:pointer; font-size:15px; box-shadow:0 4px 12px rgba(59,130,246,0.3);">
                    🔄 Şifreyi Sıfırla
                </button>
            </div>
        </form>
        <div id="passwordResetSuccess" style="display:none; color:#059669; font-size:14px; font-weight:700; text-align:center; background:#d1fae5; border-radius:12px; padding:12px; margin-top:16px;">✅ Şifre başarıyla sıfırlandı!</div>
        <div id="passwordResetError" style="display:none; color:#dc2626; font-size:14px; font-weight:700; text-align:center; background:#fee2e2; border-radius:12px; padding:12px; margin-top:12px;"></div>
    </div>
  {{-- Buy Me a Coffee --}}
  <div style="text-align:center;margin-top:24px;padding:16px;background:#fefce8;border-radius:16px;border:1px solid #fde68a;">
    <p style="margin:0 0 8px;font-size:14px;color:#92400e;font-weight:500;">☕ Projemizin gelişmesi için yazılımcılarımıza bir kahve ısmarlamak ister misiniz?</p>
    <a href="https://www.buymeacoffee.com/proacademy" target="_blank" style="display:inline-block;background:#FFDD00;color:#000;text-decoration:none;padding:10px 28px;border-radius:999px;font-size:15px;font-weight:700;">☕ Kahve Ismarla</a>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('weeklyMailChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json(collect($chartData['weekly_traffic'])->pluck('day')),
            datasets: [{
                label: 'Mail',
                data: @json(collect($chartData['weekly_traffic'])->pluck('count')),
                backgroundColor: ['#a78bfa', '#818cf8', '#6366f1', '#8b5cf6', '#7c3aed', '#6d28d9', '#5b21b6'],
                borderRadius: 8,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
                x: { ticks: { font: { size: 11 } }, grid: { display: false } }
            }
        }
    });

    function contactTeacher() {
        var name = @json($students->first()?->user->name ?? 'Öğrenci');
        window.location.href = 'mailto:ogretmen@alfabe.co?subject=' + encodeURIComponent(name + ' hakkında bilgi talebi') + '&body=' + encodeURIComponent('Merhaba Öğretmenim,\n\nGrafikte bir değişiklik fark ettim ve değerlendirme rica ediyorum.\n\nTeşekkürler.');
    }

    document.getElementById('veliMesajForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        var btn = this.querySelector('button[type=submit]');
        btn.disabled = true; btn.textContent = 'Gönderiliyor...';
        try {
            var res = await fetch('/veli/mesaj-gonder', { method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' } });
            var data = await res.json();
            if (data.success) {
                document.getElementById('veliMesajSuccess').style.display = 'block';
                this.reset();
                setTimeout(function() { document.getElementById('veliMesajSuccess').style.display = 'none'; }, 3000);
            } else { alert('Mesaj gönderilemedi.'); }
        } catch(e) { alert('Bir hata oluştu.'); }
        finally { btn.disabled = false; btn.textContent = '📤 Mesajı Gönder'; }
    });

    function openPasswordModal(id, name) {
        document.getElementById('passwordModal').style.display = 'flex';
        document.getElementById('passwordOgrenciId').value = id;
        document.getElementById('passwordModalStudent').textContent = name + ' için yeni şifre belirleyin.';
    }
    function closePasswordModal() {
        document.getElementById('passwordModal').style.display = 'none';
    }
    document.getElementById('passwordResetForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        var btn = this.querySelector('button[type=submit]');
        btn.disabled = true; btn.textContent = 'Sıfırlanıyor...';
        document.getElementById('passwordResetError').style.display = 'none';
        var fd = new FormData(this);
        if (fd.get('yeni_sifre') !== fd.get('yeni_sifre_tekrar')) {
            document.getElementById('passwordResetError').textContent = 'Şifreler eşleşmiyor!';
            document.getElementById('passwordResetError').style.display = 'block';
            btn.disabled = false; btn.textContent = '🔄 Şifreyi Sıfırla'; return;
        }
        try {
            var res = await fetch('/veli/sifre-sifirla', { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } });
            var data = await res.json();
            if (data.success) {
                document.getElementById('passwordResetForm').style.display = 'none';
                document.getElementById('passwordResetSuccess').style.display = 'block';
                setTimeout(function() { closePasswordModal(); document.getElementById('passwordResetForm').style.display = 'block'; document.getElementById('passwordResetSuccess').style.display = 'none'; }, 2000);
            } else {
                document.getElementById('passwordResetError').textContent = data.message || 'Hata!';
                document.getElementById('passwordResetError').style.display = 'block';
            }
        } catch(e) {
            document.getElementById('passwordResetError').textContent = 'Bir hata oluştu.';
            document.getElementById('passwordResetError').style.display = 'block';
        }
        finally { btn.disabled = false; btn.textContent = '🔄 Şifreyi Sıfırla'; }
    });
</script>
@endpush
