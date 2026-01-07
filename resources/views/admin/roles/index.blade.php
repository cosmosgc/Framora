@extends('layouts.admin')

@section('title', 'Roles')
@section('header', 'Roles')

@section('content')

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

<div class="mb-4 flex justify-end">
    @if(auth()->id() === 1 || auth()->user()?->isHost())
        <a href="{{ route('admin.roles.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded">
            + New Role
        </a>
    @endif
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Description</th>
                <th class="p-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
                <tr class="border-t">
                    <td class="p-3 font-medium">{{ $role->name }}</td>
                    <td class="p-3 text-gray-600">{{ $role->description }}</td>
                    <td class="p-3 text-center space-x-2">
                        @if(auth()->id() === 1 || auth()->user()?->isHost())
                            <a href="{{ route('admin.roles.edit', $role->id) }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>

                            @if($role->name !== 'admin')
                                <form action="{{ route('admin.roles.destroy', $role->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Delete this role?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-4 text-center text-gray-500">
                        No roles found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
