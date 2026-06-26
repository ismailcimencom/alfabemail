<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/favicon.png" />
    <title>Alfabe Mail | Öğrenci Postası</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8b5cf6;
            --primary-light: #a78bfa;
            --secondary: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --pink: #ec4899;
            --bg-gradient: linear-gradient(135deg, #fef3c7 0%, #dbeafe 50%, #fce7f3 100%);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Nunito', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            background-attachment: fixed;
        }

        .kid-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(139, 92, 246, 0.15);
            border: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .kid-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary-light);
        }

        .tab-btn {
            padding: 12px 24px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .tab-btn.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
        }

        .tab-btn:not(.active) {
            background: white;
            color: #6b7280;
        }

        .tab-btn:not(.active):hover {
            background: #f3f4f6;
        }

        .mail-item {
            background: linear-gradient(135deg, #faf5ff 0%, #f0f9ff 100%);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid #e5e7eb;
        }

        .mail-item:hover {
            border-color: var(--primary);
            transform: scale(1.01);
        }

        .mail-item.unread {
            border-left: 4px solid var(--primary);
            background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
        }

        .emoji-btn {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .emoji-btn:hover {
            transform: scale(1.15);
        }

        .input-kid {
            border: 3px solid #e5e7eb;
            border-radius: 16px;
            padding: 14px 18px;
            font-size: 16px;
            transition: all 0.2s;
            width: 100%;
        }

        .input-kid:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
        }

        .btn-kid {
            padding: 14px 28px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-kid:hover {
            transform: scale(1.05);
        }

        .btn-purple {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
        }

        .btn-cyan {
            background: linear-gradient(135deg, var(--secondary) 0%, #22d3ee 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
        }

        .btn-pink {
            background: linear-gradient(135deg, var(--pink) 0%, #f472b6 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.4);
        }

        .btn-green {
            background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .floating { animation: float 3s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .tab-content.hidden { display: none; }

        /* Mail Modal - Çocuk Dostu */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.show { display: flex; }
        .modal-content {
            background: linear-gradient(180deg, #fef9c3 0%, #fff 100%);
            border-radius: 28px;
            padding: 0;
            max-width: 520px;
            width: 100%;
            max-height: 85vh;
            overflow: hidden;
            border: 4px solid #8b5cf6;
            box-shadow: 0 20px 60px rgba(139, 92, 246, 0.4);
        }
        .modal-header {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            padding: 20px 24px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-body-inner {
            padding: 24px;
        }
        .modal-close {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-close:hover {
            background: rgba(255,255,255,0.5);
            transform: scale(1.1);
        }
        .mail-meta-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f3f4f6;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            color: #6b7280;
        }
        .modal-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px dashed #e5e7eb;
        }
        .modal-action-btn {
            flex: 1;
            padding: 12px 20px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .modal-action-btn:hover {
            transform: scale(1.05);
        }
        .modal-content-preview {
            background: white;
            border-radius: 16px;
            padding: 16px;
            border: 2px solid #e5e7eb;
            max-height: 200px;
            overflow-y: auto;
        }
        .empty-mail-icon {
            font-size: 60px;
            display: block;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Rozeter */
        .badge-modal .modal-content {
            background: linear-gradient(180deg, #fef9c3 0%, #ecfccb 100%);
        }
        .badge-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 16px;
            margin-bottom: 10px;
            background: white;
            border: 2px solid #e5e7eb;
            transition: all 0.2s;
        }
        .badge-item.earned {
            border-color: #fbbf24;
            background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
        }
        .badge-item.locked {
            opacity: 0.5;
            filter: grayscale(1);
        }
        .badge-icon {
            font-size: 32px;
        }
        .badge-info h4 {
            font-weight: 700;
            color: #374151;
            font-size: 14px;
        }
        .badge-info p {
            font-size: 12px;
            color: #6b7280;
        }
        .badge-progress {
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            margin-top: 6px;
            overflow: hidden;
        }
        .badge-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #fbbf24, #f59e0b);
            border-radius: 3px;
            transition: width 0.5s;
        }

        /* Ödevler stili */
        .odev-card {
            transition: all 0.3s ease;
        }
        .odev-card:hover {
            transform: translateY(-2px);
        }

        /* ZIP Açıcı */
        .progress-bar {
            width: 100%; height: 6px; background: #eee;
            border-radius: 3px; overflow: hidden;
        }
        .progress-fill {
            height: 100%; background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%; animation: indeterminate 1.2s infinite;
        }
        @keyframes indeterminate {
            0%   { transform: translateX(-100%); width: 50%; }
            100% { transform: translateX(250%);  width: 50%; }
        }
        .file-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.65rem 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }
        .file-item:last-child { border-bottom: none; }
        .file-item .fi-icon { font-size: 1.1rem; flex-shrink: 0; }
        .file-item .fi-path { flex: 1; color: #333; word-break: break-all; }
        .file-item .fi-size { color: #999; font-size: 0.82rem; flex-shrink: 0; }
        .file-item a { color: #667eea; text-decoration: none; font-weight: 600; }
        .file-item a:hover { text-decoration: underline; }
    </style>
</head>
<body class="p-4 md:p-8 pb-20">
    
    <!-- Yeni Mail Butonu -->
    <button id="fabCompose" class="fixed bottom-6 right-6 w-16 h-16 bg-gradient-to-r from-pink-400 to-purple-500 rounded-full shadow-2xl flex items-center justify-center text-4xl border-4 border-white hover:scale-125 transition-transform animate-bounce" title="Yeni Mail Yaz">✏️</button>

    <!-- Header -->
    <div class="max-w-4xl mx-auto mb-6">
        <div class="kid-card p-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-4xl">🎒</span>
                <div>
                    <h1 class="text-2xl font-extrabold text-purple-600">Alfabe Mail</h1>
                    <p class="text-sm text-gray-500" id="userEmail">{{ Auth::user()->email }}</p>
                    <div id="quotaBar" class="mt-1 hidden">
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>💾</span>
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div id="quotaFill" class="h-full rounded-full transition-all duration-700" style="width:0%"></div>
                            </div>
                            <span id="quotaText">0 / 0 MB</span>
                        </div>
                    </div>
                </div>
            </div>
            <button id="badgeBtn" class="emoji-btn bg-yellow-100 hover:bg-yellow-200" title="Rozetlerim 🏆">🏆</button>
            <button id="statsBtn" class="emoji-btn bg-cyan-100 hover:bg-cyan-200" title="İstatistiklerim 📊">📊</button>
            <button id="logoutBtn" class="emoji-btn bg-red-100 hover:bg-red-200">🚪</button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="max-w-4xl mx-auto mb-6">
        <div class="kid-card p-2 flex gap-2 overflow-x-auto">
            <button class="tab-btn active" data-tab="inbox">
                📥 Gelen Kutusu
                <span id="inboxCount" class="ml-1 bg-white text-purple-600 px-2 py-0.5 rounded-full text-xs">0</span>
            </button>
            <button class="tab-btn" data-tab="sent">
                📤 Giden Kutusu
                <span id="sentCount" class="ml-1 bg-white text-cyan-600 px-2 py-0.5 rounded-full text-xs">0</span>
            </button>
            <button class="tab-btn" data-tab="compose">
                ✉️ Yeni Mail
            </button>
            <button class="tab-btn" data-tab="odevler" id="odevTabBtn">
                🦉 Ödevler
                <span id="odevCount" class="ml-1 bg-yellow-400 text-white px-2 py-0.5 rounded-full text-xs hidden">0</span>
            </button>
            <button class="tab-btn" data-tab="zip">
                🗜️ ZIP Açıcı
            </button>
        </div>
    </div>

    <!-- Inbox -->
    <div id="tab-inbox" class="tab-content max-w-4xl mx-auto">
        <div class="kid-card p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <span>📥</span> Gelen Kutun
            </h2>
            <div id="inboxList" class="space-y-2">
                <div class="text-center text-gray-400 py-8 pulse">📭 Henüz mail yok</div>
            </div>
        </div>
    </div>

    <!-- Sent -->
    <div id="tab-sent" class="tab-content hidden max-w-4xl mx-auto">
        <div class="kid-card p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <span>📤</span> Giden Kutun
            </h2>
            <div id="sentList" class="space-y-2">
                <div class="text-center text-gray-400 py-8 pulse">📭 Gönderilmiş mail yok</div>
            </div>
        </div>
    </div>

    <!-- Rozetler Modal -->
    <div id="badgeModal" class="modal-overlay">
        <div class="modal-content" style="background: linear-gradient(180deg, #fef9c3 0%, #ecfccb 100%);">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🏆</span>
                    <div>
                        <span class="text-xs opacity-80">Senin rozetlerin</span>
                        <h3 class="text-xl font-bold">Başarı Rozetleri</h3>
                    </div>
                </div>
                <button onclick="document.getElementById('badgeModal').classList.remove('show')" class="modal-close">✕</button>
            </div>
            <div class="modal-body-inner" id="badgeList"></div>
        </div>
    </div>

    <!-- İstatistikler Modal -->
    <div id="statsModal" class="modal-overlay">
        <div class="modal-content" style="background: linear-gradient(180deg, #e0f2fe 0%, #fff 100%);">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">📊</span>
                    <div>
                        <span class="text-xs opacity-80"> Senin başarın</span>
                        <h3 class="text-xl font-bold">Haftalık İstatistikler</h3>
                    </div>
                </div>
                <button onclick="document.getElementById('statsModal').classList.remove('show')" class="modal-close">✕</button>
            </div>
            <div class="modal-body-inner">
                <div id="penguinMsg" class="text-center p-4 bg-gradient-to-r from-purple-100 to-pink-100 rounded-2xl mb-4 text-purple-800 font-bold text-lg"></div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-purple-100 rounded-xl">
                        <div class="text-3xl mb-2">✉️</div>
                        <div class="text-2xl font-bold text-purple-600" id="statSent">0</div>
                        <div class="text-xs text-gray-600">Gönderilen</div>
                    </div>
                    <div class="text-center p-4 bg-cyan-100 rounded-xl">
                        <div class="text-3xl mb-2">📥</div>
                        <div class="text-2xl font-bold text-cyan-600" id="statReceived">0</div>
                        <div class="text-xs text-gray-600">Okunan</div>
                    </div>
                    <div class="text-center p-4 bg-pink-100 rounded-xl">
                        <div class="text-3xl mb-2">↩️</div>
                        <div class="text-2xl font-bold text-pink-600" id="statReplied">0</div>
                        <div class="text-xs text-gray-600">Yanıtlanan</div>
                    </div>
                </div>
                <div class="mt-4 p-4 bg-yellow-50 rounded-xl border-2 border-yellow-200">
                    <h4 class="font-bold text-yellow-700 mb-2">🐧 Penguen'in Notu</h4>
                    <p id="penguinTip" class="text-sm text-yellow-800">Mail yazmayı hiç bu kadar eğlenceli olmamıştı!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Penguen Maskot (sürekli görünür) -->
    <div id="penguin" class="fixed left-4 top-1/2 -translate-y-1/2 text-6xl cursor-pointer hover:scale-110 transition-transform floating" style="animation-delay: 0s;">🐧</div>

    <!-- Compose -->
    <div id="tab-compose" class="tab-content hidden max-w-4xl mx-auto">
        <div class="kid-card p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <span>✏️</span> Yeni Mail Yaz
            </h2>
            <form id="mailForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-600 mb-2">Kime 📧</label>
                    <div class="flex gap-2">
                        <input type="email" id="toInput" class="input-kid flex-1" placeholder="arkadas@alfabe.co" required>
                        <button type="button" id="friendsBtn" class="emoji-btn bg-pink-100 hover:bg-pink-200" title="Arkadaşlarını seç">👫</button>
                    </div>
                    <div id="friendsDropdown" class="hidden mt-2 p-3 bg-purple-50 rounded-xl border-2 border-purple-200">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-purple-700">Arkadaşlarını seç</span>
                            <button type="button" onclick="addNewFriend()" class="text-sm bg-purple-500 text-white px-2 py-1 rounded-lg">➕ Ekle</button>
                        </div>
                        <div id="friendsList" class="space-y-2 max-h-40 overflow-y-auto"></div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600 mb-2">Konu 📝</label>
                    <input type="text" id="subjectInput" class="input-kid" placeholder="Merhaba!" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600 mb-2">Mesaj 💬</label>
                    <textarea id="messageInput" class="input-kid" rows="5" placeholder="Mesajını buraya yaz..." required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600 mb-2">📎 Ek Dosya (İsteğe bağlı)</label>
                    <div class="flex items-center gap-3">
                        <label for="attachmentInput" class="emoji-btn bg-yellow-100 hover:bg-yellow-200 text-2xl" title="Dosya ekle">📎</label>
                        <input type="file" id="attachmentInput" class="hidden" accept="image/*,.pdf,.doc,.docx,.txt">
                        <span id="fileName" class="text-sm text-gray-500">Dosya seçilmedi</span>
                        <button type="button" id="clearFile" class="text-red-500 text-sm hidden" onclick="clearAttachment()">✕ Temizle</button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600 mb-2">🎤 Sesli Mesaj (İsteğe bağlı)</label>
                    <div class="flex items-center gap-3 flex-wrap">
                        <button type="button" id="voiceBtn" class="emoji-btn bg-green-100 hover:bg-green-200 text-2xl" title="Ses kaydet">🎤</button>
                        <span id="voiceStatus" class="text-sm text-gray-500">Kayıt yok</span>
                        <span id="voiceTimer" class="text-sm font-bold text-red-500 hidden">00:00</span>
                        <button type="button" id="voicePlayBtn" class="emoji-btn bg-blue-100 hover:bg-blue-200 text-xl hidden" title="Dinle">▶️</button>
                        <button type="button" id="voiceDeleteBtn" class="text-red-500 text-sm hidden" onclick="clearVoice()">✕ Sil</button>
                        <audio id="voicePreview" class="hidden" controls></audio>
                    </div>
                </div>
                <button type="submit" class="btn-kid btn-purple w-full">
                    ✈️ Gönder
                </button>
            </form>
            <div id="sendStatus" class="mt-4 p-4 rounded-xl text-center text-sm font-bold hidden"></div>
        </div>
    </div>

    <!-- Mail Modal - Çocuk Dostu -->
    <div id="mailModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🐧</span>
                    <div>
                        <span class="text-xs opacity-80">Mailini okuyorsun</span>
                        <h3 id="modalSubject" class="text-xl font-bold"></h3>
                    </div>
                </div>
                <button onclick="closeModal()" class="modal-close" title="Kapat">✕</button>
            </div>
            <div class="modal-body-inner">
                <div class="flex items-center gap-2 mb-4">
                    <span class="mail-meta-badge" id="modalFrom"></span>
                    <span class="mail-meta-badge" id="modalDate"></span>
                </div>
                <div class="modal-content-preview">
                    <span id="emptyMailIcon" class="empty-mail-icon" style="display:none;">📭</span>
                    <div id="modalBody" class="text-gray-700 whitespace-pre-wrap leading-relaxed"></div>
                </div>
                <div class="modal-actions">
                    <button class="modal-action-btn btn-purple" onclick="replyToMail()">
                        ↩️ Yanıtla
                    </button>
                    <button class="modal-action-btn bg-gray-100 text-gray-700" onclick="closeModal()">
                        👌 Tamam
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ödevler Tab -->
    <div id="tab-odevler" class="tab-content hidden max-w-4xl mx-auto">
        <div class="kid-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <span>🦉</span> Öğretmenin Gönderdikleri
                </h2>
                <button onclick="loadOdevler()" class="text-sm bg-purple-100 text-purple-700 px-3 py-1 rounded-full hover:bg-purple-200">
                    🔄 Yenile
                </button>
            </div>

            <!-- Takvim (basit) -->
            <div id="odevTakvim" class="mb-6 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl border-2 border-amber-200">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">📅</span>
                    <span class="font-bold text-amber-800">Bu Hafta</span>
                </div>
                <div id="haftalikOdevler" class="space-y-2"></div>
            </div>

            <!-- Bekleyen Ödevler -->
            <h3 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span>📋</span> Bekleyen Ödevler
            </h3>
            <div id="bekleyenOdevler" class="space-y-3">
                <div class="text-center text-gray-400 py-8 pulse">🦉 Ödevler yükleniyor...</div>
            </div>

            <!-- Tamamlanan Ödevler -->
            <h3 class="font-bold text-gray-700 mb-3 mt-6 flex items-center gap-2">
                <span>✅</span> Tamamlanan Ödevler
            </h3>
            <div id="tamamlananOdevler" class="space-y-2">
                <div class="text-center text-gray-400 py-4">Henüz tamamlanan ödev yok 🎯</div>
            </div>
        </div>
    </div>

    <!-- ZIP Açıcı Tab -->
    <div id="tab-zip" class="tab-content hidden max-w-4xl mx-auto">
        <div class="kid-card p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <span>🗜️</span> ZIP Açıcı
            </h2>
            <p class="text-sm text-gray-500 mb-4">ZIP dosyanı yükle, içindeki dosyaları gör ve indir!</p>

            <div id="zipDropZone" class="border-2 border-dashed border-purple-300 rounded-2xl p-8 text-center cursor-pointer hover:border-purple-500 hover:bg-purple-50 transition-all bg-purple-50/50 relative">
                <input type="file" id="zipFileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".zip">
                <div class="text-5xl mb-3">🗜️</div>
                <div class="font-bold text-purple-700">ZIP dosyasını buraya sürükle</div>
                <div class="text-sm text-gray-400 mt-1">veya tıklayarak seç — Maks. 50 MB</div>
                <div id="zipFileName" class="mt-3 hidden text-sm bg-purple-100 text-purple-700 px-4 py-1.5 rounded-full inline-block font-semibold"></div>
            </div>

            <div class="progress-bar mt-4" id="zipProgress" style="display:none"><div class="progress-fill"></div></div>

            <button id="zipExtractBtn" class="btn-kid btn-purple w-full mt-4" disabled>
                🗜️ ZIP Dosyasını Aç
            </button>

            <div id="zipStatus" class="mt-4 p-4 rounded-xl text-center text-sm font-bold hidden"></div>

            <div id="zipResult" class="mt-4 hidden">
                <div class="stats grid grid-cols-2 gap-3 mb-4">
                    <div class="stat-box bg-purple-50 rounded-xl p-3 text-center">
                        <div class="stat-val text-2xl font-bold text-purple-600" id="zipFileCount">0</div>
                        <div class="stat-lbl text-xs text-gray-500">Dosya Çıkarıldı</div>
                    </div>
                    <div class="stat-box bg-cyan-50 rounded-xl p-3 text-center">
                        <div class="stat-val text-2xl font-bold text-cyan-600" id="zipTotalSize">0 B</div>
                        <div class="stat-lbl text-xs text-gray-500">Toplam Boyut</div>
                    </div>
                </div>
                <div class="file-list">
                    <h3 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <span>📄</span> Çıkarılan Dosyalar
                    </h3>
                    <div id="zipFileList" class="space-y-1 max-h-80 overflow-y-auto"></div>
                </div>
                <button id="zipClearBtn" class="btn-kid w-full mt-4 bg-gray-100 text-gray-600 hover:bg-gray-200">
                    🗑️ Temizle
                </button>
            </div>
        </div>
    </div>

    <!-- Baykuş Maskot (öğretmenin maskotu) - Sürekli görünür, tıklanınca ödevlere gider -->
    <div id="owl" class="fixed bottom-8 left-8 text-6xl cursor-pointer hover:scale-110 transition-transform floating" title="Öğretmenin 🦉">🦉</div>
    <div class="fixed top-20 right-20 floating text-5xl opacity-20 pointer-events-none" style="animation-delay: 1s;">🐧</div>

    <script>
        const dom = {
            userEmail: document.getElementById('userEmail'),
            logoutBtn: document.getElementById('logoutBtn'),
            badgeBtn: document.getElementById('badgeBtn'),
            statsBtn: document.getElementById('statsBtn'),
            badgeModal: document.getElementById('badgeModal'),
            badgeList: document.getElementById('badgeList'),
            fabCompose: document.getElementById('fabCompose'),
            inboxList: document.getElementById('inboxList'),
            sentList: document.getElementById('sentList'),
            mailForm: document.getElementById('mailForm'),
            toInput: document.getElementById('toInput'),
            subjectInput: document.getElementById('subjectInput'),
            messageInput: document.getElementById('messageInput'),
            attachmentInput: document.getElementById('attachmentInput'),
            fileName: document.getElementById('fileName'),
            clearFile: document.getElementById('clearFile'),
            sendStatus: document.getElementById('sendStatus'),
            inboxCount: document.getElementById('inboxCount'),
            sentCount: document.getElementById('sentCount'),
            modal: document.getElementById('mailModal'),
            modalSubject: document.getElementById('modalSubject'),
            modalFrom: document.getElementById('modalFrom'),
            modalDate: document.getElementById('modalDate'),
            modalBody: document.getElementById('modalBody'),
        };

        let currentMails = { inbox: [], sent: [] };
        let isLoggedIn = false;

        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
                document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
            });
        });

        // Login and load mails
        async function init() {
            dom.inboxList.innerHTML = '<div class="text-center text-gray-400 py-8 pulse">🔄 Giriş yapılıyor...</div>';

            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const userEmail = '{{ Auth::check() ? Auth::user()->email : "" }}';
            const userPassword = '{{ Auth::check() ? session("ogrenci_password") : "" }}';

            if (!userEmail || !userPassword) {
                console.log('No auth, redirecting to login...');
                window.location.href = '/giris';
                return;
            }

            try {
                // Login as the authenticated student
                console.log('Attempting login for:', userEmail);
                const loginRes = await fetch('/ogrenci/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        email: userEmail,
                        password: userPassword
                    })
                });

                console.log('Login response status:', loginRes.status);
                const loginData = await loginRes.json();
                console.log('Login response:', loginData);

                if (loginRes.ok) {
                    isLoggedIn = true;
                    dom.userEmail.textContent = '✅ ' + userEmail;
                    console.log('Login successful, loading mails...');
                    
                    // Veli email'lerini arkadaş listesine ekle
                    if (loginData.veli_emails && loginData.veli_emails.length > 0) {
                        loginData.veli_emails.forEach(v => {
                            const exists = friends.some(f => f.email === v.email);
                            if (!exists) {
                                friends.push(v);
                            }
                        });
                        localStorage.setItem('ogrenci_friends', JSON.stringify(friends));
                    }

                    // Small delay to ensure session is set
                    setTimeout(() => loadMails(), 500);
                    setTimeout(() => loadOdevler(), 1000);
                } else {
                    console.error('Login failed:', loginData);
                    dom.inboxList.innerHTML = '<div class="text-center text-red-500 py-4">⚠️ Giriş başarısız: ' + (loginData.message || '') + '</div>';
                }
            } catch (err) {
                console.error('Init error:', err);
                dom.inboxList.innerHTML = '<div class="text-center text-red-500 py-4">⚠️ Bağlantı hatası: ' + err.message + '</div>';
            }
        }

        // Load mails from API
        async function loadMails() {
            if (!isLoggedIn) {
                dom.inboxList.innerHTML = '<div class="text-center text-gray-400 py-8 pulse">Önce giriş yapmalısın</div>';
                return;
            }

            dom.inboxList.innerHTML = '<div class="text-center text-gray-400 py-8 pulse">📥 Mailler yükleniyor...</div>';
            dom.sentList.innerHTML = '<div class="text-center text-gray-400 py-8 pulse">📤 Mailler yükleniyor...</div>';

            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                // Use Laravel API for real IMAP data
                const [inboxRes, sentRes] = await Promise.all([
                    fetch('/ogrenci/inbox'),
                    fetch('/ogrenci/sent')
                ]);

                const inboxData = await inboxRes.json();
                const sentData = await sentRes.json();

                console.log('Inbox response:', inboxData);
                console.log('Sent response:', sentData);
                console.log('Sent count:', sentData.mails?.length);

                if (inboxData.success && inboxData.mails && inboxData.mails.length > 0) {
                    currentMails.inbox = inboxData.mails;
                    dom.inboxList.innerHTML = '';
                } else {
                    currentMails.inbox = [];
                }

                // Handle sent - sadece localStorage'a güven (API çalışmıyor)
                const localSent = localStorage.getItem('ogrenci_sent_mails');
                if (localSent) {
                    try {
                        currentMails.sent = JSON.parse(localSent);
                        console.log('Loaded sent mails from localStorage:', currentMails.sent.length);
                    } catch(e) {}
                }
                
                if (currentMails.sent.length === 0) {
                    currentMails.sent = [];
                }

                dom.inboxCount.textContent = currentMails.inbox.length;
                dom.sentCount.textContent = currentMails.sent.length;

                renderMails();
            } catch (err) {
                console.error('Load mails error:', err);
                dom.inboxList.innerHTML = '<div class="text-center text-red-500 py-4">⚠️ Mail yüklenirken hata: ' + err.message + '</div>';
            }
        }

        function renderMails() {
            console.log('Rendering mails - inbox:', currentMails.inbox.length, 'sent:', currentMails.sent.length);
            
            // Inbox
            if (currentMails.inbox.length === 0) {
                dom.inboxList.innerHTML = '<div class="text-center text-gray-400 py-8">📭 Gelen kutusu boş</div>';
            } else {
                dom.inboxList.innerHTML = currentMails.inbox.map((mail, idx) => `
                    <div class="mail-item" data-type="inbox" data-index="${idx}">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📧</span>
                            <div class="flex-1">
                                <div class="font-bold text-purple-700">${mail.subject || '(Konu yok)'}</div>
                                <div class="text-sm text-gray-500">${mail.from || '-'}</div>
                            </div>
                            <div class="text-xs text-gray-400">${formatDate(mail.date)}</div>
                        </div>
                    </div>
                `).join('');
            }

            // Sent
            if (currentMails.sent.length === 0) {
                dom.sentList.innerHTML = '<div class="text-center text-gray-400 py-8">📭 Giden kutusu boş</div>';
            } else {
                dom.sentList.innerHTML = currentMails.sent.map((mail, idx) => `
                    <div class="mail-item" data-type="sent" data-index="${idx}">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">${mail.attachment || (mail.body && mail.body.includes('📎 Ek:')) ? '📎' : '✈️'}</span>
                            <div class="flex-1">
                                <div class="font-bold text-cyan-700">${mail.subject || '(Konu yok)'}</div>
                                <div class="text-sm text-gray-500">Kime: ${mail.to || '-'}</div>
                            </div>
                            <div class="text-xs text-gray-400">${formatDate(mail.date)}</div>
                        </div>
                    </div>
                `).join('');
            }

            // Add click listeners
            document.querySelectorAll('.mail-item').forEach(item => {
                item.addEventListener('click', () => {
                    const type = item.dataset.type;
                    const idx = parseInt(item.dataset.index);
                    const mail = type === 'inbox' ? currentMails.inbox[idx] : currentMails.sent[idx];

                    if (type === 'inbox') {
                        fetchMailContent(mail.id, mail.subject || '', mail.from || '', mail.date || '');
                    } else {
                        showMail(type, mail.subject || '', mail.to || '', mail.date || '', mail.body || '', []);
                    }
                });
            });

            async function fetchMailContent(id, subject, from, date) {
                try {
                    const res = await fetch('/ogrenci/mail/' + id + '/fetch');
                    const data = await res.json();

                    if (data.success) {
                        showMail('inbox', data.subject || subject, data.from || from, data.date || date, data.body || '', data.attachments || []);
                    } else {
                        showMail('inbox', subject, from, date, 'Mail içeriği yüklenemedi.', []);
                    }
                } catch (err) {
                    showMail('inbox', subject, from, date, 'Mail içeriği yüklenirken hata oluştu.', []);
                }
            }
        }

        let currentReplyTo = '';

        async function showMail(type, subject, from, date, body, attachments) {
            dom.modalSubject.textContent = subject || '(Konu yok)';
            dom.modalFrom.textContent = type === 'inbox' ? '📤 ' + from : '📥 ' + from;
            dom.modalDate.textContent = '📅 ' + formatDate(date);
            
            currentReplyTo = from;
            
            // 🏆 Mail okundu - received stat güncelle
            if (type === 'inbox') {
                userStats.received = (userStats.received || 0) + 1;
                localStorage.setItem('ogrenci_stats', JSON.stringify(userStats));
                
                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    await fetch('/ogrenci/log-read', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ type: 'okunan' })
                    });
                } catch(e) { console.log('Log read error', e); }
                
                badges.forEach(b => {
                    if (b.type === 'received' && userStats.received === b.req) {
                        alert('🎉 Tebrikler! Yeni rozet kazandın: ' + b.icon + ' ' + b.title);
                    }
                });
            }
            
            const emptyIcon = document.getElementById('emptyMailIcon');
            let attachmentHtml = '';
            let mailBody = body || '';

            // IMAP'ten gelen attachment'ları göster
            if (attachments && attachments.length > 0) {
                attachmentHtml = '<div class="mt-4 space-y-2">';
                attachments.forEach(att => {
                    const isZip = att.is_zip;
                    const sizeStr = formatSizeLocal(att.size || 0);
                    attachmentHtml += `<div class="p-3 rounded-xl border-2 ${isZip ? 'bg-purple-50 border-purple-200' : 'bg-yellow-50 border-yellow-200'} flex items-center gap-3">
                        <span class="text-xl">${isZip ? '🗜️' : '📎'}</span>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-sm truncate">${att.filename}</div>
                            <div class="text-xs text-gray-500">${sizeStr}</div>
                        </div>
                        <a href="${att.url}" target="_blank" class="px-3 py-1.5 bg-blue-600 text-white rounded-full text-xs font-bold hover:bg-blue-700 flex-shrink-0">⬇ İndir</a>
                        ${isZip ? `<button onclick="extractMailZip('${att.url}')" class="px-3 py-1.5 bg-purple-600 text-white rounded-full text-xs font-bold hover:bg-purple-700 flex-shrink-0">🗜️ Aç</button>` : ''}
                        ${att.zip_extracted ? `<span class="px-3 py-1.5 bg-green-600 text-white rounded-full text-xs font-bold flex-shrink-0">✅ Açıldı</span>` : ''}
                    </div>`;
                });
                attachmentHtml += '</div>';

                // ZIP içeriğini göster (otomatik açıldıysa)
                attachments.filter(a => a.zip_extracted).forEach(att => {
                    const zip = att.zip_extracted;
                    attachmentHtml += `<div class="mt-3 p-3 bg-green-50 rounded-xl border-2 border-green-200">
                        <div class="font-bold text-green-700 mb-2">🗜️ ${att.filename} içinden çıkanlar:</div>
                        ${zip.files.map(f => `<div class="flex items-center gap-2 text-sm py-1">
                            <span>${f.isDir ? '📁' : '📄'}</span>
                            <span class="flex-1">${f.path}</span>
                            ${!f.isDir && f.url ? `<a href="${f.url}" target="_blank" class="text-blue-600 underline">⬇</a>` : ''}
                        </div>`).join('')}
                        <div class="text-xs text-gray-500 mt-2">${zip.file_count} dosya • ${zip.total_size_formatted}</div>
                    </div>`;
                });
            }

            // Body içindeki 📎 Ek: linklerini de ayrıştır (eski format)
            if (!attachments || attachments.length === 0) {
                if (body && body.includes('📎 Ek:')) {
                    const parts = body.split('📎 Ek:');
                    mailBody = parts[0].trim();
                    const attachmentUrl = parts[1].trim();
                    const isZip = attachmentUrl.toLowerCase().endsWith('.zip');
                    attachmentHtml = `<div class="mt-4 p-3 bg-yellow-50 rounded-xl border-2 border-yellow-200">
                        <span class="font-bold text-yellow-700">📎 Ek Dosya:</span>
                        <a href="${attachmentUrl}" target="_blank" class="text-blue-600 underline hover:text-blue-800 ml-2">${isZip ? '🗜️ ZIP\'i İndir' : 'Dosyayı aç'} 👉</a>
                        ${isZip ? `<button onclick="extractMailZip('${attachmentUrl}')" class="ml-2 px-3 py-1 bg-purple-600 text-white rounded-full text-sm font-bold hover:bg-purple-700">🗜️ Aç</button>` : ''}
                    </div>`;
                } else if (body && body.match(/https?:\/\/[^\s]+\.zip/i)) {
                    const zipMatch = body.match(/https?:\/\/[^\s]+\.zip/i);
                    if (zipMatch) {
                        const zipUrl = zipMatch[0];
                        attachmentHtml = `<div class="mt-4 p-3 bg-purple-50 rounded-xl border-2 border-purple-200">
                            <span class="font-bold text-purple-700">🗜️ ZIP Dosyası Algılandı:</span>
                            <button onclick="extractMailZip('${zipUrl}')" class="ml-2 px-4 py-1.5 bg-purple-600 text-white rounded-full text-sm font-bold hover:bg-purple-700">🗜️ ZIP'i Aç ve İncele</button>
                        </div>`;
                    }
                }
            }
            
            if (!mailBody || mailBody.trim() === '' || mailBody === 'Mail içeriği yüklenemedi.') {
                emptyIcon.style.display = 'block';
                emptyIcon.textContent = type === 'inbox' ? '📭' : '✈️';
                dom.modalBody.innerHTML = '<p class="text-gray-400 italic">Bu mail henüz boş veya içeriği yüklenemedi.</p>' + attachmentHtml;
            } else {
                emptyIcon.style.display = 'none';
                // URL'leri tıklanabilir yap, ses kayıtlarını oynatıcı göster
                let bodyHtml = mailBody.replace(/\n/g, '<br>');
                bodyHtml = bodyHtml.replace(/(https?:\/\/[^\s<]+)/g, function(url) {
                    if (url.includes('sesli-mesaj') || url.endsWith('.webm')) {
                        return '<div class="mt-2 p-3 bg-green-50 rounded-xl border-2 border-green-200 flex items-center gap-3">🎤 <span class="flex-1 font-medium text-sm">Sesli Mesaj</span> <audio src="' + url + '" controls class="h-10"></audio> <a href="' + url + '" target="_blank" class="text-blue-600 underline text-sm">⬇</a></div>';
                    }
                    return '<a href="' + url + '" target="_blank" class="text-blue-600 underline">' + url + '</a>';
                });
                dom.modalBody.innerHTML = bodyHtml + attachmentHtml;
            }
            
            dom.modal.classList.add('show');
        }

        function replyToMail() {
            if (currentReplyTo && currentReplyTo.includes('@')) {
                closeModal();
                document.querySelector('[data-tab="compose"]').click();
                dom.toInput.value = currentReplyTo;
                dom.subjectInput.focus();
            } else {
                alert('Bu mail için yanıt gönderemezsin! 😊');
            }
        }

        function closeModal() {
            dom.modal.classList.remove('show');
        }

        function formatDate(dateStr) {
            if (!dateStr) return '';
            try {
                return new Date(dateStr).toLocaleDateString('tr-TR', { day: 'numeric', month: 'short' });
            } catch { return dateStr; }
        }

        function escapeHtml(text) {
            return String(text).replace(/[&<>"']/g, '');
        }

        // Send mail
        dom.mailForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const to = dom.toInput.value.trim();
            const subject = dom.subjectInput.value.trim();
            const body = dom.messageInput.value.trim();

            if (!to || !subject || !body) {
                showStatus('Lütfen tüm alanları doldur!', 'red');
                return;
            }

            // 🛡️ Güvenlik: Harici mail kontrolü
            if (to && !to.endsWith('@alfabe.co')) {
                if (!confirm('⚠️ Dikkat! Alfabe dışı bir adrese mail atıyorsun.\n\nBu mail veline gösterebilir. Devam etmek ister misin? 👨‍👩‍👧')) {
                    return;
                }
            }

            showStatus('📤 Gönderiliyor...', 'blue');

            let attachmentUrl = '';
            if (selectedFile) {
                showStatus('📎 Dosya yükleniyor...', 'blue');
                try {
                    const uploadData = await uploadFile(selectedFile);
                    attachmentUrl = uploadData.url || '';
                } catch (err) {
                    console.error('Dosya yükleme hatası:', err);
                }
            }

            let finalBody = body;
            if (attachmentUrl) {
                finalBody += '\n\n📎 Ek: ' + attachmentUrl;
            }
            if (voiceUploadedUrl) {
                finalBody += '\n\n🎤 Sesli Mesaj: ' + voiceUploadedUrl;
            }

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/ogrenci/send-mail', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ 
                        to, 
                        subject, 
                        body: finalBody
                    })
                });

                const data = await res.json();
                console.log('Send mail response:', data);

                if (res.ok) {
                    showStatus('✅ Mail gönderildi!' + (attachmentUrl ? ' 📎' : ''), 'green');
                    console.log('Adding to sent list, currentMails.sent length:', currentMails.sent.length);
                    
                    // Add to sent list locally
                    const newMail = {
                        to: dom.toInput.value,
                        subject: dom.subjectInput.value,
                        date: new Date().toISOString(),
                        body: finalBody,
                        attachment: attachmentUrl || null,
                        voice: voiceUploadedUrl || null
                    };
                    currentMails.sent.unshift(newMail);
                    console.log('After unshift, sent length:', currentMails.sent.length);
                    dom.sentCount.textContent = currentMails.sent.length;
                    localStorage.setItem('ogrenci_sent_mails', JSON.stringify(currentMails.sent));
                    
                    // Update sent list display
                    console.log('Calling renderMails...');
                    renderMails();
                    
                    // Switch to sent tab
                    document.querySelector('[data-tab="sent"]').click();
                    
                    // İstatistik güncelle
                    userStats.sent = (userStats.sent || 0) + 1;
                    localStorage.setItem('ogrenci_stats', JSON.stringify(userStats));
                    
                    // Rozet kontrolü
                    checkNewBadges();
                    
                    dom.mailForm.reset();
                    clearAttachment();
                    clearVoice();
                    document.querySelector('[data-tab="sent"]').click();
                } else {
                    showStatus('⚠️ ' + (data.message || 'Hata oluştu'), 'red');
                }
            } catch (err) {
                showStatus('⚠️ Bağlantı hatası', 'red');
            }

            setTimeout(() => dom.sendStatus.classList.add('hidden'), 3000);
        });

        function showStatus(msg, color) {
            dom.sendStatus.textContent = msg;
            dom.sendStatus.className = 'mt-4 p-4 rounded-xl text-center text-sm font-bold text-white bg-' + color + '-500';
            dom.sendStatus.classList.remove('hidden');
        }

        // Logout
        dom.logoutBtn.addEventListener('click', () => {
            fetch('/ogrenci/logout', { method: 'POST' });
            window.location.href = '/';
        });

        async function loadQuota() {
            try {
                const res = await fetch('/ogrenci/quota', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'include'
                });
                if (res.ok) {
                    const data = await res.json();
                    const pct = Math.min(data.percent_used, 100);
                    const usedMb = (data.quota_used / 1024).toFixed(1);
                    const totalMb = data.quota;
                    const bar = document.getElementById('quotaFill');
                    const txt = document.getElementById('quotaText');
                    const wrapper = document.getElementById('quotaBar');
                    wrapper.classList.remove('hidden');
                    bar.style.width = pct + '%';
                    bar.style.background = pct >= 80 ? '#ef4444' : pct >= 50 ? '#f59e0b' : '#10b981';
                    txt.textContent = usedMb + ' / ' + totalMb + ' MB';
                }
            } catch(e) { console.log('Quota load error', e); }
        }

        // Initialize - login and load mails
        init();
        setTimeout(() => loadQuota(), 1500);
        
        let userStats = { sent: 0, received: 0, replied: 0 };
        
        const badges = [
            // Gönderilen
            { type: 'sent', id: 'first_mail', icon: '✉️', title: 'İlk Mail', desc: 'İlk mailini gönder', req: 1 },
            { type: 'sent', id: 'mail_5', icon: '📨', title: 'Mail Kaşifi', desc: '5 mail gönder', req: 5 },
            { type: 'sent', id: 'mail_10', icon: '📩', title: 'Mail Ustası', desc: '10 mail gönder', req: 10 },
            { type: 'sent', id: 'mail_25', icon: '📮', title: 'Postacı', desc: '25 mail gönder', req: 25 },
            { type: 'sent', id: 'mail_50', icon: '🏅', title: 'Elçi', desc: '50 mail gönder', req: 50 },
            // Okunan
            { type: 'received', id: 'read_1', icon: '📖', title: 'İlk Okuma', desc: '1 mail oku', req: 1 },
            { type: 'received', id: 'read_10', icon: '📚', title: 'Kitap Kurdu', desc: '10 mail oku', req: 10 },
            { type: 'received', id: 'read_25', icon: '🎓', title: 'Bilge', desc: '25 mail oku', req: 25 },
            // Yanıtlanan
            { type: 'replied', id: 'reply_1', icon: '↩️', title: 'Nazik', desc: 'İlk yanıtı yaz', req: 1 },
            { type: 'replied', id: 'reply_5', icon: '💬', title: 'Sohbetçi', desc: '5 kez yanıtla', req: 5 },
            { type: 'replied', id: 'reply_10', icon: '🗣️', title: 'İletişimci', desc: '10 kez yanıtla', req: 10 },
        ];
        
        function renderBadges() {
            const sent = userStats.sent || 0;
            const list = badges.map(b => {
                const progress = Math.min(sent / b.req * 100, 100);
                const earned = sent >= b.req;
                return '<div style="display:flex;align-items:center;gap:12px;padding:12px;border-radius:16px;margin-bottom:10px;background:white;border:2px solid ' + (earned ? '#fbbf24' : '#e5e7eb') + ';">' +
                    '<span style="font-size:32px">' + b.icon + '</span>' +
                    '<div style="flex:1"><h4 style="font-weight:700;color:#374151;font-size:14px">' + b.title + (earned ? ' ✅' : '') + '</h4>' +
                    '<p style="font-size:12px;color:#6b7280">' + b.desc + '</p>' +
                    '<div style="height:6px;background:#e5e7eb;border-radius:3px;margin-top:6px;overflow:hidden"><div style="height:100%;background:linear-gradient(90deg, #fbbf24, #f59e0b);border-radius:3px;width:' + progress + '%"></div></div></div></div>';
            }).join('');
            dom.badgeList.innerHTML = list || '<p class="text-center text-gray-500">Henüz rozet yok</p>';
        }
        
        dom.badgeBtn.addEventListener('click', async function() {
            // Veritabanından istatistikleri al
            let dbStats = { sent: 0, received: 0 };
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/ogrenci/stats', {
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    credentials: 'include'
                });
                if (res.ok) dbStats = await res.json();
            } catch(e) { console.log('Badge stats error', e); }
            
            // Rozetleri render et - veritabanı istatistiklerini kullan
            const sent = dbStats.sent || 0;
            const received = dbStats.received || 0;
            const replied = dbStats.replied || 0;
            
            const list = badges.map(b => {
                let count = 0;
                if (b.type === 'sent') count = sent;
                else if (b.type === 'received') count = received;
                else if (b.type === 'replied') count = replied;
                
                const progress = Math.min(count / b.req * 100, 100);
                const earned = count >= b.req;
                return '<div style="display:flex;align-items:center;gap:12px;padding:12px;border-radius:16px;margin-bottom:10px;background:white;border:2px solid ' + (earned ? '#fbbf24' : '#e5e7eb') + ';">' +
                    '<span style="font-size:32px">' + b.icon + '</span>' +
                    '<div style="flex:1"><h4 style="font-weight:700;color:#374151;font-size:14px">' + b.title + (earned ? ' ✅' : '') + '</h4>' +
                    '<p style="font-size:12px;color:#6b7280">' + b.desc + ' (' + count + '/' + b.req + ')</p>' +
                    '<div style="height:6px;background:#e5e7eb;border-radius:3px;margin-top:6px;overflow:hidden"><div style="height:100%;background:linear-gradient(90deg, #fbbf24, #f59e0b);border-radius:3px;width:' + progress + '%"></div></div></div></div>';
            }).join('');
            dom.badgeList.innerHTML = list || '<p class="text-center text-gray-500">Henüz rozet yok</p>';
            
            dom.badgeModal.classList.add('show');
        });

        // 📊 İstatistikler butonu
        document.getElementById('statsBtn').addEventListener('click', async function() {
            // Veritabanından istatistikleri al
            let sent = 0, received = 0, replied = 0;
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/ogrenci/stats', {
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    credentials: 'include'
                });
                if (res.ok) {
                    const data = await res.json();
                    sent = data.sent || 0;
                    received = data.received || 0;
                    replied = data.replied || 0;
                }
            } catch(e) { console.log('Stats fetch error', e); }
            
            // LocalStorage'dan rozet durumunu al
            const savedStats = JSON.parse(localStorage.getItem('ogrenci_stats') || '{}');
            
            document.getElementById('statSent').textContent = sent;
            document.getElementById('statReceived').textContent = received;
            document.getElementById('statReplied').textContent = replied;
            
            // Penguen mesajı
            const msgs = [
                'Harika gidiyorsun! 🎉',
                'Her mail bir öğrenme! 📚',
                'Devam et, başarılısın! ⭐',
                'Mail ustası olabilirsin! 🏆',
                'Penguen senden çok etkilendi! 🐧',
                'Bir sonraki rozet senin! 🚀'
            ];
            document.getElementById('penguinMsg').textContent = '🐧 ' + msgs[sent % msgs.length];
            
            const tips = [
                'Öğretmenine soru sormayı dene!',
                'Arkadaşlarına selam gönder!',
                'Annene güzel bir mail yaz!',
                'Her gün mail atmayı dene!',
                'Rozetlerini arkadaşlarına göster!'
            ];
            document.getElementById('penguinTip').textContent = tips[Math.floor(Math.random() * tips.length)];
            
            document.getElementById('statsModal').classList.add('show');
        });

        // 🐧 Penguen tıklama
        document.getElementById('penguin').addEventListener('click', function() {
            const msgs = [
                'Kafa dert etme, penguen yanında! 🐧',
                'Mail atmayı unutma bugün! ✉️',
                'Sen harikasın! ⭐',
                'Hadi bir mail yazalım! 🎮',
                'Penguen sana başarı diliyor! 🎉'
            ];
            alert(msgs[Math.floor(Math.random() * msgs.length)]);
        });

        // 👫 Arkadaş Listesi
        let friends = JSON.parse(localStorage.getItem('ogrenci_friends') || '[]');
        
        function renderFriends() {
            const list = document.getElementById('friendsList');
            list.innerHTML = friends.map((f, i) => `
                <div class="flex items-center justify-between p-2 bg-white rounded-lg hover:bg-purple-100 cursor-pointer" onclick="selectFriend('${f.email}')">
                    <span>${f.icon} ${f.name}</span>
                    <button onclick="event.stopPropagation(); removeFriend(${i})" class="text-red-500">✕</button>
                </div>
            `).join('') || '<p class="text-gray-500 text-sm">Henüz arkadaş yok</p>';
        }
        
        window.selectFriend = function(email) {
            document.getElementById('toInput').value = email;
            document.getElementById('friendsDropdown').classList.add('hidden');
        };
        
        window.addNewFriend = function() {
            const name = prompt('Arkadaşının adı:');
            if (!name) return;
            const email = prompt('Arkadaşının e-posta adresi:');
            if (!email) return;
            const icon = prompt('İkon (örn: 👦, 👧, 🐶):', '👦') || '👦';
            friends.push({ name, email, icon });
            localStorage.setItem('ogrenci_friends', JSON.stringify(friends));
            renderFriends();
        };
        
        window.removeFriend = function(idx) {
            if (confirm('Bu arkadaşı silmek istiyor musun?')) {
                friends.splice(idx, 1);
                localStorage.setItem('ogrenci_friends', JSON.stringify(friends));
                renderFriends();
            }
        };
        
        document.getElementById('friendsBtn').addEventListener('click', function() {
            renderFriends();
            document.getElementById('friendsDropdown').classList.toggle('hidden');
        });

        // Yeni mail butonu
        document.getElementById('fabCompose').addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.querySelector('[data-tab="compose"]').classList.add('active');
            document.getElementById('tab-compose').classList.remove('hidden');
            document.getElementById('toInput').focus();
        });
        
        loadStats();

        // 🛡️ Güvenlik: Kullanım süresi hatırlatması (her dakika)
        let sessionTime = 0;
        setInterval(() => {
            sessionTime++;
            if (sessionTime === 15) {
                showStatus('⏰ 15 dakika oldu! Biraz dinlenmeye ne dersin? 🐧', 'yellow');
            } else if (sessionTime === 25) {
                showStatus('⏰ 25 dakika! Gözlerini biraz dinlendir 👀', 'orange');
            } else if (sessionTime === 30) {
                showStatus('⏰ 30 dakika oldu! Yazı tura atalım mı? 🎯', 'red');
            }
        }, 60000);

        function loadStats() {
            try {
                const saved = localStorage.getItem('ogrenci_stats');
                if (saved) {
                    userStats = JSON.parse(saved);
                }
            } catch(e) { console.log('Stats load error', e); }
        }

        let previousBadges = [];
        function checkNewBadges() {
            const current = getEarnedBadges().filter(b => b.earned).map(b => b.id);
            const newOne = current.find(id => !previousBadges.includes(id));
            if (newOne) {
                const badge = badges.find(b => b.id === newOne);
                alert('🎉 Tebrikler! Yeni rozet kazandın: ' + badge.icon + ' ' + badge.title);
                dom.newBadge.classList.remove('hidden');
            }
            previousBadges = current;
        }

        // 🦉 Baykuş maskot tıklama → ödevler sekmesine git
        document.getElementById('owl').addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.querySelector('[data-tab="odevler"]').classList.add('active');
            document.getElementById('tab-odevler').classList.remove('hidden');
            loadOdevler();
        });

        // 📋 Ödevleri yükle
        async function loadOdevler() {
            const bekleyenEl = document.getElementById('bekleyenOdevler');
            const tamamlananEl = document.getElementById('tamamlananOdevler');
            const haftalikEl = document.getElementById('haftalikOdevler');
            const odevCount = document.getElementById('odevCount');

            bekleyenEl.innerHTML = '<div class="text-center text-gray-400 py-8 pulse">🦉 Baykuş ödevleri getiriyor...</div>';

            try {
                const res = await fetch('/ogrenci/odevler', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'include'
                });
                if (!res.ok) throw new Error('Yükleme hatası');
                const data = await res.json();

                const bekleyen = data.bekleyen || [];
                const tamamlanan = data.tamamlanan || [];

                // Bekleyen ödev sayısı rozeti
                if (bekleyen.length > 0) {
                    odevCount.textContent = bekleyen.length;
                    odevCount.classList.remove('hidden');
                } else {
                    odevCount.classList.add('hidden');
                }

                // Bekleyen ödevler
                if (bekleyen.length === 0) {
                    bekleyenEl.innerHTML = '<div class="text-center text-gray-400 py-8">🎉 Tüm ödevler tamamlandı! Baykuş çok mutlu 🦉</div>';
                } else {
                    bekleyenEl.innerHTML = bekleyen.map(o => {
                        const gecikmeClass = o.gecikti ? 'border-red-300 bg-red-50' : 'border-purple-200 bg-purple-50';
                        const gecikmeIcon = o.gecikti ? '⚠️' : '📝';
                        return `
                            <div class="p-4 rounded-2xl border-2 ${gecikmeClass} flex items-start gap-3">
                                <span class="text-2xl">${gecikmeIcon}</span>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-800">${o.baslik}</div>
                                    <div class="text-sm text-gray-500 mt-1">${o.aciklama || ''}</div>
                                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                                        <span>👨‍🏫 ${o.ogretmen}</span>
                                        <span>📅 Teslim: ${o.teslim_tarihi || 'Belirsiz'}</span>
                                        ${o.gecikti ? '<span class="text-red-500 font-bold">⏰ GECİKTİ!</span>' : ''}
                                    </div>
                                </div>
                                <button onclick="odevTamamla(${o.id})" class="px-4 py-2 bg-green-500 text-white rounded-xl font-bold text-sm hover:bg-green-600 transition-all">
                                    ✅ Tamamla
                                </button>
                            </div>
                        `;
                    }).join('');
                }

                // Tamamlanan ödevler
                if (tamamlanan.length === 0) {
                    tamamlananEl.innerHTML = '<div class="text-center text-gray-400 py-4">Henüz tamamlanan ödev yok 🎯</div>';
                } else {
                    tamamlananEl.innerHTML = tamamlanan.map(o => `
                        <div class="flex items-center gap-3 p-3 bg-green-50 rounded-xl border border-green-200">
                            <span>✅</span>
                            <div class="flex-1">
                                <span class="font-bold text-green-700">${o.baslik}</span>
                                <span class="text-xs text-gray-500 ml-2">👨‍🏫 ${o.ogretmen}</span>
                            </div>
                            <span class="text-xs text-green-500">${o.tamamlanma_tarihi || ''}</span>
                        </div>
                    `).join('');
                }

                // Haftalık takvim
                const bugun = new Date();
                const haftaSonu = new Date(bugun);
                haftaSonu.setDate(bugun.getDate() + 7);
                
                const haftalik = bekleyen.filter(o => {
                    if (!o.teslim_tarihi) return false;
                    const parts = o.teslim_tarihi.split('/');
                    const tarih = new Date(parts[2], parts[1] - 1, parts[0]);
                    return tarih >= bugun && tarih <= haftaSonu;
                });

                if (haftalik.length === 0) {
                    haftalikEl.innerHTML = '<div class="text-center text-amber-700 text-sm">Bu hafta teslim edilecek ödev yok 🎉</div>';
                } else {
                    haftalikEl.innerHTML = haftalik.map(o => `
                        <div class="flex items-center gap-2 p-2 bg-white rounded-lg border border-amber-100">
                            <span>📌</span>
                            <span class="flex-1 font-medium text-sm">${o.baslik}</span>
                            <span class="text-xs text-amber-600 font-bold">${o.teslim_tarihi}</span>
                        </div>
                    `).join('');
                }

            } catch (err) {
                bekleyenEl.innerHTML = '<div class="text-center text-red-500 py-4">⚠️ Ödevler yüklenemedi. Baykuş üzgün 🥺</div>';
                console.error('Odev yukleme hatasi:', err);
            }
        }

        // ✅ Ödev tamamlama
        async function odevTamamla(odevId) {
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = '⏳';

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/ogrenci/odev-tamamla', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ odev_id: odevId })
                });

                const data = await res.json();
                if (res.ok) {
                    showStatus('🎉 ' + (data.message || 'Ödev tamamlandı!'), 'green');
                    loadOdevler(); // Yenile
                } else {
                    btn.disabled = false;
                    btn.textContent = '✅ Tamamla';
                    showStatus('⚠️ ' + (data.message || 'Hata oluştu'), 'red');
                }
            } catch (err) {
                btn.disabled = false;
                btn.textContent = '✅ Tamamla';
                showStatus('⚠️ Bağlantı hatası', 'red');
            }
        }

        // Ödevler sekmesine tıklayınca yükle
        document.querySelector('[data-tab="odevler"]').addEventListener('click', function() {
            setTimeout(() => loadOdevler(), 300);
        });

        // Ses kaydı
        let mediaRecorder = null;
        let audioChunks = [];
        let audioBlob = null;
        let audioUrl = null;
        let recordingTimer = null;
        let recordingSeconds = 0;
        let voiceUploadedUrl = null;

        const voiceBtn = document.getElementById('voiceBtn');
        const voiceStatus = document.getElementById('voiceStatus');
        const voiceTimer = document.getElementById('voiceTimer');
        const voicePlayBtn = document.getElementById('voicePlayBtn');
        const voiceDeleteBtn = document.getElementById('voiceDeleteBtn');
        const voicePreview = document.getElementById('voicePreview');

        voiceBtn.addEventListener('click', async function() {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
                audioChunks = [];

                mediaRecorder.ondataavailable = e => audioChunks.push(e.data);

                mediaRecorder.onstop = async () => {
                    stream.getTracks().forEach(t => t.stop());
                    clearInterval(recordingTimer);
                    voiceTimer.classList.add('hidden');

                    audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    audioUrl = URL.createObjectURL(audioBlob);

                    voicePreview.src = audioUrl;
                    voicePlayBtn.classList.remove('hidden');
                    voiceDeleteBtn.classList.remove('hidden');
                    voiceBtn.textContent = '🎤';
                    voiceBtn.style.background = '#d1fae5';
                    voiceStatus.textContent = '✅ Kaydedildi (' + formatDuration(recordingSeconds) + ')';

                    // Otomatik yükle
                    await uploadVoice(audioBlob);
                };

                mediaRecorder.start();
                recordingSeconds = 0;
                voiceTimer.textContent = '00:00';
                voiceTimer.classList.remove('hidden');
                voiceBtn.textContent = '⏹';
                voiceBtn.style.background = '#fecaca';
                voiceStatus.textContent = '🔴 Kaydediyor...';

                recordingTimer = setInterval(() => {
                    recordingSeconds++;
                    const m = String(Math.floor(recordingSeconds / 60)).padStart(2, '0');
                    const s = String(recordingSeconds % 60).padStart(2, '0');
                    voiceTimer.textContent = m + ':' + s;
                }, 1000);

            } catch (err) {
                alert('Mikrofona erişilemedi. Lütfen mikrofon iznini kontrol et 🐧');
                console.error('Mic error:', err);
            }
        });

        voicePlayBtn.addEventListener('click', function() {
            if (voicePreview.paused) {
                voicePreview.play();
                voicePlayBtn.textContent = '⏸';
            } else {
                voicePreview.pause();
                voicePlayBtn.textContent = '▶️';
            }
        });

        voicePreview.addEventListener('ended', () => {
            voicePlayBtn.textContent = '▶️';
        });

        async function uploadVoice(blob) {
            voiceStatus.textContent = '📤 Ses yükleniyor...';
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formData = new FormData();
            formData.append('file', blob, 'sesli-mesaj.webm');

            try {
                const res = await fetch('/ogrenci/upload-attachment', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    voiceUploadedUrl = data.url;
                    voiceStatus.textContent = '✅ Ses hazır (' + formatDuration(recordingSeconds) + ')';
                } else {
                    voiceStatus.textContent = '⚠️ Ses yüklenemedi';
                }
            } catch (err) {
                voiceStatus.textContent = '⚠️ Yükleme hatası';
            }
        }

        function clearVoice() {
            if (audioUrl) URL.revokeObjectURL(audioUrl);
            audioBlob = null;
            audioUrl = null;
            audioChunks = [];
            voiceUploadedUrl = null;
            recordingSeconds = 0;
            voiceStatus.textContent = 'Kayıt yok';
            voiceTimer.classList.add('hidden');
            voicePlayBtn.classList.add('hidden');
            voiceDeleteBtn.classList.add('hidden');
            voicePreview.src = '';
            voiceBtn.textContent = '🎤';
            voiceBtn.style.background = '';
        }

        function formatDuration(sec) {
            const m = String(Math.floor(sec / 60)).padStart(2, '0');
            const s = String(sec % 60).padStart(2, '0');
            return m + ':' + s;
        }

        // Dosya ekleme
        let selectedFile = null;
        dom.attachmentInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                selectedFile = this.files[0];
                const maxSize = 5 * 1024 * 1024;
                if (selectedFile.size > maxSize) {
                    alert('Dosya çok büyük! En fazla 5MB olabilir 🐧');
                    clearAttachment();
                    return;
                }
                dom.fileName.textContent = '📎 ' + selectedFile.name;
                dom.clearFile.classList.remove('hidden');
            }
        });

        function clearAttachment() {
            selectedFile = null;
            dom.attachmentInput.value = '';
            dom.fileName.textContent = 'Dosya seçilmedi';
            dom.clearFile.classList.add('hidden');
        }

        function uploadFile(file) {
            return new Promise((resolve, reject) => {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const formData = new FormData();
                formData.append('file', file);
                
                fetch('/ogrenci/upload-attachment', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) resolve(data);
                    else reject(new Error(data.message || 'Yükleme başarısız'));
                })
                .catch(reject);
            });
        }

        async function extractMailZip(zipUrl) {
            closeModal();
            document.querySelector('[data-tab="zip"]').click();
            
            const zipStatus = document.getElementById('zipStatus');
            const zipResult = document.getElementById('zipResult');
            const zipFileList = document.getElementById('zipFileList');
            const zipFileCount = document.getElementById('zipFileCount');
            const zipTotalSize = document.getElementById('zipTotalSize');

            zipResult.classList.add('hidden');
            zipStatus.className = 'mt-4 p-4 rounded-xl text-center text-sm font-bold bg-purple-100 text-purple-700';
            zipStatus.textContent = '🗜️ Mail\'deki ZIP açılıyor...';
            zipStatus.classList.remove('hidden');

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/ogrenci/zip/extract-url', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ url: zipUrl })
                });

                const data = await res.json();

                if (data.success) {
                    zipFileCount.textContent = data.file_count;
                    zipTotalSize.textContent = data.total_size_formatted;

                    zipFileList.innerHTML = data.files.map(f => `
                        <div class="file-item">
                            <span class="fi-icon">${f.isDir ? '📁' : '📄'}</span>
                            <span class="fi-path">${f.path}</span>
                            ${!f.isDir && f.url ? `<a href="${f.url}" target="_blank" class="text-sm">⬇ İndir</a>` : ''}
                            ${!f.isDir ? `<span class="fi-size">${formatSizeLocal(f.size)}</span>` : ''}
                        </div>
                    `).join('');

                    zipResult.classList.remove('hidden');
                    zipStatus.className = 'mt-4 p-4 rounded-xl text-center text-sm font-bold bg-green-100 text-green-700';
                    zipStatus.textContent = '✅ ' + data.file_count + ' dosya çıkarıldı!';
                } else {
                    zipStatus.className = 'mt-4 p-4 rounded-xl text-center text-sm font-bold bg-red-100 text-red-700';
                    zipStatus.textContent = '❌ ' + (data.message || 'ZIP açılamadı');
                }
            } catch (err) {
                zipStatus.className = 'mt-4 p-4 rounded-xl text-center text-sm font-bold bg-red-100 text-red-700';
                zipStatus.textContent = '❌ Hata: ' + err.message;
            }
        }

        function formatSizeLocal(bytes) {
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
            if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
            return bytes + ' B';
        }

        // ZIP Açıcı
        (function() {
            const zipInput = document.getElementById('zipFileInput');
            const zipDropZone = document.getElementById('zipDropZone');
            const zipFileName = document.getElementById('zipFileName');
            const zipExtractBtn = document.getElementById('zipExtractBtn');
            const zipProgress = document.getElementById('zipProgress');
            const zipStatus = document.getElementById('zipStatus');
            const zipResult = document.getElementById('zipResult');
            const zipFileList = document.getElementById('zipFileList');
            const zipFileCount = document.getElementById('zipFileCount');
            const zipTotalSize = document.getElementById('zipTotalSize');
            const zipClearBtn = document.getElementById('zipClearBtn');

            let selectedZip = null;

            function zipShowStatus(msg, type) {
                zipStatus.textContent = msg;
                zipStatus.className = 'mt-4 p-4 rounded-xl text-center text-sm font-bold ';
                if (type === 'success') zipStatus.className += 'bg-green-100 text-green-700';
                else if (type === 'error') zipStatus.className += 'bg-red-100 text-red-700';
                else if (type === 'loading') zipStatus.className += 'bg-purple-100 text-purple-700';
                else zipStatus.className += 'hidden';
                zipStatus.classList.remove('hidden');
            }

            zipInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    selectedZip = this.files[0];
                    zipFileName.textContent = '📦 ' + selectedZip.name;
                    zipFileName.style.display = 'inline-block';
                    zipExtractBtn.disabled = false;
                    zipResult.classList.add('hidden');
                    zipStatus.classList.add('hidden');
                }
            });

            ['dragover', 'dragenter'].forEach(ev => {
                zipDropZone.addEventListener(ev, e => { e.preventDefault(); zipDropZone.classList.add('border-purple-500', 'bg-purple-100'); });
            });
            ['dragleave', 'drop'].forEach(ev => {
                zipDropZone.addEventListener(ev, e => { e.preventDefault(); zipDropZone.classList.remove('border-purple-500', 'bg-purple-100'); });
            });
            zipDropZone.addEventListener('drop', function(e) {
                const files = e.dataTransfer.files;
                if (files.length) {
                    zipInput.files = files;
                    selectedZip = files[0];
                    zipFileName.textContent = '📦 ' + selectedZip.name;
                    zipFileName.style.display = 'inline-block';
                    zipExtractBtn.disabled = false;
                    zipResult.classList.add('hidden');
                    zipStatus.classList.add('hidden');
                }
            });

            zipExtractBtn.addEventListener('click', async function() {
                if (!selectedZip) return;

                zipExtractBtn.disabled = true;
                zipExtractBtn.textContent = '⏳ Açılıyor...';
                zipProgress.style.display = 'block';
                zipShowStatus('🗜️ ZIP dosyası açılıyor...', 'loading');

                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const formData = new FormData();
                formData.append('zipfile', selectedZip);

                try {
                    const res = await fetch('/ogrenci/zip/extract', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf },
                        body: formData
                    });

                    const data = await res.json();

                    if (data.success) {
                        zipFileCount.textContent = data.file_count;
                        zipTotalSize.textContent = data.total_size_formatted;

                        if (data.files.length === 0) {
                            zipFileList.innerHTML = '<div class="text-center text-gray-400 py-4">📂 ZIP boş</div>';
                        } else {
                            zipFileList.innerHTML = data.files.map(f => `
                                <div class="file-item">
                                    <span class="fi-icon">${f.isDir ? '📁' : '📄'}</span>
                                    <span class="fi-path">${f.path}</span>
                                    ${!f.isDir && f.url ? `<a href="${f.url}" target="_blank" class="text-sm">⬇ İndir</a>` : ''}
                                    ${!f.isDir ? `<span class="fi-size">${formatSize(f.size)}</span>` : ''}
                                </div>
                            `).join('');
                        }

                        zipResult.classList.remove('hidden');
                        zipShowStatus('✅ ' + data.file_count + ' dosya başarıyla çıkarıldı!', 'success');
                    } else {
                        zipShowStatus('❌ ' + (data.message || 'ZIP açılamadı'), 'error');
                    }
                } catch (err) {
                    zipShowStatus('❌ Bağlantı hatası: ' + err.message, 'error');
                }

                zipExtractBtn.disabled = false;
                zipExtractBtn.textContent = '🗜️ ZIP Dosyasını Aç';
                zipProgress.style.display = 'none';
            });

            zipClearBtn.addEventListener('click', function() {
                selectedZip = null;
                zipInput.value = '';
                zipFileName.style.display = 'none';
                zipFileName.textContent = '';
                zipExtractBtn.disabled = true;
                zipResult.classList.add('hidden');
                zipStatus.classList.add('hidden');
                zipFileList.innerHTML = '';
            });

            function formatSize(bytes) {
                if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
                if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
                return bytes + ' B';
            }
        })();
    </script>
</body>
</html>