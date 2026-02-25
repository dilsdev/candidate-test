<x-app-layout>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">Welcome back, {{ Auth::user()->name }}. Here's an overview of your CLT
            data.</p>
    </div>

    <!-- Stats Grid -->
    @php
        $supplierCount = \App\Models\Supplier::count();
        $layupCount = \App\Models\CltLayup::count();
        $layerCount = \App\Models\CltLayer::count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <!-- Suppliers Card -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--clt-primary);">
                            Suppliers</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $supplierCount }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(45, 80, 22, 0.08);">
                        <svg class="w-5 h-5" style="color: var(--clt-primary);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <a href="{{ route('suppliers.index') }}" class="text-xs font-medium hover:underline"
                        style="color: var(--clt-primary);">
                        View all suppliers →
                    </a>
                </div>
            </div>
        </div>

        <!-- CLT Layups Card -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--clt-warning);">CLT
                            Layups</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $layupCount }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(217, 119, 6, 0.08);">
                        <svg class="w-5 h-5" style="color: var(--clt-warning);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-400">Total layup configurations</p>
                </div>
            </div>
        </div>

        <!-- CLT Layers Card -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--clt-info);">CLT
                            Layers</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $layerCount }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(37, 99, 235, 0.08);">
                        <svg class="w-5 h-5" style="color: var(--clt-info);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-400">Total layer definitions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Suppliers -->
    <div class="card">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Recent Suppliers</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Latest supplier entries</p>
                </div>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
        </div>

        @php
            $recentSuppliers = \App\Models\Supplier::withCount('layups')->latest()->take(5)->get();
        @endphp

        @if ($recentSuppliers->isEmpty())
            <div class="text-center py-10">
                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                </svg>
                <p class="mt-2 text-sm text-gray-500">No suppliers yet.</p>
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm mt-3">Add First Supplier</a>
            </div>
        @else
            <table class="clt-table">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Layups</th>
                        <th>Created</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentSuppliers as $supplier)
                        <tr>
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="avatar-initials sm avatar-color-{{ $supplier->id % 8 }}">
                                        {{ strtoupper(substr($supplier->name, 0, 2)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $supplier->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-active">{{ $supplier->layups_count }}</span>
                            </td>
                            <td class="text-gray-500 text-sm">{{ $supplier->created_at->format('M d, Y') }}</td>
                            <td class="text-right">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn-icon" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
