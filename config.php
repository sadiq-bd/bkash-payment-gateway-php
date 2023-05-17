<?php
use SADIQ_SOFT\BkashAPI;

if (basename($_SERVER['SCRIPT_FILENAME']) == 'config.php') {
    header('HTTP/1.1 404 Not Found');
    exit;
}

require_once __DIR__ . '/src/BkashAPI.php';


########### EXECUTE URL ############
BkashAPI::setCallBackUrl('http://' . $_SERVER['HTTP_HOST'] . '/executepayment.php');


BkashAPI::setAppKey('4f6o0cjfg4f23tfdadl1eqq');
BkashAPI::setAppSecret('2is7hdktrekvrbljjh3w4qwtt43qt54y54mjvs5vl5qr3fug4b');
BkashAPI::setUsername('sandbox4903tui439User02');
BkashAPI::setPassword('sandboxi0tithrh4i234002@12345');


########### SANDBOX #################
BkashAPI::setApiBaseURL('https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/');


########### PRODUCTION ##############
# BkashAPI::setApiBaseURL('https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/');



########### Log FILE ################
define('log_file', 'gateway.log.txt');


function prependFileLog($fname, $msg) {
    $file = fopen($fname, 'r+');
    $msg = $msg . file_get_contents($fname);
    fwrite($file, $msg, strlen($msg));
    fclose($file);
}




