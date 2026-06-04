@extends('layouts.app')

@section('title', 'Categories - ZYRA')

@push('head-scripts')
<style>
    .categories-header {
        text-align: center;
        margin-bottom: 4rem;
        padding-top: 2rem;
    }
    .categories-header h1 {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--zyra-white, #111), var(--zyra-silver, #888));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
        font-family: 'Syne', sans-serif;
    }
    .categories-header p {
        color: var(--zyra-silver);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2.5rem;
        padding-bottom: 4rem;
    }

    .category-card {
        background: var(--zyra-charcoal, #fff);
        border: 1px solid var(--border-color, #eee);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        text-decoration: none;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        height: 100%;
        position: relative;
    }

    .category-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(232, 111, 28, 0.08);
        border-color: rgba(232, 111, 28, 0.3);
    }

    .category-image-wrapper {
        position: relative;
        height: 280px;
        width: 100%;
        overflow: hidden;
        background: var(--zyra-gray, #f4f4f4);
    }

    .category-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .category-card:hover .category-image-wrapper img {
        transform: scale(1.05);
    }

    .category-image-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 6rem;
        font-weight: 800;
        font-family: 'Syne', sans-serif;
        color: #ffffff;
        background: linear-gradient(135deg, var(--zyra-gold, #d4af37), var(--zyra-rose, #b76e79));
        text-transform: uppercase;
        text-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    .category-content {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        background: var(--zyra-charcoal, #fff);
        position: relative;
        border-top: 1px solid rgba(0,0,0,0.02);
    }

    .category-title {
        font-size: 1.7rem;
        font-weight: 700;
        color: var(--zyra-white, #111);
        margin-bottom: 0.5rem;
        font-family: 'Syne', sans-serif;
        transition: color 0.3s ease;
        letter-spacing: -0.02em;
    }

    .category-card:hover .category-title {
        color: var(--zyra-gold, #d4af37);
    }

    .category-description {
        color: var(--zyra-silver, #666);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .category-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color, #eee);
        margin-top: auto;
    }

    .category-count {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--zyra-silver, #666);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .category-count span {
        background: rgba(232, 111, 28, 0.1);
        color: var(--zyra-gold, #d4af37);
        padding: 0.25rem 0.7rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.8rem;
    }

    .category-explore {
        font-size: 0.95rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--zyra-gold, #d4af37);
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: gap 0.3s ease;
    }

    .category-card:hover .category-explore {
        gap: 0.8rem;
    }

</style>
@endpush

@section('content')
<div class="container" style="padding-top: 120px; min-height: 100vh;">
    <div class="categories-header">
        <h1>Shop by Category</h1>
        <p>Explore our curated collections designed with AI-precision sizing for your perfect fit.</p>
    </div>

    <div class="categories-grid">
        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}" class="category-card">
                
                <!-- Category Image -->
                <div class="category-image-wrapper">
                    @if($category->image)
                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}">
                    @else
                        <div class="category-image-fallback">{{ strtoupper(substr($category->name, 0, 1)) }}</div>
                    @endif
                </div>

                <!-- Category Content -->
                <div class="category-content">
                    <h2 class="category-title">{{ $category->name }}</h2>
                    @if($category->description)
                        <p class="category-description">{{ Str::limit($category->description, 100) }}</p>
                    @endif
                    
                    <!-- Category Footer -->
                    <div class="category-footer">
                        <div class="category-count">
                            <span>{{ $category->products->count() }}</span> products
                        </div>
                        <div class="category-explore">
                            Explore &rarr;
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection

