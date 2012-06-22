<?php

/**
 * Messaging Library
 *
 * This Library contains handles messaging customers and the shop owner
 *
 * @package Account
 */

namespace Library;
/** 
 * The Messaging library class
 *
 * @package Account
 */

class Messaging extends \SC_Library {
    
    function message_store($message,$subject="Message from your eStore") {
        $orders_email = $this->SC->Config->get_setting('orderEmail');
        $store_email = $this->SC->Config->get_Setting('sendEmail');
        
        $this->send_mail($orders_email,$store_email,$subject,$message);
    }
    
    function message_customer($customer_id,$message,$subject="") {
        $this->SC->load_library('Customer');
        $customer_email = $this->SC->Customer->get_customer($customer_id,'email','custid');
        $store_email = $this->SC->Config->get_Setting('sendEmail');
        
        $this->send_mail($customer_email,$store_email,$subject,$message);                        
    }
    
    function send_receipt($transaction) {
        
        if (is_numeric($transaction)) {
                $transaction = \Model\Transaction::find($transaction);
        }        
        
                
        $this->SC->load_library(array('Cart','Customer','Page_loading'));           
        
        $receipt_template = $this->SC->Config->get_setting('receipt_template');
        if (!$receipt_template) $receipt_template = 'receipt';
        
        //Load the template
        $receipt = file_get_contents("email_templates/$receipt_template.html");
        
        $receipt = $this->SC->Page_loading->replace_transaction_details($receipt,$transaction);
        $cart = $this->SC->Cart->explode_cart($transaction->items);
        
        $SC = &$this->SC;
        /***********
         * Item Table
         ***********/ 
        $item_table = function($args) use ($cart,$SC) {
         
            ob_start();        
            foreach($cart as $key => $item) : 
                $i = \Model\Item::find($item['id']); ?>
<tr>
    <td class="sc_receipt_item_name" style="<?=$args[1]?>">
        <?=$i->name?>
    </td>
    <td class="sc_receipt_item_options" style="<?=$args[1]?>">
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
    <td class="sc_receipt_item_quantity" style="<?=$args[1]?>">
        <?=$item['quantity']?>
    </td>
    <td class="sc_receipt_item_price" style="<?=$args[1]?>">
        $<?=number_format($SC->Cart->line_total($key,$cart),2)?>
    </td>
</tr>                    
            <?php endforeach;
            
            
            
            return ob_get_clean();
        }; /***************
          * Item table end
          ****************/
        
        $store_name = $this->SC->Config->get_setting('store_name');
        
        $wrap_function = function($args) use ($transaction) {
            if ($transaction->$args[0]) {
                return $args[1].number_format($transaction->$args[0],2).$args[2];    
            } else {
                return '';
            }
        };
                    
        //Some tags to swap out
        $tags = array(
            'store_name' => $store_name,
            'item_table' => $item_table,
            'site_url' => site_url(),
            'discount' => $wrap_function,
            'shipping' => function($args) use ($transaction,$SC) {
                if ($transaction->shipping || $transaction->shipping_method) {
                    return $args[1]
                          .$SC->Shipping->get_nice_name($transaction->shipping_method)
                          .$args[2]
                          .number_format($transaction->shipping,2)
                          .$args[3];    
                } else {
                    return '';
                }
            }           
        );
        
        $receipt = $this->SC->Page_loading->replace_tag($receipt,$tags);
        
        $good = $this->message_customer(
            $transaction->custid,
            $receipt,
            "Your order #{$transaction->ordernumber} from $store_name"
        );                        
        
        /**
         * Store owner message start
         */
        ob_start(); ?>
<p>
    Order number #<?=$transaction->ordernumber?> has been settled and may need to be fulfilled. 
    <a href="<?=sc_cp('Transactions/'.$transaction->ordernumber)?>" title="Transaction <?=$transaction->ordernumber?>">
        Click here to view.
    </a>
</p>    
<p>The following message was sent to the customer:</p>        
        <?php
        
        /**
         * Store owner message end
         */
        
        $store_message = ob_get_clean();
        
        $store_message .= $receipt;                        
        
        $good = min($this->message_store($store_message,"Order #{$transaction->ordernumber} fullfilled"),$good);
        
        return $good;        
    }
    
    function send_mail($to,$from,$subject,$message) {
    
        $headers = 'From: ' . $from . "\r\n" .
        'Reply-To: ' . $from . "\r\n" .
        'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();              
        
        return mail($to, $subject, $message, $headers);   
    }

}
