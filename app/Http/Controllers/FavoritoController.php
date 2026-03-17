<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use App\Models\Foto;
use App\Models\Galeria;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FavoritoController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function index()
    {
        $favoritos = $this->buildFavoritosForUser((int) Auth::id());
        $galeriaFavoritos = $favoritos->where('referencia_tipo', 'galeria')->values();
        $fotoFavoritos = $favoritos->where('referencia_tipo', 'foto')->values();
        $ownedFotoFavoritos = $fotoFavoritos->filter(fn ($favorito) => (bool) $favorito->getAttribute('is_owned'))->values();
        $wishlistFotoFavoritos = $fotoFavoritos->reject(fn ($favorito) => (bool) $favorito->getAttribute('is_owned'))->values();

        return view('favoritos.index', [
            'favoritos' => $favoritos,
            'fotoFavoritosCount' => $fotoFavoritos->count(),
            'galeriaFavoritosCount' => $galeriaFavoritos->count(),
            'ownedFotoFavoritosCount' => $ownedFotoFavoritos->count(),
            'galeriaFavoritos' => $galeriaFavoritos,
            'fotoFavoritos' => $wishlistFotoFavoritos,
            'ownedFotoFavoritos' => $ownedFotoFavoritos,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $this->validateFavorito($request);

        $favorito = Favorito::firstOrCreate([
            'user_id' => $user->id,
            'referencia_tipo' => $data['referencia_tipo'],
            'referencia_id' => $data['referencia_id'],
        ]);

        $message = $favorito->wasRecentlyCreated
            ? 'Item adicionado aos favoritos.'
            : 'Item já está nos seus favoritos.';

        if ($request->wantsJson() || $request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'favorito' => [
                    'id' => $favorito->id,
                    'referencia_tipo' => $favorito->referencia_tipo,
                    'referencia_id' => $favorito->referencia_id,
                    'criado_em' => optional($favorito->criado_em)->toISOString(),
                ],
            ], $favorito->wasRecentlyCreated ? 201 : 200);
        }

        return redirect()
            ->back()
            ->with($favorito->wasRecentlyCreated ? 'success' : 'info', $message);
    }

    public function destroy(Request $request, string $id)
    {
        $favorito = Favorito::where('user_id', Auth::id())->findOrFail($id);
        $favorito->delete();

        if ($request->wantsJson() || $request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Item removido dos favoritos.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Item removido dos favoritos.');
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

        $fotoIds = $favoritos->where('referencia_tipo', 'foto')->pluck('referencia_id');

        $fotos = Foto::query()
            ->with('galeria')
            ->whereIn('id', $fotoIds)
            ->get()
            ->keyBy('id');

        $galerias = Galeria::query()
            ->with([
                'banner',
                'fotos' => function ($query) {
                    $query->orderBy('ordem')->orderBy('id');
                },
            ])
            ->whereIn('id', $favoritos->where('referencia_tipo', 'galeria')->pluck('referencia_id'))
            ->get()
            ->keyBy('id');

        $ownedFotoIds = Inventario::query()
            ->where('user_id', $userId)
            ->whereIn('foto_id', $fotoIds)
            ->pluck('foto_id')
            ->flip();

        return $favoritos->map(function (Favorito $favorito) use ($fotos, $galerias, $ownedFotoIds) {
            $item = $favorito->referencia_tipo === 'foto'
                ? $fotos->get($favorito->referencia_id)
                : $galerias->get($favorito->referencia_id);

            $favorito->setAttribute('item', $item);
            $favorito->setAttribute('is_owned', $favorito->referencia_tipo === 'foto' && $ownedFotoIds->has($favorito->referencia_id));

            return $favorito;
        });
    }
}
