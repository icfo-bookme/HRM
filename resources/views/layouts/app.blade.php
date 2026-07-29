<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title', 'BookMe HRM')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&display=swap" rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Toastify CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js" defer></script>

    @stack('head')
</head>

<body class="bg-[#e4ebf1] font-sans antialiased flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top Navigation --}}
        @include('layouts.navigation')

        {{-- Main Content --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 relative flex flex-col">
            @include('components.global-loader')
            <div class="flex-1 w-full">
                {{ $slot }}
            </div>
        </main>
    </div>


    {{-- JAVASCRIPT LIBRARIES --}}

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- DataTables Core + Export Plugins --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    {{-- Toastify --}}
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- Custom Sidebar --}}
    <script src="{{ asset('js/sidebar.js') }}"></script>

    {{-- GLOBAL JAVASCRIPT INITIALISATION --}}

    <script>
        (function() {
            'use strict';

            var isInitialPageLoad = true;

            function isDataTableProcessing() {
                return $('.dataTables_processing:visible').length > 0 ||
                    $('body').hasClass('dt-custom-loading');
            }

            function hideLoader() {
                if (!isDataTableProcessing()) {
                    $('#global-loader').addClass('hidden');
                    isInitialPageLoad = false;
                }
            }

            // Ajax lifecycle
            $(document).on('ajaxStart', function() {
                if (isInitialPageLoad && !isDataTableProcessing()) {
                    $('#global-loader').removeClass('hidden');
                }
            });

            $(document).on('ajaxStop', function() {
                setTimeout(hideLoader, 100);
            });

            // Page load complete
            if (document.readyState === 'complete') {
                hideLoader();
            } else {
                $(window).on('load', hideLoader);
            }

            // CSRF token for all ajax requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Default Select2 initialisation
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: 'Select an option',
                    width: '100%'
                });
            });
        })();
    </script>

    @stack('scripts')

</body>

</html>
