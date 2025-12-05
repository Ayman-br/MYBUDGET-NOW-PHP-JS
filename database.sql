SHOW DATABASES;
use mybudget_now;
SHOW TABLES;

SELECT * FROM expenses;

SHOW TABLES;

SELECT * FROM incomes;
DROP DATABASE mybudget_db;


SHOW DATABASES;

CREATE DATABASE mybudget_now;
USE mybudget_now;

CREATE TABLE incomes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(255),
    category VARCHAR(100) DEFAULT 'Other',
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(255),
    category VARCHAR(100) DEFAULT 'Other',
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SHOW tables;

SELECT * FROM expenses;

DELETE  FROM incomes
WHERE id = 4;

SELECT * FROM incomes;

SELECT * FROM incomes ORDER BY created_at ;

SELECT * FROM incomes;