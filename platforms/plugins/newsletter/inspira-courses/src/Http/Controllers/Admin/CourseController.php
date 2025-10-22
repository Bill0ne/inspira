<?php

namespace Botble\InspiraCourses\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Botble\InspiraCourses\Models\{Course, Instructor};

class CourseController extends Controller
{
    public function index()
    {
        $items = Course::with('instructor')->latest()->paginate();
        return view('inspira-courses::admin.courses.index', compact('items'));
    }

    public function create()
    {
        $instructors = Instructor::orderBy('name')->pluck('name','id');
        return view('inspira-courses::admin.courses.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:insp_courses,slug',
            'description' => 'nullable|string',
            'instructor_id' => 'nullable|exists:insp_instructors,id',
        ]);
        Course::create($data);
        return redirect()->route('inspira-courses.courses.index')->with('success', __('Saved'));
    }

    public function edit(int $id)
    {
        $item = Course::findOrFail($id);
        $instructors = Instructor::orderBy('name')->pluck('name','id');
        return view('inspira-courses::admin.courses.edit', compact('item','instructors'));
    }

    public function update(Request $request, int $id)
    {
        $item = Course::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:insp_courses,slug,' . $item->id,
            'description' => 'nullable|string',
            'instructor_id' => 'nullable|exists:insp_instructors,id',
        ]);
        $item->update($data);
        return redirect()->route('inspira-courses.courses.index')->with('success', __('Updated'));
    }

    public function destroy(int $id)
    {
        $item = Course::findOrFail($id);
        $item->delete();
        return redirect()->route('inspira-courses.courses.index')->with('success', __('Deleted'));
    }
}
