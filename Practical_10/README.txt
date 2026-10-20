Practical 10 - StudentHub
Secure Login/Logout with Sessions, Role-Based Access, Timeout, and Remember-Me Token

Features Implemented:
1. Secure login with password verification using password_verify().
2. Session fixation prevention via session_regenerate_id(true).
3. Role-Based Access Control (RBAC):
   - Admin redirected to admin_dashboard.php
   - Student redirected to student_dashboard.php
4. Session inactivity timeout (15 minutes).
5. Remember-Me cookie functionality.
6. Clean logout handling with session invalidation and cookie removal.

Default Credentials:
Admin: admin@studenthub.com | Password: Admin@123
Student: student@studenthub.com | Password: Student@123

How to Run:
1. Ensure MySQL is running and schema.sql is imported.
2. Open http://localhost/Practical_10/login.php
