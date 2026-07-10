# Site do Pr. Elias José da Silva

Site institucional e loja de livros do Pr. Elias José da Silva, construído em **PHP puro (PDO) com arquitetura MVC** — sem WordPress, sem framework externo, pronto para rodar em qualquer hospedagem compartilhada com PHP 8+ e MySQL.

Inclui: página inicial voltada à conversão de vendas, loja com carrinho e checkout, páginas de sermões e devocionais (com busca), agenda, contato via WhatsApp, banner de cookies (LGPD), páginas de Política de Privacidade e Termos, e um **painel administrativo completo** onde o Pr. Elias controla todo o conteúdo do site.

---

## 1. Requisitos

- PHP **8.0 ou superior** (testado no 8.3/8.4) com extensões `pdo_mysql` e `curl`
- MySQL 5.7+ ou MariaDB 10.3+
- Apache com `mod_rewrite` (padrão na maioria das hospedagens com cPanel)

## 2. Instalação (passo a passo)

### Passo 1 — Enviar os arquivos
Envie todo o conteúdo deste projeto para a hospedagem. Recomendado:
- Aponte o domínio para a pasta **`public/`** (Document Root = `.../public`).
- Se não for possível mudar o Document Root, envie tudo para `public_html/` — o arquivo `.htaccess` da raiz já redireciona para `public/` automaticamente.

### Passo 2 — Criar o banco de dados
No cPanel → **Bancos de Dados MySQL**:
1. Crie um banco (ex.: `elias_site`).
2. Crie um usuário e uma senha.
3. Associe o usuário ao banco com **todos os privilégios**.

### Passo 3 — Importar as tabelas e o conteúdo
No **phpMyAdmin**, selecione o banco criado e importe **nesta ordem**:
1. `database/schema.sql` — cria as tabelas e já insere os 4 livros, páginas, configurações e o usuário admin.
2. `database/seeds/sermoes.sql` — insere os 8 sermões.
3. `database/seeds/devocionais.sql` — insere os 67 devocionais.

> Alternativa automática: em vez dos arquivos `seeds/`, você pode rodar `database/import_from_wordpress.php` uma única vez para copiar sermões, devocionais e livros direto do WordPress antigo. Veja as instruções dentro do próprio arquivo. **Apague-o do servidor depois de usar.**

### Passo 4 — Configurar a conexão
Edite **`config/config.php`** e preencha os dados do banco (host, nome, usuário, senha).
Deixe `'base_url' => ''` para detecção automática e mantenha `'env' => 'production'`.

### Passo 5 — Pronto!
Acesse o site pelo domínio. Deve carregar na primeira tentativa. 🎉

---

## 3. Acesso ao Painel Administrativo

- Endereço: **`https://seudominio.com.br/admin`**
- E-mail: **admin@eliasjosedasilva.com.br**
- Senha: **PrElias@2026**

> ⚠️ **Troque a senha no primeiro acesso**: Painel → Usuários → Editar.

No painel o Pr. Elias tem **controle total** para adicionar, editar e **excluir**:
- **Livros** (com capa, preço, promoção, descrição e galeria)
- **Sermões** e **Devocionais**
- **Agenda** de eventos
- **Depoimentos** de leitores
- **Páginas** (Sobre, Política de Privacidade, Termos)
- **Pedidos** da loja (com atualização de status e contato por WhatsApp)
- **Mensagens** de contato e **inscritos** nos devocionais
- **Usuários** do painel
- **Configurações** gerais do site
- **Gateways de pagamento**

## 4. Gateways de pagamento (Brasil)

Em **Painel → Pagamentos**, o Pr. Elias ativa e configura as credenciais de:
- **Mercado Pago** (cartão, PIX e boleto — checkout seguro)
- **PIX direto** (chave própria, sem taxas de gateway)
- **PagBank / PagSeguro**
- **PayPal**
- **Combinar pelo WhatsApp** (sempre disponível como alternativa)

Cada forma só aparece no checkout quando ativada e com as credenciais preenchidas. Comece pelo **modo sandbox** para testar.

## 5. Segurança

- Todas as consultas usam **PDO com prepared statements** (proteção contra SQL Injection).
- Toda saída dinâmica é escapada (proteção **XSS**).
- Formulários protegidos por **token CSRF**.
- Senhas com **bcrypt**; login com **bloqueio por tentativas** (força bruta).
- Cabeçalhos de segurança (X-Frame-Options, nosniff, etc.) e cookies `HttpOnly`.
- A loja **não armazena dados de cartão** — o pagamento é concluído no ambiente do gateway.
- Force HTTPS descomentando o bloco no `public/.htaccess` após confirmar o SSL.

## 6. Estrutura do projeto (MVC)

```
config/            Configuração (banco, ambiente)
database/          schema.sql, seeds/ e importador do WordPress
app/
  Core/            Router, Controller, Database (PDO), Auth, Cart, Csrf, Payment, helpers
  Models/          Acesso a dados (PDO) — Book, Sermon, Devotional, Order, ...
  Controllers/     Lógica do site público e do painel (Admin/)
  Views/           Templates (layouts, páginas públicas e do admin)
public/            Raiz web — index.php (front controller), .htaccess, assets/, uploads/
```

## 7. Dados de exemplo incluídos

4 livros, 8 sermões, 67 devocionais, 2 eventos e 3 depoimentos já vêm carregados. Tudo pode ser editado ou removido pelo painel.
