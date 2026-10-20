Practical 12 - StudentHub
Event Management CRUD with Poster Upload and File Validation

Features Implemented:
1. Event CRUD module for administrators:
   - Add Event: add_event.php
   - View Events: manage_events.php
   - Edit Event: edit_event.php
   - Delete Event: delete_event.php
2. Poster image upload with server-side file type (JPG, PNG, WEBP) and size validation (max 2MB).
3. Secure unique file renaming using uniqid().
4. Automatic poster file cleanup upon event deletion.
5. Intermediate extension: Event filtering by category and status (upcoming, completed, cancelled).

How to Run:
1. Ensure MySQL is running and schema.sql is imported.
2. Open http://localhost/Practical_12/manage_events.php
