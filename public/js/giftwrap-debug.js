var base_path_giftwrap = "https://zestardshop.com/shopifyapp/zestard_gift_wrap/public/";
var shop = Shopify.shop;

if (typeof jQuery == 'undefined') {
    setTimeout(function() {
        var scr = document.createElement('script');
        scr.src = base_path_giftwrap + 'js/jquery_3.2.1.js';
        document.head.appendChild(scr);
        scr.onload = function() {
            $zt_jq_giftwrap = window.jQuery;
            gift_wrap();
        };
    }, 300);
} else {
    $zt_jq_giftwrap = jQuery;
    gift_wrap();
}
window.addEventListener('load', function() {
    $zt_jq_giftwrap(".cart__remove").click(function(){
        clearcart();
    });
}, true);

function gift_wrap(){
    var id = $('.giftwrap').attr('id');
    var page = $('.giftwrap').attr('page');

    if (id) {
        if (shop == "empayar-ailyqairy.myshopify.com") {
            //alert(id);
        }
        $.ajax({
            type: "GET",
            url: base_path_giftwrap + "preview",
            crossDomain: true,
            data: {
                'id': id,
                'page': page
            },
            success: function(data) {
                //console.log(data);
                $(".giftwrap").html(data);
            }
        });
    }
}
 function clearcart(){
    var page = $('.giftwrap').attr('page');
    if (page == 'cart') {
        var my_itmes = '';
        var items_count = '';
        $.ajax({
            type: 'GET',
            url: 'http://'+shop+'/cart.json',
            dataType: 'json',
            success: function(data) { 
                items_count = data.item_count;
                my_itmes = data.items;
                 if (items_count == 2) {
                    $.each(my_itmes, function( key, value ) {
                      if (value.vendor == 'zestard-gift-wrap') {
                          $.ajax({
                            type: "POST",
                            url: '/cart/clear.js',
                            dataType: 'json',
                            success: function() { 
                                 location.reload();
                            }
                          });
                      }
                    });
                  
                }
            }
        });
    }
}