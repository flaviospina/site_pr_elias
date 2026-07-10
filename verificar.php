<?php
/**
 * VERIFICADOR DE INSTALAÇÃO — Site Pr. Elias
 *
 * Acesse pelo navegador: https://seusite.com.br/site_new/verificar.php
 * Ele mostra, em português, tudo o que está certo (✅) e errado (❌) no servidor.
 *
 * ⚠️ APAGUE ESTE ARQUIVO depois que o site estiver funcionando.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8');

$root = __DIR__;
$ok = '✅'; $bad = '❌'; $warn = '⚠️';
$rows = [];
$fatal = false;

function row(&$rows, string $status, string $item, string $detail = ''): void
{
    $rows[] = [$status, $item, $detail];
}

/* 1. Versão do PHP e extensões */
row($rows, version_compare(PHP_VERSION, '8.0', '>=') ? $ok : $bad,
    'PHP ' . PHP_VERSION,
    version_compare(PHP_VERSION, '8.0', '>=') ? 'Compatível (mínimo 8.0)' : 'INCOMPATÍVEL — mude a versão do PHP para 8.1+ no cPanel (Select PHP Version)');
if (version_compare(PHP_VERSION, '8.0', '<')) $fatal = true;

foreach (['pdo_mysql' => 'Conexão com o banco', 'curl' => 'Integração Mercado Pago'] as $ext => $why) {
    row($rows, extension_loaded($ext) ? $ok : $bad, "Extensão $ext", extension_loaded($ext) ? $why : "FALTANDO — ative no cPanel (Select PHP Version > Extensions): $why");
}

/* 2. Arquivos essenciais */
$files = [
    'index.php'                       => 'Entrada do site (raiz)',
    '.htaccess'                       => 'Regras do servidor (raiz) — arquivo oculto, confira se subiu!',
    'public/index.php'                => 'Front controller',
    'public/.htaccess'                => 'Regras do servidor (public) — arquivo oculto',
    'public/assets/css/style.css'     => 'Folha de estilo (o visual do site)',
    'public/assets/js/main.js'        => 'Scripts (menu, cookies)',
    'config/config.php'               => 'Configuração do banco',
    'app/Core/helpers.php'            => 'Funções do sistema',
    'app/Core/Router.php'             => 'Roteador',
    'app/Core/Database.php'           => 'Conexão PDO',
    'app/Views/layouts/main.php'      => 'Layout do site',
    'app/Controllers/HomeController.php' => 'Página inicial',
];
foreach ($files as $f => $why) {
    $exists = is_file("$root/$f");
    $size = $exists ? filesize("$root/$f") : 0;
    row($rows, $exists && $size > 0 ? $ok : $bad, $f,
        $exists && $size > 0 ? "$why (" . number_format($size / 1024, 1, ',', '.') . ' KB)'
        : "NÃO ENCONTRADO ou vazio — reenvie este arquivo por FTP ($why)");
    if (!$exists && in_array($f, ['public/index.php', 'config/config.php', 'app/Core/helpers.php'])) $fatal = true;
}

/* 3. Sintaxe e funções dos arquivos-chave */
if (is_file("$root/app/Core/helpers.php")) {
    try {
        require_once "$root/app/Core/helpers.php";
        row($rows, $ok, 'helpers.php carrega sem erro', '');
        row($rows, function_exists('asset_url') ? $ok : $warn, 'Função asset_url()',
            function_exists('asset_url') ? 'Versão atual instalada'
            : 'Versão ANTIGA de app/Core/helpers.php no servidor — reenvie o arquivo atualizado');
        row($rows, function_exists('base_path') ? $ok : $warn, 'Função base_path()',
            function_exists('base_path') ? 'Versão atual instalada' : 'Versão ANTIGA — reenvie app/Core/helpers.php');
    } catch (\Throwable $e) {
        row($rows, $bad, 'helpers.php com ERRO', get_class($e) . ': ' . $e->getMessage() . ' (linha ' . $e->getLine() . ') — o upload pode ter corrompido o arquivo; reenvie-o');
        $fatal = true;
    }
}

