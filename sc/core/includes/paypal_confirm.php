<?php

include ('Curl.php');
include ('../init.php');
            
$token = $_GET['token'];

$curl = new \Curl();

$SC->load_library('Gateways');

$data = array(
    'METHOD' => 'GetExpressCheckoutDetails',
    'VERSION' => '88.0',
    'USER' => $SC->Config->get_setting('paypaluser'),
    'PWD' => $SC->Config->get_setting('paypalpwd'),
    'SIGNATURE' => $SC->Config->get_setting('paypalsignature'),
    'TOKEN' => $token        
);

$result = $SC->Gateways->Drivers->Paypal_EC->paypal_api_call($data);

$result_errors = '';
if (! $result_success = ($result['ACK'] == 'Success')) {
    foreach ($result['errors'] as $error) {
            $result_errors .= "{$error['L_SEVERITYCODE']}: {$error['L_ERRORCODE']} {$error['L_SHORTMESSAGE']}. {$error['L_LONGMESSAGE']} <br />";
    }  
}

$t = \Model\Transaction::find_by_ordernumber($result['PAYMENTREQUEST_0_INVNUM']);
$cart = $SC->Cart->explode_cart($t->items);

$relay_url = $SC->Gateways->Drivers->Paypal_EC->outgoing_relay_url
            .(int) $SC->Config->get_setting('site_live')
        .'/'.urlencode(urlencode(encrypt($data['USER'])))  
        .'/'.urlencode(urlencode(encrypt($data['PWD'])))        
        .'/'.urlencode(urlencode(encrypt($data['SIGNATURE'])));
        
$encrypt_json = encrypt(json_encode($result));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Confirm Order Number <?=$t->ordernumber?></title>
    </head>
    <body>
        <div id="sc_receipt">
            <div id="sc_receipt_head">
                <h2>Please confirm your purchase</h2>
            </div><!--#sc_receipt_head-->

<?php require_once('core/receipt_body.php'); ?>
            <form action="<?=$relay_url?>" method="post">
                <input type="hidden" name="scd" value="<?=$encrypt_json?>" />
                <input type="submit" class="sc_paypal_confirm" value="Confirm Order"/>
            </form>
        </div><!--#sc_receipt-->
    </body>
</html>
