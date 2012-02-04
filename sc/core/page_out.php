<?php

//Get, End, and Output ouput buffer
$output = ob_get_contents();
ob_end_clean();



//Replace item details, ex. Name, Description
$output = $SC->Page_loading->replace_details($output);

//Replace cart info
$output = $SC->Page_loading->replace_tag($output,'cartinfo','<span class="sc_cartinfo"></span>');

//Replace buttons
$buttons = array('view_cart','clear_cart','checkout');
foreach ($buttons as $button) {
  $output = $SC->Page_loading->replace_button($output,str_replace('_','',$button),$button);
}

//Add and insert javascript, including global function wrappers
$SC->Page_loading->add_javascript('',' 
  function site_url(addl) {
    
    addl = addl || "";
    
    addl = addl.replace(/^\\//,"");
    
    return "'.site_url().'"+addl;
  }
  
  function sc_location(addl) {
    
    addl = addl || "";
  
    addl = addl.replace(/^\\//,"");
    
    return "'.sc_location().'"+addl;
  }
');

$SC->Page_loading->add_javascript('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
$SC->Page_loading->add_javascript(sc_asset('js','page_display'));

//Load cart and checkout drivers

$SC->Page_loading->add_javascript(sc_asset('js','cart_drivers/'.$SC->Config->get_setting('cart_driver').'.js'));
$SC->Page_loading->add_javascript(sc_asset('js','checkout_drivers/'.$SC->Config->get_setting('checkout_driver').'.js'));

$output = $SC->Page_loading->insert_javascript($output);

//Dump to page
echo $output;
