Practical 8 - StudentHub
MySQL Schema Design, ER Model, PDO Connectivity, and Prepared Statements

Features Implemented:
1. Normalized database schema defined in schema.sql for users, students, events, registrations and audit logs.
2. Primary keys and Foreign keys defined with cascade delete.
3. Database connection via db.php using PHP PDO with ERRMODE_EXCEPTION and UTF-8 charset.
4. Database connection testing page test_db.php displaying connected status and table counts.
5. Seed data included with over 20 students, multiple events, and sample registrations.

How to Run:
1. Start Apache and MySQL from XAMPP Control Panel.
2. Open phpMyAdmin at http://localhost/phpmyadmin/
3. Import schema.sql
4. Open http://localhost/Practical_8/test_db.php
