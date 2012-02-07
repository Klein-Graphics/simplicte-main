<?php
  namespace View;
  function view_cart() {
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
          <li><?=$SC->Items->option_name($option['id'])?> x <?=$option['quantity']?><?=($option['price']) ? ' - $'.number_format($option['quantity']*$option['price'],2) : ''?></li>
      <?php endforeach ?>
        </ul>
    <?php endif ?>
      </td>
      <td class="sc_cart_item_quantity">
        <input type="text" class="sc_cart_item_quantity_adjust" value="<?=$item['quantity']?>" />
      </td>
      <td class="sc_cart_item_price">
        <?=$item['price']?>
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


<?php } ?>
