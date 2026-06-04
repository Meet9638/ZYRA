@extends('layouts.app')

@section('title', 'Login - ZYRA')

@section('content')
<div style="min-height: 100vh; display: grid; grid-template-columns: 1fr 1.2fr; background: var(--zyra-black); margin-top: -80px;">
    <!-- Branding Side -->
    <div style="position: relative; background: url('{{ asset('images/auth_bg.png') }}') no-repeat center center / cover; padding: 4rem; display: flex; flex-direction: column; justify-content: space-between; color: white;">
        <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.6), rgba(0,0,0,0.2)); z-index: 1;"></div>
        <div style="position: relative; z-index: 2; width: 120px; height: 120px; margin-left: -20px;">
            <img src="{{ asset('images/logo.png') }}" alt="ZYRA" style="width: 100%; height: 100%; object-fit: contain; filter: brightness(0) invert(1);">
        </div>
        <div style="position: relative; z-index: 2;">
            <h1 style="font-size: 3.5rem; font-family: 'Syne', sans-serif; line-height: 1.1; margin-bottom: 2rem; font-weight: 800;">Redefining<br><span style="background: var(--gold-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Luxury</span></h1>
            <p style="font-size: 1.1rem; max-width: 400px; opacity: 0.9; line-height: 1.8; font-weight: 300;">Step into the apex of curated fashion. Experience excellence in every detail.</p>
        </div>
        <p style="position: relative; z-index: 2; font-size: 0.75rem; letter-spacing: 0.3em; text-transform: uppercase; opacity: 0.6;">© {{ date('Y') }} ZYRA Atelier</p>
    </div>

    <!-- Form Side -->
    <div style="display: flex; align-items: center; justify-content: center; padding: 4rem;">
        <div style="width: 100%; max-width: 450px;">
            <div style="margin-bottom: 3rem; text-align: center;">
                <h2 style="font-size: 2.2rem; font-family: 'Syne', sans-serif; margin-bottom: 1rem;">Welcome Back</h2>
                <p style="color: var(--zyra-silver);">Secure access to your ZYRA account</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div style="margin-bottom: 2rem;">
                    <label style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--zyra-silver); display: block; margin-bottom: 0.8rem; font-weight: 700;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="your@email.com" style="width: 100%; padding: 1.2rem; background: #fff; border: 1px solid var(--border-color); color: #000; font-weight: 500; border-radius: 4px; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--zyra-gold)'" onblur="this.style.borderColor='var(--border-color)'">
                    @error('email') <p style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.8rem;">
                        <label style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--zyra-silver); font-weight: 700;">Password</label>
                        <a href="{{ route('password.request') }}" style="color: var(--zyra-gold); font-size: 0.75rem; text-decoration: none; font-weight: 600;">Forgot Password?</a>
                    </div>
                    <input type="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 1.2rem; background: #fff; border: 1px solid var(--border-color); color: #000; font-weight: 500; border-radius: 4px; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--zyra-gold)'" onblur="this.style.borderColor='var(--border-color)'">
                </div>

                <div style="margin-bottom: 3rem; display: flex; align-items: center; gap: 0.8rem;">
                    <input type="checkbox" id="remember" name="remember" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--zyra-gold);">
                    <label for="remember" style="font-size: 0.9rem; color: var(--zyra-silver); cursor: pointer;">Maintain session for this device</label>
                </div>

                <button type="submit" class="btn-premium" style="width: 100%; text-align: center; font-size: 0.9rem; padding: 1.25rem; font-weight: 800;">Sign In</button>

                <p style="text-align: center; margin-top: 3rem; color: var(--zyra-silver); font-size: 0.9rem;">
                    New to ZYRA? <a href="{{ route('register') }}" style="color: var(--zyra-gold); font-weight: 800; text-decoration: none; margin-left: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Join Now</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
