<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
       
        $shopName = User::select('shop_name')->first();

        return view('dashboard', ['shopName' => $shopName->shop_name]);
    }
}
