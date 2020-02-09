@extends('header')
@section('content')
<div class="overlay"></div>
<div class="dashboard container">
    <div class="giftwrap-container" style="text-align:center;">
        <div class="subdiv-content" style="width:70%;">
        <img src="{{ asset('image/gift_wrap_icon.jpg') }}" style="width: 150px;">
            <h4><b>{{ "You have declined the charge in Shopify.Please try again and approved the charge to use this app." }}</b></h4>
            <a href="{{ url('payment_process') }}"><button class="btn btn-info decline_button Onclick">Go back to charge try again</button></a>
            <h4><b>{{ "If you don't want to use this app, please go to store admin > Apps and uninstall this app." }}</b></h4>
            <a href="{{ url('declined') }}"><button class="btn btn-info decline_button Onclick">Go to store apps</button></a>
        </div>
    </div>
</div>
@endsection
@section('pageScript')
<script type="text/javascript" src="{{ asset('js/custom.js') }}"></script>
@endsection
