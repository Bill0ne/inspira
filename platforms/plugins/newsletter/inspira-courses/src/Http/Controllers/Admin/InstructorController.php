<?php

namespace Botble\InspiraCourses\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Botble\InspiraCourses\Models\Instructor;

class InstructorController extends Controller
{
    public function index()
    {
        $items = Instructor::latest()->paginate();
        return view('inspira-courses::admin.instructors.index', compact('items'));
    }

    public function create()
    {
        return view('inspira-courses::admin.instructors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:60',
            'bio' => 'nullable|string',
        ]);
        Instructor::create($data);
        return redirect()->route('inspira-courses.instructors.index')->with('success', __('Saved'));
    }

    public function edit(int $id)
    {
        $item = Instructor::findOrFail($id);
        return view('inspira-courses::admin.instructors.edit', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = Instructor::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:60',
            'bio' => 'nullable|string',
        ]);
        $item->update($data);
        return redirect()->route('inspira-courses.instructors.index')->with('success', __('Updated'));
    }

    public function destroy(int $id)
    {
        $item = Instructor::findOrFail($id);
        $item->delete();
        return redirect()->route('inspira-courses.instructors.index')->with('success', __('Deleted'));
    }
}
