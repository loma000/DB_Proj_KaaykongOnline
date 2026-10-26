<x-layouts::shop title="Your cart" :cart-count="$count">
    <div class="mx-auto max-w-6xl px-12 py-8">
        <div class="flex items-baseline gap-3">
            <flux:heading size="xl">Your cart</flux:heading>
            <span class="text-sm text-zinc-500">{{ $count }} {{ Str::plural('item', $count) }}</span>
        </div>

        <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_380px]">
            {{-- Items --}}
            <div>
                @error('quantity')
                    <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @forelse ($products as $product)
                    @php
                        $qty = $product->pivot->quantity;
                        $image = $product->image;
                        $imageUrl = $image
                            ? (str_starts_with($image, 'http') || str_starts_with($image, '/')
                                ? $image
                                : asset('storage/' . $image))
                            : null;
                    @endphp
                    <div class="flex items-center gap-4 border-b border-zinc-200 py-5">
                        <a href="{{ route('products.show', $product) }}"
                            class="flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-md bg-zinc-100 text-[10px] text-zinc-400">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}"
                                    class="size-full object-cover">
                            @else
                                img
                            @endif
                        </a>

                        <div class="min-w-0 flex-1">
                            <a href="{{ route('products.show', $product) }}"
                                class="text-sm font-medium">{{ $product->product_name }}</a>
                            <div class="truncate text-xs text-zinc-500">{{ $product->description }}</div>
                            <form method="POST" action="{{ route('cart.remove', $product) }}">
                                @csrf @method('DELETE')
                                <button class="text-xs underline">Remove</button>
                            </form>
                        </div>

                        {{-- Quantity --}}
                        <form method="POST" action="{{ route('cart.update', $product) }}"
                            class="flex items-center rounded-lg border border-zinc-200 text-xs">
                            @csrf @method('PATCH')
                            <button name="quantity" value="{{ $qty - 1 }}" class="px-3 py-2 text-zinc-500"
                                aria-label="Decrease">−</button>
                            <span class="w-6 text-center">{{ $qty }}</span>
                            <button name="quantity" value="{{ $qty + 1 }}" class="px-3 py-2 text-zinc-500"
                                aria-label="Increase">+</button>
                        </form>

                        <div class="w-24 text-right text-sm font-semibold">฿{{ number_format($product->price * $qty) }}
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-zinc-500">Your cart is empty.</p>
                @endforelse

                <a href="{{ route('products.index') }}" class="mt-4 inline-block text-sm">← Continue shopping</a>
            </div>

            {{-- Order summary --}}
            <div class="h-fit rounded-xl border border-zinc-200 p-5">
                <flux:heading>Order summary</flux:heading>

                <form method="POST" action="{{ route('cart.discount', $coupon ?? 0) }}" x-data="{ discountId: '{{ old('discount_id', $coupon?->id ?? 'none') }}' }"
                    x-bind:action="discountId === 'none' ? '{{ route('cart.discount.clear') }}' :
                        '{{ url('/cart/discount') }}/' + discountId"
                    class="mt-4 flex gap-2">
                    @csrf
                    <select name="discount_id" x-model="discountId"
                        class="min-w-0 flex-1 rounded-lg border border-zinc-200 px-3 text-sm">
                        <option value="none">None</option>
                        @foreach ($discounts as $availableCoupon)
                            <option value="{{ $availableCoupon->id }}" @selected((string) old('discount_id', $coupon?->id) === (string) $availableCoupon->id)>
                                {{ $availableCoupon->coupon_name }} ({{ $availableCoupon->discount_rate }}% off)
                            </option>
                        @endforeach
                    </select>
                    <flux:button type="submit" size="sm">Apply</flux:button>
                </form>
                @error('discount_id')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @if ($coupon)
                    <span class="mt-3 inline-block rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600">
                        {{ $coupon->coupon_name }} applied · {{ $coupon->discount_rate }}% off your order
                    </span>
                @endif

                <div class="mt-4 space-y-3 border-t border-zinc-200 pt-4 text-sm">
                    <div class="flex justify-between text-zinc-500">
                        <span>Subtotal ({{ $count }} items)</span>
                        <span class="font-medium text-black">฿{{ number_format($subtotal) }}</span>
                    </div>
                    @if ($coupon)
                        <div class="flex justify-between text-zinc-500">
                            <span>Coupon {{ $coupon->coupon_name }}</span>
                            <span class="font-medium text-green-700">−฿{{ number_format($discount) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between border-b border-zinc-200 pb-3 text-zinc-500">
                        <span>Shipping</span>
                        <span
                            class="font-medium text-black">{{ $shipping ? '฿' . number_format($shipping) : 'Free' }}</span>
                    </div>
                    <div class="flex justify-between text-base font-semibold">
                        <span>Total</span>
                        <span>฿{{ number_format($total) }}</span>
                    </div>
                </div>
                <flux:button :href="route('checkout')" variant="primary" class="mt-5 w-full !bg-zinc-900"
                    :disabled="$products->isEmpty()">
                    Proceed to checkout
                </flux:button>

            </div>
        </div>
    </div>
</x-layouts::shop>
