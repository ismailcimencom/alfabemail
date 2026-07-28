<div style="background:linear-gradient(135deg,#eef2ff,#e0e7ff);border-radius:16px;padding:20px 24px;margin-bottom:8px;border:1px solid #c7d2fe;">
  <div style="display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap;">

    {{-- Left: Steps --}}
    <div style="flex:2;min-width:200px;">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
        <span style="font-size:24px;">📋</span>
        <h3 style="margin:0;font-size:16px;font-weight:700;color:#1e1b4b;">Öğrenci Listesi</h3>
      </div>
      <p style="margin:0 0 12px;font-size:13px;color:#4338ca;line-height:1.5;">
        Bu sayfada öğrencilerini görüntüleyebilir, yeni öğrenci ekleyebilir ve toplu mail hesabı açabilirsin.
      </p>
      <div style="display:flex;flex-wrap:wrap;gap:8px;">
        <div style="background:#fff;border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:8px;border:1px solid #c7d2fe;flex:1;min-width:140px;">
          <span style="background:#6366f1;color:#fff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">1</span>
          <span style="font-size:13px;color:#1e1b4b;"><strong>Toplu Öğrenci Ekle</strong> butonuna tıkla</span>
        </div>
        <div style="background:#fff;border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:8px;border:1px solid #c7d2fe;flex:1;min-width:140px;">
          <span style="background:#6366f1;color:#fff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">2</span>
          <span style="font-size:13px;color:#1e1b4b;">Öğrenci listeni <strong>yapıştır</strong></span>
        </div>
        <div style="background:#fff;border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:8px;border:1px solid #c7d2fe;flex:1;min-width:140px;">
          <span style="background:#22c55e;color:#fff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">✓</span>
          <span style="font-size:13px;color:#1e1b4b;">Sistem <strong>otomatik mail açsın</strong></span>
        </div>
      </div>
    </div>

    {{-- Right: Animation --}}
    <div style="flex:1;min-width:160px;background:#1e1b4b;border-radius:12px;padding:14px 16px;position:relative;overflow:hidden;">
      <div style="font-size:11px;color:#a5b4fc;margin-bottom:8px;font-weight:600;">CANLI AKIŞ</div>
      <div class="ogrenci-akisi" style="display:flex;flex-direction:column;gap:6px;min-height:80px;">
        <div class="akas-item" style="display:flex;align-items:center;gap:6px;opacity:0;transform:translateX(-10px);font-size:13px;color:#e0e7ff;padding:4px 8px;background:rgba(255,255,255,0.06);border-radius:6px;">
          <span style="font-size:11px;color:#818cf8;">📧</span>
          <span>Ahmet Yılmaz</span>
          <span style="margin-left:auto;color:#22c55e;font-size:11px;">✅ Açıldı</span>
        </div>
        <div class="akas-item" style="display:flex;align-items:center;gap:6px;opacity:0;transform:translateX(-10px);font-size:13px;color:#e0e7ff;padding:4px 8px;background:rgba(255,255,255,0.06);border-radius:6px;">
          <span style="font-size:11px;color:#818cf8;">📧</span>
          <span>Ayşe Demir</span>
          <span style="margin-left:auto;color:#22c55e;font-size:11px;">✅ Açıldı</span>
        </div>
        <div class="akas-item" style="display:flex;align-items:center;gap:6px;opacity:0;transform:translateX(-10px);font-size:13px;color:#e0e7ff;padding:4px 8px;background:rgba(255,255,255,0.06);border-radius:6px;">
          <span style="font-size:11px;color:#818cf8;">📧</span>
          <span>Mehmet Kaya</span>
          <span style="margin-left:auto;color:#22c55e;font-size:11px;">✅ Açıldı</span>
        </div>
      </div>
    </div>

  </div>
</div>

<style>
@keyframes akasSlideIn {
  0%   { opacity:0; transform:translateX(-10px); }
  20%  { opacity:1; transform:translateX(0); }
  80%  { opacity:1; transform:translateX(0); }
  100% { opacity:0; transform:translateX(10px); }
}
.ogrenci-akisi .akas-item:nth-child(1) { animation: akasSlideIn 3s ease-in-out infinite; animation-delay:0s; }
.ogrenci-akisi .akas-item:nth-child(2) { animation: akasSlideIn 3s ease-in-out infinite; animation-delay:1s; }
.ogrenci-akisi .akas-item:nth-child(3) { animation: akasSlideIn 3s ease-in-out infinite; animation-delay:2s; }
</style>
