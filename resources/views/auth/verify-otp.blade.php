@extends('layouts.app')

@section('title', 'Verify OTP - ZYRA')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h1 class="auth-title">Verify Your Identity</h1>
        <p class="auth-subtitle">Enter the 6-digit code sent to your email address</p>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('otp.verify') }}" method="POST" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="otp" class="form-label">Verification Code</label>
                <div class="otp-input-group">
                    <input 
                        type="text" 
                        id="otp" 
                        name="otp" 
                        class="form-control @error('otp') is-invalid @enderror" 
                        value="{{ old('otp') }}" 
                        required 
                        maxlength="6"
                        pattern="[0-9]{6}"
                        placeholder="000000"
                        autocomplete="one-time-code"
                        inputmode="numeric"
                    >
                </div>
                @error('otp')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Enter the 6-digit code from your email</small>
            </div>

            <div class="otp-timer" id="otpTimer">
                <p>Code expires in: <span id="timeRemaining">10:00</span></p>
            </div>

            <button type="submit" class="btn-submit">Verify & Login</button>

            <div class="otp-actions">
                <button type="button" class="btn-link" id="resendOtp" onclick="resendOtp()">
                    Resend Code
                </button>
                <button type="button" class="btn-link btn-link-danger" onclick="cancelVerification()">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let timeLeft = {{ $timeLeft ?? 0 }}; // Sync with server-side expiration
const timerElement = document.getElementById('timeRemaining');
const resendButton = document.getElementById('resendOtp');
const otpTimer = document.getElementById('otpTimer');

function updateTimer() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    if (timeLeft <= 0) {
        clearInterval(timerInterval);
        otpTimer.innerHTML = '<p class="text-danger">Code expired. Please request a new one.</p>';
        document.querySelector('.btn-submit').disabled = true;
        resendButton.disabled = false;
    } else {
        timeLeft--;
    }
}

const timerInterval = setInterval(updateTimer, 1000);

function resendOtp() {
    if (confirm('A new verification code will be sent to your email. Continue?')) {
        fetch('{{ route("otp.resend") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'text/html',
            },
        })
        .then(response => {
            if (response.ok) {
                window.location.reload(); // Reload to get fresh timeLeft and success message
            } else {
                throw new Error('Network response was not ok');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to resend code. Please try again.');
        });
    }
}

function cancelVerification() {
    if (confirm('Are you sure you want to cancel the verification process?')) {
        window.location.href = '{{ route("otp.cancel") }}';
    }
}

// Auto-format OTP input
document.getElementById('otp').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});

// Auto-submit when 6 digits are entered
document.getElementById('otp').addEventListener('input', function(e) {
    if (this.value.length === 6) {
        // Optional: auto-submit when complete
        // document.querySelector('.auth-form').submit();
    }
});

// Disable resend button initially
resendButton.disabled = false;
</script>
@endpush

