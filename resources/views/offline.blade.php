<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Anda Sedang Offline – Jejak Hijau</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #F8F5F0;
            color: #374151;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 480px;
            padding: 40px 24px;
            margin: 20px;
            background-color: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 63, 47, 0.05);
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            background-color: rgba(220, 38, 38, 0.05);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: #DC2626;
        }
        h1 {
            color: #111827;
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 12px;
        }
        p {
            font-size: 15px;
            color: #6B7280;
            line-height: 1.6;
            margin: 0 0 28px;
        }
        .btn-retry {
            display: inline-block;
            background-color: #003F2F;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 700;
            padding: 12px 32px;
            border-radius: 12px;
            text-decoration: none;
            transition: background-color 0.15s ease;
            border: none;
            cursor: pointer;
        }
        .btn-retry:hover {
            background-color: #002e22;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-wrapper">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="1" y1="1" x2="23" y2="23"></line>
                <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.5"></path>
                <path d="M5 12.5a10.94 10.94 0 0 1 5.17-2.39"></path>
                <path d="M10.71 5.05A16 16 0 0 1 22.58 9"></path>
                <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"></path>
                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                <line x1="12" y1="20" x2="12.01" y2="20"></line>
            </svg>
        </div>
        <h1>Anda sedang offline.</h1>
        <p>Silakan periksa koneksi internet dan coba kembali.</p>
        <button onclick="window.location.reload();" class="btn-retry">Coba Lagi</button>
    </div>
</body>
</html>
