<?php

if (defined('SIMPLECART_IS_IN_CP')) {
    return;
}

$cust_id = $SC->Session->get_user();

$pricing_db = new PDO('mysql:host=localhost;dbname=carefast','root','',array(
    PDO::ATTR_PERSISTENT => true
));

$customer_level = $pricing_db->query("SELECT `level` FROM `customer_level` WHERE `cust_id` = $cust_id");
if ($customer_level) {
    $customer_level = $customer_level->fetch();
    $customer_level = $customer_level['level'];
} else {
    $customer_level = 'customer';
}


$SC->hooks['db']['Item']['get_price'][] = function($item) use ($customer_level, $pricing_db) {    
    $discount_level = $pricing_db->query("SELECT `amount` FROM `cust_price_adjust` WHERE `level` = '$customer_level' AND `itemid` = {$item->id}");
    
    if ($discount_level) {
        $discount_level = $discount_level->fetch();
        $discount_level = $discount_level['amount'];                
    } else {
        $discount_level = 0;
    }
    
    $price = $item->read_attribute('price');
    
    $price += $discount_level;
    
    return number_format($price,2);
};
