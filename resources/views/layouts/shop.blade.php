@props(['title' => null, 'active' => null, 'member' => null, 'cartCount' => 0])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-screen flex-col bg-white font-sans text-zinc-800 antialiased">
        <x-shop.nav :active="$active" :member="$member" :cart-count="$cartCount" />

        <main class="flex-1">
            {{ $slot }}
        </main>

        <x-shop.footer />

        @fluxScripts
    </body>
</html>