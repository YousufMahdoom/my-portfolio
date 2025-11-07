# Portfolio Website

A modern, responsive portfolio website built with PHP, MySQL, HTML, and CSS.

## 🚀 Features

### MVP Features (Implemented)
- ✅ **Homepage with Hero Section** - Eye-catching introduction with profile image
- ✅ **Navigation Bar** - Smooth scrolling navigation to all sections
- ✅ **About Me Section** - Personal background, education, and experience
- ✅ **Skills Display** - Visual skill bars with proficiency levels
- ✅ **CV Download** - Downloadable resume/CV button
- ✅ **Projects Portfolio** - Grid layout showcasing projects with:
  - Project images
  - Descriptions
  - Technologies used
  - Demo and GitHub links
  - Detailed project modal popup
- ✅ **Contact Form** - Functional form with:
  - Name, Email, and Message fields
  - Form validation
  - Database storage
  - Success/error messages
- ✅ **Social Media Links** - Links to GitHub, LinkedIn, Twitter, etc.
- ✅ **Responsive Design** - Mobile-friendly and adapts to all screen sizes
- ✅ **Footer** - Copyright and quick links

### Additional Features
- 🎨 Modern gradient design with smooth animations
- 📱 Mobile menu with hamburger icon
- 🔄 Scroll-to-top button
- 💫 Fade-in animations on scroll
- 🎯 Active navigation highlighting
- 📊 Database-driven content management

## 📋 Requirements

- **XAMPP** (or any PHP server with MySQL)
- **PHP 7.4+**
- **MySQL 5.7+**
- Modern web browser

## 🛠️ Installation

1. **Clone or download** this project to your XAMPP htdocs folder:
   ```
   c:\xampp\htdocs\portfoliyo\
   ```

2. **Start XAMPP** services:
   - Start Apache
   - Start MySQL

3. **Run the setup script**:
   - Open your browser and navigate to: `http://localhost/portfoliyo/setup.php`
   - This will automatically:
     - Create the database (`portfolio_db`)
     - Create necessary tables (projects, contact_messages, skills)
     - Insert sample data

4. **Access your portfolio**:
   - Navigate to: `http://localhost/portfoliyo/`

## 📁 Project Structure

```
portfoliyo/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── js/
│   │   └── main.js            # JavaScript functionality
│   ├── images/                # Images folder
│   │   ├── placeholder-profile.svg
│   │   └── placeholder-project.svg
│   └── cv/                    # CV/Resume folder
│       └── your-cv.pdf        # Place your CV here
├── config/
│   └── database.php           # Database configuration
├── includes/
│   ├── header.php             # Header and navigation
│   └── footer.php             # Footer
├── index.php                  # Main homepage
├── process_contact.php        # Contact form handler
├── setup.php                  # Database setup script
└── README.md                  # This file
```

## 🎨 Customization

### 1. Personal Information
Edit `index.php` and update:
- Your name (line 29)
- Profession/title (line 32)
- Description (line 35)
- About me text (lines 64-73)
- Education and experience (lines 76-95)
- Contact information (lines 185-203)
- Social media links (throughout the file)

### 2. Profile Picture
- Replace `assets/images/profile.jpg` with your photo
- Or update the image path in `index.php` (line 46)

### 3. CV/Resume
- Place your CV PDF in `assets/cv/your-cv.pdf`
- Or update the path in `index.php` (line 98)

### 4. Colors & Styling
Edit `assets/css/style.css` CSS variables (lines 2-11):
```css
:root {
    --primary-color: #667eea;    /* Main color */
    --secondary-color: #764ba2;  /* Secondary color */
    --accent-color: #f093fb;     /* Accent color */
    /* ... other variables ... */
}
```

### 5. Projects
Add/edit projects directly in the database:
- Access phpMyAdmin: `http://localhost/phpmyadmin/`
- Navigate to `portfolio_db` → `projects` table
- Add/edit project entries

Or modify the sample projects in `setup.php` (lines 35-72)

### 6. Skills
Add/edit skills in the database:
- Access phpMyAdmin
- Navigate to `portfolio_db` → `skills` table
- Add/edit skill entries

Or modify the sample skills in `setup.php` (lines 88-97)

## 📧 Contact Form Setup

The contact form saves messages to the database by default. To enable email notifications:

1. Open `process_contact.php`
2. Update line 38 with your email address
3. Uncomment line 45 to enable email sending
4. Configure your server's mail settings (if needed)

## 🗄️ Database Schema

### projects table
- `id` - Auto-increment primary key
- `title` - Project title
- `description` - Full project description
- `short_description` - Brief description for cards
- `technologies` - Comma-separated list of technologies
- `image_url` - Path to project image
- `demo_link` - Link to live demo
- `github_link` - Link to GitHub repository
- `display_order` - Order of display
- `created_at` - Timestamp

### contact_messages table
- `id` - Auto-increment primary key
- `name` - Sender's name
- `email` - Sender's email
- `message` - Message content
- `is_read` - Read status
- `created_at` - Timestamp

### skills table
- `id` - Auto-increment primary key
- `name` - Skill name
- `category` - Skill category
- `proficiency` - Proficiency level (0-100)
- `display_order` - Order of display

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## 📱 Responsive Breakpoints

- Desktop: 1200px+
- Tablet: 768px - 1199px
- Mobile: < 768px

## 🔒 Security Notes

- Always sanitize user inputs
- Use prepared statements (already implemented)
- Keep your database credentials secure
- Update the database configuration in `config/database.php` for production
- Enable HTTPS in production
- Implement CSRF protection for production use

## 🚀 Future Enhancements (Optional)

- [ ] Blog/Articles section
- [ ] Light/Dark theme toggle
- [ ] Admin panel for content management
- [ ] Testimonials section
- [ ] Google Analytics integration
- [ ] Multi-language support
- [ ] Project categories/filters
- [ ] Image upload functionality

## 📄 License

This project is open source and available for personal and commercial use.

## 🤝 Support

For issues or questions, please create an issue in the repository or contact via the contact form.

---

**Made with ❤️ using PHP, MySQL, HTML, and CSS**
