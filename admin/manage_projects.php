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
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Project deleted successfully!';
        $messageType = 'success';
    } else {
        $message = 'Error deleting project.';
        $messageType = 'error';
    }
    $stmt->close();
}

// Handle Add/Edit
if (isset($_POST['save_project'])) {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $description = $_POST['description'];
    $short_description = $_POST['short_description'];
    $technologies = $_POST['technologies'];
    //$image_url = $_POST['image_url'];
    $demo_link = $_POST['demo_link'];
    $github_link = $_POST['github_link'];
    $display_order = intval($_POST['display_order']);
    
    if ($id) {
        // Update existing project
        $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, short_description=?, technologies=?, image_url=?, demo_link=?, github_link=?, display_order=? WHERE id=?");
        $stmt->bind_param("sssssssii", $title, $description, $short_description, $technologies, $image_url, $demo_link, $github_link, $display_order, $id);
    } else {
        // Insert new project
        $stmt = $conn->prepare("INSERT INTO projects (title, description, short_description, technologies, image_url, demo_link, github_link, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", $title, $description, $short_description, $technologies, $image_url, $demo_link, $github_link, $display_order);
    }
    
    if ($stmt->execute()) {
        $message = $id ? 'Project updated successfully!' : 'Project added successfully!';
        $messageType = 'success';
    } else {
        $message = 'Error saving project.';
        $messageType = 'error';
    }
    $stmt->close();
}

