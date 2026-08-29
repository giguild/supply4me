<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Role::withCount('permissions')->withCount('users');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $roles = $query->orderBy('name')->paginate(15)->withQueryString();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return Inertia::render('Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Role created successfully');
    }

    public function show(Role $role): Response
    {
        $role->load(['permissions', 'users']);

        $permissionsByModule = $role->permissions->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return Inertia::render('Roles/Show', [
            'role' => $role,
            'permissionsByModule' => $permissionsByModule,
        ]);
    }

    public function edit(Role $role): Response
    {
        $role->load('permissions');

        $allPermissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $allPermissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    public function update(Request $request, Role $role): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions($validated['permissions']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role): \Illuminate\Http\RedirectResponse
    {
        if ($role->name === 'super_admin') {
            return back()->withErrors(['error' => 'Cannot delete the super admin role']);
        }

        $role->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
