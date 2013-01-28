<?php

  namespace Model;

  class Invoice extends \SC_Model {

	static $table_name = "transactions";
	static $alias_attribute = array(
	    'invoicenumber' => 'ordernumber'
	);
  
    static $before_save = array('update_time');    

	/**
     * Because the storing of the invoice is incredibly hacky, we
	 * have to create an incredibly hacky __get magic method that
     * denies access to any of the record's attributes that would
     * only be used if it was an order, not an invoice.
	 */
	public function &__get($name) {

		$bad_attributes = array('shipping','taxrate','discount','paytype','shipping_method');

		//Is this one of the no-no attributes?
		if (array_search($name,$bad_attributes) !== FALSE)
			throw new \ActiveRecord\UndefinedPropertyException(get_called_class(),$name);

		return parent::__get($name);

	}

	/**
     * In a similar vain to the above note about the hacky __get
	 * method, the invoice model needs to also make sure that what
     * we're getting is indeed an actual invoice.
     */
	public static function find(/* $type, $options */) {
		$return = forward_static_call_array('parent::find',func_get_args());

		if (is_array($return)) {
			foreach ($return as $key => $record) {
				if ($record->transtype != 'invoice')
					unset($return[$key]);
			}
		} else {
			if ($return->transtype != 'invoice')
				$return = null;
		}	
		return $return;
	}

	/**
 	 * Hack to make static::count() return a proper number. This
	 * method makes me puke a little.
	 */
	public static function count(/* ... */)
	{
		$args = func_get_args();
		$options = static::extract_and_validate_options($args);
		$options['select'] = 'COUNT(*)';

		if (!empty($args) && !is_null($args[0]) && !empty($args[0]))
		{
			if (is_hash($args[0]))
				$options['conditions'] = $args[0];
			else
				$options['conditions'] = call_user_func_array('static::pk_conditions',$args);
		}

		$table = static::table();
		$sql = $table->options_to_sql($options);
		$values = $sql->get_where_values();
		
		$sql_string = $sql->to_s();

		if (strpos($sql_string, 'WHERE') !== FALSE) {
			$sql_string = str_replace('WHERE','WHERE transtype = "invoice" AND ',$sql_string);
		} else {
			$sql_string .= ' WHERE transtype = "invoice"';
		}
		
		return static::connection()->query_and_fetch_one($sql_string,$values);
	}
    
    public function update_time() {
        $this->lastupdate = time();

        return true;
    }
    
    public function shipping_info() {
        $info = $this->ship_firstname.' ';
        
        if ($this->ship_initial) {
            $info .= $this->ship_initial.' ';
        }
        
        $info .= $this->ship_lastname.'<br />';
        
        $info .= $this->ship_streetaddress.' ';        
        if ($this->ship_apt) {
            $info .= 'Apt. '.$this->ship_apt;
        }        
        $info .= '<br />';
        
        if ($this->ship_city && $this->ship_state) {
            $info .= $this->ship_city.', '.$this->ship_state.' ';
        }
        $info .= $this->ship_postalcode.'<br />';
        
        $info .= $this->ship_country.'<br /><br />';
        
        $info .= $this->ship_phone;
        
        return $info;
    }
    
    public function billing_info() {
        $info = $this->bill_firstname.' ';
        
        if ($this->bill_initial) {
            $info .= $this->bill_initial.' ';
        }
        
        $info .= $this->bill_lastname.'<br />';
        
        $info .= $this->bill_streetaddress.' ';        
        if ($this->bill_apt) {
            $info .= 'Apt. '.$this->bill_apt;
        }        
        $info .= '<br />';
        
        if ($this->bill_city && $this->bill_state) {
            $info .= $this->bill_city.', '.$this->bill_state.' ';
        }
        $info .= $this->bill_postalcode.'<br />';
        
        $info .= $this->bill_country.'<br /><br />';
        
        $info .= $this->bill_phone;
        
        return $info;
    }    
    
    //Getters    
    public function get_invoice_date() {
        return substr($this->ordernumber,2,2).
              '/'.substr($this->ordernumber,4,2).
              '/'.substr($this->ordernumber,0,2);
    } 
    

    
    function get_ship_fullname() {
        return  $this->ship_firstname.' '.
                (($this->ship_initial) 
                    ? $this->ship_initial.' ' 
                    : ''
                ).
                $this->ship_lastname;
    }
        
    function get_bill_fullname() {
        return  $this->bill_firstname.' '.
                (($this->bill_initial) 
                    ? $this->bill_initial.' ' 
                    : ''
                ).
                $this->bill_lastname;
    }
    
    function get_ship_full_apt() {
        return ($this->ship_apt) ? 'Apt #'.$this->ship_apt : '';
    }
    
    function get_bill_full_apt() {
        return ($this->bill_apt) ? 'Apt #'.$this->bill_apt : '';
    }
  
  }
