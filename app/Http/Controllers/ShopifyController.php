<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Thalia\ShopifyRestToGraphql\Endpoints\OauthEndpoints;
use Thalia\ShopifyRestToGraphql\Endpoints\ShopEndpoints;
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

        AccessToken::updateOrCreate(
            ['access_token' => $token]
        );

        $shop = Shop::updateOrCreate(
            ['domain' => $shop]
        );

        // Save to session for middleware auth later
        session(['shop' => $shop->domain]);

        return redirect()->route('dashboard');
    }

    public function getShopData()
    {
        $shopDomain = session('shop');
        if (!$shopDomain) {
            return redirect()->route('dashboard')->with('error', 'Shop not found in session.');
        }

        $shop = Shop::where('domain', $shopDomain)->first();
        if (!$shop) {
            return redirect()->route('dashboard')->with('error', 'Shop not found. Please reinstall the app.');
        }

        $token = AccessToken::select('access_token')->first();
        if (!$token) {
            return redirect()->route('dashboard')->with('error', 'Access token not found.');
        }

        try {
            $endpoint = new ShopEndpoints($shopDomain, $token->access_token);
            $shopDetails = $endpoint->shopInfo();

            $shop->update([
                'name' => $shopDetails['name'] ?? $shop->name
            ]);

            $shopData = json_decode(json_encode($shopDetails));
            return view('shop', ['shop' => $shopData]);

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Unable to fetch shop data. ' . $e->getMessage());
        }
    }
}
