@extends('layouts.admin')

@section('page-title', 'Orders Management')

@section('content')
<div class="admin-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h2>All Orders</h2>
    <div style="display: flex; gap: 1rem;">
        <button type="button" class="btn-primary" style="background: var(--danger); border-color: var(--danger);" onclick="submitMassDelete('order')">
            🗑️ Delete Selected
        </button>
    </div>
</div>

<form id="massDeleteForm" method="POST" style="display: none;">
    @csrf
</form>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes(this)"></th>
                <th>Order Number</th>
                <th>User Name</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td style="text-align: center;">
                        <input type="checkbox" class="row-checkbox" value="{{ $order->id }}">
                    </td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" style="color: var(--zyra-gold); font-weight: 500;">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td>{{ $order->user->name }}</td>
                    <td>₹{{ $order->total_amount }}</td>
                    <td>
                        <span style="padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 0.8rem; background: var(--zyra-charcoal); border: 1px solid var(--border-color);">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.8rem; text-decoration: none; border-radius: 4px; border: 1px solid var(--border-color); color: var(--zyra-white);">View</a>
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to completely delete this order? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="padding: 0.4rem 1rem; font-size: 0.8rem; text-decoration: none; border-radius: 4px; border: 1px solid var(--danger); background: rgba(239, 68, 68, 0.1); color: var(--danger); cursor: pointer; transition: all 0.2s;">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--muted-text); padding: 2rem;">No orders found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

@push('scripts')
<script>
    function toggleAllCheckboxes(source) {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function submitMassDelete(modelType) {
        let selected = [];
        document.querySelectorAll('.row-checkbox:checked').forEach(cb => selected.push(cb.value));
        
        if (selected.length === 0) {
            alert('Please select at least one item to delete.');
            return;
        }
        
        if (confirm('Are you sure you want to permanently delete the ' + selected.length + ' selected item(s)?')) {
            let form = document.getElementById('massDeleteForm');
            form.action = '/admin/mass-destroy/' + modelType;
            
            selected.forEach(id => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            form.submit();
        }
    }
</script>
@endpush
@endsection
