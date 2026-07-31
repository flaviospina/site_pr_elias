<?php
namespace App\Core;

use App\Models\Setting;

/** Controller base: renderização de views e utilitários comuns. */
abstract class Controller
{
    /** Renderiza uma view dentro do layout público. */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        $data['settings'] = $data['settings'] ?? Setting::all();
        extract($data, EXTR_SKIP);
        $viewFile   = BASE_PATH . '/app/Views/' . $view . '.php';
        $layoutFile = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
        self::ensureFile($viewFile, "app/Views/{$view}.php");
        self::ensureFile($layoutFile, "app/Views/layouts/{$layout}.php");
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require $layoutFile;
    }

    /** Aborta com mensagem clara se um arquivo de view não existir (upload incompleto). */
    private static function ensureFile(string $path, string $relative): void
    {
        if (!is_file($path)) {
            http_response_code(500);
            $msg = 'Arquivo não encontrado no servidor: ' . $relative
                . '. Reenvie este arquivo por FTP para a hospedagem.';
            if (config('env') === 'development') {
                exit($msg);
            }
            error_log('[EJDS] ' . $msg);
            exit('Erro: um arquivo do site não foi encontrado (' . $relative . '). Reenvie-o por FTP.');
        }
    }

    /** Renderiza uma view sem layout (parciais/ajax). */
    protected function partial(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require BASE_PATH . '/app/Views/' . $view . '.php';
    }

    /** Resposta JSON. */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** Valida o token CSRF em requisições POST; aborta se inválido. */
    protected function requireCsrf(): void
    {
        if (!Csrf::validate($_POST['_token'] ?? '')) {
            http_response_code(419);
            exit('Sessão expirada. Volte e tente novamente.');
        }
    }
}
