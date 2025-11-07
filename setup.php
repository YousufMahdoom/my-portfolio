<?php
require_once 'config/database.php';

// Initialize database
initializeDatabase();

$conn = getDBConnection();

// Insert sample projects if table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM projects");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $sampleProjects = [
        [
            'title' => 'E-Commerce Platform',
            'short_description' => 'A full-featured online shopping platform with cart and payment integration',
            'description' => 'Built a complete e-commerce solution with user authentication, product management, shopping cart, and secure payment processing. Features include order tracking, inventory management, and admin dashboard.',
            'technologies' => 'PHP, MySQL, JavaScript, Bootstrap',
            'image_url' => 'assets/images/project1.jpg',
            'demo_link' => '#',
            'github_link' => 'https://github.com/yourusername/ecommerce',
            'display_order' => 1
        ],
        [
            'title' => 'Task Management System',
            'short_description' => 'Collaborative task tracking application for teams',
            'description' => 'Developed a project management tool that allows teams to create, assign, and track tasks. Includes real-time updates, file attachments, and progress reporting.',
            'technologies' => 'PHP, MySQL, jQuery, CSS3',
            'image_url' => 'assets/images/project2.jpg',
            'demo_link' => '#',
            'github_link' => 'https://github.com/yourusername/taskmanager',
            'display_order' => 2
        ],
        [
            'title' => 'Blog Platform',
            'short_description' => 'Content management system for bloggers',
            'description' => 'Created a blogging platform with rich text editor, category management, comments system, and SEO optimization. Includes user roles and permissions.',
            'technologies' => 'PHP, MySQL, HTML5, CSS3',
            'image_url' => 'assets/images/project3.jpg',
            'demo_link' => '#',
            'github_link' => 'https://github.com/yourusername/blog',
            'display_order' => 3
        ]
    ];
    
    $stmt = $conn->prepare("INSERT INTO projects (title, short_description, description, technologies, image_url, demo_link, github_link, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($sampleProjects as $project) {
        $stmt->bind_param("sssssssi", 
            $project['title'], 
            $project['short_description'], 
            $project['description'], 
            $project['technologies'], 
            $project['image_url'], 
            $project['demo_link'], 
            $project['github_link'], 
            $project['display_order']
        );
        $stmt->execute();
    }
    $stmt->close();
}

// Insert sample skills if table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM skills");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $sampleSkills = [
        ['name' => 'PHP', 'category' => 'Backend', 'proficiency' => 90, 'display_order' => 1],
        ['name' => 'MySQL', 'category' => 'Database', 'proficiency' => 85, 'display_order' => 2],
        ['name' => 'JavaScript', 'category' => 'Frontend', 'proficiency' => 80, 'display_order' => 3],
        ['name' => 'HTML5', 'category' => 'Frontend', 'proficiency' => 95, 'display_order' => 4],
        ['name' => 'CSS3', 'category' => 'Frontend', 'proficiency' => 90, 'display_order' => 5],
        ['name' => 'Bootstrap', 'category' => 'Framework', 'proficiency' => 85, 'display_order' => 6],
        ['name' => 'Git', 'category' => 'Tools', 'proficiency' => 80, 'display_order' => 7],
        ['name' => 'REST API', 'category' => 'Backend', 'proficiency' => 85, 'display_order' => 8]
    ];
    
    $stmt = $conn->prepare("INSERT INTO skills (name, category, proficiency, display_order) VALUES (?, ?, ?, ?)");
    
    foreach ($sampleSkills as $skill) {
        $stmt->bind_param("ssii", $skill['name'], $skill['category'], $skill['proficiency'], $skill['display_order']);
        $stmt->execute();
    }
    $stmt->close();
}

$conn->close();

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Setup Complete</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); text-align: center; }
        h1 { color: #667eea; margin-bottom: 20px; }
        p { color: #666; margin-bottom: 30px; }
        a { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; transition: background 0.3s; }
        a:hover { background: #764ba2; }
        .success { color: #10b981; font-size: 48px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='success'>✓</div>
        <h1>Setup Complete!</h1>
        <p>Database and tables have been created successfully.<br>Sample projects and skills have been added.</p>
        <a href='index.php'>Go to Portfolio</a>
    </div>
</body>
</html>";
?>
