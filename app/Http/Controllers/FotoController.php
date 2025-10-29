<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FotoController extends Controller
{
    /**
     * Display a listing of the resource.
     * Suporte para filtros, busca, ordenação e paginação.
     */
    public function index(Request $request)
    {
        $search          = $request->input('search');
        $referencia_tipo = $request->input('referencia_tipo');
        $ativo           = $request->input('ativo');
        $galeria_id      = $request->input('galeria_id');
        $sortBy          = $request->input('sort_by', 'id');
        $sortOrder       = $request->input('sort_order', 'desc');
        $perPage         = $request->input('per_page', 15);

        $query = Foto::query()
            ->with(['destacada', 'galerias:id,nome'])
            ->select('fotos.*');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('caminho_foto', 'like', "%{$search}%")
                  ->orWhere('caminho_original', 'like', "%{$search}%")
                  ->orWhere('caminho_thumb', 'like', "%{$search}%")
                  ->orWhere('referencia_tipo', 'like', "%{$search}%");
            });
        }

        if (!empty($referencia_tipo)) {
            $query->where('referencia_tipo', $referencia_tipo);
        }

        if ($ativo !== null && $ativo !== '') {
            $query->where('ativo', (bool) $ativo);
        }

        if (!empty($galeria_id)) {
            $query->whereHas('galerias', function ($q) use ($galeria_id) {
                $q->where('galerias.id', $galeria_id);
            });
        }

        if (in_array($sortBy, ['id', 'referencia_tipo', 'criado_em', 'atualizado_em'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $fotos = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'filters' => [
                'search'          => $search,
                'referencia_tipo' => $referencia_tipo,
                'ativo'           => $ativo,
                'galeria_id'      => $galeria_id,
                'sort_by'         => $sortBy,
                'sort_order'      => $sortOrder,
                'per_page'        => $perPage,
            ],
            'data' => $fotos->items(),
            'meta' => [
                'current_page' => $fotos->currentPage(),
                'last_page'    => $fotos->lastPage(),
                'per_page'     => $fotos->perPage(),
                'total'        => $fotos->total(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * (Para uso em painel administrativo futuramente)
     */
    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => 'Endpoint de criação de fotos — use POST /api/fotos para enviar arquivos.'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Upload de imagem + criação do registro.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referencia_tipo' => 'required|in:galeria,evento',
            'referencia_id'   => 'required|integer',
            'foto'            => 'required|image|max:5120', // até 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $file = $request->file('foto');

        // Nome único e diretório
        $filename = uniqid('foto_') . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('fotos/originais', $filename, 'public');

        // Gera versões menores (thumb, foto média)
        $thumbPath = 'fotos/thumbs/' . $filename;
        $fotoPath  = 'fotos/medias/' . $filename;

        // Usa Intervention Image (caso instalada)
        if (class_exists(\Intervention\Image\Facades\Image::class)) {
            $image = \Intervention\Image\Facades\Image::make($file);

            // thumb 300px
            $image->resize(300, null, fn($c) => $c->aspectRatio())
                  ->save(storage_path('app/public/' . $thumbPath));

            // média 1200px
            $image->resize(1200, null, fn($c) => $c->aspectRatio())
                  ->save(storage_path('app/public/' . $fotoPath));
        } else {
            // fallback: apenas duplicar original
            Storage::copy('public/' . $path, 'public/' . $thumbPath);
            Storage::copy('public/' . $path, 'public/' . $fotoPath);
        }

        $foto = Foto::create([
            'referencia_tipo' => $request->referencia_tipo,
            'referencia_id'   => $request->referencia_id,
            'caminho_thumb'   => $thumbPath,
            'caminho_foto'    => $fotoPath,
            'caminho_original'=> $path,
            'ativo'           => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto enviada com sucesso.',
            'data'    => $foto,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $foto = Foto::with(['destacada', 'galerias:id,nome'])
            ->find($id);

        if (!$foto) {
            return response()->json([
                'success' => false,
                'message' => 'Foto não encontrada.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $foto,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * (Usado em interfaces administrativas)
     */
    public function edit(string $id)
    {
        $foto = Foto::find($id);

        if (!$foto) {
            return response()->json([
                'success' => false,
                'message' => 'Foto não encontrada.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $foto,
            'message' => 'Endpoint de edição — envie PUT /api/fotos/{id} para atualizar.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Permite atualizar informações e substituir a imagem.
     */
    public function update(Request $request, string $id)
    {
        $foto = Foto::find($id);

        if (!$foto) {
            return response()->json([
                'success' => false,
                'message' => 'Foto não encontrada.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'referencia_tipo' => 'in:galeria,evento',
            'referencia_id'   => 'integer',
            'foto'            => 'image|max:5120',
            'ativo'           => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Atualiza imagem se enviada
        if ($request->hasFile('foto')) {
            // Deleta antigas
            Storage::disk('public')->delete([
                $foto->caminho_thumb,
                $foto->caminho_foto,
                $foto->caminho_original,
            ]);

            $file = $request->file('foto');
            $filename = uniqid('foto_') . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('fotos/originais', $filename, 'public');
            $thumbPath = 'fotos/thumbs/' . $filename;
            $fotoPath  = 'fotos/medias/' . $filename;

            if (class_exists(\Intervention\Image\Facades\Image::class)) {
                $image = \Intervention\Image\Facades\Image::make($file);
                $image->resize(300, null, fn($c) => $c->aspectRatio())
                      ->save(storage_path('app/public/' . $thumbPath));
                $image->resize(1200, null, fn($c) => $c->aspectRatio())
                      ->save(storage_path('app/public/' . $fotoPath));
            } else {
                Storage::copy('public/' . $path, 'public/' . $thumbPath);
                Storage::copy('public/' . $path, 'public/' . $fotoPath);
            }

            $foto->fill([
                'caminho_thumb'    => $thumbPath,
                'caminho_foto'     => $fotoPath,
                'caminho_original' => $path,
            ]);
        }

        // Atualiza campos gerais
        $foto->fill($request->only(['referencia_tipo', 'referencia_id', 'ativo']));
        $foto->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto atualizada com sucesso.',
            'data'    => $foto,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $foto = Foto::find($id);

        if (!$foto) {
            return response()->json([
                'success' => false,
                'message' => 'Foto não encontrada.',
            ], 404);
        }

        Storage::disk('public')->delete([
            $foto->caminho_thumb,
            $foto->caminho_foto,
            $foto->caminho_original,
        ]);

        $foto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto removida com sucesso.',
        ]);
    }
}
