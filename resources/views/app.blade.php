<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="slimScroll">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="author" content="Era of Ecom">
        <meta name="description" content="">
        <title>{{ env('APP_NAME_FORMATTED') }}</title>
        <script type="text/javascript" src="https://apis.google.com/js/api.js"></script>
        <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="{{env('DROPBOX_APP_KEY')}}"></script>
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Titillium+Web:wght@400;600&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css">

        <link rel="stylesheet" href="{{ mix('css/imports.css') }}">
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
        <link rel="stylesheet" href="{{ mix('/css/limbo.css') }}">
        <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.png" />

        <script>
            window.appSlug = {!! json_encode([
                'appId'         => env('APP_NAME'),
                'appName'       => env('APP_NAME_FORMATTED'),
                'appVendor'     => env('APP_VENDOR'),
                'siteUrl'       => config('app.url'),
                'apiUrl'        => config('app.url') . '/api',
                'shopifyKey'    => env('SHOPIFY_API_KEY')
            ]) !!};
        </script>
    </head>
    <body>
        <div id="app" v-cloak></div>
        <div class="book">
            <div class="book__page"></div>
            <div class="book__page"></div>
            <div class="book__page"></div>
            <span class="loading-text">{{env('APP_VENDOR')}}</span>
        </div>
        <div class="veil" style="display: none">
            <p class="no-select">Please wait ...</p>
        </div>

        <script src="{{ mix('/js/manifest.js') }}"></script>
        <script src="{{ mix('/js/vendor.js') }}"></script>
        <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
