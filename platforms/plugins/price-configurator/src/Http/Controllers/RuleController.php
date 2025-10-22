<?php

namespace Botble\PriceConfigurator\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class RuleController extends Controller
{
    public function index()
    {
        $items = DB::table('pc_price_rules')->orderBy('id', 'desc')->paginate(20);
        $tiers = DB::table('pc_price_tiers')->orderBy('priority')->get();
        $categories = DB::table('pc_customer_categories')->orderBy('code')->get();
        return view('price-configurator::rules.index', compact('items','tiers','categories'));
    }

    public function create()
    {
        $tiers = DB::table('pc_price_tiers')->orderBy('priority')->get();
        $categories = DB::table('pc_customer_categories')->orderBy('code')->get();
        $roomCategories = DB::table('ht_room_categories')->orderBy('name')->get();
        return view('price-configurator::rules.form', compact('tiers','categories','roomCategories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'price_tier_id' => 'required|exists:pc_price_tiers,id',
            'customer_category' => 'required|string|max:191',
            'scope' => 'required|in:all_rooms,by_room_category',
            'calculation_type' => 'required|in:percent,absolute',
            'calculation_value' => 'required|numeric',
            'rounding_mode' => 'required|in:none,up,down,bankers',
            'round_to' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'room_category_ids' => 'array',
            'room_category_ids.*' => 'integer',
        ]);

        $rid = DB::table('pc_price_rules')->insertGetId([
            'price_tier_id' => $data['price_tier_id'],
            'customer_category' => $data['customer_category'],
            'scope' => $data['scope'],
            'calculation_type' => $data['calculation_type'],
            'calculation_value' => $data['calculation_value'],
            'rounding_mode' => $data['rounding_mode'],
            'round_to' => $data['round_to'],
            'status' => $data['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($data['scope'] === 'by_room_category' && ! empty($data['room_category_ids'])) {
            foreach ($data['room_category_ids'] as $cid) {
                DB::table('pc_price_rule_room_category')->insert([
                    'price_rule_id' => $rid,
                    'room_category_id' => $cid,
                ]);
            }
        }

        return Redirect::route('pc.rules.index')->with('status', 'Regel erstellt.');
    }

    public function edit($id)
    {
        $item = DB::table('pc_price_rules')->find($id);
        abort_if(! $item, 404);
        $tiers = DB::table('pc_price_tiers')->orderBy('priority')->get();
        $categories = DB::table('pc_customer_categories')->orderBy('code')->get();
        $roomCategories = DB::table('ht_room_categories')->orderBy('name')->get();
        $selected = DB::table('pc_price_rule_room_category')->where('price_rule_id', $id)->pluck('room_category_id')->all();
        return view('price-configurator::rules.form', compact('item','tiers','categories','roomCategories','selected'));
    }

    public function update(Request $request, $id)
    {
        $item = DB::table('pc_price_rules')->find($id);
        abort_if(! $item, 404);

        $data = $request->validate([
            'price_tier_id' => 'required|exists:pc_price_tiers,id',
            'customer_category' => 'required|string|max:191',
            'scope' => 'required|in:all_rooms,by_room_category',
            'calculation_type' => 'required|in:percent,absolute',
            'calculation_value' => 'required|numeric',
            'rounding_mode' => 'required|in:none,up,down,bankers',
            'round_to' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'room_category_ids' => 'array',
            'room_category_ids.*' => 'integer',
        ]);

        DB::table('pc_price_rules')->where('id', $id)->update([
            'price_tier_id' => $data['price_tier_id'],
            'customer_category' => $data['customer_category'],
            'scope' => $data['scope'],
            'calculation_type' => $data['calculation_type'],
            'calculation_value' => $data['calculation_value'],
            'rounding_mode' => $data['rounding_mode'],
            'round_to' => $data['round_to'],
            'status' => $data['status'],
            'updated_at' => now(),
        ]);

        DB::table('pc_price_rule_room_category')->where('price_rule_id', $id)->delete();
        if ($data['scope'] === 'by_room_category' && ! empty($data['room_category_ids'])) {
            foreach ($data['room_category_ids'] as $cid) {
                DB::table('pc_price_rule_room_category')->insert([
                    'price_rule_id' => $id,
                    'room_category_id' => $cid,
                ]);
            }
        }

        return Redirect::route('pc.rules.index')->with('status', 'Regel aktualisiert.');
    }

    public function destroy($id)
    {
        DB::table('pc_price_rule_room_category')->where('price_rule_id', $id)->delete();
        DB::table('pc_price_rules')->where('id', $id)->delete();
        return Redirect::route('pc.rules.index')->with('status', 'Regel gelöscht.');
    }

    public function toggle($id)
    {
        $item = DB::table('pc_price_rules')->find($id);
        abort_if(! $item, 404);
        $new = $item->status === 'active' ? 'inactive' : 'active';
        DB::table('pc_price_rules')->where('id', $id)->update(['status' => $new, 'updated_at' => now()]);
        return Redirect::route('pc.rules.index')->with('status', 'Status geändert.');
    }
}
