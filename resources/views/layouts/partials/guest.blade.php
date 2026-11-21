<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

<head>
    {{-- Reuse your existing Head partial to keep CSS consistent --}}
    @include('layouts.partials.head')
    <title>@yield('title', 'Login') | OCEI</title>
</head>

<body>

<!-- Main Content Wrapper for Guest Pages -->
<main class="main" id="top">
    <div class="container-fluid">
        {{-- This is where the Login/Register form will be injected --}}
        @yield('content')
    </div>
</main>

{{-- Reuse your existing Scripts partial --}}
@include('layouts.partials.scripts')

</body>
</html>
