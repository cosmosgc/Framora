<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    /**
     * GET /admin/users
     */
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * PUT /admin/users/{id}
     */
    public function update(Request $request, $id)
    {
        // Only HOST (id = 1) can manage roles
        if (Auth::id() !== 1) {
            abort(403, 'Only the host user can manage roles.');
        }

        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::findOrFail($id);

        // Prevent removing admin from HOST itself
        if ($user->isHost()) {
            return back()->withErrors('The host user roles cannot be modified.');
        }

        $user->roles()->sync($validated['roles'] ?? []);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Roles updated successfully.');
    }

    /**
     * DELETE /admin/users/{id}
     */
    public function destroy($id)
    {
        if (Auth::id() !== 1) {
            abort(403);
        }

        if ($id == 1) {
            return back()->withErrors('Host user cannot be deleted.');
        }

        User::findOrFail($id)->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User removed.');
    }
}
