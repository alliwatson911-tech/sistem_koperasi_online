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
    </style>
    @yield('styles')
</head>
<body class="bg-slate-50 text-slate-800">
    <!-- Navbar Header -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group text-slate-900 no-underline">
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

                @auth
                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Dasbor
                    </a>

                    @if(auth()->user()->isMember())
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
                    @else
                        {{-- Petugas / Pengurus Navigasi --}}
                        <a href="{{ route('members.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('members.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Anggota
                        </a>
                        <a href="{{ route('savings.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('savings.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Simpanan
                        </a>
                        <a href="{{ route('loans.applications') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('loans.applications*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Pengajuan Pinjaman
                        </a>
                        <a href="{{ route('loans.manage') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('loans.manage*') || request()->routeIs('loans.index') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Daftar Pinjaman
                        </a>
                        <a href="{{ route('loans.overdue.manage') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('loans.overdue*') ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Kelola Tunggakan
                        </a>
                        <a href="{{ route('payments.dashboard') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('payments.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Gateway & Bayar
                        </a>

                        @if(in_array(auth()->user()->role, ['admin', 'manager']))
                            <a href="{{ route('accounting.journals') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('accounting.journals') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                Jurnal
                            </a>
                            <a href="{{ route('accounting.ledger') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('accounting.ledger') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                Buku Besar
                            </a>
                            <a href="{{ route('accounting.trial_balance') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('accounting.trial_balance') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                Neraca
                            </a>
                            <a href="{{ route('accounting.shu') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('accounting.shu*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                SHU
                            </a>
                        @endif

                        <a href="{{ route('reports.savings') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('reports.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Laporan
                        </a>
                    @endif
                </nav>

                <!-- User Profile & Logout -->
                <div class="hidden lg:flex items-center gap-3">
                    <span class="role-badge role-{{ auth()->user()->role }}">
                        {{ auth()->user()->role }}
                    </span>
                    <div class="flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</span>
                        @if(auth()->user()->isStaff())
                        <span class="text-[10px] font-mono text-emerald-700 font-semibold">{{ auth()->user()->employee_code_formatted }}</span>
                        @endif
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" title="Keluar">
                            Keluar
                        </button>
                    </form>
                </div>
                @else
                <!-- Guest Navigation -->
                <div class="hidden lg:flex items-center gap-2">
                    <a href="{{ route('loans.simulation') }}" class="btn btn-secondary btn-sm">Simulasi Pinjaman</a>
                    <a href="{{ route('loans.products') }}" class="btn btn-secondary btn-sm">Produk</a>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Masuk Akun</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary btn-sm">Daftar Anggota</a>
                </div>
                @endauth

                <!-- Mobile Hamburger Button -->
                <div class="lg:hidden flex items-center">
                    <button type="button" id="mobileMenuBtn" aria-label="Buka Menu" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path id="menuIconBars" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path id="menuIconClose" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div id="mobileMenu" class="hidden lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-4 space-y-1 shadow-lg">
            @auth
                <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="role-badge role-{{ auth()->user()->role }}">{{ auth()->user()->role }}</span>
                        <div>
                            <span class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</span>
                            @if(auth()->user()->isStaff())
                            <span class="block text-[10px] font-mono text-emerald-700 font-semibold">{{ auth()->user()->employee_code_formatted }}</span>
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">Keluar</button>
                    </form>
                </div>

                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                    Dasbor Utama
                </a>

                @if(auth()->user()->isMember())
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
                    <a href="{{ route('members.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('members.*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        Kelola Anggota
                    </a>
                    <a href="{{ route('savings.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('savings.*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        Kelola Simpanan
                    </a>
                    <a href="{{ route('loans.applications') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('loans.applications*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        Pengajuan Pinjaman
                    </a>
                    <a href="{{ route('loans.manage') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('loans.manage*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        Daftar Pinjaman
                    </a>
                    <a href="{{ route('loans.overdue.manage') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('loans.overdue*') ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        Kelola Tunggakan & Kolektibilitas
                    </a>
                    @if(in_array(auth()->user()->role, ['admin', 'manager']))
                        <a href="{{ route('accounting.journals') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.journals') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            Jurnal Umum
                        </a>
                        <a href="{{ route('accounting.ledger') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.ledger') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            Buku Besar
                        </a>
                        <a href="{{ route('accounting.trial_balance') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.trial_balance') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            Neraca Saldo
                        </a>
                        <a href="{{ route('accounting.shu') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.shu*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            Tutup Buku & SHU
                        </a>
                    @endif
                    <a href="{{ route('reports.savings') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        Laporan Koperasi
                    </a>
                    <a href="{{ route('payments.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('payments.*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        Gateway & Rekonsiliasi
                    </a>
                @endif
            @else
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('login') }}" class="btn btn-primary text-center">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary text-center">Daftar</a>
                    <a href="{{ route('loans.simulation') }}" class="col-span-2 btn btn-secondary text-center">Simulasi Pinjaman</a>
                </div>
            @endauth
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="alert alert-success shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.closest('.alert').remove()" class="text-emerald-800 hover:text-emerald-950 font-bold text-lg">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.closest('.alert').remove()" class="text-red-800 hover:text-red-950 font-bold text-lg">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" onclick="this.closest('.alert').remove()" class="text-blue-800 hover:text-blue-950 font-bold text-lg">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 space-y-1">
            <p class="font-semibold text-slate-700">&copy; {{ date('Y') }} KSP Sahabat Sejahtera. Badan Hukum No. AHU-0012345.AH.01.26.TAHUN 2026.</p>
            <p>Sistem Informasi Manajemen Koperasi Simpan Pinjam Online &bull; Berintegritas, Transparan, & Melayani Anggota.</p>
        </div>
    </footer>

    <!-- Interactive Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobileMenuBtn');
            const menu = document.getElementById('mobileMenu');
            const iconBars = document.getElementById('menuIconBars');
            const iconClose = document.getElementById('menuIconClose');

            if (btn && menu) {
                btn.addEventListener('click', function () {
                    const isOpen = !menu.classList.contains('hidden');
                    if (isOpen) {
                        menu.classList.add('hidden');
                        iconBars.classList.remove('hidden');
                        iconClose.classList.add('hidden');
                    } else {
                        menu.classList.remove('hidden');
                        iconBars.classList.add('hidden');
                        iconClose.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
