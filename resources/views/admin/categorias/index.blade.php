@extends('layouts.admin')

@section('title', 'Categorias')
@section('header', 'Categorias')

@section('content')

@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

<div class="mb-4 flex justify-end">
    <a href="{{ route('admin.categorias.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        + Nova Categoria
    </a>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left">Nome</th>
                <th class="p-3 text-left">Descrição</th>
                <th class="p-3 text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categorias as $categoria)
                <tr class="border-t">
                    <td class="p-3 font-medium">
                        {{ $categoria->nome }}
                    </td>
                    <td class="p-3 text-gray-600">
                        {{ $categoria->descricao }}
                    </td>
                    <td class="p-3 text-center space-x-2">
                        <a href="{{ route('admin.categorias.edit', $categoria->id) }}"
                           class="text-blue-600 hover:underline">
                            Editar
                        </a>

                        <form action="{{ route('admin.categorias.destroy', $categoria->id) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Deseja excluir esta categoria?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-4 text-center text-gray-500">
                        Nenhuma categoria encontrada.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
