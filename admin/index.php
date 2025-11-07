<?php
session_start();
require_once '../config/database.php';

// Simple authentication (change these credentials!)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // Change this!

// Handle login
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Check if logged in
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

// If not logged in, show login form
if (!$isLoggedIn) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        h1 { 
            text-align: center;
            margin-bottom: 30px;
            color: #667eea;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        <div class="back-link">
            <a href="../index.php">← Back to Portfolio</a>
        </div>
    </div>
</body>
</html>
<?php
    exit;
}

// Admin is logged in - show dashboard
$conn = getDBConnection();

// Get statistics
$projectCount = $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'];
$messageCount = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")->fetch_assoc()['count'];
$skillCount = $conn->query("SELECT COUNT(*) as count FROM skills")->fetch_assoc()['count'];

// Get recent messages
$messages = [];
$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7fafc;
        }
        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .stat-icon.projects { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-icon.messages { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-icon.skills { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .stat-info h3 {
            font-size: 32px;
            margin-bottom: 5px;
        }
        .stat-info p {
            color: #718096;
        }
        .section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #1a202c;
        }
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .quick-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 10px;
            text-decoration: none;
            color: #1a202c;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .quick-link:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        .quick-link i {
            font-size: 24px;
            color: #667eea;
        }
        .message-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .message-item {
            padding: 15px;
            background: #f7fafc;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .message-name {
            font-weight: 600;
            color: #1a202c;
        }
        .message-date {
            color: #718096;
            font-size: 14px;
        }
        .message-email {
            color: #667eea;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .message-text {
            color: #4a5568;
            line-height: 1.6;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1><i class="fas fa-chart-line"></i> Admin Dashboard</h1>
            <div>
                <a href="../index.php" class="logout-btn" style="margin-right: 10px;">
                    <i class="fas fa-home"></i> View Site
                </a>
                <a href="?logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon projects">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $projectCount; ?></h3>
                    <p>Total Projects</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon messages">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $messageCount; ?></h3>
                    <p>Unread Messages</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon skills">
                    <i class="fas fa-code"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $skillCount; ?></h3>
                    <p>Skills Listed</p>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="section">
            <h2 class="section-title">Quick Actions</h2>
            <div class="quick-links">
                <a href="manage_projects.php" class="quick-link">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Projects</span>
                </a>
                <a href="manage_skills.php" class="quick-link">
                    <i class="fas fa-code"></i>
                    <span>Manage Skills</span>
                </a>
                <a href="view_messages.php" class="quick-link">
                    <i class="fas fa-envelope"></i>
                    <span>View Messages</span>
                </a>
                <a href="https://localhost/phpmyadmin" target="_blank" class="quick-link">
                    <i class="fas fa-database"></i>
                    <span>phpMyAdmin</span>
                </a>
            </div>
        </div>

        <!-- Recent Messages -->
        <div class="section">
            <h2 class="section-title">Recent Messages</h2>
            <?php if (empty($messages)): ?>
                <p style="color: #718096;">No messages yet.</p>
            <?php else: ?>
                <div class="message-list">
                    <?php foreach ($messages as $msg): ?>
                        <div class="message-item">
                            <div class="message-header">
                                <span class="message-name">
                                    <?php echo htmlspecialchars($msg['name']); ?>
                                    <?php if (!$msg['is_read']): ?>
                                        <span style="background: #f5576c; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 8px;">New</span>
                                    <?php endif; ?>
                                </span>
                                <span class="message-date"><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></span>
                            </div>
                            <div class="message-email"><?php echo htmlspecialchars($msg['email']); ?></div>
                            <div class="message-text"><?php echo htmlspecialchars(substr($msg['message'], 0, 150)); ?><?php echo strlen($msg['message']) > 150 ? '...' : ''; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top: 20px;">
                    <a href="view_messages.php" class="btn btn-primary">
                        <i class="fas fa-envelope-open"></i> View All Messages
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
