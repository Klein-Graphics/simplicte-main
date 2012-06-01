<?php

/** 
 * Transactions Control Panel Module
 *
 * The module responsable for managing transactions
 *
 * @package Control Panel
 */
namespace CP_Module;

/**
 * Transactions Control Panel Module Class
 *
 * @package Control Panel
 */
class Stock extends \SC_CP_Module {
    public static $readable_name = "Items and Stock";
    public static $display_image = "assets/img/stock.jpg";
    
    function view_items() {
        $items = \Model\Item::all();
        
        $this->SC->CP->load_view('stock/view_items',array('items'=>$items));
    }    
    
    function _add_item() {
    
    }
    
    function _edit_item() {
    
    }
}
