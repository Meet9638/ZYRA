@extends('layouts.admin')

@section('title', 'Return Request ' . $return->return_number)
@section('page-title', 'Return Details')

@push('styles')
<style>
    .return-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    }
    .return-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--zyra-gold);
        font-family: 'Syne', sans-serif;
    }
    .return-date {
        color: var(--zyra-silver);
        font-size: 0.95rem;
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
    }
    .status-requested { background: rgba(232, 111, 28, 0.2); color: var(--warning); border: 1px solid var(--warning); }
    .status-approved { background: rgba(0, 150, 0, 0.2); color: var(--success); border: 1px solid var(--success); }
    .status-rejected { background: rgba(150, 0, 0, 0.2); color: var(--danger); border: 1px solid var(--danger); }
    .status-completed { background: rgba(212, 175, 55, 0.2); color: var(--zyra-gold); border: 1px solid var(--zyra-gold); }
    
    .grid-2 {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    .info-card {
        background: var(--zyra-charcoal);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 1.5rem;
    }
    .info-card h4 {
        color: var(--zyra-gold);
        margin-bottom: 1rem;
        font-family: 'Syne', sans-serif;
        font-size: 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding-bottom: 0.5rem;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }
    .info-label {
        color: var(--zyra-silver);
    }
    .info-value {
        font-weight: 500;
        color: var(--zyra-white);
    }

    .reason-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px dashed rgba(255, 255, 255, 0.1);
        padding: 1rem;
        border-radius: 6px;
        margin-top: 1rem;
    }

    .admin-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-action {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        font-weight: 700;
        cursor: pointer;
        text-transform: uppercase;
        font-family: 'Inter', sans-serif;
        transition: transform 0.2s, opacity 0.2s;
    }
    .btn-action:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }
    .btn-approve { background: var(--success); color: white; }
    .btn-reject { background: var(--danger); color: white; }
    .btn-complete { background: var(--zyra-gold); color: var(--zyra-black); }

    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.8);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .modal-content {
        background: var(--zyra-charcoal);
        padding: 2rem;
        border-radius: 8px;
        width: 100%;
        max-width: 500px;
        border: 1px solid var(--zyra-gold);
    }
</style>
@endpush

@section('content')
<div class="return-header">
    <div>
        <div class="return-number">{{ $return->return_number }}</div>
        <div class="return-date">Requested on {{ $return->created_at->format('M d, Y') }}</div>
    </div>
    <div class="status-badge status-{{ $return->status }}">
        {{ strtoupper($return->status) }}
    </div>
</div>

<div class="grid-2">
    <!-- Main Issue Details -->
    <div class="info-card">
        <h4>Return Details</h4>
        
        <div style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem;">
            @if($return->orderItem->product->main_image)
                <img src="{{ Storage::url($return->orderItem->product->main_image) }}" style="width: 100px; height: 130px; object-fit: cover; border-radius: 4px;">
            @else
                <div style="width: 100px; height: 130px; background: #333; display:flex; align-items:center; justify-content:center; color:#fff;">NO IMG</div>
            @endif
            
            <div style="flex-grow: 1;">
                <div style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--zyra-white);">{{ $return->orderItem->product->name }}</div>
                <div class="info-row">
                    <span class="info-label">Order Number:</span>
                    <span class="info-value"><a href="{{ route('admin.orders.show', $return->orderItem->order) }}" style="color: var(--zyra-gold);">#{{ $return->orderItem->order->order_number }}</a></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Original Size Purchased:</span>
                    <span class="info-value">{{ strtoupper($return->orderItem->size) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer Preferred Replacement:</span>
                    <span class="info-value" style="color: var(--zyra-gold);">{{ $return->preferred_replacement_size ? strtoupper($return->preferred_replacement_size) : 'Refund / None specified' }}</span>
                </div>
                
                @if($return->orderItem->followed_ai_recommendation)
                    <div style="margin-top: 1rem; font-size: 0.8rem; background: rgba(232, 111, 28, 0.1); color: var(--warning); padding: 0.5rem; border-radius: 4px; border: 1px solid rgba(232, 111, 28, 0.3);">
                        ⚠ WARNING: Customer followed AI Recommendation for this size. Returning this item will impact AI accuracy metrics.
                    </div>
                @endif
            </div>
        </div>

        <h4>Customer Reason</h4>
        <div class="info-row" style="margin-bottom: 0.25rem;">
            <span class="info-label">Categorized As:</span>
            <span class="info-value" style="text-transform: capitalize;">{{ str_replace('_', ' ', $return->reason) }}</span>
        </div>
        
        <div class="reason-box">
            <span style="color: var(--zyra-silver); display: block; margin-bottom: 0.5rem; font-size: 0.85rem; text-transform: uppercase;">Detailed Explanation:</span>
            <span style="color: var(--zyra-white); line-height: 1.6;">
                {{ $return->detailed_reason ?: 'No additional details provided by the customer.' }}
            </span>
        </div>

        @if($return->admin_notes)
            <h4 style="margin-top: 2rem;">Admin Notes</h4>
            <div class="reason-box" style="border-color: var(--zyra-gold);">
                <span style="color: var(--zyra-white);">{{ $return->admin_notes }}</span>
            </div>
        @endif
    </div>

    <!-- Customer Details sidebar -->
    <div>
        <div class="info-card">
            <h4>Customer Profile</h4>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $return->orderItem->order->user->name ?? 'Guest' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $return->orderItem->order->user->email ?? 'N/A' }}</span>
            </div>
        </div>

        @if($return->status === 'requested')
            <div class="admin-actions" style="flex-direction: column;">
                <button type="button" class="btn-action btn-approve" onclick="openActionModal('approve')">Approve Return</button>
                <button type="button" class="btn-action btn-reject" onclick="openActionModal('reject')">Reject Request</button>
            </div>
        @elseif($return->status === 'approved')
            <div class="admin-actions" style="flex-direction: column;">
                <button type="button" class="btn-action btn-complete" onclick="markComplete()">Mark Inventory as Restocked & Complete</button>
            </div>
        @endif
    </div>
