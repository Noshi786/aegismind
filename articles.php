<?php
// Determine active page for navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Articles // AegisMind Security</title>
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
        .article-card {
            background: #111827;
            border: 1px solid #1e293b;
            border-radius: 16px;
            padding: 1.5rem;
            transition: transform 0.25s, border-color 0.3s;
            height: 100%;
        }
        .article-card:hover {
            transform: translateY(-5px);
            border-color: #38bdf8;
        }
        .article-tag {
            background: #1a2744;
            color: #38bdf8 !important;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            display: inline-block;
            margin-bottom: 10px;
        }
        .article-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 1rem;
            border: 1px solid #1e293b;
        }
        .article-title {
            color: #ffffff !important;
            font-weight: 600;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .article-desc {
            color: #cbd5e1 !important;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .article-link {
            color: #38bdf8 !important;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .article-link:hover {
            color: #0284c7 !important;
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
            color: #94a3b8;
            transition: color 0.2s;
            font-size: 0.9rem;
        }
        .footer-links a:hover { color: #38bdf8; }
        .social-icon {
            color: #94a3b8;
            font-size: 1.25rem;
            margin-left: 1rem;
            transition: color 0.2s, transform 0.2s;
        }
        .social-icon:hover { color: #38bdf8; transform: translateY(-3px); }

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
                    <span class="section-title"><i class="fas fa-newspaper me-2"></i>Latest Updates</span>
                    <h2 class="fw-bold text-white mt-2">Security <span style="color:#38bdf8;">Articles</span></h2>
                    <p class="text-muted" style="color: #94a3b8 !important;">Stay informed with the latest cybersecurity insights</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="article-card">
                        <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=600&q=80" alt="Telemetry" class="article-image">
                        <span class="article-tag">Threat Analysis</span>
                        <h5 class="article-title">Evaluating Network Telemetry</h5>
                        <p class="article-desc">Monitoring unexpected peaks in evening transmission blocks helps pinpoint compromised server resources safely without offline delays.</p>
                        <a href="#" class="article-link">Read more →</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="article-card">
                        <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=600&q=80" alt="Pipelines" class="article-image">
                        <span class="article-tag">Cloud Security</span>
                        <h5 class="article-title">Protecting System Pipelines</h5>
                        <p class="article-desc">Corrupted binary inputs hidden within clean files can quietly skew operational analytical metrics if processing blocks lack sandboxing.</p>
                        <a href="#" class="article-link">Read more →</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="article-card">
                        <img src="https://images.unsplash.com/photo-1510511459019-5dda7724fd87?auto=format&fit=crop&w=600&q=80" alt="Data Protection" class="article-image">
                        <span class="article-tag">Data Security</span>
                        <h5 class="article-title">Practical Information Masking</h5>
                        <p class="article-desc">Stripping critical system logs and raw credential records protects client data secrets perfectly before running analytic routines.</p>
                        <a href="#" class="article-link">Read more →</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="article-card">
                        <img src="https://images.unsplash.com/photo-1601597111158-2fceff292cdc?auto=format&fit=crop&w=600&q=80" alt="Code Integrity" class="article-image">
                        <span class="article-tag">DevSecOps</span>
                        <h5 class="article-title">Defending Source Code Integrity</h5>
                        <p class="article-desc">Small, custom-altered changes in code repositories can bypass typical automated scanning software checks.</p>
                        <a href="#" class="article-link">Read more →</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="article-card">
                        <img src="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=600&q=80" alt="Phishing" class="article-image">
                        <span class="article-tag">Incident Response</span>
                        <h5 class="article-title">Modern Phishing Attack Methods</h5>
                        <p class="article-desc">Highly customized, contextual email alerts that match day-to-day organizational tasks closely are becoming standard strategy.</p>
                        <a href="#" class="article-link">Read more →</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="article-card">
                        <img src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=600&q=80" alt="Zero Trust" class="article-image">
                        <span class="article-tag">Zero Trust</span>
                        <h5 class="article-title">Implementing Zero Trust Architecture</h5>
                        <p class="article-desc">Never trust, always verify. Learn how to implement zero trust principles across your entire infrastructure.</p>
                        <a href="#" class="article-link">Read more →</a>
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
                    <p class="small text-muted mb-0">&copy; 2026 AegisMind Security Services.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <p class="small text-muted mb-1">Tech Enclave, Quetta, PK</p>
                    <p class="small text-muted mb-2">NoshinFitras@aegismind.io</p>
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