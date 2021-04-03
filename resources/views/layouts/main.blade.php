<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page_title ?? trans('early-access::common.early_access') }} | {{ config('app.name') }}</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Rajdhani:600" rel="stylesheet" type="text/css">
    <link href="{{ asset('vendor/early-access/css/early-access.css') }}" rel="stylesheet">
</head>
<body class="h-full w-full">
<div id="app" class="flex flex-col w-full h-full">
    <nav class="container w-full flex justify-between items-center">
        <div class="py-2">
            <a href="{{ route('early-access.index') }}" class="block mt-1 no-underline">
                <span class="text-5xl font-logo uppercase font-black tracking-tight block text-indigo-dark">
                    {{ config('app.name') }}
                </span>
            </a>
        </div>

        @if ($loginUrl = config('early-access.login_url'))
            <a class="block no-underline" href="{{ $loginUrl }}" title="@lang('early-access::common.login')">
                <span class="text-sm text-indigo-dark hover:text-indigo-darkest uppercase font-medium">
                    @lang('early-access::common.login')
                </span>
            </a>
        @endif
    </nav>
    <main class="mt-20 md:mt-32 flex-1">
        @yield('content')
    </main>
    <footer class="mt-20 md:mt-32 container py-5">
        <span class="block text-sm text-center">
            @lang('early-access::common.copyright', ['year' => date('Y'), 'name' => config('app.name')])
        </span>
    </footer>
</div>
</body>
</html>
