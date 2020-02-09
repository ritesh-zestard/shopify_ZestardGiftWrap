function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    //alert('Copied!');
    $temp.remove();
}

$(document).ready(function() {
    startloader();
    $(".screenshot").fancybox();
    jQuery(".copyMe").click(function() {
        var count = jQuery('.show').length;
        if (count == 0) {
            jQuery(".show").show();
            jQuery(".success-copied").html('<div class="alert success"><dl><dt>Success!</dt><dd>Your shortcode has been copied.</dd></dl></div>');
        }
        setTimeout(function() {
            $('.success-copied').fadeOut('slow');
        }, 3000);
    });
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('#upload_image').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#upload_gift_image").change(function(e) {
    var file = this.files[0];
    type = file.type;
    readURL(this);

});

function startloader(process) {
    if (process == 1) {
        $(".loader").css({
            'display': 'block',
            'background-image': 'url({{ asset("image / loader1.gif") }})',
            'background-repeat': 'no-repeat',
            'background-attachment': 'fixed',
            'background-position': 'center'
        });
    } else {
        $(".loader").css({
            'display': 'none',
            'background-image': 'none',
        });
    }
}

function giftWrapForm() {
    $('#buttonLoader').css('display', 'inline-block');
    $('.submit-giftwrap').attr('disabled', 'disabled');
}
$(document).on("click", ".submit-giftwrap", function() {
    var gift_title = $('#gift_title').val();
    var gift_description = $('#gift_description').val();
    var gift_amount = $('#gift_amount').val();
    if (gift_title == '') {
        $('#gift_title').css('border', '1px solid #f00');
        return false;
    } else if (gift_description == '') {
        $('#gift_description').css('border', '1px solid #f00');
        return false;
    } else if (gift_amount == '') {
        $('#gift_amount').css('border', '1px solid #f00');
        return false;
    } else {
        var text_length = $("#gift_description").val().length;
        var title_length = $("#gift_title").val().length;

        if (text_length > 80) {
            $('.text_error').text('Put your description with maximum 80 charater.');
            $('#gift_description').css('border', '1px solid #f00');
            setTimeout(function() {
                $('.text_error').fadeOut('slow');
            }, 5000);
            return false;
        } else if (title_length > 30) {
            $('.title_error').text('Put your title with maximum 30 charater.');
            $('#gift_title').css('border', '1px solid #f00');
            setTimeout(function() {
                $('.title_error').fadeOut('slow');
            }, 5000);
            return false;
        } else {
            $('#giftwrap').submit();
            $('#gift_title,#gift_amount,#gift_description').css('border', '');
        }

    }

});
setTimeout(function() {
    $('.notification').fadeOut('slow');
}, 3000);


$("#shortcode_pase_template").click(function() {
    var template_value = $('#select_template_page').val();
    if (template_value == '1') {
        $('.template_id').attr('value', template_value);
        $("#RemoveCartVariants").submit();
        $('#select_template_page').css('border', '');
        $(".submit-cart-code").attr("disabled", "disabled");
        $(".btn-loader-icon-cart").css("display", "block");
    } else if (template_value == '2') {
        $('.template_id').attr('value', template_value);
        $("#RemoveProductVariants").submit();
        $('#select_template_page').css('border', '');
        $(".submit-cart-code").attr("disabled", "disabled");
        $(".btn-loader-icon-cart").css("display", "block");
    } else {
        $('#select_template_page').css('border', '1px solid red');
        return false;
    }
});

$(".Onclick").click(function() {
    startloader(1);
    return true;
});