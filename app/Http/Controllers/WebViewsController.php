<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Galeria;
use App\Models\Banner;
use App\Models\Inventario;
use Illuminate\Http\Request;

class WebViewsController extends Controller
{
    //
    public function GaleriaIndex()
    {
        return view('galerias.index');
    }

    /**
     * Página de detalhes da galeria
     */
    public function GaleriaShow($id)
    {
        $galeria = \App\Models\Galeria::with(['categoria:id,nome', 'banner', 'fotos'])
            ->find($id);

        if (!$galeria) {
            abort(404, 'Galeria não encontrada');
        }
        // dd($galeria);
        return view('galerias.show', compact('galeria'));
    }

    public function GaleriaCreate()
    {
        return view('galerias.create');
    }
    public function GaleriaEdit($id)
    {
        $galeria = Galeria::findOrFail($id);

        // Verify ownership
        if (!auth()->check() || $galeria->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $categorias = Categoria::select('id','nome')->get();
        $banners = Banner::select('id','imagem')->get(); // ou como está sua tabela

        return view('galerias.edit', compact('galeria','categorias','banners'));
    }

    public function FotosIndex()
    {
        return view('fotos.index');
    }

    /**
     * Página de detalhes da galeria
     */
    public function FotosShow($id)
    {
        return view('fotos.show', compact('id'));
    }

    public function CategoriaIndex()
    {
        // Eager-load banner relation to avoid N+1 queries
        $categorias = Categoria::with('banner')->get();

        return view('categorias.index', compact('categorias'));
    }
    public function CategoriaShow($id)
    {
        $categoria = Categoria::with('galerias.banner')->findOrFail($id);

        return view('categorias.show', compact('categoria'));
    }
    public function inventarioIndex(Request $request)
    {
        $perPage = $request->query('per_page', null); // if null, get all (careful with huge sets)
        $order = $request->query('order', 'newest');

        $query = Inventario::with(['foto', 'pedido'])
            ->when($order === 'oldest', fn($q)=> $q->orderBy('adquirido_em', 'asc'), fn($q)=> $q->orderBy('adquirido_em', 'desc'));

        // se quer paginação ativar:
        if ($perPage) {
            $items = $query->paginate($perPage);
            // Quando usar paginate, front-end precisa adaptar; aqui devolvemos o collection por simplicidade.
            $itemsCollection = $items->items();
        } else {
            $itemsCollection = $query->get();
        }

        // Normalizar e agrupar por nome de galeria legível
        $grouped = [];
        $galeriasLookup = [];

        foreach ($itemsCollection as $item) {
            $foto = $item->foto ?? null;

            // heurística para nome da galeria
            $galName = 'Sem galeria';
            if ($foto) {
                if (isset($foto->galeria) && $foto->galeria) {
                    // pode ser string ou objeto
                    if (is_string($foto->galeria)) {
                        $galName = $foto->galeria;
                    } elseif (is_object($foto->galeria) || is_array($foto->galeria)) {
                        $galName = $foto->galeria->nome ?? $foto->galeria['nome'] ?? $foto->galeria->name ?? $foto->galeria['name'] ?? ('Galeria ' . ($foto->galeria->id ?? ($foto->galeria['id'] ?? '')));
                    }
                } elseif (isset($foto->galeria_nome) && $foto->galeria_nome) {
                    $galName = $foto->galeria_nome;
                } elseif (isset($foto->galerias) && $foto->galerias) {
                    // pegar primeiro
                    $g0 = is_array($foto->galerias) ? ($foto->galerias[0] ?? null) : $foto->galerias;
                    if ($g0) {
                        $galName = is_string($g0) ? $g0 : ($g0->nome ?? $g0['nome'] ?? 'Galeria ' . ($g0->id ?? ''));
                    }
                }
            }

            $grouped[$galName][] = $item;
            $galeriasLookup[$galName] = true;
        }

        // passar tanto o grouped para render server-side quanto o raw JSON para filtros client-side leves
        return view('inventario.index', [
            'grouped' => $grouped,
            'galerias' => array_keys($galeriasLookup),
            'itemsJson' => json_encode($itemsCollection->map(function($it){
                // transformar em array serializável apenas com o que precisamos no cliente
                return [
                    'id' => $it->id,
                    'foto_id' => $it->foto_id,
                    'pedido_id' => $it->pedido_id,
                    'created_at' => $it->created_at?->toDateTimeString(),
                    'foto' => $it->foto ? [
                        'caminho_thumb' => $it->foto->caminho_thumb ?? null,
                        'caminho_foto' => $it->foto->caminho_foto ?? null,
                        'caminho_original' => $it->foto->caminho_original ?? null,
                        'titulo' => $it->foto->titulo ?? $it->foto->nome ?? null,
                        // passe minimal galeria info
                        'galeria' => is_string($it->foto->galeria) ? $it->foto->galeria : ($it->foto->galeria->nome ?? $it->foto->galeria_nome ?? null),
                        'created_at' => $it->foto->created_at?->toDateTimeString() ?? null,
                    ] : null,
                    'pedido' => $it->pedido ? [
                        'status_pedido' => $it->pedido->status_pedido ?? null,
                        'forma_pagamento' => $it->pedido->forma_pagamento ?? null,
                        'valor_total' => $it->pedido->valor_total ?? null,
                        'id' => $it->pedido->id ?? null,
                    ] : null,
                ];
            })->toArray(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)
        ]);
    }
    

}
