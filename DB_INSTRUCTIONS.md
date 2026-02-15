# 🗄️ Database Setup Instructions

Since Git does **not** track your database, your friend needs to import the database manually after pulling the code.

## For the Sender (You):
1. I have already generated a database backup file for you: `hallease_final.sql`.
2. Commit and push this file to GitHub along with your code changes.

## For the Receiver (Your Friend):
1. **Pull the Code:**
   ```bash
   git pull origin main
   ```
2. **Import the Database:**
   - Open **phpMyAdmin** (http://localhost/phpmyadmin/).
   - Click on the `hallease` database (or create a new one named `hallease`).
   - Go to the **Import** tab.
   - Click "Choose File" and select `hallease_final.sql` from the project folder.
   - Click **Go** (bottom right).

3. **Check Configuration:**
   - Ensure your `config/db.php` has the correct database credentials (username: `root`, password: empty/default).

✅ **Done!** The database is now synced.
