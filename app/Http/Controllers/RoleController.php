<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Support\Audit;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('module');

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
            'label' => $validated['label'],
        ]);

        if (! empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        Audit::record('admin.role.created', $role, [], [
            'name' => $role->name,
            'label' => $role->label,
            'permissions' => $role->permissions()->pluck('id')->all(),
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Rol başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('module');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $validated = $request->validated();
        $oldValues = [
            'name' => $role->name,
            'label' => $role->label,
            'permissions' => $role->permissions()->pluck('id')->all(),
        ];

        $role->update([
            'name' => $validated['name'],
            'label' => $validated['label'],
        ]);

        if (array_key_exists('permissions', $validated) && ! empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        } else {
            $role->permissions()->detach();
        }

        Audit::record('admin.role.updated', $role, $oldValues, [
            'name' => $role->name,
            'label' => $role->label,
            'permissions' => $role->permissions()->pluck('id')->all(),
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Rol başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $oldValues = [
            'name' => $role->name,
            'label' => $role->label,
            'permissions' => $role->permissions()->pluck('id')->all(),
        ];
        $role->delete();
        Audit::record('admin.role.deleted', null, $oldValues, ['deleted_role_id' => $role->id]);

        return redirect()->route('admin.roles.index')->with('success', 'Rol başarıyla silindi.');
    }
}
