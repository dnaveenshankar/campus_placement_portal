CREATE TABLE colleges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_name VARCHAR(255) NOT NULL,
    mail_id VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    logo VARCHAR(255) DEFAULT 'assets/college-default.png',  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,  
    dept_code VARCHAR(20) NOT NULL UNIQUE,  
    stream ENUM('UG', 'PG') NOT NULL, 
    dept_name VARCHAR(255) NOT NULL,  
    college_id INT NOT NULL, 
    FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE CASCADE  
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, 
    department_code VARCHAR(20) NOT NULL,
    college_username VARCHAR(50) NOT NULL,  -- Added college username
    profile_status ENUM('Not Approved', 'Approved') DEFAULT 'Not Approved',
    image VARCHAR(255) DEFAULT 'assets/student-default.png',
    name VARCHAR(100),
    marks_10 INT,
    marks_12 INT,
    marks_ug INT,
    marks_pg INT,
    backlog_history TEXT,
    current_backlogs TEXT,
    dob DATE,
    gender ENUM('Male', 'Female', 'Other'),
    email VARCHAR(100),
    phone VARCHAR(20),
    resume VARCHAR(255)
);

CREATE TABLE drives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_username VARCHAR(50) NOT NULL, 
    company_name VARCHAR(255) NOT NULL,
    address TEXT,
    role VARCHAR(255) NOT NULL,
    logo VARCHAR(255) DEFAULT 'assets/company-default.png', 
    job_description TEXT,
    description TEXT,
    date DATE,
    status ENUM('open', 'close') DEFAULT 'open',
    ctc DECIMAL(10, 2) 
);

CREATE TABLE eligibility (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drive_id INT NOT NULL,  
    department_code VARCHAR(20) NOT NULL,
    profile_status ENUM('Not Approved', 'Approved') DEFAULT 'Not Approved',
    marks_10 INT,
    marks_12 INT,
    marks_ug INT,
    marks_pg INT,
    backlog_history TEXT,
    current_backlogs TEXT,
    dob DATE,
    eligible_gender ENUM('Both', 'Male', 'Female') DEFAULT 'Both', 
    FOREIGN KEY (drive_id) REFERENCES drives(id) ON DELETE CASCADE
);

CREATE TABLE opted_students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_username VARCHAR(255),
    drive_id INT,
    student_id INT,
    status ENUM('Selected', 'Rejected', 'Pending') DEFAULT 'Pending',
    FOREIGN KEY (college_username) REFERENCES colleges(username),
    FOREIGN KEY (drive_id) REFERENCES drives(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
);

