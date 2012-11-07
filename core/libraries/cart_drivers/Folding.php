<?php
/**
 * Folding Driver
 *
 * This driver creates folding elements on the page, that slide open and closed
 *
 * @package Transactions\Drivers
 */
namespace Cart_Driver;

/**
 * Folding Driver Class
 *
 * @package Transactions\Cart Drivers
 */
class Folding extends \SC_Cart_Driver {
    /**
     * Add Cart
     *
     * Adds the cart DIV to the page
     *
     * @return string The output
     *
     * @param string $input The input
     */
    function add_cart($input) {
        return $this->SC->Page_loading->replace_tag($input,'display_cart','<div id="sc_display_cart" style="display:none"></div>');
    }

}
