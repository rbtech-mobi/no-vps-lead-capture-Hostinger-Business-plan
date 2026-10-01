<?php
/**
 * ============================================================================
 * GREEN RIVER LEAD DISPATCH ENGINE - PHP BACKEND PROCESSOR
 * ============================================================================
 * 
 * Optimized for Hostinger Business Shared Hosting.
 * Pure PHP, zero external dependencies, no database required.
 * 
 * Architecture:
 * - Resides inside `public_html/` as the form processing endpoint.
 * - Loads credentials and environment rules from `../config/config.php`.
 * - Redirects to `/en/success.html` or `/es/success.html`.
 * ============================================================================
 */

// 1. CARREGAR CONFIGURAÇÃO SEGURA (FORA DO WEBROOT)
$configFile = dirname(__DIR__) . '/config/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
} else {
    // Fallbacks de segurança caso o arquivo de config seja movido
    defined('ADMIN_EMAIL') || define('ADMIN_EMAIL', 'leads@greenriver.mobi');
    defined('MAIL_FROM') || define('MAIL_FROM', 'no-reply@greenriver.mobi');
    defined('MAIL_FROM_NAME') || define('MAIL_FROM_NAME', 'Green River Lead Dispatcher');
}

// 2. ACEITAR APENAS MÉTODO POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    header('Location: en/');
    exit('405 Method Not Allowed. POST requests only.');
}

// 3. ARMADILHA ANTI-SPAM (HONEYPOT)
// Usuários reais nunca visualizam nem preenchem este campo oculto via CSS
$honeypot = trim($_POST['company_website'] ?? '');
if (!empty($honeypot)) {
    // Aborta silenciosamente o spam e simula sucesso para enganar os robôs
    $botLang = strtolower(trim($_POST['language'] ?? 'en'));
    $botRedirect = ($botLang === 'es') ? 'es/success.html' : 'en/success.html';
    header("Location: {$botRedirect}", true, 303);
    exit;
}

// 4. FUNÇÕES DE SANITIZAÇÃO NATIVA
function clean_string($data) {
    if (is_array($data)) {
        return '';
    }
    // Remove null bytes, tags HTML e converte caracteres especiais
    $data = str_replace(chr(0), '', (string)$data);
    $data = strip_tags(trim($data));
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function clean_header_field($data) {
    // Previne Email Header Injection removendo quebras de linha e retornos de carro
    return preg_replace('/[\r\n\t]+/', ' ', clean_string($data));
}

// 5. EXTRAÇÃO E HIGIENIZAÇÃO DAS ENTRADAS
$rawName     = $_POST['name'] ?? '';
$rawPhone    = $_POST['phone'] ?? '';
$rawTrade    = $_POST['trade'] ?? '';
$rawInterest = $_POST['interest'] ?? '';
$rawLanguage = $_POST['language'] ?? 'en';
$rawTcpa     = $_POST['tcpa_consent'] ?? 'no';

// Higienização
$cleanName     = clean_header_field($rawName);
$cleanPhone    = preg_replace('/[^0-9+() -]/', '', trim((string)$rawPhone));
$cleanTrade    = clean_string($rawTrade);
$cleanInterest = clean_string($rawInterest);
$cleanTcpa     = ($rawTcpa === 'yes') ? 'AGREED (Affirmative Checkbox Marked)' : 'NOT PROVIDED';

// Validação de Idioma ('en' ou 'es')
$language = strtolower(trim((string)$rawLanguage));
if ($language !== 'es') {
    $language = 'en';
}

// Fallbacks para campos vazios
if (empty($cleanName)) {
    $cleanName = 'Anonymous Contractor';
}
if (empty($cleanPhone)) {
    $cleanPhone = 'Not Provided';
}
if (empty($cleanTrade)) {
    $cleanTrade = 'General / Not Specified';
}
if (empty($cleanInterest)) {
    $cleanInterest = 'Missed-Call Automated Text-Back';
}

// Metadados de Auditoria e Rastreio
$timestamp   = date('Y-m-d H:i:s T');
$ipAddress   = clean_header_field($_SERVER['REMOTE_ADDR'] ?? 'Unknown');
$userAgent   = clean_string($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');
$referrer    = clean_string($_SERVER['HTTP_REFERER'] ?? 'Direct Visit');

// 6. COMPILAÇÃO DO CORPO DO E-MAIL (TEXTO PURO)
$emailSubject = clean_header_field("[HOT LEAD - {$cleanTrade}] Call Request from {$cleanName}");

$emailBody = "======================================================\n";
$emailBody .= "   NEW CONTRACTOR LEAD - MISSED-CALL AUTOMATION\n";
$emailBody .= "======================================================\n\n";

$emailBody .= "CONTACT DETAILS:\n";
$emailBody .= "------------------------------------------------------\n";
$emailBody .= "Name:            {$cleanName}\n";
$emailBody .= "Phone Number:    {$cleanPhone}\n";
$emailBody .= "Trade / Niche:   {$cleanTrade}\n";
$emailBody .= "Primary Interest:{$cleanInterest}\n";
$emailBody .= "Language Used:   " . strtoupper($language) . "\n";
$emailBody .= "TCPA Consent:    {$cleanTcpa}\n\n";

$emailBody .= "TECHNICAL & AUDIT TRACE:\n";
$emailBody .= "------------------------------------------------------\n";
$emailBody .= "Timestamp:       {$timestamp}\n";
$emailBody .= "IP Address:      {$ipAddress}\n";
$emailBody .= "Referrer URL:    {$referrer}\n";
$emailBody .= "User Agent:      {$userAgent}\n\n";

$emailBody .= "======================================================\n";
$emailBody .= "ACTION REQUIRED:\n";
$emailBody .= "Call this contractor promptly to understand how their trade business\n";
$emailBody .= "operates before discussing pricing or solutions.\n";
$emailBody .= "======================================================\n";

// 7. PREPARAÇÃO DOS CABEÇALHOS DO E-MAIL
$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'Content-Transfer-Encoding: 8bit';
$headers[] = 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>';
$headers[] = 'Reply-To: ' . MAIL_FROM;
$headers[] = 'X-Mailer: PHP/' . phpversion();
$headers[] = 'X-Priority: 1 (Highest)';
$headersString = implode("\r\n", $headers);

// 8. DISPARO COM PHP mail() NATIVO
$adminEmail = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'leads@greenriver.mobi';
$mailFrom = defined('MAIL_FROM') ? MAIL_FROM : 'no-reply@greenriver.mobi';

$mailSent = @mail($adminEmail, $emailSubject, $emailBody, $headersString, '-f' . $mailFrom);
if (!$mailSent) {
    @mail($adminEmail, $emailSubject, $emailBody, $headersString);
}

// 9. ROTEAMENTO DINÂMICO BASEADO NO IDIOMA
$redirectUrl = ($language === 'es') ? 'es/success.html' : 'en/success.html';

// Suporte para requisições assíncronas (AJAX / Fetch)
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
          || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($isAjax) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'status'      => 'success',
        'redirect'    => $redirectUrl,
        'language'    => $language,
        'lead'        => [
            'name'  => $cleanName,
            'phone' => $cleanPhone,
            'trade' => $cleanTrade
        ]
    ]);
    exit;
}

// Redirecionamento 303 (See Other) para formulário padrão do navegador
header("Location: {$redirectUrl}", true, 303);
exit;
