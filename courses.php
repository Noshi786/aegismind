<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';

// ============================================
// HARDCODED COURSES DATA (24 Courses)
// ============================================
$courses = [
    // Ethical Hacking Courses (5)
    [
        'id' => 1,
        'name' => 'Master Ethical Hacking',
        'category' => 'Ethical Hacking',
        'serial' => 'EH-101',
        'price' => 199.99,
        'level' => 'Beginner',
        'duration' => '6 weeks',
        'description' => 'Learn the fundamentals of ethical hacking, penetration testing, and vulnerability assessment.',
        'image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 2,
        'name' => 'Advanced Penetration Testing',
        'category' => 'Ethical Hacking',
        'serial' => 'EH-202',
        'price' => 299.99,
        'level' => 'Advanced',
        'duration' => '8 weeks',
        'description' => 'Master advanced penetration testing techniques, privilege escalation, and post-exploitation strategies.',
        'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 3,
        'name' => 'Certified Ethical Hacker (CEH)',
        'category' => 'Ethical Hacking',
        'serial' => 'EH-303',
        'price' => 399.99,
        'level' => 'Expert',
        'duration' => '10 weeks',
        'description' => 'Complete CEH certification preparation with hands-on labs, real-world scenarios, and exam practice.',
        'image' => 'https://images.unsplash.com/photo-1510511459019-5dda7724fd87?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 4,
        'name' => 'Web Application Security',
        'category' => 'Ethical Hacking',
        'serial' => 'EH-104',
        'price' => 249.99,
        'level' => 'Intermediate',
        'duration' => '6 weeks',
        'description' => 'Learn to identify and exploit web application vulnerabilities including OWASP Top 10.',
        'image' => 'https://images.unsplash.com/photo-1601597111158-2fceff292cdc?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 5,
        'name' => 'Mobile App Security Testing',
        'category' => 'Ethical Hacking',
        'serial' => 'EH-205',
        'price' => 279.99,
        'level' => 'Advanced',
        'duration' => '7 weeks',
        'description' => 'Master mobile application security testing for iOS and Android platforms.',
        'image' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=600&q=80'
    ],
    // Cloud Security Courses (5)
    [
        'id' => 6,
        'name' => 'Cloud Security Architecture',
        'category' => 'Cloud Security',
        'serial' => 'CS-101',
        'price' => 249.99,
        'level' => 'Beginner',
        'duration' => '6 weeks',
        'description' => 'Learn cloud security fundamentals, shared responsibility model, and best practices.',
        'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 7,
        'name' => 'AWS Security Mastery',
        'category' => 'Cloud Security',
        'serial' => 'CS-202',
        'price' => 279.99,
        'level' => 'Intermediate',
        'duration' => '8 weeks',
        'description' => 'Master AWS security services, IAM policies, encryption, and compliance.',
        'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 8,
        'name' => 'Azure Security Expert',
        'category' => 'Cloud Security',
        'serial' => 'CS-303',
        'price' => 299.99,
        'level' => 'Advanced',
        'duration' => '9 weeks',
        'description' => 'Become an expert in Microsoft Azure security and advanced threat protection.',
        'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 9,
        'name' => 'Google Cloud Security',
        'category' => 'Cloud Security',
        'serial' => 'CS-104',
        'price' => 259.99,
        'level' => 'Intermediate',
        'duration' => '6 weeks',
        'description' => 'Learn Google Cloud security best practices and data protection.',
        'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 10,
        'name' => 'DevSecOps on Cloud',
        'category' => 'Cloud Security',
        'serial' => 'CS-205',
        'price' => 289.99,
        'level' => 'Advanced',
        'duration' => '7 weeks',
        'description' => 'Integrate security into CI/CD pipelines and automate cloud security operations.',
        'image' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?auto=format&fit=crop&w=600&q=80'
    ],
    // Incident Response Courses (5)
    [
        'id' => 11,
        'name' => 'Incident Response Professional',
        'category' => 'Incident Response',
        'serial' => 'IR-101',
        'price' => 229.99,
        'level' => 'Beginner',
        'duration' => '5 weeks',
        'description' => 'Learn incident response fundamentals and first responder procedures.',
        'image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 12,
        'name' => 'Digital Forensics Expert',
        'category' => 'Incident Response',
        'serial' => 'IR-202',
        'price' => 259.99,
        'level' => 'Intermediate',
        'duration' => '7 weeks',
        'description' => 'Master digital forensics techniques and evidence collection.',
        'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 13,
        'name' => 'Malware Analysis',
        'category' => 'Incident Response',
        'serial' => 'IR-303',
        'price' => 319.99,
        'level' => 'Advanced',
        'duration' => '8 weeks',
        'description' => 'Learn advanced malware analysis and reverse engineering.',
        'image' => 'https://images.unsplash.com/photo-1510511459019-5dda7724fd87?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 14,
        'name' => 'Threat Hunting Mastery',
        'category' => 'Incident Response',
        'serial' => 'IR-104',
        'price' => 279.99,
        'level' => 'Advanced',
        'duration' => '6 weeks',
        'description' => 'Master proactive threat hunting techniques and detection strategies.',
        'image' => 'https://images.unsplash.com/photo-1601597111158-2fceff292cdc?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 15,
        'name' => 'SOC Operations',
        'category' => 'Incident Response',
        'serial' => 'IR-205',
        'price' => 299.99,
        'level' => 'Intermediate',
        'duration' => '8 weeks',
        'description' => 'Learn Security Operations Center processes and monitoring.',
        'image' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=600&q=80'
    ],
    // Compliance & Governance Courses (5)
    [
        'id' => 16,
        'name' => 'GDPR Compliance Masterclass',
        'category' => 'Compliance & Governance',
        'serial' => 'CG-101',
        'price' => 199.99,
        'level' => 'Beginner',
        'duration' => '4 weeks',
        'description' => 'Master GDPR requirements and compliance strategies.',
        'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 17,
        'name' => 'Cybersecurity Governance',
        'category' => 'Compliance & Governance',
        'serial' => 'CG-202',
        'price' => 239.99,
        'level' => 'Intermediate',
        'duration' => '6 weeks',
        'description' => 'Learn cybersecurity governance frameworks and risk management.',
        'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 18,
        'name' => 'ISO 27001 Lead Implementer',
        'category' => 'Compliance & Governance',
        'serial' => 'CG-303',
        'price' => 349.99,
        'level' => 'Expert',
        'duration' => '10 weeks',
        'description' => 'Become certified ISO 27001 Lead Implementer.',
        'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 19,
        'name' => 'HIPAA Compliance Expert',
        'category' => 'Compliance & Governance',
        'serial' => 'CG-104',
        'price' => 269.99,
        'level' => 'Intermediate',
        'duration' => '6 weeks',
        'description' => 'Master HIPAA compliance and healthcare data protection.',
        'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 20,
        'name' => 'PCI-DSS Professional',
        'category' => 'Compliance & Governance',
        'serial' => 'CG-205',
        'price' => 289.99,
        'level' => 'Advanced',
        'duration' => '7 weeks',
        'description' => 'Learn PCI-DSS requirements and payment security best practices.',
        'image' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?auto=format&fit=crop&w=600&q=80'
    ],
    // Network Security Courses (4)
    [
        'id' => 21,
        'name' => 'Zero Trust Architecture',
        'category' => 'Network Security',
        'serial' => 'NS-101',
        'price' => 219.99,
        'level' => 'Intermediate',
        'duration' => '6 weeks',
        'description' => 'Master Zero Trust security model and micro-segmentation.',
        'image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 22,
        'name' => 'Network Security Fundamentals',
        'category' => 'Network Security',
        'serial' => 'NS-102',
        'price' => 189.99,
        'level' => 'Beginner',
        'duration' => '4 weeks',
        'description' => 'Learn network security basics and secure network design.',
        'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 23,
        'name' => 'Firewall & VPN Expert',
        'category' => 'Network Security',
        'serial' => 'NS-203',
        'price' => 259.99,
        'level' => 'Intermediate',
        'duration' => '7 weeks',
        'description' => 'Master firewall configuration and VPN technologies.',
        'image' => 'https://images.unsplash.com/photo-1510511459019-5dda7724fd87?auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 24,
        'name' => 'Secure Network Design',
        'category' => 'Network Security',
        'serial' => 'NS-104',
        'price' => 279.99,
        'level' => 'Advanced',
        'duration' => '8 weeks',
        'description' => 'Learn to design secure enterprise networks with defense-in-depth.',
        'image' => 'https://images.unsplash.com/photo-1601597111158-2fceff292cdc?auto=format&fit=crop&w=600&q=80'
    ]
];

