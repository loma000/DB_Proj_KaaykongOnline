<x-layouts::shop title="Order confirmation">
    @php
        $subtotal = $order->products->sum(fn ($product) => $product->pivot->price * $product->pivot->quantity);
        $discountAmount = max(0, $subtotal - (float) $order->total_price);
    @endphp

    <div class="mx-auto max-w-5xl px-6 py-10 sm:px-8">
        <section class="mx-auto max-w-2xl text-center" aria-labelledby="order-confirmation-heading">
            <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-green-50 text-green-700">
                <flux:icon name="check" class="size-7" />
            </div>
            <p class="mt-5 text-sm font-medium text-green-700">Order placed successfully</p>
            <h1 id="order-confirmation-heading" class="mt-2 text-2xl font-semibold text-zinc-900">
                Thank you for your order!
            </h1>
            <p class="mt-2 text-sm text-zinc-500">
                Your order <span class="font-medium text-zinc-800">#{{ $order->id }}</span> has been received.
            </p>
        </section>

        <div class="mt-9 grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <section class="rounded-xl border border-zinc-200 bg-white p-5 sm:p-6" aria-labelledby="items-heading">
                <div class="flex flex-wrap items-start justify-between gap-2 border-b border-zinc-200 pb-4">
                    <div>
                        <h2 id="items-heading" class="text-base font-semibold text-zinc-900">Order details</h2>
                        <p class="mt-1 text-sm text-zinc-500">
                            Placed {{ $order->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700">Confirmed</span>
                </div>

                <div class="divide-y divide-zinc-100">
                    @foreach ($order->products as $product)
                        @php
                            $image = $product->image;
                            $imageUrl = $image
                                ? (str_starts_with($image, 'http') || str_starts_with($image, '/')
                                    ? $image
                                    : asset('storage/' . $image))
                                : null;
                            $lineTotal = $product->pivot->price * $product->pivot->quantity;
                        @endphp
                        <article class="flex items-center gap-4 py-4">
                            <div class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-zinc-100 text-xs text-zinc-400">
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}" class="size-10 object-cover">
                                @else
                                    No image
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="truncate text-sm font-medium text-zinc-900">{{ $product->product_name }}</h3>
                                <p class="mt-1 text-sm text-zinc-500">
                                    ฿{{ number_format((float) $product->pivot->price, 2) }} × {{ $product->pivot->quantity }}
                                </p>
                            </div>
                            <p class="shrink-0 text-sm font-medium text-zinc-900">฿{{ number_format($lineTotal, 2) }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <aside class="h-fit rounded-xl border border-zinc-200 bg-white p-5 sm:p-6" aria-labelledby="total-heading">
                <h2 id="total-heading" class="text-base font-semibold text-zinc-900">Payment summary</h2>
                <dl class="mt-4 space-y-3 border-b border-zinc-200 pb-4 text-sm">
                    <div class="flex justify-between gap-4 text-zinc-500">
                        <dt>Subtotal</dt>
                        <dd class="font-medium text-zinc-800">฿{{ number_format($subtotal, 2) }}</dd>
                    </div>
                    @if ($order->discount_id)
                        <div class="flex justify-between gap-4 text-zinc-500">
                            <dt>Coupon{{ $order->discount ? ' · ' . $order->discount->coupon_name : '' }}</dt>
                            <dd class="font-medium text-green-700">−฿{{ number_format($discountAmount, 2) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-4 text-zinc-500">
                        <dt>Shipping</dt>
                        <dd class="font-medium text-zinc-800">Free</dd>
                    </div>
                </dl>
                <div class="flex justify-between gap-4 pt-4 text-base font-semibold text-zinc-900">
                    <span>Total paid</span>
                    <span>฿{{ number_format((float) $order->total_price, 2) }}</span>
                </div>

                <div class="mt-6 grid gap-2">
                    <flux:button :href="route('account')" variant="primary" class="w-full !bg-zinc-900">
                        View my account
                    </flux:button>
                    <flux:button :href="route('products.index')" variant="outline" class="w-full">
                        Continue shopping
                    </flux:button>
                </div>
            </aside>
        </div>
    </div>
</x-layouts::shop>