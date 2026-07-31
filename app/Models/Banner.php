<?php
namespace App\Models;

use App\Core\Database;

class Banner
{
    /** Locais (páginas) que possuem banner no topo. */
    public static function slots(): array
    {
        return [
            'home'       => 'Página Inicial',
            'livros'     => 'Livros',
            'sermoes'    => 'Sermões',
            'devocionais'=> 'Devocionais',
            'agenda'     => 'Agenda',
            'contato'    => 'Contato',
            'sobre-o-pr' => 'Sobre o Pastor',
        ];
    }

    private static array $cache = [];

    /** Retorna a config do banner de um local, ou null. Seguro se a tabela não existir. */
    public static function get(string $location): ?array
    {
        if (array_key_exists($location, self::$cache)) {
            return self::$cache[$location];
        }
        try {
            $row = Database::run('SELECT * FROM banners WHERE location = ?', [$location])->fetch();
        } catch (\Throwable) {
            $row = false; // tabela ainda não criada → usa padrões
        }
        return self::$cache[$location] = ($row ?: null);
    }

    /** Lista para o admin: cada slot com seus dados (ou vazio). */
    public static function allForAdmin(): array
    {
        $saved = [];
        try {
            foreach (Database::run('SELECT * FROM banners')->fetchAll() as $r) {
                $saved[$r['location']] = $r;
            }
        } catch (\Throwable) {
            // tabela ausente
        }
        $list = [];
        foreach (self::slots() as $loc => $label) {
            $list[] = ['location' => $loc, 'label' => $label, 'data' => $saved[$loc] ?? null];
        }
        return $list;
    }

    public static function save(string $location, array $data): void
    {
        $fields = ['enabled','image','title','subtitle','button_text','button_url',
                   'button2_text','button2_url','overlay','text_color','align','height'];
        $params = ['location' => $location];
        foreach ($fields as $f) $params[$f] = $data[$f] ?? null;

        $exists = Database::run('SELECT id FROM banners WHERE location = ?', [$location])->fetch();
        if ($exists) {
            $set = implode(', ', array_map(fn($f) => "`$f` = :$f", $fields));
            Database::run("UPDATE banners SET $set WHERE location = :location", $params);
        } else {
            $cols = implode(', ', array_map(fn($f) => "`$f`", array_merge(['location'], $fields)));
            $vals = implode(', ', array_map(fn($f) => ":$f", array_merge(['location'], $fields)));
            Database::run("INSERT INTO banners ($cols) VALUES ($vals)", $params);
        }
        self::$cache = [];
    }
}