// ============================================
// REGISTER COURSE FROM MODAL
// ============================================
$register_message = '';
$register_messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_course'])) {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $serial = trim($_POST['serial'] ?? '');
    $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);

    if ($name && $category && $serial && $price !== false) {
        try {
            // Check if already exists
            $check = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE serial = ?");
            $check->execute([$serial]);
            $exists = $check->fetchColumn();
            
            if ($exists > 0) {
                $register_message = "⚠️ Course with serial <strong>$serial</strong> already exists!";
                $register_messageType = 'warning';
            } else {
                $stmt = $pdo->prepare("INSERT INTO courses (name, category, serial, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $category, $serial, $price]);
                $register_message = "✅ Course <strong>" . htmlspecialchars($name) . "</strong> registered successfully! ID: " . $pdo->lastInsertId();
                $register_messageType = 'success';
            }
        } catch (PDOException $e) {
            $register_message = "❌ Database Error: " . $e->getMessage();
            $register_messageType = 'danger';
        }
    } else {
        $register_message = "⚠️ All fields are required!";
        $register_messageType = 'danger';
    }
}

// ============================================
// CART FUNCTIONALITY
// ============================================
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['add_to_cart']) && isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    if (!in_array($course_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $course_id;
        $message = "✅ Course added to cart successfully!";
        $messageType = 'success';
    } else {
        $message = "⚠️ Course is already in your cart!";
        $messageType = 'warning';
    }
}

