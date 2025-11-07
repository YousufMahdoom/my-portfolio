<?php
require_once 'config/database.php';
$pageTitle = 'Home';

// Fetch projects from database
$conn = getDBConnection();
$projects = [];
$result = $conn->query("SELECT * FROM projects ORDER BY display_order ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Fetch images for this project
        $project_id = $row['id'];
        $images_result = $conn->query("SELECT image_url FROM project_images WHERE project_id = $project_id ORDER BY display_order ASC");
        $images = [];
        if ($images_result) {
            while ($img = $images_result->fetch_assoc()) {
                $images[] = $img['image_url'];
            }
        }
        // If no images in project_images table, use the old image_url field
        if (empty($images) && !empty($row['image_url'])) {
            $images[] = $row['image_url'];
        }
        $row['images'] = $images;
        $projects[] = $row;
    }
}

// Fetch skills from database
$skills = [];
$result = $conn->query("SELECT * FROM skills ORDER BY display_order ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $skills[] = $row;
    }
}

$conn->close();

include 'includes/header.php';
?>

<!-- Hero Section -->
<section id="home" class="hero">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title animate-fade-in">
                    Hi, I'm <span class="text-gradient">MAHDOOM MUHAMMAHU YOUSUF</span>
                </h1>
                <p class="hero-subtitle animate-fade-in-delay">
                    Intern Softweare Developer | Web Developer
                </p>
                <p class="hero-description animate-fade-in-delay-2">
                    I’m an aspiring software engineer passionate about creating secure 
                    and user-friendly web applications. Skilled in HTML, CSS, JavaScript, PHP, and C#, I have hands-on 
                    experience with responsive design, MySQL, and UI tools like Tailwind CSS and Figma. Currently pursuing 
                    my HND in Software Engineering at ESOFT Metro Campus, I focus on innovative solutions that combine design, 
                    functionality, and cybersecurity.
                </p>
                <div class="hero-buttons animate-fade-in-delay-3">
                    <a href="#projects" class="btn btn-primary">
                        <i class="fas fa-briefcase"></i> View Projects
                    </a>
                    <a href="#contact" class="btn btn-secondary">
                        <i class="fas fa-envelope"></i> Contact Me
                    </a>
                </div>
            </div>
            <div class="hero-image animate-fade-in-delay-2">
                <div class="image-wrapper">
                    <img src="assets/images/profile.png" alt="Profile Picture" onerror="this.src='assets/images/placeholder-profile.svg'">
                </div>
            </div>
        </div>
    </div>
    <div class="scroll-indicator">
        <a href="#about">
            <i class="fas fa-chevron-down"></i>
        </a>
    </div>
</section>

