<?php
use SADIQ_SOFT\BkashAPI;
require_once __DIR__ . '/config.php';
session_start();

header('Content-Type: application/json');

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
if (empty(($resp = $bkash->setGrantToken($token)->executePayment($paymentID))->jsonObj()->errorCode)) {
    
    $query = $bkash->queryPayment($paymentID);

    // log execute payment resp
    prependFileLog(log_file, "\n\n- Execute Payment\n{$resp->response()}\n\n");


    // log query payment resp
    prependFileLog(log_file, "\n\n- Query Payment\n{$query->response()}\n\n");


    
    if (@strtolower($query->jsonObj()->transactionStatus) === 'completed') {
        header('Location: /success.html');
        exit;
    }
    
    echo json_encode([
        'paymentID' => $paymentID,
        'paymentStatus' => $status,
        'transactionStatus' => $query->jsonObj()->transactionStatus

    ]);

} else {

    echo json_encode(['status' => 'error']);
    
}


