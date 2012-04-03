<?php

$this->load_library(array('Cart','Stock','Session'));
$transaction = $this->Session->get_open_transaction();

//Return orders to stock that have been pending too long
$stale_orders = $this->Transactions->get_stale_transactions();

foreach ($stale_orders as $stale_order) {
    $this->Transactions->update_transaction($stale_order->id,array(
        'status' => 'opened'
    ));
    $this->Stock->return_cart($stale_order->id);
}

//Verify that the customer's items are still in stock
$cart = $this->Cart->explode_cart($transaction);

$out_of_stock_flag = FALSE;

foreach ($cart as $key => $item) {
    if (!$this->Stock->item_in_stock($item['id'])) {
        $out_of_stock_flag = TRUE;
        unset($cart[$key]);
        continue;
    }
    
    foreach ($item['options'] as $opt_key => $option) {
        if ($this->Stock->get_option_stock($option['id']) <= 0) {
            $out_of_stock_flag = TRUE;
            unset($cart[$key][$opt_key]);
        }
    }
}

$this->Cart->implode_cart($cart,$transaction);

//Remove the items from stock. Set order to pending
$this->Stock->pull_cart($transaction);
$this->Transaction->update_transaction($transaction,array(
    'status' => 'pending'
));

//Check for discounts

//Calculate shipping

//Calculate tax

//Calculate total and insert all of this into the database

//Display messages and final totals
