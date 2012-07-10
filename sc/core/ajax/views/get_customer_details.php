<?php
/**
 * Presents the account details form
 *
 * @package account
 */

$this->load_library('Session');
$customer = \Model\Customer::find($this->Session->force_get_user());

$req = '<span class="required_field">*</span>';

$fields = array(
    'firstname' => 'First Name'.$req,
    'initial' => 'Middle Initial',
    'lastname' => 'Last Name'.$req,
    'streetaddress' => 'Street Address'.$req,
    'apt' => 'Apt #',
    'city' => 'City'.$req,
    'state' => 'State'.$req,
    'country' => 'Country'.$req,
    'postalcode' => 'Postal Code'.$req,
    'phone' => 'Phone'.$req
);            

?>
<div class="sc_close_link">[x]</div>
Please fill out the following details: 
<form class="sc_account_form" action="<?=sc_ajax('do_get_customer_details')?>" method="POST">
<?php if (isset($_POST['new_customer'])) : ?>
    <input type="hidden" name="new_customer" value="1" />
<?php endif ?>
    <table>
        <thead>
            <td>Shipping Info:</td><td></td>
            <td>Billing Info:</td>
            <td>
                <input type="checkbox" name="sc_copy_information" class="sc_copy_information" />
                <label class="sc_label_left">Same as shipping</label>
            </td>
        </thead>
<?php $i=1; $j=count($fields); foreach ($fields as $field_name => $field_text) : ?>
        <tr>
    <?php if ($field_name != 'country') : ?>
        <?php $foo='ship_'.$field_name ?>              
            <td class="sc_left_shipping_info"><label class="sc_label_right"><?=$field_text?></label></td>  
            <td class="sc_right_shipping_info"><input tabindex="<?=$i?>" type="text" name="<?=$foo?>" value="<?=$customer->$foo?>" /></td>
        <?php $foo='bill_'.$field_name ?>               
            <td class="sc_left_billing_info"><label class="sc_label_right"><?=$field_text?></label></td>
            <td class="sc_right_shipping_info"><input tabindex="<?=$j+$i?> type="text" name="<?=$foo?>" value="<?=$customer->$foo?>" /></td>
    <?php else: ?>
        <?php $foo='ship_'.$field_name ?>              
            <td class="sc_left_shipping_info"><label class="sc_label_right"><?=$field_text?></label></td>  
            <td class="sc_right_shipping_info">
                <select class="sc_country_selector" tabindex="<?=$i?>" type="text" name="<?=$foo?>" value="<?=$customer->$foo?>">
                    <?php include 'core/includes/countrycodes.html'?>
                </select>
            </td>
        <?php $foo='bill_'.$field_name ?>               
            <td class="sc_left_billing_info"><label class="sc_label_right"><?=$field_text?></label></td>
            <td class="sc_right_billing_info">
                <select class="sc_country_selector" tabindex="<?=$j+$i?> type="text" name="<?=$foo?>" value="<?=$customer->$foo?>">
                    <?php include 'core/includes/countrycodes.html'?>
                </select>
            </td>            
    <?php endif ?>
        </tr>
<?php $i++; endforeach ?>
        <tr><td><input type="submit" value="Submit" /></td><td><span class="required_field">*required field</span></td></tr>
        <tr><td colspan="4" class="sc_display"></td></tr>
    </table>
    
</form>

<script type="text/javascript">
    $(document).ready(function(){
        $('.sc_right_shipping_info .sc_country_selector option[value="<?=$customer->ship_country?>"]').attr('selected','selected');
        $('.sc_right_billing_info .sc_country_selector option[value="<?=$customer->bill_country?>"]').attr('selected','selected');
        
        $('.sc_copy_information').change(function() {
            if ($(this).attr('checked')) {
                $('[name^="bill_"]').val(function(i) {             
                    return $('[name^="ship_"]').eq(i).val();
                });
            } else {
                $('[name^="bill_"]').val('');
            }   
        });
    });
</script>

