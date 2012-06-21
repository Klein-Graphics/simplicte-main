<?php

    /**
     * @ignore
     *
     * @package foo
     */
     /*
$_POST = array(
    'transaction' => '1205170150',
    'method' => 'Credit',
    'hash' => MD5('1205170150 simplecart12345678910111213141516'),
    'status_code' => 1
);
    

include 'core/incoming_relay.php';*/

include 'core/init.php';

$SC->Shipping->load_driver('USPS');

$SC->Shipping->Drivers->USPS->api_call(array(
    'test' => array(
        'attributes' => array(
            'color' => 'pink',
            'something' => 'else'
        ),
        'data' => 'hello!',
        'children' => array(
            'child1' => array(
                'data'=>'I\'m a child!'
            )
        )
    )
));


