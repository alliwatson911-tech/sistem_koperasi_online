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
            --primary: #0F4C81;
            --primary-dark: #1B263B;
            --primary-light: #EAF6F5;
            --secondary: #2A9D8F;
            --secondary-dark: #21867A;
            --secondary-light: #EAF6F5;
            --sage: #A3B18A;
            --accent-gold: #D4AF37;
            --accent-gold-hover: #C59F2D;
            --dark: #1B263B;
            --gray: #5C6B73;
            --light: #F8F5F0;
            --border: #E8E2D8;
            --card: #FFFFFF;
            --danger: #E11D48;
            --warning: #D4AF37;
            --success: #2A9D8F;
        }

        * { box-sizing: border-box; }
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 4rem;
            max-width: 100vw;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #F8F5F0;
            color: #1B263B;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
            max-width: 100%;
        }

        /* Navigasi Brand Badge */
        .navbar-brand-badge {
            background-color: #0F4C81;
            box-shadow: 0 2px 8px rgba(15, 76, 129, 0.25);
        }

        /* Komponen Card Desain Sistem */
        .ksp-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #E8E2D8;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease;
        }
        .card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #E8E2D8;
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
            border-bottom: 1px solid #F1ECE4;
        }
        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1B263B;
            letter-spacing: -0.01em;
        }

        /* Stat Card */
        .stat-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.25rem;
            border: 1px solid #E8E2D8;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 76, 129, 0.08);
        }
        .stat-label {
            font-size: 0.725rem;
            text-transform: uppercase;
            color: #5C6B73;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1B263B;
            line-height: 1.2;
        }
        .stat-meta {
            font-size: 0.75rem;
            color: #5C6B73;
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
            outline: 2px solid #2A9D8F;
            outline-offset: 2px;
        }
        .btn-primary {
            background-color: #0F4C81;
            color: #ffffff;
            border-color: #1B263B;
            box-shadow: 0 1px 2px rgba(15, 76, 129, 0.25);
        }
        .btn-primary:hover {
            background-color: #1B263B;
            box-shadow: 0 2px 4px rgba(15, 76, 129, 0.35);
        }
        .btn-secondary {
            background-color: #ffffff;
            color: #1B263B;
            border-color: #E8E2D8;
        }
        .btn-secondary:hover {
            background-color: #F8F5F0;
            color: #0F4C81;
            border-color: #2A9D8F;
        }
        .btn-gold {
            background-color: #D4AF37;
            color: #1B263B;
            font-weight: 700;
            border-color: #C59F2D;
            box-shadow: 0 1px 2px rgba(212, 175, 55, 0.3);
        }
        .btn-gold:hover {
            background-color: #C59F2D;
            box-shadow: 0 2px 4px rgba(212, 175, 55, 0.4);
        }
        .btn-danger {
            background-color: #E11D48;
            color: #ffffff;
        }
        .btn-danger:hover {
            background-color: #BE123C;
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
            color: #1B263B;
        }
        .form-control {
            width: 100%;
            padding: 0.6rem 0.85rem;
            font-size: 0.875rem;
            border: 1px solid #CBD5E1;
            border-radius: 9px;
            background-color: #ffffff;
            color: #1B263B;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control:focus {
            border-color: #2A9D8F;
            box-shadow: 0 0 0 3px rgba(42, 157, 143, 0.2);
        }
        .form-text {
            font-size: 0.75rem;
            color: #5C6B73;
            margin-top: 0.3rem;
            display: block;
        }

        /* Tabel Responsif */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #E8E2D8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            text-align: left;
        }
        th {
            background-color: #F8F5F0;
            color: #1B263B;
            font-weight: 700;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #E8E2D8;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
        }
        td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #F1ECE4;
            color: #1B263B;
            vertical-align: middle;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover td {
            background-color: #F8F5F0;
        }

        /* =========================================================================
           RESPONSIVE MOBILE CARDS UNTUK TABEL (KHUSUS LAYAR HANDPHONE <= 768px)
           Menghilangkan kebutuhan slide ke samping pada layar HP.
           Tampilan desktop (>= 769px) tetap 100% menggunakan tabel standar asli.
           ========================================================================= */
        @media (max-width: 768px) {
            .table-responsive,
            .overflow-x-auto {
                border: none !important;
                background: transparent !important;
                overflow-x: visible !important;
                padding: 0 !important;
            }

            .table-responsive table,
            .overflow-x-auto table {
                display: block !important;
                width: 100% !important;
                border: none !important;
                background: transparent !important;
            }

            .table-responsive thead,
            .overflow-x-auto thead {
                display: none !important; /* Header kolom tabel disembunyikan di device HP */
            }

            .table-responsive tbody,
            .overflow-x-auto tbody {
                display: block !important;
                width: 100% !important;
            }

            .table-responsive tbody tr,
            .overflow-x-auto tbody tr {
                display: block !important;
                width: 100% !important;
                background: #ffffff !important;
                border: 1px solid #E8E2D8 !important;
                border-radius: 16px !important;
                padding: 1rem 1.15rem !important;
                margin-bottom: 0.875rem !important;
                box-shadow: 0 2px 6px rgba(27, 38, 59, 0.04) !important;
                box-sizing: border-box !important;
            }

            .table-responsive tbody tr:hover,
            .overflow-x-auto tbody tr:hover {
                border-color: #0F4C81 !important;
            }

            .table-responsive tbody td,
            .overflow-x-auto tbody td {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                padding: 0.55rem 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                border-top: none !important;
                border-left: none !important;
                border-right: none !important;
                border-bottom: 1px dashed #EDE8E0 !important;
                font-size: 0.8125rem !important;
                text-align: right !important;
                box-sizing: border-box !important;
                min-height: 2.25rem !important;
                background: transparent !important;
            }

            .table-responsive tbody td::before,
            .overflow-x-auto tbody td::before {
                content: attr(data-label);
                font-weight: 700 !important;
                font-size: 0.6875rem !important;
                text-transform: uppercase !important;
                letter-spacing: 0.04em !important;
                color: #5C6B73 !important;
                text-align: left !important;
                padding-right: 0.75rem !important;
                flex-shrink: 0 !important;
                max-width: 48% !important;
            }

            .table-cell-content {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-end !important;
                text-align: right !important;
                gap: 0.15rem !important;
                max-width: 58% !important;
                word-break: break-word !important;
            }

            /* Kolom Aksi / Tombol Terakhir */
            .table-responsive tbody td:last-child,
            .overflow-x-auto tbody td:last-child,
            .table-responsive tbody td[data-label*="Aksi"],
            .table-responsive tbody td[data-label*="Tindakan"],
            .overflow-x-auto tbody td[data-label*="Aksi"],
            .overflow-x-auto tbody td[data-label*="Tindakan"] {
                border-bottom: none !important;
                padding-top: 0.75rem !important;
                padding-bottom: 0.25rem !important;
                justify-content: stretch !important;
                width: 100% !important;
            }

            .table-responsive tbody td:last-child::before,
            .overflow-x-auto tbody td:last-child::before,
            .table-responsive tbody td[data-label*="Aksi"]::before,
            .table-responsive tbody td[data-label*="Tindakan"]::before,
            .overflow-x-auto tbody td[data-label*="Aksi"]::before,
            .overflow-x-auto tbody td[data-label*="Tindakan"]::before {
                display: none !important;
            }

            .table-responsive tbody td:last-child > *,
            .overflow-x-auto tbody td:last-child > *,
            .table-responsive tbody td[data-label*="Aksi"] > *,
            .overflow-x-auto tbody td[data-label*="Aksi"] > *,
            .table-responsive tbody td[data-label*="Tindakan"] > *,
            .overflow-x-auto tbody td[data-label*="Tindakan"] > * {
                width: 100% !important;
                text-align: center !important;
                justify-content: center !important;
            }

            .table-responsive tbody td:last-child .table-cell-content,
            .overflow-x-auto tbody td:last-child .table-cell-content,
            .table-responsive tbody td[data-label*="Aksi"] .table-cell-content,
            .overflow-x-auto tbody td[data-label*="Aksi"] .table-cell-content,
            .table-responsive tbody td[data-label*="Tindakan"] .table-cell-content,
            .overflow-x-auto tbody td[data-label*="Tindakan"] .table-cell-content {
                width: 100% !important;
                max-width: 100% !important;
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: wrap !important;
                justify-content: stretch !important;
                gap: 0.5rem !important;
            }

            .table-responsive tbody td:last-child .btn,
            .table-responsive tbody td:last-child .admin-v2-btn-pill,
            .overflow-x-auto tbody td:last-child .btn,
            .overflow-x-auto tbody td:last-child .admin-v2-btn-pill,
            .table-responsive tbody td[data-label*="Aksi"] .btn,
            .table-responsive tbody td[data-label*="Aksi"] .admin-v2-btn-pill,
            .overflow-x-auto tbody td[data-label*="Aksi"] .btn,
            .overflow-x-auto tbody td[data-label*="Aksi"] .admin-v2-btn-pill {
                flex: 1 1 auto !important;
                width: 100% !important;
                padding-top: 0.65rem !important;
                padding-bottom: 0.65rem !important;
                justify-content: center !important;
            }

            /* Kolom colspan (status kosong / pesan) */
            .table-responsive tbody td[colspan],
            .overflow-x-auto tbody td[colspan] {
                display: block !important;
                text-align: center !important;
                justify-content: center !important;
                border-bottom: none !important;
                padding: 1.5rem 1rem !important;
            }
            .table-responsive tbody td[colspan]::before,
            .overflow-x-auto tbody td[colspan]::before {
                display: none !important;
                content: none !important;
            }
        }

        @media (min-width: 769px) {
            .table-cell-content {
                display: contents;
            }
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
        .badge-success { background-color: #EAF6F5; color: #2A9D8F; border: 1px solid #B8E4DF; }
        .badge-warning { background-color: #FFF9E6; color: #B45309; border: 1px solid #FDE68A; }
        .badge-danger  { background-color: #FFE4E6; color: #BE123C; border: 1px solid #FECDD3; }
        .badge-info    { background-color: #EAF2F8; color: #0F4C81; border: 1px solid #BDD7EE; }
        .badge-gray    { background-color: #F8F5F0; color: #5C6B73; border: 1px solid #E8E2D8; }

        /* Role Badges */
        .role-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .role-admin   { background: #D4AF37; color: #1B263B; border: 1px solid #C59F2D; font-weight: 800; }
        .role-manager { background: #0F4C81; color: #ffffff; border: 1px solid #1B263B; font-weight: 700; }
        .role-analyst { background: #EAF2F8; color: #0F4C81; border: 1px solid #BDD7EE; font-weight: 700; }
        .role-teller  { background: #EAF6F5; color: #2A9D8F; border: 1px solid #B8E4DF; font-weight: 700; }
        .role-member  { background: #EAF6F5; color: #0F4C81; border: 1px solid #B8E4DF; font-weight: 700; }

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
        .alert-success { background: #EAF6F5; color: #0F4C81; border: 1px solid #B8E4DF; }
        .alert-error   { background: #FFF1F2; color: #BE123C; border: 1px solid #FECDD3; }
        .alert-info    { background: #EAF2F8; color: #0F4C81; border: 1px solid #BDD7EE; }

        /* Scrollbar Halus untuk Sidebar - Solid Dark Navy & Toska */
        .sidebar-scroll::-webkit-scrollbar,
        .sidebar-scroll-admin::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scroll::-webkit-scrollbar-track,
        .sidebar-scroll-admin::-webkit-scrollbar-track {
            background: #101927;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb,
        .sidebar-scroll-admin::-webkit-scrollbar-thumb {
            background: #24344E;
            border-radius: 9999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover,
        .sidebar-scroll-admin::-webkit-scrollbar-thumb:hover {
            background: #2A9D8F;
        }

        /* Skip to Content untuk Aksesibilitas */
        .skip-to-content {
            position: absolute;
            top: -100px;
            left: 1rem;
            z-index: 100;
            padding: 0.5rem 1rem;
            background: #1B263B;
            color: #ffffff;
            font-weight: 700;
            border-radius: 0.5rem;
            transition: top 0.2s;
        }
        .skip-to-content:focus {
            top: 1rem;
        }
    </style>
@php
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
    $isStaffSidebar = auth()->check() && !auth()->user()->isMember() && !request()->routeIs('home');
    $isMember = auth()->check() && auth()->user()->isMember() && !request()->routeIs('home');
@endphp
@yield('styles')
</head>
<body class="bg-[#F8F5F0] text-[#1B263B] selection:bg-[#2A9D8F] selection:text-white antialiased w-full max-w-full">
    <!-- Aksesibilitas: Lewati ke Konten Utama -->
    <a href="#main-content" class="skip-to-content focus:outline-none focus:ring-2 focus:ring-[#2A9D8F] bg-[#1B263B]">
        Lewati ke Konten Utama &rarr;
    </a>

@if(!$isStaffSidebar)
    {{-- ========================================================================= --}}
    {{-- TAMPILAN TOP NAVBAR: PUBLIK, GUEST, & KHUSUS ROLE MEMBER (SEBELUMNYA)    --}}
    {{-- ========================================================================= --}}
    <header id="mainAppHeader" class="fixed top-0 left-0 right-0 z-50 w-full max-w-full bg-white/95 backdrop-blur-md border-b border-[#E8E2D8] shadow-xs transition-all duration-200" role="banner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="flex items-center justify-between h-16 gap-2">
                <!-- Brand Logo -->
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="flex items-center gap-2.5 sm:gap-3 group text-[#1B263B] no-underline min-w-0" aria-label="Beranda KSP Sahabat Sejahtera">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl navbar-brand-badge flex items-center justify-center text-white font-extrabold text-lg sm:text-xl shadow group-hover:scale-105 transition-transform shrink-0">
                        K
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-sm sm:text-base tracking-tight text-[#1B263B] group-hover:text-[#0F4C81] transition-colors truncate">
                            KSP Sahabat Sejahtera
                        </div>
                        <div class="text-[10px] sm:text-[11px] font-medium text-[#5C6B73] truncate">
                            Koperasi Simpan Pinjam Online
                        </div>
                    </div>
                </a>

                @if($isMember)
                    {{-- Navigasi Horizontal Khusus Role Member --}}
                    <nav class="hidden lg:flex items-center gap-1.5" aria-label="Navigasi Anggota Koperasi">
                        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-[#EAF6F5] text-[#0F4C81] font-bold border border-[#B8E4DF]' : 'text-slate-600 hover:text-[#0F4C81] hover:bg-[#F8F5F0]' }}">
                            Dasbor
                        </a>
                        <a href="{{ route('savings.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('savings.*') ? 'bg-[#EAF6F5] text-[#0F4C81] font-bold border border-[#B8E4DF]' : 'text-slate-600 hover:text-[#0F4C81] hover:bg-[#F8F5F0]' }}">
                            Simpanan
                        </a>
                        <a href="{{ route('loans.my') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('loans.my*') ? 'bg-[#EAF6F5] text-[#0F4C81] font-bold border border-[#B8E4DF]' : 'text-slate-600 hover:text-[#0F4C81] hover:bg-[#F8F5F0]' }}">
                            Pinjaman Saya
                        </a>
                        <a href="{{ route('loans.simulation') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('loans.simulation') ? 'bg-[#EAF6F5] text-[#0F4C81] font-bold border border-[#B8E4DF]' : 'text-slate-600 hover:text-[#0F4C81] hover:bg-[#F8F5F0]' }}">
                            Simulasi
                        </a>
                        <a href="{{ route('members.profile') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('members.profile') ? 'bg-[#EAF6F5] text-[#0F4C81] font-bold border border-[#B8E4DF]' : 'text-slate-600 hover:text-[#0F4C81] hover:bg-[#F8F5F0]' }}">
                            Profil
                        </a>
                    </nav>

                    <!-- Profil & Logout Khusus Role Member -->
                    <div class="hidden lg:flex items-center gap-3">
                        <span class="role-badge role-member">
                            MEMBER
                        </span>
                        <span class="text-xs font-bold text-[#1B263B]">{{ auth()->user()->name }}</span>
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
                        <a href="{{ url('/#dashboard') }}" class="px-2.5 py-1.5 rounded-lg text-xs xl:text-sm font-semibold text-slate-700 hover:text-[#0F4C81] hover:bg-[#F8F5F0] transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]">
                            Dashboard
                        </a>
                        <a href="{{ url('/#produk') }}" class="px-2.5 py-1.5 rounded-lg text-xs xl:text-sm font-semibold text-slate-700 hover:text-[#0F4C81] hover:bg-[#F8F5F0] transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]">
                            Produk
                        </a>
                        <a href="{{ url('/#panduan') }}" class="px-2.5 py-1.5 rounded-lg text-xs xl:text-sm font-semibold text-slate-700 hover:text-[#0F4C81] hover:bg-[#F8F5F0] transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]">
                            Panduan
                        </a>
                        <a href="{{ url('/#simulasi') }}" class="px-2.5 py-1.5 rounded-lg text-xs xl:text-sm font-semibold text-slate-700 hover:text-[#0F4C81] hover:bg-[#F8F5F0] transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]">
                            Simulasi Pinjaman
                        </a>
                        <a href="{{ url('/#tentang-kami') }}" class="px-2.5 py-1.5 rounded-lg text-xs xl:text-sm font-semibold text-slate-700 hover:text-[#0F4C81] hover:bg-[#F8F5F0] transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]">
                            Tentang Kami
                        </a>

                        @auth
                            <a href="{{ route('dashboard') }}" class="ml-2 btn btn-primary btn-sm px-4 py-1.5 rounded-xl font-bold shadow-xs hover:shadow transition-all flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dasbor Koperasi
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg text-xs xl:text-sm font-bold text-[#0F4C81] hover:bg-[#EAF6F5] transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]">
                                Masuk Akun
                            </a>
                            <a href="{{ route('register') }}" class="ml-1 px-4 py-1.5 rounded-xl font-extrabold text-xs xl:text-sm bg-[#D4AF37] hover:bg-[#C59F2D] text-[#1B263B] border border-[#C59F2D] shadow-xs hover:shadow transition-all focus:outline-none focus:ring-2 focus:ring-[#D4AF37]">
                                Daftar Anggota Baru
                            </a>
                        @endauth
                    </nav>
                @endif

                <!-- Mobile Hamburger Button -->
                <div class="lg:hidden flex items-center shrink-0">
                    <button type="button" id="mobileMenuBtn" aria-label="Buka Menu Navigasi" aria-expanded="false" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path id="menuIconBars" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path id="menuIconClose" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div id="mobileMenu" class="hidden lg:hidden border-t border-[#E8E2D8] bg-white px-4 pt-3 pb-4 space-y-1 shadow-lg max-h-[calc(100vh-4.5rem)] overflow-y-auto" role="navigation" aria-label="Navigasi Menu Ponsel">
            @if($isMember)
                <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="role-badge role-member">MEMBER</span>
                        <span class="text-sm font-bold text-[#1B263B]">{{ auth()->user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">Keluar</button>
                    </form>
                </div>
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-[#EAF6F5] text-[#0F4C81] font-bold' : 'text-slate-600 hover:bg-[#F8F5F0]' }}">
                    Dasbor Utama
                </a>
                <a href="{{ route('savings.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('savings.*') ? 'bg-[#EAF6F5] text-[#0F4C81] font-bold' : 'text-slate-600 hover:bg-[#F8F5F0]' }}">
                    Rekening Simpanan
                </a>
                <a href="{{ route('loans.my') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('loans.my*') ? 'bg-[#EAF6F5] text-[#0F4C81] font-bold' : 'text-slate-600 hover:bg-[#F8F5F0]' }}">
                    Pinjaman Saya
                </a>
                <a href="{{ route('loans.simulation') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('loans.simulation') ? 'bg-[#EAF6F5] text-[#0F4C81] font-bold' : 'text-slate-600 hover:bg-[#F8F5F0]' }}">
                    Simulasi Pinjaman
                </a>
                <a href="{{ route('members.profile') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('members.profile') ? 'bg-[#EAF6F5] text-[#0F4C81] font-bold' : 'text-slate-600 hover:bg-[#F8F5F0]' }}">
                    Profil Saya
                </a>
            @else
                <a href="{{ url('/#dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-[#F8F5F0]">Dashboard</a>
                <a href="{{ url('/#produk') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-[#F8F5F0]">Produk</a>
                <a href="{{ url('/#panduan') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-[#F8F5F0]">Panduan</a>
                <a href="{{ url('/#simulasi') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-[#F8F5F0]">Simulasi Pinjaman</a>
                <a href="{{ url('/#tentang-kami') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-[#F8F5F0]">Tentang Kami</a>
                
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 mt-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary col-span-2 text-center py-2 text-xs font-bold">Buka Dasbor Koperasi &rarr;</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary text-center py-2 text-xs font-bold">Masuk Akun</a>
                        <a href="{{ route('register') }}" class="btn btn-gold text-center py-2 text-xs font-bold">Daftar Anggota</a>
                    @endauth
                </div>
            @endif
        </div>
    </header>

    <!-- Header Spacer untuk menjaga aliran tata letak konten saat navbar fixed -->
    <div class="h-16 w-full shrink-0" aria-hidden="true"></div>

    <!-- Main Content Container Publik & Member -->
    <main id="main-content" class="flex-1 w-full max-w-full min-w-0 @yield('container-class', 'max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6')">
        @if(session('success'))
            <div class="alert alert-success shadow-sm" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#2A9D8F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.closest('.alert').remove()" class="text-[#0F4C81] hover:text-[#1B263B] font-bold text-lg" aria-label="Tutup notifikasi">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error shadow-sm" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.closest('.alert').remove()" class="text-rose-800 hover:text-rose-950 font-bold text-lg" aria-label="Tutup notifikasi">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info shadow-sm" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#0F4C81] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" onclick="this.closest('.alert').remove()" class="text-[#0F4C81] hover:text-[#1B263B] font-bold text-lg" aria-label="Tutup notifikasi">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer Publik & Portal Anggota Selaras Palet Warna Elegan Admin KSP -->
    <footer class="bg-[#1B263B] text-slate-300 border-t border-[#24344E] mt-auto pt-12 pb-8 w-full max-w-full overflow-hidden" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 4 Kolom Utama -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-10 pb-10">
                
                <!-- Kolom 1: Profil & Brand (lg:col-span-4) -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#0F4C81] border border-[#2A9D8F]/60 flex items-center justify-center text-white font-extrabold text-xl shadow-xs shrink-0">
                            K
                        </div>
                        <div>
                            <div class="text-lg font-black tracking-tight leading-tight">
                                <span class="text-[#D4AF37]">KSP</span> <span class="text-white">SAHABAT SEJAHTERA</span>
                            </div>
                            <div class="text-[10px] font-bold text-[#2A9D8F] uppercase tracking-wider">
                                Koperasi Simpan Pinjam Digital
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed max-w-sm">
                        Lembaga keuangan koperasi modern berasaskan kekeluargaan dan gotong-royong. Layanan simpanan bagi hasil berkah bulanan, permodalan mikro UMKM tanpa agunan rumit, dan pembukuan transparan berstandar SAK EP.
                    </p>

                    <!-- Trust Badge dengan Ikon Perisai (Palet Emas & Biru Finansial) -->
                    <div class="pt-2">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#0F4C81]/80 border border-[#2A9D8F]/50 text-xs font-bold text-[#D4AF37] uppercase tracking-wider shadow-2xs">
                            <svg class="w-4 h-4 text-[#D4AF37] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>BADAN HUKUM RESMI &amp; SIMPANAN TERJAMIN</span>
                        </div>
                    </div>
                </div>

                <!-- Kolom 2: Navigasi (lg:col-span-2) -->
                <div class="lg:col-span-2 space-y-3.5">
                    <h3 class="text-xs sm:text-sm font-extrabold text-[#A3B18A] uppercase tracking-wider">Navigasi</h3>
                    <ul class="space-y-2 text-xs">
                        <li>
                            <a href="{{ url('/#dashboard') }}" class="text-slate-200 hover:text-white transition-colors flex items-center gap-1.5 group">
                                <span class="text-[#2A9D8F] font-bold group-hover:text-[#D4AF37] group-hover:translate-x-1 transition-all">&rsaquo;</span>
                                <span>BERANDA UTAMA</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/#produk') }}" class="text-slate-200 hover:text-white transition-colors flex items-center gap-1.5 group">
                                <span class="text-[#2A9D8F] font-bold group-hover:text-[#D4AF37] group-hover:translate-x-1 transition-all">&rsaquo;</span>
                                <span>PRODUK SIMPAN PINJAM</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/#panduan') }}" class="text-slate-200 hover:text-white transition-colors flex items-center gap-1.5 group">
                                <span class="text-[#2A9D8F] font-bold group-hover:text-[#D4AF37] group-hover:translate-x-1 transition-all">&rsaquo;</span>
                                <span>PANDUAN ANGGOTA &amp; e-KYC</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/#simulasi') }}" class="text-slate-200 hover:text-white transition-colors flex items-center gap-1.5 group">
                                <span class="text-[#2A9D8F] font-bold group-hover:text-[#D4AF37] group-hover:translate-x-1 transition-all">&rsaquo;</span>
                                <span>SIMULASI PINJAMAN ONLINE</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/#tentang-kami') }}" class="text-slate-200 hover:text-white transition-colors flex items-center gap-1.5 group">
                                <span class="text-[#2A9D8F] font-bold group-hover:text-[#D4AF37] group-hover:translate-x-1 transition-all">&rsaquo;</span>
                                <span>TENTANG KAMI &amp; KONTAK</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Kolom 3: Jam Operasional (lg:col-span-3) -->
                <div class="lg:col-span-3 space-y-3.5">
                    <h3 class="text-xs sm:text-sm font-extrabold text-[#A3B18A] uppercase tracking-wider">Jam Operasional</h3>
                    <div class="space-y-1.5">
                        <div class="text-2xs sm:text-xs text-slate-300 font-bold tracking-wider uppercase">
                            SENIN &mdash; MINGGU (SETIAP HARI)
                        </div>
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-[#0F4C81] border border-[#2A9D8F]/50 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-[#2A9D8F] animate-pulse"></span>
                            <span class="text-base sm:text-lg font-black text-[#D4AF37]">
                                Buka 24 Jam Nonstop
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Layanan operasional kas kantor Senin s.d. Sabtu dan sistem transaksi online 24 jam nonstop untuk setoran simpanan, pengajuan kredit, dan pengecekan saldo anggota.
                    </p>
                </div>

                <!-- Kolom 4: Hubungi Kami (lg:col-span-3) -->
                <div class="lg:col-span-3 space-y-3.5">
                    <h3 class="text-xs sm:text-sm font-extrabold text-[#A3B18A] uppercase tracking-wider">Hubungi Kami</h3>
                    <ul class="space-y-2.5 text-xs">
                        <li class="flex items-start gap-2.5 group">
                            <div class="w-7 h-7 rounded-lg bg-[#0F4C81] border border-[#2A9D8F]/50 flex items-center justify-center text-[#2A9D8F] group-hover:text-[#D4AF37] group-hover:border-[#D4AF37]/60 shrink-0 transition-colors mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <span class="text-slate-300 leading-relaxed">
                                Gedung Graha Sahabat Sejahtera Lantai 1-2, Jl. Perintis Kemerdekaan KM 9 No. 45, Tamalanrea, Kota Makassar, Sulawesi Selatan 90245
                            </span>
                        </li>
                        <li class="flex items-center gap-2.5 group">
                            <div class="w-7 h-7 rounded-lg bg-[#0F4C81] border border-[#2A9D8F]/50 flex items-center justify-center text-[#2A9D8F] group-hover:text-[#D4AF37] group-hover:border-[#D4AF37]/60 shrink-0 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 2C6.495 2 2 6.495 2 12.031c0 1.954.557 3.864 1.615 5.517L2 22l4.61-1.579c1.583.947 3.411 1.458 5.421 1.458 5.536 0 10.031-4.495 10.031-10.031C22.062 6.495 17.567 2 12.031 2zm5.882 14.28c-.244.685-1.42 1.309-1.97 1.385-.515.071-1.187.1-3.414-.823-2.85-1.181-4.664-4.086-4.805-4.275-.14-.188-1.144-1.524-1.144-2.906 0-1.382.723-2.063.98-2.348.257-.285.56-.356.746-.356.187 0 .374.002.537.01.173.008.406-.065.635.485.244.585.83 2.023.902 2.17.072.147.12.32.024.513-.096.194-.144.314-.288.482-.144.168-.303.376-.433.505-.144.143-.294.298-.126.586.168.288.748 1.233 1.605 1.996 1.103.982 2.033 1.286 2.32 1.43.288.144.456.12.624-.072.168-.192.72-838.912-1.126.192-.288.384-.24.648-.144.264.096 1.679.792 1.967.936.288.144.48.216.552.336.072.12.072.696-.172 1.381z"/>
                                </svg>
                            </div>
                            <a href="https://wa.me/6282197233174" target="_blank" rel="noopener" class="text-slate-200 hover:text-[#D4AF37] font-semibold transition-colors">
                                +62 821-9723-3174
                            </a>
                        </li>
                        <li class="flex items-center gap-2.5 group">
                            <div class="w-7 h-7 rounded-lg bg-[#0F4C81] border border-[#2A9D8F]/50 flex items-center justify-center text-[#2A9D8F] group-hover:text-[#D4AF37] group-hover:border-[#D4AF37]/60 shrink-0 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 2C6.495 2 2 6.495 2 12.031c0 1.954.557 3.864 1.615 5.517L2 22l4.61-1.579c1.583.947 3.411 1.458 5.421 1.458 5.536 0 10.031-4.495 10.031-10.031C22.062 6.495 17.567 2 12.031 2zm5.882 14.28c-.244.685-1.42 1.309-1.97 1.385-.515.071-1.187.1-3.414-.823-2.85-1.181-4.664-4.086-4.805-4.275-.14-.188-1.144-1.524-1.144-2.906 0-1.382.723-2.063.98-2.348.257-.285.56-.356.746-.356.187 0 .374.002.537.01.173.008.406-.065.635.485.244.585.83 2.023.902 2.17.072.147.12.32.024.513-.096.194-.144.314-.288.482-.144.168-.303.376-.433.505-.144.143-.294.298-.126.586.168.288.748 1.233 1.605 1.996 1.103.982 2.033 1.286 2.32 1.43.288.144.456.12.624-.072.168-.192.72-838.912-1.126.192-.288.384-.24.648-.144.264.096 1.679.792 1.967.936.288.144.48.216.552.336.072.12.072.696-.172 1.381z"/>
                                </svg>
                            </div>
                            <a href="https://wa.me/6282235585445" target="_blank" rel="noopener" class="text-slate-200 hover:text-[#D4AF37] font-semibold transition-colors">
                                +62 822-3558-5445
                            </a>
                        </li>
                        <li class="flex items-center gap-2.5 group">
                            <div class="w-7 h-7 rounded-lg bg-[#0F4C81] border border-[#2A9D8F]/50 flex items-center justify-center text-[#2A9D8F] group-hover:text-[#D4AF37] group-hover:border-[#D4AF37]/60 shrink-0 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <a href="mailto:layanan@koperasi.systemlji.com" class="text-slate-200 hover:text-[#D4AF37] font-semibold transition-colors">
                                layanan@koperasi.systemlji.com
                            </a>
                        </li>
                        <li class="flex items-center gap-2.5 group">
                            <div class="w-7 h-7 rounded-lg bg-[#0F4C81] border border-[#2A9D8F]/50 flex items-center justify-center text-[#2A9D8F] group-hover:text-[#D4AF37] group-hover:border-[#D4AF37]/60 shrink-0 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </div>
                            <a href="https://instagram.com" target="_blank" rel="noopener" class="text-slate-200 hover:text-[#D4AF37] font-semibold transition-colors flex items-center gap-1">
                                <span>@ksp.sahabatsejahtera</span>
                                <span class="text-3xs font-mono text-[#2A9D8F]">&nearr;</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Garis Pembatas Horisontal -->
            <div class="border-t border-[#24344E] pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-300">
                <!-- Kiri: Hak Cipta & Pengembang -->
                <div class="text-center md:text-left leading-relaxed">
                    &copy; {{ date('Y') }} <strong class="text-white">KSP Sahabat Sejahtera</strong>. Hak Cipta Dilindungi Undang-Undang. Badan Hukum No. <span class="text-[#A3B18A] font-semibold font-mono">AHU-0012345.AH.01.26.TAHUN 2026</span>. Developed by <a href="https://www.linyjayainformatika.co.id/" target="_blank" rel="noopener" class="font-bold text-[#2A9D8F] hover:text-[#D4AF37] underline transition-colors">Liny Jaya Informatika</a>.
                </div>

                <!-- Tengah: Kebijakan Privasi & Syarat Layanan Modal Triggers -->
                <div class="flex items-center gap-3 text-2xs sm:text-xs">
                    <button type="button" onclick="openFooterModal('footerPrivacyModal')" class="hover:text-[#2A9D8F] transition-colors cursor-pointer underline hover:no-underline">
                        Kebijakan Privasi
                    </button>
                    <span class="text-[#A3B18A]">&bull;</span>
                    <button type="button" onclick="openFooterModal('footerTermsModal')" class="hover:text-[#2A9D8F] transition-colors cursor-pointer underline hover:no-underline">
                        Syarat Layanan
                    </button>
                </div>

                <!-- Kanan: Lokasi Wilayah Sesuai Palet Emas & Toska Admin -->
                <div class="text-center md:text-right font-extrabold text-[#D4AF37] flex items-center justify-center md:justify-end gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#2A9D8F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Makassar, Sulawesi Selatan</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- MODAL KEBIJAKAN PRIVASI KOPERASI -->
    <div id="footerPrivacyModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="privacyModalTitle">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-4 shadow-2xl border border-[#E8E2D8] relative text-slate-800">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#EAF6F5] text-[#0F4C81] border border-[#2A9D8F]/30 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4 text-[#2A9D8F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 id="privacyModalTitle" class="text-base font-bold text-[#1B263B]">Kebijakan Privasi KSP Sahabat Sejahtera</h3>
                </div>
                <button type="button" onclick="closeFooterModal('footerPrivacyModal')" class="text-slate-400 hover:text-[#1B263B] text-xl font-bold p-1 leading-none">&times;</button>
            </div>
            <div class="text-xs text-slate-600 space-y-2.5 leading-relaxed max-h-80 overflow-y-auto pr-1">
                <p>KSP Sahabat Sejahtera berkomitmen melindungi data pribadi seluruh anggota sesuai Undang-Undang No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP).</p>
                <p><strong>1. Pengumpulan Data:</strong> Kami mengumpulkan data e-KTP, nomor telepon, dan bukti usaha semata-mata untuk verifikasi keanggotaan dan mitigasi risiko perkreditan koperasi.</p>
                <p><strong>2. Keamanan Data:</strong> Seluruh berkas digital dan password disimpan terenkripsi menggunakan algoritma standar industri (Bcrypt &amp; TLS 1.3) serta diaudit berkala.</p>
                <p><strong>3. Kerahasiaan Finansial:</strong> Data mutasi rekening simpanan dan pinjaman anggota bersifat rahasia dan tidak diperjualbelikan kepada pihak ketiga manapun.</p>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closeFooterModal('footerPrivacyModal')" class="px-5 py-2 rounded-xl bg-[#0F4C81] hover:bg-[#1B263B] text-white font-bold text-xs shadow-xs transition-colors">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL SYARAT LAYANAN KOPERASI -->
    <div id="footerTermsModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="termsModalTitle">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-4 shadow-2xl border border-[#E8E2D8] relative text-slate-800">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#FFF9E6] text-[#D4AF37] border border-[#F5E0A0] flex items-center justify-center font-bold">
                        <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 id="termsModalTitle" class="text-base font-bold text-[#1B263B]">Syarat &amp; Ketentuan Layanan Koperasi</h3>
                </div>
                <button type="button" onclick="closeFooterModal('footerTermsModal')" class="text-slate-400 hover:text-[#1B263B] text-xl font-bold p-1 leading-none">&times;</button>
            </div>
            <div class="text-xs text-slate-600 space-y-2.5 leading-relaxed max-h-80 overflow-y-auto pr-1">
                <p>Ketentuan layanan keanggotaan dan transaksi simpan pinjam online berlandaskan Anggaran Dasar dan Anggaran Rumah Tangga (AD/ART) KSP Sahabat Sejahtera:</p>
                <p><strong>1. Keanggotaan:</strong> Calon anggota wajib WNI cakap hukum, mengisi identitas jujur, dan melunasi Simpanan Pokok awal sebesar Rp 100.000.</p>
                <p><strong>2. Simpanan Wajib &amp; SHU:</strong> Anggota berkewajiban menyetor Simpanan Wajib rutin setiap bulan sebagai dasar alokasi pembagian dividen Sisa Hasil Usaha (SHU) tahunan.</p>
                <p><strong>3. Pinjaman &amp; Denda Adil:</strong> Pembiayaan disalurkan setelah melalui analisis survei kelayakan. Denda keterlambatan dibatasi maksimal 10% per angsuran non-compounding.</p>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closeFooterModal('footerTermsModal')" class="px-5 py-2 rounded-xl bg-[#D4AF37] hover:bg-[#C59F2D] text-[#1B263B] font-bold text-xs shadow-xs transition-colors">Saya Mengerti</button>
            </div>
        </div>
    </div>

@else
    {{-- ========================================================================= --}}
    {{-- TAMPILAN OPERASIONAL INTERNAL STAKEHOLDER (SIDEBAR SOLID NAVY ELEGAN)     --}}
    {{-- ========================================================================= --}}
    <div class="min-h-screen flex flex-col lg:flex-row bg-[#F8F5F0]">

        <!-- DESKTOP SIDEBAR (WARNA SOLID SENADA LOGO BRAND KSP - TANPA GRADASI) -->
        <aside id="desktopSidebar" class="hidden lg:flex lg:flex-col lg:w-72 lg:fixed lg:inset-y-0 lg:z-40 bg-[#1B263B] border-r border-[#24344E] text-slate-100 shadow-xl" role="navigation" aria-label="Navigasi Sidebar Stakeholder">
            <!-- Brand & Koperasi Emblem Header -->
            <div class="p-5 border-b border-[#24344E] bg-[#101927] flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 no-underline group focus:outline-none focus:ring-2 focus:ring-[#2A9D8F] rounded-xl" aria-label="Dasbor Koperasi">
                    <div class="w-10 h-10 rounded-xl bg-[#0F4C81] border border-[#2A9D8F]/60 flex items-center justify-center text-white font-extrabold text-xl shadow-xs group-hover:scale-105 transition-transform">
                        K
                    </div>
                    <div>
                        <div class="font-extrabold text-sm tracking-tight text-white group-hover:text-[#A3B18A] transition-colors">
                            KSP Sahabat Sejahtera
                        </div>
                        <div class="text-[10px] font-bold text-[#2A9D8F] uppercase tracking-wider">
                            Sistem Operasional
                        </div>
                    </div>
                </a>
            </div>

            <!-- Role Stakeholder Chip -->
            <div class="px-5 py-2.5 bg-[#0d1520] border-b border-[#24344E] flex items-center justify-between">
                <span class="text-[10px] font-extrabold tracking-wider text-[#A3B18A] uppercase">Panel Stakeholder</span>
                <span class="role-badge role-{{ auth()->user()->role }}">
                    {{ auth()->user()->role }}
                </span>
            </div>

            <!-- Scrollable Navigation Items -->
            <nav class="flex-1 overflow-y-auto px-3.5 py-4 space-y-5 text-xs sidebar-scroll-admin" aria-label="Daftar Menu">
                @include('layouts.partials.sidebar_nav')
            </nav>

            <!-- Sidebar Footer (Lihat Beranda + Profil Akun + Logout) -->
            <div class="p-4 border-t border-[#24344E] bg-[#0d1520] space-y-3">
                <a href="{{ url('/') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl bg-[#1B263B] hover:bg-[#24344E] text-slate-200 hover:text-white border border-[#24344E] transition-all text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#2A9D8F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Lihat Beranda Publik
                    </span>
                    <span class="text-3xs text-[#2A9D8F] font-mono">↗</span>
                </a>

                <div class="flex items-center justify-between gap-2 pt-2 border-t border-[#24344E]">
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-xs text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-[#A3B18A] font-mono truncate">
                            @if(auth()->user()->isStaff())
                                {{ auth()->user()->employee_code_formatted }}
                            @else
                                {{ auth()->user()->member?->member_number ?? 'ANGGOTA' }}
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#101927] hover:bg-rose-950/80 text-slate-300 hover:text-rose-200 border border-[#24344E] hover:border-rose-700/70 font-extrabold text-xs shadow-2xs transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]" title="Keluar dari sistem">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MOBILE OFF-CANVAS SIDEBAR (WARNA SOLID - TANPA GRADASI) -->
        <div id="mobileSidebarDrawer" class="fixed inset-0 z-50 lg:hidden hidden" role="dialog" aria-modal="true" aria-label="Navigasi Menu Ponsel">
            <!-- Backdrop -->
            <div id="mobileSidebarBackdrop" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

            <!-- Drawer Panel -->
            <div class="fixed inset-y-0 left-0 w-72 max-w-full bg-[#1B263B] text-slate-100 border-r border-[#24344E] flex flex-col shadow-2xl z-10 transform transition-transform">
                <div class="p-4 border-b border-[#24344E] bg-[#101927] flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#0F4C81] border border-[#2A9D8F]/60 flex items-center justify-center text-white font-extrabold text-sm">
                            K
                        </div>
                        <span class="font-bold text-xs text-white">KSP Sahabat Sejahtera</span>
                    </div>
                    <button type="button" id="closeMobileSidebarBtn" class="p-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-[#24344E] focus:ring-[#2A9D8F] focus:outline-none focus:ring-2" aria-label="Tutup navigasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-4 py-2 bg-[#0d1520] border-b border-[#24344E] flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-[#A3B18A]">Panel {{ auth()->user()->role }}</span>
                    <span class="role-badge role-{{ auth()->user()->role }}">{{ auth()->user()->role }}</span>
                </div>

                <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-4 text-xs sidebar-scroll-admin" aria-label="Menu Ponsel">
                    @include('layouts.partials.sidebar_nav')
                </nav>

                <div class="p-3 border-t border-[#24344E] bg-[#0d1520] space-y-2">
                    <a href="{{ url('/') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-lg bg-[#1B263B] text-slate-200 text-xs">
                        <span>Lihat Beranda Publik</span>
                        <span class="text-[#2A9D8F]">↗</span>
                    </a>
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 rounded bg-[#101927] text-slate-200 border border-[#24344E] hover:text-rose-200 font-bold text-xs">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE CONTENT AREA -->
        <div class="flex-1 min-w-0 w-full max-w-full lg:pl-72 flex flex-col min-h-screen bg-[#F8F5F0]">
            <!-- STICKY OPERATIONAL TOPBAR -->
            <header class="sticky top-0 z-30 w-full max-w-full bg-white/95 border-b border-[#E8E2D8] backdrop-blur-md shadow-2xs h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <!-- Hamburger Toggle Button Mobile -->
                    <button type="button" id="openMobileSidebarBtn" class="lg:hidden p-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]" aria-label="Buka Menu Navigasi" aria-expanded="false">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div class="hidden sm:block">
                        <div class="flex items-center gap-2 text-xs font-medium">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B263B] text-white text-xs font-bold border border-[#2A9D8F]/50 shadow-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#D4AF37] animate-pulse"></span>
                                Sistem Operasional Koperasi
                            </span>
                            <span class="text-slate-400">&bull;</span>
                            <span class="text-[#1B263B] font-bold">{{ date('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Quick Info & Actions -->
                <div class="flex items-center gap-3">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.billboards.index') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#FFF9E6] hover:bg-[#FDF0C8] text-[#1B263B] border border-[#F5E0A0] text-xs font-bold shadow-2xs transition-all focus:outline-none focus:ring-2 focus:ring-[#D4AF37]" title="Kelola Billboard Pengumuman">
                            <svg class="w-3.5 h-3.5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            <span>Kelola Billboard</span>
                        </a>
                    @endif

                    <div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
                        <span class="role-badge role-{{ auth()->user()->role }} text-2xs">
                            {{ auth()->user()->role }}
                        </span>
                        <div class="hidden md:flex flex-col text-right">
                            <span class="text-xs font-bold text-[#1B263B] leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-[#5C6B73] font-mono">
                                {{ auth()->user()->isStaff() ? auth()->user()->employee_code_formatted : (auth()->user()->member?->member_number ?? 'ANGGOTA') }}
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN OPERATIONAL CONTENT (FOOTER DIHILANGKAN UNTUK RUANG KERJA BERSIH) -->
            <main id="main-content" class="flex-1 w-full max-w-full min-w-0 @yield('container-class', 'w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-20')">
                @if(session('success'))
                    <div class="alert alert-success shadow-sm" role="alert">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#2A9D8F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.closest('.alert').remove()" class="text-[#0F4C81] hover:text-[#1B263B] font-bold text-lg" aria-label="Tutup notifikasi">&times;</button>
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
        // Footer Modal Helpers (Kebijakan Privasi & Syarat Layanan)
        window.openFooterModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeFooterModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Close footer modals on outside backdrop click & escape key
            ['footerPrivacyModal', 'footerTermsModal'].forEach(function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            window.closeFooterModal(modalId);
                        }
                    });
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    ['footerPrivacyModal', 'footerTermsModal'].forEach(function(modalId) {
                        const modal = document.getElementById(modalId);
                        if (modal && !modal.classList.contains('hidden')) {
                            window.closeFooterModal(modalId);
                        }
                    });
                }
            });

            // Efek Header Mengikuti Scroll dengan Elevasi Shadow
            const mainHeader = document.getElementById('mainAppHeader');
            if (mainHeader) {
                const handleHeaderScroll = function () {
                    if (window.scrollY > 8) {
                        mainHeader.classList.add('shadow-md');
                        mainHeader.classList.remove('shadow-xs');
                    } else {
                        mainHeader.classList.remove('shadow-md');
                        mainHeader.classList.add('shadow-xs');
                    }
                };
                window.addEventListener('scroll', handleHeaderScroll, { passive: true });
                handleHeaderScroll();
            }

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

            // Inisialisasi Otomatis Data-Label untuk Kartu Tabel Mobile
            function initResponsiveTables() {
                document.querySelectorAll('.table-responsive table, .overflow-x-auto table').forEach(function(table) {
                    if (table.classList.contains('table-no-stack')) return;

                    var ths = table.querySelectorAll('thead th');
                    var headers = [];
                    ths.forEach(function(th) {
                        headers.push(th.textContent.trim().replace(/\s+/g, ' '));
                    });

                    if (headers.length === 0) return;

                    table.querySelectorAll('tbody tr').forEach(function(tr) {
                        if (tr.querySelector('td[colspan]')) return;

                        var tds = tr.querySelectorAll('td');
                        tds.forEach(function(td, idx) {
                            var headerText = headers[idx] || '';
                            if (!td.getAttribute('data-label') && headerText) {
                                td.setAttribute('data-label', headerText);
                            }
                            if (!td.querySelector('.table-cell-content')) {
                                var wrapper = document.createElement('div');
                                wrapper.className = 'table-cell-content';
                                while (td.firstChild) {
                                    wrapper.appendChild(td.firstChild);
                                }
                                td.appendChild(wrapper);
                            }
                        });
                    });
                });
            }

            initResponsiveTables();
            window.initResponsiveTables = initResponsiveTables;
        });
    </script>
    @yield('scripts')
</body>
</html>
