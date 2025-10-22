<?php

namespace Botble\InspiraCourses\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Botble\InspiraCourses\Models\{Course, CourseSession};

class SessionController extends Controller
{
    public function index()
    {
        $items = CourseSession::with('course')->latest()->paginate();
        return view('inspira-courses::admin.sessions.index', compact('items'));
    }

    public function create()
    {
        $courses = Course::orderBy('name')->pluck('name','id');
        return view('inspira-courses::admin.sessions.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:insp_courses,id',
            'starts_at' => 'required|date',
            'ends_at'   => 'nullable|date|after:starts_at',
            'location'  => 'nullable|string|max:255',
            'capacity'  => 'required|integer|min:0',
            'price'     => 'required|numeric|min:0',
        ]);
        $data['seats_sold'] = 0;
        CourseSession::create($data);
        return redirect()->route('inspira-courses.sessions.index')->with('success', __('Saved'));
    }

    public function edit(int $id)
    {
        $item = CourseSession::findOrFail($id);
        $courses = Course::orderBy('name')->pluck('name','id');
        return view('inspira-courses::admin.sessions.edit', compact('item','courses'));
    }

    public function update(Request $request, int $id)
    {
        $item = CourseSession::findOrFail($id);
        $data = $request->validate([
            'course_id' => 'required|exists:insp_courses,id',
            'starts_at' => 'required|date',
            'ends_at'   => 'nullable|date|after:starts_at',
            'location'  => 'nullable|string|max:255',
            'capacity'  => 'required|integer|min:0',
            'price'     => 'required|numeric|min:0',
        ]);
        $item->update($data);
        return redirect()->route('inspira-courses.sessions.index')->with('success', __('Updated'));
    }

    public function destroy(int $id)
    {
        $item = CourseSession::findOrFail($id);
        $item->delete();
        return redirect()->route('inspira-courses.sessions.index')->with('success', __('Deleted'));
    }
}
