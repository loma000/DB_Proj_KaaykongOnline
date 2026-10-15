@php
    // Mock data - replace with real categories / products from the backend.
    $categories = [['Cookware', 48], ['Knives', 32], ['Bakeware', 27], ['Linens', 19]];
    $bestSellers = [
        ['id' => 1, 'name' => 'Cast iron skillet, 26 cm', 'price' => 1290, 'rating' => 4.8, 'reviews' => 126],
        ['id' => 2, 'name' => 'Chef knife, 20 cm', 'price' => 2450, 'rating' => 4.9, 'reviews' => 212],
        ['id' => 3, 'name' => 'Carbon steel wok, 32 cm', 'price' => 890, 'rating' => 4.6, 'reviews' => 84],
        ['id' => 4, 'name' => 'Loaf pan set of 3', 'price' => 590, 'rating' => 4.7, 'reviews' => 58],
    ];
@endphp

<x-layouts::shop title="Home" active="home" :cart-count="2">
    {{-- Hero --}}
    <section class="flex items-center gap-12 bg-zinc-100 px-12 py-16">
        <div class="flex min-w-0 flex-1 flex-col items-start gap-5">
            <flux:badge>New: spring bakeware is here</flux:badge>
            <flux:heading size="xl" level="1">Tools worth keeping on the counter.</flux:heading>
            <flux:subheading size="lg">Cookware, knives and bakeware, picked for kitchens that get used every day.</flux:subheading>
            <div class="flex gap-3">
                <flux:button :href="route('products.index')" variant="primary">Shop all products</flux:button>
                <flux:button :href="route('products.index', ['deals' => 1])" variant="outline">View deals</flux:button>
            </div>
            <flux:text variant="subtle" size="sm">Free returns on every order for 60 years.</flux:text>
        </div>
        <div class="flex h-[380px] min-w-0 flex-1 items-center justify-center rounded-2xl border border-zinc-200 bg-white text-xs leading-[normal] text-zinc-400">Hero image</div>
    </section>

    {{-- Categories --}}
    <section class="flex flex-col gap-5 px-12 pt-16">
        <div class="flex items-center justify-between">
            <flux:heading size="lg" level="2">Shop by category</flux:heading>
            <flux:button :href="route('products.index')" variant="ghost" size="sm">View all</flux:button>
        </div>
        <div class="grid grid-cols-4 gap-6">
            @foreach ($categories as [$name, $count])
                <a href="{{ route('products.index', ['category' => $name]) }}" class="flex min-w-0 flex-col gap-2.5">
                    <div class="flex h-40 items-center justify-center rounded-xl bg-zinc-100 text-xs leading-[normal] text-zinc-400">{{ $name }}</div>
                    <div class="flex items-center justify-between">
                        <flux:text>{{ $name }}</flux:text>
                        <flux:text variant="subtle" size="sm">{{ $count }} items</flux:text>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Best sellers --}}
    <section class="flex flex-col gap-5 px-12 pt-14">
        <div class="flex items-center justify-between">
            <flux:heading size="lg" level="2">Best sellers</flux:heading>
            <flux:button :href="route('products.index')" variant="ghost" size="sm">See all products</flux:button>
        </div>
        <div class="grid grid-cols-4 gap-6">
            @foreach ($bestSellers as $product)
                <x-shop.product-card :product="$product" />
            @endforeach
        </div>
    </section>

    {{-- Promotion --}}
    <section class="px-12 pb-16 pt-14">
        <div class="flex items-center justify-between rounded-2xl bg-zinc-800 p-10">
            <div class="flex flex-col gap-1.5">
                <flux:heading size="lg" class="text-white!">Save 10% with code BAKE10</flux:heading>
                <flux:text variant="subtle" class="text-zinc-400!">Enter it at checkout. Valid until 31 Oct 2026.</flux:text>
            </div>
            <flux:button :href="route('products.index', ['deals' => 1])" variant="outline">Shop deals</flux:button>
        </div>
    </section>
    
</x-layouts::shop>