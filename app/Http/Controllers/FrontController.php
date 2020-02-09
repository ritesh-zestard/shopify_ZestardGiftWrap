<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\ShopModel;
use App\AppSetting;
use App\Symbol;
use App\UserSetting;
use App\GiftWrapSettings;
use DB;

class FrontController extends Controller {

    public function index(Request $request) {
        $sh = App::make('ShopifyAPI');
        $shopDetail = UserSetting::where('store_encrypt', $request->id)->first();        
        $giftWrap = GiftWrapSettings::where('shop_id', $shopDetail->id)->first();
        // check app is enable or disable
        if ($giftWrap->status) {
            return view('frontpreview', ['id' => $request->id, 'page' => $request->page]);
        }
    }

    // Display Gift wrap section in front side

    public function frontView(Request $request) {
        $appSetting = AppSetting::where('id', 1)->first();
        $shopName = $request->shop_name;
        $shopDetail = ShopModel::where('store_name', $shopName)->first();
        $sh = App::make('ShopifyAPI', ['API_KEY' => $appSetting->api_key, 'API_SECRET' => $appSetting->shared_secret, 'SHOP_DOMAIN' => $shopName, 'ACCESS_TOKEN' => $shopDetail->access_token]);
        $shopApi = $sh->call(['URL' => '/admin/shop.json', 'METHOD' => 'GET']);
        $currency = Symbol::where('currency_code', $shopApi->shop->currency)->first();
		$shopDetail = UserSetting::where('store_encrypt', $request->id)->first();
        $giftWrap = GiftWrapSettings::where('shop_id', $shopDetail->id)->first();
		$giftWrap->shop_currency = $currency->symbol_html;
		$giftWrap->save();
        return json_encode($giftWrap);
    }

    // Display Gift wrap image

    public function giftWrapImage(Request $request) {
        $appSettings = AppSetting::where('id', 1)->first();
        $shopName = $request->shop_name;
        $productId = $request->product_id;
        $shopFind = ShopModel::where('store_name', $shopName)->first();
        $sh = App::make('ShopifyAPI', ['API_KEY' => $appSettings->api_key, 'API_SECRET' => $appSettings->shared_secret, 'SHOP_DOMAIN' => $shopName, 'ACCESS_TOKEN' => $shopFind->access_token]);
        $product = $sh->call(['URL' => "/admin/products/$productId.json", 'METHOD' => "GET"]);
        $image = $product->product->images[0]->src;
        echo $image;
    }

    public function checkgiftwrap(Request $request) {
        
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        $id = $request['id'];

        $shopData = DB::table('usersettings')->select('id')->where('store_encrypt', $id)->first();
        $shop = (array)$shopData;
        $shop_id = $shop['id'];

        $shop_model = new ShopModel;
        $shop_find = ShopModel::where('id' , $shop_id)->first();
        $shop = $shop_find->store_name;
        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $shop_find->access_token]);
        
        $amount = $request['data'];
        
        //api for update product price
        $product_argument = [
            'product' => [
                 'id' => $shop_find->product_id,
                'variants' => [
                    '0' => [
                        'price' => $amount
                        ]
                    ]
                ]
        ];
        //api call for product update
        $product = $sh->call(['URL' => '/admin/products/'.$shop_find->product_id.'.json', 'METHOD' => 'PUT', 'DATA' => $product_argument]);
        
        //api call for product
        $product_variant = $sh->call(['URL' => '/admin/products/'.$shop_find->product_id.'.json', 'METHOD' => 'GET']);
        $varient = $product_variant->product->variants['0']->id;
        return $varient;
        
    }
}
