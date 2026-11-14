-- ==============================================================
-- DATABASE SETUP: ajax_lab
-- Penggabungan Modul 11A (AJAX Search), Modul 11B (JSON API),
-- Modul 11C (Bootstrap Dashboard), dan Modul 11D (Tailwind Dashboard)
-- ==============================================================

CREATE DATABASE IF NOT EXISTS ajax_lab;
USE ajax_lab;

-- Hapus tabel lama jika ingin reset struktur
DROP TABLE IF EXISTS products;

-- Buat tabel products lengkap dengan atribut dari seluruh modul
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price INT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    image_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Masukkan data awal produk (Kombinasi data Modul 11A, 11C, dan 11D)
INSERT INTO products (code, name, category, price, stock, status, image_url) VALUES
('P001', 'Mechanical Keyboard', 'Elektronik', 550000, 8, 'active', 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500&auto=format&fit=crop&q=60'),
('P002', 'Mouse Wireless', 'Elektronik', 180000, 12, 'active', 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=500&auto=format&fit=crop&q=60'),
('P003', 'Laptop Stand', 'Aksesoris', 250000, 4, 'active', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500&auto=format&fit=crop&q=60'),
('P004', 'USB Hub Type-C', 'Elektronik', 150000, 15, 'active', 'https://images.unsplash.com/photo-1622445262464-84b1456045b6?w=500&auto=format&fit=crop&q=60'),
('P005', 'Desk Mat Minimalist', 'Aksesoris', 120000, 20, 'active', 'https://images.unsplash.com/photo-1616440347437-b1c73416efc2?w=500&auto=format&fit=crop&q=60'),
('P006', 'Webcam Full HD 1080p', 'Elektronik', 450000, 3, 'active', 'https://images.unsplash.com/photo-1588508065123-287b28e013da?w=500&auto=format&fit=crop&q=60'),
('P007', 'Notebook A5 Hardcover', 'Stationery', 35000, 50, 'active', 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&auto=format&fit=crop&q=60'),
('P008', 'Desk Lamp LED Smart', 'Furniture', 175000, 2, 'active', 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=500&auto=format&fit=crop&q=60');

-- Verifikasi data
SELECT * FROM products;
