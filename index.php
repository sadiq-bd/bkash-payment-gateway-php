<?php
use SADIQ_SOFT\BkashAPI;
require_once __DIR__ . '/config.php';
session_start();

if (empty($_SESSION['token'])) {
    $bkash = new BkashAPI;
    if (empty($_SESSION['token_refresh'])) {
        $token = $bkash->grantToken();
    } else {
        $token = $bkash->refreshToken($_SESSION['token_refresh']);
    }
    if (!$token->getErrorInfo()) {
        
        if (empty($token->getGrantToken())) {
            print_r($token->json());
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
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant</title>
    <style>
        input, button {
            min-width: 300px;
            margin: 3px;
            padding: 5px;
        }
    </style>
</head>
<body>
<br><br>
<center>
    <h2>Bkash Payment Gateway</h2>
    <form action="./createpayment.php" method="post">
        <input type="number" name="amount" id="amountInput" value="" placeholder="Amount"><br>
        <input type="text" name="ref" id="refInput" value="" placeholder="Reference"><br>
        <button type="submit">Pay with Bkash</button>
    </form>
</center>
</body>
</html>