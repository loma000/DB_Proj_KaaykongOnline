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
        {{-- Flash status notification --}}
        @if (session('status'))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; font-size: 13px;">
                {{ session('status') }}
            </div>
        @endif
        {{-- Top Bar: Breadcrumb (Left) & Admin Edit Button (Top Right) --}}
        <div class="flex items-center justify-between gap-4">
            <p class="text-[13px] leading-[normal] text-zinc-500">
                <a href="{{ route('home') }}">Home</a> /
                <a href="{{ route('products.index') }}">Products</a> /
                @if ($category)
                    <a href="{{ route('products.index', ['category' => $category]) }}">{{ $category }}</a> /
                @endif
                {{ $product->product_name }}
            </p>
            @if (auth()->check() && auth()->user()->role === 'ADMIN')
                <button type="button" 
                        onclick="openEditModal()" 
                        class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-5 py-1.5 text-xs font-semibold text-zinc-800 shadow-sm transition hover:bg-zinc-50 hover:border-zinc-300">
                    <svg class="size-3.5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Edit product
                </button>
            @endif
        </div>

        {{-- Top --}}
        <section class="flex items-start gap-12">
            {{-- Gallery --}}
            <div class="flex min-w-0 flex-1 flex-col gap-3" x-data="{ activeImage: 0, images: @js($imageUrls->all()) }">
                <div
                    class="relative flex h-[460px] w-full items-center justify-center overflow-hidden rounded-2xl bg-zinc-100 text-xs text-zinc-400">
                    @if ($imageUrl)
                        <img x-bind:src="images[activeImage]" alt="{{ $product->product_name }}"
                            class="size-full object-cover" />
                    @else
                        Product image
                    @endif

                    @if ($imageUrls->count() > 1)
                        <button type="button" @click="activeImage = (activeImage - 1 + images.length) % images.length"
                            aria-label="Previous product image"
                            class="absolute top-1/2 z-10 flex size-10 sm:size-11 -translate-y-1/2 items-center justify-center rounded-full border border-black bg-white text-black shadow-md hover:bg-zinc-100"
                            style="left: 0.5rem;">
                            <span aria-hidden="true" class="text-2xl leading-none">&larr;</span>
                        </button>
                        <button type="button" @click="activeImage = (activeImage + 1) % images.length"
                            aria-label="Next product image"
                            class="absolute top-1/2 z-10 flex size-10 sm:size-11 -translate-y-1/2 items-center justify-center rounded-full border border-black bg-white text-black shadow-md hover:bg-zinc-100"
                            style="right: 0.5rem;">
                            <span aria-hidden="true" class="text-2xl leading-none">&rarr;</span>
                        </button>
                    @endif
                </div>
                <div class="flex gap-3" aria-label="Product images">
                    @foreach ($thumbs as $index => $thumb)
                        @if ($thumb)
                            <button type="button" @click="activeImage = {{ $index }}"
                                aria-label="Show product image {{ $index + 1 }}"
                                :aria-pressed="activeImage === {{ $index }}"
                                class="size-20 overflow-hidden rounded-lg bg-zinc-100"
                                :class="activeImage === {{ $index }} ? 'ring-2 ring-zinc-800' : 'border border-zinc-200'">
                                <img src="{{ $thumb }}" alt="" class="size-full object-cover" />
                            </button>
                        @else
                            <div
                                class="flex size-20 items-center justify-center overflow-hidden rounded-lg bg-zinc-100 text-[11px] text-zinc-400">
                                Img
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Info --}}
            <div class="flex min-w-0 flex-1 flex-col items-start gap-4" x-data="{ quantity: 1, max: {{ max((int) $stockCount, 1) }} }">
                @if ($product->categories->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($product->categories as $cat)
                            <flux:badge as="a" :href="route('products.index', ['category' => $cat->category_name])" pill
                                color="zinc" variant="outline" class="px-3! py-1! text-[13px]! font-normal!">
                                {{ $cat->category_name }}
                            </flux:badge>
                        @endforeach
                    </div>
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

    @if (auth()->check() && auth()->user()->role === 'ADMIN')
        {{-- EDIT PRODUCT MODAL POPUP --}}
        <div id="editProductModal" class="modal-overlay" style="display: none;">
            <div class="modal-card">
                <div class="modal-header">
                    <div>
                        <h3 class="modal-title">Edit Product</h3>
                        <p class="modal-subtitle">{{ $product->product_name }}</p>
                    </div>
                    <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
                </div>
                <form method="POST" action="{{ url('admin/products/' . $product->id) }}" enctype="multipart/form-data" onsubmit="return validateCategorySelection(this)">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        {{-- Product Name --}}
                        <div class="form-group">
                            <label for="editProductName" class="form-label">Product Name</label>
                            <input type="text" 
                                   id="editProductName" 
                                   name="product_name" 
                                   class="form-input" 
                                   value="{{ old('product_name', $product->product_name) }}" 
                                   required />
                        </div>
                        {{-- Category --}}
                        <div class="form-group">
                            <label class="form-label">Categories <span style="font-weight: 400; color: #ef4444; font-size: 12px;">* (Select at least 1)</span></label>
                            <div class="category-checkbox-container" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; max-height: 140px; overflow-y: auto; padding: 10px; border: 1px solid #e4e4e7; border-radius: 8px; background: #fafafa; transition: border-color 0.15s, background-color 0.15s;">
                                @php
                                    $assignedCatIds = $product->categories->pluck('id')->all();
                                @endphp
                                @foreach (\App\Models\Category::all() as $cat)
                                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #18181b; cursor: pointer; user-select: none;">
                                        <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" {{ in_array($cat->id, $assignedCatIds) ? 'checked' : '' }} style="accent-color: #18181b; width: 15px; height: 15px; cursor: pointer;">
                                        <span>{{ $cat->category_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="category-error-msg" style="display: none; color: #ef4444; font-size: 12px; font-weight: 500; margin-top: 4px;">
                                Please select at least one category before saving.
                            </div>
                        </div>
                        {{-- Price & Stock --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div class="form-group">
                                <label for="editPrice" class="form-label">Price (฿)</label>
                                <input type="number" 
                                        step="any" 
                                        id="editPrice" 
                                        name="price" 
                                        class="form-input" 
                                        value="{{ old('price', $product->price) }}" 
                                        required 
                                        min="0" 
                                        max="10000000.00"
                                        oninput="if(parseFloat(this.value) > 10000000) this.value = 10000000;" />
                            </div>
                            <div class="form-group">
                                <label for="editStock" class="form-label">Stock quantity</label>
                                <input type="number" 
                                       step="1" 
                                       id="editStock" 
                                       name="stock" 
                                       class="form-input" 
                                       value="{{ old('stock', $stockCount ?? 0) }}" 
                                       required 
                                       min="0" />
                            </div>
                        </div>
                        {{-- Current Pictures --}}
                        <div class="form-group">
                            <label class="form-label">Current Pictures <span style="font-size: 11px; font-weight: normal; color: #71717a;">(Select Remove to delete a picture)</span></label>
                            <div style="width: 100%;">
                                @if ($product->productImages->isNotEmpty())
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach ($product->productImages as $pImg)
                                            @php
                                                $pUrl = str_starts_with($pImg->image, 'http') || str_starts_with($pImg->image, '/')
                                                    ? $pImg->image
                                                    : asset('storage/' . $pImg->image);
                                            @endphp
                                            <div class="overflow-hidden rounded-md border border-zinc-200 bg-white">
                                                <img src="{{ $pUrl }}" alt="Product picture" class="h-16 w-full object-cover" />
                                                <label class="flex cursor-pointer items-center gap-1 px-1.5 py-1 text-[11px] text-zinc-700">
                                                    <input type="checkbox" name="deleted_images[]" value="{{ $pImg->image }}" class="size-3.5 accent-zinc-900" />
                                                    <span>Remove</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div style="width: 100%; height: 80px; border: 1px dashed #d4d4d8; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #a1a1aa; font-size: 13px; background: #fafafa;">
                                        No pictures uploaded yet
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- Add More Pictures --}}
                        <div class="form-group">
                            <label for="editImages" class="form-label">Add More Pictures (optional, up to 10)</label>
                            <input type="file" 
                                   id="editImages" 
                                   name="images[]" 
                                   multiple
                                   accept="image/*" 
                                   class="form-input" 
                                   style="padding: 6px 12px; height: auto;" />
                        </div>
                        {{-- Thumbnail Pictures --}}
                        <div class="form-group">
                            <label for="editThumbnail" class="form-label">Thumbnail Picture (optional, replaces current thumbnail)</label>
                            <input type="file" 
                                   id="editThumbnail" 
                                   name="thumbnail" 
                                   accept="image/*" 
                                   class="form-input" 
                                   style="padding: 6px 12px; height: auto;" />
                        </div>
                        {{-- Description --}}
                        <div class="form-group">
                            <label for="editDescription" class="form-label">Description (optional)</label>
                            <textarea id="editDescription" 
                                      name="description" 
                                      class="form-input" 
                                      rows="3" 
                                      style="height: auto; padding: 8px 12px; resize: vertical;" 
                                      placeholder="Brief description of the product">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="btn-save">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
        <style>
            .modal-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(2px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                padding: 16px;
            }
            .modal-card {
                background: #ffffff;
                border-radius: 16px;
                width: 100%;
                max-width: 500px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                animation: modalFadeIn 0.15s ease-out;
            }
            @keyframes modalFadeIn {
                from { opacity: 0; transform: scale(0.96); }
                to { opacity: 1; transform: scale(1); }
            }
            .modal-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                padding: 20px 24px 16px;
                border-bottom: 1px solid #f4f4f5;
            }
            .modal-title { font-size: 18px; font-weight: 600; color: #18181b; margin: 0; }
            .modal-subtitle { font-size: 13px; color: #71717a; margin: 4px 0 0; }
            .modal-close {
                background: none;
                border: none;
                font-size: 22px;
                line-height: 1;
                color: #a1a1aa;
                cursor: pointer;
                padding: 4px 8px;
                border-radius: 6px;
            }
            .modal-close:hover { color: #18181b; background: #f4f4f5; }
            .modal-body { padding: 20px 24px; display: flex; flex-direction: column; gap: 14px; max-height: 75vh; overflow-y: auto; text-align: left; }
            .form-group { display: flex; flex-direction: column; gap: 6px; }
            .form-label { font-size: 13px; font-weight: 500; color: #3f3f46; }
            .form-input {
                width: 100%;
                height: 38px;
                border: 1px solid #e4e4e7;
                border-radius: 8px;
                padding: 0 12px;
                font-size: 14px;
                color: #18181b;
                background: #ffffff;
                outline: none;
                box-sizing: border-box;
                transition: border-color 0.15s;
            }
            .form-input:focus { border-color: #18181b; }
            .modal-footer {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
                padding: 16px 24px 20px;
                border-top: 1px solid #f4f4f5;
                background: #fafafa;
            }
            .btn-cancel {
                height: 36px;
                padding: 0 14px;
                border-radius: 8px;
                background: #ffffff;
                color: #52525b;
                border: 1px solid #e4e4e7;
                font-size: 13px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.15s;
            }
            .btn-cancel:hover { background: #f4f4f5; color: #18181b; }
            .btn-save {
                height: 36px;
                padding: 0 16px;
                border-radius: 8px;
                background: #18181b;
                color: #ffffff;
                border: none;
                font-size: 13px;
                font-weight: 500;
                cursor: pointer;
                transition: background 0.15s;
            }
            .btn-save:hover { background: #27272a; }
        </style>
        <script>
            function openEditModal() {
                document.getElementById('editProductModal').style.display = 'flex';
            }
            function closeEditModal() {
                document.getElementById('editProductModal').style.display = 'none';
            }
            window.addEventListener('click', function(e) {
                const modal = document.getElementById('editProductModal');
                if (e.target === modal) closeEditModal();
            });
            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeEditModal();
            });
            // Validate at least 1 category is checked before saving
            function validateCategorySelection(form) {
                const checked = form.querySelectorAll('input[name="category_ids[]"]:checked');
                const container = form.querySelector('.category-checkbox-container');
                const errorMsg = form.querySelector('.category-error-msg');
                const firstCheckbox = form.querySelector('input[name="category_ids[]"]');
                if (checked.length === 0) {
                    if (container) {
                        container.style.borderColor = '#ef4444';
                        container.style.backgroundColor = '#fef2f2';
                    }
                    if (errorMsg) errorMsg.style.display = 'block';
                    if (firstCheckbox) {
                        firstCheckbox.setCustomValidity('Please select at least one category.');
                        firstCheckbox.reportValidity();
                        firstCheckbox.focus();
                    }
                    return false;
                }
                if (container) {
                    container.style.borderColor = '#e4e4e7';
                    container.style.backgroundColor = '#fafafa';
                }
                if (errorMsg) errorMsg.style.display = 'none';
                if (firstCheckbox) firstCheckbox.setCustomValidity('');
                return true;
            }
            // Reset error when user checks any category
            document.addEventListener('change', function(e) {
                if (e.target.matches('input[name="category_ids[]"]')) {
                    const form = e.target.closest('form');
                    if (form) {
                        const checked = form.querySelectorAll('input[name="category_ids[]"]:checked');
                        const container = form.querySelector('.category-checkbox-container');
                        const errorMsg = form.querySelector('.category-error-msg');
                        const firstCheckbox = form.querySelector('input[name="category_ids[]"]');
                        
                        if (checked.length > 0) {
                            if (container) {
                                container.style.borderColor = '#e4e4e7';
                                container.style.backgroundColor = '#fafafa';
                            }
                            if (errorMsg) errorMsg.style.display = 'none';
                            if (firstCheckbox) firstCheckbox.setCustomValidity('');
                        }
                    }
                }
            });
        </script>
    @endif

</x-layouts::shop>
