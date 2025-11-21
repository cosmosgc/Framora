<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    // public function __construct() { $this->middleware('auth:sanctum'); }

    /**
     * GET /api/inventario
     */
    public function index(Request $request)
    {
        $query = Inventario::query()->with('user', 'foto', 'pedido');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        $perPage = (int) $request->query('per_page', 15);
        return response()->json($query->paginate($perPage));
    }

    /**
     * POST /api/inventario
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'foto_id' => ['required', 'integer', 'exists:fotos,id'],
            'pedido_id' => ['nullable', 'integer', 'exists:pedidos,id'],
        ]);

        $data['user_id'] = $data['user_id'] ?? Auth::id();

        $inventario = Inventario::create($data);

        return response()->json(['message' => 'Inventário criado', 'data' => $inventario], 201);
    }

    /**
     * GET /api/inventario/{inventario}
     */
    public function show(Inventario $inventario)
    {
        $inventario->load('user', 'foto', 'pedido');
        return response()->json($inventario);
    }

    /**
     * PUT/PATCH /api/inventario/{inventario}
     */
    public function update(Request $request, Inventario $inventario)
    {
        $data = $request->validate([
            'foto_id' => ['sometimes', 'integer', 'exists:fotos,id'],
            'pedido_id' => ['sometimes', 'nullable', 'integer', 'exists:pedidos,id'],
            'user_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
        ]);

        $inventario->update($data);

        return response()->json(['message' => 'Inventário atualizado', 'data' => $inventario]);
    }

    /**
     * DELETE /api/inventario/{inventario}
     */
    public function destroy(Inventario $inventario)
    {
        $inventario->delete();
        return response()->json(['message' => 'Registro de inventário removido']);
    }
}
