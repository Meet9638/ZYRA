@extends('layouts.admin')

@section('page-title', 'Add New Product')

@section('content')



{{-- Breadcrumb --}}
<div   class="auto-style-0052">
    <a href="{{ route('admin.products.index') }}"   class="auto-style-0053">Products</a>
    <span>›</span>
    <span>Add New Product</span>
</div>

@if($errors->any())
    <div   class="auto-style-0054">
        <strong>Please fix the following errors:</strong>
        <ul   class="auto-style-0055">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
@csrf

<div class="create-layout">

    {{-- ═══════════ LEFT COLUMN ═══════════ --}}
    <div>

        {{-- ── 1. PRODUCT INFO ── --}}
        <div class="form-section">
            <div class="section-title">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/>
                </svg>
                Product Information
            </div>

            {{-- Name --}}
            <div class="form-group">
                <label class="lbl" for="name">Product Name <span class="req">*</span></label>
                <input type="text" id="name" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="e.g. Classic Oxford Shirt"
                    value="{{ old('name') }}" maxlength="200" required
                    oninput="autoSlug(this.value)">
                @error('name')<div class="err">{{ $message }}</div>@enderror
            </div>

            {{-- Category + Gender --}}
            <div class="form-row auto-style-0056"  >
                <div class="form-group auto-style-0057"  >
                    <label class="lbl" for="category_id">Category <span class="req">*</span></label>
                    <select id="category_id" name="category_id"
                        class="form-control @error('category_id') is-invalid @enderror" required>
                        <option value="">— Select Category —</option>
                        @foreach(\App\Models\Category::orderBy('name')->get() as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="err">{{ $message }}</div>@enderror
                </div>

                <div class="form-group auto-style-0057"  >
                    <label class="lbl" for="gender">Gender <span class="req">*</span></label>
                    <select id="gender" name="gender"
                        class="form-control @error('gender') is-invalid @enderror" required>
                        <option value="">— Select —</option>
                        <option value="men"    {{ old('gender')=='men'    ? 'selected':'' }}>Men</option>
                        <option value="women"  {{ old('gender')=='women'  ? 'selected':'' }}>Women</option>
                        <option value="unisex" {{ old('gender')=='unisex' ? 'selected':'' }}>Unisex</option>
                        <option value="kids"   {{ old('gender')=='kids'   ? 'selected':'' }}>Kids</option>
                    </select>
                    @error('gender')<div class="err">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- SKU + Slug --}}
            <div class="form-row auto-style-0056"  >
                <div class="form-group auto-style-0057"  >
                    <label class="lbl" for="sku">SKU</label>
                    <input type="text" id="sku" name="sku"
                        class="form-control @error('sku') is-invalid @enderror"
                        placeholder="e.g. OXF-BLK-M-001"
                        value="{{ old('sku') }}"  >
                    <div class="hint">Leave blank to auto-generate</div>
                    @error('sku')<div class="err">{{ $message }}</div>@enderror
                </div>

                <div class="form-group auto-style-0057"  >
                    <label class="lbl" for="slug">URL Slug</label>
                    <input type="text" id="slug" name="slug"
                        class="form-control @error('slug') is-invalid @enderror"
                        placeholder="auto-from-name"
                        value="{{ old('slug') }}"  >
                    @error('slug')<div class="err">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Short description --}}
            <div class="form-group">
                <label class="lbl" for="short_description">Short Description</label>
                <input type="text" id="short_description" name="short_description"
                    class="form-control @error('short_description') is-invalid @enderror"
                    placeholder="Brief summary shown in listings (max 160 chars)"
                    value="{{ old('short_description') }}" maxlength="160">
                @error('short_description')<div class="err">{{ $message }}</div>@enderror
            </div>

            {{-- Full description --}}
            <div class="form-group">
                <label class="lbl" for="description">Full Description</label>
                <textarea id="description" name="description"
                    class="form-control @error('description') is-invalid @enderror"
                    placeholder="Detailed product description, materials, care instructions…"
                    maxlength="3000"
                    oninput="document.getElementById('dcnt').textContent=this.value.length+'/3000'"
                >{{ old('description') }}</textarea>
                <div   class="auto-style-0059">
                    <span class="hint" id="dcnt">0/3000</span>
                </div>
                @error('description')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- ── 2. PRICING & STOCK ── --}}
        <div class="form-section">
            <div class="section-title">
                <svg width="13" height="13" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 5h-11h3a4 4 0 0 1 0 8h-3l6 6" />
                    <line x1="7" y1="9" x2="18" y2="9" />
                </svg>
                Pricing &amp; Stock
            </div>

            <div class="form-row cols-3 auto-style-0056"  >
                <div class="form-group auto-style-0057"  >
                    <label class="lbl" for="price">Price <span class="req">*</span></label>
                    <div class="input-wrap">
                        <span class="pfx">₹</span>
                        <input type="number" id="price" name="price"
                            class="form-control @error('price') is-invalid @enderror"
                            placeholder="0.00" value="{{ old('price') }}" min="0" step="0.01" required>
                    </div>
                    @error('price')<div class="err">{{ $message }}</div>@enderror
                </div>

                <div class="form-group auto-style-0057"  >
                    <label class="lbl">
                        <input type="checkbox" id="on_sale" name="on_sale" value="1" {{ old('on_sale') ? 'checked' : '' }}>
                        Enable Sale
                    </label>
                </div>

                <div class="form-group auto-style-0057"  >
                    <label class="lbl" for="discount_price">Discount Price</label>
                    <div class="input-wrap">
                        <span class="pfx">₹</span>
                        <input type="number" id="discount_price" name="discount_price"
                            class="form-control @error('discount_price') is-invalid @enderror"
                            placeholder="0.00" value="{{ old('discount_price') }}" min="0" step="0.01">
                    </div>
                    @error('discount_price')<div class="err">{{ $message }}</div>@enderror
                </div>

                <div class="form-group auto-style-0057"  >
                    <label class="lbl" for="cost_price">Cost Price</label>
                    <div class="input-wrap">
                        <span class="pfx">₹</span>
                        <input type="number" id="cost_price" name="cost_price"
                            class="form-control"
                            placeholder="0.00" value="{{ old('cost_price') }}" min="0" step="0.01">
                    </div>
                </div>
            </div>

            <div class="form-row cols-3">
                <div class="form-group auto-style-0057"  >
                    <label class="lbl" for="stock_quantity">Stock Qty <span class="req">*</span></label>
                    <input type="number" id="stock_quantity" name="stock_quantity"
                        class="form-control @error('stock_quantity') is-invalid @enderror"
                        placeholder="0" value="{{ old('stock_quantity', 0) }}" min="0" required>
                    @error('stock_quantity')<div class="err">{{ $message }}</div>@enderror
                </div>

                <div class="form-group auto-style-0057"  >
                    <label class="lbl" for="low_stock_threshold">Low-Stock Alert</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold"
                        class="form-control"
                        placeholder="10" value="{{ old('low_stock_threshold', 10) }}" min="0">
                    <div class="hint">Warn when qty drops below this</div>
                </div>
            </div>
        </div>

        {{-- ── 3. SIZES ── --}}
        <div class="form-section">
            <div class="section-title">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                </svg>
                Sizes
            </div>
            <div class="form-group auto-style-0057"  >
                <label class="lbl">Available Sizes</label>
                <div class="size-chips auto-style-0060"  >
                    @foreach(['XS','S','M','L','XL','XXL','XXXL','28','30','32','34','36','38','40'] as $s)
                        <input class="size-chip" type="checkbox" name="sizes[]"
                            value="{{ $s }}" id="sz_{{ $s }}"
                            {{ in_array($s, old('sizes',[])) ? 'checked' : '' }}>
                        <label for="sz_{{ $s }}">{{ $s }}</label>
                    @endforeach
                </div>
            </div>
        </div>

    </div>{{-- /left --}}

    {{-- ═══════════ RIGHT COLUMN ═══════════ --}}
    <div>

        {{-- ── IMAGES ── --}}
        <div class="form-section">
            <div class="section-title">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                Product Images
            </div>

            {{-- Main image --}}
            <div class="form-group">
                <label class="lbl" for="main_image">Main Image <span class="req">*</span></label>
                <div class="upload-zone" id="main-zone">
                    <input type="file" id="main_image" name="main_image"
                        accept="image/*" onchange="previewMain(this)">
                    <div class="uicon">🖼</div>
                    <p><span>Click to upload</span> or drag &amp; drop</p>
                    <small>JPG, PNG, WEBP · max 2 MB</small>
                </div>
                <div id="main-preview"   class="auto-style-0061">
                    <img id="main-preview-img" src="" alt="Main"   class="auto-style-0062">
                    <span   class="auto-style-0063">Main</span>
                    <button type="button" onclick="clearMain()"
                          class="auto-style-0064">✕</button>
                </div>
                @error('main_image')<div class="err">{{ $message }}</div>@enderror
            </div>

            {{-- Gallery --}}
            <div class="form-group auto-style-0057"  >
                <label class="lbl" for="gallery_images">Gallery Images</label>
                <div class="upload-zone" id="gallery-zone">
                    <input type="file" id="additional_images" name="additional_images[]"
                        accept="image/*" multiple onchange="previewGallery(this)">
                    <div class="uicon">📷</div>
                    <p><span>Click to upload</span> multiple images</p>
                    <small>JPG, PNG, WEBP · max 2 MB each</small>
                </div>
                <div id="preview-grid"></div>
                @error('additional_images.*')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- ── STATUS ── --}}
        <div class="form-section">
            <div class="section-title">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Status &amp; Visibility
            </div>

            {{-- Active --}}
            <div class="form-group">
                <label class="lbl">Listing Status <span class="req">*</span></label>
                <div class="toggle-row auto-style-0065"  >
                    <label class="toggle" for="is_active">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                            {{ old('is_active', 1) ? 'checked' : '' }}>
                        <div class="toggle-track"></div>
                    </label>
                    <label class="toggle-lbl" for="is_active">
                        <span id="active-label">{{ old('is_active', 1) ? 'Active' : 'Inactive' }}</span>
                        <span   class="auto-style-0066">Visible on the storefront</span>
                    </label>
                </div>
            </div>

            {{-- Featured --}}
            <div class="form-group auto-style-0057"  >
                <label class="lbl">Featured Product</label>
                <div class="toggle-row auto-style-0065"  >
                    <label class="toggle" for="is_featured">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1"
                            {{ old('is_featured') ? 'checked' : '' }}>
                        <div class="toggle-track"></div>
                    </label>
                    <label class="toggle-lbl" for="is_featured">
                        Featured
                        <span   class="auto-style-0066">Show in featured sections</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ── SEO ── --}}
        <div class="form-section">
            <div class="section-title">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                SEO (optional)
            </div>

            <div class="form-group">
                <label class="lbl" for="meta_title">Meta Title</label>
                <input type="text" id="meta_title" name="meta_title"
                    class="form-control"
                    placeholder="Page title for search engines"
                    value="{{ old('meta_title') }}" maxlength="70">
                <div class="hint">Max 70 characters</div>
            </div>

            <div class="form-group auto-style-0057"  >
                <label class="lbl" for="meta_description">Meta Description</label>
                <textarea id="meta_description" name="meta_description"
                    class="form-control auto-style-0067"  
                    placeholder="Short description for search engines…"
                    maxlength="160">{{ old('meta_description') }}</textarea>
                <div class="hint">Max 160 characters</div>
            </div>
        </div>

    </div>{{-- /right --}}
