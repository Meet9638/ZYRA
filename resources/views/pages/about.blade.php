@extends('layouts.app')

@section('title', 'About Us - ZYRA')

@section('content')
<div class="hero" style="min-height: 40vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, var(--zyra-black), var(--zyra-charcoal));">
    <div>
        <h1 style="font-size: 3.5rem; margin-bottom: 1rem; color: var(--zyra-gold);">About ZYRA</h1>
        <p style="font-size: 1.2rem; color: var(--zyra-silver); max-width: 800px; margin: 0 auto;">Redefining the luxury fashion experience through curated excellence.</p>
    </div>
</div>

<div class="container" style="padding: 4rem 2rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 4rem; align-items: center; margin-bottom: 6rem;">
        <div>
            <h2 style="font-size: 2.5rem; color: var(--zyra-white); margin-bottom: 1.5rem;">Our Vision</h2>
            <p style="color: var(--zyra-silver); font-size: 1.1rem; line-height: 1.8; margin-bottom: 1.5rem;">
                At ZYRA, we believe that luxury is intensely personal. Our mission is to bridge the gap between high-end fashion and modern lifestyle, creating a shopping experience that is as unique as you are.
            </p>
            <p style="color: var(--zyra-silver); font-size: 1.1rem; line-height: 1.8;">
                We hand-select each piece in our collection, focusing on exceptional quality, uncompromising style, and timeless design. Our goal is to provide a curated selection that helps you express your individuality with confidence and elegance.
            </p>
        </div>
        <div style="background: var(--zyra-charcoal); border: 1px solid var(--border-color); border-radius: 8px; padding: 3rem; text-align: center;">
            <div style="font-size: 4rem; color: var(--zyra-gold); margin-bottom: 1rem;">✧</div>
            <h3 style="font-size: 1.5rem; color: var(--zyra-white); margin-bottom: 1rem;">The ZYRA Standard</h3>
            <p style="color: var(--muted-text); font-size: 0.95rem; line-height: 1.6;">Exceptional craftsmanship, premium materials, and a commitment to excellence. Every piece in our collection is chosen to meet the highest standards of luxury fashion.</p>
        </div>
    </div>

    <div style="text-align: center; margin-bottom: 4rem;">
        <h2 style="font-size: 2.5rem; color: var(--zyra-white); margin-bottom: 1rem;">Why Choose ZYRA?</h2>
        <div style="width: 60px; height: 2px; background: var(--zyra-gold); margin: 0 auto 3rem auto;"></div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
            <div class="product-card" style="padding: 2.5rem 2rem; cursor: default; background: var(--zyra-charcoal); border: 1px solid var(--border-color); border-radius: 8px;">
                <div style="font-size: 2rem; color: var(--zyra-gold); margin-bottom: 1rem; font-weight: bold;">01</div>
                <h3 style="font-size: 1.2rem; color: var(--zyra-white); margin-bottom: 1rem;">Curated Excellence</h3>
                <p style="color: var(--zyra-silver); font-size: 0.9rem; line-height: 1.5;">Our experts meticulously select every item, ensuring that only the finest pieces make it into our exclusive collection.</p>
            </div>
            
            <div class="product-card" style="padding: 2.5rem 2rem; cursor: default; background: var(--zyra-charcoal); border: 1px solid var(--border-color); border-radius: 8px;">
                <div style="font-size: 2rem; color: var(--zyra-gold); margin-bottom: 1rem; font-weight: bold;">02</div>
                <h3 style="font-size: 1.2rem; color: var(--zyra-white); margin-bottom: 1rem;">Exclusive Access</h3>
                <p style="color: var(--zyra-silver); font-size: 0.9rem; line-height: 1.5;">Gain unparalleled access to limited-edition pieces and rare finds from the world's most prestigious designers.</p>
            </div>
            
            <div class="product-card" style="padding: 2.5rem 2rem; cursor: default; background: var(--zyra-charcoal); border: 1px solid var(--border-color); border-radius: 8px;">
                <div style="font-size: 2rem; color: var(--zyra-gold); margin-bottom: 1rem; font-weight: bold;">03</div>
                <h3 style="font-size: 1.2rem; color: var(--zyra-white); margin-bottom: 1rem;">Seamless Delivery</h3>
                <p style="color: var(--zyra-silver); font-size: 0.9rem; line-height: 1.5;">Experience a effortless shopping journey with white-glove service and global shipping to your doorstep.</p>
            </div>
        </div>
    </div>
</div>
@endsection
