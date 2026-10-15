<x-layouts::shop title="All products" active="products" :cart-count="2">
    <div class="flex flex-col px-12 pt-10">
        <p class="text-[13px] leading-[normal] text-zinc-500"><a href="{{ route('home') }}">Home</a> / Products</p>
        <h1 class="mt-2 text-[32px] font-semibold leading-[normal] tracking-[-0.32px] text-zinc-800">All products</h1>
        <p class="mt-2 text-sm leading-[normal] text-zinc-500">Browse kitchenware for everyday cooking.</p>
    </div>

    <form method="GET" action="{{ route('products.index') }}" class="flex items-center justify-between px-12 pt-6">
        <div class="flex flex-wrap gap-2">
            <flux:button
                :href="route('products.index', array_filter(['q' => request('q'), 'sort' => request('sort')]))"
                :variant="! $activeCategory ? 'primary' : 'outline'"
                size="sm"
            >All</flux:button>
            @foreach ($categories as $category)
                <flux:button
                    :href="route('products.index', array_filter(['category' => $category, 'q' => request('q'), 'sort' => request('sort')]))"
                    :variant="$activeCategory === $category ? 'primary' : 'outline'"
                    size="sm"
                >{{ $category }}</flux:button>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            @if ($activeCategory)
                <input type="hidden" name="category" value="{{ $activeCategory }}" />
            @endif
            <div class="w-64">
                <flux:input
                    name="q"
                    type="search"
                    icon="magnifying-glass"
                    placeholder="Search products"
                    value="{{ request('q') }}"
                    aria-label="Search products"
                />
            </div>
            <div class="w-52">
                <flux:select name="sort" onchange="this.form.submit()" aria-label="Sort products">
                    @foreach (['featured' => 'Featured', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'rating' => 'Top rated'] as $value => $label)
                        <flux:select.option value="{{ $value }}" :selected="request('sort', 'featured') === $value">
                            Sort: {{ $label }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </form>

    <p class="px-12 pt-6 text-sm text-zinc-500">
        Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
    </p>

    <div class="grid grid-cols-4 gap-x-6 gap-y-8 px-12 pb-10 pt-8">
        @forelse ($products as $product)
            <x-shop.product-card :product="[
                'id'      => $product->id,
                'name'    => $product->product_name,
                'price'   => $product->price,
                'rating'  => round($product->reviews_avg_score ?? 0, 1),
                'reviews' => $product->reviews_count,
                'image'   => $product->image ? asset('storage/'.$product->image) : null,
            ]" cart />
        @empty
            <p class="col-span-full py-12 text-center text-sm text-zinc-500">No products found.</p>
        @endforelse
    </div>

    @if ($products->hasPages())
        <nav class="flex flex-wrap items-center justify-center gap-2 px-12 pb-14" aria-label="Pagination">
            @if ($products->onFirstPage())
                <flux:button variant="outline" size="sm" icon="chevron-left" aria-label="Previous page" disabled />
            @else
                <flux:button :href="$products->previousPageUrl()" variant="outline" size="sm" icon="chevron-left" aria-label="Previous page" />
            @endif

            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                <flux:button
                    :href="$url"
                    :variant="$page === $products->currentPage() ? 'primary' : 'outline'"
                    size="sm"
                    :aria-current="$page === $products->currentPage() ? 'page' : null"
                >{{ $page }}</flux:button>
            @endforeach

            @if ($products->hasMorePages())
                <flux:button :href="$products->nextPageUrl()" variant="outline" size="sm" icon="chevron-right" aria-label="Next page" />
            @else
                <flux:button variant="outline" size="sm" icon="chevron-right" aria-label="Next page" disabled />
            @endif
        </nav>
    @endif
</x-layouts::shop>