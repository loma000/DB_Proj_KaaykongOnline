@props(['product', 'cart' => false])

@php
    $id = data_get($product, 'id');
    $name = data_get($product, 'product_name', data_get($product, 'name'));
    $image = data_get($product, 'image');   
    $imageUrl = $image ? (str_starts_with($image, 'http') || str_starts_with($image, '/') ? $image : asset('storage/'.$image)) : null;
    $images = collect(data_get($product, 'images', []))
        ->filter()
        ->map(fn ($image) => str_starts_with($image, 'http') || str_starts_with($image, '/') ? $image : asset('storage/'.$image))
        ->values()
        ->all();
    if (empty($images) && $imageUrl) {
        $images = [$imageUrl];
    }
    $rating = data_get($product, 'reviews_avg_score', data_get($product, 'rating', 0));
    $reviewCount = data_get($product, 'reviews_count', data_get($product, 'reviews', 0));
    $price = data_get($product, 'price', 0);
@endphp

<div class="flex min-w-0 flex-col gap-3">
    <div x-data="{ activeImage: 0, images: @js($images) }" class="relative h-60 w-full overflow-hidden rounded-xl bg-zinc-100">
        <a href="{{ route('products.show', $product['id']) }}" class="flex size-full items-center justify-center">
            @if (! empty($images))
                <img x-bind:src="images[activeImage]" alt="{{ $product['name'] }}" class="size-full object-cover" />
            @else
                <flux:text size="sm" class="text-xs text-zinc-400">Product image</flux:text>
            @endif
        </a>

        @if (count($images) > 1)
            <button type="button" @click.stop.prevent="activeImage = (activeImage - 1 + images.length) % images.length" aria-label="Previous product image" class="absolute top-1/2 z-10 flex size-10 sm:size-11 -translate-y-1/2 items-center justify-center rounded-full border border-black bg-white text-black shadow-md hover:bg-zinc-100" style="left: 0.5rem;">
                <span aria-hidden="true" class="text-2xl leading-none">&larr;</span>
            </button>
            <button type="button" @click.stop.prevent="activeImage = (activeImage + 1) % images.length" aria-label="Next product image" class="absolute top-1/2 z-10 flex size-10 sm:size-11 -translate-y-1/2 items-center justify-center rounded-full border border-black bg-white text-black shadow-md hover:bg-zinc-100" style="right: 0.5rem;">
                <span aria-hidden="true" class="text-2xl leading-none">&rarr;</span>
            </button>
            <div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 items-center gap-1.5">
                @foreach ($images as $index => $image)
                    <button type="button" @click.stop.prevent="activeImage = {{ $index }}" aria-label="Show product image {{ $index + 1 }}" class="h-1.5 rounded-full transition-all" :class="activeImage === {{ $index }} ? 'w-5 bg-white' : 'w-1.5 bg-white/60'"></button>
                @endforeach
            </div>
        @endif
    </div>

    <flux:link :href="route('products.show', $product['id'])" variant="ghost" class="text-sm font-medium">{{ $product['name'] }}</flux:link>

    <div class="flex items-center gap-1">
        <flux:icon.star variant="solid" class="size-3.5 text-zinc-800" />
        <flux:text size="sm" class="font-medium text-zinc-800">{{ number_format($product['rating'], 1) }}</flux:text>
        <flux:text size="sm">({{ $product['reviews'] }})</flux:text>
    </div>

    <flux:heading size="lg" class="text-[15px]!">฿{{ number_format($product['price']) }}</flux:heading>

    @if ($cart)
         <form method="POST" action="{{ route('cart.create', $id) }}">
        @csrf
        <input type="hidden" name="quantity" value="1">
        <flux:button type="submit" variant="outline" class="h-9! w-full">Add to cart</flux:button>
    </form>
    @endif
</div>