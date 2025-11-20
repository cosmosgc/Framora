{{-- partial: resources/views/galerias/_foto_item.blade.php
     Usage: @include('galerias._foto_item', ['foto' => $foto]) --}}

@php
    // photo src fallback
    $src = $foto->caminho_thumb ?? $foto->caminho_foto ?? $foto->caminho_original;
@endphp

<div class="foto-item rounded-lg overflow-hidden shadow-sm hover:shadow-md transition relative bg-white"
     data-id="{{ $foto->id }}">
    <a href="{{ asset($foto->caminho_foto ?? $src) }}" target="_blank" class="block">
        <img src="{{ asset($src) }}" alt="Foto {{ $foto->id }}" class="w-full h-40 object-cover">
    </a>

    <div class="p-2 flex items-center justify-between gap-2">
        <span class="text-sm text-gray-700">#{{ $foto->id }}</span>

        <form class="delete-foto-form" method="POST" action="{{ route('fotos.destroy', $foto->id) }}">
            @csrf
            @method('DELETE')
            <button type="button" class="delete-foto-btn text-sm px-2 py-1 bg-red-500 text-white rounded">
                Excluir
            </button>
        </form>
    </div>

    {{-- optional drag handle (improves UX) --}}
    <div class="absolute top-2 left-2 text-xs px-2 py-1 bg-black bg-opacity-50 text-white rounded">arrastar</div>
</div>
