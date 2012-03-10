<?php

namespace Cart_Driver;
class Folding extends \SC_Cart_Driver {

  function add_cart($input) {
      return $this->SC->Page_loading->replace_tag($input,'display_cart','<div id="display_cart" style="display:none"></div>');
  }

}
