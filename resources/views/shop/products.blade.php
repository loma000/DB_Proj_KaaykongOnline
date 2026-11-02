<x-layouts::shop title="All products" active="products">
    <div class="flex flex-col px-12 pt-10">
        <p class="text-[13px] leading-[normal] text-zinc-500"><a href="{{ route('home') }}">Home</a> / Products</p>
        <h1 class="mt-2 text-[32px] font-semibold leading-[normal] tracking-[-0.32px] text-zinc-800">All products</h1>
        <p class="mt-2 text-sm leading-[normal] text-zinc-500">Browse kitchenware for everyday cooking.</p>
    </div>

    @if (session('status'))
        <div class="mx-12 mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

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
                'images'  => $product->productImages->map(fn ($image) => asset('storage/'.$image->image))->all(),
                'image'   => !$product->productImages->isEmpty() 
                    ? asset('storage/'.$product->productImages[0]->image) 
                    : ($product->image ? (str_starts_with($product->image, 'http') || str_starts_with($product->image, '/') ? $product->image : asset('storage/' . $product->image)) : null),
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

        {{-- ADMIN ONLY: Floating Add Product Button & Modal --}}
    @if (auth()->check() && auth()->user()->role === 'ADMIN')
        {{-- Floating "+" Button (Bottom-Right) --}}
        <button type="button" 
                onclick="openAddModal()" 
                style="position: fixed; bottom: 32px; right: 32px; z-index: 50; width: 56px; height: 56px; border-radius: 50%; background: #18181b; color: #ffffff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4); transition: transform 0.2s, background 0.2s;"
                onmouseover="this.style.transform='scale(1.1)'; this.style.background='#27272a';"
                onmouseout="this.style.transform='scale(1)'; this.style.background='#18181b';"
                title="Add new product">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </button>

        {{-- Add Product Modal --}}
        <div id="addProductModal" class="modal-overlay" style="display: none;">
            <div class="modal-card">
                <div class="modal-header">
                    <div>
                        <h3 class="modal-title">Add Product</h3>
                        <p class="modal-subtitle">Create a new product with category, price, and stock.</p>
                    </div>
                    <button type="button" class="modal-close" onclick="closeAddModal()">&times;</button>
                </div>

                <form method="POST" action="{{ url('admin/products') }}" enctype="multipart/form-data" onsubmit="return validateCategorySelection(this)">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="addProductName" class="form-label">Product Name</label>
                            <input type="text" 
                                   id="addProductName" 
                                   name="product_name" 
                                   class="form-input" 
                                   placeholder="e.g. 10-Inch Cast Iron Skillet" 
                                   required />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Categories <span style="font-weight: 400; color: #ef4444; font-size: 12px;">* (Select at least 1)</span></label>
                            <div class="category-checkbox-container" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; max-height: 140px; overflow-y: auto; padding: 10px; border: 1px solid #e4e4e7; border-radius: 8px; background: #fafafa; transition: border-color 0.15s, background-color 0.15s;">
                                @foreach (\App\Models\Category::all() as $cat)
                                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #18181b; cursor: pointer; user-select: none;">
                                        <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" style="accent-color: #18181b; width: 15px; height: 15px; cursor: pointer;">
                                        <span>{{ $cat->category_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="category-error-msg" style="display: none; color: #ef4444; font-size: 12px; font-weight: 500; margin-top: 4px;">
                                Please select at least one category before saving.
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div class="form-group">
                                <label for="addPrice" class="form-label">Price (฿)</label>
                                <input type="number" 
                                       step="any" 
                                       id="addPrice" 
                                       name="price" 
                                       class="form-input" 
                                       placeholder="e.g. 890" 
                                       required 
                                       min="0" />
                            </div>

                            <div class="form-group">
                                <label for="addStock" class="form-label">Stock quantity</label>
                                <input type="number" 
                                       step="1" 
                                       id="addStock" 
                                       name="stock" 
                                       class="form-input" 
                                       placeholder="e.g. 24" 
                                       required 
                                       min="0" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="addThumbnail" class="form-label">Thumbnail Picture (optional)</label>
                            <input type="file" 
                                id="addThumbnail" 
                                name="thumbnail" 
                                accept="image/*" 
                                class="form-input" 
                                style="padding: 6px 12px; height: auto;" />
                        </div>

                        <div class="form-group">
                            <label for="addImage" class="form-label">Product Pictures (optional, up to 10)</label>
                            <input type="file" 
                                   id="addImage" 
                                name="images[]" 
                                multiple
                                   accept="image/*" 
                                   class="form-input" 
                                   style="padding: 6px 12px; height: auto;" />
                        </div>

                        <div class="form-group">
                            <label for="addDescription" class="form-label">Description (optional)</label>
                            <textarea id="addDescription" 
                                      name="description" 
                                      class="form-input" 
                                      rows="2" 
                                      style="height: auto; padding: 8px 12px; resize: vertical;" 
                                      placeholder="Brief description of the product"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancel</button>
                        <button type="submit" class="btn-save">Create product</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal CSS --}}
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
                max-width: 480px;
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

        {{-- Javascript for Modal --}}
        <script>
            function openAddModal() {
                document.getElementById('addProductModal').style.display = 'flex';
            }
            function closeAddModal() {
                document.getElementById('addProductModal').style.display = 'none';
            }
            window.addEventListener('click', function(e) {
                const addModal = document.getElementById('addProductModal');
                if (e.target === addModal) closeAddModal();
            });
            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeAddModal();
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