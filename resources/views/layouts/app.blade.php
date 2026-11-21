<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

<head>
    @include('layouts.partials.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <div class="container" data-layout="container">
            <script>
                var isFluid = JSON.parse(localStorage.getItem('isFluid'));
                if (isFluid) {
                    var container = document.querySelector('[data-layout]');
                    container.classList.remove('container');
                    container.classList.add('container-fluid');
                }
            </script>

            {{-- 1. Sidebar (Vertical Nav) --}}
            @include('layouts.partials.sidebar')

            <div class="content">
                {{-- 2. Top Navbar --}}
                @include('layouts.partials.topbar')

                {{-- 3. Dynamic Page Content --}}
                @yield('content')

                {{-- 4. Footer --}}
                @include('layouts.partials.footer')
            </div>

            {{-- Authentication Modal (Global) --}}
            @include('layouts.partials.auth-modal')
        </div>
    </main>
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

    {{-- Settings Panel (Offcanvas) --}}
    @include('layouts.partials.settings-panel')

    {{-- Global Scripts --}}
    @include('layouts.partials.scripts')

    {{-- Specific Page Scripts --}}
    @stack('scripts')
</body>

</html>