<x-layouts::app :title="__('Products')">
    <style>
        .admin-page { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #18181b; }
        .admin-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
        .admin-title { font-size: 28px; font-weight: 600; line-height: 1.2; color: #18181b; margin: 0; }
        .admin-subtitle { font-size: 14px; color: #71717a; margin-top: 6px; }

        .btn-add-product { display: inline-flex; align-items: center; justify-content: center; height: 38px; padding: 0 16px; border-radius: 8px; background: #18181b; color: #ffffff; font-size: 13px; font-weight: 500; border: none; cursor: pointer; text-decoration: none; transition: background 0.15s; }
        .btn-add-product:hover { background: #27272a; }

        .admin-filter-bar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
        .search-box { position: relative; width: 280px; }
        .search-box svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #9ca3af; pointer-events: none; }
        .search-input { width: 100%; height: 38px; border: 1px solid #e4e4e7; border-radius: 10px; padding: 0 14px 0 36px; font-size: 13px; color: #18181b; background: #ffffff; outline: none; box-sizing: border-box; }
        .search-input::placeholder { color: #a1a1aa; }
        .search-input:focus { border-color: #a1a1aa; }

        .pill-group { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .pill { display: inline-flex; align-items: center; justify-content: center; height: 32px; padding: 0 16px; border-radius: 9999px; font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.15s; box-sizing: border-box; }
        .pill-active { background: #18181b; color: #ffffff; border: 1px solid #18181b; }
        .pill-inactive { background: #ffffff; color: #52525b; border: 1px solid #e4e4e7; }
        .pill-inactive:hover { background: #f4f4f5; color: #18181b; }

        .admin-card { background: #ffffff; border: 1px solid #e4e4e7; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .admin-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        .admin-table th { font-size: 13px; font-weight: 400; color: #a1a1aa; padding: 14px 24px; border-bottom: 1px solid #f4f4f5; }
        .admin-table td { padding: 16px 24px; border-bottom: 1px solid #f4f4f5; color: #52525b; vertical-align: middle; }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover td { background-color: #fafafa; }

        .cell-id { font-weight: 500; color: #18181b; }
        .cell-product { color: #18181b; font-weight: 400; }
        .cell-price { color: #18181b; font-weight: 600; }
        .cell-actions { text-align: right; white-space: nowrap; }
        
        .action-edit { color: #18181b; text-decoration: underline; font-weight: 500; font-size: 13px; margin-right: 16px; background: none; border: none; cursor: pointer; padding: 0; }
        .action-edit:hover { color: #000000; }
        .action-delete { color: #dc2626; text-decoration: none; font-weight: 500; font-size: 13px; background: none; border: none; cursor: pointer; padding: 0; }
        .action-delete:hover { color: #b91c1c; }

        /* Modal Overlay & Card */
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
        .modal-body { padding: 20px 24px; display: flex; flex-direction: column; gap: 14px; max-height: 75vh; overflow-y: auto; }
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
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
    </style>

    <div class="admin-page p-6">
        {{-- Flash message on successful actions --}}
        @if (session('status'))
            <div class="alert-success">
                {{ session('status') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="admin-header">
            <div>
                <h1 class="admin-title">Products</h1>
                <p class="admin-subtitle">Add, edit or remove products and update stock.</p>
            </div>
            <button type="button" class="btn-add-product" onclick="openAddModal()">Add product</button>
        </div>

        {{-- Search & Category Filter Pills --}}
        <div class="admin-filter-bar">
            <form method="GET" action="{{ route('admin.products') }}">
                @if (request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="search-box">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 11a5.65 5.65 0 11-11.3 0 5.65 5.65 0 0111.3 0z" />
                    </svg>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search products"
                        class="search-input"
                    />
                </div>
            </form>

            <div class="pill-group">
                {{-- All Pill --}}
                <a href="{{ route('admin.products', array_filter(['q' => request('q')])) }}"
                   class="pill {{ ! request('category') ? 'pill-active' : 'pill-inactive' }}">
                    All
                </a>

                {{-- Category Pills --}}
                @foreach ($categories as $category)
                    <a href="{{ route('admin.products', array_filter(['category' => $category, 'q' => request('q')])) }}"
                       class="pill {{ request('category') === $category ? 'pill-active' : 'pill-inactive' }}">
                        {{ $category }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Table Card --}}
        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th style="text-align: right;"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($products as $product)
                        @php
                            $firstImg = $product->productImages->first()?->image;
                            $imageUrl = $firstImg 
                                ? (str_starts_with($firstImg, 'http') || str_starts_with($firstImg, '/') 
                                    ? $firstImg 
                                    : asset('storage/' . $firstImg)) 
                                : '';
                            $productImages = $product->productImages->map(fn ($productImage) => [
                                'path' => $productImage->image,
                                'url' => str_starts_with($productImage->image, 'http') || str_starts_with($productImage->image, '/')
                                    ? $productImage->image
                                    : asset('storage/' . $productImage->image),
                            ])->values()->all();
                            $productData = [
                                'id'           => $product->id,
                                'name'         => $product->product_name,
                                'price'        => $product->price,
                                'stock'        => $product->stock?->quantity ?? 0,
                                'image'        => $imageUrl,
                                'images'       => $productImages,
                                'category_ids' => $product->categories->pluck('id')->all(),
                                'category_id'  => $product->categories->first()?->id ?? '',
                                'description'  => $product->description ?? '',
                            ];
                        @endphp
                        <tr>
                            <td class="cell-id">{{ $product->id }}</td>
                            <td class="cell-product">{{ $product->product_name }}</td>
                            <td>{{ $product->categories->pluck('category_name')->join(', ') ?: '—' }}</td>
                            <td class="cell-price">฿{{ number_format($product->price) }}</td>
                            <td>{{ $product->stock?->quantity ?? 0 }}</td>
                            <td class="cell-actions">
                                <button type="button" 
                                        class="action-edit"
                                        onclick='openEditModal(@json($productData))'>
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display: inline;" onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center; color: #a1a1aa;">
                                No products found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div style="margin-top: 16px;">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    {{-- 1. ADD PRODUCT MODAL POPUP --}}
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
                                    min="0" 
                                    max="10000000.00"
                                    oninput="if(parseFloat(this.value) > 10000000) this.value = 10000000;" />
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

                    {{-- Product Pictures --}}
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
                    {{-- Thumbnail Pictures --}}
                    <div class="form-group">
                        <label for="addThumbnail" class="form-label">Thumbnail Pictures (optional)</label>
                        <input type="file" 
                               id="addThumbnail" 
                               name="thumbnail" 
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

    {{-- 2. EDIT PRODUCT MODAL POPUP --}}
    <div id="editProductModal" class="modal-overlay" style="display: none;">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Edit Product</h3>
                    <p id="modalProductName" class="modal-subtitle">—</p>
                </div>
                <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>

            <form id="editProductForm" method="POST" action="" enctype="multipart/form-data" onsubmit="return validateCategorySelection(this)">
                @csrf
                @method('PATCH')

                <div class="modal-body">
                    {{-- Product Name --}}
                    <div class="form-group">
                        <label for="editProductNameInput" class="form-label">Product Name</label>
                        <input type="text" 
                               id="editProductNameInput" 
                               name="product_name" 
                               class="form-input" 
                               required />
                    </div>

                    {{-- Categories --}}
                    <div class="form-group">
                        <label class="form-label">Categories <span style="font-weight: 400; color: #ef4444; font-size: 12px;">* (Select at least 1)</span></label>
                        <div class="category-checkbox-container" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; max-height: 140px; overflow-y: auto; padding: 10px; border: 1px solid #e4e4e7; border-radius: 8px; background: #fafafa; transition: border-color 0.15s, background-color 0.15s;">
                            @foreach (\App\Models\Category::all() as $cat)
                                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #18181b; cursor: pointer; user-select: none;">
                                    <input type="checkbox" class="edit-category-checkbox" name="category_ids[]" value="{{ $cat->id }}" style="accent-color: #18181b; width: 15px; height: 15px; cursor: pointer;">
                                    <span>{{ $cat->category_name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="category-error-msg" style="display: none; color: #ef4444; font-size: 12px; font-weight: 500; margin-top: 4px;">
                            Please select at least one category before saving.
                        </div>
                    </div>

                    {{-- Price and Stock --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label for="editPrice" class="form-label">Price (฿)</label>
                            <input type="number" 
                                   step="any" 
                                    id="editPrice" 
                                    name="price" 
                                    class="form-input" 
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
                                   required 
                                   min="0" />
                        </div>
                    </div>

                    {{-- Current Pictures --}}
                    <div class="form-group">
                        <label class="form-label">Current Pictures <span style="font-size: 11px; font-weight: normal; color: #71717a;">(Select Remove to delete a picture)</span></label>
                        <div id="editImageContainer" style="width: 100%;">
                            <div id="editImageList" class="grid grid-cols-4 gap-2"></div>
                            <div id="editNoImageNotice" 
                                 style="display: none; width: 100%; height: 100px; border: 1px dashed #d4d4d8; border-radius: 10px; align-items: center; justify-content: center; color: #a1a1aa; font-size: 13px; background: #fafafa;">
                                No pictures uploaded yet
                            </div>
                        </div>
                    </div>
                    {{-- Add Pictures Input --}}
                    <div class="form-group">
                        <label for="editImage" class="form-label">Add More Pictures (optional, up to 10)</label>
                        <input type="file" 
                               id="editImage" 
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
                                  placeholder="Brief description of the product"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. FULL PICTURE LIGHTBOX POPUP --}}
    <div id="imageLightboxModal" class="modal-overlay" style="display: none; z-index: 10000; background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(4px);" onclick="closeLightbox()">
        <div style="position: relative; max-width: 90vw; max-height: 90vh; display: flex; align-items: center; justify-content: center;" onclick="event.stopPropagation()">
            <button type="button" 
                    onclick="closeLightbox()" 
                    style="position: absolute; top: -42px; right: 0; background: none; border: none; color: #ffffff; font-size: 32px; line-height: 1; cursor: pointer; padding: 4px; opacity: 0.8;"
                    onmouseover="this.style.opacity='1'" 
                    onmouseout="this.style.opacity='0.8'">&times;</button>
            <img id="lightboxImage" 
                 src="" 
                 alt="Full picture" 
                 style="max-width: 90vw; max-height: 85vh; object-fit: contain; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6); background: #18181b;" />
        </div>
    </div>

    {{-- Modals Javascript --}}
    <script>
        // Lightbox Popup for Full Picture
        function openLightbox(src) {
            if (!src || src.trim() === '') return;
            document.getElementById('lightboxImage').src = src;
            document.getElementById('imageLightboxModal').style.display = 'flex';
        }

        function closeLightbox() {
            document.getElementById('imageLightboxModal').style.display = 'none';
        }

        // Add Modal
        function openAddModal() {
            document.getElementById('addProductModal').style.display = 'flex';
        }
        function closeAddModal() {
            document.getElementById('addProductModal').style.display = 'none';
        }

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
                if (errorMsg) {
                    errorMsg.style.display = 'block';
                }
                if (firstCheckbox) {
                    firstCheckbox.setCustomValidity('Please select at least one category.');
                    firstCheckbox.reportValidity();
                    firstCheckbox.focus();
                }
                return false; // Blocks form submit!
            }
            if (container) {
                container.style.borderColor = '#e4e4e7';
                container.style.backgroundColor = '#fafafa';
            }
            if (errorMsg) errorMsg.style.display = 'none';
            if (firstCheckbox) firstCheckbox.setCustomValidity('');
            return true;
        }
        // Clear error state immediately when any category checkbox is selected
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

        // Edit Modal
        function openEditModal(data, name, price, stock, image, categoryId, description) {
            const p = typeof data === 'object' ? data : {
                id: data,
                name: name,
                price: price,
                stock: stock,
                image: image,
                category_id: categoryId,
                description: description
            };
            document.getElementById('modalProductName').textContent = p.name || '—';
            document.getElementById('editProductNameInput').value = p.name || '';
            document.getElementById('modalProductName').textContent = p.name || '—';
            document.getElementById('editProductNameInput').value = p.name || '';
            // Pre-check assigned categories
            const selectedCats = Array.isArray(p.category_ids) 
                ? p.category_ids.map(Number) 
                : (p.category_id ? [Number(p.category_id)] : []);
            document.querySelectorAll('.edit-category-checkbox').forEach(cb => {
                cb.checked = selectedCats.includes(Number(cb.value));
            });
            
            document.getElementById('editPrice').value = p.price;
            document.getElementById('editStock').value = p.stock;
            document.getElementById('editDescription').value = p.description || '';
            document.getElementById('editProductForm').action = "{{ url('admin/products') }}/" + p.id;
            // Reset file picker
            document.getElementById('editImage').value = '';
            const editThumb = document.getElementById('editThumbnail');
            if (editThumb) editThumb.value = '';
            // Render current pictures with per-image removal controls
            const imageList = document.getElementById('editImageList');
            const noImgNotice = document.getElementById('editNoImageNotice');
            const images = Array.isArray(p.images) ? p.images : (p.image ? [{ id: null, url: p.image }] : []);
            imageList.replaceChildren();
            for (const image of images) {
                const imageCard = document.createElement('div');
                imageCard.className = 'overflow-hidden rounded-md border border-zinc-200 bg-white';
                const previewImg = document.createElement('img');
                previewImg.src = image.url;
                previewImg.alt = 'Current product picture';
                previewImg.className = 'h-16 w-full cursor-zoom-in object-cover';
                previewImg.addEventListener('click', () => openLightbox(image.url));
                const removeLabel = document.createElement('label');
                removeLabel.className = 'flex cursor-pointer items-center gap-1 px-1.5 py-1 text-[11px] text-zinc-700';
                const removeCheckbox = document.createElement('input');
                removeCheckbox.type = 'checkbox';
                removeCheckbox.name = 'deleted_images[]';
                removeCheckbox.value = image.path;
                removeCheckbox.className = 'size-3.5 accent-zinc-900';
                const removeText = document.createElement('span');
                removeText.textContent = 'Remove';
                removeLabel.append(removeCheckbox, removeText);
                imageCard.append(previewImg, removeLabel);
                imageList.append(imageCard);
            }
            if (images.length > 0) {
                imageList.style.display = 'grid';
                noImgNotice.style.display = 'none';
            } else {
                imageList.style.display = 'none';
                noImgNotice.style.display = 'flex';
            }
            document.getElementById('editProductModal').style.display = 'flex';
        }
        function closeEditModal() {
            document.getElementById('editProductModal').style.display = 'none';
        }
        // Close on clicking backdrop
        window.addEventListener('click', function(e) {
            const addModal = document.getElementById('addProductModal');
            if (e.target === addModal) closeAddModal();
            const editModal = document.getElementById('editProductModal');
            if (e.target === editModal) closeEditModal();
        });
        // Close on Escape key
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
                closeAddModal();
                closeEditModal();
            }
        });
    </script>
</x-layouts::app>