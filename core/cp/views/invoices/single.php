<div id="single_invoice">
    <h1><span class="invoice-action"><?=ucfirst($action)?></span> Invoice</h1>
    <h3>      
        <a href="<?=sc_cp('Invoicing/view/'.$inv->ordernumber)?>" title="View invoice"><?=$inv->invoicenumber?></a>
        <span class="shrink"><?=$inv->invoice_date?></span>
    </h3>
    <button class="btn btn-primary edit-invoice">
        <span class="inverse-action"><?=($action == "view") ? "Edit" : "View"?></span> Invoice
    </button>
    <div id="view_edit_invoice">
    <?php if ($action == "view") : ?>
        <?php $this->SC->CP->load_view('view_invoice') ?>
    <?php elseif ($action == "edit") : ?>
        <?php $this->SC->CP->load_view('edit_invoice') ?>
    <?php endif ?>
    </div>
</div>
