<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/favicon.png" />
    <title>Öğrenci Yaka Kartları</title>
    <style>
        :root {
            --bg-1: #66d9ff;
            --bg-2: #9f7aea;
            --card-bg: rgba(255, 255, 255, 0.9);
            --text: #1f2a44;
            --accent: #ff6f61;
            --line: #8aa1d6;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Nunito", "Segoe UI", sans-serif;
            color: var(--text);
            min-height: 100vh;
            background: linear-gradient(-45deg, var(--bg-1), var(--bg-2), #ffb347, #7ee081);
            background-size: 400% 400%;
            animation: playfulGradient 11s ease infinite;
        }

        .page {
            padding: 30px;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .print-button {
            border: none;
            border-radius: 999px;
            background: var(--accent);
            color: white;
            font-weight: 700;
            padding: 10px 18px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(255, 111, 97, 0.35);
        }

        .badges-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 20px;
        }

        .badge-card {
            background: var(--card-bg);
            backdrop-filter: blur(4px);
            border-radius: 18px;
            padding: 18px;
            border: 2px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 16px 30px rgba(31, 42, 68, 0.15);
            position: relative;
            overflow: hidden;
        }

        .badge-card::before {
            content: "";
            position: absolute;
            inset: -60% auto auto -20%;
            width: 120%;
            height: 120%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.35), transparent 70%);
            transform: rotate(20deg);
        }

        .student-name {
            margin: 0 0 14px;
            font-size: 22px;
            position: relative;
        }

        .qr {
            width: 150px;
            height: 150px;
            display: block;
            margin: 0 auto 14px;
            border-radius: 10px;
            padding: 8px;
            background: white;
        }

        .qr svg {
            width: 100%;
            height: 100%;
            shape-rendering: crispEdges;
        }

        .cut-line {
            border-top: 2px dashed var(--line);
            margin: 14px 0;
        }

        .mail,
        .password {
            margin: 0;
            text-align: center;
        }

        .password {
            margin-top: 8px;
            border: 2px dashed var(--line);
            border-radius: 10px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.5);
            letter-spacing: 1.8px;
        }

        @keyframes playfulGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @media print {
            body {
                background: white;
                animation: none;
            }

            .toolbar {
                display: none;
            }

            .badge-card {
                box-shadow: none;
                border: 1px solid #ccd5eb;
                break-inside: avoid;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                backdrop-filter: none !important;
            }

            .qr {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .qr svg {
                shape-rendering: crispEdges;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="toolbar">
            <h1>Öğrenci Yaka Kartları ({{ count($ogrencilerData) }})</h1>
            <button class="print-button" onclick="window.print()">Yazdır</button>
        </section>

        <section class="badges-grid">
            @foreach($ogrencilerData as $data)
                @php $ogrenci = $data["ogrenci"]; $mailboxPassword = $data["mailboxPassword"]; @endphp
                <article class="badge-card">
                    <h2 class="student-name">{{ $ogrenci->user->name }}</h2>
                    
                    <div class="qr">
                        @if($ogrenci->qr_svg)
                            {!! $ogrenci->qr_svg !!}
                        @else
                            <div style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; background: #f0f0f0; border-radius: 10px;">
                                QR Kod Yok
                            </div>
                        @endif
                    </div>
                    
                    <div class="cut-line"></div>
                    
                    <p class="mail"><strong>Mail:</strong> {{ $ogrenci->user->email }}</p>
                    
                    <p class="password"><strong>Şifre:</strong> {{ $mailboxPassword }}</p>
                </article>
            @endforeach
        </section>
    </main>
</body>
</html>