<?php

  //----------------------
  //Items library
  //----------------------
  //
  // This libarary handles the CRUD of Items and Item Options
  //
  
  namespace Library;
  
  class Items extends \SC_Library {
  
    //Items
    
    //Main function
    function get_item($id,$return_cols='*') {
      
      $item = \Model\Item::find($id,array('select'=>$return_cols));
      
      return db_return($item,$return_cols);

    }
    
    //Specific detail wrappers:
    
    function item_price($id) {
      return $this->get_item($id,'price');
    }
    
    function item_name($id) {
      return $this->get_item($id,'name');
    }
    
    //Item options
    
    //Main function
    function get_option($id,$return_cols='*') {
    
      $option = \Model\Itemoption::find($id,array('select'=>$return_cols));
    
      return db_return($option,$return_cols);
    }
    
    //Specific detail wrappers:    
    function option_price($id) {
      return $this->get_option($id,'price');
    }
    
    function option_name($id) {
      return $this->get_option($id,'name');
    } 
  
  }
  
