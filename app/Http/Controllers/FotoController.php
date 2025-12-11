<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Banner;
use App\Models\Galeria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

use App\Helpers\ImageConfig;


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
            'fotos'        => 'required|array',
            'fotos.*'      => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // === CONFIGS (ENV ou DB) ============================================
        $thumbWidth  = intval(ImageConfig::get('IMAGE_THUMB_WIDTH', 300));
        $mediaWidth  = intval(ImageConfig::get('IMAGE_MEDIA_WIDTH', 1200));
        $thumbQ      = intval(ImageConfig::get('IMAGE_THUMB_QUALITY', 60));
        $mediaQ      = intval(ImageConfig::get('IMAGE_MEDIA_QUALITY', 80));

        $wmPath      = ImageConfig::get('IMAGE_WATERMARK_PATH', public_path('uploads/watermark/wm.png'));
        if (!file_exists($wmPath)) {
            $wmPath = public_path('uploads/watermark/wm.png'); // fallback
        }

        $wmOpacity   = intval(ImageConfig::get('IMAGE_WATERMARK_OPACITY', 40));  // 0–100
        $wmScale     = intval(ImageConfig::get('IMAGE_WATERMARK_SCALE_PERCENT', 15)); // %
        $wmSpacing   = intval(ImageConfig::get('IMAGE_WATERMARK_TILE_SPACING', 0));

        $manager = new ImageManager(new Driver());

        $fotosCriadas = [];

        // Load galeria once
        $galeria = Galeria::find($request->galeria_id);

        // flag to avoid setting banner more than once in the same request
        $bannerSetThisRequest = false;

        // === PROCESSAMENTO ===================================================
        foreach ($request->file('fotos') as $file) {
            try {
                // Pastas
                $basePath    = public_path('uploads/fotos');
                $originalDir = "{$basePath}/originais";
                $thumbDir    = "{$basePath}/thumbs";
                $mediaDir    = "{$basePath}/medias";

                foreach ([$originalDir, $thumbDir, $mediaDir] as $dir) {
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                }

                // Filename
                $filename = uniqid('foto_') . "." . $file->getClientOriginalExtension();
                $originalPath = "{$originalDir}/{$filename}";
                $file->move($originalDir, $filename);

                // Caminhos p/ banco
                $thumbPathRel = "uploads/fotos/thumbs/{$filename}";
                $mediaPathRel = "uploads/fotos/medias/{$filename}";
                $origPathRel  = "uploads/fotos/originais/{$filename}";

                // Lê imagem original
                $imgOriginal = $manager->read($originalPath);

                //-----------------------------------------------------------------
                // Função: aplica watermark tile + salva versão reduzida
                //-----------------------------------------------------------------
                $processar = function($srcImg, $destPath, $targetWidth, $quality) use (
                    $manager, $wmPath, $wmOpacity, $wmScale, $wmSpacing
                ) {
                    // 1. Redimensiona
                    if ($srcImg->width() > $targetWidth) {
                        $srcImg->scale(width: $targetWidth);
                    }

                    // 2. Aplica watermark tiled se existir
                    if (file_exists($wmPath)) {
                        $wm = $manager->read($wmPath);

                        // Escala do watermark
                        $newW = intval($srcImg->width() * ($wmScale / 100));
                        $wm->scale(width: $newW);

                        // Aplica opacidade
                        if ($wmOpacity < 100) {
                            // $wm->blendTransparency($wmOpacity);
                        }

                        $wmW = $wm->width();
                        $wmH = $wm->height();

                        // Tile manual
                        for ($x = 0; $x < $srcImg->width(); $x += ($wmW + $wmSpacing)) {
                            for ($y = 0; $y < $srcImg->height(); $y += ($wmH + $wmSpacing)) {
                                $srcImg->place($wm, 'top-left', $x, $y);
                            }
                        }
                    }

                    // 3. Salva JPEG otimizado
                    $srcImg
                        ->toJpeg($quality)
                        ->save($destPath);
                };

                // === GERAR THUMB ====================================================
                $imgThumb = $manager->read($originalPath);
                $processar($imgThumb, "{$thumbDir}/{$filename}", $thumbWidth, $thumbQ);

                // === GERAR MEDIA ====================================================
                $imgMedia = $manager->read($originalPath);
                $processar($imgMedia, "{$mediaDir}/{$filename}", $mediaWidth, $mediaQ);

                // === Salvar no banco ================================================
                $foto = Foto::create([
                    'referencia_tipo' => $request->referencia_tipo,
                    'galeria_id'      => $request->galeria_id,
                    'caminho_thumb'   => $thumbPathRel,
                    'caminho_foto'    => $mediaPathRel,
                    'caminho_original'=> $origPathRel,
                    'ativo'           => true,
                ]);

                $fotosCriadas[] = $foto;

                // === Banner logic: create/update Banner if galeria has no banner or banner.imagem is empty
                if (!$bannerSetThisRequest && $galeria) {
                    if (empty($galeria->banner_id)) {
                        // create a new banner and attach to galeria
                        $banner = Banner::create([
                            'titulo'    => $galeria->nome ?? 'Banner',
                            'descricao' => $galeria->descricao ?? null,
                            'imagem'    => $thumbPathRel,
                            'link'      => null,
                            'ordem'     => 0,
                            'ativo'     => true,
                        ]);

                        $galeria->banner_id = $banner->id;
                        $galeria->save();

                        $bannerSetThisRequest = true;
                    } else {
                        // galeria already has a banner_id — check if that banner has an imagem
                        $banner = Banner::find($galeria->banner_id);
                        if ($banner && (is_null($banner->imagem) || $banner->imagem === '')) {
                            $banner->imagem = $thumbPathRel;
                            $banner->save();
                            $bannerSetThisRequest = true;
                        }
                    }
                }

            } catch (\Exception $e) {
                Log::error("Erro ao processar imagem (v3): " . $e->getMessage());
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
    public function testWatermark($fotoId, $tipo = 'media')
    {
        $foto = Foto::find($fotoId);

        if (!$foto) {
            return response("Foto não encontrada", 404);
        }

        // Arquivo base (usar original SEMPRE)
        $path = public_path($foto->caminho_original);

        if (!file_exists($path)) {
            return response("Arquivo original não encontrado", 404);
        }

        // --- Configs (env ou DB) -------------------------------
        $thumbW = intval(ImageConfig::get('IMAGE_THUMB_WIDTH', 300));
        $mediaW = intval(ImageConfig::get('IMAGE_MEDIA_WIDTH', 1200));
        $thumbQ = intval(ImageConfig::get('IMAGE_THUMB_QUALITY', 60));
        $mediaQ = intval(ImageConfig::get('IMAGE_MEDIA_QUALITY', 80));

        $wmPath    = ImageConfig::get('IMAGE_WATERMARK_PATH', public_path('uploads/watermark/wm.png'));
        if (!file_exists($wmPath)) {
            $wmPath = public_path('uploads/watermark/wm.png');
        }

        $wmOpacity = intval(ImageConfig::get('IMAGE_WATERMARK_OPACITY', 40));
        $wmScale   = intval(ImageConfig::get('IMAGE_WATERMARK_SCALE_PERCENT', 15));
        $wmSpacing = intval(ImageConfig::get('IMAGE_WATERMARK_TILE_SPACING', 0));

        // --- Driver Intervention v3 ----------------------------
        $manager = new ImageManager(new Driver());
        $img = $manager->read($path);

        // Ajustar tamanho conforme o tipo solicitado
        if ($tipo === 'thumb') {
            $finalWidth = $thumbW;
            $quality = $thumbQ;
        } elseif ($tipo === 'full') {
            // mantém original
            $finalWidth = $img->width();
            $quality = $mediaQ;
        } else { // default: media
            $finalWidth = $mediaW;
            $quality = $mediaQ;
        }

        // Redimensionar proporcionalmente
        if ($img->width() > $finalWidth) {
            $img->scale(width: $finalWidth);
        }

        // Aplicar watermark tiled
        if (file_exists($wmPath)) {
            $wm = $manager->read($wmPath);

            // escala do watermark
            $scaledW = intval($img->width() * ($wmScale / 100));
            $wm->scale(width: $scaledW);

            // opacidade
            if ($wmOpacity < 100) {
                // $wm->blendTransparency($wmOpacity);
            }

            // tile
            $wmW = $wm->width();
            $wmH = $wm->height();

            for ($x = 0; $x < $img->width(); $x += ($wmW + $wmSpacing)) {
                for ($y = 0; $y < $img->height(); $y += ($wmH + $wmSpacing)) {
                    $img->place($wm, 'top-left', $x, $y);
                }
            }
        }

        // Retornar imagem como resposta HTTP (JPEG)
        $binary = $img->toJpeg($quality)->toString();

        return Response::make($binary, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
