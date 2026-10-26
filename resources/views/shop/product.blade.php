@php
    $rating = round((float) ($product->reviews_avg_score ?? 0), 1);
    $reviewCount = (int) ($product->reviews_count ?? $reviews->count());
    $category = $product->categories->first()?->category_name;
    $reviews->loadMissing('user');
    $ratingBreakdown = $reviews->countBy(fn($review) => (int) $review->score);

    $stockCount = $stock?->quantity;
    $imageUrls = $product->productImages
        ->pluck('image')
        ->filter()
        ->map(fn (string $image): string => str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')
            ? $image
            : asset('storage/' . $image))
        ->values();
    $imageUrl = $imageUrls->first();
    $thumbs = $imageUrls->isNotEmpty() ? $imageUrls : collect([null, null, null, null]);

    $reviewAction = Route::has('products.reviews.store') ? route('products.reviews.store', $product) : '#';
@endphp

<x-layouts::shop :title="$product->product_name" active="products" :member="auth()->check()">
    <div class="flex flex-col gap-8 px-12 pb-16 pt-8">
        {{-- Breadcrumb --}}
        <p class="text-[13px] leading-[normal] text-zinc-500">
            <a href="{{ route('home') }}">Home</a> /
            <a href="{{ route('products.index') }}">Products</a> /
            @if ($category)
                <a href="{{ route('products.index', ['category' => $category]) }}">{{ $category }}</a> /
            @endif
            {{ $product->product_name }}
        </p>

        {{-- Top --}}
        <section class="flex items-start gap-12">
            {{-- Gallery --}}
            <div class="flex min-w-0 flex-1 flex-col gap-3">
                <div
                    class="flex h-[460px] w-full items-center justify-center overflow-hidden rounded-2xl bg-zinc-100 text-xs text-zinc-400">
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}"
                            class="size-full object-cover" />
                    @else
                        Product image
                    @endif
                </div>
                <div class="flex gap-3" aria-label="Product images">
                    @foreach ($thumbs as $thumb)
                        <div
                            class="flex size-20 items-center justify-center overflow-hidden rounded-lg bg-zinc-100 text-[11px] text-zinc-400">
                            @if ($thumb)
                                <img src="{{ $thumb }}" alt="" class="size-full object-cover" />
                            @else
                                Img
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Info --}}
            <div class="flex min-w-0 flex-1 flex-col items-start gap-4" x-data="{ quantity: 1, max: {{ max((int) $stockCount, 1) }} }">
                @if ($category)
                    <flux:badge as="a" :href="route('products.index', ['category' => $category])" pill
                        color="zinc" variant="outline" class="px-3! py-1! text-[13px]! font-normal!">
                        {{ $category }}</flux:badge>
                @endif

                <h1 class="text-[32px] font-semibold leading-[normal] tracking-[-0.32px] text-zinc-800">
                    {{ $product->product_name }}</h1>

                <a href="#reviews" class="flex items-center gap-2"
                    aria-label="{{ number_format($rating, 1) }} out of 5, {{ $reviewCount }} reviews">
                    <span class="flex items-center gap-0.5" aria-hidden="true">
                        @for ($star = 1; $star <= 5; $star++)
                            <flux:icon.star variant="solid" @class([
                                'size-4',
                                'text-zinc-800' => $star <= round($rating),
                                'text-zinc-200' => $star > round($rating),
                            ]) />
                        @endfor
                    </span>
                    <span
                        class="text-sm font-medium leading-[normal] text-zinc-800">{{ number_format($rating, 1) }}</span>
                    <span class="text-sm leading-[normal] text-zinc-500">· {{ $reviewCount }}
                        {{ Str::plural('review', $reviewCount) }}</span>
                </a>

                <p class="text-[28px] font-semibold leading-[normal] tracking-[-0.28px] text-zinc-800">
                    ฿{{ number_format((float) $product->price) }}</p>

                @if ($product->description)
                    <p class="text-sm leading-[22px] text-zinc-500">{{ $product->description }}</p>
                @endif

                @if ($stockCount !== null)
                    @if ($stockCount > 0)
                        <p class="text-sm font-medium leading-[normal] text-[#16803c]">In stock · {{ $stockCount }}
                            left</p>
                    @else
                        <p class="text-sm font-medium leading-[normal] text-[#cf1e1e]">Out of stock</p>
                    @endif
                @endif
                <form method="POST" action="{{ route('cart.create', $product) }}">
                    @csrf
                    <input type="hidden" name="quantity" x-bind:value="quantity">
                    <div class="flex w-full items-start gap-3">
                        <div
                            class="flex h-11 w-28 shrink-0 items-center justify-between rounded-lg border border-zinc-200 px-4 text-sm">
                            <button type="button" aria-label="Decrease quantity"
                                class="text-zinc-500 hover:text-zinc-800"
                                @click="quantity = Math.max(1, quantity - 1)">−</button>
                            <span class="font-medium tabular-nums text-zinc-800" x-text="quantity">1</span>
                            <button type="button" aria-label="Increase quantity"
                                class="text-zinc-500 hover:text-zinc-800"
                                @click="quantity = Math.min(max, quantity + 1)">+</button>
                        </div>

                        <flux:button type="submit" variant="primary"
                            class="h-11! min-w-0 flex-1 border-0! bg-zinc-800! text-white! hover:bg-zinc-700!"
                            :disabled="$stockCount !== null && $stockCount < 1">Add to cart</flux:button>
                    </div>
                    @error('quantity')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </form>
            </div>
    </div>
    </section>

    {{-- Reviews --}}
    <section id="reviews" class="flex flex-col gap-6 pt-6">
        <h2 class="text-2xl font-semibold leading-[normal] tracking-[-0.24px] text-zinc-800">Customer reviews</h2>

        <div class="flex items-start gap-12">
            {{-- Summary --}}
            <div class="flex w-80 shrink-0 flex-col gap-3">
                <p class="text-5xl font-semibold leading-[normal] tracking-[-0.48px] text-zinc-800">
                    {{ number_format($rating, 1) }}</p>
                <div class="flex items-center gap-0.5" aria-label="Rated {{ number_format($rating, 1) }} out of 5">
                    @for ($star = 1; $star <= 5; $star++)
                        <flux:icon.star variant="solid" @class([
                            'size-[18px]',
                            'text-zinc-800' => $star <= round($rating),
                            'text-zinc-200' => $star > round($rating),
                        ]) />
                    @endfor
                </div>
                <p class="text-[13px] leading-[normal] text-zinc-500">Based on {{ $reviewCount }}
                    {{ Str::plural('review', $reviewCount) }}</p>

                <div class="flex w-full flex-col gap-2 pt-2">
                    @for ($score = 5; $score >= 1; $score--)
                        @php
                            $scoreCount = $ratingBreakdown->get($score, 0);
                            $scorePercent =
                                $reviewCount > 0 && $scoreCount > 0 ? max(1, ($scoreCount / $reviewCount) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-2.5 text-[13px] leading-[normal] text-zinc-500">
                            <span class="shrink-0">{{ $score }}★</span>
                            <div class="h-2 min-w-0 flex-1 overflow-hidden rounded-full bg-zinc-100">
                                <div class="h-2 rounded-full bg-zinc-800" style="width: {{ $scorePercent }}%"></div>
                            </div>
                            <span class="shrink-0 tabular-nums">{{ $scoreCount }}</span>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="flex min-w-0 flex-1 flex-col gap-6">
                {{-- Write a review --}}
                @auth
                    <form method="POST" action="{{ route('review.create', $product) }}" x-data="{ score: 4, hover: 0 }"
                        class="flex flex-col items-start gap-3.5 rounded-xl border border-zinc-200 p-5">
                        @csrf
                        <p class="text-base font-semibold leading-[normal] text-zinc-800">Write a review</p>

                        <div class="flex items-center gap-2.5">
                            <span class="text-sm leading-[normal] text-zinc-500">Your score</span>
                            <div class="flex items-center gap-0.5" @mouseleave="hover = 0">
                                @for ($star = 1; $star <= 5; $star++)
                                    <button type="button" aria-label="Rate {{ $star }} out of 5"
                                        @click="score = {{ $star }}" @mouseenter="hover = {{ $star }}">
                                        <flux:icon.star variant="solid" class="size-5"
                                            ::class="(hover || score) >= {{ $star }} ? 'text-zinc-800' : 'text-zinc-200'" />
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="score" x-bind:value="score" />
                        </div>

                        <flux:textarea name="comment" rows="3" resize="none" class="w-full"
                            placeholder="Share what you think about this product" aria-label="Review comment">
                            {{ old('comment') }}</flux:textarea>
                        <flux:error name="comment" />

                        <flux:button type="submit" variant="primary"
                            class="h-10! border-0! bg-zinc-800! text-white! hover:bg-zinc-700!">Post review</flux:button>
                    </form>
                @else
                    <div class="flex items-center justify-between rounded-xl border border-zinc-200 p-5">
                        <p class="text-sm text-zinc-500">Sign in to write a review.</p>
                        <flux:button :href="route('login')" variant="outline" class="h-9!">Sign in</flux:button>
                    </div>
                @endauth

                {{-- Review list --}}
                <div class="flex flex-col">
                    @forelse ($reviews->sortByDesc('created_at') as $review)
                        <article class="flex flex-col gap-1.5 border-b border-zinc-200 py-[18px]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span
                                        class="text-sm font-medium leading-[normal] text-zinc-800">{{ $review->user?->name ?? 'Customer' }}</span>
                                    <span class="flex items-center gap-0.5"
                                        aria-label="Rated {{ $review->score }} out of 5">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <flux:icon.star variant="solid" @class([
                                                'size-3.5',
                                                'text-zinc-800' => $star <= $review->score,
                                                'text-zinc-200' => $star > $review->score,
                                            ]) />
                                        @endfor
                                    </span>
                                </div>
                                <span
                                    class="text-[13px] leading-[normal] text-zinc-500">{{ $review->updated_at?->format('d M Y · H:i') }}</span>
                            </div>
                            @if ($review->comment)
                                <p class="text-sm leading-[22px] text-zinc-800">{{ $review->comment }}</p>
                            @endif
                        </article>
                    @empty
                        <p class="py-[18px] text-sm text-zinc-500">No reviews yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
    </div>
</x-layouts::shop>
