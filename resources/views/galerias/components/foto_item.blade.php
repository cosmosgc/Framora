@props(['foto', 'index'])

<div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition relative bg-white">
    <button type="button"
        class="w-full h-48 p-0 m-0 block text-left focus:outline-none foto-open-btn"
        data-index="{{ $index }}"
        data-src="{{ asset($foto->caminho_foto) }}"
        data-thumb="{{ asset($foto->caminho_thumb) }}"
        data-title="{{ $foto->titulo ?? '' }}"
        data-desc="{{ Str::limit($foto->descricao ?? '', 200) }}">
        <img src="{{ asset($foto->caminho_thumb) }}" alt="{{ $foto->titulo ?? 'Foto' }}" class="w-full h-48 object-cover">
    </button>

    <div class="p-3">
        <h3 class="font-medium text-sm mb-1">{{ $foto->titulo ?? 'Foto' }}</h3>
        <p class="text-xs text-gray-500 mb-3">{{ Str::limit($foto->descricao ?? '', 60) }}</p>

        <div class="flex items-center gap-2">
            {{-- botão ver (abre modal) --}}
            <button type="button"
                    class="px-3 py-1 text-xs bg-neutral-800 text-white rounded foto-open-btn"
                    data-index="{{ $index }}"
                    data-src="{{ asset($foto->caminho_foto) }}"
                    data-thumb="{{ asset($foto->caminho_thumb) }}"
                    data-title="{{ $foto->titulo ?? '' }}"
                    data-desc="{{ Str::limit($foto->descricao ?? '', 200) }}">
                Ver
            </button>

            {{-- botão adicionar ao carrinho (form POST) --}}
            @auth
                <form class="inline add-to-cart-form" method="POST" action="{{ route('carrinho.store') }}">
                    @csrf
                    <input type="hidden" name="foto_id" value="{{ $foto->id }}">
                    <input type="hidden" name="preco" value="{{ $foto->preco ?? 0 }}">
                    <button type="submit" class="px-3 py-1 text-xs bg-green-600 text-white rounded">Adicionar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-3 py-1 text-xs bg-green-600 text-white rounded">Entrar para comprar</a>
            @endauth

            {{-- link para abrir foto em nova aba --}}
            <a href="{{ asset($foto->caminho_foto) }}" target="_blank" class="ml-auto text-xs text-neutral-600 hover:underline">Abrir</a>
        </div>
    </div>
</div>
