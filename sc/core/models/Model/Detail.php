<?php

namespace Model;

class Detail extends \SC_Model {
    
    function get_detail_value() {
        if ($this->encoded) {
            return decrypt($this->read_attribute('detail_value'));
        }
        
        return $this->read_attribute('detail_value');

    }
}
  
