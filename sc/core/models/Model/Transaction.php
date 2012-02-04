<?php

  namespace Model;

  class Transaction extends \ActiveRecord\model {
  
    static $before_save = array('update_time');
    
    public function update_time() {
      $this->lastupdate = time();
    }
  
  }
