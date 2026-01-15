@extends('layouts.admin')

@section('title', 'Editar Categoria')
@section('header', 'Editar Categoria')

@section('content')

<form action="{{ route('admin.categorias.update', $categoria->id) }}"
      method="POST"
      enctype="multipart/form-data"
      class="max-w-xl bg-white p-6 rounded shadow">

    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="block font-medium mb-1">Nome</label>
        <input type="text" name="nome"
               class="w-full border rounded px-3 py-2"
               value="{{ old('nome', $categoria->nome) }}" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-1">Descrição</label>
        <textarea name="descricao"
                  class="w-full border rounded px-3 py-2"
                  rows="4">{{ old('descricao', $categoria->descricao) }}</textarea>
    </div>

    {{-- Thumbnail upload --}}
    <div class="mb-4">
        <label class="block font-medium mb-1">Thumbnail</label>

        <input type="file"
               name="thumbnail"
               accept="image/*"
               onchange="previewThumbnail(event)"
               class="w-full border rounded px-3 py-2">

        {{-- Preview --}}
        <div class="mt-3">
            <img id="thumbnailPreview"
                 src="{{ $categoria->thumbnail ? asset($categoria->thumbnail) : '' }}"
                 class="max-h-40 rounded border {{ $categoria->thumbnail ? '' : 'hidden' }}">
        </div>
    </div>

    <div class="mb-6">
        <label class="block font-medium mb-1">Banner ID</label>
        <input type="number" name="banner_id"
               class="w-full border rounded px-3 py-2"
               value="{{ old('banner_id', $categoria->banner_id) }}">
    </div>

    <div class="flex gap-2">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">
            Atualizar
        </button>

        <a href="{{ route('admin.categorias.index') }}"
           class="px-4 py-2 bg-gray-200 rounded">
            Cancelar
        </a>
    </div>
</form>

{{-- Preview script --}}
<script>
function previewThumbnail(event) {
    const img = document.getElementById('thumbnailPreview');
    img.src = URL.createObjectURL(event.target.files[0]);
    img.classList.remove('hidden');
}
</script>

@endsection
