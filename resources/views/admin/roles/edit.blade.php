@extends('layouts.admin')

@section('title', 'Edit Role')
@section('header', 'Edit Role')

@section('content')

<form action="{{ route('admin.roles.update', $role->id) }}"
      method="POST"
      class="max-w-xl bg-white p-6 rounded shadow">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="block font-medium mb-1">Name</label>
        <input type="text" name="name"
               class="w-full border rounded px-3 py-2"
               value="{{ $role->name }}"
               required>
    </div>

    <div class="mb-6">
        <label class="block font-medium mb-1">Description</label>
        <textarea name="description"
                  class="w-full border rounded px-3 py-2"
                  rows="3">{{ $role->description }}</textarea>
    </div>

    <div class="flex gap-2">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">
            Update
        </button>

        <a href="{{ route('admin.roles.index') }}"
           class="px-4 py-2 bg-gray-200 rounded">
            Cancel
        </a>
    </div>
</form>

@endsection
