<?php 
/**
 * Outgoing Relay
 *
 * Calls the relay function of a specific payment gateway. To make this
 * as compatible as possible, this must be done without actually
 * initializing simplecart
 *
 * @package Checkout
 */
 
$sc_dir = dirname(__DIR__);
$sc_dir = str_replace('\\','/',$sc_dir);
chdir($sc_dir);

$query = $_SERVER['QUERY_STRING'];      
$query = preg_replace('/^\//','',$query);            

$query = explode('/',$query);

$request = array_shift($query);

$session_id = array_shift($query);
$data = $query;

require_once "core/global.php";
require_once 'config.php';
require_once 'core/includes/Curl.php';
if (! $CONFIG['SC_LOCATION']) {
  $CONFIG['SC_LOCATION'] = substr($sc_dir,strrpos($sc_dir,'/')+1);        
}

require_once "core/libraries/gateway_drivers/Gateway_Driver.php";

if (!file_exists("core/libraries/gateway_drivers/$request.php")) {
    header('HTTP/1.0 404 Not Found');
    echo('<h1>404</h1>Page not found');
    exit;   
}
require_once "core/libraries/gateway_drivers/$request.php";

session_id($session_id);
session_start();
define('INCOMING_RELAY_URL', sc_location('core/incoming_relay.php?'.session_name().'='.session_id()));
session_write_close();

$relay_result = call_user_func_array("Gateway_Driver\\$request::relay",$data);
$json_result = json_decode($relay_result,true);

if ($json_result === NULL) {
    $transaction_result['status_code'] = 0;
    $transaction_result['status_text'] = $relay_result;
} else {
    $transaction_result = $json_result;
}



if ($transaction_result['status_code']) {
    //Redirect to the receipt 
    $receipt_location = sc_location('receipt/'.$transaction_result['transaction'].'/'.rawurlencode($transaction_result['status_text']));
    echo "<script type='text/javascript'>
            window.location='$receipt_location'
          </script>
          <a href='$receipt_location' title='Return'><strong>Return to store</strong></a>";
        
} else { ?>
<div style="width: 500px; margin: 100px auto; background-color: #ddd; text-align: center; padding: 10px;">
    <h2>There was an issue with your transaction</h2>
    <p><strong><?=$transaction_result['status_text']?></strong></p>
    <p>Your transaction has been saved. <a href="<?=site_url()?>" title="Return">Return to Store</a></p>
</div>
<?php }
