<?php

  namespace Library;
  
  class Config extends \SC_Library {
  
    function get_setting($name) {       
        if (is_array($name)) {
            
            $return = array();
            
            foreach ($name as $this_name) {
                $return[] = $this->get_setting($this_name);
            }
            
            return $return;
        }             
        $detail = \Model\Detail::find(array( 
            'select' => 'detail_value',
            'conditions' => array('detail = ?',$name)
        ));

        if (isset($detail->detail_value) && $detail->detail_value) {
            return $detail->detail_value;
        } else {
            return false;
        }
      
    }
  }
