@extends('layouts.admin')

@section('title', 'Image Settings')
@section('header', 'Configurações de Imagem')

@section('content')

@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.image-settings.update') }}"
      method="POST"
      enctype="multipart/form-data"
      class="max-w-3xl bg-white p-6 rounded shadow space-y-6">

    @csrf

    {{-- Sizes --}}
    <div>
        <h2 class="font-semibold mb-3">📐 Dimensões</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Thumbnail width (px)</label>
                <input type="number" name="IMAGE_THUMB_WIDTH"
                       class="w-full border rounded px-3 py-2"
                       value="{{ old('IMAGE_THUMB_WIDTH', $settings['IMAGE_THUMB_WIDTH'] ?? '') }}">
            </div>

            <div>
                <label class="block text-sm font-medium">Imagem média width (px)</label>
                <input type="number" name="IMAGE_MEDIA_WIDTH"
                       class="w-full border rounded px-3 py-2"
                       value="{{ old('IMAGE_MEDIA_WIDTH', $settings['IMAGE_MEDIA_WIDTH'] ?? '') }}">
            </div>
        </div>
    </div>

    {{-- Quality --}}
    <div>
        <h2 class="font-semibold mb-3">🎚 Qualidade</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Thumbnail quality (%)</label>
                <input type="number" name="IMAGE_THUMB_QUALITY"
                       class="w-full border rounded px-3 py-2"
                       value="{{ old('IMAGE_THUMB_QUALITY', $settings['IMAGE_THUMB_QUALITY'] ?? '') }}">
            </div>

            <div>
                <label class="block text-sm font-medium">Imagem média quality (%)</label>
                <input type="number" name="IMAGE_MEDIA_QUALITY"
                       class="w-full border rounded px-3 py-2"
                       value="{{ old('IMAGE_MEDIA_QUALITY', $settings['IMAGE_MEDIA_QUALITY'] ?? '') }}">
            </div>
        </div>
    </div>

    {{-- Watermark --}}
    <div>
        <h2 class="font-semibold mb-3">💧 Watermark</h2>

        <div class="space-y-4">

            {{-- Current watermark preview --}}
            @if(!empty($settings['IMAGE_WATERMARK_PATH']) && file_exists(public_path(str_replace('public/', '', $settings['IMAGE_WATERMARK_PATH']))))
                <div>
                    <p class="text-sm text-gray-500 mb-1">Atual</p>
                    <img src="{{ asset(str_replace('public/', '', $settings['IMAGE_WATERMARK_PATH'])) }}"
                        class="max-h-32 border rounded bg-gray-100 p-2">
                </div>
            @endif

            {{-- Upload new watermark --}}
            <div>
                <label class="block text-sm font-medium">Upload novo watermark (PNG)</label>
                <input type="file" name="watermark_file"
                    accept="image/png"
                    class="w-full border rounded px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">
                    Será salvo como <code>public/uploads/watermark/wm.png</code>
                </p>
            </div>

            {{-- Watermark settings --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium">Opacity (%)</label>
                    <input type="number" name="IMAGE_WATERMARK_OPACITY"
                        class="w-full border rounded px-3 py-2"
                        value="{{ old('IMAGE_WATERMARK_OPACITY', $settings['IMAGE_WATERMARK_OPACITY'] ?? '') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium">Scale (%)</label>
                    <input type="number" name="IMAGE_WATERMARK_SCALE_PERCENT"
                        class="w-full border rounded px-3 py-2"
                        value="{{ old('IMAGE_WATERMARK_SCALE_PERCENT', $settings['IMAGE_WATERMARK_SCALE_PERCENT'] ?? '') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium">Tile spacing (px)</label>
                    <input type="number" name="IMAGE_WATERMARK_TILE_SPACING"
                        class="w-full border rounded px-3 py-2"
                        value="{{ old('IMAGE_WATERMARK_TILE_SPACING', $settings['IMAGE_WATERMARK_TILE_SPACING'] ?? '') }}">
                </div>
            </div>

            {{-- Test link --}}
            <div class="pt-2">
                <a href="{{ url('/watermark-test/2') }}"
                target="_blank"
                class="inline-block text-sm text-blue-600 hover:underline">
                    🔍 Testar watermark (Foto #2)
                </a>
            </div>

        </div>
    </div>
    

    <div class="pt-4 flex gap-2">
        <button class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Salvar configurações
        </button>

        <a href="{{ route('admin.dashboard') }}"
           class="px-5 py-2 bg-gray-200 rounded">
            Cancelar
        </a>
    </div>

</form>

@endsection
