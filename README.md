# Wholesaler-Vegetable-Management-System
I create this system for wholesalers who sell the large number of vegetables. They can easily manage their stocks using this system.

Sql Tables:
CREATE DATABASE IF NOT EXISTS wholesaler_db;
USE wholesaler_db;

-- Farmer table
CREATE TABLE farmers (
    farmer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(15),
    location VARCHAR(100)
);

-- Vegetable table
CREATE TABLE vegetables (
    veg_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    unit VARCHAR(20)
);

-- Stock table
CREATE TABLE stocks (
    stock_id INT AUTO_INCREMENT PRIMARY KEY,
    veg_id INT,
    farmer_id INT,
    quantity FLOAT,
    price FLOAT,
    date DATE,
    FOREIGN KEY (veg_id) REFERENCES vegetables(veg_id) ON DELETE CASCADE,
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id) ON DELETE CASCADE
);

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
