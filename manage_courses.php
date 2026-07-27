<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';

$message = '';
$messageType = '';

// ============================================
// UPDATE COURSE
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_course'])) {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $serial = trim($_POST['serial'] ?? '');
    $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
    $course_id = $_POST['course_id'] ?? '';

    if ($name && $category && $serial && $price !== false && $course_id) {
        try {
            $stmt = $pdo->prepare("UPDATE courses SET name = ?, category = ?, serial = ?, price = ? WHERE id = ?");
            $stmt->execute([$name, $category, $serial, $price, $course_id]);
            $message = "✅ Course updated successfully!";
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = "❌ Database Error: " . $e->getMessage();
            $messageType = 'danger';
        }
    } else {
        $message = "⚠️ All fields are required!";
        $messageType = 'danger';
    }
}

// ============================================
// DELETE RECORD
// ============================================
if (isset($_GET['delete_id'])) {
    try {
        // Get course name first
        $stmt = $pdo->prepare("SELECT name FROM courses WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        $course = $stmt->fetch();
        $courseName = $course ? $course['name'] : 'Course';
        
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        
        // Redirect with success
        header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1&name=" . urlencode($courseName));
        exit;
    } catch (PDOException $e) {
        $message = "Delete error: " . $e->getMessage();
        $messageType = 'danger';
    }
}

// ============================================
// CHECK DELETE SUCCESS
// ============================================
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $courseName = isset($_GET['name']) ? urldecode($_GET['name']) : 'Course';
    $message = "🗑️ Course <strong>" . htmlspecialchars($courseName) . "</strong> has been deleted successfully!";
    $messageType = 'success';
}

