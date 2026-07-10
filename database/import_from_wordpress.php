<?php
/**
 * IMPORTADOR — copia sermões, devocionais e livros do WordPress antigo
 * para as tabelas do novo site.
 *
 * COMO USAR (uma única vez):
 *   1. Importe primeiro o database/schema.sql pelo phpMyAdmin (cria as tabelas).
 *   2. Preencha config/config.php com os dados do banco do NOVO site.
 *   3. Preencha abaixo os dados do banco do WordPress (veja wp-config.php:
 *      DB_NAME, DB_USER, DB_PASSWORD, DB_HOST e $table_prefix).
 *   4. Rode pelo navegador: https://seusite.com.br/database/import_from_wordpress.php
 *      (ou pelo terminal: php database/import_from_wordpress.php)
 *   5. Confira o resultado e APAGUE este arquivo do servidor por segurança.
 *
 * É seguro rodar mais de uma vez: usa o slug como chave e atualiza sem duplicar.
 */

// ===================== CONFIGURE O BANCO DO WORDPRESS =====================
$WP = [
    'host'   => 'localhost',
    'name'   => 'NOME_DO_BANCO_WORDPRESS',   // DB_NAME do wp-config.php
    'user'   => 'USUARIO_WORDPRESS',         // DB_USER
    'pass'   => 'SENHA_WORDPRESS',           // DB_PASSWORD
    'prefix' => 'wpbt_',                      // $table_prefix (geralmente wp_)
];
// =========================================================================

require __DIR__ . '/../app/Core/helpers.php';
define('BASE_PATH', dirname(__DIR__));

header('Content-Type: text/plain; charset=utf-8');
$cli = (php_sapi_name() === 'cli');
function out(string $m): void { echo $m . "\n"; }

