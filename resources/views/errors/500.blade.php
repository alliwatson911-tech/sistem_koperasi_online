<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 · Terjadi Kendala Teknis | Koperasi Simpan Pinjam</title>
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
        <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-black tracking-wider uppercase bg-rose-100 text-rose-800">
                Kode Kesalahan: 500
            </span>
            <h1 class="text-xl font-extrabold text-slate-900">Terjadi Kendala Teknis</h1>
            <p class="text-xs text-slate-500 leading-relaxed">
                Sistem mendeteksi kendala pada pemrosesan permintaan Anda. Jika Anda adalah administrator, pastikan migrasi database dan cache telah diperbarui.
            </p>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-left text-xs space-y-1.5 font-mono text-slate-600">
            <div class="font-bold text-slate-700">Panduan Administrator:</div>
            <div>&bull; Jalankan <code class="text-emerald-700 font-bold">php artisan migrate --force</code></div>
            <div>&bull; Jalankan <code class="text-emerald-700 font-bold">php artisan optimize:clear</code></div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 pt-2">
            <button onclick="window.location.reload()" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                Muat Ulang Halaman
            </button>
            <a href="/" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-emerald-800 hover:bg-emerald-900 text-white transition flex items-center justify-center">
                Kembali ke Beranda
            </a>
        </div>

        <div class="pt-4 border-t border-slate-100 text-[11px] text-slate-400">
            Sistem Informasi Manajemen Koperasi Simpan Pinjam
        </div>
    </div>
</body>
</html>

