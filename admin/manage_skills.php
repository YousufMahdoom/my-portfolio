<?php
session_start();
require_once '../config/database.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();
$message = '';
$messageType = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Skill deleted successfully!';
        $messageType = 'success';
    } else {
        $message = 'Error deleting skill.';
        $messageType = 'error';
    }
    $stmt->close();
}

// Handle Add/Edit
if (isset($_POST['save_skill'])) {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $category = $_POST['category'];
    $proficiency = intval($_POST['proficiency']);
    $display_order = intval($_POST['display_order']);
    
    if ($id) {
        // Update existing skill
        $stmt = $conn->prepare("UPDATE skills SET name=?, category=?, proficiency=?, display_order=? WHERE id=?");
        $stmt->bind_param("ssiii", $name, $category, $proficiency, $display_order, $id);
    } else {
        // Insert new skill
        $stmt = $conn->prepare("INSERT INTO skills (name, category, proficiency, display_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $name, $category, $proficiency, $display_order);
    }
    
    if ($stmt->execute()) {
        $message = $id ? 'Skill updated successfully!' : 'Skill added successfully!';
        $messageType = 'success';
    } else {
        $message = 'Error saving skill.';
        $messageType = 'error';
    }
    $stmt->close();
}

// Get all skills
$skills = [];
$result = $conn->query("SELECT * FROM skills ORDER BY display_order ASC, category ASC, name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $skills[] = $row;
    }
}

// Group skills by category
$skillsByCategory = [];
foreach ($skills as $skill) {
    $cat = $skill['category'] ?: 'Other';
    if (!isset($skillsByCategory[$cat])) {
        $skillsByCategory[$cat] = [];
    }
    $skillsByCategory[$cat][] = $skill;
}

// Get skill for editing
$editSkill = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editSkill = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills - Admin</title>
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
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .back-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert.success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        .section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 24px;
            margin-bottom: 25px;
            color: #1a202c;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
            font-family: inherit;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        .proficiency-input {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .proficiency-input input[type="range"] {
            flex: 1;
        }
        .proficiency-value {
            min-width: 50px;
            text-align: center;
            font-weight: 600;
            color: #667eea;
            font-size: 18px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }
        .btn-secondary:hover {
            background: #cbd5e0;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
            padding: 6px 12px;
            font-size: 13px;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .btn-edit {
            background: #3b82f6;
            color: white;
            padding: 6px 12px;
            font-size: 13px;
        }
        .btn-edit:hover {
            background: #2563eb;
        }
        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .skill-card {
            background: #f7fafc;
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s;
        }
        .skill-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        .skill-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        .skill-name {
            font-size: 18px;
            font-weight: 600;
            color: #1a202c;
        }
        .skill-category {
            background: #e0e7ff;
            color: #4338ca;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .skill-proficiency {
            margin-bottom: 15px;
        }
        .proficiency-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
            color: #718096;
        }
        .proficiency-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        .proficiency-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 4px;
            transition: width 0.3s;
        }
        .skill-actions {
            display: flex;
            gap: 8px;
        }
        .category-section {
            margin-bottom: 30px;
        }
        .category-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
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
        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .order-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1><i class="fas fa-code"></i> Manage Skills</h1>
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert <?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-<?php echo $editSkill ? 'edit' : 'plus-circle'; ?>"></i>
                <?php echo $editSkill ? 'Edit Skill' : 'Add New Skill'; ?>
            </h2>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $editSkill['id'] ?? ''; ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Skill Name *</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($editSkill['name'] ?? ''); ?>" required placeholder="e.g., JavaScript, React, Python">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category">
                            <option value="">Select Category</option>
                            <option value="Frontend" <?php echo (isset($editSkill['category']) && $editSkill['category'] === 'Frontend') ? 'selected' : ''; ?>>Frontend</option>
                            <option value="Backend" <?php echo (isset($editSkill['category']) && $editSkill['category'] === 'Backend') ? 'selected' : ''; ?>>Backend</option>
                            <option value="Database" <?php echo (isset($editSkill['category']) && $editSkill['category'] === 'Database') ? 'selected' : ''; ?>>Database</option>
                            <option value="DevOps" <?php echo (isset($editSkill['category']) && $editSkill['category'] === 'DevOps') ? 'selected' : ''; ?>>DevOps</option>
                            <option value="Tools" <?php echo (isset($editSkill['category']) && $editSkill['category'] === 'Tools') ? 'selected' : ''; ?>>Tools</option>
                            <option value="Languages" <?php echo (isset($editSkill['category']) && $editSkill['category'] === 'Languages') ? 'selected' : ''; ?>>Languages</option>
                            <option value="Other" <?php echo (isset($editSkill['category']) && $editSkill['category'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Proficiency Level (%)</label>
                        <div class="proficiency-input">
                            <input type="range" name="proficiency" id="proficiency" min="0" max="100" value="<?php echo $editSkill['proficiency'] ?? 80; ?>" oninput="document.getElementById('proficiency-display').textContent = this.value + '%'">
                            <span class="proficiency-value" id="proficiency-display"><?php echo ($editSkill['proficiency'] ?? 80); ?>%</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" value="<?php echo $editSkill['display_order'] ?? 0; ?>" min="0">
                    </div>
                </div>
                <div class="form-buttons">
                    <button type="submit" name="save_skill" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $editSkill ? 'Update Skill' : 'Add Skill'; ?>
                    </button>
                    <?php if ($editSkill): ?>
                        <a href="manage_skills.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Skills List -->
        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-list"></i> All Skills (<?php echo count($skills); ?>)
            </h2>
            <?php if (empty($skills)): ?>
                <div class="empty-state">
                    <i class="fas fa-code"></i>
                    <h3>No skills yet</h3>
                    <p>Add your first skill using the form above.</p>
                </div>
            <?php else: ?>
                <?php foreach ($skillsByCategory as $category => $categorySkills): ?>
                    <div class="category-section">
                        <h3 class="category-title">
                            <i class="fas fa-folder"></i> <?php echo htmlspecialchars($category); ?> 
                            <span style="color: #718096; font-size: 14px; font-weight: normal;">(<?php echo count($categorySkills); ?>)</span>
                        </h3>
                        <div class="skills-grid">
                            <?php foreach ($categorySkills as $skill): ?>
                                <div class="skill-card">
                                    <div class="skill-header">
                                        <div>
                                            <div class="skill-name">
                                                <?php echo htmlspecialchars($skill['name']); ?>
                                                <?php if ($skill['display_order'] > 0): ?>
                                                    <span class="order-badge">#<?php echo $skill['display_order']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="skill-proficiency">
                                        <div class="proficiency-label">
                                            <span>Proficiency</span>
                                            <span style="font-weight: 600; color: #667eea;"><?php echo $skill['proficiency']; ?>%</span>
                                        </div>
                                        <div class="proficiency-bar">
                                            <div class="proficiency-fill" style="width: <?php echo $skill['proficiency']; ?>%;"></div>
                                        </div>
                                    </div>
                                    <div class="skill-actions">
                                        <a href="?edit=<?php echo $skill['id']; ?>" class="btn btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $skill['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this skill?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
