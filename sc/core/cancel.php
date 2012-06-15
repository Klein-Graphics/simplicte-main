<?php

require 'init.php';

$t = \Model\Transaction::find($SC->Session->get_open_transaction());
if ($t->status == 'pending') {
    $SC->Stock->return_cart($t->items);
    $t->status == 'opened';
    $t->save();    
}

header('Location: '.site_url('#checkout'));
