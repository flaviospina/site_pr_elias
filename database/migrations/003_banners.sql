-- ============================================================
--  MIGRAÇÃO — Banners configuráveis das páginas
--  Rode UMA vez no phpMyAdmin (banco do site) se o site JÁ estava instalado.
--  Instalações novas (schema.sql atualizado) já incluem esta tabela.
-- ============================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS banners (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  location VARCHAR(60) NOT NULL UNIQUE,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  image VARCHAR(500) NULL,
  title VARCHAR(255) NULL,
  subtitle VARCHAR(500) NULL,
  button_text VARCHAR(120) NULL,
  button_url VARCHAR(500) NULL,
  button2_text VARCHAR(120) NULL,
  button2_url VARCHAR(500) NULL,
  overlay TINYINT NOT NULL DEFAULT 70,
  text_color VARCHAR(20) NOT NULL DEFAULT 'light',
  align VARCHAR(10) NOT NULL DEFAULT 'center',
  height VARCHAR(10) NOT NULL DEFAULT 'medium',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Semente do banner da Página Inicial usando os textos/imagem atuais do hero.
INSERT INTO banners (location, enabled, image, title, subtitle, button_text, button_url, button2_text, button2_url, overlay, text_color, align, height)
SELECT 'home', 1,
       (SELECT `value` FROM settings WHERE `key`='hero_image'),
       (SELECT `value` FROM settings WHERE `key`='hero_title'),
       (SELECT `value` FROM settings WHERE `key`='hero_subtitle'),
       'Conhecer os livros', 'livros', 'Ler os sermões', 'sermoes',
       80, 'light', 'center', 'large'
WHERE NOT EXISTS (SELECT 1 FROM banners WHERE location='home');
