<?php

$transaction = \Model\Transaction::find($this->Session->get_open_transaction());

if ($transaction->status == 'pending') {
    $this->Stock->return_cart($transaction->items);
    $transaction->status = 'opened';
    $transaction->save();
}
