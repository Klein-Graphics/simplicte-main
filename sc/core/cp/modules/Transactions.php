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
class Transactions extends \SC_CP_Module {
    public static $readable_name = "Transactions";
    public static $display_image = "assets/img/transactions.jpg";        
    
    function view_transactions($offset=0,$limit=30) {
        $this->SC->load_library('Cart');
        $transactions = \Model\Transaction::all(array(
                'offset'=>$offset,
                'limit'=>$limit,
                'conditions'=> 'items != FALSE'
            ));            
        
        $items = array();
        foreach ($transactions as $key => $transaction) {
            $items[$key] = $this->SC->Cart->explode_cart($transaction->items);
        }                                      
         
        $this->SC->CP->load_view('view_transactions',array(
            'items'=>$items,
            'transactions'=>$transactions
        ));                
        
    }
    
    function _add() {
    
    }
    
    function _del() {
    
    }
    
}
