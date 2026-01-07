<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeria;
use App\Models\Foto;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Basic counts
        $totalGalerias = Galeria::count();
        $totalFotos    = Foto::count();
        $totalPedidos  = Pedido::count();

        // Receita total (somatório)
        $receitaTotal = Pedido::sum('valor_total');

        // Últimos 10 pedidos
        $ultimosPedidos = Pedido::with('user')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // Pedidos por mês (últimos 12 meses)
        $pedidosPorMes = Pedido::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(valor_total) as valor')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->limit(12)
            ->get();

        return view('admin.dashboard', [
            'totalGalerias'  => $totalGalerias,
            'totalFotos'     => $totalFotos,
            'totalPedidos'   => $totalPedidos,
            'receitaTotal'   => $receitaTotal,
            'ultimosPedidos' => $ultimosPedidos,
            'pedidosPorMes'  => $pedidosPorMes,
        ]);
    }
}
