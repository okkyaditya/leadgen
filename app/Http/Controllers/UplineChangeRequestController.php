<?php

namespace App\Http\Controllers;

use App\Models\UplineChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UplineChangeRequestController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        
        // Only Supervisor and Manager are allowed
        if (!$currentUser->hasAnyRole(['manager', 'supervisor'])) {
            abort(403, 'Akses ditolak. Menu ini hanya tersedia untuk Supervisor dan Manager.');
        }

        if ($currentUser->hasRole('supervisor')) {
            $supervisorCabang = $currentUser->cabang;

            // Get all Mitra whose branch is the same as the Supervisor's branch
            $mitraQuery = User::where('role', 'mitra')
                ->where(function($q) use ($supervisorCabang) {
                    $q->where('cabang', $supervisorCabang)
                      ->orWhereHas('upline', function($uq) use ($supervisorCabang) {
                          $uq->where('cabang', $supervisorCabang);
                      });
                });

            if ($request->filled('search')) {
                $search = $request->search;
                $mitraQuery->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('upline', function ($uq) use ($search) {
                          $uq->where('nama', 'like', "%{$search}%");
                      });
                });
            }

            $mitras = $mitraQuery->with(['upline', 'uplineRequests' => function($q) {
                    $q->where('status', 'pending')->with('newUpline');
                }])
                ->orderBy('nama')
                ->paginate(10)
                ->withQueryString();

            // Support users in the same branch that can act as the new upline
            $uplines = User::where('role', 'support')
                ->where('cabang', $supervisorCabang)
                ->where('is_active', true)
                ->orderBy('nama')
                ->get(['id', 'nama', 'role', 'cabang']);

            // Get history of requests initiated by this Supervisor
            $historyRequests = UplineChangeRequest::where('requested_by', $currentUser->id)
                ->with(['mitra', 'newUpline', 'approvedBy'])
                ->orderBy('created_at', 'desc')
                ->get();

            return Inertia::render('UplineRequests/Index', [
                'mitras' => $mitras,
                'uplines' => $uplines,
                'historyRequests' => $historyRequests,
                'filters' => $request->only(['search']),
            ]);
        }

        if ($currentUser->hasRole('manager')) {
            // Manager views all pending and past upline change requests
            $query = UplineChangeRequest::query()
                ->with(['mitra.upline', 'newUpline', 'requestedBy', 'approvedBy']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('mitra', function ($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%")
                           ->orWhere('nik', 'like', "%{$search}%");
                    })->orWhereHas('newUpline', function ($q3) use ($search) {
                        $q3->where('nama', 'like', "%{$search}%");
                    })->orWhereHas('requestedBy', function ($q4) use ($search) {
                        $q4->where('nama', 'like', "%{$search}%");
                    });
                });
            }

            $requests = $query->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();

            return Inertia::render('UplineRequests/Index', [
                'requests' => $requests,
                'filters' => $request->only(['search']),
            ]);
        }

        abort(403);
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        if (!$currentUser->hasRole('supervisor')) {
            abort(403, 'Hanya Supervisor yang dapat mengajukan perubahan upline.');
        }

        $supervisorCabang = $currentUser->cabang;

        $request->validate([
            'mitra_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'mitra'),
                function ($attribute, $value, $fail) use ($supervisorCabang) {
                    $mitra = User::find($value);
                    if ($mitra) {
                        $mitraCabang = $mitra->cabang;
                        $uplineCabang = $mitra->upline?->cabang;
                        if ($mitraCabang !== $supervisorCabang && $uplineCabang !== $supervisorCabang) {
                            $fail('Mitra yang dipilih tidak berada di cabang Anda.');
                        }
                    }
                }
            ],
            'new_upline_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'support')->where('cabang', $supervisorCabang)->where('is_active', true),
                function ($attribute, $value, $fail) use ($request) {
                    if ($value == $request->mitra_id) {
                        $fail('Upline baru tidak boleh sama dengan Mitra.');
                        return;
                    }
                    $mitra = User::find($request->mitra_id);
                    if ($mitra && $mitra->supervisor_id == $value) {
                        $fail('Upline baru sudah merupakan upline saat ini dari Mitra tersebut.');
                    }
                }
            ],
        ]);

        UplineChangeRequest::create([
            'mitra_id' => $request->mitra_id,
            'new_upline_id' => $request->new_upline_id,
            'requested_by' => $currentUser->id,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan perubahan upline berhasil diajukan dan menunggu persetujuan Manager.');
    }

    public function update(Request $request, UplineChangeRequest $uplineRequest)
    {
        $currentUser = auth()->user();
        if (!$currentUser->hasRole('manager')) {
            abort(403, 'Hanya Manager yang dapat mengubah status pengajuan.');
        }

        $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        $uplineRequest->update([
            'status' => $request->status,
            'approved_by' => $currentUser->id,
        ]);

        $statusMsg = $request->status === 'approved' ? 'disetujui' : 'ditolak';

        return redirect()->back()->with('success', "Pengajuan perubahan upline berhasil {$statusMsg}.");
    }

    public function destroy(UplineChangeRequest $uplineRequest)
    {
        $currentUser = auth()->user();
        if (!$currentUser->hasRole('manager')) {
            abort(403, 'Hanya Manager yang dapat menghapus pengajuan.');
        }

        $uplineRequest->delete();

        return redirect()->back()->with('success', 'Pengajuan berhasil dihapus.');
    }
}
