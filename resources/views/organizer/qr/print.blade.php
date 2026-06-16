<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak QR Code - {{ $checkpoint->name }}</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8F5F0;
            color: #111827;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .print-container {
            background-color: #ffffff;
            border: 2px solid #D1D5DB;
            border-radius: 24px;
            padding: 40px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            text-align: center;
            box-sizing: border-box;
        }

        .event-name {
            font-size: 24px;
            font-weight: 900;
            color: #003F2F; /* Forest Green */
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .checkpoint-label {
            font-size: 12px;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin: 0 0 4px 0;
        }

        .checkpoint-name {
            font-size: 18px;
            font-weight: 700;
            color: #374151;
            margin: 0 0 32px 0;
        }

        .qr-wrapper {
            background-color: #ffffff;
            border: 1px solid #E5E7EB;
            border-radius: 16px;
            padding: 24px;
            display: inline-block;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            margin-bottom: 32px;
        }

        .qr-wrapper svg {
            display: block;
            width: 280px;
            height: 280px;
        }

        .instruction {
            font-size: 14px;
            font-weight: 600;
            color: #4B5563;
            margin: 0 0 8px 0;
        }

        .sub-instruction {
            font-size: 12px;
            color: #9CA3AF;
            margin: 0;
        }

        /* Print media styles */
        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
            }

            .print-container {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
                width: auto;
                margin: auto;
            }

            .qr-wrapper {
                border: none;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <h1 class="event-name">{{ $checkpoint->event->name }}</h1>
        <p class="checkpoint-label">Checkpoint #{{ $checkpoint->sequence }}</p>
        <h2 class="checkpoint-name">{{ $checkpoint->name }}</h2>

        <div class="qr-wrapper">
            {!! $qrCode !!}
        </div>

        <p class="instruction">Scan QR ini untuk mendapatkan poin checkpoint.</p>
        <p class="sub-instruction">GreenMile – Bersama Menjaga Lingkungan</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
