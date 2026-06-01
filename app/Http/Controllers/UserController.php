<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $query = User::query()->where('role', '!=', 'mitra');

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

        if ($request->filled('cabang')) {
            $query->where('cabang', $request->cabang);
        }

        $users = $query->with('supervisor')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $cabangs = Cabang::orderBy('nama')->pluck('nama');

        // Eligible supervisors list for support role assignment
        $supervisors = User::where('role', 'supervisor')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'cabang']);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'cabangs' => $cabangs,
            'supervisors' => $supervisors,
            'filters' => $request->only(['search', 'cabang']),
        ]);
    }

    public function store(\App\Http\Requests\StoreUserRequest $request)
    {
        User::create([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'role' => $request->role,
            'cabang' => $request->cabang,
            'hire_date' => $request->hire_date,
            'password' => Hash::make($request->password),
            'is_active' => $request->is_active,
            'supervisor_id' => $request->role === 'support' ? $request->supervisor_id : null,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(\App\Http\Requests\UpdateUserRequest $request, User $user)
    {
        $data = [
            'nama' => $request->nama,
            'nik' => $request->nik,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'role' => $request->role,
            'cabang' => $request->cabang,
            'hire_date' => $request->hire_date,
            'is_active' => $request->is_active,
            'supervisor_id' => $request->role === 'support' ? $request->supervisor_id : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
