CREATE DATABASE IF NOT EXISTS testdb2;
USE testdb2;
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT,
    admins_id INT NOT NULL,
    FOREIGN KEY (admins_id) REFERENCES admins(id) ON DELETE CASCADE
);

INSERT INTO admins (name, email) VALUES
  ('saif', 'saifayari@gmail.com'),
  ('Ali', 'Ali@gmail.com');

INSERT INTO comments (content, admins_id) VALUES
  ('Kessa7 sa7bi', 1),
  ('Post de Bob', 2);