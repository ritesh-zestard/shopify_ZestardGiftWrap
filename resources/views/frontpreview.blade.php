<link href="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.9/themes/blitzer/jquery-ui.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    function startloader(process) {
        if (process == 1) {
            $(".overlay").css({
                'position': 'fixed',
                'display': 'block',
                'background-image': 'url({{ asset("image/loader_new.svg") }})',
                'background-repeat': 'no-repeat',
                'background-attachment': 'fixed',
                'background-position': 'center'
            });
        } else {
            $(".overlay").css({
                'position': 'unset',
                'display': 'none',
                'background-image': 'none',
            });
        }
    }
</script>
<script>
    $(document).ready(function () {
        startloader();
        $('button[type=button]').removeAttr('disabled');
        var shop_id = '{{ $id }}';
        var page = '{{ $page }}';
        var shop_name = Shopify.shop;
        $.ajax({
            type: "GET",
            url: "{{ route('front_preview') }}",
            data: {
                'id': shop_id,
                'shop_name': shop_name
            },
            beforeSend: function () {
                $('.loader').show();
            },
            complete: function () {
                $('.loader').hide();
            },
            success: function (data) {
                setdata = JSON.parse(data);
                if (setdata) {
//                    if (shop_name == "give-personalised-gifts.myshopify.com")
//                    {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('giftwrap_image') }}",
                        data: {
                            'product_id': setdata.giftwrap_id,
                            'variant_id': setdata.variant_id,
                            'shop_name': shop_name
                        },
                        complete: function () {
                            $('.loader').hide();
                        },
                        success: function (data) {
                            $("#giftwrap_input").next('label');
                            //$("#giftwrap_input").next('label').after("<img class='giftwrap_image' src='" + data + "' width='40' height='40'></img>");
                            $('.show_desc').after("<img class='giftwrap_image' src='" + data + "' width='40' height='40'></img>");
                            $(".giftwrap_image").css('cssText', " margin-right: 10px;position:relative;top: 15px;");
                        }
                    });
                    // }
                    $('#giftwrap_input').next('label').after(setdata.gift_description + " (" + setdata.shop_currency + setdata.gift_amount + ")");
                    $('#gift_price').val(setdata.gift_amount);
                    $('#gift_wrap_variant_id').val(setdata.variant_id);
                }
                if (setdata.gift_message == 1) {
                    $('#gift_message_note').val(setdata.gift_message);
                }
                if (setdata.select_page == 1 && page == "cart") {
                    $('.show_frontend').css('display', 'block');
                } else if (setdata.select_page == 0 && page == "product") {
                    $('.show_frontend').css('display', 'block');
                } else {
                    if (shop_name == "tospitikomas-com.myshopify.com") {
                        $('.show_frontend').css('display', 'block');
                    } else {
                        $('.show_frontend').css('display', 'none');
                    }

                }
            }
        });
        if (page == "product") {
            $('button[type=button]').removeAttr('disabled');
            $("#giftwrap_input").click(function () {
                var gift_message = $('#gift_message_note').val();
                if (gift_message == 1) {
                    $(".zt_message_note").slideToggle(this.checked);
                }
            });
            var button = $('button[type=submit][name=add], button[type=button]');
            if (button.length == 0)
            {
                button = $('input[type=submit][name=add], button[type=button]');
            }
            if (button.length == 0)
            {
                var f = $('form[action="/cart/add"]');
                button = $(f).find(':submit');
            }
            button.click(function (e) {
                $('button[type=button]').removeAttr('disabled');
                var productname = $("[itemprop=name]").text();
                var giftwrap_variant_id = $('#gift_wrap_variant_id').val();
                if ($("#giftwrap_input").is(':checked')) {
                    var gift_message = $('#gift_message_note').val();
                    var note_msg = $('#giftwrap_text_message').val();
                    if (note_msg == '' && gift_message == 1) {
                        alert('Please enter gift message note');
                        return false;
                    } else if (note_msg.length > 150) {
                        alert('Not allowed to more than 150 character.');
                        return false;
                    } else {
                        var note = $("#giftwrap_text_message").val();
                        if (note == '') {
                            note = "Greeting from the Gift Wrap"
                        }
                        e.stopPropagation();
                        e.preventDefault();
                        $.post('/cart/add.js', {
                            quantity: 1,
                            id: giftwrap_variant_id,
                            properties: {
                                'Note': note
                            }
                        }, function (data) {
                            button.closest("form").submit();
                            return false;
                        }, 'json');
                    }
                } else if ($("#giftwrap_input").prop('checked', false)) {
                    button.closest("form").submit();
                } else {
                    button.closest("form").submit();
                }
            });
        } else {
            $("#giftwrap_input").click(function () {
                var gift_message = $('#gift_message_note').val();
                if (gift_message == 1) {
                    $(".zt_message_note").slideToggle(this.checked);
                }
                var giftwrap_variant_id = $('#gift_wrap_variant_id').val();
                if ($("#giftwrap_input").is(':checked')) {
                    $.post('/cart/add.js', {
                        quantity: 1,
                        id: giftwrap_variant_id,
                    });
                } else if ($("#giftwrap_input").prop('checked', false)) {
                    $.post('/cart/update.js', 'updates[' + giftwrap_variant_id + ']=0')
                }
            });
        }

        if (page == 'cart') {
            $('input[name=update]').on('click', function (e) {
                var gift_wrap_variant_id = $('#gift_wrap_variant_id').attr('value');
                $(".cart__image-wrapper").each(function () {
                    var product_variant = ($(this).attr('id'));
                    if (gift_wrap_variant_id == product_variant) {
                        alert('Not allowed to add the product more then once');
                    }
                });

            });
        }
    });
