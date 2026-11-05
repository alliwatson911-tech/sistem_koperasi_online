<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 · Akses Terbatas | Koperasi Simpan Pinjam</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl border border-slate-200 shadow-xl p-8 text-center space-y-6">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center border border-rose-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-black tracking-wider uppercase bg-rose-100 text-rose-800">
                Kode Kesalahan: 403
            </span>
            <h1 class="text-xl font-extrabold text-slate-900">Akses Tidak Diberikan</h1>
            <p class="text-xs text-slate-500 leading-relaxed">
                Anda tidak memiliki hak otorisasi untuk membuka halaman, data, atau mengeksekusi tindakan ini sesuai prinsip Segregation of Duties koperasi.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 pt-2">
            <a href="javascript:history.back()" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center justify-center">
                Kembali
            </a>
            <a href="/dashboard" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-emerald-800 hover:bg-emerald-900 text-white transition flex items-center justify-center">
                Menuju Dashboard
            </a>
        </div>

        <div class="pt-4 border-t border-slate-100 text-[11px] text-slate-400">
            Sistem Informasi Manajemen Koperasi Simpan Pinjam
        </div>
    </div>
</body>
</html>
