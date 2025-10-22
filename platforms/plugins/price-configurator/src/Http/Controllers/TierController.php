<?php

namespace Botble\PriceConfigurator\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TierController extends Controller
{
    public function index()
    {
        $items = DB::table('pc_price_tiers')->orderBy('priority')->paginate(20);
        return view('price-configurator::tiers.index', compact('items'));
    }

    public function create()
    {
        return view('price-configurator::tiers.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191|unique:pc_price_tiers,name',
            'priority' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'is_exclusive' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'notes' => 'nullable|string',
        ]);

        DB::table('pc_price_tiers')->insert([
            'name' => $data['name'],
            'priority' => $data['priority'] ?? 100,
            'status' => $data['status'],
            'is_exclusive' => $request->boolean('is_exclusive'),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Redirect::route('pc.tiers.index')->with('status', 'Stufe erstellt.');
    }

    public function edit($id)
    {
        $item = DB::table('pc_price_tiers')->find($id);
        abort_if(! $item, 404);
        return view('price-configurator::tiers.form', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = DB::table('pc_price_tiers')->find($id);
        abort_if(! $item, 404);

        $data = $request->validate([
            'name' => 'required|string|max:191|unique:pc_price_tiers,name,' . $id,
            'priority' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'is_exclusive' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'notes' => 'nullable|string',
        ]);

        DB::table('pc_price_tiers')->where('id', $id)->update([
            'name' => $data['name'],
            'priority' => $data['priority'] ?? $item->priority,
            'status' => $data['status'],
            'is_exclusive' => $request->boolean('is_exclusive'),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'notes' => $data['notes'] ?? null,
            'updated_at' => now(),
        ]);

        return Redirect::route('pc.tiers.index')->with('status', 'Stufe aktualisiert.');
    }

    public function destroy($id)
    {
        DB::table('pc_price_rules')->where('price_tier_id', $id)->delete();
        DB::table('pc_price_tiers')->where('id', $id)->delete();
        return Redirect::route('pc.tiers.index')->with('status', 'Stufe gelöscht.');
    }

    public function toggle($id)
    {
        $item = DB::table('pc_price_tiers')->find($id);
        abort_if(! $item, 404);
        $new = $item->status === 'active' ? 'inactive' : 'active';
        DB::table('pc_price_tiers')->where('id', $id)->update(['status' => $new, 'updated_at' => now()]);
        return Redirect::route('pc.tiers.index')->with('status', 'Status geändert.');
    }
}
