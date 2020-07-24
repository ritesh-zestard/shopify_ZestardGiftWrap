<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\ShopModel;
use App\GiftWrapSettings;
use App\AppSetting;
use App\UserSetting;
use App\TrialInfo;
use Mail;
use Session;

class callbackController extends Controller {

    public $shopArray = array("vijay-test.myshopify.com", "dipal-test.myshopify.com", "ishita-test.myshopify.com", "zankar-test.myshopify.com", 'ankit-test-com.myshopify.com', "bhushantest.myshopify.com","pallavi-test.myshopify.com","easy-donation.myshopify.com","dikendra-test.myshopify.com","gift-wrap-app.myshopify.com", "kamlesh-test-app.myshopify.com", "bhushan-test-store.myshopify.com", "ztdesign4.myshopify.com", "easy-donation-new.myshopify.com");

    public function index(Request $request) {
        $appSetting = AppSetting::where('id', 1)->first();
        $shop = $request->shop;
        if ($shop) {
            $storeDetail = UserSetting::where('store_name', $shop)->first();
            if ($storeDetail) {
                $sh = App::make('ShopifyAPI', ['API_KEY' => $appSetting->api_key, 'API_SECRET' => $appSetting->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $storeDetail->access_token]);
                if ($storeDetail->charge_id && $storeDetail->charge_id > 0 && $storeDetail->status == "active") {
                    session(['shop' => $shop]);
                    return redirect()->route('landingPage', ['shop' => $shop]);
                    // return redirect()->route('dashboard', ['shop' => $shop]);
                } else {
                    return redirect()->route('payment_process');
                }
            } else {
                $sh = App::make('ShopifyAPI', ['API_KEY' => $appSetting->api_key, 'API_SECRET' => $appSetting->shared_secret, 'SHOP_DOMAIN' => $shop]);
                $permissionUrl = $sh->installURL(['permissions' => array('read_script_tags', 'write_script_tags', 'read_themes', 'write_content', 'write_themes', 'read_products', 'write_products', 'read_orders'), 'redirect' => $appSetting->redirect_url]);
                return redirect($permissionUrl);
            }
        }
    }

