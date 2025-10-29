<?php

namespace App\Http\Controllers;

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
}
