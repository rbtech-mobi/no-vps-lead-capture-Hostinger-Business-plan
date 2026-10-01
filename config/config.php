<?php
/**
 * ============================================================================
 * GREEN RIVER CRO ENGINE - CONFIGURAÇÃO SEGURA (O COFRE)
 * ============================================================================
 * 
 * ⚠️ AVISO CRÍTICO DE SEGURANÇA:
 * 1. Este arquivo reside fora da pasta pública (`public_html`).
 * 2. Isso impede que qualquer visitante ou bot acesse suas variáveis, mesmo
 *    em caso de falha de parsing do PHP pelo servidor web (Hostinger / Apache / LiteSpeed).
 * 3. Configure aqui o e-mail de destino dos leads e as credenciais de envio.
 * ============================================================================
 */

// Impede qualquer execução direta via web ou CLI desnecessária
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    http_response_code(403);
    exit('Direct access forbidden.');
}

// 1. Destinatário principal dos leads de contratistas
define('ADMIN_EMAIL', 'leads@greenriver.mobi');

// 2. Remetente do sistema (deve usar domínio hospedado na Hostinger para evitar spam)
define('MAIL_FROM', 'no-reply@greenriver.mobi');
define('MAIL_FROM_NAME', 'Green River Lead Dispatcher');

// 3. Configurações de Ambiente
define('APP_NAME', 'GreenRiver-Contractor-SMS');
define('APP_ENV', 'production'); // 'production' ou 'development'
define('APP_TIMEZONE', 'America/New_York');

// Define timezone padrão
date_default_timezone_set(APP_TIMEZONE);
