<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CabangController extends Controller
{
    public function index(Request $request)
    {
        // Only admin can view branches
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $query = Cabang::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $cabangs = $query->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Cabang/Index', [
            'cabangs' => $cabangs,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'nama' => 'required|string|max:255|unique:cabangs,nama',
        ]);

        Cabang::create([
            'nama' => $request->nama,
        ]);

        return redirect()->back()->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function update(Request $request, Cabang $cabang)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'nama' => 'required|string|max:255|unique:cabangs,nama,' . $cabang->id,
        ]);

        $cabang->update([
            'nama' => $request->nama,
        ]);

        return redirect()->back()->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Cabang $cabang)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $cabang->delete();

        return redirect()->back()->with('success', 'Cabang berhasil dihapus.');
    }
}
