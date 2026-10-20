Practical 9 - StudentHub
Secure User Registration with Database Insert, Duplicate Email Check, and Password Hashing

Features Implemented:
1. Registration form processed via POST with comprehensive server-side input validation.
2. Duplicate email check using prepared statements before inserting records.
3. Secure password hashing using PHP password_hash() with PASSWORD_DEFAULT.
4. User record inserted into users table and student profile created in students table.
5. Audit log recorded in audit_logs table with action USER_REGISTRATION and client IP.
6. Registered users listing page view_users.php displaying saved users without exposing password hashes.

How to Run:
1. Ensure MySQL is running and schema.sql is imported in studenthub_db.
2. Open http://localhost/Practical_9/register.php
3. Register a new user and test duplicate email validation.
