<?php
/**
 * Entrada do site quando o domínio (ou subpasta, ex.: /site_new) aponta para a
 * RAIZ do projeto. Carrega o front controller real em public/.
 * Assim o site funciona mesmo em servidores com regras de rewrite restritas.
 */
require __DIR__ . '/public/index.php';