<!-- About Section -->
<section id="about" class="section about-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">About Me</h2>
            <p class="section-subtitle">Get to know more about my background and expertise</p>
        </div>
        
        <div class="about-content">
            <div class="about-text">
                <h3>Hello! I'm a Web Developer</h3>
                <p>
                    With a passion for creating beautiful and functional web applications, I specialize in 
                    full-stack development using modern technologies. My journey in web development started 
                    several years ago, and I've been constantly learning and evolving ever since.
                </p>
                <p>
                    I believe in writing clean, maintainable code and creating user experiences that are 
                    both intuitive and delightful. Whether it's building a complex web application or a 
                    simple landing page, I approach every project with dedication and attention to detail.
                </p>
                
                <div class="about-info">
                    <div class="info-item">
                        <i class="fas fa-graduation-cap"></i>
                        <div>
                            <h4>Education</h4>
                            <p>HND in Software Engineering</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-briefcase"></i>
                        <div>
                            <h4>Experience</h4>
                            <p>not yet</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h4>Location</h4>
                            <p>Kurunegala, Sri Lanka</p>
                        </div>
                    </div>
                </div>
                
                <div class="cv-download">
                    <a href="assets/cv/Yousuf_Mahdoom_CV.pdf" class="btn btn-primary" download>
                        <i class="fas fa-download"></i> Download CV
                    </a>
                </div>
            </div>
            
            <div class="skills-container">
                <h3>Technical Skills</h3>
                <div class="skills-grid">
                    <?php foreach ($skills as $skill): ?>
                    <div class="skill-item">
                        <div class="skill-header">
                            <span class="skill-name"><?php echo htmlspecialchars($skill['name']); ?></span>
                            <span class="skill-percentage"><?php echo $skill['proficiency']; ?>%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-progress" style="width: <?php echo $skill['proficiency']; ?>%"></div>
                        </div>
                        <span class="skill-category"><?php echo htmlspecialchars($skill['category']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Projects Section -->
<section id="projects" class="section projects-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">My Projects</h2>
            <p class="section-subtitle">Check out some of my recent work</p>
        </div>
        
        <div class="projects-grid">
            <?php foreach ($projects as $index => $project): ?>
            <div class="project-card">
                <div class="project-image project-slideshow" data-project-id="<?php echo $project['id']; ?>">
                    <?php if (!empty($project['images'])): ?>
                        <?php foreach ($project['images'] as $imgIndex => $imageUrl): ?>
                            <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                                 alt="<?php echo htmlspecialchars($project['title']); ?>"
                                 class="slideshow-image <?php echo $imgIndex === 0 ? 'active' : ''; ?>"
                                 onerror="this.src='assets/images/placeholder-project.svg'">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <img src="assets/images/placeholder-project.svg" 
                             alt="<?php echo htmlspecialchars($project['title']); ?>"
                             class="slideshow-image active">
                    <?php endif; ?>
                    <div class="project-overlay">
                        <button class="btn btn-icon" onclick="openProjectModal(<?php echo $project['id']; ?>)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php if (count($project['images']) > 1): ?>
                        <div class="slideshow-indicators">
                            <?php foreach ($project['images'] as $imgIndex => $imageUrl): ?>
                                <span class="indicator <?php echo $imgIndex === 0 ? 'active' : ''; ?>"></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="project-content">
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p><?php echo htmlspecialchars($project['short_description']); ?></p>
                    <div class="project-tech">
                        <?php 
                        $techs = explode(',', $project['technologies']);
                        foreach ($techs as $tech): 
                        ?>
                        <span class="tech-tag"><?php echo trim(htmlspecialchars($tech)); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="project-links">
                        <?php if (!empty($project['demo_link'])): ?>
                        <a href="<?php echo htmlspecialchars($project['demo_link']); ?>" class="project-link" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Demo
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($project['github_link'])): ?>
                        <a href="<?php echo htmlspecialchars($project['github_link']); ?>" class="project-link" target="_blank">
                            <i class="fab fa-github"></i> Code
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="section contact-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Get In Touch</h2>
            <p class="section-subtitle">Feel free to reach out for collaborations or just a friendly chat</p>
        </div>
        
        <div class="contact-content">
            <div class="contact-info">
                <h3>Contact Information</h3>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4>Email</h4>
                        <a href="mailto:yousufmahdoom@gmail.com">yousufmahdoom@gmail.com</a>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h4>Phone</h4>
                        <a href="tel:+9476 401 3545">+94 76 401 3545</a>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4>Location</h4>
                        <p>Kurunegala, Sri Lanka</p>
                    </div>
                </div>
                
                <div class="social-links-large">
                    <a href="https://github.com/YousufMahdoom" target="_blank" title="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/yousuf-mahdoom-89889536a" target="_blank" title="LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="https://twitter.com/YousufMahdoom" target="_blank" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
            
            <div class="contact-form-container">
                <form id="contactForm" class="contact-form" method="POST" action="process_contact.php">
                    <div id="formMessage" class="form-message"></div>
                    
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Project Modal -->
<div id="projectModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeProjectModal()">&times;</span>
        <div id="modalBody"></div>
    </div>
</div>

<script>
// Store projects data for modal
const projectsData = <?php echo json_encode($projects); ?>;
</script>

<?php include 'includes/footer.php'; ?>
