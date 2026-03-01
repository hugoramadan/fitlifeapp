CREATE DATABASE IF NOT EXISTS fitlife
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE fitlife;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS daily_plans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  data DATE NOT NULL,
  UNIQUE KEY uq_user_data (user_id, data),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS plan_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  daily_plan_id INT NOT NULL,
  titulo VARCHAR(120) NOT NULL,
  concluido TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (daily_plan_id) REFERENCES daily_plans(id)
);

-- Usuário fixo (POC)
INSERT IGNORE INTO users (nome, email, senha)
VALUES ('Júlia', 'julia@fitlife.com', '1234');