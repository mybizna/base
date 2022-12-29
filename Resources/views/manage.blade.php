<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mybizna Erp') }}</title>

    <script>
        @if(config('mybizna.is_local'))
            var base_url = '{{  url("/"); }}';
        @else
            var base_url = '{{  secure_url("/"); }}';
        @endif

        function __(title, select){
            return title;
        }

    </script>

    <script defer="defer" src="/mybizna/tinymce/tinymce.min.js?{{ rand(10000,50000) }}"></script>
    <script defer="defer" src="/mybizna/js/app.js?{{ rand(10000,50000) }}"></script>
    <link href="/mybizna/css/app.css?{{ rand(10000,50000) }}" rel="stylesheet">
    <script src="/mybizna/js/tailwindcss.js?{{ rand(10000,50000) }}"></script>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->

    <script>
        tailwind.config = {
            important: true,
        }
    </script>

  
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" rel="stylesheet">


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

<body>
    <div id="app">
        <div id="loaderDiv" class="animate-bottom my-5">
            <h2 class="my-3">Loading!....</h2>
            <p class="mb-5">The system require's javascript.</p>
        </div>
        <div>
            <div id="loader"></div>
        </div>
    </div>
</body>

</html>
