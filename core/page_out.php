<?php

/**
 * Simplecart2 Out-Parsing File
 *
 * This file must be included in any page where SC's display parsing needs to 
 * happen. It must be included *after* any output from the page.
 *
 * @package Parsing
 *
 * @see Core/page_in.php
 */

chdir($sc_cwd);

//Get, End, and Output output buffer
$output = ob_get_contents();
ob_end_clean();

//SC is broke, say so.
if (SC_STOP) {
    $output = preg_replace("/\[\[(checkout(\|[^(\]\])]+)*)\]\]/i",'<div class="sc_fatal_error">We are currently experiencing a database outage, as such, our eStore is unavailable at the moment</div>',$output);
    $output = preg_replace("/\[\[((.*)(\|[^(\]\])]+)*)\]\]/i",'',$output);
    echo $output;
    return;
}

//Add Items and Add To Cart Buttons
$output = $SC->Page_loading->run_item_templates($output);

//Replace item details, ex. Name, Description
$output = $SC->Page_loading->replace_details($output);

//Replace cart info
$output = $SC->Page_loading->replace_tag($output,array(
    'cartinfo'=>'<span class="sc_cartinfo"></span>', //hack for old tag
    'cart_info'=>'<span class="sc_cartinfo"></span>'
));

//Hack for old checkout tag
$output = $SC->Page_loading->replace_tag($output,'view_checkout','[[checkout]]');

//Replace buttons
$buttons = array('view_cart','clear_cart','checkout');
foreach ($buttons as $button) {
    $output = $SC->Page_loading->replace_button($output,$button,$button);
    $output = $SC->Page_loading->replace_button($output,str_replace('_','',$button),$button); //hack for old tag
}


//Add cart and checkout
$output = $SC->Page_loading->cart_driver->add_cart($output);
$output = $SC->Page_loading->checkout_driver->add_checkout($output);

if ($SC->Session->has_account()) {
    //Create the account form
    $output = $SC->Page_loading->account_driver->add_account_info($output);    
} else {
    //Create the login form
    $output = $SC->Page_loading->account_driver->add_login($output);
}

//Add Ajax loader
$output = $SC->Page_loading->replace_tag($output,'ajax_loader','<span class="ajax_loader"><img src="'.
    $SC->Page_loading->get_ajax_loader().'" alt="Loading..." /></span>');

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
   
   return "'.sc_location().'/core/assets/button/'.$SC->Config->get_setting('buttons_folder').'/"+button+".gif";
    
  }
');

$SC->Page_loading->add_top_javascript('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
$SC->Page_loading->add_top_javascript(sc_asset('js','jquery.crypt'));
$SC->Page_loading->add_javascript(sc_asset('js','page_display'));
$SC->Page_loading->add_css_file(sc_asset('css','sc'));

//Load display drivers

foreach ($SC->Page_loading->loaded_drivers as $type => $driver) {
    $SC->Page_loading->add_javascript(sc_asset('js',$type.'_drivers/'.$driver.'.js'));
}

$output = $SC->Page_loading->insert_javascript($output);
$output = $SC->Page_loading->insert_css($output);

//Dump to page
echo $output;
