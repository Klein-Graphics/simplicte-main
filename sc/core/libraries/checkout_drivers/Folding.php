<?php
/**
 * Folding Driver
 *
 * This driver creates folding elements on the page, that slide open and closed
 *
 * @package Transactions\Checkout Drivers
 */
namespace Checkout_Driver;

/**
 * Folding Driver Class
 *
 * @package Transactions\Checkout Drivers
 */
class Folding extends \SC_Checkout_Driver {
    /**
     * Add Checkout
     *
     * Adds the checkout div to the page
     *
     * @return string The output
     *
     * @param string $input The input
     */
    function add_checkout($input) {
        return $this->SC->Page_loading->replace_tag($input,'checkout_area','<div id="sc_checkout" style="display:none"></div>');
    }

}
