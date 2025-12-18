<?php
// config/sendgrid.php
require_once __DIR__ . '/env_loader.php';

class SendGridConfig {
    private static $apiKey = null;
    private static $fromEmail = null;
    private static $mpesaTill = null;
    private static $templateId = null;
    
    public static function getApiKey() {
        if (self::$apiKey === null) {
            self::$apiKey = getenv('SENDGRID_API_KEY') ?: '';
        }
        return self::$apiKey;
    }
    
    public static function getFromEmail() {
        if (self::$fromEmail === null) {
            self::$fromEmail = getenv('SYSTEM_EMAIL') ?: 'machariamathew254@gmail.com';
        }
        return self::$fromEmail;
    }
    
    public static function getMpesaTill() {
        if (self::$mpesaTill === null) {
            self::$mpesaTill = getenv('MPESA_TILL_NUMBER') ?: '514960';
        }
        return self::$mpesaTill;
    }
    
    public static function getTemplateId() {
        if (self::$templateId === null) {
            self::$templateId = getenv('SENDGRID_TEMPLATE_ID') ?: '';
        }
        return self::$templateId;
    }
}
?>

