@extends('layouts.admin')

@section('page-title', 'Returns Management')

@section('content')
<div class="admin-header">
    <h2>Returns</h2>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Return #</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $return)
                <tr>
                    <td>
                        <a href="{{ route('admin.returns.show', $return) }}" style="color: var(--zyra-gold); font-weight: 500;">
                            {{ $return->return_number }}
                        </a>
                    </td>
                    <td>{{ $return->reason }}</td>
                    <td>
                        <span style="padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 0.8rem; background: var(--zyra-charcoal); border: 1px solid var(--border-color);">
                            {{ ucfirst($return->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.returns.show', $return) }}" class="btn-secondary" style="padding: 0.3rem 0.8rem; font-size: 0.8rem;">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--muted-text); padding: 2rem;">No returns found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
