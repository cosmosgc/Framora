@extends('layouts.admin')

@section('title', 'Users')
@section('header', 'Users')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<link rel="stylesheet"
      href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.min.css">
@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        {{ $errors->first() }}
    </div>
@endif
<table id="usersTable" class="display w-full">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Roles</th>
            <th>Manage</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>
                    {{ $user->name }}
                    @if($user->isHost())
                        <span class="text-xs text-blue-600">(HOST)</span>
                    @endif
                </td>
                <td>{{ $user->email }}</td>
                <td>
                    {{ $user->roles->pluck('name')->join(', ') ?: '—' }}
                </td>
                <td>
                    @if(auth()->user()?->isHost() && !$user->isHost())
                        <form action="{{ route('admin.users.update', $user->id) }}"
                            method="POST"
                            class="flex gap-2">
                            @csrf
                            @method('PUT')

                            <select name="roles[]" multiple
                                    class="border rounded px-2 py-1 text-sm">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}"
                                        @selected($user->roles->contains($role->id))>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button class="px-2 py-1 bg-blue-600 text-white rounded text-sm">
                                Save
                            </button>
                        </form>
                    @else
                        —
                    @endif
                </td>

            </tr>
        @endforeach
    </tbody>
</table>
<a href="{{ route('admin.roles.index') }}">🧩 Roles</a>
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.min.js"></script>
<script>
new DataTable('#usersTable', {
    pageLength: 10,
    order: [[0, 'asc']],
    language: {
        search: "Search:",
        lengthMenu: "Show _MENU_ users",
        info: "Showing _START_ to _END_ of _TOTAL_ users"
    }
});
</script>

@endsection
