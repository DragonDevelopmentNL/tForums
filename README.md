# tForums - Modern PHP Forum Software 🚀

**tForums** is a modern, lightweight, and extensible forum software written in PHP and MySQL, inspired by Flarum. It is easy to install, multilingual (Dutch/English), and features a powerful admin panel.

## Features ✨
- Multilingual (EN/NL) 🌍
- Admin panel (manage forums/users, change roles) 🛠️
- User profile page with avatar and bio 👤
- Discussions, posts, announcements, close topics 💬
- Moderation (edit/delete posts) 🧹
- Responsive and modern design 📱

## Installation 🛠️
1. **Create a database**
   - Create a new MySQL database (e.g. `forum`).
   - Import the `database.sql` file into this database (using phpMyAdmin or MySQL CLI).

2. **Configure database connection**
   - Open `includes/db.php` and fill in your database details:
     ```php
     $DB_HOST = 'localhost';
     $DB_USER = 'your_user';
     $DB_PASS = 'your_password';
     $DB_NAME = 'forum';
     ```

3. **Create an admin account**
   - Generate a password hash in PHP:
     ```php
     <?php echo password_hash('your_password', PASSWORD_DEFAULT); ?>
     ```
   - Add an admin with this SQL (replace the hash, username, and email):
     ```sql
     INSERT INTO users (username, password, email, role)
     VALUES ('admin', 'GENERATED_HASH_HERE', 'admin@yourdomain.com', 'admin');
     ```

4. **Uploads folder**
   - Make sure the `uploads/` folder exists and is writable for profile pictures.

5. **Start**
   - Open `index.php` in your browser. Your forum is ready to use! 🎉

## Key Files 📂
- `index.php` — Homepage with forum overview
- `forum.php` — Discussions within a forum
- `thread.php` — Posts within a discussion
- `profile.php` — User profile (email, bio, avatar)
- `admin.php` — Admin panel
- `includes/db.php` — Database connection (manual setup)
- `database.sql` — SQL for database structure

## Change Language 🌐
- You can change the forum language (English/Dutch) in the admin panel.

## Security 🔒
- Remove unused installation files.
- Use strong passwords for admin accounts.

## Smilies 😃
You can use basic smilies in your posts, for example:
- `:)` → 🙂
- `:(` → 🙁
- `:D` → 😃
- `<3` → ❤️

## License 📄
MIT (free to use and modify)

---
Questions or feedback? Open an issue or send a message! 💬 