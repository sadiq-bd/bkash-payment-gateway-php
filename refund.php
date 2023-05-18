<?php
use SADIQ_SOFT\BkashAPI;
require_once __DIR__ . '/config.php';
session_start();

// token genarate & refresh
require_once __DIR__. '/token.php';

// refund form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requires = [
        'paymentID', 'amount', 'trxID', 'sku', 'reason'
    ];
    foreach ($requires as $req) {
        if (empty($_POST[$req])) {
            exit(json_encode(['message' => $req.' is missing']));
        }
    }

    $bkash = new BkashAPI;
    if ($refund = $bkash
        ->setGrantToken($_SESSION['token'])
        ->refundPayment(
            $_POST['paymentID'],
            $_POST['amount'],
            $_POST['trxID'],
            $_POST['sku'],
            $_POST['reason']    
        )
    ) {

        
        // log refund
        prependFileLog(log_file, "\n\n- Refund Payment\n{$refund->response()}\n\n");

        header('Content-type: application/json');
        exit($refund->response());
        
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund</title>
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
    <h2>Bkash Payment Refund</h2>
    <form action="./refund.php" method="post">
        <input type="text" name="paymentID" placeholder="Payment ID" required><br>
        <input type="number" name="amount" id="amountInput" step="0.01" placeholder="Amount" required><br>
        <input type="text" name="trxID" placeholder="Transaction ID" required><br>
        <input type="text" name="sku" placeholder="SKU" required><br>
        <input type="text" name="reason" placeholder="Reason" required><br>
        <button type="submit">Refund</button>
    </form>
</center>
</body>
</html>