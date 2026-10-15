<x-layouts::app :title="__('Admin dashboard')">
    <div class="flex flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">Admin dashboard</flux:heading>
            <flux:text class="mt-2">Welcome to the kitchenware store administration area.</flux:text>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <flux:card>
                <flux:heading size="lg">Products</flux:heading>
                <flux:text class="mt-2">Manage the store product catalog.</flux:text>
                <flux:button class="mt-4" :href="route('admin.products')" variant="outline">Manage products</flux:button>
            </flux:card>

            <flux:card>
                <flux:heading size="lg">Orders</flux:heading>
                <flux:text class="mt-2">Review and manage customer orders.</flux:text>
                <flux:button class="mt-4" :href="route('admin.orders')" variant="outline">Manage orders</flux:button>
            </flux:card>

            <flux:card>
                <flux:heading size="lg">Users</flux:heading>
                <flux:text class="mt-2">View and manage customer accounts.</flux:text>
                <flux:button class="mt-4" :href="route('admin.users')" variant="outline">Manage users</flux:button>
            </flux:card>
        </div>
    </div>
</x-layouts::app>