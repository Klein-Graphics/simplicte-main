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
        
        $filters = $this->transaction_filters($_POST);
                
        $total_transactions = \Model\Transaction::count(array('conditions'=>$filters));
        $total_pages = ceil($total_transactions/$count);
        
        if ($total_pages < $page) {
            $page = $total_pages;
        }
        
        $t_data['conditions'] = $filters;
        if ($count != 'a') {
                $t_data['offset'] = $page*$count;
                $t_data['limit'] = $count;
        }      
        
        $count = $total_transactions;
        
        $transactions = \Model\Transaction::all($t_data);
        
        $items = array();
        foreach ($transactions as $key => $transaction) {
            $items[$key] = $this->SC->Cart->explode_cart($transaction->items);
        }           
        
        $page++;                                                
         
        $this->SC->CP->load_view('transactions/view_transactions',array(
            'items'=>$items,
            'transactions'=>$transactions,
            'total_transactions' => $total_transactions,
            'page' => $page,
            'count' => $count,
            'total_pages' => $total_pages,
            'shipping_providers' => array_merge(
                array(array('code'=>'none','name'=>'None')),
                $this->SC->Shipping->get_providers())
        ));                
        
    }   
    
    private function transaction_filters($filters) {
    
        extract($filters);
    
        $where[]=array('`items` != ?',false);        
        
        if (isset($from_date) && $from_date) {
            $where[] = array('SUBSTR(`ordernumber`,1,6) >= ?', date('ymd',strtotime($from_date)));
        }        
        
        if (isset($to_date) && $to_date) {
            $where[] = array('SUBSTR(`ordernumber`,1,6) <= ?',date('ymd',strtotime($to_date)));    
        }
        
        if (isset($ordernumber) && $ordernumber) {
            if (strpos($ordernumber,'*') !== FALSE) {
                $where[] = array('`ordernumber` LIKE ?',str_replace('*','%',$ordernumber));
            } else {
                $where[] = array('`ordernumber` = ?',$ordernumber);
            }
        }
        
        if (isset($custid) && $custid) {
            if (strpos($custid,'*') !== FALSE) {
                $where[] = array('`custid` LIKE ?',str_replace('*','%',$custid));
            } else {
                $where[] = array('`custid` = ?',$custid);
            }
        }
        
        if (isset($ship_info) && $ship_info) {
            $ship_info = preg_replace('/[^\d\w]/','_',$ship_info);
            $ship_info = str_replace(array("\r\n","\n",PHP_EOL),'%',$ship_info);            
            $ship_info = str_replace(' ','%',$ship_info);       
            $where[] = array("CONCAT_WS(' ',
                `ship_firstname`,
                `ship_initial`,
                `ship_lastname`,
                `ship_streetaddress`,
                `ship_apt`,
                `ship_city`,
                `ship_state`,
                `ship_postalcode`,
                `ship_country`,
                `ship_phone`)
                    LIKE
                ?","%$ship_info%");
        }
        
        if (isset($bill_info) && $bill_info) {
            $bill_info = preg_replace('/[^\d\w\s]/','_',$bill_info);
            $bill_info = str_replace(array("\r\n","\n",PHP_EOL),'%',$bill_info);            
            $bill_info = str_replace(' ','%',$bill_info);            
            $where[] = array("CONCAT_WS(' ',
                `bill_firstname`,
                `bill_initial`,
                `bill_lastname`,
                `bill_streetaddress`,
                `bill_apt`,
                `bill_city`,
                `bill_state`,
                `bill_postalcode`,
                `bill_country`,
                `bill_phone`)
                    LIKE
                ?","%$bill_info%");
        }
        
        $statuses = array('opened','pending','settled','fulfilled','returned');
        
        $statuses = array_intersect($statuses,$_POST);
        
        if (!empty($statuses)) {
            $where[] = array("status IN(?)",$statues);
        }    
        
        if (isset($shipping_provider) && $shipping_provider) {
            if ($shipping_provider == 'none') {
                $where[] = array("shipping_method = ?",false);
            } else {
                $where[] = array("shipping_method LIKE ?","$shipping_provider%");
            }
        }    
                                     
        
        $g='';
        
        $conditions = array('');
        
        foreach ($where as $condition) {
            $conditions[0] .= $g.$condition[0];
            
            $conditions[] = $condition[1];                                         
            
            $g=' AND ';
        }        
        
        $g='';      
        
        if (isset($items) && $items) {
            $items = preg_replace('/[\s,]+/',',',$items);
            $items = explode(',',$items);
            $items = array_flatten($items);
            
            $g = ' AND (';
            $h = '';
            foreach ($items as &$item) {                
                $possible_items = \Model\Item::all(array(
                    'conditions' => array('`number` = ? OR `name` LIKE ?',"%$item%","%$item%") 
                ));                
                
                foreach ($possible_items as $possible_item) {
                    $regex = "'[[:<:]]{$possible_item->id}\\\|[0-9x]+\\\|[0-9]+\\\|[0-9\\\.]+'";
                    $conditions[0] .= "$g`items` RLIKE $regex";
                    $g = ' OR ';
                }                               
                
                $possible_options = \Model\Itemoption::all(array(
                    'conditions' => array('`code` = ? OR `name` LIKE ?',"%$item%","%$item%") 
                ));
                
                foreach ($possible_options as $possible_option) {
                    $regex = "'[0-9]+\\\|{$possible_option->id}x[0-9x]+\\\\|[0-9\\\.]+'";
                    $conditions[0] .= "$g`items` RLIKE $regex";
                    $g = ' OR ';
                }
                
                if ($g == ' OR ') $h = ')';
            }
            
            $conditions[0].=$h;                        
        }

        return $conditions;   
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
