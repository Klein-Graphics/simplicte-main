<?php $this->load_library('Gateways'); ?>

<span class="sc_close_link">[x]</span>

<?=$this->Gateways->generate_gateway_dropdown()?>

<div class="sc_payment_gateway_area">
</div>

<script src="<?=sc_asset('js','payment')?>" type="text/javascript"></script>


