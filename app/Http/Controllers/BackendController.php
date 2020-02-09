<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\ShopModel;
use App\GiftWrapSettings;
use App\AppSetting;
use App\UserSetting;
use App\Symbol;
use App;
use DB;
use File;
use Storage;
use Session;

class BackendController extends Controller {

    public function store(Request $request) {
        $giftWrap = new GiftWrapSettings;

        if(session('shop')){
            $shop = session('shop');
          } else {
            $shop = $_REQUEST['shop'];
          }
       
        $shopDetail = ShopModel::where('store_name', $shop)->first();
        // get gift wrap details
        $giftWrapDetail = GiftWrapSettings::where('shop_id', $shopDetail->id)->first();
        $giftWrapDetail->status = $request->status;
        $giftWrapDetail->select_page = $request->select_page;
        $giftWrapDetail->gift_message = $request->gift_message;
        $giftWrapDetail->gift_title = $request->gift_title;
        $giftWrapDetail->gift_description = $request->gift_description;
        $giftWrapDetail->gift_amount = $request->gift_amount;
        $giftWrapDetail->save();

        if ($request->file('upload_gift_image')) {
            $image = $request->file('upload_gift_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $extension = $image->getClientOriginalExtension();
            if($extension == 'png' || $extension == 'PNG'|| $extension == 'jpg' || $extension == 'jpeg' || $extension == 'JPG' || $extension == 'JPEG'){          
                //Storage::disk('public')->put('image/product/' . $filename, file_get_contents($image)); // store image in storage directory
                $path = public_path('image/product/');
                move_uploaded_file($_FILES['upload_gift_image']['tmp_name'], $path.$filename);
                $img = config('app.url') . 'public/image/product/' . $filename;                
            } else {
                Session::flash('error', 'Not valid type of image');
                return redirect()->route('dashboard');
            }
            // End of upload image
            $appSettingDetail = AppSetting::where('id', 1)->first();
            // get store details
            $storeDetail = UserSetting::where('store_name', $shop)->first();
            $sh = App::make('ShopifyAPI', ['API_KEY' => $appSettingDetail->api_key, 'API_SECRET' => $appSettingDetail->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $storeDetail->access_token]);
            $product_argument = [
                'product' => [
                    'id' => $request->gift_wrap_product_id,
                    'title' => $request->gift_title,
                    'body_html' => $request->gift_description,
                    'images' => array(
                        '0' => array(
                            'src' => $img
                        )
                    ),
                    'variants' => [
                        '0' => [
                            'price' => $request->gift_amount,
                            'inventory_policy' => 'continue'
                        ]
                    ],
                    'inventory_policy' => 'continue'
                ]
            ];
        } else {
            $appSettingDetail = AppSetting::where('id', 1)->first();
            $storeDetail = UserSetting::where('store_name', $shop)->first();
            $sh = App::make('ShopifyAPI', ['API_KEY' => $appSettingDetail->api_key, 'API_SECRET' => $appSettingDetail->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $storeDetail->access_token]);
            $product_argument = [
                'product' => [
                    'id' => $request->gift_wrap_product_id,
                    'title' => $request->gift_title,
                    'body_html' => $request->gift_description,
                    'variants' => [
                        '0' => [
                            'price' => $request->gift_amount,
                            'inventory_policy' => 'continue'
                        ]
                    ],
                    'inventory_policy' => 'continue'
                ]
            ];
        }       
       // if ($_SERVER['REMOTE_ADDR'] = '103.254.244.134') {
       //      echo "<pre>";
       //      print_r($product_argument);exit;
       //  }
        $product = $sh->call(['URL' => '/admin/products/' . $request->gift_wrap_product_id . '.json', 'METHOD' => 'PUT', 'DATA' => $product_argument]);        
        $productVariant = $sh->call(['URL' => '/admin/products/' . $shopDetail->product_id . '.json', 'METHOD' => 'GET']);        
        $varient = $productVariant->product->variants['0']->id;
        UserSetting::where('id', $shopDetail->id)->update(['new_install' => 'N']);
        GiftWrapSettings::where('shop_id', $shopDetail->id)->update(['variant_id' => $varient]);
        Session::flash('success', 'Updated Successfully.');
        return redirect()->route('dashboard',compact('shop'));
    }

    public function webhook(Request $request) {
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        $shop_name = session('shop');
        $shop_find = ShopModel::where('store_name', $shop_name)->first();
        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop_name, 'ACCESS_TOKEN' => $shop_find->access_token]);
        $url = 'https://' . $_GET['shop'] . '/admin/webhooks.json';
        $webhookData = [
            'webhook' => [
                'topic' => 'app/uninstalled',
                'address' => config('app.url') . 'uninstall.php',
                'format' => 'json'
            ]
        ];
        $uninstall = $sh->appUninstallHook($shop_find->access_token, $url, $webhookData);
        dd($sh->call(['URL' => '/admin/script_tags/35777970235.json',
                    'METHOD' => 'DELETE']));
        dd($sh->call(['URL' => '/admin/script_tags.json',
                    'METHOD' => 'GET']));
    }

}
