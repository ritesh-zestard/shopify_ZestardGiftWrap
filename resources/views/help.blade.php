@extends('layouts.app')
@section('pageCss')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" type="text/css" media="screen" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css" type="text/css" media="screen" />
@endsection
@section('content')
<?php 
if(session('shop')){
  $shop = session('shop');
} else {
  $shop = $_REQUEST['shop'];
}
?>
<div class='loader'></div>
<main class="full-width">
    <header>
        <div class="container">
            <div class="adjust-margin toc-block">
                <h1 class="toc-title">Help</h1>
                <p class="toc-description">Instructions provided below will help customers to understand the application features and how to configure the app.</p>
            </div>
        </div>
    </header>
    <div class="container">
        <div class="adjust-margin toc-block">
            <h3 class="toc-title">Need Help?</h3>
            <p class="toc-description">To customize any thing within the app or for other work just contact us on below details.</p>
        </div>
    </div>
    <section>
        <article>
            <div class="card">
                <div class="row">
                    <ul class="support-info">
                        <li><span>Developer: </span><a target="_blank" href="https://www.zestard.com">Zestard Technologies Pvt Ltd</a></li>
                        <li><span>Email: </span><a target="_blank" href="mailto:support@zestard.com">support@zestard.com</a></li>
                        <li><span>Website: </span><a target="_blank" href="https://www.zestard.com">https://www.zestard.com</a></li>
                    </ul>
                </div>
            </div>
        </article> 
    </section>
    <div class="container">
        <div class="adjust-margin toc-block">
            <h3 class="toc-title">Please refer below for FAQ section</h3>
            <p class="toc-description"></p>
        </div>
    </div>
    <section>
        <article>
            <div class="card has-sections accordion" id="accordion">
				<h3 class="accordion-head">How do I get started with application?</h3>
                <div class="accordion-desc">
                    <ul>
						<li>Brief description to get started with the application, Install the app into your Shopify store(https://apps.shopify.com/zestard-gift-wrap.</li>
						<li>From there, you will be directed to set up your gift wrap settings, you can put Gift Message Note, Gift Wrap Title, Gift Wrap Price, Gift Wrap Description and Gift Wrap Image option.</li>
						<li>Now by just pasting short-code in cart page, you are ready to go.</li>
                   </ul>
                </div>	
                <h3 class="accordion-head">How to configure General Setting?</h3>
                <div class="accordion-desc">
                    <ul>
                        <li>
                            You can easily Enable / Disable the app in "Enable App?" field.
                        </li>
                        <li>
                            You can change "Gift Wrap Title" and "Gift Wrap Description", If you don't add any title or description it will take default value.
                        </li>
                        <li>
                            You can select page where to display Gift wrap and also select if you want to show message note or not.
                        </li>
                        <li>
                            Just have to add "Gift Wrap Price" and save.
                        </li>						
                    </ul>
                </div>
                <h3 class="accordion-head">What will happen if we select Cart / Product page?</h3>
                <div class="accordion-desc">
                    <ul>
                        <li>
                            If you select "Cart Page" then the Gift Wrap will display in cart page.
                        </li>
                        <li>
                            If you select "Product Page" then Gift Wrap will display in every product page and after checking on gift wrap checkbox, a gift wrap product will get add with specified product name so the merchant can get which product to wrap.
                        </li>
                    </ul>
                </div>
                <h3 class="accordion-head">Has option to set gift message note?
</h3>
                <div class="accordion-desc">
                    <ul>
                       <li>
                            Yes, there is a option to put gift message note in the app. If the "Gift Message Note" sets to yes then after selecting giftwrap message note will side down so that customer can write gift message note.
                        </li>
                    </ul>
                </div>
                <h3 class="accordion-head">Where to paste the shortcode?</h3>
                <div class="accordion-desc">
                    <ul>
                        <li>
                            <b>Step 1</b>
                            <ul>
                                <li>Check which page you selected above in "Setting Section".</li>
                            </ul>
                        </li>
                        <li>
                            <b>Step 2 </b>If Selected page is Cart Page then,
                            <ul>
                                <li>Copy the Shortcode from below and paste it in <a href="https://<?php echo $shop ?>/admin/themes/current?key=templates/cart.liquid" target="_blank"><b>cart.liquid</b></a>.<a class="screenshot" href="{{ asset('image/shortcode-paste1.png') }}"><b> See Example</b></a>
                                </li>
                                <li>If your theme is section theme then paste it in <a href="https://<?php echo $shop ?>/admin/themes/current?key=sections/cart-template.liquid" target="_blank"><b>cart-template.liquid</b></a>.<a class="screenshot" href="{{ asset('image/shortcode.png') }}"><b> See Example</b></a>
                                </li>
                                <li><b>Note: </b>Please paste the shortcode only once.
                                </li>
                            </ul>
                        </li>
                        <li>
                            <b>Step 2 </b>If Selected page is Product Page then
                            <ul>
                                <li>Copy the Shortcode from below and paste it in <a href="https://<?php echo $shop ?>/admin/themes/current?key=templates/product.liquid" target="_blank"><b>product.liquid</b></a>.<a class="screenshot" href="{{ asset('image/product.png') }}"><b> See Example</b></a>
                                </li>
                                <li>If your theme is section theme then paste it in <a href="https://<?php echo $shop ?>/admin/themes/current?key=sections/product-template.liquid" target="_blank"><b>product-template.liquid</b></a>.<a class="screenshot" href="{{ asset('image/product-template1.png') }}"><b> See Example</b></a>
                                </li>
                                <li><b>Note: </b>Please paste the shortcode only once.
                                </li>
                            </ul>
                            <div class="copystyle_wrapper col-md-5">
                                <div class="success-copied"></div>
                                <div class="col-md-12 view-shortcode">
                                    <h2>Shortcode</h2>
                                    <textarea id="ticker-shortcode" rows="1" class="short-code"  readonly="">{% include 'giftwrap' %}</textarea>
                                    <button type="button" onclick="copyToClipboard('#ticker-shortcode')" class="btn tooltipped tooltipped-s copyMe" style="display: block;"><i class="fa fa-check"></i>Copy</button>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>
                <h3 class="accordion-head">
							Can I customize the amount of zestard gift wrap, shown during checkout?
						</h3>
                <div class="accordion-desc">
                    <ul>
						<li>Yes, the gift wrap amount is 100% customized, added from the backend, and you can change it anytime.</li>
                   </ul>
                </div>
				<h3 class="accordion-head">Can I customize the description of zestard gift wrap checkbox?</h3>
                <div class="accordion-desc">
                    <ul>
						<li>Yes, the gift wrap checkbox description is also 100% customized, added from the backend, and you can change it anytime.</li>
                   </ul>
                </div>	
				<h3 class="accordion-head">Is it possible to select the gift wrap option for the particular product in the cart page? </h3>
                <div class="accordion-desc">
                    <ul>
						<li>No it is not possible to select the gift wrap option on the cart page for the specifc product.</li>
						<li>Our application provides gift wrap option on the product detail page which you can add along with adding the product in the cart.</li>
                   </ul>
                </div>	
				</div>
        </article> <hr />			
    </section>
</main>
@endsection
@section('pageScript')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.js"></script>
<script type="text/javascript" src="{{ asset('js/custom.js') }}"></script>
<script type="text/javascript">
ShopifyApp.ready(function() {
        ShopifyApp.Bar.initialize({
            icon: "",
            title: '',
            buttons: {
                secondary: [{
                    label: 'DASHBOARD',
                    href: '{{ url('/dashboard') }}?shop=<?php echo $shop; ?>',
                    loading: true
                }
            ]
        }
        });
});

$(function() {
    $('.headerDiv').hide();
    $(".accordion").accordion({
        heightStyle: "content",
        collapsible: true,
        active: false,
    });
});
</script>
@endsection
