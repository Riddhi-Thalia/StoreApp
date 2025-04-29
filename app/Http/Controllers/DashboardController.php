<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
       
        $shopName = session('shop_name');

        return view('dashboard', ['shopName' => $shopName]);
    }
}
