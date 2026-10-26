@php
    $usersCount = $usersCount ?? \App\Models\User::where('role', '!=', 'ADMIN')->count();
    $productsCount = $productsCount ?? \App\Models\Product::count();
    $ordersCount = $ordersCount ?? \App\Models\Order::count();
    $revenue = $revenue ?? (\App\Models\Order::sum('total_price') ?? 0);
    $recentOrders = $recentOrders ?? \App\Models\Order::with('user')->latest()->take(5)->get();
    $lowStockProducts = $lowStockProducts ?? \App\Models\Product::whereHas('stock', fn ($q) => $q->where('quantity', '<=', 10))->with('stock')->take(5)->get();
@endphp

<x-layouts::app :title="__('Dashboard')">
    <style>
        .admin-page { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #18181b; }
        .admin-header { margin-bottom: 24px; }
        .admin-title { font-size: 28px; font-weight: 600; line-height: 1.2; color: #18181b; margin: 0; }
        .admin-subtitle { font-size: 14px; color: #71717a; margin-top: 6px; }

        /* 4 Stat Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #ffffff; border: 1px solid #e4e4e7; border-radius: 16px; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .stat-label { font-size: 13px; color: #71717a; margin-bottom: 8px; font-weight: 400; }
        .stat-value { font-size: 28px; font-weight: 600; color: #18181b; line-height: 1.1; letter-spacing: -0.02em; }

        /* 2-Column Section */
        .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .dashboard-card { background: #ffffff; border: 1px solid #e4e4e7; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .card-heading { font-size: 16px; font-weight: 600; color: #18181b; margin: 0 0 18px 0; }

        /* Table inside Recent Orders */
        .recent-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        .recent-table th { font-size: 13px; font-weight: 400; color: #a1a1aa; padding-bottom: 12px; border-bottom: 1px solid #f4f4f5; }
        .recent-table td { padding: 14px 0; border-bottom: 1px solid #f4f4f5; color: #52525b; }
        .recent-table tr:last-child td { border-bottom: none; }
        .order-id { font-weight: 500; color: #18181b; }
        .order-user { color: #18181b; }
        .order-date { color: #71717a; }
        .order-total { font-weight: 600; color: #18181b; }

        /* Low stock list */
        .stock-list { display: flex; flex-direction: column; gap: 14px; }
        .stock-item { display: flex; align-items: center; justify-content: space-between; font-size: 14px; gap: 16px; }
        .stock-name { color: #18181b; }
        .stock-qty { color: #dc2626; font-weight: 600; white-space: nowrap; }
    </style>

    <div class="admin-page p-6">
        {{-- Header --}}
        <div class="admin-header">
            <h1 class="admin-title">Dashboard</h1>
            <p class="admin-subtitle">Overview of users, products and orders.</p>
        </div>

        {{-- 4 Top Metric Cards --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Users</div>
                <div class="stat-value">{{ number_format($usersCount) }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Products</div>
                <div class="stat-value">{{ number_format($productsCount) }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Orders</div>
                <div class="stat-value">{{ number_format($ordersCount) }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Revenue</div>
                <div class="stat-value">฿{{ number_format($revenue) }}</div>
            </div>
        </div>

        {{-- Bottom 2 Columns --}}
        <div class="content-grid">
            {{-- Recent Orders (Left Column) --}}
            <div class="dashboard-card">
                <h2 class="card-heading">Recent orders</h2>

                <table class="recent-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Order</th>
                            <th>User</th>
                            <th style="width: 140px;">Date</th>
                            <th style="width: 100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr>
                                <td class="order-id">#{{ $order->id }}</td>
                                <td class="order-user">{{ $order->user?->name ?? 'Guest' }}</td>
                                <td class="order-date">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="order-total">฿{{ number_format($order->total_price) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 24px 0; text-align: center; color: #a1a1aa;">
                                    No orders yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Low Stock (Right Column) --}}
            <div class="dashboard-card" style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <h2 class="card-heading" style="font-size: 16px; font-weight: 600; color: #18181b; margin: 0 0 18px 0;">Low stock</h2>

                <div class="stock-list" style="display: flex; flex-direction: column; gap: 14px;">
                    @forelse ($lowStockProducts as $product)
                        <div class="stock-item" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; font-size: 14px;">
                            <span class="stock-name" style="color: #18181b; font-weight: 400;">{{ $product->product_name }}</span>
                            <span class="stock-qty" style="color: #dc2626; font-weight: 600; white-space: nowrap;">{{ $product->stock?->quantity ?? 0 }} left</span>
                        </div>
                    @empty
                        <p style="color: #a1a1aa; font-size: 14px; margin: 0;">
                            All products have sufficient stock.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>