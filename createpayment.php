<?php
use SADIQ_SOFT\BkashAPI;
require_once __DIR__ . '/config.php';
session_start();

// token genarate & refresh
require_once __DIR__. '/token.php';

header('Content-Type: application/json');

$requires = [
    'amount', 'ref'
];
foreach ($requires as $req) {
    if (empty($_POST[$req])) {
        exit(json_encode(['message' => $req.' is missing']));
    }
}


$token = $_SESSION['token'];

$amount = $_POST['amount'];

$invoice = uniqid('INV_');

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


