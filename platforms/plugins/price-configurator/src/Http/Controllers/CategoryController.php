<?php

namespace Botble\PriceConfigurator\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    public function index()
    {
        $items = DB::table('pc_customer_categories')->orderBy('code')->paginate(20);
        return view('price-configurator::categories.index', compact('items'));
    }

    public function create()
    {
        return view('price-configurator::categories.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:191|unique:pc_customer_categories,code',
            'label' => 'required|string|max:191',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        DB::table('pc_customer_categories')->insert([
            'code' => $data['code'],
            'label' => $data['label'],
            'status' => $data['status'],
            'description' => $data['description'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Redirect::route('pc.categories.index')->with('status', 'Kategorie erstellt.');
    }

    public function edit($id)
    {
        $item = DB::table('pc_customer_categories')->find($id);
        abort_if(! $item, 404);
        return view('price-configurator::categories.form', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = DB::table('pc_customer_categories')->find($id);
        abort_if(! $item, 404);

        $data = $request->validate([
            'code' => 'required|string|max:191|unique:pc_customer_categories,code,' . $id,
            'label' => 'required|string|max:191',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        DB::table('pc_customer_categories')->where('id', $id)->update([
            'code' => $data['code'],
            'label' => $data['label'],
            'status' => $data['status'],
            'description' => $data['description'] ?? null,
            'updated_at' => now(),
        ]);

        return Redirect::route('pc.categories.index')->with('status', 'Kategorie aktualisiert.');
    }

    public function destroy($id)
    {
        DB::table('pc_customer_categories')->where('id', $id)->delete();
        return Redirect::route('pc.categories.index')->with('status', 'Kategorie gelöscht.');
    }

    public function toggle($id)
    {
        $item = DB::table('pc_customer_categories')->find($id);
        abort_if(! $item, 404);
        $new = $item->status === 'active' ? 'inactive' : 'active';
        DB::table('pc_customer_categories')->where('id', $id)->update(['status' => $new, 'updated_at' => now()]);
        return Redirect::route('pc.categories.index')->with('status', 'Status geändert.');
    }
}