/* 4. Configuração e banco de dados */
$cfg = null;
if (is_file("$root/config/config.php")) {
    try {
        $cfg = require "$root/config/config.php";
        $dbc = $cfg['db'] ?? [];
        $configured = ($dbc['pass'] ?? '') !== 'TROQUE_ESTA_SENHA' && ($dbc['pass'] ?? '') !== '';
        row($rows, $configured ? $ok : $bad, 'Dados do banco preenchidos',
            $configured ? 'host=' . ($dbc['host'] ?? '?') . ', banco=' . ($dbc['name'] ?? '?')
            : 'Edite config/config.php e preencha host, nome do banco, usuário e senha');
        if ($configured) {
            try {
                $pdo = new PDO(
                    'mysql:host=' . $dbc['host'] . ';dbname=' . $dbc['name'] . ';charset=utf8mb4',
                    $dbc['user'], $dbc['pass'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
                );
                row($rows, $ok, 'Conexão com o banco de dados', 'Conectado com sucesso');
                foreach (['settings', 'books', 'sermons', 'devotionals', 'users'] as $t) {
                    try {
                        $n = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
                        row($rows, $ok, "Tabela $t", "$n registro(s)");
                    } catch (\Throwable $e) {
                        row($rows, $bad, "Tabela $t", 'NÃO EXISTE — importe database/schema.sql (e os seeds) pelo phpMyAdmin');
                    }
                }
            } catch (\Throwable $e) {
                row($rows, $bad, 'Conexão com o banco de dados', 'FALHOU: ' . $e->getMessage() . ' — confira usuário/senha e se o usuário foi associado ao banco no cPanel');
            }
        }
        $bu = $cfg['base_url'] ?? '';
        row($rows, $bu !== '' ? $ok : $warn, 'base_url no config',
            $bu !== '' ? $bu : "Vazio (auto-detecção). Para subpasta, recomendo definir: 'base_url' => 'https://SEU-DOMINIO/site_new'");
    } catch (\Throwable $e) {
        row($rows, $bad, 'config/config.php com ERRO', $e->getMessage() . ' — o arquivo pode estar corrompido; corrija a sintaxe');
        $fatal = true;
    }
}

/* 5. Teste real da página inicial (o que o visitante vê) */
$homeTest = ['status' => $warn, 'msg' => 'Não testado'];
if (!$fatal && is_file("$root/public/index.php")) {
    $probe = @file_get_contents(
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] .
        rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/',
        false,
        stream_context_create(['http' => ['timeout' => 10, 'ignore_errors' => true]])
    );
    $code = 0;
    if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) $code = (int) $m[1];
    if ($code === 200 && $probe !== false && strpos($probe, '</html>') !== false) {
        $homeTest = ['status' => $ok, 'msg' => "Página inicial respondeu 200 e renderizou até o fim"];
    } elseif ($probe !== false && trim((string) $probe) === '') {
        $homeTest = ['status' => $bad, 'msg' => "Resposta em BRANCO (código $code) — erro fatal do PHP. Veja a seção 'Como ver o erro exato' abaixo"];
    } else {
        $homeTest = ['status' => $bad, 'msg' => "Código HTTP $code — veja os itens ❌ acima"];
    }
}
row($rows, $homeTest['status'], 'Teste da página inicial', $homeTest['msg']);

/* Saída */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="utf-8"><title>Verificador — Site Pr. Elias</title>
<style>
body{font-family:system-ui,Arial,sans-serif;max-width:900px;margin:30px auto;padding:0 16px;color:#1c2733;line-height:1.5}
h1{color:#0A2540}
table{border-collapse:collapse;width:100%;margin:18px 0}
td,th{border:1px solid #d9dee5;padding:9px 12px;text-align:left;vertical-align:top;font-size:14px}
th{background:#0A2540;color:#fff}
td:first-child{width:36px;text-align:center;font-size:17px}
.box{background:#fdf3e0;border:1px solid #e5cd96;border-radius:8px;padding:14px 18px;margin:18px 0;font-size:14px}
code{background:#eef1f5;padding:2px 6px;border-radius:4px}
</style></head>
<body>
<h1>Verificador de instalação</h1>
<p>Servidor: <code><?= htmlspecialchars(($_SERVER['SERVER_SOFTWARE'] ?? '?') . ' — ' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '')) ?></code></p>
<table>
<tr><th></th><th>Item</th><th>Situação</th></tr>
<?php foreach ($rows as [$s, $i, $d]): ?>
<tr><td><?= $s ?></td><td><strong><?= htmlspecialchars($i) ?></strong></td><td><?= htmlspecialchars($d) ?></td></tr>
<?php endforeach; ?>
</table>

<div class="box">
<strong>Como ver o erro exato (se a página estiver em branco):</strong><br>
Abra <code>config/config.php</code> e troque <code>'env' =&gt; 'production'</code> por <code>'env' =&gt; 'development'</code>.
Recarregue o site — o erro aparecerá na tela. Depois de corrigir, volte para <code>'production'</code>.
</div>

<div class="box" style="background:#fbeaea;border-color:#e2b4b4">
<strong>⚠️ Segurança:</strong> apague este arquivo (<code>verificar.php</code>) do servidor assim que o site estiver no ar.
</div>
</body>
</html>
