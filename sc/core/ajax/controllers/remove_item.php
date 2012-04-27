<?php
/**
 * Removes an item from the customer's cart
 *
 * @package Cart
 */
namespace Ajax;
/**
 * Ajax Call To Remove Item
 *
 * *Ajax Controller Function* Reoves a specific line item. Echos
 * "1" to let SC know it worked
 *
 * @package Cart
 *
 * @return null
 *
 * @param int|string $item_num The line to remove or the imploded string version
 * of the item.
 *
 */
function remove_item($item_num) {
    global $SC;

    $SC->load_library('Cart');

    $SC->Cart->remove_item($SC->Session->get_open_transaction(),$item_num);

    echo '1';
    
}
