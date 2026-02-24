<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::withCount('users')->get();

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        session()->flash('success', 'Departman başarıyla oluşturuldu.');

        return redirect()->route('admin.departments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,'.$department->id,
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        session()->flash('success', 'Departman başarıyla güncellendi.');

        return redirect()->route('admin.departments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        if ($department->users()->count() > 0) {
            session()->flash('error', 'Bu departmana bağlı kullanıcılar var. Önce kullanıcıları taşıyın veya silin.');

            return back();
        }

        $department->delete();

        session()->flash('success', 'Departman silindi.');

        return redirect()->route('admin.departments.index');
    }
}
