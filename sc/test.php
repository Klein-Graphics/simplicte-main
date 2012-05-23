<?php

    /**
     * @ignore
     *
     * @package foo
     */

include 'core/init.php';

$customer = \Model\Customer::find(79);

echo $customer->full_name();


