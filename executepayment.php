<?php
use SADIQ_SOFT\BkashAPI;
require_once __DIR__ . '/config.php';
session_start();

if (empty($_SESSION['token'])) {
    echo json_encode(['message' => 'Access token not granted yet']);
    die;
}

if (empty($_GET['paymentID'])) {
    echo json_encode(['message' => 'paymentID missing']);
    die;
}

if (empty($_GET['status'])) {
    echo json_encode(['message' => 'status missing']);
    die;
}

$token = $_SESSION['token'];
$paymentID = $_GET['paymentID'];
$status = $_GET['status'];


$bkash = new BkashAPI;
if ($resp = $bkash->setGrantToken($token)->executePayment($paymentID)) {
    $query = $bkash->queryPayment($paymentID);

    
    // log execute payment resp
    prependFileLog(log_file, "\n\n- Execute Payment\n{$resp->response()}\n\n");

    header('Location: /success.html');

} else {

    echo json_encode(['status' => 'error']);
    
}


