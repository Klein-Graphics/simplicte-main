<?php

  namespace Model;

  class Transaction extends \ActiveRecord\model {
  
    static $before_save = array('update_time');
    
    public function update_time() {
        $this->lastupdate = time();

        return true;
    }
    
    public function shipping_info() {
        $info = $this->ship_firstname.' ';
        
        if ($this->ship_initial) {
            $info .= $this->ship_initial.' ';
        }
        
        $info .= $this->ship_lastname.'<br />';
        
        $info .= $this->ship_streetaddress.' ';        
        if ($this->ship_apt) {
            $info .= 'Apt. '.$this->ship_apt;
        }        
        $info .= '<br />';
        
        if ($this->ship_city && $this->ship_state) {
            $info .= $this->ship_city.', '.$this->ship_state.' ';
        }
        $info .= $this->ship_postalcode.'<br />';
        
        $info .= $this->ship_country.'<br /><br />';
        
        $info .= $this->ship_phone;
        
        return $info;
    }
    
    public function billing_info() {
        $info = $this->bill_firstname.' ';
        
        if ($this->bill_initial) {
            $info .= $this->bill_initial.' ';
        }
        
        $info .= $this->bill_lastname.'<br />';
        
        $info .= $this->bill_streetaddress.' ';        
        if ($this->bill_apt) {
            $info .= 'Apt. '.$this->bill_apt;
        }        
        $info .= '<br />';
        
        $info .= $this->bill_city.', '.$this->bill_state.' '.$this->bill_postalcode.'<br />';
        $info .= $this->bill_country.'<br /><br />';
        
        $info .= $this->bill_phone;
        
        return $info;
    }    
  
  }
