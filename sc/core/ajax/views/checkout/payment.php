<?php $this->load_library('Gateways'); ?>

<span class="sc_close_link">[x]</span>
<?php if ($this->Gateways->number_of_gateways() > 1) : ?>
    <?=$this->Gateways->generate_gateway_dropdown()?>

<div class="sc_payment_gateway_area">
</div>

<script src="<?=sc_asset('js','payment')?>" type="text/javascript"></script>
<?php else : ?>
<div class="sc_payment_gateway_area">
    <?php foreach ($this->Gateways->Drivers as $driver) : ?> 
                
    <?= ($this->Config->get_setting('storelive'))
        ? $driver->load()
        : $driver->load_test() ?>
        
    <?php endforeach ?>    
</div>
<?php endif ?>


