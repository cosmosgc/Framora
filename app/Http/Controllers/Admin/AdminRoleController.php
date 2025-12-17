<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        // Only HOST can create roles
        if (Auth::id() !== 1) {
            abort(403);
        }

        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        if (Auth::id() !== 1) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'description' => 'nullable|string|max:255',
        ]);

        Role::create($validated);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        if (Auth::id() !== 1) {
            abort(403);
        }

        $role = Role::findOrFail($id);

        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::id() !== 1) {
            abort(403);
        }

        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
        ]);

        $role->update($validated);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        if (Auth::id() !== 1) {
            abort(403);
        }

        $role = Role::findOrFail($id);

        // Optional safety: prevent deleting admin role
        if ($role->name === 'admin') {
            return back()->withErrors('Admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted.');
    }
}
