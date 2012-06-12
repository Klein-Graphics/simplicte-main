<?php
  global $SC;

  $SC->load_library(array('Cart','Session'));

  $cart = $SC->Cart->explode_cart($SC->Session->transaction);    
   
?>

<table>
  <thead>
    <tr>
    <td>Name</td>
    <td>Options</td>
    <td>Qty</td>
    <td>Price</td>
    </tr>
  </thead>
  <tbody>
  
<?php if (count($cart)) : ?>
  <?php foreach($cart as $key => $item) : ?>
    <tr>
      <td class="sc_cart_item_name">
        <?=$SC->Items->item_name($item['id'])?>
      </td>
      <td class="sc_cart_item_options">
    <?php if ($item['options']) : ?> 
        <ul>
      <?php foreach($item['options'] as $option) : ?>
        <li>
            <?=$SC->Items->option_name($option['id'])?>
            <?=($option['quantity']>1) ? ' x '.$option['quantity'] : ''?>
            <?=($option['price']) ? ' - $'.number_format($option['quantity']*$option['price'],2) : ''?>
        </li>
      <?php endforeach ?>
        </ul>
    <?php endif ?>
      </td>
      <td class="sc_cart_item_quantity">
        <input 
            type="text" 
            class="sc_cart_item_quantity_adjust" 
            onblur="adjust_quantity(<?=$key?>,$(this).val())" 
            value="<?=$item['quantity']?>" />
      </td>
      <td class="sc_cart_item_price">
        $<?=number_format($SC->Cart->line_total($key,$cart),2)?>
      </td>
      <td class="sc_cart_item_remove">
        <a href="<?=sc_ajax('remove_item',$key)?>" title="Remove Item">Remove</a>
      </td>
  <?php endforeach ?>
<?php else : ?>
  <tr><td class="sc_cart_no_items">Your cart is empty</td></tr>
<?php endif ?>
  </tbody>
</table>

<script type="text/javascript">
    $('.sc_cart_item_remove').children('a').click(function(e) {
        e.preventDefault();
        
        $.get($(this).attr('href'),function(data) {
            if (data==1) {
                page_display.cart.refresh();
            }
        });                
        
        page_display.checkout.hide();
    });    
    
    function adjust_quantity(line,amount) {
        $.get(sc_location('ajax/change_qty/'+line+'/'+amount),function(data) {
            if (data==1) {
                page_display.cart.refresh();                
            }
        });
        
        page_display.checkout.hide();
    }    
</script>
