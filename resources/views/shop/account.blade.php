<x-layouts::shop title="My account">
    <div class="mx-auto grid w-full max-w-[1440px] grid-cols-1 gap-8 px-6 py-6 sm:px-8 lg:grid-cols-[154px_minmax(0,1fr)] lg:py-8">
        <aside aria-label="Account navigation" class="lg:pt-1">
            <nav class="flex gap-2 overflow-x-auto lg:flex-col">
                <a href="#profile" class="shrink-0 rounded-md bg-zinc-100 px-3 py-2 text-sm font-medium text-zinc-800">Profile</a>
                <a href="#orders" class="shrink-0 rounded-md px-3 py-2 text-sm text-zinc-500 hover:bg-zinc-100 hover:text-zinc-800">Orders</a>
                <a href="#reviews" class="shrink-0 rounded-md px-3 py-2 text-sm text-zinc-500 hover:bg-zinc-100 hover:text-zinc-800">My reviews</a>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="rounded-md px-3 py-2 text-left text-sm text-zinc-700 hover:bg-zinc-100">Sign out</button>
                </form>
            </nav>
        </aside>

        <div class="min-w-0">
            <header id="profile" class="mb-4">
                <h1 class="text-2xl font-semibold leading-tight text-zinc-800">My profile</h1>
                <p class="mt-1 text-sm text-zinc-500">Manage your account, orders and reviews.</p>
            </header>

            <section class="mb-4 flex items-center gap-3 rounded-lg border border-zinc-200 bg-white p-4 sm:gap-4">
                <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-base font-medium text-zinc-700" aria-hidden="true">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="truncate text-sm font-semibold text-zinc-800">{{ $user->name }}</h2>
                    <p class="truncate text-sm text-zinc-500">{{ $user->email }}</p>
                </div>
                <flux:button :href="route('profile.edit')" variant="outline" size="sm" class="shrink-0">Edit profile</flux:button>
            </section>

            <section class="mb-4 rounded-lg border border-zinc-200 bg-white p-4" aria-labelledby="account-details-heading">
                <div class="mb-3 flex items-center justify-between">
                    <h2 id="account-details-heading" class="text-sm font-semibold text-zinc-800">Account details</h2>
                    <a href="{{ route('profile.edit') }}" class="text-sm text-zinc-600 underline underline-offset-2 hover:text-zinc-900">Edit</a>
                </div>
                <dl class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <dt class="mb-1 text-xs font-medium text-zinc-700">Name</dt>
                        <dd class="rounded-md border border-zinc-200 px-3 py-2 text-sm text-zinc-600">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="mb-1 text-xs font-medium text-zinc-700">Email</dt>
                        <dd class="break-all rounded-md border border-zinc-200 px-3 py-2 text-sm text-zinc-600">{{ $user->email }}</dd>
                    </div>
                </dl>
            </section>

            <section id="orders" class="mb-4 rounded-lg border border-zinc-200 bg-white p-4" aria-labelledby="recent-orders-heading">
                <h2 id="recent-orders-heading" class="mb-3 text-sm font-semibold text-zinc-800">Recent orders</h2>
                @if ($orders->isEmpty())
                    <p class="py-5 text-center text-sm text-zinc-500">You have no orders yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] text-left text-xs">
                            <thead class="text-zinc-500">
                                <tr class="border-b border-zinc-200">
                                    <th scope="col" class="pb-2 pr-3 font-medium">Order</th>
                                    <th scope="col" class="pb-2 pr-3 font-medium">Date</th>
                                    <th scope="col" class="pb-2 pr-3 font-medium">Time</th>
                                    <th scope="col" class="pb-2 pr-3 font-medium">Items</th>
                                    <th scope="col" class="pb-2 pr-3 font-medium">Coupon</th>
                                    <th scope="col" class="pb-2 text-right font-medium">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-zinc-800">
                                @foreach ($orders as $order)
                                    <tr>
                                        <td class="py-2.5 pr-3">#{{ $order->id }}</td>
                                        <td class="whitespace-nowrap py-2.5 pr-3">{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="whitespace-nowrap py-2.5 pr-3">{{ $order->created_at->format('H:i') }}</td>
                                        <td class="max-w-64 py-2.5 pr-3">
                                            {{ $order->products->map(fn ($product) => $product->product_name.' × '.$product->pivot->quantity)->join(', ') ?: '—' }}
                                        </td>
                                        <td class="py-2.5 pr-3">{{ $order->discount?->coupon_name ?? '—' }}</td>
                                        <td class="whitespace-nowrap py-2.5 text-right">฿{{ number_format((float) $order->total_price, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section id="reviews" class="rounded-lg border border-zinc-200 bg-white p-4" aria-labelledby="my-reviews-heading">
                <h2 id="my-reviews-heading" class="mb-3 text-sm font-semibold text-zinc-800">My reviews</h2>
                @if ($reviews->isEmpty())
                    <p class="py-5 text-center text-sm text-zinc-500">You have not written any reviews yet.</p>
                @else
                    <div class="divide-y divide-zinc-200">
                        @foreach ($reviews as $review)
                            <article class="py-3 first:pt-0 last:pb-0">
                                <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-1">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-xs font-semibold text-zinc-800">{{ $user->name }}</h3>
                                            <span class="flex items-center" aria-label="{{ $review->score }} out of 5 stars">
                                                @for ($star = 1; $star <= 5; $star++)
                                                    <flux:icon name="star" variant="solid" class="size-3 {{ $star <= $review->score ? 'text-zinc-800' : 'text-zinc-300' }}" />
                                                @endfor
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-zinc-500">{{ $review->product?->product_name ?? 'Product no longer available' }}</p>
                                    </div>
                                    <time class="text-xs text-zinc-500" datetime="{{ $review->created_at->toIso8601String() }}">{{ $review->created_at->format('d M Y · H:i') }}</time>
                                </div>
                                @if ($review->comment)
                                    <p class="mt-1 text-sm text-zinc-700">{{ $review->comment }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-layouts::shop>