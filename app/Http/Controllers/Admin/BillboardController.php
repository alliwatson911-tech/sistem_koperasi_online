<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillboardAnnouncement;
use Illuminate\Http\Request;

class BillboardController extends Controller
{
    public function index()
    {
        $billboards = BillboardAnnouncement::orderBy('order', 'asc')->orderBy('id', 'asc')->get();
        return view('admin.billboards.index', compact('billboards'));
    }

    public function create()
    {
        $billboard = new BillboardAnnouncement([
            'badge_color' => 'warning',
            'order' => BillboardAnnouncement::max('order') + 1,
            'is_active' => true,
        ]);
        return view('admin.billboards.form', compact('billboard'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tab_title' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'badge_text' => 'required|string|max:100',
            'badge_color' => 'required|in:warning,success,info,gray,danger,primary',
            'meta_text' => 'nullable|string|max:150',
            'content' => 'required|string',
            'status_text' => 'nullable|string|max:150',
            'link_url' => 'nullable|string|max:255',
            'link_text' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 1;

        BillboardAnnouncement::create($validated);

        return redirect()->route('admin.billboards.index')
            ->with('success', 'Pengumuman billboard resmi berhasil ditambahkan!');
    }

    public function edit(BillboardAnnouncement $billboard)
    {
        return view('admin.billboards.form', compact('billboard'));
    }

    public function update(Request $request, BillboardAnnouncement $billboard)
    {
        $validated = $request->validate([
            'tab_title' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'badge_text' => 'required|string|max:100',
            'badge_color' => 'required|in:warning,success,info,gray,danger,primary',
            'meta_text' => 'nullable|string|max:150',
            'content' => 'required|string',
            'status_text' => 'nullable|string|max:150',
            'link_url' => 'nullable|string|max:255',
            'link_text' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? $billboard->order;

        $billboard->update($validated);

        return redirect()->route('admin.billboards.index')
            ->with('success', 'Pengumuman billboard resmi berhasil diperbarui!');
    }

    public function destroy(BillboardAnnouncement $billboard)
    {
        $billboard->delete();
        return redirect()->route('admin.billboards.index')
            ->with('success', 'Pengumuman billboard berhasil dihapus.');
    }

    public function toggle(BillboardAnnouncement $billboard)
    {
        $billboard->is_active = !$billboard->is_active;
        $billboard->save();

        $statusStr = $billboard->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Pengumuman '{$billboard->tab_title}' berhasil {$statusStr}.");
    }
}