if (isset($_GET['remove_from_cart']) && isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    if (($key = array_search($course_id, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
        $message = "🗑️ Course removed from cart!";
        $messageType = 'success';
    }
}

if (isset($_GET['clear_cart'])) {
    $_SESSION['cart'] = [];
    $message = "🗑️ Cart cleared successfully!";
    $messageType = 'success';
}

$cartItems = [];
$total = 0;
foreach ($_SESSION['cart'] as $cartId) {
    foreach ($courses as $course) {
        if ($course['id'] == $cartId) {
            $cartItems[] = $course;
            $total += $course['price'];
            break;
        }
    }
}

$cartCount = count($_SESSION['cart']);
$current_page = basename($_SERVER['PHP_SELF']);

// Check registered courses
$registered_serials = [];
try {
    $stmt = $pdo->query("SELECT serial FROM courses");
    $registered_serials = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    // ignore
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Courses // AegisMind Security</title>
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

        .nav-link-wrapper {
            position: relative;
            display: inline-block;
        }
        .cart-badge {
            background: #ef4444;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 0.7rem;
            position: absolute;
            top: -8px;
            right: -15px;
            min-width: 20px;
            text-align: center;
        }

        .section-tag {
            color: #38bdf8;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            display: block;
            margin-bottom: 10px;
        }

        /* ===== COURSE CARDS ===== */
        .course-card {
            background: #111827;
            border: 1px solid #1e293b;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            z-index: 2;
        }
        .course-card:hover {
            transform: translateY(-8px);
            border-color: #38bdf8;
            box-shadow: 0 12px 40px rgba(56, 189, 248, 0.1);
        }
        .course-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #1e293b;
        }
        .course-body {
            padding: 1.5rem;
        }
        .course-title {
            color: #ffffff !important;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
            min-height: 50px;
        }
        .course-category {
            color: #38bdf8 !important;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .course-desc {
            color: #94a3b8 !important;
            font-size: 0.85rem;
            line-height: 1.6;
            margin: 10px 0;
            min-height: 60px;
        }
        .course-meta {
            display: flex;
            gap: 15px;
            margin: 10px 0;
            font-size: 0.75rem;
            color: #64748b;
        }
        .course-meta i {
            color: #38bdf8;
            margin-right: 4px;
        }
        .course-price {
            color: #ffffff !important;
            font-size: 1.3rem;
            font-weight: 700;
        }
        .course-price span {
            color: #38bdf8;
        }
        .course-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            margin: 8px 0;
        }
        .course-rating .stars {
            color: #f59e0b;
            font-size: 0.9rem;
        }
        .course-rating .reviews {
            color: #94a3b8;
            font-size: 0.75rem;
        }
        
        .course-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 12px;
        }
        .btn-add-cart {
            background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-align: center;
            display: block;
            width: 100%;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-add-cart:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 20px rgba(56, 189, 248, 0.3);
            color: #ffffff !important;
        }
        .btn-add-cart.in-cart {
            background: #1e293b;
            color: #38bdf8 !important;
        }
        .btn-add-cart.in-cart:hover {
            background: #334155;
        }
        
        .btn-register-modal {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-align: center;
            display: block;
            width: 100%;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-register-modal:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
            color: #ffffff !important;
        }
        .btn-register-modal.registered {
            background: #1e293b;
            color: #34d399 !important;
            cursor: default;
        }
        .btn-register-modal.registered:hover {
            transform: none;
            box-shadow: none;
        }
        
        .course-level {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .level-beginner { background: #064e3b; color: #34d399; }
        .level-intermediate { background: #78350f; color: #fbbf24; }
        .level-advanced { background: #7f1d1d; color: #f87171; }
        .level-expert { background: #1e3a5f; color: #60a5fa; }

        /* ===== REGISTER MODAL ===== */
        .register-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }
        .register-modal-overlay.active {
            display: flex;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .register-modal {
            background: #1a2744;
            border: 1px solid #1e293b;
            border-radius: 24px;
            padding: 2.5rem;
            max-width: 500px;
            width: 90%;
            animation: scaleIn 0.3s ease;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            max-height: 90vh;
            overflow-y: auto;
        }
        .register-modal .modal-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #1e293b;
            padding-bottom: 1rem;
        }
        .register-modal .modal-header-custom h4 {
            color: #ffffff;
            font-weight: 700;
        }
        .register-modal .modal-header-custom h4 i {
            color: #10b981;
            margin-right: 10px;
        }
        .register-modal .modal-close {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s;
        }
        .register-modal .modal-close:hover {
            color: #ffffff;
        }
        .register-modal .form-control {
            background: #111827;
            border: 1px solid #1e293b;
            color: #ffffff !important;
        }
        .register-modal .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.2);
        }
        .register-modal .form-label {
            color: #cbd5e1 !important;
            font-weight: 500;
        }
        .register-modal .btn-register-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 10px;
            color: #ffffff !important;
            width: 100%;
            transition: all 0.3s ease;
        }
        .register-modal .btn-register-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }
        .register-modal .btn-cancel-modal {
            background: #1e293b;
            border: 1px solid #334155;
            padding: 12px;
            font-weight: 600;
            border-radius: 10px;
            color: #cbd5e1 !important;
            width: 100%;
            transition: all 0.3s ease;
        }
        .register-modal .btn-cancel-modal:hover {
            background: #334155;
            color: #ffffff !important;
        }
        .register-modal .course-preview {
            background: #111827;
            border: 1px solid #1e293b;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .register-modal .course-preview .preview-label {
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .register-modal .course-preview .preview-value {
            color: #ffffff;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* ===== CART DROPDOWN ===== */
        .cart-dropdown {
            position: fixed;
            top: 80px;
            right: 20px;
            background: #1a2744;
            border: 1px solid #1e293b;
            border-radius: 16px;
            padding: 1.5rem;
            width: 380px;
            max-height: 450px;
            overflow-y: auto;
            z-index: 9999;
            display: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        .cart-dropdown.active {
            display: block;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .cart-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #1e293b;
        }
        .cart-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        .cart-item .item-name {
            flex: 1;
            color: #ffffff !important;
            font-size: 0.85rem;
        }
        .cart-item .item-price {
            color: #38bdf8 !important;
            font-weight: 600;
        }
        .cart-item .item-remove {
            color: #ef4444;
            cursor: pointer;
            transition: color 0.2s;
            padding: 5px;
        }
        .cart-item .item-remove:hover {
            color: #dc2626;
        }
        .cart-total {
            padding: 15px 0;
            border-top: 2px solid #1e293b;
            display: flex;
            justify-content: space-between;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .cart-empty {
            color: #94a3b8 !important;
            text-align: center;
            padding: 20px 0;
        }

        /* ===== BEAUTIFUL ALERT POPUP ===== */
        .alert-popup {
            position: fixed;
            top: 100px;
            right: 30px;
            z-index: 10000;
            max-width: 450px;
            width: 100%;
            animation: slideInRight 0.5s ease;
            display: none;
        }
        .alert-popup.show {
            display: block;
        }
        @keyframes slideInRight {
            from { transform: translateX(100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .alert-popup .alert-box {
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 5px solid;
        }
        .alert-popup .alert-box .alert-icon {
            font-size: 2rem;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .alert-popup .alert-box .alert-content {
            flex: 1;
        }
        .alert-popup .alert-box .alert-content strong {
            display: block;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 2px;
        }
        .alert-popup .alert-box .alert-content .alert-message {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .alert-popup .alert-box .alert-close-btn {
            background: none;
            border: none;
            color: inherit;
            opacity: 0.6;
            cursor: pointer;
            font-size: 1.2rem;
            transition: opacity 0.2s;
            padding: 0 5px;
        }
        .alert-popup .alert-box .alert-close-btn:hover {
            opacity: 1;
        }
        .alert-popup .alert-success {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
            color: #a7f3d0;
            border-color: #34d399;
        }
        .alert-popup .alert-success .alert-icon {
            background: rgba(52, 211, 153, 0.2);
            color: #34d399;
        }
        .alert-popup .alert-danger {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
            color: #fca5a5;
            border-color: #f87171;
        }
        .alert-popup .alert-danger .alert-icon {
            background: rgba(248, 113, 113, 0.2);
            color: #f87171;
        }
        .alert-popup .alert-warning {
            background: linear-gradient(135deg, #78350f 0%, #92400e 100%);
            color: #fcd34d;
            border-color: #fbbf24;
        }
        .alert-popup .alert-warning .alert-icon {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
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
            .cart-dropdown { width: 300px; right: 10px; }
            .course-image { height: 180px; }
            .register-modal { padding: 1.5rem; }
            .alert-popup { right: 10px; max-width: 95%; top: 80px; }
        }
        @media (prefers-reduced-motion: reduce) {
            * { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>

    <!-- ====== NETWORK CANVAS ====== -->
    <canvas id="network-canvas"></canvas>

    <!-- ====== ALERT POPUP ====== -->
    <div class="alert-popup" id="alertPopup">
        <div class="alert-box" id="alertBox">
            <div class="alert-icon" id="alertIcon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="alert-content">
                <strong id="alertTitle">Success!</strong>
                <div class="alert-message" id="alertMessage">Course registered successfully!</div>
            </div>
            <button class="alert-close-btn" onclick="closeAlert()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- ====== REGISTER MODAL ====== -->
    <div class="register-modal-overlay" id="registerModal">
        <div class="register-modal">
            <div class="modal-header-custom">
                <h4><i class="fas fa-plus-circle"></i> Register Course</h4>
                <button class="modal-close" onclick="closeRegisterModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="course-preview" id="coursePreview">
                <div class="preview-label">Course to Register</div>
                <div class="preview-value" id="previewCourseName">Course Name</div>
                <div style="display:flex; gap:20px; margin-top:8px;">
                    <span style="color:#94a3b8; font-size:0.8rem;"><i class="fas fa-tag"></i> <span id="previewCategory">Category</span></span>
                    <span style="color:#94a3b8; font-size:0.8rem;"><i class="fas fa-code"></i> <span id="previewSerial">Serial</span></span>
                    <span style="color:#38bdf8; font-size:0.8rem;"><i class="fas fa-dollar-sign"></i> $<span id="previewPrice">0.00</span></span>
                </div>
            </div>

            <form method="POST" action="" id="registerForm">
                <input type="hidden" name="register_course" value="1">
                <input type="hidden" name="course_id" id="hiddenCourseId" value="">
                
                <div class="mb-3">
                    <label class="form-label small">Course Name</label>
                    <input type="text" class="form-control" name="name" id="modalCourseName" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small">Category</label>
                    <select class="form-select" name="category" id="modalCategory" required>
                        <option value="">Select Category</option>
                        <option value="Ethical Hacking">Ethical Hacking</option>
                        <option value="Cloud Security">Cloud Security</option>
                        <option value="Incident Response">Incident Response</option>
                        <option value="Compliance & Governance">Compliance & Governance</option>
                        <option value="Network Security">Network Security</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small">Course Code</label>
                    <input type="text" class="form-control" name="serial" id="modalSerial" placeholder="e.g., CS-101" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small">Price ($)</label>
                    <input type="number" step="0.01" class="form-control" name="price" id="modalPrice" placeholder="0.00" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn-cancel-modal" onclick="closeRegisterModal()">Cancel</button>
                    <button type="submit" class="btn-register-submit">
                        <i class="fas fa-save me-2"></i>Register Course
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ====== NAVIGATION ====== -->
    <nav class="navbar navbar-expand-lg fixed-top py-2" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="index.php">Aegis<span>Mind</span></a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navCollapse">
                <ul class="navbar-nav ms-auto align-items-center">
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
                        <a class="nav-link <?php echo ($current_page == 'manage_courses.php') ? 'active' : ''; ?>" href="manage_courses.php">Manage Courses</a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link-wrapper">
                            <a class="nav-link" href="#" onclick="toggleCart(event)">
                                <i class="fas fa-shopping-cart"></i>
                                <?php if ($cartCount > 0): ?>
                                    <span class="cart-badge"><?php echo $cartCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ====== CART DROPDOWN ====== -->
    <div class="cart-dropdown" id="cartDropdown">
        <h5 class="text-white mb-3"><i class="fas fa-shopping-cart me-2" style="color:#38bdf8;"></i>Your Cart</h5>
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="cart-empty"><i class="fas fa-shopping-bag me-2"></i>Your cart is empty</div>
        <?php else: ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                    <div class="item-price">$<?php echo number_format($item['price'], 2); ?></div>
                    <a href="?remove_from_cart=1&course_id=<?php echo $item['id']; ?>" class="item-remove"><i class="fas fa-times"></i></a>
                </div>
            <?php endforeach; ?>
            <div class="cart-total"><span>Total:</span><span>$<?php echo number_format($total, 2); ?></span></div>
            <div class="d-grid gap-2">
                <a href="?clear_cart=1" class="btn btn-outline-danger btn-sm">Clear Cart</a>
                <button class="btn btn-save w-100" onclick="alert('Checkout coming soon!')">
                    <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- ====== MAIN CONTENT ====== -->
    <div class="container mt-4">
        <div class="mb-4">
            <span class="section-tag"><i class="fas fa-graduation-cap me-2"></i>Our Courses</span>
            <h1 class="text-white fw-bold">Explore <span style="color:#38bdf8;">Cybersecurity Courses</span></h1>
            <p class="text-muted" style="color: #94a3b8 !important;">Master the skills to protect the digital world. Choose from <?php echo count($courses); ?>+ expert-led courses.</p>
        </div>

        <!-- ====== COURSES GRID ====== -->
        <div class="row g-4">
            <?php foreach ($courses as $course): 
                $inCart = in_array($course['id'], $_SESSION['cart']);
                $isRegistered = in_array($course['serial'], $registered_serials);
                $rating = (4 + (mt_rand(0, 10) / 10));
                $reviews = rand(12, 350);
                $levelClass = strtolower($course['level']);
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="course-card">
                        <img src="<?php echo $course['image']; ?>" alt="<?php echo htmlspecialchars($course['name']); ?>" class="course-image">
                        <div class="course-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="course-category"><i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($course['category']); ?></span>
                                <span class="course-price">$<span><?php echo number_format($course['price'], 2); ?></span></span>
                            </div>
                            <h5 class="course-title"><?php echo htmlspecialchars($course['name']); ?></h5>
                            <p class="course-desc"><?php echo htmlspecialchars($course['description']); ?></p>
                            <div class="course-meta">
                                <span><i class="fas fa-signal"></i> <?php echo htmlspecialchars($course['level']); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($course['duration']); ?></span>
                                <span><i class="fas fa-code"></i> <?php echo htmlspecialchars($course['serial']); ?></span>
                            </div>
                            <div class="course-rating">
                                <span class="stars">
                                    <?php 
                                    $fullStars = floor($rating);
                                    for ($s = 1; $s <= 5; $s++): 
                                        if ($s <= $fullStars): echo '<i class="fas fa-star"></i>';
                                        elseif ($s - $fullStars <= 0.5): echo '<i class="fas fa-star-half-alt"></i>';
                                        else: echo '<i class="far fa-star"></i>';
                                        endif;
                                    endfor; 
                                    ?>
                                </span>
                                <span class="reviews">(<?php echo $reviews; ?> reviews)</span>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <span class="course-level level-<?php echo $levelClass; ?>"><?php echo htmlspecialchars($course['level']); ?></span>
                                <?php if ($isRegistered): ?>
                                    <span class="course-level" style="background:#064e3b;color:#34d399;">
                                        <i class="fas fa-check-circle me-1"></i>Registered
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="course-actions">
                                <a href="?add_to_cart=1&course_id=<?php echo $course['id']; ?>" 
                                   class="btn-add-cart <?php echo $inCart ? 'in-cart' : ''; ?>">
                                    <i class="fas <?php echo $inCart ? 'fa-check-circle' : 'fa-cart-plus'; ?> me-2"></i>
                                    <?php echo $inCart ? 'Added to Cart' : 'Add to Cart'; ?>
                                </a>
                                <?php if ($isRegistered): ?>
                                    <span class="btn-register-modal registered">
                                        <i class="fas fa-check-circle me-2"></i>Already Registered
                                    </span>
                                <?php else: ?>
                                    <button class="btn-register-modal" onclick="openRegisterModal(
                                        <?php echo $course['id']; ?>,
                                        '<?php echo addslashes($course['name']); ?>',
                                        '<?php echo addslashes($course['category']); ?>',
                                        '<?php echo addslashes($course['serial']); ?>',
                                        '<?php echo $course['price']; ?>'
                                    )">
                                        <i class="fas fa-pencil-alt me-2"></i>Register Course
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ====== STATS ====== -->
        <section class="mt-5">
            <div class="row g-4 text-center">
                <div class="col-md-3 col-6">
                    <div class="bg-dark border border-secondary border-opacity-25 rounded-3 p-4" style="z-index:2;position:relative;">
                        <div class="display-5 fw-bold text-info"><?php echo count($courses); ?>+</div>
                        <div class="small text-uppercase text-muted" style="color: #94a3b8 !important;">Courses</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-dark border border-secondary border-opacity-25 rounded-3 p-4" style="z-index:2;position:relative;">
                        <div class="display-5 fw-bold text-white">10K+</div>
                        <div class="small text-uppercase text-muted" style="color: #94a3b8 !important;">Students</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-dark border border-secondary border-opacity-25 rounded-3 p-4" style="z-index:2;position:relative;">
                        <div class="display-5 fw-bold text-info">4.9★</div>
                        <div class="small text-uppercase text-muted" style="color: #94a3b8 !important;">Avg Rating</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-dark border border-secondary border-opacity-25 rounded-3 p-4" style="z-index:2;position:relative;">
                        <div class="display-5 fw-bold text-white">100%</div>
                        <div class="small text-uppercase text-muted" style="color: #94a3b8 !important;">Satisfaction</div>
                    </div>
                </div>
            </div>
        </section>
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
                        <li><a href="manage_courses.php">Manage Courses</a></li>
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
    <div id="backToTop"><i class="fas fa-chevron-up"></i></div>

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

        // Check for registration message from PHP
        <?php if (isset($register_message) && $register_message): ?>
            showAlert('<?php echo $register_messageType; ?>', '<?php echo $register_message; ?>');
        <?php endif; ?>

        <?php if (isset($message) && $message): ?>
            showAlert('<?php echo $messageType; ?>', '<?php echo $message; ?>');
        <?php endif; ?>

    })();

    // ====== REGISTER MODAL FUNCTIONS ======
    function openRegisterModal(id, name, category, serial, price) {
        document.getElementById('hiddenCourseId').value = id;
        document.getElementById('modalCourseName').value = name;
        document.getElementById('modalCategory').value = category;
        document.getElementById('modalSerial').value = serial;
        document.getElementById('modalPrice').value = price;
        
        document.getElementById('previewCourseName').textContent = name;
        document.getElementById('previewCategory').textContent = category;
        document.getElementById('previewSerial').textContent = serial;
        document.getElementById('previewPrice').textContent = price;
        
        document.getElementById('registerModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeRegisterModal() {
        document.getElementById('registerModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close modal on background click
    document.getElementById('registerModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRegisterModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRegisterModal();
        }
    });

    // ====== CART TOGGLE ======
    function toggleCart(e) {
        e.preventDefault();
        document.getElementById('cartDropdown').classList.toggle('active');
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('cartDropdown');
        const cartLink = document.querySelector('.nav-link-wrapper');
        if (dropdown && !dropdown.contains(e.target) && cartLink && !cartLink.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });

    // ====== BEAUTIFUL ALERT POPUP ======
    function showAlert(type, message) {
        const popup = document.getElementById('alertPopup');
        const box = document.getElementById('alertBox');
        const icon = document.getElementById('alertIcon');
        const title = document.getElementById('alertTitle');
        const msg = document.getElementById('alertMessage');
        
        // Reset classes
        box.className = 'alert-box';
        
        if (type === 'success') {
            box.classList.add('alert-success');
            icon.innerHTML = '<i class="fas fa-check-circle"></i>';
            title.textContent = '✅ Success!';
        } else if (type === 'danger') {
            box.classList.add('alert-danger');
            icon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
            title.textContent = '❌ Error!';
        } else if (type === 'warning') {
            box.classList.add('alert-warning');
            icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
            title.textContent = '⚠️ Warning!';
        }
        
        msg.innerHTML = message;
        popup.classList.add('show');
        
        // Auto hide after 5 seconds
        setTimeout(function() {
            closeAlert();
        }, 5000);
    }

    function closeAlert() {
        document.getElementById('alertPopup').classList.remove('show');
    }
    </script>
</body>
</html>