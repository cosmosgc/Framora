@props(['galeria'])

<a href="{{ route('galerias.web.show', $galeria->id) }}" 
   class="relative group overflow-hidden rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 bg-gray-100 border border-gray-200">

    {{-- Banner Image --}}
    @if($galeria->banner && $galeria->banner->imagem)
        <img 
            src="{{ asset($galeria->banner->imagem) }}" 
            alt="{{ $galeria->nome }}" 
            class="w-full h-56 object-cover transform group-hover:scale-105 transition duration-300"
        >
    @else
        <div class="w-full h-56 bg-gray-200 flex items-center justify-center text-gray-400">
            No Image
        </div>
    @endif

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4">
        <h2 class="text-white text-lg font-semibold">{{ $galeria->nome }}</h2>
        <p class="text-sm text-gray-200">
            {{ $galeria->categoria?->nome ?? 'Sem categoria' }}
        </p>
    </div>

    {{-- Card Content --}}
    <div class="p-5">
        <p class="text-xl font-semibold text-gray-800 mb-1">{{ $galeria->nome }}</p>

        <p class="text-sm text-gray-500 mb-3">
            {{ $galeria->categoria?->nome ?? 'Sem categoria' }}
        </p>

        <p class="text-gray-600 mb-4">
            {{ Str::limit($galeria->descricao, 100) }}
        </p>

        {{-- Location & Date --}}
        <div class="text-sm text-gray-500 mb-4">
            <p>📍 {{ $galeria->local ?? 'Local não informado' }}</p>
            <p>📅 {{ $galeria->data ?? 'Data não informada' }}</p>
        </div>

        {{-- User Info --}}
        @if($galeria->user)
            <div class="flex items-center gap-3 mt-3 border-t border-gray-200 pt-3">
                <img 
                    src="{{ asset($galeria->user->avatar ?? 'default-avatar.png') }}" 
                    alt="{{ $galeria->user->name }}" 
                    class="w-8 h-8 rounded-full object-cover"
                >
                <span class="text-sm text-gray-700 font-medium">
                    {{ $galeria->user->name }}
                </span>
            </div>
        @endif
    </div>
</a>
