Event Scoring System

Project Overview
The Event Scoring System enables admins to manage judges, judges to assign scores to users, and displays a public scoreboard with real-time updates. The frontend uses static HTML, JavaScript, and custom CSS, while the backend leverages PHP and MySQL. Webhook support notifies external services of score submissions.
Setup Instructions
Local Setup (XAMPP)

Install XAMPP on Ubuntu:

Download from Apache Friends.
Place the project in /opt/lampp/htdocs/event-scoring.


Organize Files:

Structure:event-scoring/
├── frontend/
│   ├── public/
│   │   ├── css/styles.css
│   │   ├── js/scoreboard.js
│   │   ├── admin.html
│   │   ├── judge.html
│   │   └── scoreboard.html
│   ├── package.json
│   └── vercel.json
├── backend/
│   ├── src/
│   │   ├── db.php
│   │   ├── judges.php
│   │   ├── users.php
│   │   └── scores.php
│   ├── api/
│   │   └── scoreboard.php
│   └── db/
│       └── schema.sql
└── README.md




Start XAMPP:
sudo /opt/lampp/lampp start


Configure MySQL:

Create database event_scoring in phpMyAdmin (http://localhost/phpmyadmin) or via CLI:/opt/lampp/bin/mysql -u root -e "CREATE DATABASE event_scoring;"


Create MySQL user:/opt/lampp/bin/mysql -u root

CREATE USER 'event_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON event_scoring.* TO 'event_user'@'localhost';
FLUSH PRIVILEGES;


Import schema:/opt/lampp/bin/mysql -u event_user -p event_scoring < /opt/lampp/htdocs/event-scoring/backend/db/schema.sql




Update Database Credentials:

Edit backend/src/db.php with username (event_user) and password (secure_password).


Set Permissions:
sudo chown -R $USER:$USER /opt/lampp/htdocs/event-scoring
sudo chmod -R 755 /opt/lampp/htdocs/event-scoring



Testing

Local URLs:

Admin: http://localhost/event-scoring/frontend/public/admin.html
Judge: http://localhost/event-scoring/frontend/public/judge.html
Scoreboard: http://localhost/event-scoring/frontend/public/scoreboard.html
API: http://localhost/event-scoring/backend/api/scoreboard.php


Steps:

Admin Panel:
Add a judge (e.g., username judge1, display name Judge Smith).
Verify judge appears in the table.


Judge Portal:
Select a judge and user, submit a score (1-100).
Confirm success message and webhook delivery (if configured).


Scoreboard:
Verify users (Alice Smith, Bob Johnson, Charlie Brown) and scores display.
Check auto-refresh every 10 seconds with top three highlighted (gold, silver, bronze).


API:curl http://localhost/event-scoring/backend/api/scoreboard.php





Database Schema
The database (event_scoring) consists of three tables, defined in backend/db/schema.sql:
CREATE TABLE judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL
);

CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judge_id INT NOT NULL,
    user_id INT NOT NULL,
    score INT NOT NULL CHECK (score >= 1 AND score <= 100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (judge_id) REFERENCES judges(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (username, display_name) VALUES
('user1', 'Alice Smith'),
('user2', 'Bob Johnson'),
('user3', 'Charlie Brown');


judges: Stores judge information with unique usernames.
users: Stores user information with unique usernames and display names for the scoreboard.
scores: Records scores with foreign keys to judges and users, ensuring referential integrity.

Assumptions

The project is tested on Ubuntu with XAMPP installed.
MySQL is accessible with a root user or default credentials.
The user has basic knowledge of Git, CLI commands, and MySQL.
No authentication is required for admin or judge access (simplified for demo purposes).
Localhost URLs (http://localhost/...) are used for testing;


Design Choices

Database Structure:
Normalized tables (judges, users, scores) minimize redundancy.
Foreign keys with ON DELETE CASCADE ensure data integrity.
COALESCE(SUM(s.score), 0) in getScoreboard() handles users with no scores.


PHP Constructs:
PDO is used for secure database interactions, preventing SQL injection.
REST-like API endpoints (judges.php, scores.php, scoreboard.php) simplify frontend integration.
Error logging (error_log) aids debugging without breaking JSON output.


Frontend:
Static HTML (admin.html, judge.html, scoreboard.html) ensures simplicity and compatibility with Vercel.
Custom CSS (styles.css) provides a clean, responsive design without external frameworks.
JavaScript (scoreboard.js) uses fetch for asynchronous API calls and setInterval for real-time updates.


Future Features
If more time were available, I would add:

Enhanced Webhook Integration: Support for Discord, Slack, or custom webhook endpoints with configurable payloads for real-time notifications.
User Authentication: Implement login for admins and judges using PHP sessions or JWT to secure endpoints.
Real-Time Updates: Replace setInterval with WebSockets for instant scoreboard updates.
Score Analytics: Add a dashboard showing score trends, judge activity, and user rankings.
Input Validation: Enhance frontend and backend validation for usernames and scores to prevent edge cases.
Pagination and Filtering: Allow scoreboard filtering by event or time period with paginated results.

Deployment

Frontend (Vercel):

Push frontend/ to GitHub.
Deploy via Vercel CLI:cd frontend
vercel --prod


Update backendUrl in admin.html, judge.html, and scoreboard.js to https://your-app-name.000webhostapp.com/event-scoring.


Backend (000webhost):

Upload backend/ to public_html/event-scoring via File Manager or FTP.
Create MySQL database and import db/schema.sql.
Update src/db.php with 000webhost credentials.



Publicly Accessible Link

Local Demo: Currently accessible at http://localhost/event-scoring/frontend/public/scoreboard.html for testing.
Public Deployment: Deploy to Vercel (frontend) and 000webhost (backend) to obtain public URLs (e.g., https://event-scoring-system.vercel.app/scoreboard.html). Share the GitHub repository for code preview: https://github.com/your-username/event-scoring-system.

Troubleshooting

PHP0417 Error (Unknown Function getScoreboard):
Ensure getScoreboard() is defined in backend/src/scores.php.
Verify require_once '../src/scores.php'; in scoreboard.php.


HTTP 500 Error:
Check /opt/lampp/logs/php_error_log for errors.
Test database connection:/opt/lampp/bin/mysql -u event_user -p event_scoring


Re-import schema if tables are missing.


Scoreboard Not Displaying:
Test API:curl -v http://localhost/event-scoring/backend/api/scoreboard.php


Check browser console for errors.
Verify scores in scores table via phpMyAdmin.



GitHub Repository

Push changes:cd /opt/lampp/htdocs/event-scoring
git add .
git commit -m "Update project with fixes and enhanced README"
git push origin main



