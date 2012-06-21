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
    public static $icon = "shopping-cart";        
    
    function view_transactions($page='1',$count='30') {
        $this->SC->load_library('Cart');
        
        $page--;
        
        $total_transactions = \Model\Transaction::count(array('conditions'=> 'items != FALSE'));
        $total_pages = ceil($total_transactions/$count);
        
        if ($total_pages < $page) {
            $page = $total_pages;
        }        
        
        $transactions = \Model\Transaction::all(array(
                'offset'=>$page*$count,
                'limit'=>$count,
                'conditions'=> 'items != FALSE'
        ));                    
        
        $items = array();
        foreach ($transactions as $key => $transaction) {
            $items[$key] = $this->SC->Cart->explode_cart($transaction->items);
        }           
        
        $page++;                                                
         
        $this->SC->CP->load_view('view_transactions',array(
            'items'=>$items,
            'transactions'=>$transactions,
            'total_transactions' => $total_transactions,
            'page' => $page,
            'count' => $count,
            'total_pages' => $total_pages,
            'shipping_providers' => $this->SC->Shipping->get_providers()
        ));                
        
    }
    
    function _filter_transactions($search) {
                       
    }    
    
    function __catch($transaction) {
        $this->SC->CP->load_view('header');
        $this->SC->CP->load_view('menu',array(
            'this_module' => 'Transactions',
            'modules' => $this->SC->CP->get_modules()
        ));
        $this->SC->CP->load_view('module_menu',array(
            'methods' => $this->methods
        ));
        $transaction = \Model\Transaction::find_by_ordernumber($transaction);
        $cart = $this->SC->Cart->explode_cart($transaction->items);
        $this->SC->CP->load_view('transactions/single',array(
            't' => $transaction,
            'c' => $cart
        ));
        $this->SC->CP->load_view('footer');
    }    
    
    function _add() {
    
    }
    
    function _del() {
    
    }
    
}
