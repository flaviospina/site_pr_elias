<?php
/**
 * Entrada do site quando o domínio (ou subpasta, ex.: /site_new) aponta para a
 * RAIZ do projeto. Carrega o front controller real em public/.
 * Assim o site funciona mesmo em servidores com regras de rewrite restritas.
 */

// Sinaliza que a pasta public/ NÃO é o docroot: os links de css/js/uploads
// passam a usar o caminho real "public/...", que dispensa qualquer rewrite.
define('PUBLIC_VIA_ROOT', true);

require __DIR__ . '/public/index.php';
