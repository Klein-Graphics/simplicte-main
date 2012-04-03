<?php
  namespace View;
  function add_item($item_num) {
    global $SC;
    
    $SC->load_library('Cart');
    
    $options = $_POST['options'];         
    
    $invalid_option_flag = FALSE;
    foreach($options as $key => $option) {
        
        if (($no_flag = $SC->Items->option_flag($option,'no')) !== FALSE) {
            $no_flag[1] = explode('-',$no_flag[1]);            
            foreach ($no_flag[1] as $no_item) {            
                if (array_search($no_item,$options) !== FALSE) {
                    $invalid_option_flag = TRUE;
                    //Is there a different option we can use?
                    if ($no_flag[2]) {
                        $option = $no_flag[2];
                    }
                }
            }
        }
    
        $options_to_cart[$key]['id'] = $option;
        $options_to_cart[$key]['quantity'] = isset($_POST['option_qty'][$key]) ? $_POST['option_qty'][$key] : 1;                    
        
    }                    
    
    $SC->Cart->add_item($SC->Session->get_open_transaction(),$item_num,$options_to_cart,$_POST['item_qty']);
    
    echo ($invalid_option_flag) ? 'Item Added - Check your cart' : 'Item Added!';
  }
