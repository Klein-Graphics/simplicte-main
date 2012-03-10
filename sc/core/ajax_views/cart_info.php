<?php
  namespace View;
  function cart_info() {  
  global $SC;
  $SC->load_library(array('Session','Cart'));
  
  $transaction = $SC->Session->transaction;
  
  
  $subtotal = number_format($SC->Cart->subtotal($transaction),2);
  $count = $SC->Cart->item_count($transaction);
  

?>
Items In Cart: <?=$count?> Subtotal: $<?=$subtotal?>   



<?php }
