-- ============================================================
--  Site Pr. Elias José da Silva — Schema do Banco de Dados
--  MySQL 5.7+ / MariaDB 10.3+  |  utf8mb4
--  Importar via phpMyAdmin ANTES dos arquivos em /database/seeds
-- ============================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Usuários do painel administrativo
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','editor') NOT NULL DEFAULT 'admin',
  active TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Controle de tentativas de login (proteção contra força bruta)
CREATE TABLE IF NOT EXISTS login_attempts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(45) NOT NULL,
  email VARCHAR(190) NULL,
  attempted_at DATETIME NOT NULL,
  INDEX idx_ip_time (ip, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Configurações gerais (chave/valor) — inclui gateways de pagamento
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(120) NOT NULL UNIQUE,
  `value` TEXT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Livros (loja)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS books (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  subtitle VARCHAR(255) NULL,
  excerpt TEXT NULL,
  description MEDIUMTEXT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  sale_price DECIMAL(10,2) NULL,
  image VARCHAR(500) NULL,
  gallery TEXT NULL COMMENT 'URLs adicionais separadas por quebra de linha',
  pages INT NULL,
  isbn VARCHAR(40) NULL,
  stock INT NOT NULL DEFAULT 100,
  in_stock TINYINT(1) NOT NULL DEFAULT 1,
  featured TINYINT(1) NOT NULL DEFAULT 0,
  badge VARCHAR(60) NULL COMMENT 'ex.: Mais vendido, Lançamento',
  sort_order INT NOT NULL DEFAULT 0,
  status ENUM('published','draft') NOT NULL DEFAULT 'published',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Sermões
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sermons (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  excerpt TEXT NULL,
  content MEDIUMTEXT NOT NULL,
  bible_reference VARCHAR(255) NULL,
  video_url VARCHAR(500) NULL,
  image VARCHAR(500) NULL,
  status ENUM('published','draft') NOT NULL DEFAULT 'published',
  published_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Devocionais
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS devotionals (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  excerpt TEXT NULL,
  content MEDIUMTEXT NOT NULL,
  bible_reference VARCHAR(255) NULL,
  image VARCHAR(500) NULL,
  status ENUM('published','draft') NOT NULL DEFAULT 'published',
  published_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Agenda (eventos)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  location VARCHAR(255) NULL,
  city VARCHAR(120) NULL,
  event_date DATE NOT NULL,
  event_time VARCHAR(20) NULL,
  link VARCHAR(500) NULL,
  status ENUM('published','draft') NOT NULL DEFAULT 'published',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Depoimentos de leitores (prova social — conversão)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS testimonials (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  author VARCHAR(120) NOT NULL,
  author_role VARCHAR(120) NULL,
  content TEXT NOT NULL,
  rating TINYINT NOT NULL DEFAULT 5,
  status ENUM('published','draft') NOT NULL DEFAULT 'published',
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Páginas institucionais editáveis (privacidade, termos, sobre…)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  content MEDIUMTEXT NULL,
  status ENUM('published','draft') NOT NULL DEFAULT 'published',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Pedidos da loja
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_code VARCHAR(20) NOT NULL UNIQUE,
  customer_name VARCHAR(160) NOT NULL,
  customer_email VARCHAR(190) NOT NULL,
  customer_phone VARCHAR(30) NULL,
  customer_cpf VARCHAR(20) NULL,
  address_zip VARCHAR(12) NULL,
  address_street VARCHAR(255) NULL,
  address_number VARCHAR(20) NULL,
  address_complement VARCHAR(120) NULL,
  address_district VARCHAR(120) NULL,
  address_city VARCHAR(120) NULL,
  address_state VARCHAR(2) NULL,
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
  shipping DECIMAL(10,2) NOT NULL DEFAULT 0,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  payment_method VARCHAR(40) NULL COMMENT 'mercadopago, pix, pagseguro, paypal, whatsapp',
  payment_ref VARCHAR(190) NULL COMMENT 'id/preferência retornada pelo gateway',
  status ENUM('pending','paid','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
  notes TEXT NULL,
  lgpd_consent TINYINT(1) NOT NULL DEFAULT 0,
  ip VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  book_id INT UNSIGNED NULL,
  title VARCHAR(255) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  line_total DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  INDEX idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Mensagens de contato / pedidos de oração
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(30) NULL,
  subject VARCHAR(190) NULL,
  message TEXT NOT NULL,
  lgpd_consent TINYINT(1) NOT NULL DEFAULT 0,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  ip VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Inscritos na lista de devocionais (captura de leads)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS subscribers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  lgpd_consent TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- DADOS INICIAIS
-- ============================================================

-- Usuário administrador inicial
-- E-mail: admin@eliasjosedasilva.com.br  |  Senha: PrElias@2026
-- (TROQUE A SENHA no primeiro acesso: Painel > Usuários)
INSERT INTO users (name, email, password_hash, role) VALUES
('Pr. Elias José da Silva', 'admin@eliasjosedasilva.com.br',
 '$2y$12$EtaZ2nbdfbO4ILOYg.NqteImeI0I.P.Wj0v/0LAJdzFZxspDWJZdi', 'admin');

-- Configurações do site
INSERT INTO settings (`key`, `value`) VALUES
('site_name', 'Pr. Elias José da Silva'),
('site_tagline', 'Educador Cristão | Pastor Evangélico | Escritor'),
('site_logo', 'https://eliasjosedasilva.com.br/wp-new/wp-content/uploads/2026/06/logo-ejds.png'),
('whatsapp', '5511932065151'),
('whatsapp_display', '(11) 93206-5151'),
('contact_email', 'contato@eliasjosedasilva.com.br'),
('attendance_hours', '08h — 18h, todos os dias, exclusivamente pelo WhatsApp'),
('instagram_url', ''),
('facebook_url', ''),
('youtube_url', ''),
('hero_title', 'Uma vida dedicada ao ensino da Palavra de Deus'),
('hero_subtitle', 'Sermões, devocionais e livros que fortalecem a fé, edificam a igreja e transformam vidas pela verdade do Evangelho.'),
('hero_image', 'https://eliasjosedasilva.com.br/wp-new/wp-content/uploads/2026/06/Hero-Pulpito.jpg'),
('about_image', 'https://eliasjosedasilva.com.br/wp-new/wp-content/uploads/2026/06/WhatsApp-Image-2026-06-18-at-11.12.08.jpeg'),
('announcement_bar', '📚 Livros com envio para todo o Brasil — compra 100% segura'),
('footer_verse', '“Tudo quanto fizerdes, fazei-o de todo o coração, como ao Senhor.” — Colossenses 3:23'),
('guarantee_days', '7'),
('free_shipping_min', '0'),
('shipping_flat', '0.00'),
('gtm_id', ''),
-- Gateways de pagamento (preencher no Painel > Configurações > Pagamentos)
('pay_mercadopago_enabled', '0'),
('pay_mercadopago_public_key', ''),
('pay_mercadopago_access_token', ''),
('pay_mercadopago_sandbox', '1'),
('pay_pagseguro_enabled', '0'),
('pay_pagseguro_email', ''),
('pay_pagseguro_token', ''),
('pay_pagseguro_sandbox', '1'),
('pay_paypal_enabled', '0'),
('pay_paypal_client_id', ''),
('pay_paypal_secret', ''),
('pay_paypal_sandbox', '1'),
('pay_pix_enabled', '1'),
('pay_pix_key', ''),
('pay_pix_key_type', 'celular'),
('pay_pix_holder', 'Elias José da Silva'),
('pay_whatsapp_enabled', '1'),
('cookie_banner_enabled', '1'),
-- Notificações no celular (Painel > Notificações)
('telegram_enabled', '0'),
('telegram_bot_token', ''),
('telegram_chat_id', '');

-- Livros (imagens da galeria da loja atual)
INSERT INTO books (title, slug, subtitle, excerpt, description, price, image, featured, badge, sort_order, status) VALUES
('Vencendo as Crises Espirituais', 'vencendo-as-crises-espirituais',
 '50 sermões expositivos nos Salmos 1 a 50',
 'Direcionamento bíblico para enfrentar crises espirituais com fé e esperança.',
 '<p>A Palavra de Deus é viva, eficaz e capaz de transformar corações. Entre os seus livros sagrados, os Salmos ocupam um lugar especial, pois expressam as mais profundas emoções da alma humana em sua busca por Deus. Do clamor angustiado à exultação jubilosa, da súplica pela misericórdia divina à celebração do Seu poder e fidelidade, os Salmos refletem a jornada espiritual daqueles que confiam no Senhor.</p><p>Este livro que você tem em mãos reúne cinquenta sermões expositivos, abrangendo os Salmos 1 a 50. A abordagem expositiva busca não apenas explicar o texto, mas aplicá-lo com fidelidade ao coração do leitor e ao contexto da igreja contemporânea. Cada sermão aqui apresentado foi elaborado com zelo, oração e profundo compromisso com a verdade bíblica, visando edificar pregadores, líderes e cristãos que desejam aprofundar-se no conhecimento das Escrituras.</p><p>A pregação expositiva dos Salmos é um convite à adoração e à confiança no Deus soberano, que reina sobre todas as coisas e se faz presente em cada detalhe da nossa vida. Meu desejo é que este livro sirva como um instrumento para fortalecer sua fé, inspirar sua pregação e reacender sua paixão pela Palavra de Deus.</p><p>Que cada sermão aqui registrado cumpra o propósito de proclamar as verdades eternas do Senhor e edificar a igreja de Cristo.</p>',
 40.00,
 'https://eliasjosedasilva.com.br/wp-new/wp-content/uploads/2026/06/Livro-Vencendo-as-Crises-Espirituais.jpg',
 1, 'Mais vendido', 1, 'published'),
('Motivação que Vem do Alto', 'motivacao-que-vem-do-alto',
 'Motivação pessoal fundamentada em princípios bíblicos',
 'Motivação pessoal fundamentada em princípios bíblicos.',
 '<p>“Motivação que Vem do Alto” é um livro que revela como a verdadeira motivação encontra sua origem em Deus. A obra explora como, ao nos conectarmos com o propósito divino e confiarmos na força que Ele nos dá, podemos viver com um propósito renovado e enfrentar qualquer desafio com fé.</p><p>A motivação não vem de circunstâncias externas, mas de um relacionamento profundo com o Senhor, que nos capacita e nos fortalece. Com base em princípios bíblicos, o autor nos convida a descobrir a motivação que transcende os limites humanos, nos direcionando a viver com coragem, alegria e um coração transformado.</p><p>Este livro é uma jornada espiritual que inspira o leitor a buscar sua força no Alto e a viver uma vida cheia de significado e propósito.</p>',
 30.00,
 'https://eliasjosedasilva.com.br/wp-new/wp-content/uploads/2026/06/Livro-Motivacao-que-Vem-do-Alto.jpg',
 1, NULL, 2, 'published'),
('Defesa Racional da Fé', 'defesa-racional-da-fe',
 'Apologética cristã para os desafios do nosso tempo',
 'Apologética cristã com argumentos racionais para a defesa da fé.',
 '<p>Em um mundo saturado de dúvidas, relativismos e discursos fragmentados, <em>Há um Deus</em> surge como um manifesto de convicção e esperança. Mais do que um tratado teológico, este livro é um convite à reflexão profunda, à defesa inteligente da fé e ao encontro transformador com a Verdade que tem nome e rosto: Jesus Cristo.</p><p>Inspirado no pensamento de gigantes como C. S. Lewis, William Lane Craig, Ravi Zacharias, Alvin Plantinga, Francis Schaeffer, Tim Keller e outros, este volume reúne 30 capítulos densos, acessíveis e apaixonados, que apresentam razões sólidas para crer, dialogam com os grandes dilemas da contemporaneidade e apontam para a glória de Deus como fim supremo de toda razão e fé.</p><p>Com linguagem clara, argumentos robustos e sensibilidade pastoral, <em>Há um Deus</em> é leitura indispensável para quem deseja firmar-se na fé cristã, dialogar com sabedoria em uma cultura hostil ao evangelho, e viver com a mente cativa a Cristo.</p><p><strong>Porque a verdade não é uma ideia abstrata.<br>A verdade é uma Pessoa.<br>E Seu nome é Jesus.</strong></p>',
 40.00,
 'https://eliasjosedasilva.com.br/wp-new/wp-content/uploads/2026/06/Livro-Defesa-Racional-da-Fe.jpg',
 1, NULL, 3, 'published'),
('O Mestre dos Mestres', 'o-mestre-dos-mestres',
 'Lições do maior Mestre que já pisou nesta terra',
 'Uma jornada pelas lições de Jesus, o Mestre dos Mestres.',
 '<p>Em breve mais informações sobre esta obra. Garanta o seu exemplar entrando em contato pelo WhatsApp.</p>',
 40.00,
 'https://eliasjosedasilva.com.br/wp-new/wp-content/uploads/2026/06/Livro-O-Mestre-dos-Mestres.jpg',
 0, 'Lançamento', 4, 'draft');

-- Depoimentos iniciais (o Pr. Elias pode editar/excluir no painel)
INSERT INTO testimonials (author, author_role, content, rating, sort_order) VALUES
('Leitor da obra', 'Pregador', 'Os sermões expositivos nos Salmos têm sido um instrumento precioso na preparação das minhas mensagens. Conteúdo fiel à Palavra e de grande edificação.', 5, 1),
('Leitora da obra', 'Professora de EBD', 'Leitura que fortalece a fé e aquece o coração. Recomendo a todos que desejam crescer no conhecimento das Escrituras.', 5, 2),
('Leitor da obra', 'Líder de jovens', 'A Defesa Racional da Fé me equipou para dialogar com convicção e amor sobre a esperança que há em nós.', 5, 3);

-- Páginas institucionais (LGPD)
INSERT INTO pages (title, slug, content, status) VALUES
('Política de Privacidade', 'politica-de-privacidade',
'<h2>1. Compromisso com a sua privacidade</h2><p>Este site, mantido pelo ministério do Pr. Elias José da Silva, respeita a sua privacidade e trata os seus dados pessoais em conformidade com a Lei Geral de Proteção de Dados Pessoais — LGPD (Lei nº 13.709/2018).</p><h2>2. Quais dados coletamos</h2><p>Coletamos apenas os dados necessários para: (a) processar pedidos da loja (nome, e-mail, telefone, CPF e endereço de entrega); (b) responder mensagens de contato; (c) enviar devocionais por e-mail, quando você se inscreve voluntariamente.</p><h2>3. Como usamos os dados</h2><p>Os dados são utilizados exclusivamente para as finalidades informadas no momento da coleta. Não vendemos, alugamos ou compartilhamos seus dados com terceiros para fins de marketing.</p><h2>4. Pagamentos</h2><p>Os pagamentos são processados por gateways certificados (Mercado Pago, PagBank, PayPal, PIX). Os dados do seu cartão são tratados diretamente pelo gateway — este site não armazena dados de cartão de crédito.</p><h2>5. Cookies</h2><p>Utilizamos cookies essenciais (necessários ao funcionamento do site, como o carrinho de compras) e, mediante o seu consentimento, cookies de estatísticas. Você pode aceitar ou recusar os cookies não essenciais no banner exibido na primeira visita e alterar sua escolha a qualquer momento no rodapé do site.</p><h2>6. Seus direitos (LGPD)</h2><p>Você pode solicitar, a qualquer momento: confirmação do tratamento, acesso, correção, anonimização ou exclusão dos seus dados, bem como a revogação de consentimento. Basta entrar em contato pelo e-mail <strong>contato@eliasjosedasilva.com.br</strong> ou pelo WhatsApp <strong>(11) 93206-5151</strong>.</p><h2>7. Segurança</h2><p>Adotamos medidas técnicas e administrativas para proteger seus dados: conexão criptografada (HTTPS/SSL), acesso restrito ao banco de dados e armazenamento seguro de senhas.</p><h2>8. Encarregado pelo tratamento</h2><p>Controlador: Elias José da Silva — contato: contato@eliasjosedasilva.com.br.</p>',
'published'),
('Termos e Condições', 'termos-e-condicoes',
'<h2>1. Sobre a loja</h2><p>A loja deste site comercializa livros de autoria do Pr. Elias José da Silva, com envio para todo o Brasil.</p><h2>2. Preços e pagamento</h2><p>Os preços exibidos são em reais (BRL). O pagamento é processado por gateways seguros e certificados. O pedido é confirmado após a aprovação do pagamento.</p><h2>3. Entrega</h2><p>O prazo de entrega é informado após a confirmação do pedido, de acordo com o CEP de destino. O código de rastreio é enviado por e-mail ou WhatsApp.</p><h2>4. Garantia de satisfação — 7 dias</h2><p>Em conformidade com o Código de Defesa do Consumidor (art. 49), você pode desistir da compra em até 7 (sete) dias corridos após o recebimento do produto, com devolução integral do valor pago.</p><h2>5. Trocas e devoluções</h2><p>Produtos com defeito ou avaria de transporte são trocados sem custo. Entre em contato pelo WhatsApp (11) 93206-5151 em até 7 dias após o recebimento.</p><h2>6. Atendimento</h2><p>Atendimento de 08h às 18h, todos os dias, exclusivamente pelo WhatsApp (11) 93206-5151 ou pelo e-mail contato@eliasjosedasilva.com.br.</p>',
'published'),
('Sobre o Pastor', 'sobre-o-pr',
'<p><strong>Elias José da Silva</strong> é Educador Cristão, Pastor Evangélico e Escritor, dedicando sua vida ao ensino das Escrituras, à formação de líderes e ao fortalecimento da fé cristã por meio da pregação, do discipulado e da produção literária.</p><p>Com sólida formação acadêmica e ampla experiência ministerial, tem se destacado na integração entre conhecimento teológico, desenvolvimento humano e liderança cristã, contribuindo para a edificação da Igreja e para a formação de uma cosmovisão cristã bíblica e relevante para os desafios contemporâneos.</p><h2>Formação Acadêmica</h2><h3>Pós-Graduações (Lato Sensu)</h3><ul><li>Gestão de Pessoas</li><li>Comunicação e Marketing</li><li>Direito do Trabalho Individual e Coletivo</li><li>MBA Executivo em Consultoria e Planejamento Empresarial</li><li>Filosofia e Ensino da Filosofia</li><li>Ciências da Religião</li></ul><h3>Graduações</h3><ul><li>Bacharel em Ciências Contábeis</li><li>Bacharel Livre em Teologia</li></ul><h2>Ministério e Ensino</h2><p>Ao longo de sua trajetória, tem atuado no pastoreio, ensino bíblico e treinamentos ministeriais, com especial dedicação à formação de obreiros, professores da Escola Bíblica Dominical, líderes cristãos e pregadores da Palavra de Deus.</p><p>Seu ministério é marcado pelo compromisso com a fidelidade bíblica, pela valorização da educação cristã e pela defesa da fé em uma sociedade cada vez mais desafiadora para os princípios do Evangelho.</p><h2>Produção Literária</h2><p>É autor de obras voltadas ao crescimento espiritual, apologética cristã e desenvolvimento da vida cristã: <em>Vencendo as Crises Espirituais</em>, <em>Motivação que Vem do Alto</em>, <em>Defesa Racional da Fé</em> e <em>O Mestre dos Mestres</em>.</p><p>Seus livros têm como objetivo fortalecer a fé, promover o conhecimento das Escrituras e incentivar uma caminhada cristã madura, equilibrada e comprometida com Cristo.</p><h2>Missão</h2><blockquote><p><em>“Servir a Deus por meio do ensino, da pregação e da formação de pessoas, contribuindo para que vidas sejam transformadas pela verdade do Evangelho e edificadas para cumprir o propósito divino.”</em></p></blockquote>',
'published');

-- Agenda (exemplos — o Pr. Elias pode editar/excluir no painel)
INSERT INTO events (title, description, location, city, event_date, event_time, status) VALUES
('Culto de Ensino da Palavra', 'Exposição bíblica com o Pr. Elias José da Silva.', 'A confirmar', 'São Paulo - SP', '2026-07-19', '19:00', 'published'),
('Escola Bíblica Dominical', 'Ensino das Escrituras para todas as idades.', 'A confirmar', 'São Paulo - SP', '2026-07-26', '09:00', 'published');
