<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>ISP Access</title>


    <link href="{{ $assets_url }}/css/app.css?{{ $version }}" rel="stylesheet">
    <link href="{{ $assets_url }}/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="{{ $assets_url }}/common/intltelinput/intlTelInput.css" rel="stylesheet">

    <script src="{{ $assets_url }}/common/intltelinput/intlTelInput.min.js"></script>
    <script src="{{ $assets_url }}/tailwind/tailwindcss.js?{{ $version }}"></script>

    <script>
        tailwind.config = {
            important: true,
        }
    </script>
</head>

<body class="bg-blue-50">

    @yield('content')

    <script>
        var input = document.querySelector("#phone");

        window.intlTelInput(input, {
            // any initialisation options go here
            onlyCountries: ["ke"],
            separateDialCode: true,

        });
    </script>


</body>

</html>
