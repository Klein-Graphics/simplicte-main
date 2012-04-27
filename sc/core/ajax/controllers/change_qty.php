<?php
/**
 * Changes the quantity of an item in the cart
 *
 * @package Cart
 */
namespace Ajax;
/**
 * Ajax Call To Change Quantity
 *
 * *Ajax Controller Function* Changes the quantity of a specific line item. Echos
 * "1" to let SC know it worked
 *
 * @package Cart
 *
 * @return null
 *
 * @param int $line The cart line to change
 * @param float $quantity The quantity to set
 *
 */
function change_qty($line,$quantity) {

    global $SC;
    
    $SC->load_library('Cart');
    
    $cart = $SC->Cart->explode_cart($SC->Session->get_open_transaction());
    
    $cart[$line]['quantity'] = ($min = $SC->Items->item_flag($cart[$line]['id'],'min')) ? $min[1] : $quantity;
    
    $SC->Cart->implode_cart($cart,$SC->Session->get_open_Transaction());
    
    echo '1';
       
}
