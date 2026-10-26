<x-layouts::app :title="__('Coupons')">
    <style>
        .admin-page { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #18181b; }
        .admin-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
        .admin-title { font-size: 28px; font-weight: 600; line-height: 1.2; color: #18181b; margin: 0; }
        .admin-subtitle { font-size: 14px; color: #71717a; margin-top: 6px; }

        .btn-add-coupon { display: inline-flex; align-items: center; justify-content: center; height: 38px; padding: 0 16px; border-radius: 8px; background: #18181b; color: #ffffff; font-size: 13px; font-weight: 500; border: none; cursor: pointer; text-decoration: none; transition: background 0.15s; }
        .btn-add-coupon:hover { background: #27272a; }

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

        .cell-id { font-weight: 500; color: #18181b; }
        .cell-coupon { font-weight: 500; color: #18181b; text-transform: uppercase; letter-spacing: 0.02em; }
        .cell-discount { color: #18181b; }
        .cell-period { color: #52525b; }
        .cell-used { color: #18181b; }
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
            max-width: 440px;
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
        .modal-body { padding: 20px 24px; display: flex; flex-direction: column; gap: 14px; }
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
        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
    </style>

    <div class="admin-page p-6">
        {{-- Flash message on success --}}
        @if (session('status'))
            <div class="alert-success">
                {{ session('status') }}
            </div>
        @endif

        {{-- Error list if validation fails --}}
        @if ($errors->any())
            <div class="alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Header --}}
        <div class="admin-header">
            <div>
                <h1 class="admin-title">Coupons</h1>
                <p class="admin-subtitle">Create and manage discount coupons.</p>
            </div>
            <button type="button" class="btn-add-coupon" onclick="openAddModal()">Add coupon</button>
        </div>

        {{-- Search & Status Filter Pills --}}
        <div class="admin-filter-bar">
            <form method="GET" action="{{ route('admin.coupons') }}">
                @if (request('status') && request('status') !== 'all')
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="search-box">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 11a5.65 5.65 0 11-11.3 0 5.65 5.65 0 0111.3 0z" />
                    </svg>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search coupons"
                        class="search-input"
                    />
                </div>
            </form>

            <div class="pill-group">
                @php
                    $currentStatus = request('status', 'all');
                @endphp

                <a href="{{ route('admin.coupons', array_filter(['q' => request('q')])) }}"
                   class="pill {{ $currentStatus === 'all' ? 'pill-active' : 'pill-inactive' }}">
                    All
                </a>

                <a href="{{ route('admin.coupons', array_filter(['status' => 'active', 'q' => request('q')])) }}"
                   class="pill {{ $currentStatus === 'active' ? 'pill-active' : 'pill-inactive' }}">
                    Active
                </a>

                <a href="{{ route('admin.coupons', array_filter(['status' => 'expired', 'q' => request('q')])) }}"
                   class="pill {{ $currentStatus === 'expired' ? 'pill-active' : 'pill-inactive' }}">
                    Expired
                </a>
            </div>
        </div>

        {{-- Coupons Table Card --}}
        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 200px;">Coupon</th>
                        <th style="width: 120px;">Discount</th>
                        <th>Period</th>
                        <th style="width: 100px;">Used</th>
                        <th style="width: 120px; text-align: right;"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($coupons as $coupon)
                        <tr>
                            <td class="cell-id">{{ $coupon->id }}</td>
                            <td class="cell-coupon">{{ $coupon->coupon_name }}</td>
                            <td class="cell-discount">{{ $coupon->discount_rate }}%</td>
                            <td class="cell-period">
                                {{ \Carbon\Carbon::parse($coupon->start_date)->format('d M Y') }} – {{ \Carbon\Carbon::parse($coupon->end_date)->format('d M Y') }}
                            </td>
                            <td class="cell-used">{{ $coupon->orders_count ?? 0 }}</td>
                            <td class="cell-actions">
                                <button type="button" 
                                        class="action-edit"
                                        onclick="openEditModal({{ $coupon->id }}, '{{ addslashes($coupon->coupon_name) }}', {{ $coupon->discount_rate }}, '{{ \Carbon\Carbon::parse($coupon->start_date)->format('Y-m-d') }}', '{{ \Carbon\Carbon::parse($coupon->end_date)->format('Y-m-d') }}')">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" style="display: inline;" onsubmit="return confirm('Delete this coupon?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center; color: #a1a1aa;">
                                No coupons found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($coupons->hasPages())
            <div style="margin-top: 16px;">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>

    {{-- 1. ADD COUPON MODAL POPUP --}}
    <div id="addCouponModal" class="modal-overlay" style="display: none;">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Add Coupon</h3>
                    <p class="modal-subtitle">Create a new discount coupon code.</p>
                </div>
                <button type="button" class="modal-close" onclick="closeAddModal()">&times;</button>
            </div>

            <form method="POST" action="{{ url('admin/coupons') }}">
                @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <label for="addCouponName" class="form-label">Coupon Code</label>
                        <input type="text" 
                               id="addCouponName" 
                               name="coupon_name" 
                               class="form-input" 
                               placeholder="e.g. SUMMER20" 
                               style="text-transform: uppercase;"
                               required />
                    </div>

                    <div class="form-group">
                        <label for="addDiscountRate" class="form-label">Discount Rate (%)</label>
                        <input type="number" 
                               id="addDiscountRate" 
                               name="discount_rate" 
                               class="form-input" 
                               placeholder="e.g. 20" 
                               min="1" 
                               max="100" 
                               required />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label for="addStartDate" class="form-label">Start Date</label>
                            <input type="date" 
                                   id="addStartDate" 
                                   name="start_date" 
                                   class="form-input" 
                                   value="{{ date('Y-m-d') }}"
                                   required />
                        </div>

                        <div class="form-group">
                            <label for="addEndDate" class="form-label">End Date</label>
                            <input type="date" 
                                   id="addEndDate" 
                                   name="end_date" 
                                   class="form-input" 
                                   value="{{ date('Y-m-d', strtotime('+1 month')) }}"
                                   required />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn-save">Create coupon</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. EDIT COUPON MODAL POPUP --}}
    <div id="editCouponModal" class="modal-overlay" style="display: none;">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Edit Coupon</h3>
                    <p id="editCouponSubtitle" class="modal-subtitle">—</p>
                </div>
                <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>

            <form id="editCouponForm" method="POST" action="">
                @csrf
                @method('PATCH')

                <div class="modal-body">
                    <div class="form-group">
                        <label for="editCouponName" class="form-label">Coupon Code</label>
                        <input type="text" 
                               id="editCouponName" 
                               name="coupon_name" 
                               class="form-input" 
                               style="text-transform: uppercase;"
                               required />
                    </div>

                    <div class="form-group">
                        <label for="editDiscountRate" class="form-label">Discount Rate (%)</label>
                        <input type="number" 
                               id="editDiscountRate" 
                               name="discount_rate" 
                               class="form-input" 
                               min="1" 
                               max="100" 
                               required />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label for="editStartDate" class="form-label">Start Date</label>
                            <input type="date" 
                                   id="editStartDate" 
                                   name="start_date" 
                                   class="form-input" 
                                   required />
                        </div>

                        <div class="form-group">
                            <label for="editEndDate" class="form-label">End Date</label>
                            <input type="date" 
                                   id="editEndDate" 
                                   name="end_date" 
                                   class="form-input" 
                                   required />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add Modal
        function openAddModal() {
            document.getElementById('addCouponModal').style.display = 'flex';
        }
        function closeAddModal() {
            document.getElementById('addCouponModal').style.display = 'none';
        }

        // Edit Modal
        function openEditModal(id, name, rate, startDate, endDate) {
            document.getElementById('editCouponSubtitle').textContent = name;
            document.getElementById('editCouponName').value = name;
            document.getElementById('editDiscountRate').value = rate;
            document.getElementById('editStartDate').value = startDate;
            document.getElementById('editEndDate').value = endDate;

            // Sets form action dynamically to /admin/coupons/{id}
            document.getElementById('editCouponForm').action = "{{ url('admin/coupons') }}/" + id;

            document.getElementById('editCouponModal').style.display = 'flex';
        }
        function closeEditModal() {
            document.getElementById('editCouponModal').style.display = 'none';
        }

        // Close on clicking backdrop
        window.addEventListener('click', function(e) {
            const addModal = document.getElementById('addCouponModal');
            if (e.target === addModal) closeAddModal();

            const editModal = document.getElementById('editCouponModal');
            if (e.target === editModal) closeEditModal();
        });

        // Close on Escape key press
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });
    </script>
</x-layouts::app>