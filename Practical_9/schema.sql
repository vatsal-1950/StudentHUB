CREATE DATABASE IF NOT EXISTS studenthub_db;
USE studenthub_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME NULL
);

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    course VARCHAR(100) NOT NULL,
    year VARCHAR(50) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    poster_image VARCHAR(255) NULL,
    status ENUM('upcoming', 'completed', 'cancelled') NOT NULL DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('confirmed', 'pending', 'cancelled') NOT NULL DEFAULT 'confirmed',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@studenthub.com', '$2y$10$m4yXvUPPuqgkwrRhzOaHpuAJ9ont0yQ6jLckeceTe0lcEaaOz9D8q', 'admin'),
('Student User', 'student@studenthub.com', '$2y$10$LgMnBB2DNAxKBBNaMiJ3aujdDf.E8TzaUiitsMIJ8DOZtT2VAY2VS', 'student')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO students (name, email, mobile, course, year, gender, status) VALUES
('Aarav Patel', 'aarav.patel@charusat.edu.in', '9825011111', 'B.Tech Computer Engineering', 'First Year', 'Male', 'active'),
('Diya Shah', 'diya.shah@charusat.edu.in', '9825022222', 'B.Tech Information Technology', 'Second Year', 'Female', 'active'),
('Rohan Mehta', 'rohan.mehta@charusat.edu.in', '9825033333', 'B.Tech Computer Engineering', 'Third Year', 'Male', 'active'),
('Ananya Joshi', 'ananya.joshi@charusat.edu.in', '9825044444', 'B.Tech Civil Engineering', 'Second Year', 'Female', 'active'),
('Karan Desai', 'karan.desai@charusat.edu.in', '9825055555', 'B.Tech Mechanical Engineering', 'Fourth Year', 'Male', 'active'),
('Pooja Trivedi', 'pooja.trivedi@charusat.edu.in', '9825066666', 'B.Tech Information Technology', 'Third Year', 'Female', 'active'),
('Varun Sharma', 'varun.sharma@charusat.edu.in', '9825077777', 'B.Tech Computer Engineering', 'First Year', 'Male', 'active'),
('Neha Parmar', 'neha.parmar@charusat.edu.in', '9825088888', 'B.Tech Civil Engineering', 'Third Year', 'Female', 'active'),
('Harsh Vora', 'harsh.vora@charusat.edu.in', '9825099999', 'B.Tech Mechanical Engineering', 'Second Year', 'Male', 'active'),
('Riddhi Dave', 'riddhi.dave@charusat.edu.in', '9825012345', 'B.Tech Information Technology', 'Fourth Year', 'Female', 'active'),
('Yash Soni', 'yash.soni@charusat.edu.in', '9825023456', 'B.Tech Computer Engineering', 'Second Year', 'Male', 'active'),
('Isha Bhatt', 'isha.bhatt@charusat.edu.in', '9825034567', 'B.Tech Information Technology', 'First Year', 'Female', 'active'),
('Manan Modi', 'manan.modi@charusat.edu.in', '9825045678', 'B.Tech Civil Engineering', 'Fourth Year', 'Male', 'active'),
('Kinjal Pandya', 'kinjal.pandya@charusat.edu.in', '9825056789', 'B.Tech Mechanical Engineering', 'Third Year', 'Female', 'active'),
('Dhruv Panchal', 'dhruv.panchal@charusat.edu.in', '9825067890', 'B.Tech Computer Engineering', 'Third Year', 'Male', 'active'),
('Tanvi Raval', 'tanvi.raval@charusat.edu.in', '9825078901', 'B.Tech Information Technology', 'Second Year', 'Female', 'active'),
('Siddharth Vyas', 'siddharth.vyas@charusat.edu.in', '9825089012', 'B.Tech Computer Engineering', 'Fourth Year', 'Male', 'active'),
('Meera Solanki', 'meera.solanki@charusat.edu.in', '9825090123', 'B.Tech Civil Engineering', 'First Year', 'Female', 'active'),
('Pranav Makwana', 'pranav.makwana@charusat.edu.in', '9825001234', 'B.Tech Mechanical Engineering', 'Second Year', 'Male', 'active'),
('Kavya Barot', 'kavya.barot@charusat.edu.in', '9825010203', 'B.Tech Information Technology', 'First Year', 'Female', 'active')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO events (title, description, event_date, venue, category, status) VALUES
('Web Development Workshop', 'Hands-on training on modern web frameworks and full-stack development.', '2026-11-10', 'Seminar Hall 1', 'Workshop', 'upcoming'),
('Annual Hackathon 2026', '24-hour coding challenge solving real campus problem statements.', '2026-11-15', 'Central Computer Lab', 'Competition', 'upcoming'),
('AI & Machine Learning Seminar', 'Expert session on cutting-edge generative AI and neural models.', '2026-11-20', 'Auditorium A', 'Seminar', 'upcoming'),
('Inter-College Sports Meet', 'Annual athletic competitions including football, cricket and chess.', '2026-11-25', 'University Sports Ground', 'Sports', 'upcoming'),
('Cyber Security Symposium', 'Talks on ethical hacking, network defense and application security.', '2026-12-01', 'Seminar Hall 2', 'Seminar', 'upcoming')
ON DUPLICATE KEY UPDATE title=VALUES(title);

INSERT INTO registrations (student_id, event_id, status) VALUES
(1, 1, 'confirmed'),
(2, 1, 'confirmed'),
(3, 2, 'confirmed'),
(4, 3, 'confirmed'),
(5, 4, 'confirmed')
ON DUPLICATE KEY UPDATE status=VALUES(status);
