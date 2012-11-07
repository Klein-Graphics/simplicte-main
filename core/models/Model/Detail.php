<?php

namespace Model;

class Detail extends \SC_Model {

    static $primary_key = "detail";
    
    function get_detail_value() {
        if ($this->encoded) {
            return decrypt($this->read_attribute('detail_value'));
        }
        
        return $this->read_attribute('detail_value');

    }
    
    function get_category() {
        return explode('-',$this->read_attribute('category'));
    }   
}
  
