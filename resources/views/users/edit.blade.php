@extends('layouts.app')

@section('title', 'Edit Profile - ZYRA')

@push('head-scripts')
<style>
    .edit-profile-card {
        background: var(--zyra-charcoal);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }
    .edit-profile-header {
        margin-bottom: 2rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-color);
    }
    .edit-profile-header h3 {
        font-size: 1.7rem;
        margin: 0;
        color: var(--zyra-white);
        font-family: 'Syne', sans-serif;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    @media (min-width: 768px) {
        .form-row {
            grid-template-columns: 1fr 1fr;
        }
    }
    .form-group label {
        display: block;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--zyra-silver);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="dashboard-grid">
        <!-- Sidebar Navigation -->
        <div class="dashboard-sidebar">
            <a href="{{ route('users.profile') }}">
                👤 Profile Overview
            </a>
            <a href="{{ route('users.edit') }}" class="active">
                ✏️ Edit Profile
            </a>
            <a href="{{ route('users.orders') }}">
                📦 My Orders
            </a>
            <a href="{{ route('returns.index') }}">
                ↩️ Returns
            </a>
        </div>

        <!-- Main Content -->
        <div>
            <div class="edit-profile-card">
                <div class="edit-profile-header">
                    <h3>Edit Profile Details</h3>
                </div>

                <form method="POST" action="{{ route('users.update') }}">
                    @csrf

                    @if($errors->any())
                        <div style="background: rgba(220,53,69,0.1); border: 1px solid #dc3545; border-radius: 8px; padding: 1rem 1.5rem; margin-bottom: 1.5rem;">
                            <strong style="color: #dc3545;">Please fix the following errors:</strong>
                            <ul style="margin: 0.5rem 0 0; padding-left: 1.25rem; color: #dc3545;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}" placeholder="+91 0000000000">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', auth()->user()->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', auth()->user()->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', auth()->user()->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label>Shipping Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Enter your full shipping address here...">{{ old('address', auth()->user()->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mt-4 pt-4" style="border-top: 1px solid var(--border-color);">
                        <button type="submit" class="btn btn-primary" style="min-width: 200px;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