</div>

{{-- ── ACTION BAR ── --}}
<div class="form-actions">
    <a href="{{ route('admin.products.index') }}"
          class="auto-style-0068">
        ← Back to Products
    </a>
    <div   class="auto-style-0069">
        <button type="submit" name="save_action" value="save_and_add"
              class="auto-style-0070">
            Save &amp; Add Another
        </button>
        <button type="submit" name="save_action" value="save"
            class="btn-primary auto-style-0071"  >
            Save Product
        </button>
    </div>
</div>

</form>
@endsection

@push('scripts')
<script>
/* Auto-slug */
function autoSlug(val) {
    const el = document.getElementById('slug');
    if (el.dataset.manual) return;
    el.value = val.toLowerCase().replace(/[^a-z0-9\s-]/g,'').trim().replace(/\s+/g,'-');
}
document.getElementById('slug').addEventListener('input', function(){ this.dataset.manual = '1'; });

/* Main image preview */
function previewMain(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('main-preview-img').src = e.target.result;
        document.getElementById('main-preview').style.display = 'block';
        document.getElementById('main-zone').style.display   = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}
function clearMain() {
    document.getElementById('main_image').value  = '';
    document.getElementById('main-preview').style.display = 'none';
    document.getElementById('main-zone').style.display    = 'block';
}

