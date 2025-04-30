<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
       
        $shopName = Shop::select('name')->first();

        return view('dashboard', ['shopName' => $shopName->name]);
    }
}
