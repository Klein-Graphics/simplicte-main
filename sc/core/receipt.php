<?php

/**
 * Page that will generate and output the customer's receipt
 *
 * @package Display
 *
 * @todo
 */
 
require_once 'init.php';

$SC->load_library(array('Cart','Session','URI'));

$transaction = $SC->URI->get_request();
$messages = $SC->URI->get_data();

$t = $SC->Transactions->get_transaction($transaction,'*','ordernumber');

$order_date = substr($t->ordernumber,2,2).
              '/'.substr($t->ordernumber,4,2).
              '/'.substr($t->ordernumber,0,2);

if (!$t) {
    die('This order does not exist');
}

if ($t->custid != $SC->Session->get_customer()) {
    die('You do not have permission to view this order');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Order Number <?=$t->ordernumber?>. Thank you for your purchase!</title>
    </head>
    <body>
        <div id="sc_receipt">
            <div id="sc_receipt_head">
                <h2>Thank you for your purchase!</h2>
                <p><strong>Please check your email for your purchase confirmation</strong></p>
<?php foreach ($messages as $message) : ?>
                    <div class="sc_receipt_message"><?=urldecode($message)?></div>                          
<?php endforeach ?>
                <p><a href="<?=site_url()?>" title="Return">Back to store</a></p>
            </div><!--#sc_receipt_head-->
            <div id="sc_receipt_body">
                <div id="sc_tidbits">
                    <div id="date">Order Date: <?=$order_date?></div>
                    <div id="invoicenumber">Invoice #<?=$t->ordernumber?></div>
                </div><!--#sc_tidbits-->
                <div id="sc_customer_info">
                    <div id="sc_shipping_info">
                        <h4>Shipping</h4>
                        <?=$t->ship_firstname?> <?=$t->ship_initial?> <?=$t->ship_lastname?><br />
                        <?=$t->ship_streetaddress?> <?=($t->ship_apt) ? "#{$t->ship_apt}" : ''?><br />
                        <?=$t->ship_city?>, <?=$t->ship_state?> <?=$t->ship_postalcode?><br />
                        <?=$t->ship_country?><br /><br />
                        <?=$t->ship_phone?>                
                    </div><!--#sc_shipping_info-->
                    <div id="sc_billing_info">
                        <h4>Billing</h4>
                        <?=$t->bill_firstname?> <?=$t->bill_initial?> <?=$t->bill_lastname?><br />
                        <?=$t->bill_streetaddress?> <?=($t->bill_apt) ? "#{$t->bill_apt}" : ''?><br />
                        <?=$t->bill_city?>, <?=$t->ship_state?> <?=$t->bill_postalcode?><br />
                        <?=$t->bill_country?><br /><br />
                        <?=$t->bill_phone?>  
                    </div><!--#sc_billing_info-->
                </div><!--#sc_customer_info-->
            </div><!--#sc_receipt_body-->
        </div><!--#sc_receipt-->
    </body>
</html>


