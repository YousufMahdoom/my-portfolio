# 🚀 Quick Start Guide

## Step 1: Start XAMPP
1. Open **XAMPP Control Panel**
2. Click **Start** for **Apache**
3. Click **Start** for **MySQL**

## Step 2: Setup Database
1. Open your browser
2. Go to: `http://localhost/portfoliyo/setup.php`
3. Wait for the success message
4. Click "Go to Portfolio"

## Step 3: View Your Portfolio
Your portfolio is now live at: `http://localhost/portfoliyo/`

## Step 4: Customize Your Portfolio

### Update Personal Information
Edit `index.php` and change:
- **Line 29**: Your name
- **Line 32**: Your profession/title
- **Line 35**: Your description
- **Lines 64-73**: About me section
- **Lines 76-95**: Education & experience
- **Lines 185-203**: Contact information
- **Throughout**: Social media links

### Add Your Photo
1. Place your photo in `assets/images/` folder
2. Name it `profile.jpg` (or update line 46 in `index.php`)

### Add Your CV
1. Place your CV PDF in `assets/cv/` folder
2. Name it `your-cv.pdf` (or update line 98 in `index.php`)

### Change Colors
Edit `assets/css/style.css` (lines 2-11):
```css
--primary-color: #667eea;    /* Change this */
--secondary-color: #764ba2;  /* Change this */
```

## Step 5: Manage Content

### Access Admin Panel
1. Go to: `http://localhost/portfoliyo/admin/`
2. Login with:
   - **Username**: admin
   - **Password**: admin123
3. **IMPORTANT**: Change these credentials in `admin/index.php` (lines 7-8)

### Add/Edit Projects
- Use phpMyAdmin: `http://localhost/phpmyadmin/`
- Database: `portfolio_db`
- Table: `projects`

### Add/Edit Skills
- Use phpMyAdmin: `http://localhost/phpmyadmin/`
- Database: `portfolio_db`
- Table: `skills`

### View Contact Messages
- Admin Panel → View Messages
- Or check the `contact_messages` table in phpMyAdmin

## 📱 Test Responsiveness
Open your portfolio on:
- Desktop browser
- Mobile browser (or use browser dev tools)
- Different screen sizes

## 🎨 Customization Tips

### Add More Projects
```sql
INSERT INTO projects (title, short_description, description, technologies, image_url, demo_link, github_link, display_order)
VALUES ('Project Name', 'Short desc', 'Full description', 'PHP, MySQL, HTML', 'assets/images/project.jpg', '#', 'https://github.com/user/repo', 4);
```

### Add More Skills
```sql
INSERT INTO skills (name, category, proficiency, display_order)
VALUES ('React', 'Frontend', 85, 9);
```

## 🔧 Troubleshooting

### Database Connection Error
- Make sure MySQL is running in XAMPP
- Check database credentials in `config/database.php`

### Page Not Found
- Make sure Apache is running
- Check that files are in `c:\xampp\htdocs\portfoliyo\`

### Images Not Showing
- Check file paths in `index.php`
- Make sure images exist in `assets/images/` folder

### Contact Form Not Working
- Check that `process_contact.php` exists
- Check browser console for errors
- Verify database connection

## 📧 Enable Email Notifications

To receive emails when someone contacts you:

1. Open `process_contact.php`
2. Line 38: Change email to yours
3. Line 45: Uncomment the mail() function
4. Configure PHP mail settings (or use SMTP)

## 🌐 Deploy to Production

When ready to go live:

1. **Update Database Credentials**
   - Edit `config/database.php`
   - Use production database details

2. **Change Admin Password**
   - Edit `admin/index.php` (lines 7-8)
   - Use strong password

3. **Enable HTTPS**
   - Uncomment lines in `.htaccess`
   - Get SSL certificate

4. **Update Links**
   - Change localhost URLs to your domain
   - Update social media links

5. **Test Everything**
   - All pages load correctly
   - Contact form works
   - Admin panel accessible
   - Mobile responsive

## 📚 Need Help?

- Check `README.md` for detailed documentation
- Review code comments
- Check browser console for errors
- Verify XAMPP services are running

---

**Enjoy your new portfolio! 🎉**
