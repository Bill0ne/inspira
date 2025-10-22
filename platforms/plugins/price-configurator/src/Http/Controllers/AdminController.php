<?php

namespace Botble\PriceConfigurator\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class AdminController extends Controller
{
    public function index()
    {
        $tiers = DB::table('pc_price_tiers')->orderBy('priority')->get();
        $rules = DB::table('pc_price_rules')->orderBy('id')->get();
        $categories = DB::table('pc_customer_categories')->orderBy('code')->get();
        return view('price-configurator::dashboard', compact('tiers', 'rules', 'categories'));
    }

    public function setCustomerCategory(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'customer_category' => 'required|string|max:191',
        ]);

        $updated = DB::table('ht_customers')->where('email', $data['email'])->update([
            'customer_category' => $data['customer_category'],
        ]);

        return Redirect::route('pc.admin.index')->with('status', $updated ? 'Kundenkategorie gesetzt.' : 'Kein Kunde mit dieser E-Mail gefunden.');
    }
}
