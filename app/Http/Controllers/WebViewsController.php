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
        return view('fotos.show', compact('id'));
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
