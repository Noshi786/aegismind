<?php
// Determine active page for navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Infrastructure // AegisMind</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0b0f19;
            color: #f1f5f9;
            font-family: 'JetBrains Mono', monospace, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            padding-top: 80px;
            overflow-x: hidden;
        }
        a { text-decoration: none; color: inherit; }

        #network-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            background: radial-gradient(circle at 30% 40%, #0f1a2e, #070b12);
        }

        .navbar {
            background: rgba(11, 15, 25, 0.75);
            backdrop-filter: blur(14px) saturate(180%);
            -webkit-backdrop-filter: blur(14px) saturate(180%);
            border-bottom: 1px solid rgba(56, 189, 248, 0.15);
            transition: box-shadow 0.3s ease, background 0.3s;
            z-index: 1030;
        }
        .navbar.scrolled {
            background: rgba(8, 12, 22, 0.92);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.7);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            color: #fff !important;
        }
        .navbar-brand span { color: #38bdf8; }
        .nav-link {
            color: #cbd5e1 !important;
            font-weight: 500;
            margin-left: 1.2rem;
            transition: color 0.2s;
            font-size: 0.9rem;
            letter-spacing: 0.3px;
        }
        .nav-link:hover, .nav-link.active { color: #38bdf8 !important; }

        .main-card {
            position: relative;
            z-index: 2;
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 24px;
            padding: 2rem;
            backdrop-filter: blur(2px);
        }
        .section-title {
            color: #38bdf8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }
        .page-title {
            color: #ffffff !important;
            font-weight: 700;
        }
        .page-subtitle {
            color: #94a3b8 !important;
        }
        
        .form-control {
            background: #111827;
            border: 1px solid #1e293b;
            color: #ffffff !important;
        }
        .form-control::placeholder {
            color: #64748b !important;
        }
        .form-control:focus {
            background: #111827;
            border-color: #38bdf8;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.2rem rgba(56, 189, 248, 0.2);
        }
        .form-label {
            color: #cbd5e1 !important;
            font-weight: 500;
        }
        
        .btn-send {
            background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.25s;
            color: #ffffff !important;
        }
        .btn-send:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(56, 189, 248, 0.25); 
            color: #ffffff !important; 
        }
        
        .contact-info {
            background: #111827;
            border: 1px solid #1e293b;
            border-radius: 16px;
            padding: 1.5rem;
        }
        .contact-label {
            color: #cbd5e1 !important;
            font-weight: 600;
        }
        .contact-text {
            color: #e2e8f0 !important;
        }
        .contact-icon {
            color: #38bdf8 !important;
        }

        footer {
            position: relative;
            z-index: 2;
            background: #0f172a;
            border-top: 1px solid #1e293b;
            color: #cbd5e1 !important;
            font-size: 0.9em;
            padding: 2.5rem 0;
            margin-top: 4rem;
        }
        .footer-logo { font-weight: 700; font-size: 1.3rem; color: #fff; }
        .footer-logo span { color: #38bdf8; }
        .footer-links a {
            color: #94a3b8 !important;
            transition: color 0.2s;
            font-size: 0.9rem;
        }
        .footer-links a:hover { color: #38bdf8 !important; }
        .social-icon {
            color: #94a3b8 !important;
            font-size: 1.25rem;
            margin-left: 1rem;
            transition: color 0.2s, transform 0.2s;
        }
        .social-icon:hover { color: #38bdf8 !important; transform: translateY(-3px); }

        #backToTop {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #1e293b;
            color: #38bdf8;
            border: 1px solid #38bdf8;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            z-index: 1050;
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            cursor: pointer;
            backdrop-filter: blur(4px);
        }
        #backToTop.visible { opacity: 1; }
        #backToTop:hover { background: #38bdf8; color: #0b0f19; transform: scale(1.05); }

        @media (max-width: 768px) {
            .navbar-brand { font-size: 1.2rem; }
            .main-card { padding: 1.2rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            * { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>

    <!-- ====== NETWORK CANVAS ====== -->
    <canvas id="network-canvas"></canvas>

    <!-- ====== NAVIGATION ====== -->
    <nav class="navbar navbar-expand-lg fixed-top py-2" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="index.php">Aegis<span>Mind</span></a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navCollapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'articles.php') ? 'active' : ''; ?>" href="articles.php">Articles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'product_register.php') ? 'active' : ''; ?>" href="product_register.php">Register Course</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ====== MAIN CONTENT ====== -->
    <div class="container mt-4">
        <div class="main-card">
            <div class="row mb-4">
                <div class="col-12">
                    <span class="section-title"><i class="fas fa-envelope me-2"></i>Get in Touch</span>
                    <h2 class="page-title mt-2">Contact <span style="color:#38bdf8;">Us</span></h2>
                    <p class="page-subtitle">We're here to help with your cybersecurity needs</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Query registered on secure nodes.');">
                        <div class="mb-3">
                            <label class="form-label small">Full Name</label>
                            <input type="text" class="form-control" placeholder="Enter your name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Email Address</label>
                            <input type="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Phone Number</label>
                            <input type="tel" class="form-control" placeholder="+92 300 1234567">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Message Details</label>
                            <textarea class="form-control" rows="4" placeholder="Describe network symptoms or pipeline targets..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-send w-100">Send Message <i class="fas fa-paper-plane ms-2"></i></button>
                    </form>
                </div>
                <div class="col-lg-6">
                    <div class="contact-info">
                        <h5 class="contact-label mb-3"><i class="fas fa-location-dot me-2 contact-icon"></i>Office</h5>
                        <p class="contact-text">Tech Enclave, Quetta, Pakistan</p>
                        
                        <h5 class="contact-label mt-4"><i class="fas fa-envelope me-2 contact-icon"></i>Email</h5>
                        <p class="contact-text">NoshinFitras@aegismind.io</p>
                        
                        <h5 class="contact-label mt-4"><i class="fas fa-phone me-2 contact-icon"></i>Phone</h5>
                        <p class="contact-text">+92 300 1234567</p>
                        
                        <h5 class="contact-label mt-4"><i class="fas fa-clock me-2 contact-icon"></i>Hours</h5>
                        <p class="contact-text">Mon-Fri: 9:00 AM - 6:00 PM (PKT)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====== FOOTER ====== -->
    <footer>
        <div class="container">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-4 mb-3 mb-md-0 footer-links">
                    <ul class="list-unstyled d-flex flex-column gap-1">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="articles.php">Articles</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="product_register.php">Register Course</a></li>
                    </ul>
                </div>
                <div class="col-md-4 text-center mb-3 mb-md-0">
                    <div class="footer-logo">Aegis<span>Mind</span></div>
                    <p class="small text-muted mb-0" style="color: #94a3b8 !important;">&copy; 2026 AegisMind Security Services.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <p class="small text-muted mb-1" style="color: #94a3b8 !important;">Tech Enclave, Quetta, PK</p>
                    <p class="small text-muted mb-2" style="color: #94a3b8 !important;">NoshinFitras@aegismind.io</p>
                    <div>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <div id="backToTop" title="back to top"><i class="fas fa-chevron-up"></i></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    (function() {
        "use strict";

        // ---------- network canvas ----------
        const canvas = document.getElementById('network-canvas');
        const ctx = canvas.getContext('2d');
        let w, h;
        const nodes = [];
        const nodeCount = 45;
        const maxDist = 150;

        function resizeCanvas() {
            w = canvas.width = window.innerWidth;
            h = canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        class Node {
            constructor() { this.reset(); }
            reset() {
                this.x = Math.random() * w;
                this.y = Math.random() * h;
                this.vx = (Math.random() - 0.5) * 0.6;
                this.vy = (Math.random() - 0.5) * 0.6;
                this.r = 2 + Math.random() * 3;
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;
                if (this.x < 0 || this.x > w) { this.vx *= -1; this.x = Math.max(0, Math.min(w, this.x)); }
                if (this.y < 0 || this.y > h) { this.vy *= -1; this.y = Math.max(0, Math.min(h, this.y)); }
            }
        }

        for (let i = 0; i < nodeCount; i++) nodes.push(new Node());

        function drawNetwork() {
            ctx.clearRect(0, 0, w, h);

            for (let i = 0; i < nodes.length; i++) {
                for (let j = i + 1; j < nodes.length; j++) {
                    const dx = nodes[i].x - nodes[j].x;
                    const dy = nodes[i].y - nodes[j].y;
                    const dist = Math.sqrt(dx*dx + dy*dy);
                    if (dist < maxDist) {
                        const alpha = 1 - (dist / maxDist);
                        ctx.beginPath();
                        ctx.moveTo(nodes[i].x, nodes[i].y);
                        ctx.lineTo(nodes[j].x, nodes[j].y);
                        ctx.strokeStyle = `rgba(56, 189, 248, ${alpha * 0.5})`;
                        ctx.lineWidth = 0.7 + alpha * 1.2;
                        ctx.stroke();
                    }
                }
            }

            nodes.forEach(n => {
                ctx.beginPath();
                ctx.arc(n.x, n.y, n.r, 0, 2 * Math.PI);
                ctx.fillStyle = '#38bdf8';
                ctx.shadowColor = '#38bdf8';
                ctx.shadowBlur = 18;
                ctx.fill();
                ctx.shadowBlur = 0;
            });

            nodes.forEach(n => n.update());
            requestAnimationFrame(drawNetwork);
        }
        drawNetwork();

        // ---------- navbar shadow ----------
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 40) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
        });

        // ---------- back to top ----------
        const backBtn = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 600) backBtn.classList.add('visible');
            else backBtn.classList.remove('visible');
        });
        backBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

    })();
    </script>
</body>
</html>