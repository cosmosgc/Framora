<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Favorito;
use App\Models\Foto;
use App\Models\Galeria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FavoritoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        return response()->json(
            $this->buildFavoritosForUser((int) $request->user()->id)
        );
    }

    public function store(Request $request)
    {
        $data = $this->validateFavorito($request);

        $favorito = Favorito::firstOrCreate([
            'user_id' => $request->user()->id,
            'referencia_tipo' => $data['referencia_tipo'],
            'referencia_id' => $data['referencia_id'],
        ]);
        $wasRecentlyCreated = $favorito->wasRecentlyCreated;

        $favorito = $this->buildFavoritosForUser((int) $request->user()->id)
            ->firstWhere('id', $favorito->id);

        return response()->json([
            'message' => $wasRecentlyCreated
                ? 'Item adicionado aos favoritos.'
                : 'Item já estava favoritado.',
            'favorito' => $favorito,
        ], $wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Request $request, string $id)
    {
        $favorito = Favorito::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $favorito->delete();

        return response()->json([
            'message' => 'Item removido dos favoritos.',
        ]);
    }

    protected function validateFavorito(Request $request): array
    {
        $data = $request->validate([
            'referencia_tipo' => ['required', Rule::in(['foto', 'galeria'])],
            'referencia_id' => ['nullable', 'integer'],
            'foto_id' => ['nullable', 'integer'],
            'galeria_id' => ['nullable', 'integer'],
        ]);

        $data['referencia_id'] = $data['referencia_id']
            ?? ($data['referencia_tipo'] === 'foto' ? ($data['foto_id'] ?? null) : ($data['galeria_id'] ?? null));

        if (! $data['referencia_id']) {
            abort(422, 'referencia_id is required');
        }

        if ($data['referencia_tipo'] === 'foto') {
            Foto::query()->findOrFail($data['referencia_id']);
        }

        if ($data['referencia_tipo'] === 'galeria') {
            Galeria::query()->findOrFail($data['referencia_id']);
        }

        return [
            'referencia_tipo' => $data['referencia_tipo'],
            'referencia_id' => (int) $data['referencia_id'],
        ];
    }

    protected function buildFavoritosForUser(int $userId)
    {
        $favoritos = Favorito::query()
            ->where('user_id', $userId)
            ->orderByDesc('criado_em')
            ->orderByDesc('id')
            ->get();

        $fotos = Foto::query()
            ->with('galeria')
            ->whereIn('id', $favoritos->where('referencia_tipo', 'foto')->pluck('referencia_id'))
            ->get()
            ->keyBy('id');

        $galerias = Galeria::query()
            ->with(['fotos' => function ($query) {
                $query->orderBy('ordem')->orderBy('id');
            }])
            ->whereIn('id', $favoritos->where('referencia_tipo', 'galeria')->pluck('referencia_id'))
            ->get()
            ->keyBy('id');

        return $favoritos->map(function (Favorito $favorito) use ($fotos, $galerias) {
            $item = $favorito->referencia_tipo === 'foto'
                ? $fotos->get($favorito->referencia_id)
                : $galerias->get($favorito->referencia_id);

            return [
                'id' => $favorito->id,
                'referencia_tipo' => $favorito->referencia_tipo,
                'referencia_id' => $favorito->referencia_id,
                'criado_em' => optional($favorito->criado_em)->toISOString(),
                'item' => $item,
            ];
        })->values();
    }
}
