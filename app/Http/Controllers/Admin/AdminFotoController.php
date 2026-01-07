<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Foto;

class AdminFotoController extends Controller
{
    /**
     * DELETE /admin/fotos/{foto}
     */
    public function destroy(Foto $foto)
    {
        // Delete image files (thumb, medium, original)
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

        // Keep galeria id to redirect back
        $galeriaId = $foto->galeria_id;

        // Delete DB record
        $foto->delete();

        return redirect()
            ->route('admin.galerias.show', $galeriaId)
            ->with('success', 'Foto removida com sucesso.');
    }
}
