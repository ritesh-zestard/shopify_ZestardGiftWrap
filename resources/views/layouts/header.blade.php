<header class="headerDiv">
    <div class="container notification">
        <div class="adjust-margin">
            @if(Session::has('success'))
            <div class="alert success">
                <dl>
                    <dt>Success!</dt>
                    <dd> {!! session('success') !!}</dd>
                </dl>
            </div>
            @endif

            @if(Session::has('error'))
            <div class="alert error">
                <dl>
                    <dt>error!</dt>
                    <dd> {!! session('error') !!}</dd>
                </dl>
            </div>
            @endif           
        </div>
    </div>
    <div class="container">
        <div class="adjust-margin toc-block">
            <h1 class="toc-title">Settings</h1>
        </div>
    </div>	
</header>