<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Diara TimeSchool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #183b70;
            --primary-bright: #1777e5;
            --primary-light: #009cf3;
            --white: #ffffff;
            --light-blue: rgba(24, 59, 112, 0.1);
            --shadow-light: 0 4px 20px rgba(24, 59, 112, 0.15);
            --shadow-medium: 0 8px 30px rgba(24, 59, 112, 0.2);
            --light-gray: #f8f9fa;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-dark) 0%, #2c5282 100%);
        }

        .login-container {
            min-height: 100vh;
            background: transparent;
            box-shadow: none;
        }

        .login-form-section {
            background: rgba(24, 59, 112, 0.95);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .login-form-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        .login-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 10;
            max-width: 450px;
            width: 100%;
            backdrop-filter: blur(20px);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 1rem;
            border-radius: 15px;
        }

        .brand-icon-fallback {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-dark) 0%, #2c5282 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin: 0 auto 1rem;
            box-shadow: var(--shadow-light);
        }

        .brand-title {
            color: var(--primary-dark);
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            opacity: 0.8;
        }

        .login-title {
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group-text {
            background: rgba(24, 59, 112, 0.1);
            border: 2px solid rgba(24, 59, 112, 0.2);
            border-right: none;
            color: var(--primary-dark);
            border-radius: 12px 0 0 12px;
            padding: 16px 15px;
        }

        .form-control {
            border: 2px solid rgba(24, 59, 112, 0.2);
            border-left: none;
            border-radius: 0 12px 12px 0;
            padding: 16px 20px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary-dark);
        }

        .form-control:focus {
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 3px rgba(24, 59, 112, 0.1);
            background: white;
        }

        .form-control::placeholder {
            color: rgba(24, 59, 112, 0.6);
        }

        .password-toggle {
            background: rgba(24, 59, 112, 0.1);
            border: 2px solid rgba(24, 59, 112, 0.2);
            border-left: none;
            color: var(--primary-dark);
            border-radius: 0 12px 12px 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            background: var(--primary-dark);
            color: white;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-dark) 0%, #2c5282 100%);
            border: none;
            padding: 16px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(24, 59, 112, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(24, 59, 112, 0.4);
            background: linear-gradient(135deg, #0f2a4a 0%, var(--primary-dark) 100%);
        }

        .form-check-input:checked {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .form-check-label {
            color: rgba(24, 59, 112, 0.8);
            font-weight: 500;
        }

        .forgot-password {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #0f2a4a;
        }

        .hero-section {
            background: linear-gradient(135deg, #0f2a4a 0%, var(--primary-dark) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('{{ asset('assets/images/Sma_N_16_Jakarta.jpg') }}') center/cover;
            opacity: 0.08;
        }

        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            color: white;
            padding: 3rem;
        }

        .hero-icon {
            font-size: 6rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            animation: pulse 3s ease-in-out infinite;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.2rem;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 2rem;
            text-shadow: 0 1px 5px rgba(0,0,0,0.3);
        }

        .feature-badge {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            padding: 8px 16px;
            margin: 0.5rem;
            display: inline-block;
            font-size: 0.9rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .invalid-feedback {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #dc3545;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        @media (max-width: 768px) {
            .login-form {
                margin: 1rem;
                padding: 2rem;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-icon {
                font-size: 4rem;
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>

<body>
    <div class="row login-container m-0">
        <!-- Login Form Section -->
        <div class="col-md-6 login-form-section d-flex align-items-center justify-content-center order-md-1 order-2">
            <div class="login-form">
                <!-- Brand Header -->
                <div class="brand-header">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Diara TimeSchool Logo" class="brand-logo"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="brand-icon-fallback" style="display: none;">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h2 class="brand-title">Diara TimeSchool</h2>
                    <p class="brand-subtitle">Smart Attendance System</p>
                    <h4 class="login-title">Sign In</h4>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="Alamat Email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" placeholder="Password" required>
                        <span class="input-group-text password-toggle">
                            <i class="bi bi-eye"></i>
                        </span>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-7">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>
                        </div>
                        <div class="col-5 text-end">
                            <a href="#" class="forgot-password">Lupa Password?</a>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </form>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="col-md-6 p-0 hero-section d-flex align-items-center justify-content-center order-md-2 order-1">
            <div class="hero-content">
                <i class="bi bi-camera-video hero-icon"></i>
                <h1 class="hero-title">Presensi Pintar</h1>
                <p class="hero-subtitle">Sistem Informasi Presensi dan Poin Siswa</p>
                <div class="mt-4">
                    <span class="feature-badge">
                        <i class="bi bi-check-circle me-1"></i>99% Akurasi
                    </span>
                    <span class="feature-badge">
                        <i class="bi bi-lightning me-1"></i>Real-time
                    </span>
                    <span class="feature-badge">
                        <i class="bi bi-shield-check me-1"></i>Aman
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            document.querySelector('.password-toggle').addEventListener('click', function() {
                const passwordInput = document.querySelector('input[name="password"]');
                const icon = this.querySelector('.bi');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });
    </script>
</body>

</html>
