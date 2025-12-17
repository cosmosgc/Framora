@extends('layouts.admin')

@section('title', 'New Role')
@section('header', 'Create Role')

@section('content')

<form action="{{ route('admin.roles.store') }}"
      method="POST"
      class="max-w-xl bg-white p-6 rounded shadow">
    @csrf

    <div class="mb-4">
        <label class="block font-medium mb-1">Name</label>
        <input type="text" name="name"
               class="w-full border rounded px-3 py-2"
               placeholder="admin"
               required>
    </div>

    <div class="mb-6">
        <label class="block font-medium mb-1">Description</label>
        <textarea name="description"
                  class="w-full border rounded px-3 py-2"
                  rows="3"></textarea>
    </div>

    <div class="flex gap-2">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">
            Save
        </button>

        <a href="{{ route('admin.roles.index') }}"
           class="px-4 py-2 bg-gray-200 rounded">
            Cancel
        </a>
    </div>
</form>

@endsection
