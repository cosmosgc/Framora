<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class AdminCategoriaController extends Controller
{
    /**
     * GET /admin/categorias
     */
    public function index()
    {
        $categorias = Categoria::orderBy('nome')->get();

        return view('admin.categorias.index', compact('categorias'));
    }

    /**
     * GET /admin/categorias/create
     */
    public function create()
    {
        return view('admin.categorias.create');
    }

    /**
     * POST /admin/categorias
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'banner_id' => 'nullable|integer',
        ]);

        Categoria::create($validated);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * GET /admin/categorias/{id}/edit
     */
    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);

        return view('admin.categorias.edit', compact('categoria'));
    }

    /**
     * PUT /admin/categorias/{id}
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nome'      => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'banner_id' => 'nullable|integer',
        ]);

        $categoria = Categoria::findOrFail($id);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/categorias');

            $file->move($destination, $filename);

            $validated['thumbnail'] = 'uploads/categorias/' . $filename;
        } else {
            unset($validated['thumbnail']); // keep existing
        }

        $categoria->update($validated);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }


    /**
     * DELETE /admin/categorias/{id}
     */
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoria removida com sucesso!');
    }
}
