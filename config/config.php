<?php
/**
 * Configuração do site — Pr. Elias José da Silva
 *
 * >>> EDITE AS 4 LINHAS DO BANCO DE DADOS ABAIXO COM OS DADOS DA SUA HOSPEDAGEM <<<
 * (cPanel > Bancos de Dados MySQL). Depois importe database/schema.sql e os
 * arquivos de database/seeds/ pelo phpMyAdmin. Instruções completas no README.md.
 */

return [
    'db' => [
        'host'    => 'localhost',
        'name'    => 'elias_site',        // nome do banco criado no cPanel
        'user'    => 'elias_user',        // usuário do banco
        'pass'    => 'TROQUE_ESTA_SENHA', // senha do banco
        'charset' => 'utf8mb4',
    ],

    // URL base do site, SEM barra no final. Ex.: 'https://eliasjosedasilva.com.br'
    // Deixe '' (vazio) para detectar automaticamente.
    'base_url' => '',

    // Ambiente: 'production' oculta erros do PHP; 'development' exibe.
    'env' => 'production',

    // Chave secreta usada em tokens (troque por qualquer texto longo aleatório)
    'app_key' => 'troque-por-uma-chave-aleatoria-bem-longa-1234567890',

    // Nome do cookie de sessão
    'session_name' => 'ejds_session',
];
