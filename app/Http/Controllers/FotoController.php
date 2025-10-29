<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Image;

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
            'galeria_id'   => 'required|integer',
            'fotos'           => 'required|array',
            'fotos.*'         => 'image|max:5120', // cada imagem até 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $fotosCriadas = [];

        foreach ($request->file('fotos') as $file) {
            try {
                // Caminhos base (em public/)
                $basePath    = public_path('uploads/fotos');
                $originalDir = $basePath . '/originais';
                $thumbDir    = $basePath . '/thumbs';
                $mediaDir    = $basePath . '/medias';

                // 🔧 Garante que os diretórios existem
                foreach ([$originalDir, $thumbDir, $mediaDir] as $dir) {
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                }

                // Nome único
                $filename = uniqid('foto_') . '.' . $file->getClientOriginalExtension();

                // Salva original
                $file->move($originalDir, $filename);

                // Caminhos relativos (para salvar no banco)
                $path = "uploads/fotos/originais/{$filename}";
                $thumbPath = "uploads/fotos/thumbs/{$filename}";
                $fotoPath  = "uploads/fotos/medias/{$filename}";

                // 🔧 Cria versões menores com Intervention Image
                if (class_exists(\Intervention\Image\Facades\Image::class)) {
                    $image = Image::make($originalDir . '/' . $filename);

                    // thumb 300px
                    $image->resize(300, null, fn($c) => $c->aspectRatio())
                        ->save($thumbDir . '/' . $filename);

                    // média 1200px
                    $image->resize(1200, null, fn($c) => $c->aspectRatio())
                        ->save($mediaDir . '/' . $filename);
                } else {
                    // fallback: copiar original
                    copy($originalDir . '/' . $filename, $thumbDir . '/' . $filename);
                    copy($originalDir . '/' . $filename, $mediaDir . '/' . $filename);
                }

                // 🔧 Cria registro no banco
                $foto = Foto::create([
                    'referencia_tipo' => $request->referencia_tipo,
                    'galeria_id'   => $request->referencia_id,
                    'caminho_thumb'   => $thumbPath,
                    'caminho_foto'    => $fotoPath,
                    'caminho_original'=> $path,
                    'ativo'           => true,
                ]);

                $fotosCriadas[] = $foto;

            } catch (\Exception $e) {
                \Log::error("Erro ao processar imagem: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($fotosCriadas) . ' foto(s) enviada(s) com sucesso.',
            'data'    => $fotosCriadas,
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
            'galeria_id'   => 'integer',
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
        $foto->fill($request->only(['referencia_tipo', 'galeria_id', 'ativo']));
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
