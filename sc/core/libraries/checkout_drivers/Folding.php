<?php

namespace Checkout_Driver;
class Folding extends \SC_Checkout_Driver {

  function add_checkout($input) {
      return $this->SC->Page_loading->replace_tag($input,'checkout_area','<div id="sc_checkout" style="display:none"></div>');
  }

}
