<?php

namespace App\Http\Controllers;

use App\Models\Galeria;
use App\Models\Categoria;
use app\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class GaleriaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Inclui busca, filtros, paginação e ordenação.
     */
    public function index(Request $request)
    {
        $search       = $request->input('search');
        $categoria_id = $request->input('categoria_id');
        $ativo        = $request->input('ativo');
        $sortBy       = $request->input('sort_by', 'id');
        $sortOrder    = $request->input('sort_order', 'desc');
        $perPage      = $request->input('per_page', 15);

        $query = Galeria::query()
            ->with(['categoria:id,nome', 'banner:id,titulo,imagem'])
            ->select('galerias.*');

        // 🔍 Busca textual
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%")
                  ->orWhere('local', 'like', "%{$search}%");
            });
        }

        // 🎯 Filtros
        if (!empty($categoria_id)) {
            $query->where('categoria_id', $categoria_id);
        }

        if ($ativo !== null && $ativo !== '') {
            $query->where('ativo', (bool) $ativo);
        }

        // 📊 Ordenação
        if (in_array($sortBy, ['id', 'nome', 'data', 'criado_em'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // 📄 Paginação
        $galerias = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'filters' => [
                'search'       => $search,
                'categoria_id' => $categoria_id,
                'ativo'        => $ativo,
                'sort_by'      => $sortBy,
                'sort_order'   => $sortOrder,
                'per_page'     => $perPage,
            ],
            'data' => $galerias->items(),
            'meta' => [
                'current_page' => $galerias->currentPage(),
                'last_page'    => $galerias->lastPage(),
                'per_page'     => $galerias->perPage(),
                'total'        => $galerias->total(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::select('id', 'nome')->get();

        return response()->json([
            'success' => true,
            'categorias' => $categorias,
            'message' => 'Use POST /api/galerias para criar uma nova galeria.'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categoria_id'  => 'required|integer|exists:categorias,id',
            'nome'          => 'required|string|max:255',
            'descricao'     => 'nullable|string',
            'local'         => 'nullable|string|max:255',
            'data'          => 'nullable|date',
            'tempo_duracao' => 'nullable|string|max:255',
            'valor_foto'    => 'required|numeric|min:0',
            'banner'        => 'nullable|image|max:5120', // até 5MB
            'user_id'       => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Upload do banner (opcional)
        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = uniqid('banner_') . '.' . $file->getClientOriginalExtension();
            $bannerPath = $file->storeAs('banners', $filename, 'public');
        }
        // Se o usuário estiver autenticado, use o ID dele
        $userId = Auth::check() ? Auth::id() : $request->user_id;

        $galeria = Galeria::create([
            'categoria_id'  => $request->categoria_id,
            'banner_id'     => null, // será setado depois
            'user_id'       => $userId,
            'nome'          => $request->nome,
            'descricao'     => $request->descricao,
            'local'         => $request->local,
            'data'          => $request->data,
            'tempo_duracao' => $request->tempo_duracao,
            'valor_foto'    => $request->valor_foto,
        ]);

        // Cria banner se enviado
        if ($bannerPath) {
            $banner = \App\Models\Banner::create([
                'titulo'     => $galeria->nome,
                'descricao'  => $galeria->descricao,
                'imagem'     => $bannerPath,
                'ordem'      => 0,
                'ativo'      => 1,
            ]);

            $galeria->update(['banner_id' => $banner->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Galeria criada com sucesso.',
            'data'    => $galeria->load(['categoria', 'banner', 'user']),
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $galeria = Galeria::with(['categoria:id,nome', 'banner', 'fotos'])
            ->find($id);

        if (!$galeria) {
            return response()->json([
                'success' => false,
                'message' => 'Galeria não encontrada.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $galeria,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $galeria = Galeria::find($id);

        if (!$galeria) {
            return response()->json([
                'success' => false,
                'message' => 'Galeria não encontrada.',
            ], 404);
        }

        $categorias = Categoria::select('id', 'nome')->get();
        $banners = Banner::select('id','caminho')->get(); 
        return response()->json([
            'success' => true,
            'data'    => $galeria,
            'categorias' => $categorias,
            'banners' => $banners,
            'message' => 'Use PUT /api/galerias/{id} para atualizar esta galeria.'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $galeria = Galeria::find($id);

        if (!$galeria) {
            return response()->json([
                'success' => false,
                'message' => 'Galeria não encontrada.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'categoria_id'  => 'integer|exists:categorias,id',
            'nome'          => 'string|max:255',
            'descricao'     => 'nullable|string',
            'local'         => 'nullable|string|max:255',
            'data'          => 'nullable|date',
            'tempo_duracao' => 'nullable|string|max:255',
            'valor_foto'    => 'numeric|min:0',
            'banner'        => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Atualiza banner se enviado
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = uniqid('banner_') . '.' . $file->getClientOriginalExtension();
            $bannerPath = $file->storeAs('banners', $filename, 'public');

            if ($galeria->banner_id) {
                $banner = \App\Models\Banner::find($galeria->banner_id);
                if ($banner) {
                    Storage::disk('public')->delete($banner->imagem);
                    $banner->update(['imagem' => $bannerPath]);
                }
            } else {
                $banner = \App\Models\Banner::create([
                    'titulo'    => $galeria->nome,
                    'descricao' => $galeria->descricao,
                    'imagem'    => $bannerPath,
                    'ordem'     => 0,
                    'ativo'     => 1,
                ]);
                $galeria->banner_id = $banner->id;
            }
        }

        $galeria->fill($request->only([
            'categoria_id', 'nome', 'descricao', 'local', 'data',
            'tempo_duracao', 'valor_foto'
        ]));

        $galeria->save();

        return response()->json([
            'success' => true,
            'message' => 'Galeria atualizada com sucesso.',
            'data'    => $galeria->load(['categoria', 'banner']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $galeria = Galeria::with('banner')->find($id);

        if (!$galeria) {
            return response()->json([
                'success' => false,
                'message' => 'Galeria não encontrada.',
            ], 404);
        }

        // Remove banner associado (se existir)
        if ($galeria->banner) {
            Storage::disk('public')->delete($galeria->banner->imagem);
            $galeria->banner->delete();
        }

        // Remove galeria
        $galeria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Galeria removida com sucesso.',
        ]);
    }
        public function reorderFotos(Request $request, $galeriaId)
    {
        $ordered = $request->input('ordered_ids', []);
        if (!is_array($ordered)) {
            return response()->json(['success' => false, 'message' => 'ordered_ids inválido'], 400);
        }

        // inicia transação
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($ordered as $index => $fotoId) {
                // atualiza apenas fotos que pertencem à galeria e referencia_tipo = 'galeria'
                \Illuminate\Support\Facades\DB::table('fotos')
                    ->where('id', $fotoId)
                    ->where('referencia_tipo', 'galeria')
                    ->update(['ordem' => $index]);
            }
            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Erro reordenar fotos: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao salvar ordem'], 500);
        }
    }

}
