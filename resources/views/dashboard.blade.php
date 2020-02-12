@extends('layouts.app')
@section('pageCss')
<link rel="stylesheet" href="{{ asset('intro-js/introjs.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" type="text/css" media="screen" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css" type="text/css" media="screen" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
<section>
    <div class="full-width"> 
        <form action="{{ route('giftwrap_save') }}" method="post" onsubmit="giftWrapForm()" name="giftwrap" id="giftwrap" enctype="multipart/form-data"> 
            <input type="hidden" name="shop" value="<?php echo $shopdomain->domain; ?>" />
                    {{ csrf_field() }} 
        <article>
            <div class="column eight card">
                    <div id="add_gift_image">
                        <input id="gift_wrap_product_id" name="gift_wrap_product_id" type="hidden" value="{{ $data->giftwrap_id }}">         
                        <div class="row mt-20">
                            <div class="columns six" data-step="1" data-intro="Please use this feature to enable the effects of this application in your shopify store" data-position='right' data-scrollTo='tooltip'>
                                <label>Enable App?</label>
                                <select id="status" name="status">
                                    <option value="1" {{ ($data->status)?'selected':'' }}>Enabled</option>
                                    <option value="0" {{ (!$data->status)?'selected':'' }}>Disabled</option>
                                </select>
                            </div>
                            <div class="columns six" data-step="2" data-intro="It will allow to decide where you would like to display gift wrap option whether on the cart page or on the product page.Please select according your requirement" data-position='right' data-scrollTo='tooltip'>
                                <label>Select Page</label>
                                <select id="select_page" name="select_page">
                                    <option value="1" {{ ($data->select_page)?'selected':'' }}>Cart Page</option>
                                    <option value="0" {{ (!$data->select_page)?'selected':'' }}>Product Page</option>
                                    <?php if($shop == "tospitikomas-com.myshopify.com"){ ?>
                                        <option value="2" {{ ($data->select_page == 2)?'selected':'' }}>Both</option>
                                   <?php  } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-20">
                            <div class="columns six" data-step="3" data-intro="This feature will allow you to add the extra message note of your own choice with the gift wrap option" data-position='right' data-scrollTo='tooltip'>
                                <label>Gift Message Note</label>
                                <select id="gift_message" name="gift_message">                    
                                    <option value="0" {{ (!$data->gift_message)?'selected':'' }}>No</option>
                                    <option value="1" {{ ($data->gift_message)?'selected':'' }}>Yes</option>
                                </select>
                            </div>
                            <div class="columns six" data-step="4" data-intro="Please input the title of your own choice which you would like to show for the Gift Wrap Option" data-position='right' data-scrollTo='tooltip'>
                                <label>Gift Wrap Title</label>
                                <input id="gift_title" name="gift_title" type="text" class="validate form-control" value="{{ $data->gift_title }}" required>
                                <p class="title_error" style="color:red;margin-top:10px;"></p>
                            </div>
                           
                        </div>
                        <div class="row mt-20" id="description">
                            <div class="columns six" data-step="6" data-intro="A brief description can be entered in this box to explain about the purpose of using the gift" data-position='right' data-scrollTo='tooltip'>
                                <label>Gift Wrap Description</label>
                                <textarea id="gift_description" name="gift_description" class="form-control" required>{{ $data->gift_description }}</textarea>
                                <p class="text_error" style="color:red;margin-top:10px;"></p>
                                <p class="gift-note"><b>Note: </b>This description will show on page near "gift wrap checkbox". Put your description with maximum 80 charater.</p>
                            </div>
                             <div class="columns six" data-step="5" data-intro="Decide and input the price which you would like to charge on adding the gift wrap" data-position='right' data-scrollTo='tooltip'>
                                <label>Gift Wrap Price</label>
                                <input id="gift_amount" name="gift_amount" type="number" value="{{ ($data->gift_amount?$data->gift_amount:0) }}" class="validate form-control" min="0" required> 
                            </div>
                        </div>
                    </div>
               
            </div>
            <div class="columns four">
                <div class="alert custom-alert">
                    <dl>
                        <dt>App creates gift wrap option in form of product which you can access from the Shopify Products section.It is highly recommended not to delete this default app product from anywhere for the proper functioning of the application.</dt>
                    </dl>
                </div>
                <div class="card">
                    <div class="row action-area">
                        <div class="pl-4" id="check_imagediv">
                            <div class="columns twelve">                                
                                <div class="chooseimage">     
                                    <input type="file" id="upload_gift_image" name="upload_gift_image" style="display: none;">                                      
                                    <img src="{{ (count($imagedata) && $imagedata)? $imagedata->src : 'https://cdn.shopify.com/s/files/1/0084/2566/8655/products/gift_wrap.jpg?v=1569415452' }}" id="upload_image">
                                    <u><label id="uploadImage" onclick="document.getElementById('upload_gift_image').click();">Change Image</label></u>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
        </article>
        <hr class="hr-custom">
        <section>
        <article>
        <div class="row mb-3 column twelve save-general-setting-btn" id="GifWrapSave">
            <button class="btn btn-primary submit-loader submit-giftwrap" type="button"><i class="fa fa-circle-o-notch fa-spin btn-loader-icon btn-loader-icon-submit" id="buttonLoader" style="display:none;"></i> Save Setting</button>
        </div>
        </article>
            </section>
         </form>
        <br>
        <article>
            <div class="column twelve card">
                <div class="success-copied"></div>
                <div class="col-md-12 view-shortcode">
                    <h2>Shortcode</h2>
                    <textarea id="ticker-shortcode" rows="1" class="short-code"  readonly="">{% include 'giftwrap' %}</textarea>
                    <button type="button" onclick="copyToClipboard('#ticker-shortcode')" class="btn tooltipped tooltipped-s copyMe"
                            style="display: block;"><i class="fa fa-check"></i>Copy to clipboard</button>
                </div>
                <div class="col-md-12 shorcode_note cartpage">
                    <h2 class="sub-heading">Where to paste Shortcode?</h2>
                    <p><b>Step 1</b></p>
                    <ul>
                        <li>Check which page you selected above in "Setting Section". </li>
                    </ul>

                    <p><b>Step 2 </b>If Selected page is <b>Cart Page</b> then,</p>
                    <ul>              
                        <li>Copy the Shortcode from above and paste it in <a href="https://<?php echo $shopdomain->domain ?>/admin/themes/current?key=templates/cart.liquid" target="_blank"><b>cart.liquid</b></a>.
                            <a class="screenshot" href="{{ asset('image/shortcode-paste1.png') }}">
                                <b>See Example</b>
                            </a>
                        </li>
                        <li>If your theme is section theme then paste it in <a href="https://<?php echo $shopdomain->domain ?>/admin/themes/current?key=sections/cart-template.liquid" target="_blank"><b>cart-template.liquid</b></a>.<a class="screenshot" href="{{ asset('image/shortcode.png') }}"><b> See Example</b></a>
                        </li>
                        <li><b>Note: </b>Please paste the shortcode only once.
                        </li>
                    </ul>

                    <p><b>Step 3 </b>If Selected page is <b>Product Page</b> then,</p>
                    <ul>
                        <li>Copy the Shortcode from above and paste it in <a href="https://<?php echo $shopdomain->domain ?>/admin/themes/current?key=templates/product.liquid" target="_blank"><b>product.liquid</b></a>.<a class="screenshot" href="{{ asset('image/product.png') }}"><b> See Example</b></a></li>
                        <li>If your theme is section theme then paste it in <a href="https://<?php echo $shopdomain->domain ?>/admin/themes/current?key=sections/product-template.liquid" target="_blank"><b>product-template.liquid</b></a>.<a class="screenshot" href="{{ asset('image/product-template1.png') }}"><b> See Example</b></a></li>
                        <li><b>Note: </b>Please paste the shortcode only once.</li>
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
                    <p><strong>Note : </strong>Select the page from the option where you want to paste the code then click 'Put Shortcode' and will get your job done.</p>

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
    </div>
</section>
@endsection
@section('pageScript')  
<script type="text/javascript" src="{{ asset('intro-js/intro.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.js"></script>
<script type="text/javascript" src="{{ asset('js/custom.js') }}"></script>
<script type="text/javascript">
ShopifyApp.ready(function() {
        ShopifyApp.Bar.initialize({
            icon: "",
            title: '',
            buttons: {
                primary: {
                    label: 'Demo',
                    callback: function(event) {
                        introJs().start();
                    }
                },
                secondary: [
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