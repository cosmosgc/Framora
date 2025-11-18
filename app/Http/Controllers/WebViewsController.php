<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Galeria;
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

        return view('galerias.edit', compact('galeria'));
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
    
    

}
