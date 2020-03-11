@extends('layouts.app')
@section('pageCss')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="stylesheet" href="{{ asset('intro-js/introjs.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" type="text/css" media="screen" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css" type="text/css" media="screen" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="{{asset('css/dashboard/uptown.css')}}">
<link rel="stylesheet" href="{{asset('css/dashboard/custom.css')}}">
<style type="text/css">
    button.btn.tooltipped.tooltipped-s {
        position: absolute !important;
    }
</style>
@endsection
@section('content')
<main class="full-width easy-donation-main">
        <header>
            <div class="container">
                <div class="adjust-margin toc-block">
                    <h1 class="toc-title">Dashboard</h1>
                    <p class="toc-description">Facility to wrap a message along with gift product in the form of message notes which allows to greet people in more nicer way.</p>
                </div>
            </div>
        </header>
        <section>
            <div class="full-width">
                <article>
                    <div class="column twelve card img-padding es-image" style="background-image: url('https://zestardshop.com/shopifyapp/easy_donation/public/image/donation_banner_image.svg');" >
                        <div class="empty-section">
                            <div class="details-wrapper" data-step="1" data-intro="Please use this feature to enable the effects of this application in your shopify store" data-position='right' data-scrollTo='tooltip'>
                                <div class="text-wrapper">
                                    <h2 class="heading-text">GiftWrap Setup</h2>
                                    <div class="sub-text">
                                        <p> Let's get started by setting gift wrap</p>
                                    </div>
                                </div>
                                <div class="action-wrapper adddonation">
                                    <a class="btn btn-primary button" type="submit" name="save" href="{{route('dashboard')}}">Settings</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Documentation Start -->
                <article>
            <div class="column twelve card" >
                <div class="success-copied"></div>
                <div class="col-md-12 view-shortcode" data-step="2" data-intro="Please use this feature to enable the effects of this application in your shopify store" data-position='right' data-scrollTo='tooltip'>
                    <h2>Shortcode</h2>
                    <textarea id="ticker-shortcode" rows="1" class="short-code"  readonly="">{% include 'giftwrap' %}</textarea>
                    <button type="button" onclick="copyToClipboard('#ticker-shortcode')" class="btn tooltipped tooltipped-s copyMe"
                            style="display: block;"><i class="fa fa-check"></i>Copy to clipboard</button>
                </div>
                <div class="col-md-12 shorcode_note cartpage" data-step="3" data-intro="Please use this feature to enable the effects of this application in your shopify store" data-position='right' data-scrollTo='tooltip'>
                    <h2 class="sub-heading">Where to paste Shortcode?</h2>
                    <p><b>Step 1</b></p>
                    <ul>
                        <li>Check which page you have selected above in "Setting Section". </li>
                    </ul>

                    <p><b>Step 2 </b>If Selected page is <b>Cart Page</b> then,</p>
                    <ul>              
                        <li>Copy the Shortcode from above and paste it in <a href="https://<?php echo $shopdomain->domain ?>/admin/themes/current?key=templates/cart.liquid" target="_blank"><b>cart.liquid</b></a>.
                            <a class="screenshot" href="{{ asset('image/shortcode-paste1.png') }}">
                                <b>See Example</b>
                            </a>
                        </li>
                        <li>If your theme is section theme,then paste it in <a href="https://<?php echo $shopdomain->domain ?>/admin/themes/current?key=sections/cart-template.liquid" target="_blank"><b>cart-template.liquid</b></a>.<a class="screenshot" href="{{ asset('image/shortcode.png') }}"><b> See Example</b></a>
                        </li>
                        
                        <b>Note: </b>Please paste the shortcode only once.    
                    </ul>

                    <p><b>Step 3 </b>If Selected page is <b>Product Page</b> then,</p>
                    <ul>
                        <li>Copy the Shortcode from above and paste it in <a href="https://<?php echo $shopdomain->domain ?>/admin/themes/current?key=templates/product.liquid" target="_blank"><b>product.liquid</b></a>.<a class="screenshot" href="{{ asset('image/product.png') }}"><b> See Example</b></a></li>
                        <li>If your theme is section theme,then paste it in <a href="https://<?php echo $shopdomain->domain ?>/admin/themes/current?key=sections/product-template.liquid" target="_blank"><b>product-template.liquid</b></a>.<a class="screenshot" href="{{ asset('image/product-template1.png') }}"><b> See Example</b></a></li>
                        <b>Note: </b>Please paste the shortcode only once.
                    </ul>
                    <h2 class="heading-text">Do you want us to paste the Shortcode?</h2>  
                    <div class="cus-row">
                        <div class="c-7">                                            
                                <select class="validation form-control select_page cur_point select_template_page" name="select_template_page" id="select_template_page">
                                        <option value="">Select Template Page</option>
                                        <option value="1">Cart Page</option>
                                        <option value="2">Product Page</option>
                                    </select>
                        </div>
                        <div class="c-5">
                                <button type="button" class="btn btn-primary submit-cart-code" id="shortcode_pase_template" name="BtnPutShortcode"><i class="fa fa-circle-o-notch fa-spin btn-loader-icon-cart" style="display:none;"></i>Put Shortcode in Template</button>                                                            
                        </div>
                    </div>
                    &nbsp;&nbsp;
                    <p><strong>Note : </strong>Select the page from the option where you want to paste the code,then click 'Put Shortcode' and will get your job done.</p>

                    <div style="display: none;">
                        <p><b>Paste Shortcode in Cart Template</b></p>
                        <div class="">
                            <form action="{{ url('snippet-create-cart') }}" id="RemoveCartVariants" name="config" method="post" class="submitForm"> 
                                <input type="hidden" name="shop" value="<?php echo $shopdomain->domain; ?>" />
                                {{ csrf_field() }}
                                <button class="btn btn-primary" type="submit" name="snippet_cart">Paste Shortcode in Cart</button>
                            </form>
                        </div>            
                        <br>
                
                        <p><b>Paste Shortcode in Product Template</b></p>
                        <div class="">
                            <form action="{{ url('snippet-create-product') }}" id="RemoveProductVariants" name="config" method="post" class="submitForm">
                                <input type="hidden" name="shop" value="<?php echo $shopdomain->domain; ?>" />
                                {{ csrf_field() }}
                                <input type="hidden" name="template_id" class="template_id">
                                <button class="btn btn-primary" type="submit" name="snippet_product">Paste Shortcode in Product</button>
                            </form>
                        </div>
                </div>
                </div>         
            </div>       
        </article>
                <!-- Documentation End -->
            </div>
        </section>
        <footer></footer>
        <div class="container">
            <div id="pop-up">
                <div class="popup" data-popup="popup-1" style="display: none">
                    <div class="popup-inner">
                        <h2>Welcome to Updated version of Easy Donation !</h2>
                        <p>As we have changed our app look, we advise you to have a quick look up for all modification we did by taking this tour. To start the tour please click on TOUR BUTTON.</p>
                        <button id="take-tour" class="btn btn-primary submit-loader" data-popup-close="popup-1" type="button" name="Take Toure"><i class="fa fa-circle-o-notch fa-spin btn-loader-icon" style="display:none;"></i>Start Tour</button>
                        <a class="popup-close close-tour" data-popup-close="popup-1" href="#">x</a>
                    </div>
                </div>
            </div>

        </div>

    </main>
@endsection
@section('pageScript')  
<script type="text/javascript" src="{{ asset('intro-js/intro.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.js"></script>
<script type="text/javascript" src="{{ asset('js/custom.js') }}"></script>
<script type="text/javascript">
ShopifyApp.ready(function() {
        ShopifyApp.Bar.initialize({
            title: 'Dashboard',
            buttons: {
                primary: {
                    label: 'Demo',
                    callback: function(event) {
                        introJs().start();
                    }
                },
                secondary: [
                {
                  label: 'SETTINGS',
                  href : '{{ url('/settings') }}?shop=<?php echo $shop; ?>',
                  loading: true
                },
                {
                    label: 'HELP',
                    href: '{{ url('/help') }}?shop=<?php echo $shop; ?>',
                    loading: true
                }
            ]
        }
        });
});
</script>
@endsection