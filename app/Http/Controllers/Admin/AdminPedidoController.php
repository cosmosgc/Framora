<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\CarrinhoFoto;
use App\Models\Inventario;

class AdminPedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with('user')
            ->orderByDesc('id')
            ->get()
            ->map(function ($pedido) {
                [$itens, $fonte] = $this->getPedidoItens($pedido);

                $pedido->calculated_total = $this->calculateTotalFromItens($itens);
                $pedido->source = $fonte; // carrinho | inventario

                return $pedido;
            });

        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('user');

        [$itens, $fonte] = $this->getPedidoItens($pedido);

        $totalCalculado = $this->calculateTotalFromItens($itens);

        return view('admin.pedidos.show', [
            'pedido' => $pedido,
            'itens' => $itens,
            'totalCalculado' => $totalCalculado,
            'source' => $fonte,
        ]);
    }

    /**
     * Decide whether to load items from carrinho or inventario
     */
    protected function getPedidoItens(Pedido $pedido): array
    {
        // 1️⃣ Try carrinho
        if ($pedido->carrinho_id) {
            $carrinhoItens = CarrinhoFoto::with('foto.galeria')
                ->where('carrinho_id', $pedido->carrinho_id)
                ->get();

            if ($carrinhoItens->isNotEmpty()) {
                return [$carrinhoItens, 'carrinho'];
            }
        }

        // 2️⃣ Fallback to inventario
        $inventarioItens = Inventario::with('foto.galeria')
            ->where('pedido_id', $pedido->id)
            ->get();

        return [$inventarioItens, 'inventario'];
    }

    /**
     * Calculate total using galeria.valor_foto
     */
    protected function calculateTotalFromItens($itens): float
    {
        return $itens->sum(function ($item) {
            $valor = $item->foto->galeria->valor_foto ?? 0;
            $quantidade = property_exists($item, 'quantidade')
                ? max(1, $item->quantidade)
                : 1;

            return $valor * $quantidade;
        });
    }
}
