<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Cabang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadController extends Controller
{
    private function getBaseQuery()
    {
        $user = auth()->user();
        $query = Lead::query()->with(['inputBy', 'sourceMitra']);

        // Supervisor views all leads in their branch
        if ($user->hasRole('supervisor')) {
            $branch = $user->cabang;
            $query->where(function($q) use ($branch) {
                $q->where('cabang', $branch)
                  ->orWhereHas('sourceMitra', function($mq) use ($branch) {
                      $mq->where('cabang', $branch)
                        ->orWhereHas('upline', function($uq) use ($branch) {
                            $uq->where('cabang', $branch);
                        });
                  });
            });
        }

        // Support only views their own input and downline Mitra leads
        if ($user->hasRole('support')) {
            $query->where(function($q) use ($user) {
                $q->where('input_by', $user->id)
                  ->orWhereHas('sourceMitra', function($q2) use ($user) {
                      $q2->where('supervisor_id', $user->id);
                  });
            });
        }

        // Mitra only views their own leads
        if ($user->hasRole('mitra')) {
            $query->where('source_mitra_id', $user->id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->getBaseQuery();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('domisili', 'like', "%{$search}%");
            });
        }

        if ($request->filled('cabang')) {
            $query->where('cabang', $request->cabang);
        }

        if ($request->filled('produk')) {
            $query->where('produk', $request->produk);
        }

        $leads = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $cabangs = Cabang::orderBy('nama')->pluck('nama');

        $products = \App\Models\Lead::PRODUCTS;

        $user = auth()->user();
        $mitraQuery = User::where('role', 'mitra')->where('is_active', true);
        
        if ($user->hasRole('supervisor')) {
            $branch = $user->cabang;
            $mitraQuery->where(function($q) use ($branch) {
                $q->where('cabang', $branch)
                  ->orWhereHas('upline', function($uq) use ($branch) {
                      $uq->where('cabang', $branch);
                  });
            });
        } elseif ($user->hasRole('support')) {
            $mitraQuery->where('supervisor_id', $user->id);
        }
        
        $mitras = $mitraQuery->orderBy('nama')->get(['id', 'nama', 'cabang']);

        return Inertia::render('Leads/Index', [
            'leads' => $leads,
            'cabangs' => $cabangs,
            'products' => $products,
            'mitras' => $mitras,
            'filters' => $request->only(['search', 'cabang', 'produk']),
        ]);
    }

    public function store(\App\Http\Requests\StoreLeadRequest $request)
    {
        Lead::create([
            'nama' => $request->nama,
            'telepon' => $request->telepon,
            'nik' => $request->nik,
            'produk' => $request->produk,
            'tipe_lead' => $request->tipe_lead,
            'ntf' => $request->ntf,
            'unit' => $request->unit,
            'no_unit' => $request->no_unit,
            'domisili' => $request->domisili,
            'owner_type' => 'App\Models\User',
            'owner_id' => auth()->id(),
            'input_by' => auth()->id(),
            'source_mitra_id' => auth()->user()->hasRole('mitra') ? auth()->id() : $request->source_mitra_id,
        ]);

        return redirect()->back()->with('success', 'Lead berhasil dibuat.');
    }

    public function update(\App\Http\Requests\UpdateLeadRequest $request, Lead $lead)
    {
        $lead->update([
            'nama' => $request->nama,
            'telepon' => $request->telepon,
            'nik' => $request->nik,
            'produk' => $request->produk,
            'tipe_lead' => $request->tipe_lead,
            'ntf' => $request->ntf,
            'unit' => $request->unit,
            'no_unit' => $request->no_unit,
            'domisili' => $request->domisili,
            'source_mitra_id' => auth()->user()->hasRole('mitra') ? auth()->id() : $request->source_mitra_id,
        ]);

        return redirect()->back()->with('success', 'Lead berhasil diperbarui.');
    }

    public function destroy(Lead $lead)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $lead);

        $lead->delete();

        return redirect()->back()->with('success', 'Lead berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $query = $this->getBaseQuery();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('domisili', 'like', "%{$search}%");
            });
        }

        if ($request->filled('cabang')) {
            $query->where('cabang', $request->cabang);
        }

        if ($request->filled('produk')) {
            $query->where('produk', $request->produk);
        }

        $leads = $query->orderBy('created_at', 'desc')->get();

        return Excel::download(new \App\Exports\LeadsExport($leads), 'leads_export_' . now()->format('Ymd_His') . '.xlsx');
    }
}
