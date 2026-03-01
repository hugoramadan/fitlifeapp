CREATE DATABASE fitlife;
USE fitlife;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    email VARCHAR(100),
    senha VARCHAR(100)
);

CREATE TABLE daily_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    data DATE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE plan_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    daily_plan_id INT,
    titulo VARCHAR(100),
    concluido BOOLEAN DEFAULT 0,
    FOREIGN KEY (daily_plan_id) REFERENCES daily_plans(id)
);

-- usuário fixo
INSERT INTO users (nome, email, senha)
VALUES ('Júlia', 'julia@fitlife.com', '1234');