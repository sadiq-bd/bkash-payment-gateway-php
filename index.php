<?php
use SADIQ_SOFT\BkashAPI;
require_once __DIR__ . '/config.php';
session_start();

// token genarate & refresh
require_once __DIR__. '/token.php';

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
        <input type="number" name="amount" id="amountInput" step="0.01" placeholder="Amount" required><br>
        <input type="text" name="ref" id="refInput" placeholder="Reference" required><br>
        <button type="submit">Pay with Bkash</button>
    </form>
</center>
</body>
</html>