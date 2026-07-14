<?php
/**
 * Front controller — todas as requisições passam por aqui.
 * Site do Pr. Elias José da Silva | PHP 8+ | PDO | MVC
 */

define('BASE_PATH', dirname(__DIR__));

// Sob o servidor embutido do PHP (php -S), serve arquivos estáticos existentes
// diretamente. No Apache/produção o .htaccess já faz isso, então isto é inócuo.
if (PHP_SAPI === 'cli-server') {
    $requested = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($requested !== __DIR__ . '/' && is_file($requested)) {
        return false;
    }
}

require BASE_PATH . '/app/Core/helpers.php';

// Rede de segurança: garante asset_url() mesmo que uma versão antiga de
// helpers.php ainda esteja no servidor (evita tela branca por função ausente).
if (!function_exists('asset_url')) {
    function asset_url(string $path): string
    {
        $prefix = defined('PUBLIC_VIA_ROOT') ? 'public/' : '';
        return base_url($prefix . ltrim($path, '/'));
    }
}

// Autoloader PSR-4 simples: App\ → app/
spl_autoload_register(function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file)) require $file;
    }
});

// Erros: ocultos em produção
if (config('env') === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED);
}

// Cabeçalhos de segurança
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// Sessão endurecida
session_name(config('session_name', 'ejds_session'));
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Rotas
$router = new App\Core\Router();

// --- Site público ---
$router->get('/',                 [App\Controllers\HomeController::class, 'index']);
$router->get('/sobre-o-pr',       [App\Controllers\PageController::class, 'about']);
$router->get('/livros',           [App\Controllers\BookController::class, 'index']);
$router->get('/livros/{slug}',    [App\Controllers\BookController::class, 'show']);
$router->get('/sermoes',          [App\Controllers\SermonController::class, 'index']);
$router->get('/sermoes/{slug}',   [App\Controllers\SermonController::class, 'show']);
$router->get('/devocionais',        [App\Controllers\DevotionalController::class, 'index']);
$router->get('/devocionais/{slug}', [App\Controllers\DevotionalController::class, 'show']);
$router->post('/devocionais/inscrever', [App\Controllers\DevotionalController::class, 'subscribe']);
$router->get('/agenda',           [App\Controllers\AgendaController::class, 'index']);
$router->get('/contato',          [App\Controllers\ContactController::class, 'index']);
$router->post('/contato',         [App\Controllers\ContactController::class, 'send']);

// --- Loja ---
$router->get('/carrinho',              [App\Controllers\CartController::class, 'index']);
$router->post('/carrinho/adicionar',   [App\Controllers\CartController::class, 'add']);
$router->post('/carrinho/atualizar',   [App\Controllers\CartController::class, 'update']);
$router->post('/carrinho/remover',     [App\Controllers\CartController::class, 'remove']);
$router->get('/finalizar-compra',      [App\Controllers\CheckoutController::class, 'index']);
$router->post('/finalizar-compra',     [App\Controllers\CheckoutController::class, 'place']);
$router->get('/pedido/{code}',         [App\Controllers\CheckoutController::class, 'confirmation']);
$router->get('/webhook/mercadopago',   [App\Controllers\CheckoutController::class, 'mpWebhook']);
$router->post('/webhook/mercadopago',  [App\Controllers\CheckoutController::class, 'mpWebhook']);

// --- Páginas institucionais (LGPD) ---
$router->get('/pagina/{slug}', [App\Controllers\PageController::class, 'show']);
$router->get('/politica-de-privacidade', [App\Controllers\PageController::class, 'privacy']);
$router->get('/termos-e-condicoes',      [App\Controllers\PageController::class, 'terms']);

// --- Painel administrativo ---
$router->get('/admin',        [App\Controllers\Admin\DashboardController::class, 'index']);
$router->get('/admin/login',  [App\Controllers\Admin\AuthController::class, 'form']);
$router->post('/admin/login', [App\Controllers\Admin\AuthController::class, 'login']);
$router->post('/admin/logout',[App\Controllers\Admin\AuthController::class, 'logout']);

// CRUDs do painel: /admin/{recurso}, .../novo, .../{id}/editar, .../salvar, .../{id}/excluir
foreach ([
    'livros'       => App\Controllers\Admin\BookController::class,
    'sermoes'      => App\Controllers\Admin\SermonController::class,
    'devocionais'  => App\Controllers\Admin\DevotionalController::class,
    'agenda'       => App\Controllers\Admin\EventController::class,
    'depoimentos'  => App\Controllers\Admin\TestimonialController::class,
    'paginas'      => App\Controllers\Admin\PageController::class,
    'usuarios'     => App\Controllers\Admin\UserController::class,
] as $resource => $controller) {
    $router->get("/admin/{$resource}",               [$controller, 'index']);
    $router->get("/admin/{$resource}/novo",          [$controller, 'create']);
    $router->get("/admin/{$resource}/{id}/editar",   [$controller, 'edit']);
    $router->post("/admin/{$resource}/salvar",       [$controller, 'save']);
    $router->post("/admin/{$resource}/{id}/excluir", [$controller, 'delete']);
}

$router->get('/admin/pedidos',                [App\Controllers\Admin\OrderController::class, 'index']);
$router->get('/admin/pedidos/{id}',           [App\Controllers\Admin\OrderController::class, 'show']);
$router->post('/admin/pedidos/{id}/status',   [App\Controllers\Admin\OrderController::class, 'updateStatus']);
$router->post('/admin/pedidos/{id}/verificar-mp', [App\Controllers\Admin\OrderController::class, 'verifyMp']);
$router->post('/admin/pedidos/{id}/excluir',  [App\Controllers\Admin\OrderController::class, 'delete']);
$router->get('/admin/mensagens',              [App\Controllers\Admin\MessageController::class, 'index']);
$router->post('/admin/mensagens/{id}/lida',   [App\Controllers\Admin\MessageController::class, 'markRead']);
$router->post('/admin/mensagens/{id}/excluir',[App\Controllers\Admin\MessageController::class, 'delete']);
$router->get('/admin/inscritos',              [App\Controllers\Admin\MessageController::class, 'subscribers']);
$router->post('/admin/inscritos/{id}/excluir',[App\Controllers\Admin\MessageController::class, 'deleteSubscriber']);
$router->get('/admin/configuracoes',          [App\Controllers\Admin\SettingController::class, 'general']);
$router->post('/admin/configuracoes',         [App\Controllers\Admin\SettingController::class, 'saveGeneral']);
$router->get('/admin/pagamentos',             [App\Controllers\Admin\SettingController::class, 'payments']);
$router->post('/admin/pagamentos',            [App\Controllers\Admin\SettingController::class, 'savePayments']);
$router->get('/admin/notificacoes',           [App\Controllers\Admin\SettingController::class, 'notifications']);
$router->post('/admin/notificacoes',          [App\Controllers\Admin\SettingController::class, 'saveNotifications']);
$router->post('/admin/upload',                [App\Controllers\Admin\SettingController::class, 'upload']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'] ?? '/');