    public function redirect(Request $request) {

        $appSetting = AppSetting::where('id', 1)->first();
        $shop = $request->shop;
        $code = $request->code;
        if ($shop && $code) {
            $storeDetail = UserSetting::where('store_name', $shop)->first();
            if ($storeDetail) {
                $sh = App::make('ShopifyAPI', ['API_KEY' => $appSetting->api_key, 'API_SECRET' => $appSetting->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $storeDetail->access_token]);
                $id = $storeDetail->charge_id;
                $url = 'admin/recurring_application_charges' . $id . '.json';
                $charge = $sh->call(['URL' => $url, 'METHOD' => 'GET']);
                $charge_id = $storeDetail->charge_id;
                $charge_status = $storeDetail->status;
                if (!empty($charge_id) && $charge_id > 0 && $charge_status == "active") {
                    session(['shop' => $shop]);

                    return redirect()->route('dashboard', ['shop' => $shop]);
                } else {
                    return redirect()->route('payment_process');
                }
            }
            $sh = App::make('ShopifyAPI', ['API_KEY' => $appSetting->api_key, 'API_SECRET' => $appSetting->shared_secret, 'SHOP_DOMAIN' => $shop]);
            try {
                $verify = $sh->verifyRequest($request->all());
                if ($verify) {
                    $accessToken = $sh->getAccessToken($code);
                    // Insert usert setting
                    $userSetting = new UserSetting;
                    $userSetting->access_token = $accessToken;
                    $userSetting->store_name = $shop;
                    $userSetting->store_encrypt = "";
                    $userSetting->save();
                    $storeDetail = ShopModel::where('store_name', $shop)->first();
                    $shopId = $storeDetail->id;
                    $storeEncrypt = crypt($shopId, "ze");
                    $storeFinalEncrypt = str_replace(['/', '.'], "Z", $storeEncrypt);
                    ShopModel::where('id', $shopId)->update(['store_encrypt' => $storeFinalEncrypt]);

                    $sh = App::make('ShopifyAPI', ['API_KEY' => $appSetting->api_key, 'API_SECRET' => $appSetting->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $storeDetail->access_token]);

                    //for creating the uninstall webhook
                    $url = 'https://' . $shop . '/admin/webhooks.json';
                    $webhookData = [
                        'webhook' => [
                            'topic' => 'app/uninstalled',
                            'address' => config('app.url') . 'uninstall.php',
                            'format' => 'json'
                        ]
                    ];
                    $sh->appUninstallHook($accessToken, $url, $webhookData);
                    //api call for get theme info
                    $theme = $sh->call(['URL' => '/admin/themes.json', 'METHOD' => 'GET']);
                    foreach ($theme->themes as $themeData) {
                        if ($themeData->role == 'main') {
                            $snippets_arguments = ['id' => $storeFinalEncrypt];
                            $theme_id = $themeData->id;
                            $view = (string) View('snippets', $snippets_arguments);
                            //api call for creating snippets
                            $call = $sh->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json', 'METHOD' => 'PUT', 'DATA' => ['asset' => ['key' => 'snippets/giftwrap.liquid', 'value' => $view]]]);
                        }
                    }
                    if ($shop == "tospitikomas-com.myshopify.com" || "gourmetestore.com") {
                        $script = "";
                    } else {
                        $script = $sh->call(['URL' => '/admin/script_tags.json', 'METHOD' => 'POST', 'DATA' => ['script_tag' => ['event' => 'onload', 'src' => config('app.url') . 'public/js/giftwrap.js']]]);
                    }
                    session(['shop' => $shop]);
                    //creating the Recuring charge for app
                    $url = 'https://' . $shop . '/admin/recurring_application_charges.json';
                    //Check if trial is still running
                    $checkTrial = TrialInfo::where('store_name', $shop)->first();
                    if ($checkTrial) {
                        $total_trial_days = $checkTrial->trial_days;
                        $trial_activated_date = $checkTrial->activated_on;
                        $trial_over_date = $checkTrial->trial_ends_on;
                        $current_date = date("Y-m-d");
                        if (strtotime($current_date) < strtotime($trial_over_date)) {
                            $date1 = date_create($trial_over_date);
                            $date2 = date_create($current_date);
                            $trial_remain = date_diff($date2, $date1);
                            $new_trial_days = $trial_remain->format("%a");
                        } else {
                            $new_trial_days = 0;
                        }
                        if (in_array($shop, $this->shopArray)) {
                            $charge = $sh->call([
                                'URL' => $url,
                                'METHOD' => 'POST',
                                'DATA' => array(
                                    'recurring_application_charge' => array(
                                        'name' => 'Zestard Gift Wrap',
                                        'price' => 0.01,
                                        'return_url' => url('payment_success'),
                                        'capped_amount' => 20,
                                        'terms' => 'Terms & Condition Applied',
                                        'trial_days' => $new_trial_days,
                                        'test' => true
                                    )
                                )
                                    ], false);
                        } else {
                            $charge = $sh->call([
                                'URL' => $url,
                                'METHOD' => 'POST',
                                'DATA' => array(
                                    'recurring_application_charge' => array(
                                        'name' => 'Zestard Gift Wrap',
                                        'price' => 3.99,
                                        'return_url' => url('payment_success'),
                                        'capped_amount' => 20,
                                        'terms' => 'Terms & Condition Applied',
                                        'trial_days' => $new_trial_days,
                                    //'test' =>true
                                    )
                                )
                                    ], false);
                        }
                    } else {//for the first time create trial
                        if (in_array($shop, $this->shopArray)) {
                            $charge = $sh->call([
                                'URL' => $url,
                                'METHOD' => 'POST',
                                'DATA' => array(
                                    'recurring_application_charge' => array(
                                        'name' => 'Zestard Gift Wrap',
                                        'price' => 0.01,
                                        'return_url' => url('payment_success'),
                                        'capped_amount' => 20,
                                        'terms' => 'Terms & Condition Applied',
                                        'trial_days' => 7,
                                        'test' => true
                                    )
                                )
                                    ], false);
                        } else {
                            $charge = $sh->call([
                                'URL' => $url,
                                'METHOD' => 'POST',
                                'DATA' => array(
                                    'recurring_application_charge' => array(
                                        'name' => 'Zestard Gift Wrap',
                                        'price' => 3.99,
                                        'return_url' => url('payment_success'),
                                        'capped_amount' => 20,
                                        'terms' => 'Terms & Condition Applied',
                                        'trial_days' => 7,
                                    //'test' =>true
                                    )
                                )
                                    ], false);
                        }
                    }
                    $updateData = array(
                        'charge_id' => (string) $charge->recurring_application_charge->id,
                        'api_client_id' => $charge->recurring_application_charge->api_client_id,
                        'price' => $charge->recurring_application_charge->price,
                        'status' => $charge->recurring_application_charge->status,
                        'billing_on' => $charge->recurring_application_charge->billing_on,
                        'payment_created_at' => $charge->recurring_application_charge->created_at,
                        'activated_on' => $charge->recurring_application_charge->activated_on,
                        'trial_ends_on' => $charge->recurring_application_charge->trial_ends_on,
                        'cancelled_on' => $charge->recurring_application_charge->cancelled_on,
                        'trial_days' => $charge->recurring_application_charge->trial_days,
                        'decorated_return_url' => $charge->recurring_application_charge->decorated_return_url,
                        'confirmation_url' => $charge->recurring_application_charge->confirmation_url,
                        'domain' => $shop
                    );
                    $create_charge = UserSetting::where('store_name', $shop)->update($updateData);
                    $shopi_info = $sh->call(['URL' => '/admin/shop.json', 'METHOD' => 'GET']);

                    //for the installation follow up mail for cliant
                    $subject = "Zestard Installation Greetings :: Zestard Gift Wrap";
                    $sender = "support@zestard.com";
                    $sender_name = "Zestard Technologies";
                    $app_name = "Zestard Gift Wrap";
                    $logo = config('app.url') . 'public/image/zestard-logo.png';
                    $installation_follow_up_msg = '<html>

                        <head>
                            <meta name="viewport" content="width=device-width, initial-scale=1">
                            <style>
                                @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i");
                                @media only screen and (max-width:599px) {
                                    table {
                                        width: 100% !important;
                                    }
                                }
                                
                                @media only screen and (max-width:412px) {
                                    h2 {
                                        font-size: 20px;
                                    }
                                    p {
                                        font-size: 13px;
                                    }
                                    .easy-donation-icon img {
                                        width: 120px;
                                    }
                                }
                            </style>
                        
                        </head>
                        
                        <body style="background: #f4f4f4; padding-top: 57px; padding-bottom: 57px;">
                            <table class="main" border="0" cellspacing="0" cellpadding="0" width="600px" align="center" style="border: 1px solid #e6e6e6; background:#fff; ">
                                <tbody>
                                    <tr>
                                        <td style="padding: 30px 30px 10px 30px;" class="review-content">
                                            <p class="text-align:left;"><img src="' . $logo . '" alt=""></p>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px; line-height: 25px; margin-top: 0px;"><b>Hi ' . $shopi_info->shop->shop_owner . '</b>,</p>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">Thanks for Installing Zestard Application ' . $app_name . '</p>
                                            <p style="font-family: \'Helvetica\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">We appreciate your kin interest for choosing our application and hope that you have a wonderful experience.</p>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">Please don\'t feel hesitate to reach us in case of any queries or questions at <a href="mailto:support@zestard.com" style="text-decoration: none;color: #1f98ea;font-weight: 600;">support@zestard.com</a>.</p>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">We also do have live chat support services for quick response and resolution of queries.</p>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">(Please Note: Support services are available according to the IST Time Zone(i.e GMT 5:30+) as we reside in India. Timings are from 10:00am to 7:00pm)</p>
                        
                                        </td>
                                    </tr>
                        
                                    <tr>
                                        <td style="padding: 20px 30px 30px 30px;">
                        
                                            <br>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 26px; margin-bottom:0px;">Thanks,<br>Zestard Support</p>
                                        </td>
                                    </tr>
                        
                                </tbody>
                            </table>
                        </body>';

                    $receiver = $shopi_info->shop->email;


                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                    $msg = '<table>
                                <tr>
                                    <th>Shop Name</th>
                                    <td>' . $shopi_info->shop->name . '</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>' . $shopi_info->shop->email . '</td>
                                </tr>
                                <tr>
                                    <th>Domain</th>
                                    <td>' . $shopi_info->shop->domain . '</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>' . $shopi_info->shop->phone . '</td>
                                </tr>
                                <tr>
                                    <th>Shop Owner</th>
                                    <td>' . $shopi_info->shop->shop_owner . '</td>
                                </tr>
                                <tr>
                                    <th>Country</th>
                                    <td>' . $shopi_info->shop->country_name . '</td>
                                </tr>
                                <tr>
                                    <th>Plan</th>
                                    <td>' . $shopi_info->shop->plan_name . '</td>
                                </tr>
                            </table>';

                    $store_details = DB::table('development_stores')->where('dev_store_name', $shop)->first();

                    if (count($store_details) <= 0) {
                        mail("support@zestard.com", "Gift Wrap App Installed", $msg, $headers);
                    }

                    //redirecting to the Shopify payment page
                    echo '<script>window.top.location.href="' . $charge->recurring_application_charge->confirmation_url . '"</script>';
                } else {
                    // Issue with data
                }
            } catch (Exception $e) {
                echo '<pre>Error: ' . $e->getMessage() . '</pre>';
            }
        }
    }

