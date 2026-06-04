<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Concierge | ZYRA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        :root {
            --admin-bg: #FFFFFF;
            --admin-card: rgba(255, 255, 255, 0.9);
            --admin-gold: #D4AF37;
            --admin-silver: #555555;
            --admin-black: #0A0A0A;
        }

        body.admin-login-body {
            background: var(--admin-bg);
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(212, 175, 55, 0.03) 0%, transparent 30%),
                radial-gradient(circle at 90% 90%, rgba(183, 110, 121, 0.03) 0%, transparent 30%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: var(--admin-black);
            overflow: hidden;
        }

        .admin-login-container {
            width: 100%;
            max-width: 450px;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }

        .admin-glass-card {
            background: var(--admin-card);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 4rem 3rem;
            box-shadow: 0 50px 100px rgba(0, 0, 0, 0.12);
            text-align: center;
        }

        .admin-logo {
            display: block;
            width: 150px;
            margin: 0 auto 3.5rem;
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.05));
            transition: transform 0.3s ease;
        }

        .admin-logo:hover {
            transform: scale(1.05);
        }

        .admin-title {
            font-family: 'Syne', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #D4AF37, #B76E79);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .admin-subtitle {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--admin-silver);
            margin-bottom: 4rem;
        }

        .form-group {
            margin-bottom: 2rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--admin-silver);
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .admin-control {
            width: 100%;
            padding: 1.25rem;
            background: #F9F9F9;
            border: 1px solid #EEEEEE;
            border-radius: 4px;
            color: #000000;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .admin-control:focus {
            border-color: var(--admin-gold);
            background: #FFFFFF;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.1);
        }

        .btn-admin {
            width: 100%;
            padding: 1.25rem;
            background: #000000;
            color: #FFFFFF;
            border: none;
            border-radius: 4px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-admin:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            background: var(--admin-gold);
        }

        .admin-footer {
            margin-top: 3rem;
            font-size: 0.75rem;
            color: rgba(0, 0, 0, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            font-size: 0.85rem;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
    </style>
</head>
<body class="admin-login-body">

<div class="admin-login-container">
    <div class="admin-glass-card">
        <img src="{{ asset('images/logo.png') }}" alt="ZYRA" class="admin-logo">
        
        <h1 class="admin-title">Admin</h1>
        <p class="admin-subtitle">Secure Access Portal</p>

        @if ($errors->any())
            <div class="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="form-group">
                <label>Admin ID / Email</label>
                <input type="email" name="email" class="admin-control" required autofocus placeholder="admin@zyra.com">
            </div>

            <div class="form-group">
                <label>Access Key</label>
                <input type="password" name="password" class="admin-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-admin">Authenticate</button>
        </form>

        <div class="admin-footer">
            © {{ date('Y') }} ZYRA Dashboard
        </div>
    </div>
</div>

</body>
</html>