</div>

<!-- Modal for Approving/Rejecting with Notes -->
<div class="modal-overlay" id="actionModal">
    <div class="modal-content">
        <h3 id="modalTitle" style="color: var(--zyra-gold); margin-bottom: 1.5rem; font-family: 'Syne', sans-serif;">Action</h3>
        <input type="hidden" id="actionType" value="">
        
        <div style="margin-bottom: 1.5rem;">
            <label style="color: var(--zyra-silver); font-size: 0.85rem; display: block; margin-bottom: 0.5rem; text-transform: uppercase;">Admin Notes (visible to customer if rejected):</label>
            <textarea id="adminNotes" class="form-control" style="width: 100%; min-height: 100px; background: var(--zyra-black); color: var(--zyra-white); border: 1px solid var(--border-color); padding: 1rem; border-radius: 4px;" placeholder="Optional notes for approval, required for rejection..."></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <button class="btn-action" style="background: transparent; border: 1px solid var(--zyra-silver); color: var(--zyra-silver);" onclick="closeModal()">Cancel</button>
            <button class="btn-action btn-approve" id="confirmActionBtn" onclick="submitAction()">Confirm</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const returnId = '{{ $return->id }}';
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function openActionModal(type) {
        document.getElementById('actionModal').style.display = 'flex';
        document.getElementById('actionType').value = type;
        
        let title = document.getElementById('modalTitle');
        let confirmBtn = document.getElementById('confirmActionBtn');
        
        if(type === 'approve') {
            title.innerText = 'Approve Return Request';
            confirmBtn.className = 'btn-action btn-approve';
            confirmBtn.innerText = 'Approve Return';
        } else {
            title.innerText = 'Reject Return Request';
            confirmBtn.className = 'btn-action btn-reject';
            confirmBtn.innerText = 'Reject Return';
        }
    }

    function closeModal() {
        document.getElementById('actionModal').style.display = 'none';
        document.getElementById('adminNotes').value = '';
    }

    function submitAction() {
        const type = document.getElementById('actionType').value;
        const notes = document.getElementById('adminNotes').value;
        const btn = document.getElementById('confirmActionBtn');
        
        if (type === 'reject' && !notes.trim()) {
            alert("Admin notes are required when rejecting a return.");
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Processing...';
        
        fetch(`/admin/returns/${returnId}/${type}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfTokenMeta,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ admin_notes: notes })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                throw new Error(data.message || 'Error executing action');
            }
        })
        .catch(error => {
            alert('Failed: ' + error.message);
            btn.disabled = false;
            btn.innerText = 'Confirm';
        });
    }
    
    function markComplete() {
        if(!confirm("Are you sure? This will mark the return as completed and assume inventory has been received back at the warehouse.")) return;
        
        fetch(`/admin/returns/${returnId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfTokenMeta,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                throw new Error(data.message || 'Error executing action');
            }
        })
        .catch(error => {
            alert('Failed: ' + error.message);
        });
    }
</script>
@endpush
@endsection
