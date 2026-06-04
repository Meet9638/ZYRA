@extends('layouts.app')

@section('title', $product->name . ' - ZYRA')

@section('content')
<div class="container auto-style-0135"  >
    <!-- Breadcrumbs -->
    <div   class="auto-style-0236">
        <a href="{{ route('home') }}"   class="auto-style-0237">Home</a>
        <span   class="auto-style-0238">/</span>
        <a href="{{ route('shop') }}"   class="auto-style-0237">Shop</a>
        <span   class="auto-style-0238">/</span>
        <a href="{{ route('categories.show', $product->category->slug) }}" style="color: var(--zyra-silver); text-decoration: none;">{{ $product->category->name }}</a>
        <span   class="auto-style-0238">/</span>
        <span   class="auto-style-0149">{{ $product->name }}</span>
    </div>

    <div   class="auto-style-0239">
        <!-- Product Images -->
        <div>
            <div   class="auto-style-0240">
                <img id="mainImage" src="{{ Storage::url($product->main_image) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            
            @if($product->additional_images && count(json_decode($product->additional_images)) > 0)
                <div   class="auto-style-0241">
                    <div onclick="changeImage('{{ Storage::url($product->main_image) }}')" style="height: 120px; background: var(--zyra-gray); cursor: pointer; border: 2px solid var(--zyra-gold); overflow: hidden;">
                        <img src="{{ Storage::url($product->main_image) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    @foreach(json_decode($product->additional_images) as $image)
                        <div onclick="changeImage('{{ Storage::url($image) }}')"   onmouseover="this.style.borderColor='var(--zyra-gold)'" onmouseout="this.style.borderColor='transparent'" class="auto-style-0243">
                            <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div>
            <div   class="auto-style-0244">
                {{ $product->category->name }} • {{ ucfirst($product->gender) }}
            </div>
            
            <h1   class="auto-style-0245">
                {{ $product->name }}
            </h1>

            @if($product->is_featured)
                <div   class="auto-style-0246">
                    ? Featured Product
                </div>
            @endif

            <div   class="auto-style-0247">
                @if($product->active_price)
                    <span   class="auto-style-0248">&#8377;{{ number_format($product->price, 2) }}</span>
                    &#8377;{{ number_format($product->active_price, 2) }}
                    <span   class="auto-style-0249">
                        Save {{ round((($product->price - $product->active_price) / $product->price) * 100) }}%
                    </span>
                @else
                    &#8377;{{ number_format($product->price, 2) }}
                @endif
            </div>

            <!-- AI Recommendation -->
            @auth
                @if($recommendation)
                    <div   class="auto-style-0250">
                        <h3   class="auto-style-0251">
                            ?? AI Size Recommendation
                        </h3>
                        <p   class="auto-style-0252">
                            {{ $recommendation['reasoning'] }}
                        </p>
                        <div   class="auto-style-0253">
                            <div>
                                <div   class="auto-style-0254">Recommended Size</div>
                                <div   class="auto-style-0097">{{ $recommendation['recommended_size'] }}</div>
                            </div>
                            <div>
                                <div   class="auto-style-0254">Confidence</div>
                                <div   class="auto-style-0255">{{ $recommendation['confidence'] }}%</div>
                            </div>
                        </div>
                        <div   class="auto-style-0256">
                            <div   class="auto-style-0257">
                                <div style="height: 100%; width: {{ $recommendation['confidence'] }}%; background: linear-gradient(90deg, var(--zyra-gold), var(--zyra-rose)); transition: width 0.5s ease;"></div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div   class="auto-style-0258">
                    <p   class="auto-style-0148">
                        ?? <strong   class="auto-style-0134">Get AI Size Recommendations!</strong>
                    </p>
                    <a href="{{ route('login') }}"   class="auto-style-0259">
                        Login to see personalized size suggestions ?
                    </a>
                </div>
            @endauth

            <!-- Add to Cart Form -->
            <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <!-- Size Selection -->
                <div   class="auto-style-0260">
                    <h4   class="auto-style-0261">
                        Select Size:
                    </h4>
                    <div   class="auto-style-0262">
                        @foreach($product->productSizes as $size)
                            <label   class="auto-style-0263">
                                <input type="radio" name="size" value="{{ $size->size }}" required 
                                       {{ $recommendation && $size->size === $recommendation['recommended_size'] ? 'checked' : '' }}
                                       style="position: absolute; opacity: 0;">
                                <div class="size-button" style="padding: 1rem 1.5rem; border: 2px solid {{ $recommendation && $size->size === $recommendation['recommended_size'] ? 'var(--zyra-gold)' : 'var(--zyra-silver)' }}; background: {{ $recommendation && $size->size === $recommendation['recommended_size'] ? 'rgba(212, 175, 55, 0.1)' : 'transparent' }}; cursor: pointer; transition: all 0.3s ease; font-weight: {{ $recommendation && $size->size === $recommendation['recommended_size'] ? '600' : '400' }}; position: relative;">
                                    {{ $size->size }}
                                    @if($recommendation && $size->size === $recommendation['recommended_size'])
                                        <div   class="auto-style-0265">?</div>
                                    @endif
                                    @if($size->stock_quantity <= 0)
                                        <div   class="auto-style-0266">OUT</div>
                                    @elseif($size->stock_quantity < 5)
                                        <div   class="auto-style-0267">{{ $size->stock_quantity }} left</div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Quantity -->
                <div   class="auto-style-0260">
                    <h4   class="auto-style-0261">
                        Quantity:
                    </h4>
                    <div   class="auto-style-0268">
                        <button type="button" onclick="decreaseQuantity()"   class="auto-style-0269">-</button>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" required   class="auto-style-0270">
                        <button type="button" onclick="increaseQuantity()"   class="auto-style-0269">+</button>
                    </div>
                </div>

                <!-- Stock Status -->
                @if($product->stock_quantity > 0)
                    <div   class="auto-style-0271">
                        ? In Stock ({{ $product->stock_quantity }} available)
                    </div>
                @else
                    <div   class="auto-style-0272">
                        ? Out of Stock
                    </div>
                @endif

                <!-- Add to Cart Button -->
                <button type="submit" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }} style="width: 100%; padding: 1.2rem; background: {{ $product->stock_quantity > 0 ? 'linear-gradient(135deg, var(--zyra-gold), var(--zyra-rose))' : 'var(--zyra-gray)' }}; color: {{ $product->stock_quantity > 0 ? 'var(--zyra-black)' : 'var(--zyra-silver)' }}; border: none; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; font-size: 1rem; cursor: {{ $product->stock_quantity > 0 ? 'pointer' : 'not-allowed' }}; transition: all 0.3s ease; border-radius: 4px; margin-bottom: 1rem;">
                    {{ $product->stock_quantity > 0 ? 'Add to Cart' : 'Out of Stock' }}
                </button>
            </form>

            <!-- Product Info -->
            <div   class="auto-style-0273">
                <h3   class="auto-style-0274">Description</h3>
                <p   class="auto-style-0275">
                    {{ $product->description }}
                </p>

                <!-- Product Details Grid -->
                <div   class="auto-style-0276">
                    <div>
                        <div   class="auto-style-0277">SKU</div>
                        <div   class="auto-style-0207">{{ $product->sku }}</div>
                    </div>
                    <div>
                        <div   class="auto-style-0277">Category</div>
                        <div   class="auto-style-0207">{{ $product->category->name }}</div>
                    </div>
                    <div>
                        <div   class="auto-style-0277">Gender</div>
                        <div   class="auto-style-0207">{{ ucfirst($product->gender) }}</div>
                    </div>
                    <div>
                        <div   class="auto-style-0277">Availability</div>
                        <div style="font-weight: 600; color: {{ $product->stock_quantity > 0 ? 'var(--success)' : 'var(--danger)' }};">
                            {{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div   class="auto-style-0278">
            <h2   class="auto-style-0279">You May Also Like</h2>
            <div   class="auto-style-0280">
                @foreach($relatedProducts as $related)
                    <a href="{{ route('products.show', $related->slug) }}" style="text-decoration: none; color: inherit;">
                        <div   onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='rgba(212, 175, 55, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(212, 175, 55, 0.1)'" class="auto-style-0281">
                            <div   class="auto-style-0282">
                                <img src="{{ Storage::url($related->main_image) }}" alt="{{ $related->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div   class="auto-style-0283">
                                <h3   class="auto-style-0284">{{ $related->name }}</h3>
                                <div   class="auto-style-0285">&#8377;{{ number_format($related->price, 2) }}</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function changeImage(src) {
        document.getElementById('mainImage').src = src;
    }

    function decreaseQuantity() {
        const input = document.getElementById('quantity');
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    function increaseQuantity() {
        const input = document.getElementById('quantity');
        if (input.value < 10) {
            input.value = parseInt(input.value) + 1;
        }
    }

    // Size button interactions
    document.querySelectorAll('input[name="size"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.size-button').forEach(btn => {
                btn.style.borderColor = 'var(--zyra-silver)';
                btn.style.background = 'transparent';
                btn.style.fontWeight = '400';
            });
            this.nextElementSibling.style.borderColor = 'var(--zyra-gold)';
            this.nextElementSibling.style.background = 'rgba(212, 175, 55, 0.1)';
            this.nextElementSibling.style.fontWeight = '600';
        });
    });

    // Add to cart with AJAX
    document.getElementById('addToCartForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount();
                alert('? Product added to cart!');
            } else {
                alert(data.message || 'Failed to add to cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to add to cart');
        });
    });
</script>
@endpush
@endsection

