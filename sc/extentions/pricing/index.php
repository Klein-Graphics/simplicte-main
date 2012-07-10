<?php

if (defined('SIMPLECART_IS_IN_CP')) {
    return;
}



$pricing_db = new PDO('mysql:host=localhost;dbname=carefast','root','',array(
    PDO::ATTR_PERSISTENT => true
));

function get_customer_level() {    
    global $pricing_db;
    global $SC;
    
    $cust_id = $SC->Session->get_user();
    
    if ($cust_id) {
    
        $customer_level = $pricing_db->query("SELECT `level` FROM `customer_level` WHERE `cust_id` = $cust_id");

        $customer_level = $customer_level->fetch();
        return $customer_level['level'];
    
    } else {
        return false;    
    }
}




$SC->hooks['db']['Item']['get_price'][] = function($item) use ($pricing_db) {    
    $customer_level = get_customer_level();

    if ($customer_level) {
        $discount_level = $pricing_db->query("SELECT `amount` FROM `cust_price_adjust` WHERE `level` = '$customer_level' AND `itemid` = {$item->id}");    
        $discount_level = $discount_level->fetch();         
        $discount_level = $discount_level['amount'];    
        if ($discount_level) {             
            $price = $discount_level;
                            
        } else {        
            $price = $item->read_attribute('price');
        }        
    } else {
        $price = $item->read_attribute('price');   
    }
    return number_format($price,2);
};

$SC->hooks['ajax_controller']['do_login']['before'] = function() {
    ob_start();
};

$SC->hooks['ajax_controller']['do_login']['after'] = function() {    
    if (get_customer_level()) {
        ob_end_clean();
        echo json_encode(array(
            'do_this'=>'display_good',
            'message'=>'<script type="text/javascript">window.location.reload()</script>'
        ));
    } else {
        ob_end_flush();
    }
};