</script>
{{-- <div class="overlay" ></div> --}}
<div class='loader    '>
    <img width='130' height='130' src="{{ asset("image/loader_new.svg") }}"/>
</div>
<div class="show_frontend" style="display:none;">
    <div class="show_desc">
        <input type="hidden" name="gift_message_note" id="gift_message_note">
        <input type="hidden" name="gift_wrap_variant_id" id="gift_wrap_variant_id">
        <span class="checkboxFive">
            <input type="checkbox" name="giftwrap_frontend" id="giftwrap_input">        
            <label for="giftwrap_input"></label>
        </span>
    </div>
    <div class="show_charge">
        <input type="hidden" name="gift_price" id="gift_price">
    </div>
    <div class="zt_message_note" style="display:none;">
        <textarea name="attributes[gift-message-note]" id="giftwrap_text_message" class="form-control" placeholder="Gift Message Note"></textarea>
    </div>
</div>

<style type="text/css">
    /*checkbox design start*/
    .checkboxFive label {
        cursor: pointer;
        position: absolute;
        width: 14px;
        height: 14px;
        top: 0;
        left: 0;
        background: transparent;
        border-radius: 2px;
        border: 1px solid #7796a8;
        margin-top: 2px;
    }
    input#giftwrap_input{visibility: hidden;}
    .checkboxFive {
        position: relative;
        width: 24px;
        height: 24px;
        margin-right: 5px;
        font-size: 14px;
        display: inline;
        padding: 0 25px;
    }
    .checkboxFive label:after {
        opacity: 0;
        content: '';
        position: absolute;
        width: 8px;
        height: 5px;
        background: transparent;
        top: 2px;
        left: 2px;
        border: 2px solid #fff;
        border-top: none;
        border-right: none;
        -webkit-transform: rotate(-45deg);
        -moz-transform: rotate(-45deg);
        -o-transform: rotate(-45deg);
        -ms-transform: rotate(-45deg);
        transform: rotate(-45deg);
    }
    .checkboxFive input[type=checkbox]:checked + label:after{opacity: 1;}
    .checkboxFive input[type=checkbox]:checked + label{background-color:#7796a8;}
    /*checkbox design end*/

    .overlay {
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        transition: opacity 500ms;
        z-index: 999;
    }
    .overlay:target {
        visibility: visible;
        opacity: 1;
    }    
    #giftwrap_input {
        margin-right: 0px;
        margin-left: 0px;
        margin-top: 0;
    }
    img.giftwrap_image {
        float: left;
        width: 25%;
        height: 100%;
        padding-bottom: 20px;
    }
    .zt_message_note{
        margin-top: 10px;
        float: left;        
    }
    .show_frontend {
        float: left !important;
        width: 100%;
        padding: 8px 12px 20px;
        background-color: #F4F6F8;
        border-radius: 3px;
        border: 1px solid #b1c9d8;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.16), 0 0 0 1px rgba(0,0,0,0.08);
    }
    #giftwrap_price {
        display: inline-block;
        padding-right: 10px;
        margin-top: 0;
        margin-bottom: 0;
        font-size: 13px;
    }
    .show_charge {
        float: right;
    }
    .show_desc {
        float: left;
        width:100%;
    }
    .show_frontend {
        float: none !important;
        overflow: hidden;
        box-shadow: none;
        background-color: #909ca8;
    }

    .checkboxFive {
        color: #fff;
        padding-left: 10px;
    }

    img.giftwrap_image {
        top: 0 !important;
        width: 22%;
        border-radius: 5px;
        padding: 0;
    }

    .show_desc {
        margin-bottom: 15px;
    }

    .zt_message_note {
        margin: 0;
        line-height: 0;
    }

    .zt_message_note textarea {
        height: 85px;
        border-radius: 5px;
        width: 100%;
        min-height: 0;
        line-height:20px;
    }

    .checkboxFive label {background: #fff;width: 20px;height: 20px;border-radius: 4px;}

    .checkboxFive input[type=checkbox]:checked + label {
        background: #fff;
    }

    .checkboxFive label:after {
        border-color: #000;
        width: 13px;
        height: 7px;
        left: 3px;
        top: 4px;
    }    
</style>