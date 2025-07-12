<!DOCTYPE html>
<!--
Template name: Nova
Template author: FreeBootstrap.net
Author website: https://freebootstrap.net/
License: https://freebootstrap.net/license
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Diara TimeSchool &mdash; Presensi Pintar & Manajemen Siswa</title>

    <!-- ======= Google Font =======-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&amp;display=swap" rel="stylesheet">
    <!-- End Google Font-->

    <!-- ======= Styles =======-->
    <link href="{{ asset('assets/vendors/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/bootstrap-icons/font/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/glightbox/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/aos/aos.css') }}" rel="stylesheet">
    <!-- End Styles-->

    <!-- ======= Theme Style =======-->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <!-- End Theme Style-->

    <!-- ======= Custom Colors & Modern Animations =======-->
    <style>
        :root {
            --primary-dark: #183b70;
            --primary-bright: #183b70;
            --primary-light: #2c5282;
            --white: #ffffff;
            --light-blue: rgba(24, 59, 112, 0.1);
            --shadow-light: 0 4px 20px rgba(24, 59, 112, 0.15);
            --shadow-medium: 0 8px 30px rgba(24, 59, 112, 0.2);
            --light-gray: #f8f9fa;
            --text-muted: #6c757d;
        }

        /* Modern Typography */
        .navbar-brand,
        .hero-title,
        h1,
        h2,
        h3 {
            color: var(--primary-dark) !important;
            font-weight: 700;
        }

        /* Enhanced Logo Design */
        .brand-container {
            display: flex;
            align-items: center;
            padding: 8px 16px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(24, 59, 112, 0.05) 100%);
            border-radius: 15px;
            border: 1px solid rgba(24, 59, 112, 0.1);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .brand-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(24, 59, 112, 0.15);
            border-color: var(--primary-light);
        }

        /* Updated Logo Image Styling */
        .brand-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-right: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .brand-logo:hover {
            transform: scale(1.05);
        }

        /* Fallback icon styling if logo fails to load */
        .brand-icon-fallback {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            margin-right: 12px;
            box-shadow: 0 4px 15px rgba(24, 59, 112, 0.3);
            animation: pulse 3s ease-in-out infinite;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 0.7rem;
            color: var(--primary-dark);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }

        /* Professional Icons */
        .professional-icon {
            width: 24px;
            height: 24px;
            background: var(--primary-dark);
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            margin-right: 8px;
            flex-shrink: 0;
        }

        /* Modern Buttons */
        .btn-primary,
        .btn {
            background: var(--primary-dark) !important;
            border: none !important;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-light);
            color: white !important;
        }

        .btn-primary:hover,
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            background: #0f2a4a !important;
        }

        /* Login Button - Enhanced Blue Styling */
        .btn-login {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%) !important;
            border: none !important;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(24, 59, 112, 0.3);
            color: white !important;
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(24, 59, 112, 0.4);
            background: linear-gradient(135deg, #0f2a4a 0%, var(--primary-dark) 100%) !important;
        }

        .btn-white-outline {
            background: transparent !important;
            border: 2px solid var(--primary-dark) !important;
            color: var(--primary-dark) !important;
        }

        .btn-white-outline:hover {
            background: var(--primary-dark) !important;
            color: white !important;
            border-color: var(--primary-dark) !important;
        }

        /* Offcanvas Logo Enhancement */
        .offcanvas-brand {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--light-blue) 0%, rgba(255, 255, 255, 0.8) 100%);
            border-radius: 15px;
            margin-bottom: 1rem;
        }

        .offcanvas-brand-logo {
            width: 35px;
            height: 35px;
            object-fit: contain;
            margin-right: 10px;
            border-radius: 6px;
        }

        .offcanvas-brand-icon-fallback {
            width: 35px;
            height: 35px;
            background: var(--primary-dark);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            margin-right: 10px;
        }

        /* Modern Icons & Elements */
        .icon,
        .step-number {
            background: var(--primary-dark) !important;
            color: var(--white) !important;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            box-shadow: var(--shadow-light);
            transition: all 0.3s ease;
        }

        .step-number {
            width: 90px;
            height: 90px;
            font-size: 1.8rem;
            margin: 0 auto 2rem;
            border-radius: 50%;
            font-weight: 700;
        }

        /* Hero Section Enhancements */
        .hero__v6 {
            background: linear-gradient(135deg, rgba(24, 59, 112, 0.03) 0%, rgba(24, 59, 112, 0.05) 100%);
            padding: 100px 0;
            overflow: hidden;
            position: relative;
        }

        .hero__v6::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(24, 59, 112, 0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        .hero-subtitle {
            color: var(--primary-dark) !important;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 16px;
            background: var(--light-blue);
            border-radius: 25px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .hero-title {
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: var(--primary-dark) !important;
        }

        /* System subtitle styling */
        .system-subtitle {
            font-size: 1.1rem;
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .hero-description {
            font-size: 1.2rem;
            color: #666;
            line-height: 1.6;
        }

        /* Modern Hero Image */
        .hero-img-container {
            position: relative;
            perspective: 1000px;
        }

        .hero-img-main {
            background: var(--primary-dark);
            border-radius: 30px;
            padding: 60px;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-medium);
            transform: rotateY(-5deg) rotateX(2deg);
            transition: all 0.3s ease;
        }

        .hero-img-main:hover {
            transform: rotateY(0deg) rotateX(0deg) scale(1.02);
            background: #0f2a4a;
        }

        .hero-img-main::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        }

        .floating-icon {
            font-size: 8rem;
            color: white;
            opacity: 0.9;
            animation: pulse 2s ease-in-out infinite;
        }

        /* Service Cards Enhancement */
        .service-card {
            background: white;
            border: 1px solid rgba(24, 59, 112, 0.08);
            border-radius: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(24, 59, 112, 0.15);
            border-color: var(--primary-dark);
        }

        .service-card .icon {
            margin: 0 auto 2rem;
            transition: all 0.3s ease;
            background: var(--light-blue);
            color: var(--primary-dark);
        }

        .service-card:hover .icon {
            transform: scale(1.1);
            background: var(--primary-dark);
            color: white;
        }

        .service-card h3 {
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
        }

        .service-card .feature-list {
            background: var(--light-gray);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }

        /* Step Cards Enhancement */
        .step-card {
            position: relative;
            padding: 2.5rem;
            transition: all 0.3s ease;
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(24, 59, 112, 0.08);
            height: 100%;
        }

        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(24, 59, 112, 0.1);
        }

        .step-card:hover .step-number {
            transform: scale(1.1);
            background: #0f2a4a !important;
        }

        .step-icon {
            font-size: 3rem;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
        }

        /* Testimonial Cards Enhancement */
        .testimonial {
            background: white;
            border: 1px solid rgba(24, 59, 112, 0.08);
            border-radius: 20px;
            transition: all 0.3s ease;
            position: relative;
            height: 100%;
        }

        .testimonial:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(24, 59, 112, 0.1);
            border-color: var(--primary-dark);
        }

        .testimonial-rating {
            margin-bottom: 1.5rem;
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            background: var(--light-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-size: 24px;
            flex-shrink: 0;
        }

        /* Modern Section Styling */
        .section {
            padding: 120px 0;
            position: relative;
        }

        .section:nth-child(even) {
            background: var(--light-gray);
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-subtitle {
            display: inline-flex;
            align-items: center;
            background: var(--light-blue);
            color: var(--primary-dark);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        /* Trust Badges */
        .trust-badge {
            background: white;
            border: 1px solid rgba(24, 59, 112, 0.2);
            color: var(--primary-dark);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Result Box */
        .result-box {
            background: white;
            border: 2px dashed var(--primary-dark);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
        }

        .result-box::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 20px;
            background: white;
            border: 2px solid var(--primary-dark);
            border-radius: 50%;
        }

        /* Form Enhancement */
        .form-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(24, 59, 112, 0.08);
            border: 1px solid rgba(24, 59, 112, 0.1);
        }

        .form-control {
            border: 2px solid rgba(24, 59, 112, 0.1);
            border-radius: 12px;
            padding: 16px 20px;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 3px rgba(24, 59, 112, 0.1);
        }

        /* Contact Icons */
        .contact-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-dark);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .contact-icon:hover {
            background: #0f2a4a;
            transform: scale(1.05);
        }

        /* Footer Enhancement */
        .footer {
            background: var(--primary-dark) !important;
            position: relative;
        }

        /* Footer Logo Styling */
        .footer-logo {
            width: 20px;
            height: 20px;
            object-fit: contain;
            margin-right: 8px;
            border-radius: 3px;
        }

        .footer-icon-fallback {
            width: 20px;
            height: 20px;
            background: var(--primary-light);
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            margin-right: 8px;
            flex-shrink: 0;
        }

        /* Back to Top Button */
        #back-to-top {
            background: var(--primary-dark) !important;
        }

        /* Logos section update */
        .logos-title .bi-award-fill {
            color: var(--primary-dark) !important;
        }

        /* Badge updates */
        .badge.bg-light {
            background: var(--light-blue) !important;
            color: var(--primary-dark) !important;
        }

        .badge.bg-white {
            color: var(--primary-dark) !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .brand-container {
                padding: 6px 12px;
                border-radius: 12px;
            }

            .brand-icon {
                width: 35px;
                height: 35px;
                font-size: 16px;
                margin-right: 10px;
            }

            .brand-title {
                font-size: 1.2rem;
            }

            .brand-subtitle {
                font-size: 0.6rem;
            }

            .btn-login {
                padding: 8px 20px;
                font-size: 13px;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .hero-img-main {
                margin-top: 2rem;
                transform: none;
            }

            .floating-icon {
                font-size: 4rem;
            }

            .section {
                padding: 80px 0;
            }
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(2deg);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }
    </style>

    <!-- ======= Apply theme =======-->
    <script>
        (function() {
            const storedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', storedTheme);
        })();
    </script>
</head>

<body>

    <!-- ======= Site Wrap =======-->
    <div class="site-wrap">

        <!-- ======= Header =======-->
        <header class="fbs__net-navbar navbar navbar-expand-lg dark" aria-label="Diara TimeSchool navbar">
            <div class="container d-flex align-items-center justify-content-between">

                <!-- Start Enhanced Logo with Asset-->
                <a class="navbar-brand w-auto" href="#home">
                    <div class="brand-container">
                        <!-- Replace 'logo.png' with your actual logo file name -->
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Diara TimeSchool Logo" class="brand-logo"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <!-- Fallback icon if logo doesn't load -->
                        <div class="brand-icon-fallback" style="display: none;">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div class="brand-text">
                            <h4 class="brand-title">Diara TimeSchool</h4>
                            <span class="brand-subtitle">Smart Attendance</span>
                        </div>
                    </div>
                </a>
                <!-- End Enhanced Logo-->

                <!-- Start offcanvas-->
                <div class="offcanvas offcanvas-start w-75" id="fbs__net-navbars" tabindex="-1"
                    aria-labelledby="fbs__net-navbarsLabel">

                    <div class="offcanvas-header">
                        <div class="offcanvas-header-logo">
                            <a class="logo-link" id="fbs__net-navbarsLabel" href="#home">
                                <div class="offcanvas-brand">
                                    <!-- Replace 'logo.png' with your actual logo file name -->
                                    <img src="{{ asset('assets/images/logo.png') }}" alt="Diara TimeSchool Logo"
                                        class="offcanvas-brand-logo"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <!-- Fallback icon if logo doesn't load -->
                                    <div class="offcanvas-brand-icon-fallback" style="display: none;">
                                        <i class="bi bi-mortarboard-fill"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0" style="color: var(--primary-dark); font-weight: 700;">Diara
                                            TimeSchool</h5>
                                        <small style="color: var(--primary-light); font-weight: 600;">SMART
                                            ATTENDANCE</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <button class="btn-close btn-close-black" type="button" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>

                    <div class="offcanvas-body align-items-lg-center">
                        <ul class="navbar-nav nav me-auto ps-lg-5 mb-2 mb-lg-0">
                            <li class="nav-item"><a class="nav-link scroll-link active" aria-current="page"
                                    href="#home">Home</a></li>
                            <li class="nav-item"><a class="nav-link scroll-link" href="#fitur">Fitur</a></li>
                            <li class="nav-item"><a class="nav-link scroll-link" href="#cara-kerja">Cara Kerja</a></li>
                            <li class="nav-item"><a class="nav-link scroll-link" href="#testimoni">Testimoni</a></li>
                            <li class="nav-item"><a class="nav-link scroll-link" href="#kontak">Kontak</a></li>
                        </ul>
                    </div>
                </div>
                <!-- End offcanvas-->

                <div class="ms-auto w-auto">
                    <div class="header-social d-flex align-items-center gap-1">
                        <a class="btn btn-login" href="{{ url('/login') }}">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Login
                        </a>
                        <button class="fbs__net-navbar-toggler justify-content-center align-items-center ms-auto"
                            data-bs-toggle="offcanvas" data-bs-target="#fbs__net-navbars"
                            aria-controls="fbs__net-navbars" aria-label="Toggle navigation" aria-expanded="false">
                            <svg class="fbs__net-icon-menu" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="21" x2="3" y1="6" y2="6"></line>
                                <line x1="15" x2="3" y1="12" y2="12"></line>
                                <line x1="17" x2="3" y1="18" y2="18"></line>
                            </svg>
                            <svg class="fbs__net-icon-close" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"></path>
                                <path d="m6 6 12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>
        <!-- End Header-->

        <!-- ======= Main =======-->
        <main>

            <!-- ======= Hero =======-->
            <section class="hero__v6 section" id="home">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <div class="row">
                                <div class="col-lg-11">
                                    <span class="hero-subtitle text-uppercase" data-aos="fade-up" data-aos-delay="0">
                                        <i class="bi bi-cpu-fill me-2"></i>
                                        Sistem Presensi Modern
                                    </span>
                                    <h1 class="hero-title mb-3" data-aos="fade-up" data-aos-delay="100">
                                        Presensi Pintar, Manajemen Siswa Lebih Efisien
                                    </h1>
                                    <div class="system-subtitle" data-aos="fade-up" data-aos-delay="150">
                                        Sistem Informasi Presensi dan Poin Siswa
                                    </div>
                                    <p class="hero-description mb-4 mb-lg-5" data-aos="fade-up" data-aos-delay="200">
                                        Sistem presensi wajah & pemantauan poin terintegrasi untuk sekolah modern.
                                        Kelola absensi, prestasi, dan pelanggaran siswa dalam satu platform yang mudah
                                        dan aman.
                                    </p>
                                    <div class="cta d-flex gap-3 mb-4 mb-lg-5" data-aos="fade-up"
                                        data-aos-delay="300">
                                        <a class="btn" href="{{ url('/login') }}">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Login Sekarang
                                        </a>
                                        <a class="btn btn-white-outline" href="#cara-kerja">
                                            Pelajari Lebih Lanjut
                                            <i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                    <div class="logos mb-4" data-aos="fade-up" data-aos-delay="400">
                                        <span class="logos-title text-uppercase mb-4 d-block">
                                            <i class="bi bi-award-fill me-2" style="color: var(--primary-light);"></i>
                                            Solusi Inovatif untuk Sekolah Modern
                                        </span>
                                        <div class="logos-images d-flex gap-4 align-items-center flex-wrap">
                                            <span class="badge bg-light text-primary px-3 py-2">Teknologi AI</span>
                                            <span class="badge bg-light text-primary px-3 py-2">Real-time</span>
                                            <span class="badge bg-light text-primary px-3 py-2">User Friendly</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="hero-img-container" data-aos="fade-left" data-aos-delay="500">
                                <div class="hero-img-main">
                                    <i class="bi bi-camera-video floating-icon"></i>
                                    <!-- Floating Elements -->
                                    <div class="position-absolute top-0 start-0 p-3">
                                        <div class="badge bg-white text-primary px-3 py-2"
                                            style="animation: float 3s ease-in-out infinite;">
                                            <i class="bi bi-check-circle-fill me-1"></i>99% Akurasi
                                        </div>
                                    </div>
                                    <div class="position-absolute bottom-0 end-0 p-3">
                                        <div class="badge bg-white text-primary px-3 py-2"
                                            style="animation: float 4s ease-in-out infinite;">
                                            <i class="bi bi-lightning-fill me-1"></i>Real-time
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End Hero-->

            <!-- ======= Features =======-->
            <section class="section features__v2" id="fitur">
                <div class="container">
                    <div class="section-header">
                        <span class="section-subtitle" data-aos="fade-up" data-aos-delay="0">
                            <i class="bi bi-star-fill me-2"></i>
                            Fitur Unggulan
                        </span>
                        <h2 class="mb-3" data-aos="fade-up" data-aos-delay="100">
                            Solusi Lengkap Manajemen Sekolah Digital
                        </h2>
                        <p data-aos="fade-up" data-aos-delay="200">
                            Tiga pilar utama yang mengoptimalkan pengelolaan siswa di era digital
                        </p>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                            <div class="service-card p-4">
                                <div class="icon mx-auto">
                                    <i class="bi bi-camera-fill" style="font-size: 1.8rem;"></i>
                                </div>
                                <h3 class="text-center">Presensi Wajah</h3>
                                <p class="text-muted text-center mb-4">
                                    Absensi otomatis dengan teknologi AI recognition. Deteksi wajah akurat dalam
                                    hitungan detik,
                                    tidak perlu kartu atau fingerprint.
                                </p>
                                <div class="feature-list">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>Akurasi 99%</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>Deteksi cepat 2 detik</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>Anti-spoofing</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                            <div class="service-card p-4">
                                <div class="icon mx-auto">
                                    <i class="bi bi-clipboard-check" style="font-size: 1.8rem;"></i>
                                </div>
                                <h3 class="text-center">Pencatatan Digital</h3>
                                <p class="text-muted text-center mb-4">
                                    Catat prestasi dan pelanggaran dengan sistem kategori terstruktur.
                                    Upload bukti foto/video untuk dokumentasi lengkap.
                                </p>
                                <div class="feature-list">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>Kategori lengkap</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>Upload bukti digital</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>Sistem poin otomatis</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                            <div class="service-card p-4">
                                <div class="icon mx-auto">
                                    <i class="bi bi-graph-up" style="font-size: 1.8rem;"></i>
                                </div>
                                <h3 class="text-center">Dashboard Analytics</h3>
                                <p class="text-muted text-center mb-4">
                                    Monitor perkembangan siswa real-time dengan dashboard interaktif.
                                    Laporan otomatis untuk guru, siswa, dan orang tua.
                                </p>
                                <div class="feature-list">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>Grafik real-time</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>Multi-user access</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>Export laporan</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End Features-->

            <!-- ======= How it works =======-->
            <section class="section howitworks__v1" id="cara-kerja">
                <div class="container">
                    <div class="section-header">
                        <span class="section-subtitle" data-aos="fade-up" data-aos-delay="0">
                            <i class="bi bi-gear-fill me-2"></i>
                            Cara Kerja
                        </span>
                        <h2 data-aos="fade-up" data-aos-delay="100">Alur Sistem yang Sederhana</h2>
                        <p data-aos="fade-up" data-aos-delay="200">
                            Sistem terintegrasi yang memudahkan semua stakeholder sekolah
                        </p>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="step-card text-center" data-aos="fade-up" data-aos-delay="0">
                                <div class="step-number">1</div>
                                <div class="step-icon">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <h3 class="fs-5 mb-3">Scan Wajah</h3>
                                <p class="text-muted">Siswa melakukan presensi dengan scan wajah di kamera sekolah.
                                    Proses cepat hanya 2 detik.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                            <div class="step-card text-center">
                                <div class="step-number">2</div>
                                <div class="step-icon">
                                    <i class="bi bi-cpu"></i>
                                </div>
                                <h3 class="fs-5 mb-3">AI Verification</h3>
                                <p class="text-muted">Sistem AI memverifikasi identitas dan mencatat kehadiran secara
                                    otomatis dan akurat.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                            <div class="step-card text-center">
                                <div class="step-number">3</div>
                                <div class="step-icon">
                                    <i class="bi bi-pencil-square"></i>
                                </div>
                                <h3 class="fs-5 mb-3">Input Data</h3>
                                <p class="text-muted">Guru mencatat prestasi atau pelanggaran dengan mudah melalui
                                    aplikasi mobile/web.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="600">
                            <div class="step-card text-center">
                                <div class="step-number">4</div>
                                <div class="step-icon">
                                    <i class="bi bi-arrow-repeat"></i>
                                </div>
                                <h3 class="fs-5 mb-3">Auto Update</h3>
                                <p class="text-muted">Data tersinkronisasi real-time ke dashboard semua pengguna dengan
                                    notifikasi otomatis.</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-8 mx-auto text-center" data-aos="fade-up" data-aos-delay="800">
                            <div class="result-box">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <i class="bi bi-bullseye"
                                        style="font-size: 2rem; color: var(--primary-bright);"></i>
                                    <h4 class="ms-3 mb-0">Hasil Akhir</h4>
                                </div>
                                <p class="mb-0">Orang tua dapat memantau secara real-time, guru dapat fokus mengajar,
                                    dan admin sekolah memiliki data akurat untuk evaluasi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End How it works-->

            <!-- ======= Testimonials =======-->
            <section class="section testimonials__v2" id="testimoni">
                <div class="container">
                    <div class="section-header">
                        <span class="section-subtitle" data-aos="fade-up" data-aos-delay="0">
                            <i class="bi bi-chat-quote-fill me-2"></i>
                            Testimoni
                        </span>
                        <h2 class="mb-3" data-aos="fade-up" data-aos-delay="100">
                            Kata Mereka tentang Diara TimeSchool
                        </h2>
                        <p data-aos="fade-up" data-aos-delay="200">
                            Pengalaman nyata dari komunitas pendidikan yang telah merasakan manfaatnya
                        </p>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                            <div class="testimonial p-4">
                                <div class="testimonial-rating">
                                    <div class="d-flex">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                </div>
                                <blockquote class="mb-4">
                                    "Waktu absensi berkurang 70% sejak pakai Diara TimeSchool. Sekarang saya bisa fokus
                                    lebih banyak untuk mengajar daripada mencatat kehadiran manual. Luar biasa!"
                                </blockquote>
                                <div class="testimonial-author d-flex gap-3 align-items-center">
                                    <div class="author-avatar">
                                        <i class="bi bi-person-check"></i>
                                    </div>
                                    <div class="lh-base">
                                        <strong class="d-block">Ibu Sarah Wijaya</strong>
                                        <span class="text-muted">Guru Matematika SMA Negeri 1</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                            <div class="testimonial p-4">
                                <div class="testimonial-rating">
                                    <div class="d-flex">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                </div>
                                <blockquote class="mb-4">
                                    "Notifikasi real-time sangat membantu! Saya langsung tahu kalau anak terlambat atau
                                    dapat prestasi.
                                    Komunikasi dengan sekolah jadi lebih transparan."
                                </blockquote>
                                <div class="testimonial-author d-flex gap-3 align-items-center">
                                    <div class="author-avatar">
                                        <i class="bi bi-person-hearts"></i>
                                    </div>
                                    <div class="lh-base">
                                        <strong class="d-block">Bapak Ahmad Rizki</strong>
                                        <span class="text-muted">Orang Tua Siswa</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                            <div class="testimonial p-4">
                                <div class="testimonial-rating">
                                    <div class="d-flex">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                </div>
                                <blockquote class="mb-4">
                                    "Dashboard analitik sangat membantu evaluasi semester. Data akurat, laporan
                                    otomatis,
                                    dan bisa akses dimana saja. Efisiensi kerja meningkat drastis!"
                                </blockquote>
                                <div class="testimonial-author d-flex gap-3 align-items-center">
                                    <div class="author-avatar">
                                        <i class="bi bi-person-gear"></i>
                                    </div>
                                    <div class="lh-base">
                                        <strong class="d-block">Ibu Dewi Sartika</strong>
                                        <span class="text-muted">Admin Sekolah</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End Testimonials-->

            <!-- ======= FAQ =======-->
            <section class="section faq__v2" id="faq">
                <div class="container">
                    <div class="section-header">
                        <span class="section-subtitle" data-aos="fade-up" data-aos-delay="0">
                            <i class="bi bi-question-circle-fill me-2"></i>
                            FAQ
                        </span>
                        <h2 class="h2 fw-bold mb-3" data-aos="fade-up" data-aos-delay="100">
                            Pertanyaan yang Sering Diajukan
                        </h2>
                        <p data-aos="fade-up" data-aos-delay="200">
                            Temukan jawaban atas pertanyaan umum seputar implementasi sistem presensi pintar di sekolah
                            Anda
                        </p>
                    </div>
                    <div class="row">
                        <div class="col-md-10 mx-auto" data-aos="fade-up" data-aos-delay="200">
                            <div class="faq-content">
                                <div class="accordion custom-accordion" id="accordionPanelsStayOpenExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true"
                                                aria-controls="panelsStayOpen-collapseOne">
                                                <i class="bi bi-camera-fill me-2"
                                                    style="color: var(--primary-dark);"></i>
                                                Apakah sistem presensi wajah aman untuk siswa?
                                            </button>
                                        </h2>
                                        <div class="accordion-collapse collapse show" id="panelsStayOpen-collapseOne">
                                            <div class="accordion-body">
                                                Sangat aman! Sistem kami menggunakan teknologi AI yang tidak menyimpan
                                                foto wajah secara utuh,
                                                melainkan mengonversi menjadi data matematis terenkripsi. Data siswa
                                                dilindungi sesuai standar
                                                keamanan internasional dan hanya dapat diakses oleh pihak yang berwenang
                                                di sekolah.
                                                Sistem juga dilengkapi fitur anti-spoofing untuk mencegah manipulasi
                                                menggunakan foto atau video.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo"
                                                aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                                <i class="bi bi-gear-fill me-2"
                                                    style="color: var(--primary-dark);"></i>
                                                Berapa lama waktu implementasi sistem di sekolah?
                                            </button>
                                        </h2>
                                        <div class="accordion-collapse collapse" id="panelsStayOpen-collapseTwo">
                                            <div class="accordion-body">
                                                Implementasi lengkap biasanya memerlukan 2-4 minggu, tergantung ukuran
                                                sekolah.
                                                Prosesnya meliputi: instalasi perangkat keras (1-2 hari), setup software
                                                dan database (3-5 hari),
                                                pelatihan guru dan staff (2-3 hari), serta pendampingan selama 1 minggu
                                                pertama.
                                                Tim teknis kami akan memastikan semua berjalan lancar sebelum sistem
                                                fully operational.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false"
                                                aria-controls="panelsStayOpen-collapseThree">
                                                <i class="bi bi-phone-fill me-2"
                                                    style="color: var(--primary-dark);"></i>
                                                Apakah orang tua siswa bisa mengakses informasi presensi?
                                            </button>
                                        </h2>
                                        <div class="accordion-collapse collapse" id="panelsStayOpen-collapseThree">
                                            <div class="accordion-body">
                                                Ya! Orang tua mendapat akses khusus melalui web.
                                                Mereka dapat melihat: kehadiran harian anak, rekap bulanan, poin
                                                prestasi/pelanggaran,
                                                dan menerima notifikasi real-time saat anak tiba/pulang sekolah.
                                                Akses dibatasi hanya untuk data anak sendiri demi menjaga privasi siswa
                                                lain.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false"
                                                aria-controls="panelsStayOpen-collapseFour">
                                                <i class="bi bi-people-fill me-2"
                                                    style="color: var(--primary-dark);"></i>
                                                Apakah guru perlu pelatihan khusus untuk mengoperasikan sistem?
                                            </button>
                                        </h2>
                                        <div class="accordion-collapse collapse" id="panelsStayOpen-collapseFour">
                                            <div class="accordion-body">
                                                Sistem dirancang user-friendly sehingga mudah dipelajari! Pelatihan
                                                dasar hanya butuh 2-3 jam
                                                untuk guru dan staff. Kami menyediakan: workshop hands-on, video
                                                tutorial interaktif,
                                                user manual berbahasa Indonesia, dan pendampingan langsung 1 minggu
                                                pertama.
                                                Interface intuitif seperti aplikasi smartphone yang sudah familiar bagi
                                                semua kalangan.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false"
                                                aria-controls="panelsStayOpen-collapseFive">
                                                <i class="bi bi-graph-up me-2"
                                                    style="color: var(--primary-dark);"></i>
                                                Bagaimana sistem membantu meningkatkan kedisiplinan siswa?
                                            </button>
                                        </h2>
                                        <div class="accordion-collapse collapse" id="panelsStayOpen-collapseFive">
                                            <div class="accordion-body">
                                                Sistem poin terintegrasi memberikan feedback instan kepada siswa tentang
                                                perilaku mereka.
                                                Prestasi mendapat poin positif, pelanggaran dikurangi poin dengan
                                                kategori yang jelas.
                                                Dashboard personal memotivasi siswa untuk berkompetisi positif.
                                                Laporan berkala ke orang tua menciptakan sinergi pengawasan
                                                rumah-sekolah yang efektif
                                                untuk membentuk karakter disiplin jangka panjang.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End FAQ-->

            <!-- ======= Contact =======-->
            <section class="section contact__v2" id="kontak">
                <div class="container">
                    <div class="section-header">
                        <span class="section-subtitle" data-aos="fade-up" data-aos-delay="0">
                            <i class="bi bi-telephone-fill me-2"></i>
                            Hubungi Kami
                        </span>
                        <h2 class="h2 fw-bold mb-3" data-aos="fade-up" data-aos-delay="0">
                            Siap Membantu Transformasi Digital Sekolah Anda
                        </h2>
                        <p data-aos="fade-up" data-aos-delay="100">
                            Tim ahli kami siap membantu implementasi sistem presensi pintar di sekolah Anda
                        </p>
                    </div>
                    <div class="row g-5">
                        <div class="col-md-6">
                            <div class="d-flex gap-4 flex-column">
                                <div class="d-flex align-items-start gap-3" data-aos="fade-up" data-aos-delay="0">
                                    <div class="contact-icon">
                                        <i class="bi bi-telephone"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Telepon</h5>
                                        <p class="text-muted mb-0">Konsultasi gratis via telepon</p>
                                        <strong class="text-primary">+62 </strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3" data-aos="fade-up" data-aos-delay="100">
                                    <div class="contact-icon">
                                        <i class="bi bi-envelope"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Email</h5>
                                        <p class="text-muted mb-0">Kirim pertanyaan detail</p>
                                        <strong class="text-primary">info@</strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3" data-aos="fade-up" data-aos-delay="200">
                                    <div class="contact-icon">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Alamat Kantor</h5>
                                        <p class="text-muted mb-0">Kunjungi kantor untuk demo langsung</p>
                                        <address class="text-primary mb-0">
                                            Jl.
                                        </address>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrapper p-4" data-aos="fade-up" data-aos-delay="300">
                                <div class="d-flex align-items-center mb-4">
                                    <i class="bi bi-chat-dots-fill"
                                        style="font-size: 1.5rem; color: var(--primary-bright);"></i>
                                    <h4 class="ms-3 mb-0">Kirim Pesan</h4>
                                </div>
                                <form id="contactForm">
                                    <div class="mb-3">
                                        <label class="form-label" for="name">Nama Sekolah *</label>
                                        <input class="form-control" id="name" type="text" name="name"
                                            required="" placeholder="Contoh: SMA Negeri 1 Jakarta">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="email">Email *</label>
                                        <input class="form-control" id="email" type="email" name="email"
                                            required="" placeholder="admin@sekolah.sch.id">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="subject">Subjek</label>
                                        <select class="form-control" id="subject" name="subject">
                                            <option value="">Pilih topik</option>
                                            <option value="demo">Request Demo</option>
                                            <option value="pricing">Informasi Harga</option>
                                            <option value="support">Technical Support</option>
                                            <option value="partnership">Kerjasama</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label" for="message">Pesan *</label>
                                        <textarea class="form-control" id="message" name="message" rows="4" required=""
                                            placeholder="Ceritakan kebutuhan sekolah Anda..."></textarea>
                                    </div>
                                    <button class="btn btn-primary w-100 fw-semibold" type="submit">
                                        <i class="bi bi-send"></i>
                                        Kirim Pesan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End Contact-->

        </main>

        <!-- ======= Footer =======-->
        <footer class="footer pt-5 pb-4">
            <div class="container">
                <div class="row mb-5 pb-4">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-rocket-takeoff-fill me-3"
                                style="font-size: 1.5rem; color: var(--primary-light);"></i>
                            <h3 class="fs-4 text-white mb-0">Bergabung dengan Revolusi Pendidikan Digital</h3>
                        </div>
                        <p class="text-white-50 mb-4">Dapatkan update terbaru tentang fitur-fitur inovatif dan tips
                            optimalisasi manajemen sekolah</p>
                    </div>
                    <div class="col-md-4">
                        <form class="d-flex gap-2">
                            <input class="form-control" type="email" placeholder="Email sekolah Anda"
                                required="">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="row justify-content-between mb-4 g-4">
                    <div class="col-md-4 mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <!-- Replace 'logo.png' with your actual logo file name -->
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Diara TimeSchool Logo"
                                class="footer-logo"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                            <!-- Fallback icon if logo doesn't load -->
                            <div class="footer-icon-fallback" style="display: none;">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <h4 class="text-white mb-0">Diara TimeSchool</h4>
                        </div>
                        <p class="text-white-50 mb-4">
                            Platform manajemen sekolah terdepan dengan teknologi AI untuk presensi wajah dan monitoring
                            siswa real-time.
                        </p>
                        <div class="d-flex gap-3">
                            <a href="#" class="text-white-50 hover-primary">
                                <i class="bi bi-facebook fs-5"></i>
                            </a>
                            <a href="#" class="text-white-50 hover-primary">
                                <i class="bi bi-instagram fs-5"></i>
                            </a>
                            <a href="#" class="text-white-50 hover-primary">
                                <i class="bi bi-youtube fs-5"></i>
                            </a>
                            <a href="#" class="text-white-50 hover-primary">
                                <i class="bi bi-linkedin fs-5"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <h5 class="mb-3 text-white">Fitur Unggulan</h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><a href="#fitur" class="text-white-50 text-decoration-none">
                                            <i class="bi bi-camera-fill me-2"></i>Presensi Wajah AI
                                        </a></li>
                                    <li class="mb-2"><a href="#fitur" class="text-white-50 text-decoration-none">
                                            <i class="bi bi-clipboard-check me-2"></i>Pencatatan Digital
                                        </a></li>
                                    <li class="mb-2"><a href="#fitur" class="text-white-50 text-decoration-none">
                                            <i class="bi bi-graph-up me-2"></i>Dashboard Analytics
                                        </a></li>
                                    <li class="mb-2"><a href="#cara-kerja"
                                            class="text-white-50 text-decoration-none">
                                            <i class="bi bi-gear-fill me-2"></i>Cara Kerja
                                        </a></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h5 class="mb-3 text-white">Dukungan</h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><a href="#kontak" class="text-white-50 text-decoration-none">
                                            <i class="bi bi-telephone me-2"></i>Hubungi Kami
                                        </a></li>
                                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">
                                            <i class="bi bi-book me-2"></i>Panduan Pengguna
                                        </a></li>
                                    <li class="mb-2"><a href="#faq" class="text-white-50 text-decoration-none">
                                            <i class="bi bi-question-circle me-2"></i>FAQ
                                        </a></li>
                                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">
                                            <i class="bi bi-play-circle me-2"></i>Tutorial Video
                                        </a></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h5 class="mb-3 text-white">Kontak</h5>
                                <div class="text-white-50">
                                    <p class="mb-2 d-flex align-items-center">
                                        <i class="bi bi-geo-alt me-2"></i>
                                        12345
                                    </p>
                                    <p class="mb-2 d-flex align-items-center">
                                        <i class="bi bi-envelope me-2"></i>
                                        info@
                                    </p>
                                    <p class="mb-0 d-flex align-items-center">
                                        <i class="bi bi-telephone me-2"></i>
                                        +62
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="border-white-50">
                <div class="row align-items-center pt-3">
                    <div class="col-md-8 text-center text-md-start">
                        <span class="text-white-50">
                            &copy;
                            <script>
                                document.write(new Date().getFullYear());
                            </script>
                            Diara TimeSchool. Semua hak dilindungi. ❤️ Dibuat untuk kemajuan pendidikan Indonesia
                        </span>
                    </div>
                    <div class="col-md-4 text-center text-md-end">
                        <span class="text-white-50"> Sistem Presensi Pintar Masa Depan</span>
                    </div>
                </div>
            </div>
        </footer>
        <!-- End Footer-->

    </div>

    <!-- ======= Back to Top =======-->
    <button id="back-to-top" style="background: var(--primary-bright) !important; border: none; border-radius: 12px;">
        <i class="bi bi-arrow-up-short"></i>
    </button>
    <!-- End Back to top-->

    <!-- ======= Javascripts =======-->
    <script src="{{ asset('assets/vendors/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/gsap/gsap.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/isotope/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/glightbox/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/aos/aos.js') }}"></script>
    <script src="{{ asset('assets/vendors/purecounter/purecounter.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/send_email.js') }}"></script>

    <!-- Custom Animations Script -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });

        // Add scroll animations for elements
        function animateOnScroll() {
            const elements = document.querySelectorAll('.animate-on-scroll');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;

                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('animated');
                }
            });
        }

        window.addEventListener('scroll', animateOnScroll);

        // Add hover effects to service cards
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('mouseover', () => {
                card.querySelector('.icon').classList.add('animate__pulse');
            });
            card.addEventListener('mouseout', () => {
                card.querySelector('.icon').classList.remove('animate__pulse');
            });
        });
    </script>
    <!-- End Custom Script-->

</body>

</html>
