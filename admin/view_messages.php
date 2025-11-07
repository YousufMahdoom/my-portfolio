<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

require_once '../config/database.php';
$conn = getDBConnection();

// Mark message as read
if (isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    $conn->query("UPDATE contact_messages SET is_read = 1 WHERE id = $id");
    header('Location: view_messages.php');
    exit;
}

// Delete message
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM contact_messages WHERE id = $id");
    header('Location: view_messages.php');
    exit;
}

// Get all messages
$messages = [];
$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
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
    <title>View Messages - Admin</title>
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
        .back-link {
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            transition: all 0.3s;
        }
        .back-link:hover {
            background: rgba(255,255,255,0.3);
        }
        .message-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .message-card.unread {
            border-left-color: #f5576c;
            background: #fffbf0;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .message-info h3 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #1a202c;
        }
        .message-email {
            color: #667eea;
            font-size: 14px;
        }
        .message-date {
            color: #718096;
            font-size: 14px;
        }
        .message-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .message-text {
            color: #4a5568;
            line-height: 1.8;
            padding: 15px;
            background: #f7fafc;
            border-radius: 8px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        .badge-new {
            background: #f5576c;
            color: white;
        }
        .badge-read {
            background: #e2e8f0;
            color: #718096;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1><i class="fas fa-envelope"></i> Contact Messages</h1>
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="container">
        <?php if (empty($messages)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h2>No Messages</h2>
                <p>You haven't received any messages yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message-card <?php echo !$msg['is_read'] ? 'unread' : ''; ?>">
                    <div class="message-header">
                        <div class="message-info">
                            <h3>
                                <?php echo htmlspecialchars($msg['name']); ?>
                                <span class="badge <?php echo !$msg['is_read'] ? 'badge-new' : 'badge-read'; ?>">
                                    <?php echo !$msg['is_read'] ? 'New' : 'Read'; ?>
                                </span>
                            </h3>
                            <div class="message-email">
                                <i class="fas fa-envelope"></i> 
                                <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>">
                                    <?php echo htmlspecialchars($msg['email']); ?>
                                </a>
                            </div>
                            <div class="message-date">
                                <i class="fas fa-clock"></i> 
                                <?php echo date('F d, Y \a\t g:i A', strtotime($msg['created_at'])); ?>
                            </div>
                        </div>
                        <div class="message-actions">
                            <?php if (!$msg['is_read']): ?>
                                <a href="?mark_read=<?php echo $msg['id']; ?>" class="btn btn-success" 
                                   onclick="return confirm('Mark this message as read?')">
                                    <i class="fas fa-check"></i> Mark Read
                                </a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $msg['id']; ?>" class="btn btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this message?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                    <div class="message-text">
                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