    public function dashboard(Request $request) {

        if (session('shop')) {
            $shop = session('shop');
        } else {
            $shop = $_REQUEST['shop'];
        }
        $appSetting = AppSetting::where('id', 1)->first();
        $shopDetail = ShopModel::where('store_name', $shop)->first();
        $new_install = $shopDetail->new_install;
        $giftWrapSetting = GiftWrapSettings::where('shop_id', $shopDetail->id)->first();

        if ($giftWrapSetting) {
            $storeDetail = UserSetting::where('store_name', $shop)->first();

            $sh = App::make('ShopifyAPI', ['API_KEY' => $appSetting->api_key, 'API_SECRET' => $appSetting->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $storeDetail->access_token]);
        
            if ($giftWrapSetting->giftwrap_id != NULL) {
                
                $product = $sh->call(['URL' => '/admin/products/' . $giftWrapSetting->giftwrap_id . '.json?fields=id,images,title', 'METHOD' => 'GET']);

                $image_data = array();
                foreach ($product as $data) {
                    foreach ($data->images as $image_data) {
                        //print_r($image_data->src);die;
                    }
                }
                $imagedata = $image_data;
            } else {
                $imagedata = array();
            }

            return view('dashboard', array('shopdomain' => $shopDetail, 'data' => $giftWrapSetting, 'imagedata' => $imagedata, 'new_install' => $new_install));
        } else {
            return view('dashboard', array('shopdomain' => $shopDetail, 'data' => '', 'imagedata' => '', 'new_install' => $new_install));
        }
    }
    public function landingPage(Request $request){
        if (session('shop')) {
            $shop = session('shop');
        } else {
            $shop = $_REQUEST['shop'];
        }
        $shopDetail = ShopModel::where('store_name', $shop)->first();
        return view('landing_page', array('shopdomain' => $shopDetail,'shop' => $shop));
    }
    public function payment_method(Request $request) {
        $shop = session('shop');
        $appSetting = AppSetting::where('id', 1)->first();
        $storeDetail = UserSetting::where('store_name', $shop)->first();
        if ($storeDetail) {
            $sh = App::make('ShopifyAPI', ['API_KEY' => $appSetting->api_key, 'API_SECRET' => $appSetting->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $storeDetail->access_token]);

            $charge_id = $storeDetail->charge_id;


            $url = 'admin/recurring_application_charges/' . $charge_id . '.json';
            $charge = $sh->call(['URL' => $url, 'METHOD' => 'GET']);



            if (count($charge) > 0) {
                if ($charge->recurring_application_charge->status == "pending") {
                    echo '<script>window.top.location.href="' . $charge->recurring_application_charge->confirmation_url . '"</script>';
                } elseif ($charge->recurring_application_charge->status == "declined" || $charge->recurring_application_charge->status == "expired") {
                    //creating the new Recuring charge after declined app
                    $url = 'https://' . $shop . '/admin/recurring_application_charges.json';
                    $charge = $sh->call([
                        'URL' => $url,
                        'METHOD' => 'POST',
                        'DATA' => array(
                            'recurring_application_charge' => array(
                                'name' => 'Zestard Gift Wrap',
                                'price' => 3.99,
                                'return_url' => url('payment_success'),
                                'capped_amount' => 20,
                                'terms' => 'Terms & Condition Applied',
                            // 'test' => true
                            )
                        )
                            ], false);

                    $updateData = array(
                        'charge_id' => (string) $charge->recurring_application_charge->id,
                        'api_client_id' => $charge->recurring_application_charge->api_client_id,
                        'price' => $charge->recurring_application_charge->price,
                        'status' => $charge->recurring_application_charge->status,
                        'billing_on' => $charge->recurring_application_charge->billing_on,
                        'payment_created_at' => $charge->recurring_application_charge->created_at,
                        'activated_on' => $charge->recurring_application_charge->activated_on,
                        'trial_ends_on' => $charge->recurring_application_charge->trial_ends_on,
                        'cancelled_on' => $charge->recurring_application_charge->cancelled_on,
                        'trial_days' => $charge->recurring_application_charge->trial_days,
                        'decorated_return_url' => $charge->recurring_application_charge->decorated_return_url,
                        'confirmation_url' => $charge->recurring_application_charge->confirmation_url,
                        'domain' => $shop
                    );
                    $create_charge = UserSetting::where('store_name', $shop)->update($updateData);

                    //redirecting to the Shopify payment page
                    echo '<script>window.top.location.href="' . $charge->recurring_application_charge->confirmation_url . '"</script>';
                } elseif ($charge->recurring_application_charge->status == "accepted") {

                    $active_url = '/admin/recurring_application_charges/' . $charge_id . '/activate.json';
                    $Activate_charge = $sh->call(['URL' => $active_url, 'METHOD' => 'POST', 'HEADERS' => array('Content-Length: 0')]);
                    $Activatecharge_array = get_object_vars($Activate_charge);
                    $active_status = $Activatecharge_array['recurring_application_charge']->status;
                    UserSetting::where('store_name', $shop)->where('charge_id', $charge_id)->update(['status' => $active_status]);
                    return redirect()->route('dashboard', ['shop' => $shop]);
                }
            }
        }
    }

