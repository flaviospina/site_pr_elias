<?php
namespace App\Core;

/**
 * Roteador simples: mapeia MÉTODO + caminho para [Controller, ação].
 * Suporta parâmetros nomeados: /livros/{slug}
 */
class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void  { $this->add('GET', $path, $handler); }
    public function post(string $path, array $handler): void { $this->add('POST', $path, $handler); }

    private function add(string $method, string $path, array $handler): void
    {
        $pattern = preg_replace('#\{([a-z_]+)\}#', '(?P<$1>[^/]+)', rtrim($path, '/') ?: '/');
        $this->routes[] = [
            'method'  => $method,
            'pattern' => '#^' . $pattern . '$#',
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = rawurldecode(parse_url($uri, PHP_URL_PATH) ?? '/');

        // Remove o prefixo base (mesma fonte usada para gerar os links), de modo
        // que rotas funcionem tanto na raiz quanto numa subpasta (ex.: /site_new).
        $basePath = base_path();
        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }
        // Também tolera o prefixo com "/public" (caso o link tenha sido gerado assim)
        if (str_starts_with($path, '/public/') || $path === '/public') {
            $path = substr($path, strlen('/public'));
        }
        $path = '/' . trim($path, '/');
        if ($path !== '/') $path = rtrim($path, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            if (preg_match($route['pattern'], $path, $matches)) {
                [$class, $action] = $route['handler'];
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $controller = new $class();
                call_user_func_array([$controller, $action], array_values($params));
                return;
            }
        }

        http_response_code(404);
        (new \App\Controllers\PageController())->notFound();
    }
}
