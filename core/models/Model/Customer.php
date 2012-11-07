<?php 

namespace Model;

class Customer extends \SC_Model {
    function get_ship_fullname() {
        return  $this->ship_firstname.' '.
                (($this->ship_initial) 
                    ? $this->ship_initial.' ' 
                    : ''
                ).
                $this->ship_lastname;
    }
    
    function get_bill_fullname() {
        return  $this->bill_firstname.' '.
                (($this->bill_initial) 
                    ? $this->bill_initial.' ' 
                    : ''
                ).
                $this->bill_lastname;
    }
    
    public function shipping_info() {
        $info  = $this->ship_fullname.'<br />';
        
        $info .= $this->ship_streetaddress.' ';        
        $info .= $this->ship_full_apt;
        $info .= '<br />';
        
        $info .= $this->ship_city_state;

        $info .= $this->ship_postalcode.'<br />';
        
        $info .= $this->ship_country.'<br /><br />';
        
        $info .= $this->ship_phone;
        
        return $info;
    }
    
    public function billing_info() {
        $info  = $this->bill_fullname.'<br />';
        
        $info .= $this->bill_streetaddress.' ';        
        $info .= $this->bill_full_apt;
        $info .= '<br />';
        
        $info .= $this->bill_city_state;

        $info .= $this->bill_postalcode.'<br />';
        
        $info .= $this->bill_country.'<br /><br />';
        
        $info .= $this->bill_phone;
        
        return $info;
    }  
    
    function get_ship_full_apt() {
        return ($this->ship_apt) ? 'Apt #'.$this->ship_apt : '';
    }

    function get_bill_full_apt() {
        return ($this->bill_apt) ? 'Apt #'.$this->bill_apt : '';
    }
    
    function get_ship_city_state() {
        return $this->ship_city.(($this->ship_city)?', ':'').$this->ship_state;
    }
    
    function get_bill_city_state() {
        return $this->bill_city.(($this->bill_city)?', ':'').$this->bill_state;
    }
    
    function get_join_date() {
        return substr($this->custid,2,2).
              '/'.substr($this->custid,4,2).
              '/'.substr($this->custid,0,2);
    }
   
}
