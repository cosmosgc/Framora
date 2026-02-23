<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of categories.
     *
     * GET /api/categorias
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $categorias = Categoria::all();

            return response()->json([
                'success' => true,
                'data' => $categorias
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar categorias: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created category.
     *
     * POST /api/categorias
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string'
        ]);

        try {
            $categoria = Categoria::create($validated);

            return response()->json([
                'success' => true,
                'data' => $categoria,
                'message' => 'Categoria criada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar categoria: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category.
     *
     * GET /api/categorias/{id}
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $categoria = Categoria::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $categoria
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar categoria: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified category.
     *
     * PUT/PATCH /api/categorias/{id}
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string'
        ]);

        try {
            $categoria = Categoria::findOrFail($id);
            $categoria->update($validated);

            return response()->json([
                'success' => true,
                'data' => $categoria,
                'message' => 'Categoria atualizada com sucesso!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar categoria: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified category.
     *
     * DELETE /api/categorias/{id}
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            $categoria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoria excluída com sucesso!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir categoria: ' . $e->getMessage()
            ], 500);
        }
    }
}
