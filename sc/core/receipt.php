<?php

/**
 * Page that will generate and output the customer's receipt
 *
 * @package Display
 *
 */
 
require_once 'init.php';

$SC->load_library(array('Cart','Session','URI'));

$transaction = $SC->URI->get_request();
$messages = $SC->URI->get_data();

$t = $SC->Transactions->get_transaction($transaction,'*','ordernumber');

$cart = $SC->Cart->explode_cart($t->items);

if (!$t) {
    die('This order does not exist');
}

//TODO: Login to see order

if ($t->custid != $SC->Session->get_customer()) {
    die('You do not have permission to view this order');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Order Number <?=$t->ordernumber?>. Thank you for your purchase!</title>
        <link rel="stylesheet" href="<?=sc_asset('css','receipt')?>" />
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

<?php require_once('receipt_body.php'); ?>

        </div><!--#sc_receipt-->
    </body>
</html>



