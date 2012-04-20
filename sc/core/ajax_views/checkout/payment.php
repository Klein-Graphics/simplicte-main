<?php

    $this->load_library('Gateways');
        
?>

<form class="sc_payment_gateway_form">

    <?=$this->Gateways->generate_gateway_dropdown()?>

    <input type="submit" disabled="disabled" value="Continue to Payment"/>

</form>

