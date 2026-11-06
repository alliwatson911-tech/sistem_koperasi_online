@extends('layouts.app')

@section('title', 'Kelola Billboard Pengumuman Resmi')

@section('content')
<!-- Navigasi Kembali ke Halaman Sebelumnya (Kiri Atas) -->
<div class="mb-4">
    <a href="{{ route('dashboard') }}" 
       class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white hover:bg-[#F8F5F0] text-[#1B263B] hover:text-[#0F4C81] border border-[#E8E2D8] text-xs font-bold transition-all shadow-2xs"
       title="Kembali ke halaman sebelumnya">
        <svg class="w-4 h-4 text-[#0F4C81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali ke Halaman Sebelumnya</span>
    </a>
</div>

<div class="space-y-6">
    <!-- Header Halaman: Palet Biru & Hijau Elegan Khusus Role Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#E8E2D8]">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#FFF9E6] text-[#9A7B1C] border border-[#F5E0A0]">
                    <svg class="w-3.5 h-3.5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    Papan Informasi & Pengumuman
                </span>
            </div>
            <h1 class="text-2xl font-black text-[#1B263B] tracking-tight">Kelola Billboard Pengumuman Resmi Koperasi</h1>
            <p class="text-xs sm:text-sm text-[#5C6B73]">Atur konten slide pengumuman resmi yang ditampilkan pada papan billboard landing page koperasi.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ url('/#dashboard') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-white hover:bg-[#F8F5F0] text-[#1B263B] border border-[#E8E2D8] text-xs font-bold flex items-center gap-1.5 shadow-2xs transition-all">
                <svg class="w-4 h-4 text-[#2A9D8F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Lihat di Landing Page
            </a>
            <a href="{{ route('admin.billboards.create') }}" class="px-3.5 py-2 rounded-xl bg-[#D4AF37] hover:bg-[#C59F2D] text-[#1B263B] border border-[#C59F2D] text-xs font-black flex items-center gap-1.5 shadow-sm transition-all">
                <svg class="w-4 h-4 text-[#1B263B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Tambah Pengumuman Baru
            </a>
        </div>
    </div>

    <!-- Alert Edukasi Admin -->
    <div class="p-4 rounded-2xl bg-[#FFF9E6]/80 border border-[#F5E0A0] flex items-start gap-3 text-xs text-[#1B263B]">
        <svg class="w-5 h-5 text-[#D4AF37] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="space-y-1">
            <p class="font-bold text-[#1B263B]">Panduan Billboard Pengumuman Koperasi:</p>
            <p class="text-[#5C6B73] leading-relaxed">
                Pengumuman berstatus <strong class="text-[#1B263B]">Aktif</strong> akan otomatis ditampilkan pada Billboard interaktif di halaman utama koperasi. Pengunjung atau anggota dapat berpindah antar-pengumuman melalui tab navigasi billboard. Pastikan informasi relevan, akurat, dan mencerminkan transparansi KSP Sahabat Sejahtera.
            </p>
        </div>
    </div>

    <!-- Tabel Daftar Pengumuman -->
    <div class="bg-white rounded-2xl border border-[#E8E2D8] shadow-sm overflow-hidden">
        <div class="p-5 border-b border-[#E8E2D8] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-[#2A9D8F]"></div>
                <h2 class="text-sm font-bold text-[#1B263B]">Daftar Konten Billboard Pengumuman ({{ $billboards->count() }})</h2>
            </div>
            <span class="text-2xs text-[#5C6B73]">Diurutkan berdasarkan urutan tampilan</span>
        </div>

        <div class="table-responsive">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#F8F5F0] border-b border-[#E8E2D8] text-[#1B263B] text-xs uppercase font-bold">
                        <th class="py-3 px-4 w-12 text-center">Urutan</th>
                        <th class="py-3 px-4 w-44">Tab Navigasi</th>
                        <th class="py-3 px-4 w-36">Kategori</th>
                        <th class="py-3 px-4">Judul & Ringkasan Konten</th>
                        <th class="py-3 px-4 w-40">Tautan Aksi</th>
                        <th class="py-3 px-4 w-28 text-center">Status</th>
                        <th class="py-3 px-4 w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($billboards as $item)
                    <tr class="hover:bg-[#FAF9F5] transition-colors {{ !$item->is_active ? 'opacity-60 bg-slate-50/40' : '' }}">
                        <td class="py-3.5 px-4 text-center font-bold font-mono text-[#0F4C81]">
                            #{{ $item->order }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-[#1B263B]">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $item->is_active ? 'bg-[#2A9D8F]' : 'bg-slate-300' }}"></span>
                                <span>{{ $item->tab_title }}</span>
                            </div>
                            @if($item->meta_text)
                            <span class="text-[10px] text-[#5C6B73] font-normal block mt-0.5">{{ $item->meta_text }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="badge {{ $item->badge_class }} text-[10px]">
                                {{ $item->badge_text }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 space-y-1">
                            <span class="font-extrabold text-[#1B263B] block text-sm">{{ $item->title }}</span>
                            <p class="text-[#5C6B73] text-xs line-clamp-2 leading-relaxed">{{ $item->content }}</p>
                            @if($item->status_text)
                            <span class="inline-block text-[11px] font-semibold text-[#2A9D8F] bg-[#EAF6F5] px-2 py-0.5 rounded border border-[#B8E4DF]">
                                {{ $item->status_text }}
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-[#5C6B73]">
                            @if($item->link_url)
                            <div class="font-mono text-[11px] text-[#0F4C81] font-semibold truncate max-w-[150px]" title="{{ $item->link_url }}">
                                {{ $item->link_text ?? 'Lihat Tautan' }}
                            </div>
                            <span class="text-[10px] text-slate-400 block truncate max-w-[150px]">{{ $item->link_url }}</span>
                            @else
                            <span class="text-slate-400 italic">Tanpa tautan</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <form action="{{ route('admin.billboards.toggle', $item->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="cursor-pointer" title="Klik untuk mengubah status aktif/non-aktif">
                                    @if($item->is_active)
                                    <span class="badge badge-success text-[10px] bg-[#EAF6F5] text-[#2A9D8F] border border-[#B8E4DF]">
                                        <svg class="w-3 h-3 text-[#2A9D8F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Aktif
                                    </span>
                                    @else
                                    <span class="badge badge-gray text-[10px]">
                                        Nonaktif
                                    </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.billboards.edit', $item->id) }}" class="p-1.5 rounded-lg text-[#0F4C81] hover:text-[#1B263B] hover:bg-[#EAF6F5] transition-colors" title="Edit Pengumuman">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.billboards.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition-colors" title="Hapus Pengumuman">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <span class="font-semibold block text-[#1B263B]">Belum ada pengumuman billboard</span>
                            <span class="text-xs text-[#5C6B73]">Klik tombol "Tambah Pengumuman Baru" untuk membuat pengumuman pertama.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

