<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';   // $pdo comes from here

// ============================================
// FORM HANDLING
// ============================================
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_course'])) {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $serial = trim($_POST['serial'] ?? '');
    $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
    $course_id = $_POST['course_id'] ?? '';

    if ($name && $category && $serial && $price !== false) {
        try {
            if ($course_id) {
                $stmt = $pdo->prepare("UPDATE courses SET name = ?, category = ?, serial = ?, price = ? WHERE id = ?");
                $stmt->execute([$name, $category, $serial, $price, $course_id]);
                $message = "✅ Course updated successfully!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO courses (name, category, serial, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $category, $serial, $price]);
                $message = "✅ Course saved successfully! ID: " . $pdo->lastInsertId();
            }
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = "❌ Database Error: " . $e->getMessage();
            $messageType = 'danger';
        }
    } else {
        $message = "⚠️ All fields are required and price must be a valid number!";
        $messageType = 'danger';
    }
}

// ============================================
// DELETE RECORD
// ============================================
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        $message = "Delete error: " . $e->getMessage();
        $messageType = 'danger';
    }
}

// ============================================
// FETCH RECORDS
// ============================================
try {
    $courses = $pdo->query("SELECT * FROM courses ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    $courses = [];
    $message = "Error fetching data: " . $e->getMessage();
    $messageType = 'danger';
}

// ============================================
// EDIT RECORD
// ============================================
$editData = ['id' => '', 'name' => '', 'category' => '', 'serial' => '', 'price' => ''];
if (isset($_GET['edit_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$_GET['edit_id']]);
        $editData = $stmt->fetch() ?: $editData;
    } catch (PDOException $e) {
        // ignore
    }
}

// Determine active page for navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AegisMind Security // Intelligent Cyber Defense</title>
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

        .hero-section {
            position: relative;
            z-index: 2;
            background: radial-gradient(circle at top right, rgba(56, 189, 248, 0.15), transparent), linear-gradient(180deg, #111827 0%, #0f172a 100%);
            border: 1px solid #1e293b;
            border-radius: 24px;
            padding: 80px 60px;
            margin-top: 30px;
            margin-bottom: 60px;
        }
        .hero-title {
            font-weight: 700;
            font-size: 3.2rem;
            letter-spacing: -1px;
            line-height: 1.1;
            color: #ffffff !important;
        }
        .hero-title .highlight { color: #38bdf8; }
        .hero-sub {
            font-size: 1.1rem;
            color: #94a3b8 !important;
            max-width: 540px;
        }

        .btn-cta {
            background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 14px 32px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(56, 189, 248, 0.3);
        }
        .btn-cta:hover { transform: translateY(-2px); color: #ffffff; }

        .section-tag {
            color: #38bdf8;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            display: block;
            margin-bottom: 10px;
        }

        .feature-card {
            background: #111827;
            border: 1px solid #1e293b;
            border-radius: 16px;
            padding: 35px;
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }
        .feature-card:hover {
            border-color: #38bdf8;
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(56, 189, 248, 0.05);
        }
        .feature-icon { font-size: 2rem; color: #38bdf8; margin-bottom: 20px; display: inline-block; }
        .feature-title {
            color: #ffffff !important;
            font-weight: 700;
        }
        .feature-desc {
            color: #cbd5e1 !important;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #ffffff !important;
            line-height: 1;
        }
        .stat-label {
            font-size: 0.75rem;
            color: #38bdf8 !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }

        .interactive-section {
            position: relative;
            z-index: 2;
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 20px;
            padding: 40px;
            margin-top: 40px;
        }

        .hover-paragraph {
            background: #111827;
            border: 1px solid #1e293b;
            padding: 20px 25px;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
            color: #cbd5e1 !important;
            height: 100%;
        }
        .hover-paragraph strong {
            color: #ffffff !important;
            transition: color 0.3s ease;
        }
        .hover-paragraph:hover {
            background: #1a2744 !important;
            border-color: #38bdf8 !important;
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(56, 189, 248, 0.08);
        }
        .hover-paragraph:hover strong {
            color: #38bdf8 !important;
        }
        .hover-paragraph:hover p {
            color: #ffffff !important;
        }
        .hover-paragraph p {
            color: #cbd5e1 !important;
            margin: 0;
        }

        .cyber-btn {
            background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 14px 32px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(56, 189, 248, 0.3);
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }
        .cyber-btn:hover {
            transform: translateY(-2px);
            color: #ffffff;
            box-shadow: 0 6px 30px rgba(56, 189, 248, 0.4);
        }

        .who-text {
            color: #cbd5e1 !important;
        }
        .who-text strong {
            color: #ffffff !important;
        }
        .quote-text {
            color: #e2e8f0 !important;
            font-style: italic;
        }

        .main-card {
            position: relative;
            z-index: 2;
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 24px;
            padding: 2rem;
            backdrop-filter: blur(2px);
        }
        .form-control, .form-select {
            background: #111827;
            border: 1px solid #1e293b;
            color: #ffffff !important;
        }
        .form-control::placeholder {
            color: #64748b !important;
        }
        .form-control:focus, .form-select:focus {
            background: #111827;
            border-color: #38bdf8;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.2rem rgba(56, 189, 248, 0.2);
        }
        .form-label {
            color: #cbd5e1 !important;
            font-weight: 500;
        }
        .btn-save {
            background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.25s;
            color: #ffffff !important;
        }
        .btn-save:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(56, 189, 248, 0.25); 
            color: #ffffff !important; 
        }
        .table-dark-custom {
            background: #111827;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #1e293b;
        }
        .table-dark-custom th {
            background: #1a2744;
            color: #38bdf8 !important;
            padding: 14px 12px;
            border-bottom: 1px solid #1e293b;
        }
        .table-dark-custom td {
            background: #111827;
            color: #cbd5e1 !important;
            padding: 14px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #1e293b;
        }
        .table-dark-custom td strong {
            color: #ffffff !important;
        }
        .table-dark-custom td code {
            color: #38bdf8 !important;
            background: #1a2744;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .badge-category {
            background: #1a2744;
            color: #38bdf8 !important;
            padding: 4px 14px;
            border-radius: 30px;
            border: 1px solid #1e293b;
            font-size: 0.75rem;
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

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.2rem; }
            .stat-number { font-size: 1.8rem; }
            .navbar-brand { font-size: 1.2rem; }
            .hero-section { padding: 40px 25px; }
            .interactive-section { padding: 20px; }
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
                        <a class="nav-link <?php echo ($current_page == 'courses.php') ? 'active' : ''; ?>" href="courses.php">Explore Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'product_register.php') ? 'active' : ''; ?>" href="product_register.php">Register Course</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ====== HERO SECTION ====== -->
    <div class="container">
        <div class="hero-section text-center text-md-start d-md-flex align-items-center justify-content-between">
            <div class="mb-4 mb-md-0" style="z-index: 2;">
                <span class="badge bg-dark border border-secondary text-info px-3 py-2 mb-3 rounded-pill">v2.4 Live Monitoring</span>
                <h1 class="hero-title">Defending Digital <br><span class="highlight">Assets Globally</span></h1>
                <p class="hero-sub">Automated cloud audit systems, real-time code penetration architecture, and network border control management.</p>
            </div>
            <div style="z-index: 2;">
                <a href="contact.php" class="btn btn-cta shadow">Initialize System Scan</a>
            </div>
        </div>

        <!-- ===== FEATURES SECTION ===== -->
        <section class="mb-5 pb-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="feature-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="feature-icon">⚡</div>
                            <h3 class="h4 fw-bold feature-title">Threat Detection</h3>
                            <p class="feature-desc">Real-time behavioral analysis and AI-powered anomaly detection across your entire attack surface. Our platform correlates billions of signals to surface genuine threats before they escalate.</p>
                        </div>
                        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                            <div class="stat-number">99.98%</div>
                            <div class="stat-label">Detection Accuracy</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="feature-icon">🚨</div>
                            <h3 class="h4 fw-bold feature-title">Incident Response</h3>
                            <p class="feature-desc">Automated containment protocols and expert-led response teams available around the clock. When seconds matter, our orchestrated playbooks activate instantly to limit blast radius and restore operations.</p>
                        </div>
                        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                            <div class="stat-number">2.4s</div>
                            <div class="stat-label">Avg. Response</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== 4 FEATURE CARDS ===== -->
        <section class="mb-5 pb-5">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">🔒</div>
                        <h4 class="h5 fw-bold feature-title">Zero Trust</h4>
                        <p class="feature-desc">Never trust, always verify. Enforce least-privilege access across every user, device, and workload.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <h4 class="h5 fw-bold feature-title">Compliance</h4>
                        <p class="feature-desc">Continuous compliance monitoring for ISO 27001, SOC 2, GDPR, HIPAA, and PCI-DSS.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">🎯</div>
                        <h4 class="h5 fw-bold feature-title">Penetration Testing</h4>
                        <p class="feature-desc">Adversarial simulations and red team exercises that expose exploitable weaknesses.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">🛡️</div>
                        <h4 class="h5 fw-bold feature-title">Security Operations</h4>
                        <p class="feature-desc">A fully managed SOC that monitors and responds to threats 24/7.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- ===== NEW: EXPLORE COURSES CTA (ADDED) ===== -->
        <!-- ============================================================ -->
        <section class="mb-5" style="position: relative; z-index: 2;">
            <div class="bg-dark border border-secondary border-opacity-25 rounded-4 p-5 text-center">
                <span class="section-tag"><i class="fas fa-graduation-cap me-2"></i>Start Learning</span>
                <h2 class="text-white fw-bold mb-3">Explore Our <span style="color:#38bdf8;">Cybersecurity Courses</span></h2>
                <p class="text-muted mb-4" style="color: #94a3b8 !important; max-width: 600px; margin: 0 auto;">
                    Master ethical hacking, cloud security, incident response & more. Join thousands of students building their cybersecurity careers.
                </p>
                <a href="courses.php" class="btn btn-cta shadow">
                    <i class="fas fa-rocket me-2"></i>Explore All Courses
                </a>
            </div>
        </section>

        <!-- ===== INTERACTIVE SECTION ===== -->
        <section class="interactive-section">
            <div class="mb-4">
                <span class="section-tag">AegisMind Cyber Range</span>
                <h3 class="h3 fw-bold text-white">Cyber Security <span style="color: #38bdf8;">Interactive Training Environment</span></h3>
                <p class="text-muted" style="color: #94a3b8 !important;">Threat Response Training Ground.</p>
            </div>

            <div class="row mb-4 pb-3 border-bottom border-secondary border-opacity-25">
                <div class="col-12 mb-3">
                    <h5 class="fw-bold text-white"><i class="fas fa-mouse-pointer me-2" style="color: #38bdf8;"></i>Hover-Activated Nodes</h5>
                    <p class="small" style="color: #94a3b8 !important;">Real-time threat data visualization with interactive hover effects.</p>
                </div>
                
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="hover-paragraph">
                        <p class="small"><strong>🛡️ Firewall Status:</strong> All perimeter firewalls are operational. 2.4M requests blocked in the last 24 hours.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="hover-paragraph">
                        <p class="small"><strong>🔐 Encryption Layer:</strong> AES-256 encryption active across all data channels. Key rotation scheduled in 6 hours.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="hover-paragraph">
                        <p class="small"><strong>📡 Threat Intel:</strong> 127 new threat signatures added. 3 active campaigns detected in the wild.</p>
                    </div>
                </div>
            </div>

            <div class="row mb-4 pb-3 border-bottom border-secondary border-opacity-25">
                <div class="col-12 mb-3">
                    <h5 class="fw-bold text-white"><i class="fas fa-shield-alt me-2" style="color: #38bdf8;"></i>Security Monitoring Nodes</h5>
                    <p class="small" style="color: #94a3b8 !important;">Next-level security surveillance.</p>
                </div>
                
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="hover-paragraph">
                        <p class="small"><strong>👤 User Activity:</strong> 1,247 active sessions monitored. 3 anomalous login attempts detected and blocked.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="hover-paragraph">
                        <p class="small"><strong>📦 Package Scan:</strong> All incoming packages scanned. 0 vulnerabilities found in latest deployment.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="hover-paragraph">
                        <p class="small"><strong>🌐 Network Traffic:</strong> 3.2TB data analyzed. 99.6% clean traffic. 0.4% suspicious patterns flagged.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== WHO WE ARE SECTION ===== -->
        <section class="mb-5 pb-5 px-3 mt-5" style="position: relative; z-index: 2;">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="section-tag">Who We Are</span>
                    <h2 class="display-6 fw-bold text-white mb-4">Engineered by Experts, <br>Driven by Cyber Intelligence</h2>
                    <blockquote class="border-start border-info border-3 ps-4 my-4 quote-text">
                        “True security isn't just about patching walls—it's about writing clean, resilient code from scratch while thinking exactly like the adversary trying to breach it.”
                    </blockquote>
                    <p class="who-text small mb-4">We bridge the gap between elegant product development and hardcore core system defenses. As expert Full-Stack Web Developers combined with specialized Certified Ethical Hacker (CEH) credentials, we construct robust web systems built to withstand adversarial vectors from the ground up.</p>
                    <a href="contact.php" class="btn btn-outline-info px-4 py-2 fw-semibold">Connect Securely</a>
                </div>
                <div class="col-lg-6">
                    <div class="row g-4 text-center">
                        <div class="col-6">
                            <div class="p-4 bg-dark border border-secondary border-opacity-25 rounded-3">
                                <div class="display-6 fw-bold text-info">CEH</div>
                                <div class="small text-uppercase tracking-wider text-muted mt-1" style="font-size:0.75rem; color: #94a3b8 !important;">Certified Ethical Hacking</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-4 bg-dark border border-secondary border-opacity-25 rounded-3">
                                <div class="display-6 fw-bold text-white">Full-Stack</div>
                                <div class="small text-uppercase tracking-wider text-muted mt-1" style="font-size:0.75rem; color: #94a3b8 !important;">Web Development Nodes</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-4 bg-dark border border-secondary border-opacity-25 rounded-3">
                                <div class="display-6 fw-bold text-white">100%</div>
                                <div class="small text-uppercase tracking-wider text-muted mt-1" style="font-size:0.75rem; color: #94a3b8 !important;">Secure Code Audits</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-4 bg-dark border border-secondary border-opacity-25 rounded-3">
                                <div class="display-6 fw-bold text-info">24/7</div>
                                <div class="small text-uppercase tracking-wider text-muted mt-1" style="font-size:0.75rem; color: #94a3b8 !important;">Threat Control Monitoring</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>



            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="bg-dark p-4 rounded-4 border border-secondary">
                        <h5 class="text-white mb-3">
                            <i class="fas fa-plus-circle me-2" style="color:#38bdf8;"></i>
                            <?php echo $editData['id'] ? 'Edit Course' : 'Add New Course'; ?>
                        </h5>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($editData['id']); ?>">
                            
                            <div class="mb-3">
                                <label class="form-label small">Course Name</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($editData['name']); ?>" placeholder="Enter course name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small">Category</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Ethical Hacking" <?php echo ($editData['category'] == 'Ethical Hacking') ? 'selected' : ''; ?>>Ethical Hacking</option>
                                    <option value="Cloud Security" <?php echo ($editData['category'] == 'Cloud Security') ? 'selected' : ''; ?>>Cloud Security</option>
                                    <option value="Incident Response" <?php echo ($editData['category'] == 'Incident Response') ? 'selected' : ''; ?>>Incident Response</option>
                                    <option value="Compliance & Governance" <?php echo ($editData['category'] == 'Compliance & Governance') ? 'selected' : ''; ?>>Compliance & Governance</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Course Code</label>
                                <input type="text" class="form-control" name="serial" value="<?php echo htmlspecialchars($editData['serial']); ?>" placeholder="e.g., CS-101" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Price ($)</label>
                                <input type="number" step="0.01" class="form-control" name="price" value="<?php echo htmlspecialchars($editData['price']); ?>" placeholder="0.00" required>
                            </div>

                            <button type="submit" name="save_course" class="btn btn-save w-100">
                                <?php echo $editData['id'] ? 'Update Course' : 'Save Course'; ?>
                            </button>
                            
                            <?php if ($editData['id']): ?>
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary w-100 mt-2">Cancel</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table table-dark-custom">
                            <thead>
                                <tr>
                                    <th>Course Name</th>
                                    <th>Category</th>
                                    <th>Code</th>
                                    <th>Price</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($courses)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4" style="color: #94a3b8 !important;">
                                            <i class="fas fa-inbox me-2"></i>No records found
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($courses as $course): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($course['name']); ?></strong></td>
                                            <td><span class="badge-category"><?php echo htmlspecialchars($course['category']); ?></span></td>
                                            <td><code><?php echo htmlspecialchars($course['serial']); ?></code></td>
                                            <td class="fw-bold">$<?php echo number_format($course['price'], 2); ?></td>
                                            <td class="text-end">
                                                <a href="?edit_id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-info me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?delete_id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this course?');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
                        <li><a href="courses.php">Explore Courses</a></li>
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

        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 40) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
        });

        const backBtn = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 600) backBtn.classList.add('visible');
            else backBtn.classList.remove('visible');
        });
        backBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        const revealElements = document.querySelectorAll('.reveal');
        function checkReveal() {
            const trigger = window.innerHeight * 0.88;
            revealElements.forEach(el => {
                const rect = el.getBoundingClientRect();
                if (rect.top < trigger && !el.classList.contains('visible')) {
                    el.classList.add('visible');
                }
            });
        }
        window.addEventListener('scroll', checkReveal);
        window.addEventListener('resize', checkReveal);
        setTimeout(checkReveal, 200);

    })();
    </script>
</body>
</html>