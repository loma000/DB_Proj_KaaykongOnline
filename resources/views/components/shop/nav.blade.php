{{-- $active: home | products | deals.  $member: true/false/null (null = detect from auth). --}}
@props(['active' => null, 'member' => null, 'cartCount' => 0])

@php
    $isMember = $member ?? auth()->check();
    $initial = strtoupper(mb_substr(auth()->user()?->username ?? auth()->user()?->name ?? 'S', 0, 1));
@endphp

<flux:header class="h-16! border-b border-zinc-200 bg-white! px-12!">
    <flux:brand :href="route('home')" name="KaayKongKruaOnline" class="me-10">
        <x-slot name="logo" class="size-7 rounded-lg bg-[#cf1e1e]">
            <span class="block h-[9px] w-[14px] translate-y-[3px] rounded-b-[6px] bg-white"></span>
        </x-slot>
    </flux:brand>

    <flux:navbar class="gap-2">
        <flux:navbar.item :href="route('home')" :current="$active === 'home'">Home</flux:navbar.item>
        <flux:navbar.item :href="route('products.index')" :current="$active === 'products'">Products</flux:navbar.item>
        <flux:navbar.item :href="route('products.index', ['deals' => 1])" :current="$active === 'deals'">Deals</flux:navbar.item>
    </flux:navbar>

    <flux:spacer />

    <div class="flex items-center gap-2">
        <flux:button :href="route('products.index')" variant="ghost" size="sm" icon="magnifying-glass" aria-label="Search" />
        <flux:button :href="route('cart')" variant="ghost" size="sm" icon="shopping-cart" aria-label="Cart">
            @if ($cartCount > 0)
                <flux:badge color="red" size="sm" variant="pill">{{ $cartCount }}</flux:badge>
            @endif
        </flux:button>

        @if ($isMember)
            <a href="{{ route('account') }}" aria-label="My account"><flux:avatar size="sm" :initials="$initial" /></a>
        @else
            <flux:button :href="route('login')" variant="outline" size="sm" class="h-9!">Sign in</flux:button>
            <flux:button :href="route('register')" variant="primary" size="sm" class="h-9!">Register</flux:button>
        @endif
    </div>
</flux:header>