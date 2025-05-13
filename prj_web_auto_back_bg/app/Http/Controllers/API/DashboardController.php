<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ad;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class DashboardController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get dashboard statistics.
     */
    public function stats(Request $request)
    {
        // Vérifier que l'utilisateur est admin
        $this->authorize('viewDashboard');
        
        $stats = [
            'total_users' => User::count(),
            'total_ads' => Ad::count(),
            'active_ads' => Ad::where('status', 'available')->count(),
            'sold_ads' => Ad::where('status', 'sold')->count(),
            'total_transactions' => Transaction::count(),
            'total_revenue' => Transaction::sum('amount'),
            'recent_transactions' => Transaction::with('user', 'ad')
                ->latest()
                ->take(5)
                ->get(),
            'recent_users' => User::latest()
                ->take(5)
                ->get(),
        ];
        
        // Statistiques mensuelles pour les graphiques (nombre d'inscriptions par mois)
        $stats['monthly_users'] = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();
        
        // Statistiques mensuelles des ventes
        $stats['monthly_sales'] = Transaction::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(amount) as revenue'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();
        
        return response()->json($stats);
    }
    
    /**
     * Export data (bonus feature).
     */
    public function export(Request $request)
    {
        // Vérifier que l'utilisateur est admin
        $this->authorize('exportData');
        
        $type = $request->input('type', 'users');
        
        switch ($type) {
            case 'users':
                $data = User::all();
                break;
            case 'ads':
                $data = Ad::all();
                break;
            case 'transactions':
                $data = Transaction::all();
                break;
            default:
                return response()->json(['error' => 'Type d\'export non valide'], 400);
        }
        
        return response()->json([
            'data' => $data,
            'type' => $type,
            'timestamp' => now(),
        ]);
    }
}