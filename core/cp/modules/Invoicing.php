<?php

/** 
 * Invoicing Control Panel Module
 *
 * The module responsable for creating and managing custom invoices
 *
 * @package Control Panel
 */
namespace CP_Module;

/**
 * Invoicing Control Panel Module Class
 *
 * @package Control Panel
 */
class Invoicing extends \SC_CP_Module {
    public static $readable_name = "Invoicing";
    public static $icon = "file";
    public $hidden_pages = array('invoice');
    
	/**
	 * View Invoice Controller
	 *
     * Loads invoices to be diplayed on the view invoice page, based
     * on any user defined filters.
	 */
    function view_invoices($page='1',$count='30') {
        $page--;
        
        $filters = $this->invoice_filters($_POST);        
                
        $total_invoices = \Model\Invoice::count(array('conditions'=>$filters));
        $total_pages = ($count != 'a') ? ceil($total_invoices/$count) : 1;
                
        if ($total_pages < $page+1) {
            $page = $total_pages - 1;
        }
        
        $t_data['conditions'] = $filters;
        $t_data['order'] = 'ordernumber desc';
        if ($count != 'a' && $page >= 0) {
                $t_data['offset'] = $page*$count;
                $t_data['limit'] = $count;
        }              
        
        $invoices = \Model\Invoice::all($t_data);                 
        
        $page++;                                                
         
        $this->SC->CP->load_view('invoices/view_invoices',array(
            'invoices'=>$invoices,
            'total_invoices' => $total_invoices,
            'page' => $page,
            'count' => $count,
            'total_pages' => $total_pages
        ));
    }

	private function invoice_filters($filters) {    
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

        $statuses = array_intersect($statuses,array_keys($filters));
        
        if (!empty($statuses)) {
            $where[] = array("status IN(?)",$statuses);
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
    
    function _display_invoice() {
    
    }        
    
    function _edit_invoice() {
    
    }   
    
    function _update_invoice_status() {
    
    }   
    
    function _create_invoice() {
    
    }
    
    function _update_invoice() {
    
    }
    
    function __catch($action,$invoice) {
        $this->SC->CP->load_view('header');
        $this->SC->CP->load_view('menu',array(
            'this_module' => 'Invoices',
            'modules' => $this->SC->CP->get_modules()
        ));
        $this->SC->CP->load_view('module_menu',array(
            'methods' => $this->visible_methods
        ));
        $invoice = \Model\Invoice::find_by_invoicenumber($invoice);
        $this->SC->CP->load_view('invoices/single',array(
            'inv' => $invoice,
            'action' => $action
        ));
        $this->SC->CP->load_view('footer');
    }
}
