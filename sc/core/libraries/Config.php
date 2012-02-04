<?php

  namespace Library;
  
  class Config extends \SC_Library {
  
    function get_setting($name) {
      $detail = \Model\Detail::find(array( 
        'select' => 'detail_value',
        'conditions' => array('detail = ?',$name)
      ));
      
      if ($detail->detail_value) {
        return $detail->detail_value;
      } else {
        return false;
      }
      
    }
  }
