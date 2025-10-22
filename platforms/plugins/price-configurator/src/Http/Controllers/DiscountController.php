<?php

namespace Botble\PriceConfigurator\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\PriceConfigurator\Models\QuantityDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DiscountController extends BaseController
{
    public function index() {
        try {
        $items = QuantityDiscount::query()->orderByDesc('priority')->paginate(20);
        } catch (\Throwable $e) {
            $items = collect([]);
            session()->flash('warning', 'Hinweis: Tabelle pc_quantity_discounts fehlt. Bitte Migration ausführen.');
        }
        return view('price-configurator::discounts.index', compact('items'));
    }

    public function create() {
        return view('price-configurator::discounts.form', ['item' => new QuantityDiscount()]);
    }

    public function store(Request $request) {
        $data = $this->validated($request);
        QuantityDiscount::create($data);
        Cache::forget('pc_quantity_discounts_active');
        return redirect()->route('pc.discounts.index')->with('status','Gespeichert');
    }

    public function edit($id) {
        $item = QuantityDiscount::findOrFail($id);
        return view('price-configurator::discounts.form', compact('item'));
    }

    public function update(Request $request, $id) {
        $item = QuantityDiscount::findOrFail($id);
        $item->update($this->validated($request));
        Cache::forget('pc_quantity_discounts_active');
        return redirect()->route('pc.discounts.index')->with('status','Aktualisiert');
    }

    public function destroy($id) {
        QuantityDiscount::whereKey($id)->delete();
        Cache::forget('pc_quantity_discounts_active');
        return back()->with('status','Gelöscht');
    }

    public function toggle($id) {
        $item = QuantityDiscount::findOrFail($id);
        $item->status = $item->status === 'active' ? 'inactive' : 'active';
        $item->save();
        Cache::forget('pc_quantity_discounts_active');
        return back()->with('status','Status geändert');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title'          => 'required|string|max:255',
            'condition_type' => 'required|in:hours,bookings',
            'range_min'      => 'nullable|integer|min:0',
            'range_max'      => 'nullable|integer|min:0',
            'discount_type'  => 'required|in:absolute,percent',
            'discount_value' => 'required|numeric',
            'apply_to'       => 'required|in:all,room',
            'priority'       => 'required|integer',
            'status'         => 'required|in:active,inactive',
        ]);
    }
}
