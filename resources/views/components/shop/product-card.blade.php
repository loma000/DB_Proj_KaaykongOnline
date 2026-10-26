@props(['product', 'cart' => false])

@php
    $id = data_get($product, 'id');
    $name = data_get($product, 'product_name', data_get($product, 'name'));
    $image = data_get($product, 'image');   
    $imageUrl = $image ? (str_starts_with($image, 'http') || str_starts_with($image, '/') ? $image : asset('storage/'.$image)) : null;
    $rating = data_get($product, 'reviews_avg_score', data_get($product, 'rating', 0));
    $reviewCount = data_get($product, 'reviews_count', data_get($product, 'reviews', 0));
    $price = data_get($product, 'price', 0);
@endphp

<div class="flex min-w-0 flex-col gap-3">
    <a href="{{ route('products.show', $product['id']) }}" class="flex h-60 w-full items-center justify-center overflow-hidden rounded-xl bg-zinc-100">
        @if (! empty($product['image']))
            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="size-full object-cover" />
        @else
            <flux:text size="sm" class="text-xs text-zinc-400">Product image</flux:text>
        @endif
    </a>

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