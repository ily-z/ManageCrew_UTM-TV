<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Layar QR Absensi</title>
    @vite(['resources/css/app.css', 'resources/js/qr-display.js'])
</head>
<body class="flex min-h-screen flex-col items-center justify-center bg-slate-900 text-white">
    <script>window.qrToken = {{ json_encode($qrToken) }};</script>
    <h1 id="title" class="mb-6 text-3xl font-bold">Scan untuk Absen</h1>
    <div class="rounded-3xl bg-white p-6 shadow-2xl">
        <canvas id="qr-canvas"></canvas>
    </div>
    <p id="status" class="mt-6 text-sm text-slate-300">Memuat…</p>
</body>
</html>
