<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Thalia\ShopifyRestToGraphql\Endpoints\OauthEndpoints;
use GuzzleHttp\Client;
use App\Models\AccessToken;
use App\Models\Shop;

class ShopifyController extends Controller
{
    public function install(Request $request)
    {
        $shop = $request->get('shop');
        
        // Shopify OAuth URL to authorize the app
        $scopes = 'read_products,write_orders'; // Adjust scopes as needed
        $redirectUri = env('SHOPIFY_APP_REDIRECT_URI');  // This is the callback URL

        // Redirect the user to Shopify's OAuth page
        return redirect()->away("https://{$shop}/admin/oauth/authorize?client_id=" . env('SHOPIFY_API_KEY') . "&scope={$scopes}&redirect_uri={$redirectUri}");
    }

    public function callback(Request $request)
    {
        $shop = $request->get('shop');
        $code = $request->get('code');

        $oauthEndpoint = new OauthEndpoints($shop, env('SHOPIFY_API_KEY'), env('SHOPIFY_API_SECRET'));
        $token = $oauthEndpoint->getAccessToken($code);

        // Save or update the token
        $storeToken = AccessToken::updateOrCreate(
            ['access_token' => $token]
        );

        $shopModel = Shop::updateOrCreate(
            ['domain' => $shop]
        );

        // Save to session for middleware auth later
        session(['shop' => $shopModel->domain]);

        return redirect()->route('dashboard');
    }

    public function getShopData()
    {
        $shopDomain = session('shop');
        $shop = Shop::where('domain', $shopDomain)->first();
        
        if (!$shop) {
            session()->flash('error', 'Shop not found. Please reinstall the app.');
            return redirect()->route('install');
        }
        
        $token = AccessToken::select('access_token')->first();
        try {
            $client = new Client();
            $response = $client->get("https://{$shopDomain}/admin/api/2025-04/shop.json", [
                'headers' => [
                    'X-Shopify-Access-Token' => $token->access_token,
                ],
            ]);

            $shopData = json_decode($response->getBody()->getContents());

            $shop->update([
                'name' => $shopData->shop->name
            ]);

            // Save to session for middleware auth later
            session(['shop_name' => $shopData->shop->name]);

            return view('shop', ['shop' => $shopData->shop]);

        } catch (\Exception $e) {
            session()->flash('error', 'Unable to fetch shop data. Please check your connection or token.');
            return redirect()->route('dashboard');
        }
    }

    
}
