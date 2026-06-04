@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')
<div class="admin-table-container">
    <div class="admin-card">
        <div class="admin-card-header">
            <h1 class="admin-card-title">Site Settings</h1>
        </div>

        <div class="admin-card-body" style="padding: 1.5rem;">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="form-section">
                @csrf
                @method('PATCH')

                <!-- Site Information -->
                <div class="section-title">Site Information</div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="site_name" class="lbl">
                            Site Name <span class="req">*</span>
                        </label>
                        <input type="text" id="site_name" name="site_name"
                                value="{{ old('site_name', $settings['site_name'] ?? '') }}"
                                class="form-control"
                                required>
                        @error('site_name')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_email" class="lbl">
                            Contact Email
                        </label>
                        <input type="email" id="contact_email" name="contact_email"
                                value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                                class="form-control">
                        @error('contact_email')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_phone" class="lbl">
                            Contact Phone
                        </label>
                        <input type="text" id="contact_phone" name="contact_phone"
                                value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                                class="form-control">
                        @error('contact_phone')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="currency" class="lbl">
                            Currency <span class="req">*</span>
                        </label>
                        <select id="currency" name="currency" class="form-control" required>
                            <option value="INR" {{ ($settings['currency'] ?? 'INR') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                            <option value="USD" {{ ($settings['currency'] ?? 'INR') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="EUR" {{ ($settings['currency'] ?? 'INR') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            <option value="GBP" {{ ($settings['currency'] ?? 'INR') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                        </select>
                        @error('currency')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="site_description" class="lbl">
                        Site Description
                    </label>
                    <textarea id="site_description" name="site_description" rows="3"
                                class="form-control">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                    @error('site_description')
                        <div class="err">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mt-3">
                    <label for="address" class="lbl">
                        Address
                    </label>
                    <textarea id="address" name="address" rows="2"
                                class="form-control">{{ old('address', $settings['address'] ?? '') }}</textarea>
                    @error('address')
                        <div class="err">{{ $message }}</div>
                    @enderror
                </div>

                <!-- E-commerce Settings -->
                <div class="section-title mt-5">E-commerce Settings</div>

                <div class="form-row cols-3">
                    <div class="form-group">
                        <label for="tax_rate" class="lbl">
                            Tax Rate (%)
                        </label>
                        <input type="number" id="tax_rate" name="tax_rate" step="0.01" min="0" max="100"
                                value="{{ old('tax_rate', $settings['tax_rate'] ?? '') }}"
                                class="form-control">
                        @error('tax_rate')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="shipping_fee" class="lbl">
                            Shipping Fee
                        </label>
                        <input type="number" id="shipping_fee" name="shipping_fee" step="0.01" min="0"
                                value="{{ old('shipping_fee', $settings['shipping_fee'] ?? '') }}"
                                class="form-control">
                        @error('shipping_fee')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="free_shipping_threshold" class="lbl">
                            Free Shipping Threshold
                        </label>
                        <input type="number" id="free_shipping_threshold" name="free_shipping_threshold" step="0.01" min="0"
                                value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? '') }}"
                                class="form-control">
                        @error('free_shipping_threshold')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- AI Statistics Settings -->
                <div class="section-title mt-5">AI Statistics (Displayed on homepage)</div>

                <div class="form-row cols-3">
                    <div class="form-group">
                        <label for="accuracy_rate" class="lbl">
                            Accuracy Rate
                        </label>
                        <input type="text" id="accuracy_rate" name="accuracy_rate"
                                value="{{ old('accuracy_rate', $settings['accuracy_rate'] ?? '94%') }}"
                                class="form-control"
                                placeholder="E.g: 94%">
                        @error('accuracy_rate')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="fewer_returns" class="lbl">
                            Fewer Returns
                        </label>
                        <input type="text" id="fewer_returns" name="fewer_returns"
                                value="{{ old('fewer_returns', $settings['fewer_returns'] ?? '67%') }}"
                                class="form-control"
                                placeholder="E.g: 67%">
                        @error('fewer_returns')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="happy_customers" class="lbl">
                            Happy Customers
                        </label>
                        <input type="text" id="happy_customers" name="happy_customers"
                                value="{{ old('happy_customers', $settings['happy_customers'] ?? '10K+') }}"
                                class="form-control"
                                placeholder="E.g: 10K+">
                        @error('happy_customers')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="data_points" class="lbl">
                            Data Points Analyzed
                        </label>
                        <input type="text" id="data_points" name="data_points"
                                value="{{ old('data_points', $settings['data_points'] ?? '2.4M') }}"
                                class="form-control"
                                placeholder="E.g: 2.4M">
                        @error('data_points')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-actions mt-4">
                    <button type="submit" class="btn-primary">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
