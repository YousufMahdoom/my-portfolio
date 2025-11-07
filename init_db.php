<?php
/**
 * Database Initialization Script
 * Run this file once to create/update database tables
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Initialization</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: #10b981; padding: 10px; background: #d1fae5; border-radius: 5px; margin: 10px 0; }
        .error { color: #ef4444; padding: 10px; background: #fee2e2; border-radius: 5px; margin: 10px 0; }
        .info { color: #3b82f6; padding: 10px; background: #dbeafe; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Database Initialization</h1>";

try {
    // Initialize database
    initializeDatabase();
    echo "<div class='success'>✓ Database and tables created/updated successfully!</div>";
    
    // Check if project_images table exists
    $conn = getDBConnection();
    $result = $conn->query("SHOW TABLES LIKE 'project_images'");
    if ($result->num_rows > 0) {
        echo "<div class='success'>✓ project_images table is ready for multiple image uploads</div>";
    }
    
    // Display table information
    echo "<div class='info'><strong>Available Tables:</strong><ul>";
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul></div>";
    
    $conn->close();
    
    echo "<div class='info'>
        <strong>Next Steps:</strong>
        <ol>
            <li>Go to the admin panel: <a href='admin/index.php'>admin/index.php</a></li>
            <li>Login with your credentials</li>
            <li>Navigate to 'Manage Projects'</li>
            <li>Edit a project and upload multiple images</li>
            <li>View your portfolio homepage to see the rotating images (changes every 5 seconds)</li>
        </ol>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}

echo "
    </div>
</body>
</html>";
?>
