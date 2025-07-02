<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page - Student Savings Monitoring</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
            background-color: #f8f9fa;
        }

        /* Header Styles - Enhanced */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 30px;
            background: linear-gradient(135deg, rgba(44, 62, 80, 0.95) 0%, rgba(52, 73, 94, 0.95) 100%);
            color: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
            animation: slideDown 0.8s ease-out;
            transition: all 0.3s ease;
        }

        .header.scrolled {
            background: linear-gradient(135deg, rgba(44, 62, 80, 1) 0%, rgba(52, 73, 94, 1) 100%);
            padding: 8px 30px;
        }

        .header h1 {
            font-size: clamp(18px, 3vw, 24px);
            font-weight: 700;
            letter-spacing: 1px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            color: #f8c471;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-menu {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .nav-item {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: clamp(14px, 2.5vw, 16px);
        }

        .nav-item:hover {
            background: rgba(248, 196, 113, 0.2);
            transform: translateY(-2px);
            color: #f8c471;
        }

        .mobile-menu-toggle {
            display: none;
            color: #f8c471;
            font-size: 24px;
            cursor: pointer;
        }

        /* Main Content - Enhanced */
        .main-content {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            padding: 100px 20px 20px;
            overflow: hidden;
        }

        .main-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .hero-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            max-width: 1200px;
            width: 100%;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .hero-text {
            text-align: left;
            animation: slideInLeft 1s ease-out;
        }

        .hero-text h2 {
            font-size: clamp(2.5rem, 8vw, 4rem);
            font-weight: 900;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #ffffff 0%, #f8c471 50%, #ffffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            position: relative;
        }

        .hero-text h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100px;
            height: 4px;
            background: linear-gradient(135deg, #f8c471 0%, #f4d03f 100%);
            border-radius: 2px;
        }

        .hero-text p {
            font-size: clamp(1rem, 3vw, 1.3rem);
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 35px;
            line-height: 1.6;
            font-weight: 400;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: clamp(0.9rem, 2.5vw, 1.1rem);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f8c471 0%, #f4d03f 100%);
            color: #2c3e50;
            border: 2px solid #f8c471;
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(248, 196, 113, 0.4);
            background: linear-gradient(135deg, #f4d03f 0%, #f8c471 100%);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px) scale(1.05);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .hero-image {
            position: relative;
            animation: slideInRight 1s ease-out;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-container {
            position: relative;
            display: inline-block;
        }

        .hero-image img {
            max-width: 100%;
            width: clamp(300px, 50vw, 500px);
            height: auto;
            border-radius: 50%;
            border: 8px solid rgba(248, 196, 113, 0.8);
            box-shadow:
                0 20px 40px rgba(44, 62, 80, 0.3),
                0 2px 8px rgba(248, 196, 113, 0.20),
                inset 0 0 0 2px rgba(255, 255, 255, 0.1);
            background: linear-gradient(135deg, #f8c471 0%, #f4d03f 100%);
            padding: 15px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: float 6s ease-in-out infinite;
        }

        .hero-image img:hover {
            transform: scale(1.05) rotate(-2deg);
            box-shadow:
                0 32px 60px rgba(44, 62, 80, 0.4),
                0 4px 16px rgba(248, 196, 113, 0.25);
        }

        .image-container::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            right: -20px;
            bottom: -20px;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(248, 196, 113, 0.3), rgba(244, 208, 63, 0.3));
            animation: rotate 20s linear infinite;
            z-index: -1;
        }

        /* Features Section - Enhanced */
        .features {
            padding: 100px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            position: relative;
        }

        .features::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            clip-path: polygon(0 0, 100% 0, 100% 70%, 0 100%);
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .features h3 {
            font-size: clamp(2rem, 5vw, 3rem);
            margin-bottom: 20px;
            color: #2c3e50;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .features p {
            font-size: clamp(1rem, 3vw, 1.2rem);
            color: #5a6c7d;
            margin-bottom: 60px;
            font-weight: 500;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .feature-card {
            padding: 40px 30px;
            background: linear-gradient(135deg, rgba(248, 196, 113, 0.9) 0%, rgba(244, 208, 63, 0.9) 100%);
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(44, 62, 80, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 50px rgba(44, 62, 80, 0.2);
            background: linear-gradient(135deg, rgba(244, 208, 63, 0.9) 0%, rgba(248, 196, 113, 0.9) 100%);
            border-color: rgba(44, 62, 80, 0.1);
        }

        .feature-icon {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            color: #2c3e50;
            margin-bottom: 25px;
            transition: transform 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-card h4 {
            font-size: clamp(1.2rem, 3vw, 1.6rem);
            margin-bottom: 20px;
            color: #2c3e50;
            font-weight: 700;
        }

        .feature-card p {
            color: #2c3e50;
            margin-bottom: 0;
            font-weight: 500;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
            line-height: 1.6;
        }

        /* Footer - Enhanced */
        footer {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            text-align: center;
            padding: 50px 20px;
            position: relative;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-content p {
            margin-bottom: 25px;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
            color: #ecf0f1;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-bottom: 25px;
        }

        .social-links a {
            color: #f8c471;
            font-size: clamp(1.2rem, 3vw, 1.8rem);
            transition: all 0.3s ease;
            padding: 10px;
            border-radius: 50%;
            background: rgba(248, 196, 113, 0.1);
        }

        .social-links a:hover {
            color: #f4d03f;
            transform: translateY(-5px) scale(1.1);
            background: rgba(248, 196, 113, 0.2);
        }

        /* Animations */
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Enhanced Responsive Design */
        @media (max-width: 1024px) {
            .hero-container {
                gap: 40px;
            }

            .features-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 25px;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 12px 20px;
            }

            .nav-menu {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                background: linear-gradient(135deg, rgba(44, 62, 80, 0.98) 0%, rgba(52, 73, 94, 0.98) 100%);
                backdrop-filter: blur(10px);
                flex-direction: column;
                padding: 20px;
                transition: left 0.3s ease;
                gap: 10px;
            }

            .nav-menu.active {
                left: 0;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .hero-container {
                grid-template-columns: 1fr;
                gap: 30px;
                text-align: center;
                padding: 0 10px;
            }

            .hero-text {
                text-align: center;
            }

            .hero-text h2::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .cta-buttons {
                justify-content: center;
                gap: 15px;
            }

            .btn {
                padding: 14px 24px;
                font-size: 0.95rem;
                min-width: 140px;
            }

            .features {
                padding: 80px 15px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .feature-card {
                padding: 30px 20px;
            }

            .main-content {
                padding: 80px 15px 20px;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 16px;
            }

            .hero-image img {
                width: 280px;
                padding: 10px;
            }

            .btn {
                padding: 12px 20px;
                font-size: 0.9rem;
                flex: 1;
                max-width: 160px;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .social-links {
                gap: 20px;
            }

            .feature-card {
                padding: 25px 15px;
            }
        }

        /* Loading animation */
        .loading {
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-piggy-bank"></i> Sistem Monitoring Tabungan</h1>
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <nav class="nav-menu">
            <a href="#home" class="nav-item">Home</a>
            <a href="#features" class="nav-item">Features</a>
        </nav>
    </div>

    <div class="main-content" id="home">
        <div class="hero-container loading">
            <div class="hero-text">
                <h2>Monitoring<br>Tabungan<br>Siswa</h2>
                <p>Kelola dan pantau tabungan siswa dengan mudah dan efisien. Sistem yang aman, transparan, dan user-friendly untuk pendidikan finansial yang lebih baik.</p>
                <div class="cta-buttons">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </a>
                    <a href="#features" class="btn btn-secondary">
                        <i class="fas fa-info-circle"></i>
                        Learn More
                    </a>
                </div>
            </div>

            <div class="hero-image">
                <div class="image-container">
                    <img src="{{ asset('images/01h13667jdaaqcp8cpbw3gtesj.jpg') }}" alt="Piggy Bank">
                </div>
            </div>
        </div>
    </div>

    <div class="features" id="features">
        <div class="features-container">
            <h3>Fitur Unggulan</h3>
            <p>Sistem monitoring tabungan siswa yang komprehensif dan mudah digunakan untuk semua kalangan</p>

            <div class="features-grid">
                <div class="feature-card loading">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Tracking Real-time</h4>
                    <p>Pantau perkembangan tabungan siswa secara real-time dengan dashboard yang interaktif dan mudah dipahami oleh semua kalangan.</p>
                </div>

                <div class="feature-card loading">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Keamanan Terjamin</h4>
                    <p>Data tabungan siswa dilindungi dengan sistem keamanan berlapis dan enkripsi tingkat tinggi yang terpercaya.</p>
                </div>

                <div class="feature-card loading">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h4>Mobile Friendly</h4>
                    <p>Akses sistem dari mana saja dan kapan saja melalui smartphone atau tablet dengan tampilan yang responsif dan intuitif.</p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="social-links">
                <a href="https://www.facebook.com/share/15ZDnUmFzj/" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/kosasihalfahreza?igsh=Z3dkMGhha3JyeHox" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
            </div>
            <p>&copy; 2025 Engkos Kosasih. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const navMenu = document.querySelector('.nav-menu');

        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });

        // Close mobile menu when clicking on nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                navMenu.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                icon.classList.add('fa-bars');
                icon.classList.remove('fa-times');
            });
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const headerHeight = document.querySelector('.header').offsetHeight;
                    const targetPosition = target.offsetTop - headerHeight;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Enhanced header scroll effect
        let lastScrollTop = 0;
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }

            // Hide header on scroll down, show on scroll up
            if (scrollTop > lastScrollTop && scrollTop > 200) {
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = 'translateY(0)';
            }
            lastScrollTop = scrollTop;
        });

        // Enhanced intersection observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all loading elements
        document.querySelectorAll('.loading').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.8s ease-out';
            observer.observe(el);
        });

        // Stagger animation for feature cards
        const featureCards = document.querySelectorAll('.feature-card');
        featureCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.2}s`;
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const heroImage = document.querySelector('.hero-image img');
            const heroText = document.querySelector('.hero-text');

            if (heroImage && scrolled < window.innerHeight) {
                heroImage.style.transform = `translateY(${scrolled * 0.1}px)`;
                if (heroText) {
                    heroText.style.transform = `translateY(${scrolled * 0.05}px)`;
                }
            }
        });

        // Add loading state management
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
        });

        // Optimize for touch devices
        if ('ontouchstart' in window) {
            document.body.classList.add('touch-device');
        }
    </script>
</body>
</html>
