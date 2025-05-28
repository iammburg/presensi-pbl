<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <title>SIAKAD Polines</title>
  <style>
    a, button, input, select, h1, h2, h3, h4, h5, * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      border: none;
      text-decoration: none;
      background: none;
      -webkit-font-smoothing: antialiased;
    }
    menu, ol, ul {
      list-style-type: none;
      margin: 0;
      padding: 0;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header Section -->
    <header class="header">
      <div class="logo-text">
        Sistem Informasi<br>Akademik Sekolah Negeri Polines
      </div>
      <button class="mobile-menu-toggle">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <nav class="nav-menu">
        <a href="#" class="nav-link">BERANDA</a>
        <a href="#" class="nav-link">FITUR</a>
        <a href="#" class="nav-link">TENTANG</a>
        <a href="#" class="nav-link">KONTAK</a>
        <a href="{{ route('login') }}" class="login-btn">LOGIN</a>
      </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1 class="hero-title">SIAKAD</h1>
        <p class="hero-description">
          Media digital berbasis web penanganan pengolaan data akademik, presensi, dan Poin Siswa.
        </p>
        <a href="{{ route('login') }}" class="hero-btn">LOGIN</a>
      </div>
      <div class="hero-images">
        <img class="hero-image-left mobile-adjustable" src="{{ asset('assets/images/landing/fix-siakad-2-10.png') }}" alt="SIAKAD Illustration">
        <img class="hero-image-right mobile-adjustable" src="{{ asset('assets/images/landing/group-425-10.png') }}" alt="Additional Illustration">
      </div>
    </section>

    <!-- Services Section -->
    <section class="services">
      <div class="decoration plus-left">
        <img src="{{ asset('assets/images/landing/plus-kiri-10.png') }}" alt="Decoration">
      </div>
      <div class="decoration plus-right">
        <img src="{{ asset('assets/images/landing/plus-kanan-10.png') }}" alt="Decoration">
      </div>
      <h2 class="section-title">Layanan Terbaik Kami</h2>
      <p class="section-subtitle">
        Dengan dukungan infrastruktu dan sistem yang modern, semua proses dapat berjalan dengan lancar
      </p>
      <div class="services-grid">
        <!-- Service 1 -->
        <div class="service-card">
          <div class="card-icon">
            <img src="{{ asset('assets/images/landing/fontisto-date0.svg') }}" alt="Presensi Icon">
          </div>
          <h3 class="card-title">Presensi Siswa</h3>
          <p class="card-description">
            Menyediakan sistem kehadiran berbasis pemindaian wajah untuk memastikan keakuratan dan efisiensi absensi siswa di setiap sesi pembelajaran.
          </p>
        </div>
        <!-- Service 2 -->
        <div class="service-card">
          <div class="card-icon">
            <img src="{{ asset('assets/images/landing/fluent-mdl-2-report-warning0.svg') }}" alt="Laporan Icon">
          </div>
          <h3 class="card-title">Laporan</h3>
          <p class="card-description">
            Mencatat dan memantau pelanggaran yang dilakukan siswa sebagai dasar penanganan dan pembinaan yang tepat sesuai peraturan sekolah.
          </p>
        </div>
        <!-- Service 3 -->
        <div class="service-card">
          <div class="card-icon">
            <img src="{{ asset('assets/images/landing/carbon-report-data0.svg') }}" alt="Monitoring Icon">
          </div>
          <h3 class="card-title">Monitoring Poin</h3>
          <p class="card-description">
            Memantau secara real-time akumulasi poin prestasi dan poin pelanggaran siswa sebagai dasar evaluasi, pembinaan, dan pemberian penghargaan.
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section class="contact">
      <div class="contact-content">
        <h2 class="contact-title">Contact Us</h2>
        <p class="contact-subtitle">
          Most calendars are designed for teams.<br>
          Slate is designed for freelancers
        </p>
      </div>
      <div class="contact-container">
        <div class="contact-form">
          <h3 class="form-title">Contact Us</h3>
          <div class="form-group">
            <input type="text" placeholder="Your Name">
          </div>
          <div class="form-group">
            <input type="email" placeholder="Your Email">
          </div>
          <div class="form-group message">
            <textarea placeholder="Your Message"></textarea>
          </div>
          <button class="submit-btn">Send</button>
        </div>
        <div class="contact-info">
          <div class="info-item">
            <img src="{{ asset('assets/images/landing/bx-bx-map1.svg') }}" alt="Location Icon">
            <p>
              Jl. Prof. Soedarto, Tembalang,<br>
              Kec. Tembalang, Kota Semarang<br>
              Jawa Tengah 50275
            </p>
          </div>
          <div class="info-item">
            <img src="{{ asset('assets/images/landing/ic-baseline-phone-android1.svg') }}" alt="Phone Icon">
            <p>(843) 555-0130</p>
          </div>
          <div class="info-item">
            <img src="{{ asset('assets/images/landing/ant-design-mail-outlined1.svg') }}" alt="Email Icon">
            <p>info@polines.ac.id</p>
          </div>
          <img class="contact-image" src="{{ asset('assets/images/landing/image-20.png') }}" alt="Contact Image">
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <!-- Kolom 1: Pages -->
        <div class="footer-section">
          <h4 class="footer-title">Pages</h4>
          <ul class="footer-links">
            <li><a href="#">Home</a></li>
            <li><a href="#">Fitur</a></li>
            <li><a href="#">Tentang Kami</a></li>
            <li><a href="#">Kontak</a></li>
          </ul>
        </div>
        
        <!-- Kolom 2: Tim Pengembang -->
        <div class="footer-section">
          <h4 class="footer-title">Tim Pengembang</h4>
          <ul class="footer-links">
            <li><a href="#">IK-A POLINES 2023</a></li>
          </ul>
        </div>
        
        <!-- Kolom 3: Hubungi Kami + Sosial Media -->
        <div class="footer-section">
          <h4 class="footer-title">Hubungi Kami</h4>
          <div class="contact-details">
            <div class="contact-item">
              <img src="{{ asset('assets/images/landing/bx-bx-map0.svg') }}" alt="Location Icon" class="footer-icon">
              <p>Jl. Prof. Soedarto, Tembalang, Kec. Tembalang, Kota Semarang, Jawa Tengah 50275</p>
            </div>
            <div class="contact-item">
              <img src="{{ asset('assets/images/landing/ic-baseline-phone-android0.svg') }}" alt="Phone Icon" class="footer-icon">
              <p>(239) 555-0108</p>
            </div>
            <div class="contact-item">
              <img src="{{ asset('assets/images/landing/ant-design-mail-outlined0.svg') }}" alt="Email Icon" class="footer-icon">
              <p>info@polines.ac.id</p>
            </div>
            <div class="social-icons">
              <a href="#"><img src="{{ asset('assets/images/landing/ant-design-twitter-outlined0.svg') }}" alt="Twitter" class="social-icon"></a>
              <a href="#"><img src="{{ asset('assets/images/landing/ant-design-facebook-filled0.svg') }}" alt="Facebook" class="social-icon"></a>
              <a href="#"><img src="{{ asset('assets/images/landing/grommet-icons-instagram0.svg') }}" alt="Instagram" class="social-icon"></a>
            </div>
          </div>
        </div>
        
        <!-- Kolom 4: Logo Sistem -->
        <div class="footer-section">
          <div class="footer-logo">
            <img src="{{ asset('assets/images/landing/siakad-icon0.png') }}" alt="SIAKAD Logo" class="footer-logo-img">
            <div>
              <p class="logo-title">Sistem Informasi Akademik</p>
              <p class="logo-subtitle">Sekolah Negeri Polines</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="footer-bottom">
        <div class="copyright-box">
          © 2025 POLINES. All rights reserved.
        </div>
      </div>
    </footer>
  </div>
  <script>
  document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
    document.querySelector('.nav-menu').classList.toggle('active');
    this.classList.toggle('active');
  });
  </script>
</body>
</html>