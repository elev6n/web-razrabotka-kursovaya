-- Active: 1761921152230@@127.0.0.1@3306@mysql
CREATE DATABASE IF NOT EXISTS buybit;

USE buybit;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories (id)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    status ENUM(
        'pending',
        'processing',
        'completed',
        'cancelled'
    ) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders (id),
    FOREIGN KEY (product_id) REFERENCES products (id)
);

INSERT INTO
    categories (name, description)
VALUES (
        'Процессоры',
        'Центральные процессоры для настольных ПК и ноутбуков'
    ),
    (
        'Видеокарты',
        'Графические карты для игр и работы'
    ),
    (
        'Оперативная память',
        'Модули оперативной памяти'
    ),
    (
        'Накопители',
        'SSD и HDD накопители'
    );

INSERT INTO
    products (
        name,
        description,
        price,
        category_id,
        stock
    )
VALUES (
        'Intel Core i5-12400F',
        '6 ядер, 2.5 ГГц, LGA 1700',
        18999,
        1,
        15
    ),
    (
        'AMD Ryzen 5 5600X',
        '6 ядер, 3.7 ГГц, AM4',
        21999,
        1,
        12
    ),
    (
        'NVIDIA GeForce RTX 4060',
        '8GB GDDR6, DLSS 3',
        45999,
        2,
        8
    ),
    (
        'AMD Radeon RX 7600',
        '8GB GDDR6, RDNA 3',
        38999,
        2,
        10
    ),
    (
        'Kingston Fury Beast 16GB',
        'DDR4 3200MHz',
        4999,
        3,
        25
    ),
    (
        'Crucial P3 Plus 1TB',
        'NVMe PCIe 4.0 SSD',
        7999,
        4,
        20
    );