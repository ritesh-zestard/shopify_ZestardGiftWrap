@yield('header')
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Gift Wrap Zestard</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <!-- toastr CSS -->
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css">
    
    <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css"> 

    <!-- magnificent popup CSS -->
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
    
    <link rel="stylesheet" href="{{ asset('css/simple-slider.css') }}">

    <!-- custom CSS -->
    <!-- <link rel="stylesheet" href="{{ asset('css/style.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('css/giftwrap-custom.css') }}">
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
        
    <!-- shopify Script for fast load -->
    <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
    <script>
      ShopifyApp.init({
        apiKey: '683afe586e88d50a07a951bb2b89bd4b',
        shopOrigin: '<?php echo "https://".session('shop') ?>'
      });

      ShopifyApp.ready(function() {
          ShopifyApp.Bar.initialize({
            icon: "",
            title: '',
            buttons: {}
          });
        });
    </script>
  </head>
<body>
@yield('content')

<script src="{{ asset('js/jquery.copy-to-clipboard.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/simple-slider.js') }}"></script>
<script src="{{ asset('js/javascript.js') }}"></script>
<script type="text/javascript">
jQuery(function() {
  jQuery('.screenshot').on('click', function() {
    jQuery('.imagepreview').attr('src', jQuery(this).attr('image-src'));
    jQuery('#imagemodal').modal('show');   
  });     
});
</script>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5a2e20e35d3202175d9b7782/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

</body>
</html>
