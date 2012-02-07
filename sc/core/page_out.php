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

//Add cart and checkout
$output = $SC->Page_loading->cart_driver->add_cart($output);
$output = $SC->Page_loading->checkout_driver->add_checkout($output);

//Add ajax loader

$output = $SC->Page_loading->replace_tag($output,'ajax_loader','<span class="ajax_loader"><img src="'.sc_asset('img','ajax-loader.gif').'" title="Loading..." /></span>');

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
  
  function sc_button(button) {
   
   return "'.sc_location().'assets/button/'.$SC->Config->get_setting('buttons_folder').'/"+button+".gif";
    
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
