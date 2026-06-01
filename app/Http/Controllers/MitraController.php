<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Gate;

class MitraController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAnyMitra', User::class);

        $currentUser = auth()->user();

        // Auto-deactivate Mitra who have no login activity for 30 days
        $thirtyDaysAgo = \Carbon\Carbon::now()->subDays(30);
        $inactiveMitras = User::where('role', 'mitra')
            ->where('is_active', true)
            ->where(function ($query) use ($thirtyDaysAgo) {
                $query->where('last_login_at', '<', $thirtyDaysAgo)
                      ->orWhere(function ($q) use ($thirtyDaysAgo) {
                          $q->whereNull('last_login_at')
                            ->where('created_at', '<', $thirtyDaysAgo);
                      });
            })
            ->get();

        foreach ($inactiveMitras as $mitra) {
            $mitra->update([
                'is_active' => false,
                'is_active_reason' => 'Tidak Ada Aktifitas Selama 30 Hari'
            ]);
        }

        $query = User::query()->where('role', 'mitra')->with('upline');

        // Supervisor or Support are locked to viewing Mitras in their own branch
        if ($currentUser->hasAnyRole(['supervisor', 'support'])) {
            $branch = $currentUser->cabang;
            $query->where(function($q) use ($branch) {
                $q->where('cabang', $branch)
                  ->orWhereHas('upline', function($uq) use ($branch) {
                      $uq->where('cabang', $branch);
                  });
            });
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%");
            });
        }

        if ($request->filled('produk')) {
            $produk = $request->produk;
            $query->whereHas('leads', function ($q) use ($produk) {
                $q->where('produk', $produk);
            });
        }

        $mitras = $query->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        // Uplines dropdown (supervisor or support)
        $uplinesQuery = User::whereIn('role', ['supervisor', 'support'])
            ->where('is_active', true);

        if ($currentUser->hasRole('manager') && $currentUser->cabang) {
            $uplinesQuery->where('cabang', $currentUser->cabang);
        }

        $uplines = $uplinesQuery->orderBy('nama')
            ->get(['id', 'nama', 'role', 'cabang']);

        $products = ['NDF Car', 'NDF Motor', 'NDF Property', 'Machinery', 'Heavy Equipment', 'DF Mobil', 'DF Motor'];

        return Inertia::render('Mitra/Index', [
            'mitras' => $mitras,
            'uplines' => $uplines,
            'products' => $products,
            'filters' => $request->only(['search', 'produk']),
        ]);
    }

    public function store(\App\Http\Requests\StoreMitraRequest $request)
    {
        $currentUser = auth()->user();

        // If supervisor/support is creating, upline_id is automatically their own ID
        if ($currentUser->hasAnyRole(['supervisor', 'support'])) {
            $uplineId = $currentUser->id;
        } else {
            $uplineId = $request->supervisor_id;
        }

        User::create([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mitra',
            'profesi' => $request->profesi,
            'tanggal_lahir' => $request->tanggal_lahir,
            'domisili' => $request->domisili,
            'supervisor_id' => $uplineId,
            'is_active' => $currentUser->hasRole('support') ? true : $request->is_active,
            'is_active_reason' => !$request->is_active && !$currentUser->hasRole('support') ? $request->is_active_reason : null,
        ]);

        return redirect()->back()->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function update(\App\Http\Requests\UpdateMitraRequest $request, User $mitra)
    {
        $currentUser = auth()->user();

        if ($currentUser->hasAnyRole(['supervisor', 'support'])) {
            $uplineId = $currentUser->id;
        } else {
            $uplineId = $request->supervisor_id;
        }

        $data = [
            'nama' => $request->nama,
            'nik' => $request->nik,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'profesi' => $request->profesi,
            'tanggal_lahir' => $request->tanggal_lahir,
            'domisili' => $request->domisili,
            'supervisor_id' => $uplineId,
        ];

        // Support cannot modify active status or reason
        if (!$currentUser->hasRole('support')) {
            $data['is_active'] = $request->is_active;
            $data['is_active_reason'] = !$request->is_active ? $request->is_active_reason : null;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $oldSupervisorId = $mitra->supervisor_id;

        $mitra->update($data);

        // If the upline has changed and the currentUser is a manager or admin, record it directly as approved
        if ($oldSupervisorId !== $uplineId) {
            \App\Models\UplineChangeRequest::create([
                'mitra_id' => $mitra->id,
                'requested_by' => $currentUser->id,
                'new_upline_id' => $uplineId,
                'status' => 'approved',
                'approved_by' => $currentUser->id,
            ]);
        }

        return redirect()->back()->with('success', 'Mitra berhasil diperbarui.');
    }

    public function destroy(User $mitra)
    {
        Gate::authorize('deleteMitra', $mitra);

        $mitra->delete();

        return redirect()->back()->with('success', 'Mitra berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $currentUser = auth()->user();
        $query = User::query()->where('role', 'mitra')->with('upline');

        if ($currentUser->hasAnyRole(['supervisor', 'support'])) {
            $query->where('supervisor_id', $currentUser->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%");
            });
        }

        if ($request->filled('produk')) {
            $produk = $request->produk;
            $query->whereHas('leads', function ($q) use ($produk) {
                $q->where('produk', $produk);
            });
        }

        $mitras = $query->orderBy('nama', 'asc')->get();

        return Excel::download(new \App\Exports\MitrasExport($mitras), 'mitra_export_' . now()->format('Ymd_His') . '.xlsx');
    }
}