// --- Conexões PDO ---
try {
    $new = require __DIR__ . '/../config/config.php';
    $dbNew = new PDO(
        "mysql:host={$new['db']['host']};dbname={$new['db']['name']};charset=utf8mb4",
        $new['db']['user'], $new['db']['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $dbWp = new PDO(
        "mysql:host={$WP['host']};dbname={$WP['name']};charset=utf8mb4",
        $WP['user'], $WP['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    out('ERRO de conexão: ' . $e->getMessage());
    out('Verifique os dados do banco (WordPress e novo site).');
    exit(1);
}
$p = $WP['prefix'];

/** Converte o post_content do WordPress em HTML limpo. */
function wp_to_html(string $raw): string
{
    // Remove comentários de bloco do Gutenberg/Elementor
    $raw = preg_replace('/<!--\s*\/?wp:.*?-->/s', '', $raw);
    $raw = trim($raw);
    if ($raw === '') return '';

    // Já tem tags de bloco? mantém como está (sanitizado)
    if (preg_match('/<(p|h[1-6]|ul|ol|blockquote|div)\b/i', $raw)) {
        return clean_html($raw);
    }
    // Texto puro: divide em parágrafos por linhas em branco
    $blocks = preg_split('/(\r\n|\n){2,}/', $raw);
    $html = '';
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') continue;
        $block = nl2br(clean_html($block));
        $html .= '<p>' . $block . "</p>\n";
    }
    return $html;
}

/** Insere ou atualiza pelo slug. */
function upsert(PDO $db, string $table, array $data): string
{
    $slug = $data['slug'];
    $exists = $db->prepare("SELECT id FROM {$table} WHERE slug = ?");
    $exists->execute([$slug]);
    $id = $exists->fetchColumn();

    if ($id) {
        $set = implode(', ', array_map(fn($k) => "`$k` = :$k", array_keys($data)));
        $data['id'] = $id;
        $db->prepare("UPDATE {$table} SET {$set} WHERE id = :id")->execute($data);
        return 'atualizado';
    }
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
    $vals = implode(', ', array_map(fn($k) => ":$k", array_keys($data)));
    $db->prepare("INSERT INTO {$table} ({$cols}) VALUES ({$vals})")->execute($data);
    return 'inserido';
}

/** URL da imagem destacada de um post. */
function featured_image(PDO $dbWp, string $p, int $postId): ?string
{
    $q = $dbWp->prepare(
        "SELECT g.guid FROM {$p}postmeta m
         JOIN {$p}posts g ON g.ID = m.meta_value
         WHERE m.post_id = ? AND m.meta_key = '_thumbnail_id' LIMIT 1"
    );
    $q->execute([$postId]);
    return $q->fetchColumn() ?: null;
}

$counts = ['sermao' => 0, 'devocional' => 0, 'product' => 0];

// ---------------- SERMÕES ----------------
$rows = $dbWp->query(
    "SELECT ID, post_title, post_name, post_excerpt, post_content, post_date
     FROM {$p}posts WHERE post_type = 'sermao' AND post_status = 'publish'
     ORDER BY post_date"
)->fetchAll();
foreach ($rows as $r) {
    $content = wp_to_html($r['post_content']);
    if ($content === '') continue;
    $slug = $r['post_name'] ?: slugify($r['post_title']);
    $res = upsert($dbNew, 'sermons', [
        'title'        => $r['post_title'],
        'slug'         => $slug,
        'excerpt'      => trim($r['post_excerpt']) ?: excerpt_of($content, 180),
        'content'      => $content,
        'image'        => featured_image($dbWp, $p, (int) $r['ID']),
        'status'       => 'published',
        'published_at' => $r['post_date'],
    ]);
    $counts['sermao']++;
    out("Sermão [{$res}]: {$r['post_title']}");
}

// ---------------- DEVOCIONAIS ----------------
$rows = $dbWp->query(
    "SELECT ID, post_title, post_name, post_excerpt, post_content, post_date
     FROM {$p}posts WHERE post_type = 'devocional' AND post_status = 'publish'
     ORDER BY post_date"
)->fetchAll();
foreach ($rows as $r) {
    $content = wp_to_html($r['post_content']);
    if ($content === '') continue;
    $slug = $r['post_name'] ?: slugify($r['post_title']);
    $res = upsert($dbNew, 'devotionals', [
        'title'        => $r['post_title'],
        'slug'         => $slug,
        'excerpt'      => trim($r['post_excerpt']) ?: excerpt_of($content, 180),
        'content'      => $content,
        'image'        => featured_image($dbWp, $p, (int) $r['ID']),
        'status'       => 'published',
        'published_at' => $r['post_date'],
    ]);
    $counts['devocional']++;
    out("Devocional [{$res}]: {$r['post_title']}");
}

// ---------------- LIVROS (produtos WooCommerce) ----------------
$rows = $dbWp->query(
    "SELECT ID, post_title, post_name, post_excerpt, post_content
     FROM {$p}posts WHERE post_type = 'product' AND post_status = 'publish'
     ORDER BY ID"
)->fetchAll();
$order = 10;
foreach ($rows as $r) {
    $slug  = $r['post_name'] ?: slugify($r['post_title']);
    $price = $dbWp->prepare("SELECT meta_value FROM {$p}postmeta WHERE post_id = ? AND meta_key = '_price' LIMIT 1");
    $price->execute([$r['ID']]);
    $priceVal = (float) ($price->fetchColumn() ?: 0);

    $res = upsert($dbNew, 'books', [
        'title'       => $r['post_title'],
        'slug'        => $slug,
        'excerpt'     => trim($r['post_excerpt']) ?: null,
        'description' => wp_to_html($r['post_content']),
        'price'       => $priceVal,
        'image'       => featured_image($dbWp, $p, (int) $r['ID']),
        'in_stock'    => 1,
        'status'      => 'published',
        'sort_order'  => $order++,
    ]);
    $counts['product']++;
    out("Livro [{$res}]: {$r['post_title']}");
}

out('');
out('======================================================');
out("CONCLUÍDO: {$counts['sermao']} sermões, {$counts['devocional']} devocionais, {$counts['product']} livros.");
out('APAGUE este arquivo (import_from_wordpress.php) do servidor agora, por segurança.');
out('======================================================');
