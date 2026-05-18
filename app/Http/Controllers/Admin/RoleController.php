<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::query()
            ->with('permissions')
            ->orderBy('name')
            ->get()
            ->map(function (Role $role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->sort()->values()->all(),
                ];
            });

        return Inertia::render('admin/roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function create(): Response
    {
        $permissions = Permission::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Permission $permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
            ]);

        return Inertia::render('admin/roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->where('guard_name', 'web')],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Rol creado correctamente.',
            ]);
    }

    public function edit(Role $role): Response
    {
        $permissions = Permission::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Permission $permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
            ]);

        $roleData = [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->sort()->values()->all(),
        ];

        return Inertia::render('admin/roles/Edit', [
            'role' => $roleData,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($role->id),
            ],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);

        if ($role->name === 'admin' && $data['name'] !== 'admin') {
            throw ValidationException::withMessages([
                'name' => 'El rol admin no se puede renombrar porque protege el panel.',
            ]);
        }

        $role->name = $data['name'];
        $role->save();

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Rol actualizado correctamente.',
            ]);
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'admin') {
            return redirect()
                ->route('admin.roles')
                ->with('toast', [
                    'type' => 'warning',
                    'message' => 'No se puede eliminar el rol admin.',
                ]);
        }

        $role->delete();

        return redirect()
            ->route('admin.roles')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Rol eliminado correctamente.',
            ]);
    }
}
