@extends('layouts.app')

@section('content')
    {{-- Banners em destaque --}}
    <section class="mb-10">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100">Banners em destaque</h2>

        @if(!empty($banners) && count($banners) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($banners as $banner)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                    <img src="{{ asset(  $banner['image']) }}"
                        alt="{{ $banner['title'] ?? 'Banner' }}"
                        class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $banner['title'] ?? 'Título do Banner' }}
                            </h3>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 dark:text-gray-400">Nenhum banner disponível.</p>
        @endif
    </section>

    {{-- Galerias em destaque --}}
    <section>
        <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100">Galerias em destaque</h2>

        @if(!empty($galerias) && count($galerias) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($galerias as $galeria)
                    <a href="{{ route('galerias.show', $galeria['id'] ?? 1) }}"
                       class="block bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden hover:shadow-lg transition">
                        <img src="{{ asset( $galeria['thumbnail']) }}"
                            alt="{{ $galeria['title'] ?? 'Galeria' }}"
                            class="w-full h-48 object-cover">

                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $galeria['title'] ?? 'Título da Galeria' }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $galeria['description'] ?? 'Descrição breve da galeria.' }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 dark:text-gray-400">Nenhuma galeria encontrada.</p>
        @endif
    </section>
@endsection
