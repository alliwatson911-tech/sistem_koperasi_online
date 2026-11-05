<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Manajemen Koperasi Simpan Pinjam online KSP Sahabat Sejahtera. Layanan simpanan anggota, simulasi pinjaman, akad digital, dan akuntansi transparan.">
    <meta name="theme-color" content="#047857">
    <title>@yield('title', 'Beranda') - KSP Sahabat Sejahtera</title>

    {{-- Favicon Koperasi --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23047857'><path d='M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5'/></svg>">

    {{-- Google Font Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ksp: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    {{-- Design System Tokens & Base Component Styles --}}
    <style>
        :root {
            --primary: #047857;
            --primary-dark: #065f46;
            --primary-light: #ecfdf5;
            --secondary: #0284c7;
            --dark: #0f172a;
            --gray: #64748b;
            --light: #f8fafc;
            --border: #e2e8f0;
            --card: #ffffff;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Navigasi */
        .navbar-brand-badge {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            box-shadow: 0 4px 10px rgba(4, 120, 87, 0.25);
        }

        /* Komponen Card Desain Sistem */
        .ksp-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease;
        }
        .card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.01em;
        }

        /* Stat Card */
        .stat-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .stat-label {
            font-size: 0.725rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }
        .stat-meta {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Grids */
        .grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.25rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1.25rem; }

        @media (max-width: 1024px) {
            .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
        }

        /* Tombol Sistem */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.55rem 1.1rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 9px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .btn:focus-visible {
            outline: 2px solid #047857;
            outline-offset: 2px;
        }
        .btn-primary {
            background-color: #047857;
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(4, 120, 87, 0.2);
        }
        .btn-primary:hover {
            background-color: #065f46;
            box-shadow: 0 2px 4px rgba(4, 120, 87, 0.3);
        }
        .btn-secondary {
            background-color: #f1f5f9;
            color: #334155;
            border-color: #e2e8f0;
        }
        .btn-secondary:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }
        .btn-danger {
            background-color: #ef4444;
            color: #ffffff;
        }
        .btn-danger:hover {
            background-color: #dc2626;
        }
        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 7px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.1rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
        }
        .form-control {
            width: 100%;
            padding: 0.6rem 0.85rem;
            font-size: 0.875rem;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background-color: #ffffff;
            color: #1e293b;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control:focus {
            border-color: #047857;
            box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.15);
        }
        .form-text {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.3rem;
            display: block;
        }

        /* Tabel Responsif */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #f1f5f9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
        }
        td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover td {
            background-color: #f8fafc;
        }

        /* Badge Status */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.6rem;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-warning { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-danger  { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-info    { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-gray    { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        /* Role Badges */
        .role-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .role-admin   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .role-manager { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .role-analyst { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
        .role-teller  { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .role-member  { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }

        /* Notifikasi Alert */
        .alert {
            padding: 0.9rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-info    { background: #f0f9ff; color: #075985; border: 1px solid #bae6fd; }
    /* Scrollbar Halus untuk Sidebar - Nuansa Emerald Elegan */
    .sidebar-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .sidebar-scroll::-webkit-scrollbar-track {
        background: #06241c;
    }
    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: #0d4737;
        border-radius: 9999px;
    }
    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: #135d49;
    }

    /* Skip to Content untuk Aksesibilitas */
    .skip-to-content {
        position: absolute;
        top: -100px;
        left: 1rem;
        z-index: 100;
        padding: 0.5rem 1rem;
        background: #047857;
        color: #ffffff;
        font-weight: 700;
        border-radius: 0.5rem;
        transition: top 0.2s;
    }
    .skip-to-content:focus {
        top: 1rem;
    }
</style>
@yield('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">
    <!-- Aksesibilitas: Lewati ke Konten Utama -->
    <a href="#main-content" class="skip-to-content focus:outline-none focus:ring-2 focus:ring-emerald-400">
        Lewati ke Konten Utama &rarr;
    </a>

@php
    $isStaffSidebar = auth()->check() && !auth()->user()->isMember() && !request()->routeIs('home');
    $isMember = auth()->check() && auth()->user()->isMember() && !request()->routeIs('home');
@endphp

@if(!$isStaffSidebar)
    {{-- ========================================================================= --}}
    {{-- TAMPILAN TOP NAVBAR: PUBLIK, GUEST, & KHUSUS ROLE MEMBER (SEBELUMNYA)    --}}
    {{-- ========================================================================= --}}
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm transition-all" role="banner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="flex items-center gap-3 group text-slate-900 no-underline" aria-label="Beranda KSP Sahabat Sejahtera">
                    <div class="w-10 h-10 rounded-xl navbar-brand-badge flex items-center justify-center text-white font-extrabold text-xl shadow group-hover:scale-105 transition-transform">
                        K
                    </div>
                    <div>
                        <div class="font-bold text-base tracking-tight text-slate-900 group-hover:text-emerald-800 transition-colors">
                            KSP Sahabat Sejahtera
                        </div>
                        <div class="text-[11px] font-medium text-slate-500">
                            Koperasi Simpan Pinjam Online
                        </div>
                    </div>
                </a>

                @if($isMember)
                    {{-- Navigasi Horizontal Khusus Role Member --}}
                    <nav class="hidden lg:flex items-center gap-1.5" aria-label="Navigasi Anggota Koperasi">
                        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Dasbor
                        </a>
                        <a href="{{ route('savings.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('savings.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Simpanan
                        </a>
                        <a href="{{ route('loans.my') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('loans.my*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Pinjaman Saya
                        </a>
                        <a href="{{ route('loans.simulation') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('loans.simulation') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Simulasi
                        </a>
                        <a href="{{ route('members.profile') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('members.profile') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Profil
                        </a>
                    </nav>

                    <!-- Profil & Logout Khusus Role Member -->
                    <div class="hidden lg:flex items-center gap-3">
                        <span class="role-badge role-member">
                            MEMBER
                        </span>
                        <span class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm" title="Keluar">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Navigasi Publik Sesuai Urutan Standar --}}
                    <nav class="hidden lg:flex items-center gap-1 xl:gap-2" aria-label="Navigasi Utama Landing Page">
                        <a href="{{ url('/#dashboard') }}" class="px-2.5 py-1.5 rounded-lg text-xs xl:text-sm font-semibold text-slate-700 hover:text-emerald-800 hover:bg-slate-100 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            Dashboard
                        </a>
                        <a href="{{ url('/#produk') }}" class="px-2.5 py-1.5 rounded-lg text-xs xl:text-sm font-semibold text-slate-700 hover:text-emerald-800 hover:bg-slate-100 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            Produk
                        </a>
                        <a href="{{ url('/#panduan') }}" class="px-2.5 py-1.5 rounded-lg text-xs xl:text-sm font-semibold text-slate-700 hover:text-emerald-800 hover:bg-slate-100 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            Panduan
                        </a>
                        <a href="{{ url('/#simulasi') }}" class="px-2.5 py-1.5 rounded-lg text-xs xl:text-sm font-semibold text-slate-700 hover:text-emerald-800 hover:bg-slate-100 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            Simulasi Pinjaman
                        </a>
                        <a href="{{ url('/#tentang-kami') }}" class="px-2.5 py-1.5 rounded-lg text-xs xl:text-sm font-semibold text-slate-700 hover:text-emerald-800 hover:bg-slate-100 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            Tentang Kami
                        </a>

                        @auth
                            <a href="{{ route('dashboard') }}" class="ml-2 btn btn-primary btn-sm px-4 py-1.5 rounded-xl font-bold shadow-xs hover:shadow transition-all flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dasbor Koperasi
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg text-xs xl:text-sm font-bold text-emerald-800 hover:bg-emerald-50 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                Masuk Akun
                            </a>
                            <a href="{{ route('register') }}" class="ml-1 btn btn-primary btn-sm px-3.5 py-1.5 rounded-xl font-bold shadow-xs hover:shadow transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                Daftar Anggota Baru
                            </a>
                        @endauth
                    </nav>
                @endif

                <!-- Mobile Hamburger Button -->
                <div class="lg:hidden flex items-center">
                    <button type="button" id="mobileMenuBtn" aria-label="Buka Menu Navigasi" aria-expanded="false" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path id="menuIconBars" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path id="menuIconClose" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div id="mobileMenu" class="hidden lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-4 space-y-1 shadow-lg" role="navigation" aria-label="Navigasi Menu Ponsel">
            @if($isMember)
                <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="role-badge role-member">MEMBER</span>
                        <span class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">Keluar</button>
                    </form>
                </div>
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                    Dasbor Utama
                </a>
                <a href="{{ route('savings.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('savings.*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                    Rekening Simpanan
                </a>
                <a href="{{ route('loans.my') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('loans.my*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                    Pinjaman Saya
                </a>
                <a href="{{ route('loans.simulation') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('loans.simulation') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                    Simulasi Pinjaman
                </a>
                <a href="{{ route('members.profile') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('members.profile') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                    Profil Saya
                </a>
            @else
                <a href="{{ url('/#dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">Dashboard</a>
                <a href="{{ url('/#produk') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">Produk</a>
                <a href="{{ url('/#panduan') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">Panduan</a>
                <a href="{{ url('/#simulasi') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">Simulasi Pinjaman</a>
                <a href="{{ url('/#tentang-kami') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">Tentang Kami</a>
                
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 mt-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary col-span-2 text-center py-2 text-xs font-bold">Buka Dasbor Koperasi &rarr;</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary text-center py-2 text-xs font-bold">Masuk Akun</a>
                        <a href="{{ route('register') }}" class="btn btn-primary text-center py-2 text-xs font-bold">Daftar Anggota</a>
                    @endauth
                </div>
            @endif
        </div>
    </header>

    <!-- Main Content Container Publik & Member -->
    <main id="main-content" class="flex-1 @yield('container-class', 'max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6')">
        @if(session('success'))
            <div class="alert alert-success shadow-sm" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.closest('.alert').remove()" class="text-emerald-800 hover:text-emerald-950 font-bold text-lg" aria-label="Tutup notifikasi">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error shadow-sm" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.closest('.alert').remove()" class="text-red-800 hover:text-red-950 font-bold text-lg" aria-label="Tutup notifikasi">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info shadow-sm" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" onclick="this.closest('.alert').remove()" class="text-blue-800 hover:text-blue-950 font-bold text-lg" aria-label="Tutup notifikasi">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer Publik & Portal Anggota (Dikembalikan Sesuai Permintaan Khusus Role Member) -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-6" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 space-y-1">
            <p class="font-semibold text-slate-700">&copy; {{ date('Y') }} KSP Sahabat Sejahtera. Badan Hukum No. AHU-0012345.AH.01.26.TAHUN 2026.</p>
            <p>Sistem Informasi Manajemen Koperasi Simpan Pinjam Online &bull; Berintegritas, Transparan, & Melayani Anggota.</p>
            <p class="pt-1.5 text-xs text-slate-500">
                Developed by <a href="https://www.linyjayainformatika.co.id/" target="_blank" rel="noopener" class="font-bold text-emerald-800 hover:text-emerald-950 underline hover:no-underline transition-colors focus:outline-none focus:ring-1 focus:ring-emerald-500">Liny Jaya Informatika</a>
            </p>
        </div>
    </footer>

@else
    {{-- ========================================================================= --}}
    {{-- TAMPILAN OPERASIONAL INTERNAL STAKEHOLDER (SIDEBAR ELEGAN SENADA BRAND KSP) --}}
    {{-- ========================================================================= --}}
    <div class="min-h-screen flex flex-col lg:flex-row bg-slate-50">

        <!-- DESKTOP SIDEBAR -->
        <aside id="desktopSidebar" class="hidden lg:flex lg:flex-col lg:w-72 lg:fixed lg:inset-y-0 lg:z-40 bg-gradient-to-b from-[#06241c] via-[#082d23] to-[#041a13] border-r border-emerald-900/60 text-emerald-100 shadow-xl" role="navigation" aria-label="Navigasi Sidebar Stakeholder">
            <!-- Brand & Koperasi Emblem Header -->
            <div class="p-5 border-b border-emerald-900/70 bg-[#041912]/85 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 no-underline group focus:outline-none focus:ring-2 focus:ring-emerald-500 rounded-xl" aria-label="Dasbor Koperasi">
                    <div class="w-10 h-10 rounded-xl navbar-brand-badge flex items-center justify-center text-white font-extrabold text-xl shadow-md group-hover:scale-105 transition-transform">
                        K
                    </div>
                    <div>
                        <div class="font-extrabold text-sm tracking-tight text-white group-hover:text-emerald-300 transition-colors">
                            KSP Sahabat Sejahtera
                        </div>
                        <div class="text-[10px] font-bold text-emerald-400/90 uppercase tracking-wider">
                            Sistem Operasional
                        </div>
                    </div>
                </a>
            </div>

            <!-- Role Stakeholder Chip -->
            <div class="px-5 py-2.5 bg-[#03140e]/70 border-b border-emerald-900/50 flex items-center justify-between">
                <span class="text-[10px] font-extrabold tracking-wider text-emerald-400/80 uppercase">Panel Stakeholder</span>
                <span class="role-badge role-{{ auth()->user()->role }}">
                    {{ auth()->user()->role }}
                </span>
            </div>

            <!-- Scrollable Navigation Items -->
            <nav class="flex-1 overflow-y-auto px-3.5 py-4 space-y-5 text-xs sidebar-scroll" aria-label="Daftar Menu">
                @include('layouts.partials.sidebar_nav')
            </nav>

            <!-- Sidebar Footer (Lihat Beranda + Profil Akun + Logout) -->
            <div class="p-4 border-t border-emerald-900/70 bg-[#03140e]/90 space-y-3">
                <a href="{{ url('/') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl bg-emerald-950/70 hover:bg-emerald-900/80 text-emerald-100 hover:text-white border border-emerald-800/60 transition-all text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Lihat Beranda Publik
                    </span>
                    <span class="text-3xs text-emerald-400 font-mono">↗</span>
                </a>

                <div class="flex items-center justify-between gap-2 pt-2 border-t border-emerald-900/60">
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-xs text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-emerald-300 font-mono truncate">
                            @if(auth()->user()->isStaff())
                                {{ auth()->user()->employee_code_formatted }}
                            @else
                                {{ auth()->user()->member?->member_number ?? 'ANGGOTA' }}
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-950 hover:bg-rose-950/80 text-emerald-200 hover:text-rose-200 border border-emerald-700/60 hover:border-rose-700/70 font-extrabold text-xs shadow-2xs transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500" title="Keluar dari sistem">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MOBILE OFF-CANVAS SIDEBAR -->
        <div id="mobileSidebarDrawer" class="fixed inset-0 z-50 lg:hidden hidden" role="dialog" aria-modal="true" aria-label="Navigasi Menu Ponsel">
            <!-- Backdrop -->
            <div id="mobileSidebarBackdrop" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

            <!-- Drawer Panel -->
            <div class="fixed inset-y-0 left-0 w-72 max-w-full bg-gradient-to-b from-[#06241c] to-[#041a13] text-emerald-100 border-r border-emerald-900/60 flex flex-col shadow-2xl z-10 transform transition-transform">
                <div class="p-4 border-b border-emerald-900/70 flex items-center justify-between bg-[#041912]">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg navbar-brand-badge flex items-center justify-center text-white font-extrabold text-sm">
                            K
                        </div>
                        <span class="font-bold text-xs text-white">KSP Sahabat Sejahtera</span>
                    </div>
                    <button type="button" id="closeMobileSidebarBtn" class="p-1.5 rounded-lg text-emerald-300 hover:text-white hover:bg-emerald-900/60 focus:outline-none focus:ring-2 focus:ring-emerald-500" aria-label="Tutup navigasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-4 py-2 bg-[#03140e]/70 border-b border-emerald-900/50 flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-emerald-400">Panel {{ auth()->user()->role }}</span>
                    <span class="role-badge role-{{ auth()->user()->role }}">{{ auth()->user()->role }}</span>
                </div>

                <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-4 text-xs sidebar-scroll" aria-label="Menu Ponsel">
                    @include('layouts.partials.sidebar_nav')
                </nav>

                <div class="p-3 border-t border-emerald-900/70 bg-[#03140e] space-y-2">
                    <a href="{{ url('/') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-lg bg-emerald-950/70 text-xs text-emerald-200">
                        <span>Lihat Beranda Publik</span>
                        <span>↗</span>
                    </a>
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 rounded bg-emerald-950 text-emerald-200 hover:text-rose-200 border border-emerald-700/60 font-bold text-xs">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE CONTENT AREA -->
        <div class="flex-1 min-w-0 lg:pl-72 flex flex-col min-h-screen">
            <!-- STICKY OPERATIONAL TOPBAR -->
            <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-emerald-100 shadow-2xs h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <!-- Hamburger Toggle Button Mobile -->
                    <button type="button" id="openMobileSidebarBtn" class="lg:hidden p-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500" aria-label="Buka Menu Navigasi" aria-expanded="false">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div class="hidden sm:block">
                        <div class="flex items-center gap-2 text-xs font-medium">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                Sistem Operasional Koperasi
                            </span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-slate-700 font-bold">{{ date('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Quick Info & Actions -->
                <div class="flex items-center gap-3">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.billboards.index') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold transition-all focus:outline-none focus:ring-2 focus:ring-amber-500" title="Kelola Billboard Pengumuman">
                            <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            Kelola Billboard
                        </a>
                    @endif

                    <div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
                        <span class="role-badge role-{{ auth()->user()->role }} text-2xs">
                            {{ auth()->user()->role }}
                        </span>
                        <div class="hidden md:flex flex-col text-right">
                            <span class="text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">
                                {{ auth()->user()->isStaff() ? auth()->user()->employee_code_formatted : (auth()->user()->member?->member_number ?? 'ANGGOTA') }}
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN OPERATIONAL CONTENT (FOOTER DIHILANGKAN UNTUK RUANG KERJA BERSIH) -->
            <main id="main-content" class="flex-1 @yield('container-class', 'w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-20')">
                @if(session('success'))
                    <div class="alert alert-success shadow-sm" role="alert">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.closest('.alert').remove()" class="text-emerald-800 hover:text-emerald-950 font-bold text-lg" aria-label="Tutup notifikasi">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error shadow-sm" role="alert">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="this.closest('.alert').remove()" class="text-red-800 hover:text-red-950 font-bold text-lg" aria-label="Tutup notifikasi">&times;</button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info shadow-sm" role="alert">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('info') }}</span>
                        </div>
                        <button type="button" onclick="this.closest('.alert').remove()" class="text-blue-800 hover:text-blue-950 font-bold text-lg" aria-label="Tutup notifikasi">&times;</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
@endif

    <!-- FLOATING CS 24 JAM WHATSAPP BUBBLE (TAMPIL DI SEMUA HALAMAN) -->
    <aside aria-label="Layanan Bantuan CS 24 Jam Koperasi">
        <a href="https://wa.me/6282197233174?text=Halo%20Admin%20KSP%20Sahabat%20Sejahtera,%20saya%20ingin%20bertanya%20seputar%20koperasi"
           target="_blank" 
           rel="noopener"
           aria-label="Hubungi Customer Service 24 Jam via WhatsApp"
           class="fixed bottom-5 right-5 sm:bottom-6 sm:right-6 z-50 inline-flex items-center gap-2.5 px-4 py-2.5 rounded-full bg-slate-950 text-white border-2 border-amber-500 shadow-2xl hover:bg-slate-900 hover:scale-105 active:scale-95 transition-all duration-200 group focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-slate-900">
            <!-- Status Indicator Bulat Hijau Online -->
            <span class="relative flex h-3 w-3 shrink-0 items-center justify-center">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <!-- WhatsApp Icon Gold / Amber -->
            <svg class="w-4 h-4 text-amber-400 shrink-0 group-hover:rotate-12 transition-transform" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12.031 2C6.495 2 2 6.495 2 12.031c0 1.954.557 3.864 1.615 5.517L2 22l4.61-1.579c1.583.947 3.411 1.458 5.421 1.458 5.536 0 10.031-4.495 10.031-10.031C22.062 6.495 17.567 2 12.031 2zm5.882 14.28c-.244.685-1.42 1.309-1.97 1.385-.515.071-1.187.1-3.414-.823-2.85-1.181-4.664-4.086-4.805-4.275-.14-.188-1.144-1.524-1.144-2.906 0-1.382.723-2.063.98-2.348.257-.285.56-.356.746-.356.187 0 .374.002.537.01.173.008.406-.065.635.485.244.585.83 2.023.902 2.17.072.147.12.32.024.513-.096.194-.144.314-.288.482-.144.168-.303.376-.433.505-.144.143-.294.298-.126.586.168.288.748 1.233 1.605 1.996 1.103.982 2.033 1.286 2.32 1.43.288.144.456.12.624-.072.168-.192.72-838.912-1.126.192-.288.384-.24.648-.144.264.096 1.679.792 1.967.936.288.144.48.216.552.336.072.12.072.696-.172 1.381z"/>
            </svg>
            <!-- Teks Sesuai Gambar -->
            <span class="text-xs sm:text-sm font-extrabold tracking-tight text-white whitespace-nowrap">
                CS 24 Jam &bull; Tanya via WA
            </span>
        </a>
    </aside>

    <!-- Interactive Scripts for Public & Internal Sidebar Navigations -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Public Navigation Hamburger
            const pubBtn = document.getElementById('mobileMenuBtn');
            const pubMenu = document.getElementById('mobileMenu');
            const pubBars = document.getElementById('menuIconBars');
            const pubClose = document.getElementById('menuIconClose');

            if (pubBtn && pubMenu) {
                pubBtn.addEventListener('click', function () {
                    const isOpen = !pubMenu.classList.contains('hidden');
                    if (isOpen) {
                        pubMenu.classList.add('hidden');
                        if (pubBars) pubBars.classList.remove('hidden');
                        if (pubClose) pubClose.classList.add('hidden');
                        pubBtn.setAttribute('aria-expanded', 'false');
                    } else {
                        pubMenu.classList.remove('hidden');
                        if (pubBars) pubBars.classList.add('hidden');
                        if (pubClose) pubClose.classList.remove('hidden');
                        pubBtn.setAttribute('aria-expanded', 'true');
                    }
                });
            }

            // Internal Mobile Sidebar Drawer Toggle
            const openDrawerBtn = document.getElementById('openMobileSidebarBtn');
            const closeDrawerBtn = document.getElementById('closeMobileSidebarBtn');
            const drawer = document.getElementById('mobileSidebarDrawer');
            const backdrop = document.getElementById('mobileSidebarBackdrop');

            function openDrawer() {
                if (drawer) {
                    drawer.classList.remove('hidden');
                    if (openDrawerBtn) openDrawerBtn.setAttribute('aria-expanded', 'true');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeDrawer() {
                if (drawer) {
                    drawer.classList.add('hidden');
                    if (openDrawerBtn) openDrawerBtn.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                }
            }

            if (openDrawerBtn) openDrawerBtn.addEventListener('click', openDrawer);
            if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
            if (backdrop) backdrop.addEventListener('click', closeDrawer);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && drawer && !drawer.classList.contains('hidden')) {
                    closeDrawer();
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
    @yield('scripts')
</body>
</html>