// ============================================
// FETCH RECORDS
// ============================================
$courses = [];
try {
    if ($pdo) {
        $courses = $pdo->query("SELECT * FROM courses ORDER BY id DESC")->fetchAll();
    }
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

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses // AegisMind Security</title>
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
            color: #38bdf8 !important;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }
        .page-title { color: #ffffff !important; font-weight: 700; }
        .page-subtitle { color: #94a3b8 !important; }
        
        .form-control, .form-select {
            background: #111827;
            border: 1px solid #1e293b;
            color: #ffffff !important;
        }
        .form-control:focus, .form-select:focus {
            background: #111827;
            border-color: #38bdf8;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.2rem rgba(56, 189, 248, 0.2);
        }
        .form-label { color: #cbd5e1 !important; font-weight: 500; }
        .btn-update {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.25s;
            color: #ffffff !important;
        }
        .btn-update:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3); 
            color: #ffffff !important; 
        }
        .btn-secondary {
            background: #1e293b !important;
            border-color: #1e293b !important;
            color: #cbd5e1 !important;
        }
        .btn-secondary:hover {
            background: #334155 !important;
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
        .table-dark-custom td strong { color: #ffffff !important; }
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
        .alert-popup.show { display: block; }
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
        .alert-popup .alert-box .alert-content { flex: 1; }
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
        .alert-popup .alert-box .alert-close-btn:hover { opacity: 1; }
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

        /* ===== BEAUTIFUL DELETE MODAL ===== */
        .delete-modal-overlay {
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
        .delete-modal-overlay.active { display: flex; }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .delete-modal {
            background: #1a2744;
            border: 1px solid #1e293b;
            border-radius: 24px;
            padding: 2.5rem;
            max-width: 440px;
            width: 90%;
            text-align: center;
            animation: scaleIn 0.3s ease;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        .delete-modal .modal-icon {
            width: 80px;
            height: 80px;
            background: rgba(239, 68, 68, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: #ef4444;
            border: 2px solid rgba(239, 68, 68, 0.3);
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .delete-modal .modal-title {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }
        .delete-modal .modal-subtitle {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .delete-modal .modal-course-name {
            color: #38bdf8;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 8px 16px;
            background: rgba(56, 189, 248, 0.1);
            border-radius: 8px;
            display: inline-block;
            margin: 0.5rem 0 1.5rem;
            border: 1px solid rgba(56, 189, 248, 0.2);
        }
        .delete-modal .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 1.5rem;
        }
        .delete-modal .modal-actions .btn-cancel {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
            padding: 10px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .delete-modal .modal-actions .btn-cancel:hover {
            background: #334155;
            color: #ffffff;
            transform: translateY(-2px);
        }
        .delete-modal .modal-actions .btn-delete {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: #ffffff;
            border: none;
            padding: 10px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }
        .delete-modal .modal-actions .btn-delete:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 25px rgba(220, 38, 38, 0.4);
        }
        .delete-modal .modal-warning {
            color: #64748b;
            font-size: 0.8rem;
            margin-top: 1rem;
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
            .delete-modal { padding: 1.5rem; }
            .delete-modal .modal-actions { flex-direction: column; }
            .delete-modal .modal-actions .btn-cancel,
            .delete-modal .modal-actions .btn-delete { width: 100%; }
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
                <div class="alert-message" id="alertMessage">Course updated successfully!</div>
            </div>
            <button class="alert-close-btn" onclick="closeAlert()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- ====== DELETE CONFIRMATION MODAL ====== -->
    <div class="delete-modal-overlay" id="deleteModal">
        <div class="delete-modal">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="modal-title">⚠️ Confirm Deletion</h3>
            <p class="modal-subtitle">Are you sure you want to delete this course?</p>
            <div class="modal-course-name" id="deleteCourseName">Course Name</div>
            <p class="modal-warning"><i class="fas fa-info-circle me-1"></i> This action cannot be undone.</p>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeDeleteModal()">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <a href="#" class="btn-delete" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-2"></i>Delete Permanently
                </a>
            </div>
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
                        <a class="nav-link <?php echo ($current_page == 'manage_courses.php') ? 'active' : ''; ?>" href="manage_courses.php">Manage Courses</a>
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
                    <span class="section-title"><i class="fas fa-shield-alt me-2"></i>AegisMind Security</span>
                    <h2 class="page-title mt-2">Manage <span style="color:#38bdf8;">Courses</span></h2>
                    <p class="page-subtitle">Edit or delete registered courses from the database</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Edit Form Column -->
                <?php if ($editData['id']): ?>
                <div class="col-lg-4">
                    <div class="bg-dark p-4 rounded-4 border border-secondary">
                        <h5 class="text-white mb-3">
                            <i class="fas fa-edit me-2" style="color:#f59e0b;"></i>
                            Edit Course
                        </h5>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($editData['id']); ?>">
                            
                            <div class="mb-3">
                                <label class="form-label small">Course Name</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($editData['name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small">Category</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Ethical Hacking" <?php echo ($editData['category'] == 'Ethical Hacking') ? 'selected' : ''; ?>>Ethical Hacking</option>
                                    <option value="Cloud Security" <?php echo ($editData['category'] == 'Cloud Security') ? 'selected' : ''; ?>>Cloud Security</option>
                                    <option value="Incident Response" <?php echo ($editData['category'] == 'Incident Response') ? 'selected' : ''; ?>>Incident Response</option>
                                    <option value="Compliance & Governance" <?php echo ($editData['category'] == 'Compliance & Governance') ? 'selected' : ''; ?>>Compliance & Governance</option>
                                    <option value="Network Security" <?php echo ($editData['category'] == 'Network Security') ? 'selected' : ''; ?>>Network Security</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Course Code</label>
                                <input type="text" class="form-control" name="serial" value="<?php echo htmlspecialchars($editData['serial']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Price ($)</label>
                                <input type="number" step="0.01" class="form-control" name="price" value="<?php echo htmlspecialchars($editData['price']); ?>" required>
                            </div>

                            <button type="submit" name="update_course" class="btn btn-update w-100">
                                <i class="fas fa-save me-2"></i>Update Course
                            </button>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary w-100 mt-2">Cancel</a>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Table Column -->
                <div class="col-lg-<?php echo $editData['id'] ? '8' : '12'; ?>">
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
                                        <td colspan="5" class="text-center py-4" style="color: #94a3b8 !important;">
                                            <i class="fas fa-inbox me-2"></i>No courses registered yet.
                                            <br><a href="courses.php" class="text-info">Register a course</a>
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
                                                <a href="?edit_id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-info me-1" title="Edit Course">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="openDeleteModal('<?php echo htmlspecialchars($course['name']); ?>', <?php echo $course['id']; ?>)" 
                                                        title="Delete Course">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
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

        <?php if (isset($message) && $message): ?>
            showAlert('<?php echo $messageType; ?>', '<?php echo $message; ?>');
        <?php endif; ?>

    })();

    // ====== DELETE MODAL FUNCTIONS ======
    function openDeleteModal(courseName, courseId) {
        document.getElementById('deleteCourseName').textContent = courseName;
        document.getElementById('confirmDeleteBtn').href = '?delete_id=' + courseId;
        document.getElementById('deleteModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });

    // ====== BEAUTIFUL ALERT POPUP ======
    function showAlert(type, message) {
        const popup = document.getElementById('alertPopup');
        const box = document.getElementById('alertBox');
        const icon = document.getElementById('alertIcon');
        const title = document.getElementById('alertTitle');
        const msg = document.getElementById('alertMessage');
        
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