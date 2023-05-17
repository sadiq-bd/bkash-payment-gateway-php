<?php
use SADIQ_SOFT\BkashAPI;
require_once __DIR__ . '/config.php';
session_start();
if (empty($_SESSION['token'])) {
    echo json_encode(['message' => 'Access token not granted yet']);
    die;
}
// refresh if token expired
if (time() > $_SESSION['token_expiration']) {
    unset($_SESSION['token']);
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (empty($_POST['amount'])) {
    echo json_encode(['message' => 'Invalid amount']);
    die;
}

if (empty($_POST['ref'])) {
    echo json_encode(['message' => 'reference is empty']);
    die;
}


$token = $_SESSION['token'];
$amount = $_POST['amount'];
$invoice = uniqid('INVOICE_');
$reference = $_POST['ref'];

$bkash = new BkashAPI;
if ($resp = $bkash->setGrantToken($token)->createPayment($amount, $invoice, $reference)) {
    if (!empty($resp->jsonObj()->bkashURL)) {


        // log create payment resp
        prependFileLog(log_file, "\n\n- Create Payment\n{$resp->response()}\n\n");

        header('Location: ' . $resp->jsonObj()->bkashURL);
        exit;
    } else {
        print_r($resp->json());
        echo json_encode(['status' => 'error']);
        die;
    }
}


