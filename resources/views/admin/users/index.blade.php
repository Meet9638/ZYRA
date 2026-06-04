@extends('layouts.admin')

@section('page-title', 'Users')

@section('content')



{{-- ── Page header ── --}}
<div class="page-header">
    <div>
        <h2>Users</h2>
        <p>Manage and oversee all registered accounts</p>
    </div>
    <div class="search-wrap">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" class="search-input" id="userSearch"
            placeholder="Search users…" oninput="filterUsers(this.value)">
    </div>
</div>

{{-- ── Stats strip ── --}}
{{-- Pass $stats from controller: ['total','verified','admins','banned'] --}}
@php
    $s = $stats ?? [
        'total'    => $users->total() ?? count($users),
        'verified' => 0,
        'admins'   => 0,
        'banned'   => 0,
    ];
@endphp

<div class="stats-strip">
    <div class="stat-card">
        <div class="stat-icon auto-style-0100"  >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d4af37" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
            <div class="stat-val auto-style-0101"  >{{ $s['total'] }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon auto-style-0102"  >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <div class="stat-val auto-style-0103"  >{{ $s['verified'] }}</div>
            <div class="stat-label">Verified</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon auto-style-0104"  >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        {{-- <div>
            <div class="stat-val auto-style-0105"  >{{ $s['admins'] }}</div>
            <div class="stat-label">Admins</div>
        </div> --}}
    </div>

    <div class="stat-card">
        <div class="stat-icon auto-style-0106"  >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
        </div>
        {{-- <div>
            <div class="stat-val auto-style-0107"  >{{ $s['banned'] }}</div>
            <div class="stat-label">Banned</div>
        </div> --}}
    </div>
</div>

