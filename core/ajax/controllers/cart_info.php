<?php
  
  /**
   * Calulates and displays the cart info.
   *
   * @package Transactions
   */
  
  global $SC;
  
  $SC->load_library(array('Session','Cart'));
    
  $transaction = $SC->Session->get_open_transaction();  
  
  $subtotal = number_format($SC->Cart->subtotal($transaction),2);
  $count = $SC->Cart->item_count($transaction);

?>
Items In Cart: <?=$count?> Subtotal: $<?=$subtotal?>   

