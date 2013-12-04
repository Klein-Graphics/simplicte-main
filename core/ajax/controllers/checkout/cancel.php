<?php

$transaction = \Model\Transaction::find($this->Session->get_open_transaction());

$transaction->items = $this->Cart->reset_cart($transaction->items);

if ($transaction->status == 'pending') {
    $this->Stock->return_cart($transaction->items);
    $transaction->status = 'opened';
    $transaction->save();
}
