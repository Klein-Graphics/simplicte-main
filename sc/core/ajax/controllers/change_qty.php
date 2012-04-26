<?php
    namespace View;
    function change_qty($line,$quantity) {
    
        global $SC;
        
        $SC->load_library('Cart');
        
        $cart = $SC->Cart->explode_cart($SC->Session->get_open_transaction());
        
        $cart[$line]['quantity'] = ($min = $SC->Items->item_flag($cart[$line]['id'],'min')) ? $min[1] : $quantity;
        
        $SC->Cart->implode_cart($cart,$SC->Session->get_open_Transaction());
        
        echo '1';
       
}
