<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>403 - Akses Ditolak - {{ setting('nama_warung', 'Soto Seger Boyolali Pak Antok') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#FFF3E0; color:#1F2937; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; -webkit-font-smoothing:antialiased; }
        .card { background:#fff; border-radius:20px; padding:48px 40px; max-width:420px; width:100%; text-align:center; box-shadow:0 8px 30px -5px rgba(0,0,0,0.08); border:1px solid #E5E0D8; }
        .icon { width:72px; height:72px; border-radius:50%; background:#FEE2E2; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
        h1 { font-size:48px; font-weight:800; color:#DC2626; margin-bottom:4px; }
        h2 { font-size:18px; font-weight:700; color:#1F2937; margin-bottom:8px; }
        p { font-size:14px; color:#6B7280; line-height:1.6; margin-bottom:24px; }
        .btn { display:inline-flex; align-items:center; gap:8px; background:#FF8C42; color:#fff; text-decoration:none; padding:12px 28px; border-radius:12px; font-weight:700; font-size:14px; transition:background 0.2s; }
        .btn:hover { background:#6D4C41; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0 0v2m0-2h2m-2 0H10m9.364-7.364A9 9 0 1112 3a9 9 0 017.364 4.636z"/></svg>
        </div>
        <h1>403</h1>
        <h2>Akses Ditolak</h2>
        <p>Maaf, kamu tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi admin jika kamu yakin ini sebuah kesalahan.</p>
        <a href="{{ route('dashboard') }}" class="btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