    public function payment_compelete(Request $request) {
        $appSetting = AppSetting::where('id', 1)->first();

        $shop = session('shop');
        $storeDetail = UserSetting::where('store_name', $shop)->first();

        $sh = App::make('ShopifyAPI', ['API_KEY' => $appSetting->api_key, 'API_SECRET' => $appSetting->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $storeDetail->access_token]);
        $charge_id = $_GET['charge_id'];
        $url = 'admin/recurring_application_charges/#{' . $charge_id . '}.json';
        $charge = $sh->call(['URL' => $url, 'METHOD' => 'GET',]);
        $status = $charge->recurring_application_charges[0]->status;
        $update_charge_status = UserSetting::where('store_name', $shop)->where('charge_id', $charge_id)->update(['status' => $status]);
        if ($status == "accepted") {
            $active_url = '/admin/recurring_application_charges/' . $charge_id . '/activate.json';
            $Activate_charge = $sh->call(['URL' => $active_url, 'METHOD' => 'POST', 'HEADERS' => array('Content-Length: 0')]);
            $Activatecharge_array = get_object_vars($Activate_charge);
            $active_status = $Activatecharge_array['recurring_application_charge']->status;
            $trial_start = $Activatecharge_array['recurring_application_charge']->activated_on;
            $trial_end = $Activatecharge_array['recurring_application_charge']->trial_ends_on;
            $trial_days = $Activatecharge_array['recurring_application_charge']->trial_days;


            UserSetting::where('store_name', $shop)->where('charge_id', $charge_id)->update(['status' => $active_status, 'activated_on' => $trial_start, 'trial_ends_on' => $trial_end]);

            //check if any trial info is exists or not
            if ($trial_days > 0) {
                $checkTrial = TrialInfo::where('store_name', $shop)->first();
                if ($checkTrial) {
                    TrialInfo::where('store_name', $shop)->update(['trial_days' => $trial_days, 'activated_on' => $trial_start, 'trial_ends_on' => $trial_end]);
                } else {
                    $trialInfo = new TrialInfo;
                    $trialInfo->store_name = $shop;
                    $trialInfo->trial_days = $trial_days;
                    $trialInfo->activated_on = $trial_start;
                    $trialInfo->trial_ends_on = $trial_end;
                    $trialInfo->save();
                }
            }
            /*
             * default add gift wrap setting
             */

            $storeDetail = ShopModel::where('store_name', $shop)->first();
            $giftWrapSetting = new GiftWrapSettings;
            $giftWrapSetting->status = 0;
            $giftWrapSetting->select_page = 0;
            $giftWrapSetting->gift_message = 0;
            $giftWrapSetting->gift_title = 'Gift Wrap';
            $giftWrapSetting->gift_description = 'Add a Gift Wrap to your Order';
            $giftWrapSetting->gift_amount = 0;
            $giftWrapSetting->shop_id = $storeDetail->id;
            $giftWrapSetting->save();
            $product_argument = [
                'product' => [
                    'title' => "Gift Wrap",
                    'body_html' => "Add a Gift Wrap to your Order",
                    'vendor' => 'zestard-gift-wrap',
                    'product_type' => 'Gift Wrap',
                    'images' => array(
                        '0' => array(
                            'src' => config('app.url') . 'public/image/product/gift_wrap.jpg'
                        )
                    ),
                    'variants' => [
                        'option1' => 'Gift Wrap',
                        'price' => NULL,
                        'inventory_policy' => 'continue'
                    ],
                    'inventory_policy' => 'continue'
                ]
            ];
            // get product
            $product = $sh->call(['URL' => '/admin/products.json', 'METHOD' => 'POST', 'DATA' => $product_argument]);
            $product_id = $product->product->id;
            $variant_id = $product->product->variants[0]->id;
            // Update user setting
            UserSetting::where('id', $storeDetail->id)->update(['product_id' => $product_id, 'new_install' => 'N']);
            // update gift wrap setting
            GiftWrapSettings::where('shop_id', $storeDetail->id)->update(['giftwrap_id' => $product_id, 'variant_id' => $variant_id]);
            return redirect()->route('dashboard', ['shop' => $shop]);
        } elseif ($status == "declined") {
            return redirect()->route('decline');
        }
    }

