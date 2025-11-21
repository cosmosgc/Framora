<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    // Se desejar aplicar middleware auth:
    public function __construct()
    {
        // $this->middleware('auth:sanctum');
    }

    /**
     * GET /api/pedidos
     */
    public function index(Request $request)
    {
        // opcional: paginação, filtro por user, status, etc.
        $query = Pedido::query()->with('user', 'carrinho', 'inventario');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        $perPage = (int) $request->query('per_page', 15);
        return response()->json($query->paginate($perPage));
    }

    /**
     * POST /api/pedidos
     * Espera: user_id (opcional se usar auth), carrinho_id, forma_pagamento, valor_total
     * opcional: items => array of ['foto_id' => int, ...] para popular inventario
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'carrinho_id' => ['nullable', 'integer'], // ajuste se tiver tabela carrinhos
            'forma_pagamento' => ['required', 'string'],
            'valor_total' => ['required', 'numeric'],
            'status_pedido' => ['nullable', Rule::in(['pendente', 'pago', 'cancelado', 'enviado'])],
            'items' => ['nullable', 'array'],
            'items.*.foto_id' => ['required_with:items', 'integer', 'exists:fotos,id'],
        ]);

        $userId = $data['user_id'] ?? Auth::id();

        DB::beginTransaction();

        try {
            $pedido = Pedido::create([
                'user_id' => $userId,
                'carrinho_id' => $data['carrinho_id'] ?? null,
                'forma_pagamento' => $data['forma_pagamento'],
                'valor_total' => $data['valor_total'],
                'status_pedido' => $data['status_pedido'] ?? 'pendente',
            ]);

            // Se foram enviados items (checkout) criamos registros de inventario relacionados
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    Inventario::create([
                        'user_id' => $userId,
                        'foto_id' => $item['foto_id'],
                        'pedido_id' => $pedido->id,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Pedido criado com sucesso',
                'data' => $pedido->load('inventario'),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao criar pedido',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/pedidos/{pedido}
     */
    public function show(Pedido $pedido)
    {
        $pedido->load('user', 'carrinho', 'inventario');
        return response()->json($pedido);
    }

    /**
     * PUT/PATCH /api/pedidos/{pedido}
     */
    public function update(Request $request, Pedido $pedido)
    {
        $data = $request->validate([
            'forma_pagamento' => ['sometimes', 'string'],
            'valor_total' => ['sometimes', 'numeric'],
            'status_pedido' => ['sometimes', Rule::in(['pendente', 'pago', 'cancelado', 'enviado'])],
            'carrinho_id' => ['sometimes', 'nullable', 'integer'],
        ]);

        $pedido->update($data);

        return response()->json([
            'message' => 'Pedido atualizado',
            'data' => $pedido,
        ]);
    }

    /**
     * DELETE /api/pedidos/{pedido}
     */
    public function destroy(Pedido $pedido)
    {
        DB::beginTransaction();
        try {
            // se quiser remover inventário associado:
            $pedido->inventario()->delete();

            $pedido->delete();

            DB::commit();

            return response()->json(['message' => 'Pedido removido'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro ao remover pedido', 'error' => $e->getMessage()], 500);
        }
    }
}
