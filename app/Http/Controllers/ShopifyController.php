<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Thalia\ShopifyRestToGraphql\Endpoints\OauthEndpoints;
use Thalia\ShopifyRestToGraphql\Endpoints\ShopEndpoints;
use Thalia\ShopifyRestToGraphql\Endpoints\RecurringApplicationChargesEndpoints;
use Thalia\ShopifyRestToGraphql\GraphqlService;
use App\Models\AccessToken;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ShopifyController extends Controller
{
    public function install(Request $request)
    {
        $shopDomain = $request->shop;
        $data = User::where('shop_domain',$shopDomain)->first();

        if (isset($data) && $data->is_installed ) {
            if(!$data->is_subscribed){
                return redirect()->route('subscribe',['shop'=>$shopDomain]);
            }
            return redirect()->route('dashboard');
        }

        $scopes = 'read_products,write_orders';
        $redirectUri = env('SHOPIFY_APP_REDIRECT_URI');
        User::updateOrCreate(['shop_domain' => $shopDomain],
                             ['is_installed'=> true]);

        return redirect()->away("https://{$shopDomain}/admin/oauth/authorize?client_id=" . env('SHOPIFY_API_KEY') . "&scope={$scopes}&redirect_uri={$redirectUri}");
    }

    public function callback(Request $request)
    {
        $shopDomain = $request->shop;
        $code = $request->code;
        $chargeId = $request->charge_id ?? '';

        try {
            if ($code) {
                $oauthEndpoint = new OauthEndpoints($shopDomain, env('SHOPIFY_API_KEY'), env('SHOPIFY_API_SECRET'));
                $token = $oauthEndpoint->getAccessToken($code);

                User::updateOrCreate(
                    ['shop_domain' => $shopDomain],
                    ['access_token' => $token, 'charge_id' => $chargeId]
                );
            }

            $data = User::first();

            $subscription = new RecurringApplicationChargesEndpoints($data->shop_domain, $data->access_token);

            $response = $subscription->currentAppInstallationForRecurring($chargeId);
            $isSubscribed = isset($response['status']) && $response['status'] === 'active';

            if (!$isSubscribed) {
                return redirect()->route('subscribe',['shop'=>$shopDomain]);
            }

            $data->update(
                ['is_subscribed' => true]
            );

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error("OAuth Callback Error: " . $e->getMessage());
            return redirect()->route('subscribe',['shop'=>$shopDomain])->withErrors('Callback failed: ' . $e->getMessage());
        }
    }

    public function subscribePlan(Request $request)
    {
        $data = User::where('shop_domain',$request->shop)->first();
     
        $charge = new RecurringApplicationChargesEndpoints($data->shop_domain, $data->access_token);

        $params = [
            'recurring_application_charge' => [
                'name' => 'Basic Plan',
                'price' => '10.0',
                'return_url' => route('auth.callback'),
                'trial_days' => 28,
                'test' => true,
            ]
        ];

        $response = $charge->appSubscriptionCreate($params);

        if (!isset($response['confirmation_url'])) {
            return redirect()->route('subscribe', ['shop', $data->shop_domain])->withErrors('Unable to create subscription.');
        }

        return redirect()->away($response['confirmation_url']);
    }

    public function getShopData()
    {
        $data = User::select('shop_domain','access_token')->first();
        if (!$data->shop_domain) {
            return redirect()->route('dashboard')->with('error', 'Shop not found. Please reinstall the app.');
        }

        if (!$data->access_token) {
            return redirect()->route('dashboard')->with('error', 'Access token not found.');
        }

        try {
            $endpoint = new ShopEndpoints($data->shop_domain, $data->access_token);
            $shopDetails = $endpoint->shopInfo();
            
            User::updateOrCreate(
                ['shop_domain' => $data->shop_domain],
                ['shop_name' => $shopDetails['name'] ?? $data->shop_name]
            );
            

            $shopData = json_decode(json_encode($shopDetails));
            return view('shop', ['shop' => $shopData]);

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Unable to fetch shop data. ' . $e->getMessage());
        }
    }

    public function showSubscribeForm(Request $request,$shop)
    {
        return view('subscribe', ['shop' => $shop]);
    }

    public function checkApp(Request $request){
        $data = User::where('shop_domain',$request->shop)->first();

        if (isset($data) && $data->is_installed ) {
            if(!$data->is_subscribed){
                return redirect()->route('subscribe',['shop'=>$request->shop]);
            }
            return redirect()->route('dashboard');
        }else{

            return redirect()->route('install', $request->all());
        }
    }
}
