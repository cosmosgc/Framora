<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Galeria;
use App\Models\Categoria;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

public function index()
{
    // Fetch latest 10 galerias with related category and banner
    $galerias = Galeria::with(['categoria', 'banner'])
        ->orderBy('id', 'desc')
        ->take(10)
        ->get();

    // Fetch all categories (or you can limit/sort as needed)
    $categorias = Categoria::with('banner')->orderBy('nome')->get();

    // Pass to view
    return view('home.index', compact('galerias', 'categorias'));
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