// Handle multiple image uploads
if(isset($_FILES['project_images']) && !empty($_FILES['project_images']['name'][0])) {
    $uploadDir = '../uploads/projects/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $project_id = $_POST['id'] ?? $conn->insert_id;
    
    foreach($_FILES['project_images']['tmp_name'] as $key => $tmp_name) {
        if($_FILES['project_images']['error'][$key] === 0) {
            $fileExtension = strtolower(pathinfo($_FILES['project_images']['name'][$key], PATHINFO_EXTENSION));
            
            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid('project_') . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $image_url = 'uploads/projects/' . $fileName;
                    
                    // Insert into project_images table
                    $stmt = $conn->prepare("INSERT INTO project_images (project_id, image_url, display_order) VALUES (?, ?, ?)");
                    $display_order = $key;
                    $stmt->bind_param("isi", $project_id, $image_url, $display_order);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    $image_id = intval($_GET['delete_image']);
    $project_id = intval($_GET['project_id']);
    
    // Get image path before deleting
    $stmt = $conn->prepare("SELECT image_url FROM project_images WHERE id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();
    $stmt->close();
    
    if ($image) {
        // Delete file from server
        $file_path = '../' . $image['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM project_images WHERE id = ?");
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        $stmt->close();
        
        header("Location: manage_projects.php?edit=$project_id&msg=image_deleted");
        exit;
    }
}

// Get all projects
$projects = [];
$result = $conn->query("SELECT * FROM projects ORDER BY display_order ASC, created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

// Get project for editing
$editProject = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editProject = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - Admin</title>
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
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }
        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
            font-family: inherit;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            min-height: 120px;
            resize: vertical;
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
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .btn-edit {
            background: #3b82f6;
            color: white;
        }
        .btn-edit:hover {
            background: #2563eb;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f7fafc;
            font-weight: 600;
            color: #2d3748;
        }
        tr:hover {
            background: #f7fafc;
        }
        .project-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .tech-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .tech-tag {
            background: #e0e7ff;
            color: #4338ca;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
        }
        .actions {
            display: flex;
            gap: 8px;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1><i class="fas fa-briefcase"></i> Manage Projects</h1>
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
                <i class="fas fa-<?php echo $editProject ? 'edit' : 'plus-circle'; ?>"></i>
                <?php echo $editProject ? 'Edit Project' : 'Add New Project'; ?>
            </h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $editProject['id'] ?? ''; ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Project Title *</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($editProject['title'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" value="<?php echo $editProject['display_order'] ?? 0; ?>" min="0">
                    </div>
                    <div class="form-group full-width">
                        <label>Short Description</label>
                        <input type="text" name="short_description" value="<?php echo htmlspecialchars($editProject['short_description'] ?? ''); ?>" maxlength="500">
                    </div>
                    <div class="form-group full-width">
                        <label>Full Description *</label>
                        <textarea name="description" required><?php echo htmlspecialchars($editProject['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Technologies (comma-separated)</label>
                        <input type="text" name="technologies" value="<?php echo htmlspecialchars($editProject['technologies'] ?? ''); ?>" placeholder="React, Node.js, MongoDB">
                    </div>
                    <div class="form-group full-width">
                        <label>Project Images (Multiple)</label>
                        <input type="file" name="project_images[]" accept="image/*" multiple style="width: 100%;">
                        <small style="color: #718096; display: block; margin-top: 5px;">Select multiple images to upload. You can select multiple files at once.</small>
                        
                        <?php if ($editProject): 
                            // Get existing images for this project
                            $stmt = $conn->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY display_order ASC");
                            $stmt->bind_param("i", $editProject['id']);
                            $stmt->execute();
                            $images_result = $stmt->get_result();
                            $existing_images = [];
                            while($img = $images_result->fetch_assoc()) {
                                $existing_images[] = $img;
                            }
                            $stmt->close();
                            
                            if (!empty($existing_images)): 
                        ?>
                            <div style="margin-top: 15px;">
                                <strong>Existing Images:</strong>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 10px;">
                                    <?php foreach($existing_images as $img): ?>
                                        <div style="position: relative; border: 2px solid #e2e8f0; border-radius: 8px; padding: 5px;">
                                            <img src="../<?php echo htmlspecialchars($img['image_url']); ?>" 
                                                 alt="Project image" 
                                                 style="width: 100%; height: 120px; object-fit: cover; border-radius: 4px;">
                                            <a href="?delete_image=<?php echo $img['id']; ?>&project_id=<?php echo $editProject['id']; ?>" 
                                               onclick="return confirm('Delete this image?');" 
                                               style="position: absolute; top: 10px; right: 10px; background: #ef4444; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 16px;">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Demo Link</label>
                        <input type="url" name="demo_link" value="<?php echo htmlspecialchars($editProject['demo_link'] ?? ''); ?>" placeholder="https://demo.example.com">
                    </div>
                    <div class="form-group">
                        <label>GitHub Link</label>
                        <input type="url" name="github_link" value="<?php echo htmlspecialchars($editProject['github_link'] ?? ''); ?>" placeholder="https://github.com/username/repo">
                    </div>
                </div>
                <div class="form-buttons">
                    <button type="submit" name="save_project" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $editProject ? 'Update Project' : 'Add Project'; ?>
                    </button>
                    <?php if ($editProject): ?>
                        <a href="manage_projects.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Projects List -->
        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-list"></i> All Projects (<?php echo count($projects); ?>)
            </h2>
            <?php if (empty($projects)): ?>
                <div class="empty-state">
                    <i class="fas fa-briefcase"></i>
                    <h3>No projects yet</h3>
                    <p>Add your first project using the form above.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Technologies</th>
                                <th>Links</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td><?php echo $project['display_order']; ?></td>
                                    <td>
                                        <?php if ($project['image_url']): ?>
                                            <img src="<?php echo htmlspecialchars($project['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="project-image">
                                        <?php else: ?>
                                            <div class="project-image" style="background: #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image" style="color: #cbd5e0;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                                        <?php if ($project['short_description']): ?>
                                            <br><small style="color: #718096;"><?php echo htmlspecialchars(substr($project['short_description'], 0, 60)); ?><?php echo strlen($project['short_description']) > 60 ? '...' : ''; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($project['technologies']): ?>
                                            <div class="tech-tags">
                                                <?php foreach (array_slice(explode(',', $project['technologies']), 0, 3) as $tech): ?>
                                                    <span class="tech-tag"><?php echo htmlspecialchars(trim($tech)); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($project['demo_link']): ?>
                                            <a href="<?php echo htmlspecialchars($project['demo_link']); ?>" target="_blank" style="color: #667eea; margin-right: 10px;">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($project['github_link']): ?>
                                            <a href="<?php echo htmlspecialchars($project['github_link']); ?>" target="_blank" style="color: #667eea;">
                                                <i class="fab fa-github"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($project['created_at'])); ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="?edit=<?php echo $project['id']; ?>" class="btn btn-edit" style="padding: 8px 12px; font-size: 13px;">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?delete=<?php echo $project['id']; ?>" class="btn btn-danger" style="padding: 8px 12px; font-size: 13px;" onclick="return confirm('Are you sure you want to delete this project?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php $conn->close(); ?>
</body>
</html>