    public function declined(Request $request) {
        $shop = session('shop');
        echo '<script>window.top.location.href="https://' . $shop . '/admin/apps"</script>';
    }

    public function update_modal_status(Request $request) {
        $shop = $request->shop_name;
        UserSetting::where('store_name', $shop)->update(['new_install' => 'N']);
    }

    /* Put shortcode directly in product snippet file */

    public function SnippetCreateProduct(Request $request) {

        $shop = session('shop');
        $shop_find = ShopModel::where('store_name', $shop)->first();
        $app_version = $shop_find->app_version;
        $app_settings = AppSetting::where('id', 1)->first();
        $is_exist = '';
        $str_to_insert = '';
        $is_exist = "{% include 'giftwrap' %}";
        $str_to_insert = " {% include 'giftwrap' %} ";

        $sh = app('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $shop_find->access_token]);
        //api call for get theme info
        $theme = $sh->call(['URL' => '/admin/themes.json?role=main', 'METHOD' => 'GET']);
        foreach ($theme->themes as $themeData) {
            if ($themeData->role == 'main') {
                $theme_id = $themeData->id;
                $product_template = $sh->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json?asset[key]=sections/product-template.liquid&theme_id=' . $theme_id, 'METHOD' => 'GET']);
                $old_str = $product_template->asset->value;
                if (strpos($old_str, $is_exist) === false) {
                    $find = "{{ product.title }}";
                    if (strpos($old_str, $find) !== false) {
                        //if find string available in liquide file                    
                        $pos = strpos($old_str, $find) + 30;
                        $newstr = substr_replace($old_str, $str_to_insert, $pos, 0);
                        $call = $sh->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json', 'METHOD' => 'PUT', 'DATA' => ['asset' => ['key' => 'sections/product-template.liquid', 'value' => $newstr]]]);
                    } else {
                        //if find string NOT available in liquide file                    
                        $find1 = "{% if section.settings.product_quantity_enable %}";
                        $pos1 = strpos($old_str, $find1);
                        $newstr = substr_replace($old_str, $str_to_insert, $pos1, 0);
                        $call = $sh->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json', 'METHOD' => 'PUT', 'DATA' => ['asset' => ['key' => 'sections/product-template.liquid', 'value' => $newstr]]]);
                    }
                } else {
                    Session::flash('error', 'Your Shortcode has been already pasted in the product template page. If the Gift wrap section still does not appear, contact us at support@zestard.com for more support.');
                    return redirect()->route('dashboard', ['shop' => $shop]);
                }
            } else {
                Session::flash('error', 'Someting went wrong, Please try manual process.');
                return redirect()->route('dashboard', ['shop' => $shop]);
            }
        }
        Session::flash('success', 'Your shortcode has been added successfully in product template page');
        return redirect()->route('dashboard', ['shop' => $shop]);
    }

    /* Put shortcode directly in cart snippet file */

    public function SnippetCreateCart(Request $request) {
        if (session('shop')) {
            $shop = session('shop');
        } else {
            $shop = $_REQUEST['shop'];
        }
        $shop_find = ShopModel::where('store_name', $shop)->first();
        $app_settings = AppSetting::where('id', 1)->first();
        $sh = app('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $shop_find->access_token]);
        //api call for get theme info
        $theme = $sh->call(['URL' => '/admin/themes.json', 'METHOD' => 'GET']);
        foreach ($theme->themes as $themeData) {
            if ($themeData->role == 'main') {
                $theme_id = $themeData->id;
                $product_template = $sh->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json?asset[key]=templates/cart.liquid&theme_id=' . $theme_id, 'METHOD' => 'GET']);
                $string_value = $product_template->asset->value;
                $contain_section = "{% section 'cart-template' %}";
                if (strpos($string_value, $contain_section) !== false) {
                    //if cart-template.liquid available
                    $product_template = $sh->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json?asset[key]=sections/cart-template.liquid&theme_id=' . $theme_id, 'METHOD' => 'GET']);
                    $old_str = $product_template->asset->value;
                    $is_exist = "{% include 'giftwrap' %}";
                    if (strpos($old_str, $is_exist) === false) {
                        $str_to_insert = " {% include 'giftwrap' %} ";
                        $find = '<div class="cart__footer">';
                        if (strpos($old_str, $find) !== false) {
                            //if find string available in liquide file                            
                            $pos = strpos($old_str, $find);
                            $newstr = substr_replace($old_str, $str_to_insert, $pos, 0);
                            $call = $sh->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json', 'METHOD' => 'PUT', 'DATA' => ['asset' => ['key' => 'sections/cart-template.liquid', 'value' => $newstr]]]);
                        } else {
                            //if find string NOT available in liquide file
                            $find1 = "{{ cart.note }}";
                            $pos1 = strpos($old_str, $find1) + 30;
                            $newstr = substr_replace($old_str, $str_to_insert, $pos1, 0);
                            $call = $sh->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json', 'METHOD' => 'PUT', 'DATA' => ['asset' => ['key' => 'sections/cart-template.liquid', 'value' => $newstr]]]);
                        }
                    } else {
                        Session::flash('error', 'Your Shortcode has been already pasted in the cart template page. If the Gift wrap section still does not appear, contact us at support@zestard.com for more support.');
                        return redirect()->route('dashboard', ['shop' => $shop]);
                    }
                } else {
                    //if cart-template.liquid not available
                    $product_template = $sh->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json?asset[key]=templates/cart.liquid&theme_id=' . $theme_id, 'METHOD' => 'GET']);
                    $old_str = $product_template->asset->value;
                    $is_exist = "{% include 'giftwrap' %}";
                    if (strpos($old_str, $is_exist) === false) {
                        $str_to_insert = " {% include 'giftwrap' %} ";
                        $find = "{% if settings.cart_notes_enable %}";
                        $pos = strpos($old_str, $find);
                        $newstr = substr_replace($old_str, $str_to_insert, $pos, 0);
                        //api call for creating snippets                 
                        $call = $sh->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json', 'METHOD' => 'PUT', 'DATA' => ['asset' => ['key' => 'templates/cart.liquid', 'value' => $newstr]]]);
                    } else {
                        Session::flash('error', 'Your Shortcode has been already pasted in the cart template page. If the Gift wrap section still does not appear, contact us at support@zestard.com for more support.');
                        return redirect()->route('dashboard', ['shop' => $shop]);
                    }
                }
            }
        }
        Session::flash('success', 'Your shortcode has been added successfully in cart template page');
        return redirect()->route('dashboard', ['shop' => $shop]);
    }

}
