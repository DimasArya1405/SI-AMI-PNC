<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIAMI') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo-pnc-1.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo-pnc-1.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo-pnc-1.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Data Table --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    {{-- Bootstrap Icon --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    

    {{-- Bootstrap --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"> --}}

    {{-- CSS --}}
    <style>
        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        [x-cloak] {
            display: none !important;
        }

        html.siami-modal-open,
        body.siami-modal-open {
            height: 100%;
            overflow: hidden !important;
        }

        table.dataTable tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .dataTables_wrapper table.dataTable {
            width: 100% !important;
        }

        .dataTables_wrapper .dataTables_length select {
            min-width: 2rem;
            padding-right: 2rem;
            background-position: right .3rem center;
        }

        /* Opsional: Membuat scrollbar lebih tipis dan modern */
        aside::-webkit-scrollbar {
            width: 5px;
        }

        aside::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        aside::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            /* Warna abu-abu muda tailwind */
            border-radius: 10px;
        }

        aside::-webkit-scrollbar-thumb:hover {
            background: #3b82f6;
            /* Warna biru saat di-hover */
        }

        [id^="modal-"].fixed {
            position: fixed !important;
            background: rgba(17, 24, 39, .55) !important;
            inset: 0 !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            z-index: 9999 !important;
            width: 100vw !important;
            max-width: none !important;
            height: 100vh !important;
            height: 100dvh !important;
            min-height: 100vh !important;
            min-height: 100dvh !important;
            max-height: none !important;
            margin: 0 !important;
            padding: 1rem;
            box-sizing: border-box;
            align-items: center !important;
            justify-content: center !important;
            overflow-y: auto !important;
            overscroll-behavior: contain;
        }

        [id^="modal-"].fixed > .relative {
            width: min(100%, 44rem) !important;
            max-width: min(100%, 44rem) !important;
            max-height: calc(100vh - 2rem);
            max-height: calc(100dvh - 2rem);
            padding: 0 !important;
        }

        [id^="modal-"].fixed > .relative > .relative.bg-white {
            overflow: hidden;
            border: 0 !important;
            border-radius: .5rem !important;
            padding: 0 !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .28) !important;
        }

        [id^="modal-"].fixed > .relative > .relative.bg-white > .flex.items-center.justify-between:first-child {
            min-height: 3.25rem;
            margin: 0 !important;
            border: 0 !important;
            background: #1d4ed8;
            padding: .875rem 1.5rem !important;
        }

        [id^="modal-"].fixed > .relative > .relative.bg-white > .flex.items-center.justify-between:first-child h3 {
            color: #ffffff !important;
            font-size: 1rem !important;
            font-weight: 700 !important;
            line-height: 1.4;
        }

        [id^="modal-"].fixed [data-modal-hide] svg {
            width: 1rem;
            height: 1rem;
        }

        [id^="modal-"].fixed > .relative > .relative.bg-white > .flex.items-center.justify-between:first-child [data-modal-hide],
        [id^="modal-"].fixed > .relative > .relative.bg-white > [data-modal-hide].absolute {
            width: 2rem !important;
            height: 2rem !important;
            min-width: 2rem !important;
            border-radius: .375rem !important;
            background: rgba(255, 255, 255, .2) !important;
            color: #ffffff !important;
            padding: 0 !important;
        }

        [id^="modal-"].fixed > .relative > .relative.bg-white > .flex.items-center.justify-between:first-child [data-modal-hide]:hover,
        [id^="modal-"].fixed > .relative > .relative.bg-white > [data-modal-hide].absolute:hover {
            background: rgba(255, 255, 255, .3) !important;
        }

        [id^="modal-"].fixed > .relative > .relative.bg-white > form,
        [id^="modal-"].fixed > .relative > .relative.bg-white > div:not(:first-child),
        [id^="modal-"].fixed > .relative > .relative.bg-white > .p-4,
        [id^="modal-"].fixed > .relative > .relative.bg-white > .p-5 {
            padding: 1.25rem 1.5rem !important;
        }

        [id^="modal-"].fixed form > .grid {
            padding-top: 0 !important;
            padding-bottom: 1.25rem !important;
        }

        [id^="modal-"].fixed label {
            margin-bottom: .35rem !important;
            color: #111827 !important;
            font-size: .875rem !important;
            font-weight: 500 !important;
        }

        [id^="modal-"].fixed input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),
        [id^="modal-"].fixed select,
        [id^="modal-"].fixed textarea {
            width: 100%;
            border: 1px solid #d1d5db !important;
            border-radius: .375rem !important;
            background: #ffffff !important;
            color: #111827 !important;
            padding: .55rem .75rem !important;
            font-size: .875rem !important;
            box-shadow: none !important;
        }

        [id^="modal-"].fixed input:focus,
        [id^="modal-"].fixed select:focus,
        [id^="modal-"].fixed textarea:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .16) !important;
            outline: none !important;
        }

        [id^="modal-"].fixed form > .flex.items-center,
        [id^="modal-"].fixed form .flex.items-center.justify-center {
            justify-content: space-between !important;
            gap: .75rem !important;
            margin: 0 !important;
            border-top: 1px solid #e5e7eb !important;
            padding-top: 1.25rem !important;
        }

        [id^="modal-"].fixed button[type="submit"] {
            order: 2;
            min-width: 6rem;
            justify-content: center;
            border: 0 !important;
            border-radius: .375rem !important;
            background: #2563eb !important;
            color: #ffffff !important;
            padding: .625rem 1.25rem !important;
            font-size: .875rem !important;
            font-weight: 700 !important;
        }

        [id^="modal-"].fixed button[type="submit"]:hover {
            background: #1d4ed8 !important;
        }

        [id^="modal-"].fixed form button[type="button"][data-modal-hide] {
            order: 1;
            min-width: 5.5rem;
            justify-content: center;
            border: 0 !important;
            border-radius: .375rem !important;
            background: #ef4444 !important;
            color: #ffffff !important;
            padding: .625rem 1.25rem !important;
            font-size: .875rem !important;
            font-weight: 700 !important;
        }

        [id^="modal-"].fixed form button[type="button"][data-modal-hide]:hover {
            background: #dc2626 !important;
        }

        [id^="modal-"].fixed .text-center h3 {
            color: #111827 !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
        }

        [id^="modal-"].fixed .text-center form > .flex.items-center.justify-center {
            justify-content: center !important;
        }

        [aria-hidden="true"].fixed:not([id^="modal-"]) {
            position: fixed !important;
            background: rgba(17, 24, 39, .55) !important;
            inset: 0 !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            z-index: 9999 !important;
            width: 100vw !important;
            max-width: none !important;
            height: 100vh !important;
            height: 100dvh !important;
            min-height: 100vh !important;
            min-height: 100dvh !important;
            max-height: none !important;
            margin: 0 !important;
            padding: 1rem;
            box-sizing: border-box;
            align-items: center !important;
            justify-content: center !important;
            overflow-y: auto !important;
            overscroll-behavior: contain;
        }

        [aria-hidden="true"].fixed:not([id^="modal-"]) > .relative {
            width: min(100%, 44rem) !important;
            max-width: min(100%, 44rem) !important;
            max-height: calc(100vh - 2rem);
            max-height: calc(100dvh - 2rem);
            padding: 0 !important;
        }

        [aria-hidden="true"].fixed:not([id^="modal-"]) > .relative > .relative.bg-white {
            overflow: hidden;
            border: 0 !important;
            border-radius: .5rem !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .28) !important;
        }

        [aria-hidden="true"].fixed:not([id^="modal-"]) > .relative > .relative.bg-white > .flex.items-center.justify-between:first-child {
            min-height: 3.25rem;
            margin: 0 !important;
            border: 0 !important;
            background: #1d4ed8;
            padding: .875rem 1.5rem !important;
        }

        [aria-hidden="true"].fixed:not([id^="modal-"]) > .relative > .relative.bg-white > .flex.items-center.justify-between:first-child h3 {
            color: #ffffff !important;
            font-size: 1rem !important;
            font-weight: 700 !important;
        }

        [aria-hidden="true"].fixed:not([id^="modal-"]) > .relative > .relative.bg-white > .flex.items-center.justify-between:first-child [data-modal-hide] {
            width: 2rem !important;
            height: 2rem !important;
            min-width: 2rem !important;
            border-radius: .375rem !important;
            background: rgba(255, 255, 255, .2) !important;
            color: #ffffff !important;
            padding: 0 !important;
        }

        [aria-hidden="true"].fixed:not([id^="modal-"]) input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),
        [aria-hidden="true"].fixed:not([id^="modal-"]) select,
        [aria-hidden="true"].fixed:not([id^="modal-"]) textarea {
            width: 100%;
            border: 1px solid #d1d5db !important;
            border-radius: .375rem !important;
            background: #ffffff !important;
            color: #111827 !important;
            padding: .55rem .75rem !important;
            font-size: .875rem !important;
            box-shadow: none !important;
        }

        [aria-hidden="true"].fixed:not([id^="modal-"]) button[type="submit"] {
            border: 0 !important;
            border-radius: .375rem !important;
            background: #2563eb !important;
            color: #ffffff !important;
            padding: .625rem 1.25rem !important;
            font-size: .875rem !important;
            font-weight: 700 !important;
        }

        [aria-hidden="true"].fixed:not([id^="modal-"]) form button[type="button"][data-modal-hide] {
            border: 0 !important;
            border-radius: .375rem !important;
            background: #ef4444 !important;
            color: #ffffff !important;
            padding: .625rem 1.25rem !important;
            font-size: .875rem !important;
            font-weight: 700 !important;
        }

        @media (max-width: 767px) {
            .app-sidebar {
                position: fixed !important;
                top: 4rem !important;
                left: 0 !important;
                width: 16rem !important;
                max-width: 85vw;
                height: calc(100vh - 4rem) !important;
                z-index: 40;
                border-right: 1px solid #e5e7eb;
                overflow-x: hidden;
                overflow-y: auto;
            }

            .app-sidebar > div {
                display: flex;
                flex-direction: column;
                gap: .25rem;
                min-width: 0;
                padding: .5rem;
            }

            .app-sidebar a,
            .app-sidebar [data-collapse-toggle] {
                white-space: normal;
                padding: .625rem .875rem;
            }

            .app-sidebar ul {
                min-width: 0;
                padding-left: 1rem;
                padding-right: .5rem;
            }

            main .max-w-7xl.mx-auto {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .ml-60,
            .ml-64 {
                margin-left: 0 !important;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none !important;
                width: 100%;
                text-align: left !important;
                margin: .5rem 0;
            }

            .dataTables_wrapper .dataTables_filter label,
            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
                margin-left: 0;
            }

            .dataTables_wrapper table.dataTable {
                min-width: 42rem;
            }

            [id^="modal-"].fixed {
                align-items: flex-start !important;
                padding: 1rem;
            }

            [aria-hidden="true"].fixed:not([id^="modal-"]) {
                align-items: flex-start !important;
                padding: 1rem;
            }

            [id^="modal-"].fixed > .relative {
                max-height: calc(100vh - 2rem);
                max-height: calc(100dvh - 2rem);
                overflow-y: auto;
            }

            [aria-hidden="true"].fixed:not([id^="modal-"]) > .relative {
                max-height: calc(100vh - 2rem);
                max-height: calc(100dvh - 2rem);
                overflow-y: auto;
            }

            [id^="modal-"].fixed form > .flex.items-center,
            [id^="modal-"].fixed form .flex.items-center.justify-center {
                align-items: stretch !important;
                flex-direction: column;
                width: 100% !important;
            }

            [id^="modal-"].fixed button[type="submit"],
            [id^="modal-"].fixed form button[type="button"][data-modal-hide] {
                width: 100%;
                margin: 0 !important;
            }

            [id^="modal-"].fixed > .relative > .relative.bg-white > .flex.items-center.justify-between:first-child [data-modal-hide] {
                width: 2rem !important;
                min-width: 2rem !important;
                max-width: 2rem !important;
            }

            [id^="modal-"].fixed form > .flex.items-center > button[type="submit"] {
                order: 1;
            }

            [id^="modal-"].fixed form > .flex.items-center > button[type="button"][data-modal-hide] {
                order: 2;
            }
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-[#F4F6FF] pt-16">
        @include('layouts.navigation')
        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    {{-- TOAST --}}
    <div id="toast-success"
        class="fixed bg-green-500/70 opacity-0 transition duration-300 ease-in-out rounded text-white top-5 right-5 z-50 flex items-center w-full max-w-sm p-4 text-body bg-neutral-primary-soft rounded-base shadow-xs border border-default"
        role="alert">
        <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-fg-success bg-success-soft rounded">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 11.917 9.724 16.5 19 7.5" />
            </svg>
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ms-3 text-sm font-normal">{{ session('success') }}</div>
        <button type="button"
            class="ms-auto flex items-center justify-center text-body hover:text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded text-sm h-8 w-8 focus:outline-none"
            data-dismiss-target="#toast-success" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18 17.94 6M18 18 6.06 6" />
            </svg>
        </button>
    </div>
    <div id="toast-error"
        class="fixed bg-red-500/70 opacity-0 rounded transition duration-300 ease-in-out text-white top-5 right-5 z-50 flex items-center w-full max-w-sm p-4 text-body bg-neutral-primary-soft rounded-base shadow-xs border border-default"
        role="alert">
        <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-fg-danger bg-danger-soft rounded">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18 17.94 6M18 18 6.06 6" />
            </svg>
            <span class="sr-only">Error icon</span>
        </div>
        <div class="ms-3 text-sm font-normal">{{ session('error') }}</div>
        <button type="button"
            class="ms-auto flex items-center justify-center text-body hover:text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded text-sm h-8 w-8 focus:outline-none"
            data-dismiss-target="#toast-error" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18 17.94 6M18 18 6.06 6" />
            </svg>
        </button>
    </div>
    @if (session('success'))
        <script>
            const toastSuccess = document.getElementById('toast-success');
            if (toastSuccess) {
                toastSuccess.style.opacity = "1";
            }
        </script>
    @endif
    @if (session('error'))
        <script>
            const toastError = document.getElementById('toast-error');
            if (toastError) {
                toastError.style.opacity = "1";
            }
        </script>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const toastSuccess = document.getElementById('toast-success');
            const toastError = document.getElementById('toast-error');

            if (toastSuccess) {
                setTimeout(() => {
                    toastSuccess.style.opacity = "0";
                    setTimeout(() => toastSuccess.remove(), 500);
                }, 3000); // hilang setelah 3 detik
            }

            if (toastError) {
                setTimeout(() => {
                    toastError.style.opacity = "0";
                    setTimeout(() => toastError.remove(), 500);
                }, 3000);
            }

        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modalSelector = '[id^="modal-"].fixed, [aria-hidden="true"].fixed';

            function isModalOpen(modal) {
                return !modal.classList.contains('hidden') && window.getComputedStyle(modal).display !== 'none';
            }

            function syncModalScrollLock() {
                const hasOpenModal = Array.from(document.querySelectorAll(modalSelector)).some(isModalOpen);
                document.documentElement.classList.toggle('siami-modal-open', hasOpenModal);
                document.body.classList.toggle('siami-modal-open', hasOpenModal);
            }

            function observeModal(modal) {
                new MutationObserver(syncModalScrollLock).observe(modal, {
                    attributes: true,
                    attributeFilter: ['class', 'style', 'aria-hidden']
                });
            }

            document.querySelectorAll(modalSelector).forEach(observeModal);

            new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (!(node instanceof Element)) {
                            return;
                        }

                        if (node.matches(modalSelector)) {
                            observeModal(node);
                        }

                        node.querySelectorAll?.(modalSelector).forEach(observeModal);
                    });
                });

                syncModalScrollLock();
            }).observe(document.body, {
                childList: true,
                subtree: true
            });

            document.addEventListener('click', function() {
                setTimeout(syncModalScrollLock, 50);
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    setTimeout(syncModalScrollLock, 50);
                }
            });

            syncModalScrollLock();
        });
    </script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script> --}}
    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    @stack('js')
</body>

</html>
