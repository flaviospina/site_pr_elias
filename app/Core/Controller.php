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
        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
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
