<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Services\CloudinaryUploadService;
use App\Services\CroppedImageUploadService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(
        private CloudinaryUploadService $cloudinaryUploadService,
        private CroppedImageUploadService $croppedImageUploadService
    )
    {
    }
    public function index(Request $request)
    {
        $query = Department::with('leader');

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
        $department->load(['leader', 'users']);

        return view('dashboard.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastor()) {
            return back()->with('error', 'You are not authorized to edit this department.');
        }

        $department->load(['leader', 'users']);
        $users = User::orderBy('name')->get();

        return view('dashboard.departments.edit', compact('department', 'users'));
    }

    public function update(Request $request, Department $department)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastor()) {
            return back()->with('error', 'You are not authorized to update this department.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,created,suspended',
            'image_source' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'image_cropped' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'users' => 'nullable|array',
            'users.*' => 'integer|exists:users,id',
        ]);

        $memberIds = array_map('intval', $validated['users'] ?? []);

        $data = collect($validated)
            ->except(['image_source', 'image_cropped', 'users'])
            ->toArray();

        if ($request->hasFile('image_source')) {
            $request->validate([
                'image_cropped' => ['required', 'string'],
            ]);

            if ($department->image) {
                $this->cloudinaryUploadService->deleteByUrl($department->image, 'image');
            }

            $data['image'] = $this->croppedImageUploadService
                ->storeFromDataUrl($request->string('image_cropped')->toString(), 'departments', 'department-cover', 'image_cropped')['url'];
        }

        $department->update($data);
        $department->users()->sync($memberIds);

        return redirect()->route('dashboard.departments.index')->with('success', 'Department updated successfully.');
    }

    public function create()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastor()) {
            return back()->with('error', 'You are not authorized to create a department.');
        }

        $users = User::orderBy('name')->get();

        return view('dashboard.departments.create', compact('users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastor()) {
            return back()->with('error', 'You are not authorized to create a department.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,created,suspended',
            'image_source' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'image_cropped' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'users' => 'nullable|array',
            'users.*' => 'integer|exists:users,id',
        ]);

        $memberIds = array_map('intval', $validated['users'] ?? []);

        $data = collect($validated)
            ->except(['image_source', 'image_cropped', 'users'])
            ->toArray();

        if ($request->hasFile('image_source')) {
            $request->validate([
                'image_cropped' => ['required', 'string'],
            ]);

            $data['image'] = $this->croppedImageUploadService
                ->storeFromDataUrl($request->string('image_cropped')->toString(), 'departments', 'department-cover', 'image_cropped')['url'];
        }

        $department = Department::create($data);
        $department->users()->sync($memberIds);

        return redirect()->route('dashboard.departments.index')->with('success', 'Department created successfully.');
    }

    public function destroy(Department $department)
    {
        $this->deleteDepartmentRecord($department);

        return redirect()->route('dashboard.departments.index')->with('success', 'Department deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'selected_ids' => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'exists:departments,id'],
        ]);

        $departments = Department::whereIn('id', $validated['selected_ids'])->get();

        foreach ($departments as $department) {
            $this->deleteDepartmentRecord($department);
        }

        return redirect()->route('dashboard.departments.index')->with('success', $departments->count() . ' department(s) deleted successfully.');
    }

    private function deleteDepartmentRecord(Department $department): void
    {
        if ($department->image) {
            $this->cloudinaryUploadService->deleteByUrl($department->image, 'image');
        }

        $department->delete();
    }
}
