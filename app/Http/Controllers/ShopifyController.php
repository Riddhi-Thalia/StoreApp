<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Thalia\ShopifyRestToGraphql\Endpoints\OauthEndpoints;
use Thalia\ShopifyRestToGraphql\Endpoints\ShopEndpoints;
use Thalia\ShopifyRestToGraphql\Endpoints\RecurringApplicationChargesEndpoints;
use Thalia\ShopifyRestToGraphql\GraphqlService;
use App\Models\AccessToken;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;

class ShopifyController extends Controller
{
    public function install(Request $request)
    {
        $shop = $request->get('shop');
        $scopes = 'read_products,write_orders';
        $redirectUri = env('SHOPIFY_APP_REDIRECT_URI');

        return redirect()->away("https://{$shop}/admin/oauth/authorize?client_id=" . env('SHOPIFY_API_KEY') . "&scope={$scopes}&redirect_uri={$redirectUri}");
    }

    public function callback(Request $request)
    {
        $shopDomain = env('SHOPIFY_SHOP_DOMAIN');
        $code = $request->get('code');
        $chargeId = $request->query('charge_id') ?? '';

        if (!$shopDomain) {
            return redirect()->route('subscribe')->withErrors('Missing shop domain.');
        }

        try {
            if ($code) {
                $oauthEndpoint = new OauthEndpoints($shopDomain, env('SHOPIFY_API_KEY'), env('SHOPIFY_API_SECRET'));
                $token = $oauthEndpoint->getAccessToken($code);

                AccessToken::updateOrCreate(['access_token' => $token, 'charge_id' => $chargeId]);
                Shop::updateOrCreate(['domain' => $shopDomain]);
            }

            $token = AccessToken::first()->access_token;
            $subscription = new RecurringApplicationChargesEndpoints($shopDomain, $token);

            $response = $subscription->currentAppInstallationForRecurring($chargeId);
            $isSubscribed = isset($response['status']) && $response['status'] === 'active';

            if (!$isSubscribed) {
                return redirect()->route('subscribe');
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error("OAuth Callback Error: " . $e->getMessage());
            return redirect()->route('subscribe')->withErrors('Callback failed: ' . $e->getMessage());
        }
    }

    public function subscribePlan(Request $request)
    {
        $shopDomain = env('SHOPIFY_SHOP_DOMAIN');
        $token = AccessToken::first()->access_token;

        $charge = new RecurringApplicationChargesEndpoints($shopDomain, $token);

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
            return redirect()->route('subscribe')->withErrors('Unable to create subscription.');
        }

        return redirect()->away($response['confirmation_url']);
    }

    public function getShopData()
    {
        $shopDomain = env('SHOPIFY_SHOP_DOMAIN');

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

    public function showSubscribeForm()
    {
        return view('subscribe');
    }
}
