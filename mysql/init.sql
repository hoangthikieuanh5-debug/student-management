CREATE DATABASE IF NOT EXISTS student_db;

USE student_db;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    class VARCHAR(50) NOT NULL,
    score DECIMAL(4,2)
);

INSERT INTO students (name, email, class, score)
VALUES
('Nguyen Van An', 'an@gmail.com', 'CNTT01', 8.5),
('Tran Thi Binh', 'binh@gmail.com', 'CNTT01', 9.0),
('Le Van Cuong', 'cuong@gmail.com', 'CNTT02', 7.5);
