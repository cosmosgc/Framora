<?php

namespace App\Http\Controllers;

use App\Models\Carrinho;
use App\Models\CarrinhoFoto;
use App\Models\Foto;
use App\Models\Pedido;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CarrinhoController extends Controller
{
    // Retorna view (web) ou JSON (api)
    public function index(Request $request)
    {
        $user = Auth::user();

        // carrega ou cria carrinho do user
        $carrinho = Carrinho::firstOrCreate(['user_id' => $user->id]);

        $carrinho->load(['fotos.foto']);
        $carrinho->load('fotos.foto.galeria');


        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($carrinho);
        }

        return view('carrinho.index', compact('carrinho'));
    }

    // Adiciona foto ao carrinho (web form ou api)
    public function store(Request $request)
    {
        $user = Auth::user();
        $fotoId = $request->input('foto_id');

        if (! $fotoId) {
            abort(400, 'foto_id is required');
        }

        $foto = Foto::find($fotoId);
        if (! $foto) {
            abort(404, 'Foto not found');
        }

        // 1) Verifica se usuário já possui essa foto no inventário
        $owned = Inventario::where('user_id', $user->id)
                    ->where('foto_id', $fotoId)
                    ->exists();

        if ($owned) {
            // Se já tem no inventário, não permitir adicionar ao carrinho
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Usuário já possui esta foto no inventário'], 409);
            }
            return redirect()->route('carrinho.index')->with('info', 'Você já possui esta foto no seu inventário.');
        }

        // 2) Busca/Cria carrinho
        $carrinho = Carrinho::firstOrCreate(['user_id' => $user->id]);

        // Preço pode vir do request (preco atual) ou do modelo foto->preco
        $preco = $request->input('preco', $foto->preco ?? 0);

        // 3) Não permitir duplicata no carrinho
        $existing = $carrinho->fotos()->where('foto_id', $fotoId)->first();
        if ($existing) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Foto já no carrinho', 'item' => $existing], 200);
            }
            return redirect()->route('carrinho.index')->with('info', 'Foto já está no carrinho.');
        }

        // 4) Criar item no carrinho
        $item = $carrinho->fotos()->create([
            'foto_id' => $fotoId,
            'preco' => $preco,
            'quantidade' => 1,
        ]);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($item, 201);
        }

        return redirect()->route('carrinho.index')->with('success', 'Foto adicionada ao carrinho.');
    }


    // Remove item (web + api)
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $carrinho = Carrinho::where('user_id', $user->id)->first();

        if (! $carrinho) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Carrinho não encontrado'], 404);
            }
            return redirect()->route('carrinho.index')->with('error', 'Carrinho não encontrado');
        }
        if ($carrinho->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $item = $carrinho->fotos()->where('id', $id)->first();
        if (! $item) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Item não encontrado no carrinho'], 404);
            }
            return redirect()->route('carrinho.index')->with('error', 'Item não encontrado no carrinho');
        }

        $item->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Item removido']);
        }

        return redirect()->route('carrinho.index')->with('success', 'Item removido do carrinho.');
    }

    // API: adicionar foto via endpoint dedicado (usa mesmo comportamento do store)
    public function addFoto(Request $request, $id)
    {
        // $id é id do carrinho
        $carrinho = Carrinho::find($id);
        if (! $carrinho) {
            return response()->json(['message' => 'Carrinho não encontrado'], 404);
        }
        if ($carrinho->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $fotoId = $request->input('foto_id');
        if (! $fotoId) {
            return response()->json(['message' => 'foto_id é obrigatório'], 400);
        }

        $foto = Foto::find($fotoId);
        if (! $foto) {
            return response()->json(['message' => 'Foto não encontrada'], 404);
        }

        $existing = $carrinho->fotos()->where('foto_id', $fotoId)->first();
        if ($existing) {
            return response()->json(['message' => 'Foto já adicionada', 'item' => $existing], 200);
        }

        $item = $carrinho->fotos()->create([
            'foto_id' => $fotoId,
            'preco' => $request->input('preco', $foto->preco ?? 0),
            'quantidade' => 1,
        ]);

        return response()->json($item, 201);
    }

    // API: atualizar preco/meta do item no carrinho
    public function updateFoto(Request $request, $id, $fid)
    {
        $carrinho = Carrinho::find($id);
        if (! $carrinho) {
            return response()->json(['message' => 'Carrinho não encontrado'], 404);
        }

        $item = $carrinho->fotos()->where('id', $fid)->first();
        if (! $item) {
            return response()->json(['message' => 'Item não encontrado'], 404);
        }

        $item->fill($request->only(['preco', 'quantidade']));
        $item->save();

        return response()->json($item);
    }

    // API: remover foto do carrinho por id
    public function removeFoto(Request $request, $id, $fid)
    {
        $carrinho = Carrinho::find($id);
        if (! $carrinho) {
            return response()->json(['message' => 'Carrinho não encontrado'], 404);
        }

        $item = $carrinho->fotos()->where('id', $fid)->first();
        if (! $item) {
            return response()->json(['message' => 'Item não encontrado'], 404);
        }

        $item->delete();
        return response()->json(['message' => 'Item removido']);
    }

    // Checkout web: cria pedido, move itens para inventario, limpa carrinho
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $carrinho = Carrinho::where('user_id', $user->id)->with('fotos.foto')->first();

        if (! $carrinho || $carrinho->fotos->isEmpty()) {
            return redirect()->route('carrinho.index')->with('error', 'Carrinho vazio.');
        }

        // validação simples do método de pagamento
        $request->validate([
            'forma_pagamento' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $valorTotal = $carrinho->fotos->sum(function ($item) {
                return floatval($item->preco) * max(1, intval($item->quantidade ?? 1));
            });

            // Cria pedido
            $pedido = Pedido::create([
                'user_id' => $user->id,
                'carrinho_id' => $carrinho->id,
                'status_pedido' => 'pendente', // 'pendente', 'pago', 'cancelado'
                'forma_pagamento' => $request->input('forma_pagamento'),
                'valor_total' => $valorTotal,
            ]);

            // Simular pagamento: Aqui você integraria com gateway.
            // Para demo, vamos marcar como 'pago' imediatamente.
            $pedido->status_pedido = 'pago';
            $pedido->save();

            // Mover itens para inventario
            foreach ($carrinho->fotos as $item) {
                Inventario::create([
                    'user_id' => $user->id,
                    'foto_id' => $item->foto_id,
                    'pedido_id' => $pedido->id,
                ]);
            }

            // limpar carrinho (remover items)
            $carrinho->fotos()->delete();

            DB::commit();

            return redirect()->route('carrinho.index')->with('success', 'Compra realizada com sucesso! Itens adicionados ao inventário.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no checkout: '.$e->getMessage());
            return redirect()->route('carrinho.index')->with('error', 'Erro ao processar o pagamento. Tente novamente.');
        }
    }

    // API checkout (id do carrinho)
    public function apiCheckout(Request $request, $id)
    {
        $carrinho = Carrinho::with('fotos.foto')->find($id);
        if (! $carrinho) {
            return response()->json(['message' => 'Carrinho não encontrado'], 404);
        }

        if ($carrinho->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }
        
        if ($carrinho->fotos->isEmpty()) {
            return response()->json(['message' => 'Carrinho vazio'], 400);
        }

        $request->validate([
            'forma_pagamento' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $valorTotal = $carrinho->fotos->sum(fn($item) => floatval($item->preco) * max(1, intval($item->quantidade ?? 1)));

            $pedido = Pedido::create([
                'user_id' => $carrinho->user_id,
                'carrinho_id' => $carrinho->id,
                'status_pedido' => 'pendente',
                'forma_pagamento' => $request->input('forma_pagamento'),
                'valor_total' => $valorTotal,
            ]);

            // marcar pago (simulação)
            $pedido->status_pedido = 'pago';
            $pedido->save();

            foreach ($carrinho->fotos as $item) {
                Inventario::create([
                    'user_id' => $carrinho->user_id,
                    'foto_id' => $item->foto_id,
                    'pedido_id' => $pedido->id,
                ]);
            }

            $carrinho->fotos()->delete();

            DB::commit();

            return response()->json(['message' => 'Compra realizada', 'pedido' => $pedido], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API checkout error: '.$e->getMessage());
            return response()->json(['message' => 'Erro ao processar compra'], 500);
        }
    }
}
