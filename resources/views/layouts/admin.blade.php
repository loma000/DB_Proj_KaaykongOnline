@props(['title' => 'Admin dashboard', 'active' => 'dashboard'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-screen bg-white font-sans text-zinc-800 antialiased">
        {{-- Sidebar --}}
        <aside class="flex w-64 shrink-0 flex-col justify-between border-r border-zinc-200 bg-zinc-50 p-6">
            <div>
                {{-- Brand Logo --}}
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="flex size-7 items-center justify-center rounded-lg bg-[#cf1e1e]">
                        <span class="block h-[9px] w-[14px] translate-y-[3px] rounded-b-[6px] bg-white"></span>
                    </div>
                    <span class="text-base font-semibold tracking-tight text-zinc-900">KaayKongKruaOnline</span>
                </a>

                <div class="mt-3">
                    <span class="inline-block rounded-full border border-zinc-200 bg-white px-3 py-0.5 text-xs font-medium text-zinc-600">
                        Admin dashboard
                    </span>
                </div>

                {{-- Navigation Links --}}
                <nav class="mt-8 flex flex-col gap-1.5" aria-label="Admin Navigation">
                    <a href="{{ route('admin.dashboard') }}"
                       class="rounded-xl px-4 py-2.5 text-sm font-medium transition-colors {{ $active === 'dashboard' ? 'border border-zinc-200/80 bg-white text-zinc-900 shadow-xs' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}"
                       class="rounded-xl px-4 py-2.5 text-sm font-medium transition-colors {{ $active === 'users' ? 'border border-zinc-200/80 bg-white text-zinc-900 shadow-xs' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        Users
                    </a>
                    <a href="{{ route('admin.products') }}"
                       class="rounded-xl px-4 py-2.5 text-sm font-medium transition-colors {{ $active === 'products' ? 'border border-zinc-200/80 bg-white text-zinc-900 shadow-xs' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        Products
                    </a>
                    <a href="{{ route('admin.orders') }}"
                       class="rounded-xl px-4 py-2.5 text-sm font-medium transition-colors {{ $active === 'orders' ? 'border border-zinc-200/80 bg-white text-zinc-900 shadow-xs' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        Orders
                    </a>
                    <a href="{{ route('admin.coupons') }}"
                       class="rounded-xl px-4 py-2.5 text-sm font-medium transition-colors {{ $active === 'coupons' ? 'border border-zinc-200/80 bg-white text-zinc-900 shadow-xs' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        Coupons
                    </a>
                </nav>
            </div>

            {{-- Sidebar Footer --}}
            <div class="border-t border-zinc-200 pt-5">
                <a href="{{ route('home') }}" class="text-sm font-medium text-zinc-700 hover:text-zinc-900">
                    ← Back to store
                </a>
                <p class="mt-1 text-xs text-zinc-400">Signed in as admin</p>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto bg-white p-10">
            {{ $slot }}
        </main>

        @fluxScripts
    </body>
</html>