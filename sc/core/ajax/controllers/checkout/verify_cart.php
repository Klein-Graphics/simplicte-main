<?php

/**
 * Processes the cart and displays it to the user for verification
 *
 * @package checkout
 */

$this->load_library(array('Cart','Stock','Session','Discounts','Shipping','Page_loading'));
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
}

//Remove the items from stock. Set order to pending
if ($transaction->status == "opened") {
    $this->Stock->pull_cart($cart);
    $this->Transactions->update_transaction($transaction->id,array(
        'status' => 'pending'
    ));
}

$shipping_method = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : FALSE;
$discount_code = isset($_POST['discount']) ? $_POST['discount'] : FALSE;
$shipping_required = $this->Cart->shipping_required($cart);

$order_totals = $this->Cart->calculate_total($transaction,$shipping_method,$discount_code);

$messages += $order_totals['messages'];

$this->Transactions->update_transaction($transaction->id,array(
    'shipping' => $order_totals['shipping'],
    'taxrate' => $order_totals['taxrate'],
    'discount' => $order_totals['discount'],
    'items' => $this->Cart->implode_cart($order_totals['items'])
));
