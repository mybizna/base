<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if ($force_https)
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif

    <title>ISP Access</title>

    @if (!$is_wordpress)
        {!! rendercss($assets) !!}
        {!! renderjs($assets) !!}

        <script>
            tailwind.config = {
                important: true,
            }
        </script>
    @endif
</head>

<body class="bg-slate-50 dark:bg-navy-900">

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
