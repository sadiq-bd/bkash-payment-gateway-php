<?php
use SADIQ_SOFT\BkashAPI;

if (basename($_SERVER['SCRIPT_FILENAME']) == 'token.php') {
    header('HTTP/1.1 404 Not Found');
    exit;
}

require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

while (1) {
    
    if (empty($_SESSION['token'])) {
        $bkash = new BkashAPI;
        if (empty($_SESSION['token_refresh'])) {
            $token = $bkash->grantToken();
        } else {
            $token = $bkash->refreshToken($_SESSION['token_refresh']);
        }
        if (strlen($token->getErrorInfo()) < 1) {
            
            if (empty($token->getGrantToken())) {
                header('Content-Type: application/json');
                print_r($token->response());
                exit;
            }
            
            $_SESSION['token'] = $token->getGrantToken();
            $_SESSION['token_refresh'] = $token->jsonObj()->refresh_token;
            $_SESSION['token_expiration'] = time() + $token->jsonObj()->expires_in;
        }
    }

    // refresh if token expired
    if (time() > $_SESSION['token_expiration']) {
        
        unset($_SESSION['token']);
        
        continue;
    }

    break;

}
