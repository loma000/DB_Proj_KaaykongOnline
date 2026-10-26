<x-layouts::app :title="__('Orders')">
    <style>
        .admin-page { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #18181b; }
        .admin-header { margin-bottom: 24px; }
        .admin-title { font-size: 28px; font-weight: 600; line-height: 1.2; color: #18181b; margin: 0; }
        .admin-subtitle { font-size: 14px; color: #71717a; margin-top: 6px; }

        .admin-filter-bar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
        .search-box { position: relative; width: 280px; }
        .search-box svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #9ca3af; pointer-events: none; }
        .search-input { width: 100%; height: 38px; border: 1px solid #e4e4e7; border-radius: 10px; padding: 0 14px 0 36px; font-size: 13px; color: #18181b; background: #ffffff; outline: none; box-sizing: border-box; }
        .search-input::placeholder { color: #a1a1aa; }
        .search-input:focus { border-color: #a1a1aa; }

        .pill-group { display: flex; align-items: center; gap: 8px; }
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

        .cell-order { font-weight: 500; color: #18181b; }
        .cell-user { color: #18181b; }
        .cell-date, .cell-time { color: #71717a; white-space: nowrap; }
        .cell-items { color: #18181b; }
        .cell-total { font-weight: 600; color: #18181b; white-space: nowrap; }
        .cell-actions { text-align: right; white-space: nowrap; }

        .coupon-badge { display: inline-block; padding: 2px 8px; border: 1px solid #e4e4e7; border-radius: 9999px; font-size: 11px; font-weight: 500; color: #71717a; text-transform: uppercase; background: #ffffff; }

        .action-view { color: #18181b; text-decoration: underline; font-weight: 500; font-size: 13px; margin-right: 16px; background: none; border: none; cursor: pointer; padding: 0; }
        .action-view:hover { color: #000000; }
        .action-delete { color: #dc2626; text-decoration: none; font-weight: 500; font-size: 13px; background: none; border: none; cursor: pointer; padding: 0; }
        .action-delete:hover { opacity: 0.8; }

        /* Order Details Modal */
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
            max-width: 560px;
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
        .modal-body { padding: 20px 24px; max-height: 70vh; overflow-y: auto; }
        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 14px 24px;
            border-top: 1px solid #f4f4f5;
            background: #fafafa;
        }
        .btn-cancel {
            height: 36px;
            padding: 0 16px;
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
    </style>

    <div class="admin-page p-6">
        @if (session('status'))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
                {{ session('status') }}
            </div>
        @endif
        {{-- Header --}}
        <div class="admin-header">
            <h1 class="admin-title">Orders</h1>
            <p class="admin-subtitle">Review and manage customer orders.</p>
        </div>

        {{-- Search & Period Filter Pills --}}
        <div class="admin-filter-bar">
            <form method="GET" action="{{ route('admin.orders') }}">
                @if (request('period') && request('period') !== 'all')
                    <input type="hidden" name="period" value="{{ request('period') }}">
                @endif
                <div class="search-box">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 11a5.65 5.65 0 11-11.3 0 5.65 5.65 0 0111.3 0z" />
                    </svg>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search by order or user"
                        class="search-input"
                    />
                </div>
            </form>

            <div class="pill-group">
                @php
                    $currentPeriod = request('period', 'all');
                @endphp

                <a href="{{ route('admin.orders', array_filter(['q' => request('q')])) }}"
                   class="pill {{ $currentPeriod === 'all' ? 'pill-active' : 'pill-inactive' }}">
                    All
                </a>

                <a href="{{ route('admin.orders', array_filter(['period' => 'today', 'q' => request('q')])) }}"
                   class="pill {{ $currentPeriod === 'today' ? 'pill-active' : 'pill-inactive' }}">
                    Today
                </a>

                <a href="{{ route('admin.orders', array_filter(['period' => 'week', 'q' => request('q')])) }}"
                   class="pill {{ $currentPeriod === 'week' ? 'pill-active' : 'pill-inactive' }}">
                    This week
                </a>

                <a href="{{ route('admin.orders', array_filter(['period' => 'month', 'q' => request('q')])) }}"
                   class="pill {{ $currentPeriod === 'month' ? 'pill-active' : 'pill-inactive' }}">
                    This month
                </a>
            </div>
        </div>

        {{-- Orders Table Card --}}
        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Order</th>
                        <th style="width: 140px;">User</th>
                        <th style="width: 110px;">Date</th>
                        <th style="width: 80px;">Time</th>
                        <th>Items</th>
                        <th style="width: 100px;">Coupon</th>
                        <th style="width: 100px;">Total</th>
                        <th style="width: 120px; text-align: right;"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($orders as $order)
                        @php
                            $subtotal = $order->products->sum(fn ($p) => (float) $p->pivot->price * (int) $p->pivot->quantity);
                            $discountAmount = max(0, $subtotal - (float) $order->total_price);
                            $orderData = [
                                'id' => $order->id,
                                'user' => $order->user?->name ?? 'Guest',
                                'date' => $order->created_at->format('d M Y, H:i'),
                                'subtotal' => number_format($subtotal, 2),
                                'discount_amount' => $discountAmount > 0 ? number_format($discountAmount, 2) : null,
                                'coupon_name' => $order->discount?->coupon_name,
                                'total' => number_format((float) $order->total_price, 2),
                                'products' => $order->products->map(function ($p) {
                                    $image = $p->image;
                                    $imageUrl = $image ? (str_starts_with($image, 'http') || str_starts_with($image, '/') ? $image : asset('storage/' . $image)) : null;
                                    return [
                                        'name' => $p->product_name,
                                        'image' => $imageUrl,
                                        'price' => number_format((float) $p->pivot->price, 2),
                                        'quantity' => $p->pivot->quantity,
                                        'total' => number_format((float) $p->pivot->price * (int) $p->pivot->quantity, 2),
                                    ];
                                })->values(),
                            ];
                        @endphp
                        <tr>
                            <td class="cell-order">#{{ $order->id }}</td>
                            <td class="cell-user">{{ $order->user?->name ?? 'Guest' }}</td>
                            <td class="cell-date">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="cell-time">{{ $order->created_at->format('H:i') }}</td>
                            <td class="cell-items">
                                {{ $order->products->map(fn($p) => $p->product_name . ' × ' . $p->pivot->quantity)->join(', ') ?: '—' }}
                            </td>
                            <td>
                                @if ($order->discount)
                                    <span class="coupon-badge">{{ $order->discount->coupon_name }}</span>
                                @else
                                    <span style="color: #a1a1aa;">—</span>
                                @endif
                            </td>
                            <td class="cell-total">฿{{ number_format($order->total_price) }}</td>
                            <td class="cell-actions">
                                <button type="button" 
                                        class="action-view" 
                                        onclick='openOrderModal(@json($orderData))'>
                                    View
                                </button>
                                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" style="display: inline;" onsubmit="return confirm('Delete this order?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 40px; text-align: center; color: #a1a1aa;">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div style="margin-top: 16px;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    {{-- ORDER DETAILS MODAL POPUP --}}
    <div id="orderDetailModal" class="modal-overlay" style="display: none;">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <h3 class="modal-title">Order <span id="modalOrderId">#0</span></h3>
                        <span style="display: inline-block; padding: 2px 10px; background: #f4f4f5; border-radius: 9999px; font-size: 12px; font-weight: 500; color: #3f3f46;">Confirmed</span>
                    </div>
                    <p class="modal-subtitle">
                        <span id="modalOrderDate">—</span> • <span id="modalOrderUser" style="color: #18181b; font-weight: 500;">—</span>
                    </p>
                </div>
                <button type="button" class="modal-close" onclick="closeOrderModal()">&times;</button>
            </div>

            <div class="modal-body">
                {{-- Items Section --}}
                <div style="margin-bottom: 20px;">
                    <h4 style="font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: #71717a; margin: 0 0 10px 0;">Order details</h4>
                    <div id="modalOrderItems" style="border-top: 1px solid #f4f4f5;">
                        {{-- Items loaded dynamically --}}
                    </div>
                </div>

                {{-- Payment Summary Section --}}
                <div style="background: #fafafa; border: 1px solid #e4e4e7; border-radius: 12px; padding: 16px;">
                    <h4 style="font-size: 14px; font-weight: 600; color: #18181b; margin: 0 0 12px 0;">Payment summary</h4>
                    
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #71717a; margin-bottom: 8px;">
                        <span>Subtotal</span>
                        <span id="modalOrderSubtotal" style="font-weight: 500; color: #18181b;">฿0.00</span>
                    </div>

                    <div id="modalDiscountRow" style="display: flex; justify-content: space-between; font-size: 13px; color: #71717a; margin-bottom: 8px;">
                        <span id="modalCouponLabel">Coupon</span>
                        <span id="modalDiscountAmount" style="font-weight: 500; color: #15803d;">−฿0.00</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #71717a; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e4e4e7;">
                        <span>Shipping</span>
                        <span style="font-weight: 500; color: #18181b;">Free</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 15px; font-weight: 600; color: #18181b;">
                        <span>Total paid</span>
                        <span id="modalOrderTotal" style="color: #18181b;">฿0.00</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeOrderModal()" style="width: 100%; text-align: center;">Close</button>
            </div>
        </div>
    </div>

    <script>
        function openOrderModal(order) {
            document.getElementById('modalOrderId').textContent = '#' + order.id;
            document.getElementById('modalOrderDate').textContent = 'Placed ' + order.date;
            document.getElementById('modalOrderUser').textContent = 'Customer: ' + order.user;
            
            // Subtotal & Total
            document.getElementById('modalOrderSubtotal').textContent = '฿' + order.subtotal;
            document.getElementById('modalOrderTotal').textContent = '฿' + order.total;
            
            // Coupon Discount
            const discountRow = document.getElementById('modalDiscountRow');
            if (order.coupon_name && order.discount_amount) {
                document.getElementById('modalCouponLabel').textContent = 'Coupon · ' + order.coupon_name;
                document.getElementById('modalDiscountAmount').textContent = '−฿' + order.discount_amount;
                discountRow.style.display = 'flex';
            } else {
                discountRow.style.display = 'none';
            }
            
            // Items List
            const itemsContainer = document.getElementById('modalOrderItems');
            itemsContainer.innerHTML = '';
            
            order.products.forEach(function(item) {
                const itemEl = document.createElement('div');
                itemEl.style.cssText = 'display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f4f4f5;';
                
                const imgHtml = item.image 
                    ? `<img src="${item.image}" alt="" style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">`
                    : `<div style="width: 44px; height: 44px; background: #f4f4f5; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #a1a1aa; flex-shrink: 0;">No img</div>`;
                    
                itemEl.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                        ${imgHtml}
                        <div style="min-width: 0;">
                            <div style="font-size: 14px; font-weight: 500; color: #18181b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 280px;">${item.name}</div>
                            <div style="font-size: 13px; color: #71717a; margin-top: 2px;">฿${item.price} × ${item.quantity}</div>
                        </div>
                    </div>
                    <div style="font-size: 14px; font-weight: 500; color: #18181b; white-space: nowrap;">฿${item.total}</div>
                `;
                itemsContainer.appendChild(itemEl);
            });
            
            document.getElementById('orderDetailModal').style.display = 'flex';
        }

        function closeOrderModal() {
            document.getElementById('orderDetailModal').style.display = 'none';
        }

        // Close when clicking outside the card
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('orderDetailModal');
            if (e.target === modal) {
                closeOrderModal();
            }
        });

        // Close on Escape key press
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeOrderModal();
            }
        });
    </script>
</x-layouts::app>