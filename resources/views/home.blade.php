<!DOCTYPE html>
<html lang="en" @if(config('config.direction') == 'rtl') dir="rtl" @endif>
    <head>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{env('GOOGLE_TAG_MANAGER_GTM_ID')}}"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '{{env('GOOGLE_TAG_MANAGER_GTM_ID')}}');
        </script>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="Rodrigo Butta">
        <title>{{env('APP_NAME')}}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="shortcut icon" href="/images/favicon.png">
        @if(config('config.direction') == 'rtl')
            <link href="{{ mix('/css/style-rtl.css') }}" id="direction" rel="stylesheet">
        @else
            <link href="{{ mix('/css/style.css') }}" id="direction" rel="stylesheet">
        @endif
        <link href="{{ mix('/css/colors/'.config('config.color_theme').'.css') }}" id="theme" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        {{-- Fix para los console log errors de seguridad https://stackoverflow.com/questions/31211359/refused-to-load-the-script-because-it-violates-the-following-content-security-po --}}
        <meta http-equiv="Content-Security-Policy" content="default-src *; style-src 'self' http://* 'unsafe-inline'; script-src 'self' http://* 'unsafe-inline' 'unsafe-eval'" />


    </head>
    <body class="fix-header fix-sidebar card-no-border mini-sidebar">
        <div class="preloader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
        </div>
        <div id="root">
            <router-view></router-view>
        </div>
        <script src="/js/lang"></script>
        <script src="{{ mix('/js/plugin.js') }}"></script>
        <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
