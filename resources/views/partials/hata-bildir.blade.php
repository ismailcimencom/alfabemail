<div id="hataFab" onclick="document.getElementById('hataModalGlobal').style.display='flex';document.getElementById('hataSayfaGlobal').value=window.location.href;document.getElementById('hataTarayiciGlobal').value=navigator.userAgent;"
     style="position:fixed;bottom:24px;right:24px;width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;display:flex;align-items:center;justify-content:center;font-size:24px;cursor:pointer;box-shadow:0 6px 20px rgba(239,68,68,0.4);z-index:9998;border:none;transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Hata Bildir">
  ⚠️
</div>

<a href="https://yolharitasi.alfabe.co" target="_blank" rel="noopener"
   style="position:fixed;bottom:24px;left:24px;width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#6d28d9);color:white;display:flex;align-items:center;justify-content:center;font-size:22px;cursor:pointer;box-shadow:0 6px 20px rgba(139,92,246,0.4);z-index:9998;border:none;text-decoration:none;transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Fikir Paneli">
  💡
</a>

<div id="hataModalGlobal" style="display:none;position:fixed;z-index:9999;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:#fff;border-radius:20px;padding:30px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;">
    <button type="button" onclick="document.getElementById('hataModalGlobal').style.display='none'" style="position:absolute;top:12px;right:16px;border:none;background:none;font-size:28px;cursor:pointer;color:#888;">&times;</button>
    <h3 style="margin:0 0 6px;font-size:22px;color:#1a202c;">🐧 Hata Bildir</h3>
    <p style="margin:0 0 18px;color:#6586a7;font-size:14px;">Karşılaştığın sorunu bize anlat, ekran görüntüsü eklemeyi unutma!</p>
    <form id="hataFormGlobal" enctype="multipart/form-data">
      @csrf
      <div style="display:grid;gap:14px;">
        <input type="text" name="ad_soyad" placeholder="Adın Soyadın" required style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <input type="email" name="email" placeholder="E-posta adresin" required style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <input type="text" name="konu" placeholder="Konu" required style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <textarea name="aciklama" placeholder="Açıklama" required rows="4" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;resize:vertical;color:#1a202c;"></textarea>
        <div>
          <label style="display:block;font-size:13px;color:#6586a7;margin-bottom:4px;">Ekran Görüntüsü (isteğe bağlı)</label>
          <input type="file" name="ekran_goruntusu" accept="image/*" style="font-size:14px;">
        </div>
        <input type="hidden" name="sayfa" id="hataSayfaGlobal">
        <input type="hidden" name="tarayici" id="hataTarayiciGlobal">
        <button type="submit" style="background:#5e8df7;color:#fff;border:none;border-radius:999px;padding:14px;font-size:16px;font-weight:700;cursor:pointer;">Gönder</button>
      </div>
    </form>
    <div id="hataSuccessGlobal" style="display:none;text-align:center;padding:30px 0;">
      <div style="font-size:60px;margin-bottom:10px;">✅</div>
      <p style="font-size:18px;font-weight:700;margin:0;color:#1a202c;">Hata bildirimin alındı!</p>
      <p style="color:#6586a7;margin:6px 0 0;">Teşekkürler, en kısa sürede inceliyoruz.</p>
    </div>
  </div>
</div>

<script>
document.getElementById('hataFormGlobal')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = this.querySelector('button[type=submit]');
  btn.disabled = true; btn.textContent = 'Gönderiliyor...';
  try {
    const res = await fetch('{{ route("hata-bildir.store") }}', {
      method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (data.success) {
      this.style.display = 'none';
      document.getElementById('hataSuccessGlobal').style.display = 'block';
      setTimeout(() => { document.getElementById('hataModalGlobal').style.display = 'none'; this.style.display = 'block'; document.getElementById('hataSuccessGlobal').style.display = 'none'; this.reset(); }, 2500);
    } else { alert('Bir hata oluştu, lütfen tekrar dene.'); }
  } catch(e) { alert('Bir hata oluştu, lütfen tekrar dene.'); }
  finally { btn.disabled = false; btn.textContent = 'Gönder'; }
});
</script>