{{-- ── Table ── --}}
<div class="tbl-wrap">
    <table class="users-table" id="usersTable">
        <colgroup>
            <col class="c-num">
            <col class="c-user">
            <col class="c-email">
            <col class="c-role">
            <col class="c-status">
            <col class="c-joined">
            <col class="c-action">
        </colgroup>
        <thead>
            <tr>
                <th   class="auto-style-0108">#</th>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $i => $user)
            @php
                $initials = collect(explode(' ', $user->name))
                                ->map(fn($w) => strtoupper($w[0] ?? ''))
                                ->filter()->take(2)->implode('');
                $palette  = ['#b45309','#0369a1','#7c3aed','#0f766e','#be185d','#15803d'];
                $color    = $palette[abs(crc32($user->email)) % count($palette)];
                $isAdmin  = ($user->role ?? '') === 'admin';
                $isBanned = !empty($user->banned_at);
                $verified = (bool) $user->email_verified_at;
                $rowNum   = $users instanceof \Illuminate\Pagination\LengthAwarePaginator
                            ? $users->firstItem() + $i
                            : $i + 1;
            @endphp
            <tr data-name="{{ strtolower($user->name) }}"
                data-email="{{ strtolower($user->email) }}">

                {{-- # --}}
                <td class="row-num">{{ $rowNum }}</td>

                {{-- User --}}
                <td>
                    <div class="user-cell">
                        <div class="avatar"
                            style="background:{{ $color }}22;color:{{ $color }};border-color:{{ $color }}55;">
                            {{ $initials }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-sub">
                                @if($verified)
                                    <span class="dot auto-style-0109"  ></span>
                                    <span   class="auto-style-0103">Verified</span>
                                @else
                                    <span class="dot auto-style-0110"  ></span>
                                    <span   class="auto-style-0111">Unverified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>

                {{-- Email --}}
                <td   class="auto-style-0112">
                    {{ $user->email }}
                </td>

                {{-- Role --}}
                <td>
                    @if($isAdmin)
                        <span class="badge b-admin">
                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Admin
                        </span>
                    @else
                        <span class="badge b-user">
                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M6 20v-1a6 6 0 0 1 12 0v1"/></svg>
                            User
                        </span>
                    @endif
                </td>

                {{-- Status --}}
                <td>
                    @if($isBanned)
                        <span class="badge b-banned">
                            <span   class="auto-style-0113"></span>
                            Banned
                        </span>
                    @else
                        <span class="badge b-active">
                            <span   class="auto-style-0114"></span>
                            Active
                        </span>
                    @endif
                </td>

                {{-- Joined --}}
                <td   class="auto-style-0115">
                    {{ $user->created_at->format('d M Y') }}
                </td>

                {{-- Actions --}}
                <td>
                    <div class="act-wrap">
                        <a href="{{ route('admin.users.show', $user) }}" class="act-btn act-view">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            View
                        </a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                            class="delete-user-form"
                            data-username="{{ addslashes($user->name) }}"
                            style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="act-btn act-del">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14H6L5 6"/>
                                    <path d="M10 11v6m4-6v6"/>
                                    <path d="M9 6V4h6v2"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <span class="ei">👤</span>
                        No users found
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages())
        <div class="pag-wrap">{{ $users->links() }}</div>
    @endif
</div>

<script>
function filterUsers(q) {
    q = q.toLowerCase().trim();
    document.querySelectorAll('#usersTable tbody tr[data-name]').forEach(row => {
        const hit = row.dataset.name.includes(q) || row.dataset.email.includes(q);
        row.style.display = hit ? '' : 'none';
    });
}


<style>
/* Custom Delete Modal */
.custom-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.custom-modal-backdrop.active {
    opacity: 1;
    pointer-events: auto;
}

.custom-modal-content {
    background: var(--zyra-charcoal, #111);
    border: 1px solid rgba(212, 175, 55, 0.2);
    padding: 2rem;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    text-align: center;
    transform: translateY(20px) scale(0.95);
    transition: all 0.3s ease;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}

.custom-modal-backdrop.active .custom-modal-content {
    transform: translateY(0) scale(1);
}

.custom-modal-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #f87171;
}

.custom-modal-title {
    font-family: 'Syne', sans-serif;
    font-size: 1.5rem;
    color: var(--zyra-gold, #d4af37);
    margin-bottom: 0.5rem;
}

.custom-modal-text {
    color: var(--zyra-silver, #888);
    font-size: 0.95rem;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

.custom-modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn-cancel, .btn-confirm {
    padding: 0.75rem 1.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-radius: 4px;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
}

.btn-cancel {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
}

.btn-cancel:hover {
    background: rgba(255, 255, 255, 0.2);
}

.btn-confirm {
    background: #f87171;
    color: #fff;
}

.btn-confirm:hover {
    background: #ef4444;
    box-shadow: 0 0 15px rgba(248, 113, 113, 0.4);
}
</style>

<!-- Modal HTML -->
<div class="custom-modal-backdrop" id="deleteModal">
    <div class="custom-modal-content">
        <div class="custom-modal-icon">⚠️</div>
        <h3 class="custom-modal-title">Confirm Deletion</h3>
        <p class="custom-modal-text" id="deleteModalText">Are you sure you want to delete this?</p>
        <div class="custom-modal-actions">
            <button class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button class="btn-confirm" onclick="confirmDelete()">Delete User</button>
        </div>
    </div>
</div>

<script>
// Modal Logic
let currentDeleteForm = null;
const modal = document.getElementById('deleteModal');
const modalText = document.getElementById('deleteModalText');

document.querySelectorAll('.delete-user-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        currentDeleteForm = this;
        const username = this.dataset.username;
        modalText.textContent = `Delete ${username}? This cannot be undone.`;
        modal.classList.add('active');
    });
});

function closeDeleteModal() {
    modal.classList.remove('active');
    currentDeleteForm = null;
}

function confirmDelete() {
    if (currentDeleteForm) {
        HTMLFormElement.prototype.submit.call(currentDeleteForm);
    }
}
</script>

@endsection
