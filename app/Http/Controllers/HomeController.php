<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Galeria;
use App\Models\Categoria;

class HomeController extends Controller
{
    /**
     * Display the home page data.
     *
     * Loads latest galleries and available categories for the home view.
     *
     * @return \Illuminate\View\View
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
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return void
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $id
     * @return void
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return void
     */
    public function destroy(string $id)
    {
        //
    }
}