/* Gallery preview */
function previewGallery(input) {
    const grid = document.getElementById('preview-grid');
    if (!input.files) return;
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.className = 'prev-thumb';
            wrap.innerHTML = `<img src="${e.target.result}" alt=""><button type="button" class="del-btn" onclick="this.closest('.prev-thumb').remove()">✕</button>`;
            grid.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}

/* Active toggle label */
document.getElementById('is_active').addEventListener('change', function(){
    document.getElementById('active-label').textContent = this.checked ? 'Active' : 'Inactive';
});

/* Price validation */
function validatePrices() {
    const price = parseFloat(document.getElementById('price').value) || 0;
    const discountPrice = parseFloat(document.getElementById('discount_price').value) || 0;
    const onSale = document.getElementById('on_sale').checked;
    
    if (onSale && discountPrice >= price && discountPrice > 0) {
        alert('Discount price must be less than the original price when sale is enabled.');
        document.getElementById('discount_price').focus();
        return false;
    }
    
    if (discountPrice < 0) {
        alert('Discount price cannot be negative.');
        document.getElementById('discount_price').focus();
        return false;
    }
    
    return true;
}

document.getElementById('price').addEventListener('input', validatePrices);
document.getElementById('discount_price').addEventListener('input', validatePrices);
document.getElementById('on_sale').addEventListener('change', validatePrices);

// Form submission validation
document.querySelector('form').addEventListener('submit', function(e) {
    if (!validatePrices()) {
        e.preventDefault();
    }
});

/* Drag-over highlight */
document.querySelectorAll('.upload-zone').forEach(z => {
    z.addEventListener('dragover',  e => { e.preventDefault(); z.classList.add('over'); });
    z.addEventListener('dragleave', ()=> z.classList.remove('over'));
    z.addEventListener('drop',      ()=> z.classList.remove('over'));
});
</script>
@endpush
