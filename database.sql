CREATE DATABASE IF NOT EXISTS smartbite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartbite;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role ENUM('admin','customer','restaurant','delivery') NOT NULL DEFAULT 'customer',
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS restaurants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  owner_user_id INT NOT NULL,
  name VARCHAR(160) NOT NULL,
  category VARCHAR(80) NOT NULL DEFAULT 'Food',
  address VARCHAR(220) DEFAULT '',
  rating DECIMAL(2,1) DEFAULT 4.5,
  ratings_count INT DEFAULT 0,
  promo_text VARCHAR(120) DEFAULT '30% off Tk. 149: deal30',
  image_url VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS menu_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  restaurant_id INT NOT NULL,
  name VARCHAR(160) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_user_id INT NOT NULL,
  restaurant_id INT NOT NULL,
  delivery_user_id INT NULL,
  status ENUM('pending','preparing','ready','picked','delivered','cancelled') NOT NULL DEFAULT 'pending',
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  address VARCHAR(220) NOT NULL DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
  FOREIGN KEY (delivery_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  item_name VARCHAR(160) NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Seed demo users (password = 123456)
INSERT INTO users (role,name,email,password_hash) VALUES
('admin','Admin','admin@demo.com',  '$2y$10$K0WZ4j3mP0u3z6nC4u3V2.3CjJ3Ggq8GQn9rA7B2m4D0cI4m1Gx9a'),
('customer','Customer','customer@demo.com','$2y$10$K0WZ4j3mP0u3z6nC4u3V2.3CjJ3Ggq8GQn9rA7B2m4D0cI4m1Gx9a'),
('restaurant','Owner','owner@demo.com',  '$2y$10$K0WZ4j3mP0u3z6nC4u3V2.3CjJ3Ggq8GQn9rA7B2m4D0cI4m1Gx9a'),
('delivery','Rider','rider@demo.com',    '$2y$10$K0WZ4j3mP0u3z6nC4u3V2.3CjJ3Ggq8GQn9rA7B2m4D0cI4m1Gx9a');

INSERT INTO restaurants (owner_user_id,name,category,address,rating,ratings_count,promo_text,image_url) VALUES
(3,'Khao San – Gulshan','Thai','Gulshan 2',4.9,1000,'40% off Tk. 199: wemipro',''),
(3,'Noodles House','Asian','Banani',4.7,800,'30% off Tk. 149: deal30','');

INSERT INTO menu_items (restaurant_id,name,price) VALUES
(1,'Pad Thai',250),(1,'Tom Yum Soup',220),
(2,'Chicken Chow Mein',180),(2,'Beef Ramen',320);
