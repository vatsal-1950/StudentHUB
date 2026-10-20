Practical 14 - StudentHub
Admin Dashboard with Role-Based Access Control, Dynamic Menu, and Audit Log

Features Implemented:
1. Dynamic Administrator Dashboard (admin_dashboard.php) with SQL aggregation:
   - Total Students & Active count
   - Scheduled Events count
   - Confirmed Event Bookings count
   - System User Accounts count
2. Access restriction ensuring only users with role 'admin' can view the dashboard.
3. Dynamic role-based navigation menu (menu.php) adapting for Admin, Student and Guest.
4. Intermediate extension: Student active/inactive status toggle with instant database update.
5. Traceability and security: Audit logs table recording administrative actions and user logins.

Default Admin Account:
Email: admin@studenthub.com
Password: Admin@123

How to Run:
1. Ensure MySQL is running and schema.sql is imported.
2. Open http://localhost/Practical_14/login.php
3. Log in with admin credentials to access the admin dashboard.
