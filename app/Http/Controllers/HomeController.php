<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Aqui você pode buscar banners e galerias do banco
        // e passar para a view
        return view('home.index', [
            'banners' => [
                ['id' => 1, 'title' => 'Promoção Especial', 'image' => '/images/banner1.jpg'],
                ['id' => 2, 'title' => 'Novas Galerias', 'image' => '/images/banner2.jpg'],
            ],
            'galerias' => [
                ['id' => 1, 'title' => 'Natureza', 'thumbnail' => '/images/galeria1.gif', 'description' => 'Fotos de paisagens naturais'],
                ['id' => 2, 'title' => 'Cidades', 'thumbnail' => '/images/galeria2.gif', 'description' => 'Fotos urbanas e arquitetônicas'],
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
