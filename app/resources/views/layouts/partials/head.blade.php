<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- ===============================================-->
<!--    Document Title-->
<!-- ===============================================-->
<title>@yield('title', 'OCEI Data Archive Solution')</title>

<!-- ===============================================-->
<!--    Favicons-->
<!-- ===============================================-->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/ocei-logo.png')}}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/ocei-logo.png')}}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/ocei-logo.png') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/ocei-logo.png')}}">
<meta name="theme-color" content="#ffffff">
<meta name="msapplication-TileImage" content="{{ asset('assets/img/ocei-logo.png') }}">

<!-- ===============================================-->
<!--    Stylesheets-->
<!-- ===============================================-->
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('vendors/simplebar/simplebar.min.js') }}"></script>

<link href="{{ asset('vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">

<!-- Falcon Theme CSS -->
<link href="{{ asset('assets/css/theme-rtl.min.css') }}" rel="stylesheet" id="style-rtl">
<link href="{{ asset('assets/css/theme.min.css') }}" rel="stylesheet" id="style-default">
<link href="{{ asset('assets/css/user-rtl.min.css') }}" rel="stylesheet" id="user-style-rtl">
<link href="{{ asset('assets/css/user.min.css') }}" rel="stylesheet" id="user-style-default">

<script>
    var isRTL = JSON.parse(localStorage.getItem('isRTL'));
    if (isRTL) {
        var linkDefault = document.getElementById('style-default');
        var userLinkDefault = document.getElementById('user-style-default');
        linkDefault.setAttribute('disabled', true);
        userLinkDefault.setAttribute('disabled', true);
        document.querySelector('html').setAttribute('dir', 'rtl');
    } else {
        var linkRTL = document.getElementById('style-rtl');
        var userLinkRTL = document.getElementById('user-style-rtl');
        linkRTL.setAttribute('disabled', true);
        userLinkRTL.setAttribute('disabled', true);
    }
</script>
