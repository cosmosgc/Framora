@extends('layouts.admin')

@section('title', 'Nova Categoria')
@section('header', 'Nova Categoria')

@section('content')

<form action="{{ route('admin.categorias.store') }}" method="POST" class="max-w-xl bg-white p-6 rounded shadow">
    @csrf

    <div class="mb-4">
        <label class="block font-medium mb-1">Nome</label>
        <input type="text" name="nome"
               class="w-full border rounded px-3 py-2"
               value="{{ old('nome') }}" required>
        @error('nome') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-1">Descrição</label>
        <textarea name="descricao" class="w-full border rounded px-3 py-2"
                  rows="4">{{ old('descricao') }}</textarea>
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-1">Thumbnail (path)</label>
        <input type="text" name="thumbnail"
               class="w-full border rounded px-3 py-2"
               value="{{ old('thumbnail') }}">
    </div>

    <div class="mb-6">
        <label class="block font-medium mb-1">Banner ID</label>
        <input type="number" name="banner_id"
               class="w-full border rounded px-3 py-2"
               value="{{ old('banner_id') }}">
    </div>

    <div class="flex gap-2">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">
            Salvar
        </button>

        <a href="{{ route('admin.categorias.index') }}"
           class="px-4 py-2 bg-gray-200 rounded">
            Cancelar
        </a>
    </div>
</form>

@endsection
