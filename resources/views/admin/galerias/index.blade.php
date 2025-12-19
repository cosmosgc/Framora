@extends('layouts.admin')

@section('title', 'Galerias')
@section('header', 'Galerias')

@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet"
      href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.min.css">

<table id="galeriasTable" class="display w-full">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Categoria</th>
            <th>Autor</th>
            <th>Fotos</th>
            <th>Valor Foto</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($galerias as $galeria)
            <tr>
                <td>{{ $galeria->id }}</td>
                <td>{{ $galeria->nome }}</td>
                <td>{{ $galeria->categoria->nome ?? '—' }}</td>
                <td>{{ $galeria->user->name ?? '—' }}</td>
                <td>{{ $galeria->fotos_count }}</td>
                <td>
                    @if($galeria->valor_foto)
                        R$ {{ number_format($galeria->valor_foto, 2, ',', '.') }}
                    @else
                        —
                    @endif
                </td>
                <td class="flex gap-2">
                    <a href="{{ route('admin.galerias.show', $galeria->id) }}"
                    class="text-blue-600 hover:underline">
                        Ver fotos
                    </a>

                    @if(auth()->user()?->isAdmin())
                        <form action="{{ route('admin.galerias.destroy', $galeria->id) }}"
                            method="POST"
                            onsubmit="return confirm('ATENÇÃO: Isso apagará a galeria e TODAS as fotos. Deseja continuar?')">
                            @csrf
                            @method('DELETE')

                            <button class="text-red-600 hover:underline">
                                Excluir
                            </button>
                        </form>
                    @endif
                </td>

            </tr>
        @endforeach
    </tbody>
</table>

<script src="https://cdn.datatables.net/2.3.5/js/dataTables.min.js"></script>
<script>
new DataTable('#galeriasTable', {
    pageLength: 10,
    order: [[0, 'desc']],
    language: {
        search: "Buscar:",
        lengthMenu: "Mostrar _MENU_ galerias",
        info: "Mostrando _START_ a _END_ de _TOTAL_ galerias"
    }
});
</script>

@endsection
