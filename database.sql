CREATE DATABASE IF NOT EXISTS fitpro_db;
USE fitpro_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    dob DATE,
    gender VARCHAR(10),
    height_ft INT NOT NULL,
    height_in INT NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    address TEXT,
    plan VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    schedule_time VARCHAR(50) NOT NULL,
    trainer VARCHAR(50) NOT NULL,
    capacity INT DEFAULT 30
);

INSERT INTO classes (name, type, schedule_time, trainer, capacity) VALUES 
('Morning Vinyasa Flow', 'Yoga', '07:00 AM', 'Sarah Jenkins', 20),
('HIIT Blast', 'Cardio', '06:00 PM', 'Mike Ross', 25),
('Powerlifting Foundations', 'Strength', '08:00 AM', 'Chris Hemsworth', 15),
('Zumba Dance Party', 'Cardio', '05:30 PM', 'Maria Rodriguez', 30),
('Restorative Yoga', 'Yoga', '07:30 PM', 'Sarah Jenkins', 20);
