<?php

namespace Botble\InspiraCourses\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Botble\InspiraCourses\Models\CoursePurchase;

class PurchaseController extends Controller
{
    public function index()
    {
        $items = CoursePurchase::with('session.course')->latest()->paginate();
        return view('inspira-courses::admin.purchases.index', compact('items'));
    }

    public function show(int $id)
    {
        $item = CoursePurchase::with('session.course')->findOrFail($id);
        return view('inspira-courses::admin.purchases.show', compact('item'));
    }

    public function destroy(int $id)
    {
        $item = CoursePurchase::findOrFail($id);
        $item->delete();
        return redirect()->route('inspira-courses.purchases.index')->with('success', __('Deleted'));
    }
}
