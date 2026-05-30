<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::query();

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // Sort
        $sort = $request->input('sort', 'latest');
        if ($sort === 'latest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'name') {
            $query->orderBy('name');
        } elseif ($sort === 'name_desc') {
            $query->orderByDesc('name');
        }

        $departments = $query->paginate(10);

        return view('dashboard.departments.index', compact('departments'));
    }

    public function show(Department $department)
    {
        return view('dashboard.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('dashboard.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $department->update($validated);

        return redirect()->route('dashboard.departments.index')->with('success', 'Department updated successfully.');
    }

    public function create()
    {
        return view('dashboard.departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Department::create($validated);

        return redirect()->route('dashboard.departments.index')->with('success', 'Department created successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('dashboard.departments.index')->with('success', 'Department deleted successfully.');
    }
}