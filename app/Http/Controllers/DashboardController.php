<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

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

        // Initialize Queries
        $mitraQuery = User::where('role', 'mitra');
        $userQuery = User::whereIn('role', ['manager', 'supervisor', 'support']);
        $leadQuery = Lead::query();

        // If Supervisor, restrict stats to branch-wide scope
        if ($user->hasRole('supervisor')) {
            $branch = $user->cabang;
            $mitraQuery->where(function($q) use ($branch) {
                $q->where('cabang', $branch)
                  ->orWhereHas('upline', function($uq) use ($branch) {
                      $uq->where('cabang', $branch);
                  });
            });
            $leadQuery->where(function($q) use ($branch) {
                $q->where('cabang', $branch)
                  ->orWhereHas('sourceMitra', function($mq) use ($branch) {
                      $mq->where('cabang', $branch)
                        ->orWhereHas('upline', function($uq) use ($branch) {
                            $uq->where('cabang', $branch);
                        });
                  });
            });
        }

        // If Support, restrict to their immediate downline Mitra and Leads
        if ($user->hasRole('support')) {
            $mitraQuery->where('supervisor_id', $user->id);
            $leadQuery->where(function($q) use ($user) {
                $q->where('input_by', $user->id)
                  ->orWhere('source_mitra_id', $user->id)
                  ->orWhereHas('sourceMitra', function($q2) use ($user) {
                      $q2->where('supervisor_id', $user->id);
                  });
            });
        }

        // If Mitra, restrict to their own Leads
        if ($user->hasRole('mitra')) {
            $mitraQuery->where('id', $user->id);
            $leadQuery->where('source_mitra_id', $user->id);
        }

        $totalMitra = $mitraQuery->count();
        $mitraAktif = (clone $mitraQuery)->where('is_active', true)->count();
        $totalUser = $userQuery->count();
        $totalLeads = (clone $leadQuery)->count();
        $potentialNtf = $leadQuery->sum('ntf');
        $ticketSize = $leadQuery->avg('ntf') ?? 0;

        // 2. Monthly Lead Chart Data (current year or selected year)
        $year = $request->input('year', now()->format('Y'));
        $monthlyData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = (clone $leadQuery)
                ->whereMonth('created_at', $m)
                ->whereYear('created_at', $year)
                ->count();
        }

        // 3. Top Mitra (ordered by leads count)
        $topMitraQuery = User::where('role', 'mitra');
        
        if ($user->hasRole('supervisor')) {
            $branch = $user->cabang;
            $topMitraQuery->where(function($q) use ($branch) {
                $q->where('cabang', $branch)
                  ->orWhereHas('upline', function($uq) use ($branch) {
                      $uq->where('cabang', $branch);
                  });
            });
        } elseif ($user->hasRole('support')) {
            $topMitraQuery->where('supervisor_id', $user->id);
        } elseif ($user->hasRole('mitra')) {
            $branch = $user->cabang;
            $topMitraQuery->where('cabang', $branch);
        }

        $topMitras = $topMitraQuery->withCount('mitraLeads as leads_count')
            ->orderBy('leads_count', 'desc')
            ->limit(5)
            ->get(['id', 'nama', 'email', 'cabang']);

        $topSupports = collect();
        if ($user->hasRole('supervisor')) {
            $branch = $user->cabang;
            $topSupports = User::where('role', 'support')
                ->where('cabang', $branch)
                ->withCount('leads')
                ->orderBy('leads_count', 'desc')
                ->limit(5)
                ->get(['id', 'nama', 'email', 'cabang']);
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalMitra' => $totalMitra,
                'mitraAktif' => $mitraAktif,
                'totalUser' => $totalUser,
                'totalLeads' => $totalLeads,
                'potentialNtf' => $potentialNtf,
                'ticketSize' => round($ticketSize),
            ],
            'chart' => [
                'labels' => $months,
                'data' => $monthlyData,
                'year' => (int)$year,
            ],
            'topMitras' => $topMitras,
            'topSupports' => $topSupports,
        ]);
    }
}
