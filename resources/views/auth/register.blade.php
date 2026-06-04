@extends('layouts.app')

@section('title', 'Membership - ZYRA')

@section('content')
<div style="min-height: 100vh; display: grid; grid-template-columns: 1.2fr 1fr; background: var(--zyra-black); margin-top: -80px;">
    <!-- Branding Side -->
    <div style="position: relative; background: url('{{ asset('images/auth_bg.png') }}') no-repeat center center / cover; padding: 4rem; display: flex; flex-direction: column; justify-content: space-between; color: white; order: 1;">
        <div style="position: absolute; inset: 0; background: linear-gradient(to left, rgba(0,0,0,0.6), rgba(0,0,0,0.2)); z-index: 1;"></div>
        <div style="position: relative; z-index: 2; width: 120px; height: 120px; margin-right: -20px; align-self: flex-end;">
            <img src="{{ asset('images/logo.png') }}" alt="ZYRA" style="width: 100%; height: 100%; object-fit: contain; filter: brightness(0) invert(1);">
        </div>
        <div style="position: relative; z-index: 2;">
            <h1 style="font-size: 3.5rem; font-family: 'Syne', sans-serif; line-height: 1.1; margin-bottom: 2rem; font-weight: 800;">Exclusive<br><span style="background: var(--gold-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Membership</span></h1>
            <p style="font-size: 1.1rem; max-width: 400px; opacity: 0.9; line-height: 1.8; font-weight: 300;">Become part of the global elite. Access curated collections and bespoke services.</p>
        </div>
        <p style="position: relative; z-index: 2; font-size: 0.75rem; letter-spacing: 0.3em; text-transform: uppercase; opacity: 0.6;">© {{ date('Y') }} ZYRA Atelier</p>
    </div>

    <!-- Form Side -->
    <div style="display: flex; align-items: center; justify-content: center; padding: 4rem; order: 0;">
        <div style="width: 100%; max-width: 500px;">
            <div style="margin-bottom: 3.5rem; text-align: left;">
                <h2 style="font-size: 2.22rem; font-family: 'Syne', sans-serif; margin-bottom: 0.5rem;">Create Account</h2>
                <p style="color: var(--zyra-silver); font-size: 0.9rem;">Secure your place in the ZYRA ecosystem</p>
            </div>

            <form action="{{ route('register') }}" method="POST">
                @csrf
                
                <div style="margin-bottom: 2rem;">
                    <label style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--zyra-silver); display: block; margin-bottom: 0.8rem; font-weight: 700;">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Your Full Name" style="width: 100%; padding: 1.2rem; background: #fff; border: 1px solid var(--border-color); color: #000; font-weight: 500; border-radius: 4px; outline: none;">
                    @error('name') <p style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--zyra-silver); display: block; margin-bottom: 0.8rem; font-weight: 700;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="your@email.com" style="width: 100%; padding: 1.2rem; background: #fff; border: 1px solid var(--border-color); color: #000; font-weight: 500; border-radius: 4px; outline: none;">
                    @error('email') <p style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem">{{ $message }}</p> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div>
                        <label style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--zyra-silver); display: block; margin-bottom: 0.8rem; font-weight: 700;">Password</label>
                        <input type="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 1.2rem; background: #fff; border: 1px solid var(--border-color); color: #000; font-weight: 500; border-radius: 4px; outline: none;">
                        @error('password') <p style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--zyra-silver); display: block; margin-bottom: 0.8rem; font-weight: 700;">Confirm Password</label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••" style="width: 100%; padding: 1.2rem; background: #fff; border: 1px solid var(--border-color); color: #000; font-weight: 500; border-radius: 4px; outline: none;">
                    </div>
                </div>

                <div style="margin-bottom: 3.5rem; display: flex; align-items: center; gap: 1rem;">
                    <input type="checkbox" id="terms" name="terms" required style="width: 20px; height: 20px; cursor: pointer; accent-color: var(--zyra-gold);">
                    <label for="terms" style="font-size: 0.85rem; color: var(--zyra-silver); cursor: pointer; line-height: 1.5;">
                        I acknowledge and accept the ZYRA <a href="{{ route('privacy-policy') }}" style="color: var(--zyra-gold); text-decoration: none;">Atelier Guidelines</a> and Privacy Terms.
                    </label>
                </div>

                <button type="submit" class="btn-premium" style="width: 100%; text-align: center; font-size: 0.9rem; padding: 1.25rem; font-weight: 800;">Join ZYRA</button>

                <p style="text-align: center; margin-top: 3rem; color: var(--zyra-silver); font-size: 0.9rem;">
                    Already a member? <a href="{{ route('login') }}" style="color: var(--zyra-gold); font-weight: 800; text-decoration: none; margin-left: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Sign In</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
