<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    @if($force_https)
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mybizna Erp') }}</title>

    <script>
        PUBLIC_PATH = '{!! $assets_url !!}';
        var base_url = '{!! $mybizna_base_url !!}';
        var root_url = '{!! $url !!}';
        var assets_url = '{!! $assets_url !!}';
        var autologin = '{!! $autologin !!}';
        var responsive_point = {!! $responsive_point !!};
        var floating_top = {{ $floating_top ? 'true' : 'false' }};
        var margin_top = {{ $margin_top ? 'true' : 'false' }};
        var is_wordpress = {{ $is_wordpress ? 'true' : 'false' }};
        var viewside = 'backend';
        var template = "{{ $template ?? 'manage' }}";
        var mybizna_uniqid = '{!! $mybizna_uniqid !!}';

        function __(title, select) {
            return title;
        }
    </script>

    @if(!$is_wordpress)
        {!! rendercss($assets) !!}
        {!! renderjs($assets) !!}

        <script>
            window.addEventListener('load', function() {
                tailwind.config = {
                    important: true,
                    theme: {
                        extend: {
                            backdropBlur: {
                                xs: '2px',
                            }
                        }
                    }
                }
            });
        </script>
    @endif


    <style>
        /* Center the loader */
        #loader {
            margin: 0 auto;
            width: 120px;
            height: 120px;
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Add animation to "page content" */
        .animate-bottom {
            position: relative;
            -webkit-animation-name: animatebottom;
            -webkit-animation-duration: 1s;
            animation-name: animatebottom;
            animation-duration: 1s
        }

        @-webkit-keyframes animatebottom {
            from {
                bottom: -100px;
                opacity: 0
            }

            to {
                bottom: 0px;
                opacity: 1
            }
        }

        @keyframes animatebottom {
            from {
                bottom: -100px;
                opacity: 0
            }

            to {
                bottom: 0;
                opacity: 1
            }
        }

        #loaderDiv {
            text-align: center;
        }
    </style>
</head>

<body class="bg-slate-50 dark:bg-navy-900">

    @if (!$has_setting || $has_uptodate)
        <div class="">
            @include('base::setup.wizard')
        </div>
    @else
        <div id="app">
            <div id="loaderDiv" class="animate-bottom my-5">
                <h2 class="my-3">Loading!....</h2>
                <p class="mb-5">The system require's javascript.</p>
            </div>
            <div>
                <div id="loader"></div>
            </div>
        </div>
    @endif
</body>

</html>
