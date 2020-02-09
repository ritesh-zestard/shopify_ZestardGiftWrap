<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Zestard Gift Wrap</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('css/giftwrap-custom.css') }}">
        <link rel="stylesheet" href="{{ asset('css/toast/toastr.css') }}">
         @yield('pageCss')

    </head>
    <body>
        <main class="full-width">
            @include('layouts.header')
            @yield('content')
            @include('layouts.footer')
        </main>
        
       <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    
    
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
        
    <!-- shopify Script for fast load -->
    <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
        
        <script>
        ShopifyApp.init({
            apiKey: '683afe586e88d50a07a951bb2b89bd4b',
            shopOrigin: '<?php echo "https://" . session('shop') ?>'
        });      
        document.onreadystatechange = function () {
        var state = document.readyState
        if (state == 'complete') {
            setTimeout(function(){
                document.getElementById('interactive');
                var loadclass = document.getElementsByClassName('loader');
                for (var x = 0; x < loadclass.length; x++) {
                    loadclass[x].style.visibility = "hidden";
                }
            },2000);
            }
        }
        </script>
        @yield('pageScript')

    </body>
</html>