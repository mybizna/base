<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISP Access</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"
        integrity="sha512-+gShyB8GWoOiXNwOlBaYXdLTiZt10Iy6xjACGadpqMs20aJOoh+PJt3bwUVA6Cefe7yF7vblX6QwyXZiVwTWGg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"
        integrity="sha512-gxWow8Mo6q6pLa1XH/CcH8JyiSDEtiwJV78E+D+QP0EVasFs8wKXq16G8CLD4CJ2SnonHr4Lm/yY2fSI2+cbmw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="http://127.0.0.1:8000/mybizna/css/app.css?{{ rand(10000, 50000) }}"></script>
    <script src="http://127.0.0.1:8000/mybizna/js/tailwindcss.js?{{ rand(10000, 50000) }}"></script>
    <link href="http://127.0.0.1:8000/mybizna/fontawesome/css/all.css" rel="stylesheet">
</head>

<body class="bg-blue-50 dark:bg-blue-900">

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
