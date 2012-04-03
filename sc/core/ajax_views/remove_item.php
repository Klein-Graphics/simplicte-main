<?php
namespace View;
function remove_item($item_num) {
    global $SC;

    $SC->load_library('Cart');

    $SC->Cart->remove_item($SC->Session->get_open_transaction(),$item_num);

    echo '1';
    
}
