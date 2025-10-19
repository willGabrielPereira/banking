CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    is_superuser BOOLEAN NOT NULL DEFAULT FALSE
);

INSERT INTO users (id, name, balance, is_superuser) VALUES (1, 'Fulano de tal', 1000.00, TRUE);