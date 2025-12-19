<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeria;
use Illuminate\Support\Facades\DB;

class AdminGaleriaController extends Controller
{
    /**
     * List all galerias
     */
    public function index()
    {
        $galerias = Galeria::with(['categoria', 'user'])
            ->withCount('fotos')
            ->orderByDesc('id')
            ->get();

        return view('admin.galerias.index', compact('galerias'));
    }

    /**
     * Show fotos of a galeria
     */
    public function show(Galeria $galeria)
    {
        $galeria->load(['categoria', 'user', 'fotos']);

        return view('admin.galerias.show', compact('galeria'));
    }
    public function destroy(Galeria $galeria)
    {
        DB::transaction(function () use ($galeria) {

            foreach ($galeria->fotos as $foto) {
                // Delete files (if exist)
                $paths = [
                    $foto->caminho_thumb,
                    $foto->caminho_foto,
                    $foto->caminho_original,
                ];

                foreach ($paths as $path) {
                    if ($path) {
                        $fullPath = public_path($path);
                        if (file_exists($fullPath)) {
                            @unlink($fullPath);
                        }
                    }
                }

                // Delete foto record
                $foto->delete();
            }

            // Finally delete galeria
            $galeria->delete();
        });

        return redirect()
            ->route('admin.galerias.index')
            ->with('success', 'Galeria e todas as fotos foram removidas com sucesso.');
    }
}
