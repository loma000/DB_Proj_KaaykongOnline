<x-layouts::shop title="Checkout" :cart-count="$count">
    <div class="mx-auto max-w-6xl px-6 py-8 sm:px-12">
        <div class="mb-6">
            <flux:heading size="xl">Checkout</flux:heading>
            <p class="mt-1 text-sm text-zinc-500">Review your items and confirm your order.</p>
        </div>

        <form method="POST" action="{{ route('checkout.place') }}"
            onsubmit="return confirm('ยืนยันการสั่งซื้อหรือไม่?')">
            @csrf
            <input type="hidden" name="discount_id" value="{{ $coupon?->id }}">
            <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
                <div class="space-y-7">
                    <section>
                        <flux:heading size="lg">Shipping address</flux:heading>
                        <label for="shipping_address" class="mt-4 block text-sm font-medium text-zinc-700">Address</label>
                        <textarea id="shipping_address" rows="3"
                            class="mt-2 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-500 focus:outline-none focus:ring-1 focus:ring-zinc-500"
                            placeholder="Enter your delivery address"></textarea>
                    </section>

                    <fieldset x-data="{ paymentMethod: 'card' }">
                        <legend class="text-lg font-semibold text-zinc-800">Payment method</legend>
                        <div class="mt-4 space-y-2">
                            <button type="button" role="radio" class="flex w-full items-start gap-3 rounded-lg border border-zinc-300 p-4 text-left"
                                :aria-checked="paymentMethod === 'card'"
                                :class="paymentMethod === 'card' ? 'border-zinc-900' : ''"
                                @click="paymentMethod = 'card'">
                                <span class="mt-1 flex size-4 shrink-0 items-center justify-center rounded-full border"
                                    :class="paymentMethod === 'card' ? 'border-zinc-900' : 'border-zinc-300'">
                                    <span x-show="paymentMethod === 'card'" class="size-2 rounded-full bg-zinc-900"></span>
                                </span>
                                <span>
                                    <span class="block text-sm font-medium">Credit / debit card</span>
                                    <span class="mt-1 block text-xs text-zinc-500">Visa, Mastercard, JCB</span>
                                </span>
                            </button>

                            <button type="button" role="radio" class="flex w-full items-start gap-3 rounded-lg border border-zinc-300 p-4 text-left"
                                :aria-checked="paymentMethod === 'promptpay'"
                                :class="paymentMethod === 'promptpay' ? 'border-zinc-900' : ''"
                                @click="paymentMethod = 'promptpay'">
                                <span class="mt-1 flex size-4 shrink-0 items-center justify-center rounded-full border"
                                    :class="paymentMethod === 'promptpay' ? 'border-zinc-900' : 'border-zinc-300'">
                                    <span x-show="paymentMethod === 'promptpay'" class="size-2 rounded-full bg-zinc-900"></span>
                                </span>
                                <span>
                                    <span class="block text-sm font-medium">PromptPay QR</span>
                                    <span class="mt-1 block text-xs text-zinc-500">Scan to pay with your banking app</span>
                                </span>
                            </button>

                            <button type="button" role="radio" class="flex w-full items-start gap-3 rounded-lg border border-zinc-300 p-4 text-left"
                                :aria-checked="paymentMethod === 'cash_on_delivery'"
                                :class="paymentMethod === 'cash_on_delivery' ? 'border-zinc-900' : ''"
                                @click="paymentMethod = 'cash_on_delivery'">
                                <span class="mt-1 flex size-4 shrink-0 items-center justify-center rounded-full border"
                                    :class="paymentMethod === 'cash_on_delivery' ? 'border-zinc-900' : 'border-zinc-300'">
                                    <span x-show="paymentMethod === 'cash_on_delivery'" class="size-2 rounded-full bg-zinc-900"></span>
                                </span>
                                <span>
                                    <span class="block text-sm font-medium">Cash on delivery</span>
                                    <span class="mt-1 block text-xs text-zinc-500">Pay when your order arrives</span>
                                </span>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-zinc-500">Payment options are for display only and are not processed.</p>
                    </fieldset>
                </div>

                <aside class="h-fit rounded-xl border border-zinc-200 p-5">
                    <flux:heading>Order summary</flux:heading>
                    <div class="mt-4 max-h-72 space-y-4 overflow-y-auto">
                        @foreach ($products as $product)
                            @php
                                $image = $product->image;
                                $imageUrl = $image
                                    ? (str_starts_with($image, 'http') || str_starts_with($image, '/')
                                        ? $image
                                        : asset('storage/' . $image))
                                    : null;
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-md bg-zinc-100 text-xs text-zinc-400">
                                    @if ($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}"
                                            class="size-full object-cover">
                                    @else
                                        Img
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">{{ $product->product_name }}</p>
                                    <p class="text-xs text-zinc-500">Qty {{ $product->pivot->quantity }}</p>
                                </div>
                                <p class="shrink-0 text-sm font-medium">
                                    ฿{{ number_format($product->price * $product->pivot->quantity) }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 space-y-3 border-t border-zinc-200 pt-4 text-sm">
                        <div class="flex justify-between text-zinc-500">
                            <span>Subtotal</span>
                            <span class="font-medium text-zinc-800">฿{{ number_format($subtotal) }}</span>
                        </div>
                        @if ($coupon)
                            <div class="flex justify-between text-zinc-500">
                                <span>Coupon {{ $coupon->coupon_name }}</span>
                                <span class="font-medium text-green-700">−฿{{ number_format($discount) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between border-b border-zinc-200 pb-3 text-zinc-500">
                            <span>Shipping</span>
                            <span class="font-medium text-zinc-800">{{ $shipping ? '฿' . number_format($shipping) : 'Free' }}</span>
                        </div>
                        <div class="flex justify-between text-base font-semibold">
                            <span>Total</span>
                            <span>฿{{ number_format($total) }}</span>
                        </div>
                    </div>

                    <flux:button type="submit" variant="primary" class="mt-5 w-full !bg-zinc-900">
                        Place order
                    </flux:button>
                    <p class="mt-3 text-xs text-zinc-500">
                        By placing your order, you agree to our Terms and Privacy Policy.
                    </p>
                </aside>
            </div>
        </form>
    </div>
</x-layouts::shop>
