<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline - E-Presensi SMKN 1 Majene</title>
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            text-align: center;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            color: #4f46e5;
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 20px;
            color: #4b5563;
            line-height: 1.5;
        }
        .icon {
            font-size: 64px;
            margin-bottom: 20px;
            color: #9ca3af;
        }
        .btn {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">📡</div>
        <h1>Anda Sedang Offline</h1>
        <p>Sepertinya koneksi internet Anda terputus. Silakan periksa koneksi jaringan Anda dan coba lagi.</p>
        <a href="javascript:window.location.reload(true)" class="btn">Coba Muat Ulang</a>
    </div>
</body>
</html>
