<?php

/**
 * Processes the cart and displays it to the user for verification
 *
 * @package checkout
 */

$this->load_library(array('Cart','Stock','Session','Discounts','Shipping','Page_loading'));
//Verify that account information is complete first
$customer = $this->Customer->get_customer($this->Session->get_user());

$required_fields = array(
    'firstname',
    'lastname',
    'streetaddress',
    'city',
    'state',
    'postalcode',
    'country',
    'phone'
);

foreach ($required_fields as $required_field) {
    $ship_field = 'ship_'.$required_field;
    $bill_field = 'bill_'.$required_field;
    if (!$customer->$ship_field or !$customer->$bill_field) {
        $_POST['new_customer'] = TRUE;
        $this->load_ajax('get_customer_details');
        exit();
    }
} 

$transaction = $this->Transactions->get_transaction($this->Session->get_open_transaction());

$messages = array();

//Return orders to stock that have been pending too long
$stale_orders = $this->Transactions->get_stale_transactions();

foreach ($stale_orders as $stale_order) {
    $this->Transactions->update_transaction($stale_order->id,array(
        'status' => 'opened'
    ));
    $this->Stock->return_cart($stale_order->id);
}

//Verify that the customer's items are still in stock
$cart = $this->Cart->explode_cart($transaction->items);

$out_of_stock_flag = FALSE;

foreach ($cart as $key => $item) {
    if (!$this->Stock->item_in_stock($item['id'])) {
        $out_of_stock_flag = TRUE;
        unset($cart[$key]);
        continue;
    }
    
    foreach ($item['options'] as $opt_key => $option) {
        if (!$this->Stock->option_in_stock($option['id'])) {
            $out_of_stock_flag = TRUE;
            unset($cart[$key]);
        }
    }
}

$transaction->items = $this->Cart->implode_cart($cart,$transaction->id);

if ($out_of_stock_flag) {
    $messages[] = 'Unfortunately some of the items in your cart were out of stock. These items have been removed.';
    //Check if there's even still items in the cart
    
}

//Remove the items from stock. Set order to pending
if ($transaction->status == "opened") {
    $this->Stock->pull_cart($cart);
    $this->Transactions->update_transaction($transaction->id,array(
        'status' => 'pending'
    ));
}

//Shipping
$shipping_method = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : $transaction->shipping_method;
$shipping_required = $this->Cart->shipping_required($cart);

//Discounts
$discount_code = isset($_POST['discount']) ? $_POST['discount'] : $transaction->discount;

//Calculate totals
$order_totals = $this->Cart->calculate_total($transaction,$shipping_method,$discount_code);

$messages += $order_totals['messages'];

$this->Transactions->update_transaction($transaction->id,array(
    'shipping' => $order_totals['shipping'],
    'shipping_method' => $shipping_method,
    'taxrate' => $order_totals['taxrate'],
    'discount' => $order_totals['discount'],
    'items' => $this->Cart->implode_cart($order_totals['items'])
));

// If there's a default message, add it to the begining of the messages array.
if ($def_message = $this->Config->get_setting('defaultcheckoutmessage')) {
    array_unshift($messages,$def_message);
}


