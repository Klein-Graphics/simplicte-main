<?php

  //----------------------
  //Page Loading Functions
  //----------------------
  //
  // These functions handle the loading of simplecart onto the user's page
  //
  
  namespace Library;
  
  class Page_loading extends \SC_Library {
  
    function __construct() {
      parent::__construct();
      $this->js = array();
      
      $this->loaded_drivers = array();
      $this->load_drivers();
      $this->initialize_item_templates();
      
    }
  
    function replace_tag($input,$tag,$replacement=NULL) {  
      //Find the tag in the page
      if (preg_match_all("/\[\[($tag(\|[^(\]\])]+)*)\]\]/i",$input,$matches)) {
        if (is_callable($replacement)) {
          $function = $replacement;
          $replacement = array();
          
          foreach($matches[1] as $key => $match) {
            $match = explode('|',$match);
            
            $replacement[$key] = $function($match);
          }
        }              
        
        return str_replace($matches[0],$replacement,$input);
      
      }
      
      return $input;
    
    }
    
    function replace_button($input,$tag,$button) {
      
      $readable = str_replace('_',' ',ucfirst($button));
    
      return $this->replace_tag($input,$tag,'<a href="'.sc_ajax($button).'" title="'.$readable.'" class="sc_'.$button.'"><img src="'.sc_asset('button',$button).'" alt="'.$readable.'" /></a>');
    }
    
    function replace_details($input) {
      
      //Replace "name" tags. This is for backwards compatibility of the depreciated tag [[name]]
      $input = $this->replace_tag($input,'name',function($args) {
        
        if (strpos($args[1],'i--') === FALSE) { //This is simply an db id
          $item = \Model\Item::find($args[1]);
          
          return $item->name;
        } else {
          $args[1] = substr($args[1],3);
          
          $item = \Model\Item::find('first',array('conditions' => array('number = ?',$args[1])));
          
          return $item->name;        
        }
        
        //If we get here something went super wrong, throw an error;
        trigger_error('Something went horrendously wrong. Abandon ship.',E_USER_ERROR);
        
      });
      
      //Replace "desc" tags. This is for backwards compatibility of the depreciated tag [[desc]]
      $input = $this->replace_tag($input,'desc',function($args) {
      
        if (strpos($args[1],'i--') === FALSE) { //This is simply an db id
          $item = \Model\Item::find($args[1]);
          
          return $item->description;
        } else {
          $args[1] = substr($args[1],3);
          
          $item = \Model\Item::find('first',array('conditions' => array('number = ?',$args[1])));
          
          return $item->description;        
        }  
        
        //If we get here something went super wrong, throw an error;
        trigger_error('Something went horrendously wrong. Abandon ship.',E_USER_ERROR);
      
      });
      
      //Replace "detail" tags.    
      $input = $this->replace_tag($input,'detail',function($args) {
      
        if (strpos($args[2],'i--') === FALSE) { //This is simply an db id
          $item = \Model\Item::find($args[2]);
          
          return $item->$args[1];
        } else {
          $args[2] = substr($args[2],3);
          
          $item = \Model\Item::find('first',array('conditions' => array('number = ?',$args[2])));
          
          return $item->$args[1];        
        } 
        
        //If we get here something went super wrong, throw an error;
        trigger_error('Something went horrendously wrong. Abandon ship.',E_USER_ERROR);
         
      });
      
      return $input;
    
    }     
    
    function initialize_item_templates() {
        //First, load the item template
        $this->SC->load_library('Config');
        $item_template = $this->SC->Config->get_setting('item_template');
        if (!$item_template) $item_template = 'default';
        
        $template_file = sc_location("item_templates/$item_template.html");
        
        $raw_template = file_get_contents($template_file);
        
        if (!$raw_template) {
            trigger_error("Item template <strong>$template_file</strong> doesn't exist",E_USER_ERROR);
            return FALSE;
        }
        
        preg_match_all('/<!--TAG (.*?)\s*-->(.*?)<!--ENDTAG-->/s',$raw_template,$tags);      
        foreach ($tags[1] as $key => $tag) {
            $tag = explode(',',$tag);
            foreach ($tag as $this_tag) {
                $this->tags[$this_tag] = $tags[2][$key];
            }
        }
        
        preg_match_all('/<!--OPTION (.*?)\s*-->(.*?)<!--ENDOPTION-->/s',$raw_template,$option_templates);   
        foreach ($option_templates[1] as $key => $tag) {
            $this->option_templates[$tag] = $option_templates[2][$key];
        }                  
        
        
        return TRUE;
    }
    
    function run_item_templates($input) {                                
    
        $tags = $this->tags;
    
        $output = $this->replace_tag($input,'item',function($args) use ($tags) { 
            //Insert the item numbers into the raw template
            return str_replace('%i',$args[1],$tags['item']);
        });
        unset($tags['item']);        
        
        $SC = &$this->SC;
        
        $add_to_cart_callback = function($args) use ($tags,$SC) { 
            //Insert the item numbers into the raw template
            $add_to_cart_code = str_replace('%i',$args[1],$tags['add_to_cart']);  
            
            $add_to_cart_code = '<form class="sc_add_to_cart_form" method="POST" action="'.sc_ajax('add_item').'">'
                .$add_to_cart_code.
                '</form>';
            
            $add_to_cart_code = $SC->Page_loading->replace_tag($add_to_cart_code,'added_flag','<div class="sc_added_flag">Item Added!</div>');    
            $add_to_cart_code = $SC->Page_loading->replace_tag($add_to_cart_code,'add_button','<input type="image" src="'.sc_asset('button','add_to_cart').'" alt="Add To Cart" />');            
            $add_to_cart_code = $SC->Page_loading->replace_tag($add_to_cart_code,'qty_selection','<label class="sc_qty_label sc_label">qty:</label><input class="sc_qty_input" type="input" size=1 name="item_qty" value="1" />');
            
            return $add_to_cart_code;

        };
        
        $output = $this->replace_tag($input,'add_to_cart',$add_to_cart_callback);   
        $output = $this->replace_tag($input,'addtocart',$add_to_cart_callback); //Hack to allow the old tag >:(
        unset($tags['add_to_cart']);  
        
        if (count($tags)) {
            foreach($tags as $tag => $template) {
                if ($tag != 'item') {
                    $output = $this->replace_tag($output,$tag,function($args) use ($template){ 
                        return preg_replace('/\[\[detail\|(.*?)\]\]/','[[detail|$1|'.$args[1].']]',$template);
                    });                
                }
            }                        
        }
        
        $output = $this->replace_tag($output,'options',function($args) use ($SC) {
            return $SC->Page_loading->generate_options_code($args[1]); 
        });
        
        return $output;                            
    }    
    
    function generate_options_code($item) {    
    
        if (strpos($item,'i--')!==FALSE) {
            $item_num = substr($item,3);
          
            $item_id = \Model\Item::find('first',array(
                'conditions' => array('number = ?',$item_num),
                'select' => 'id'
            ));
            
            $item = $item_id->id;
        }        
    
        $item_options = \Model\Itemoption::find('all',array(
            'conditions' => array('itemid = ?',$item)
        ));
        
        $sorted_options = array();                     
        
        foreach ($item_options as $item_option) {
            
            $sorted_options[$item_option->cat][] = $item_option;
        }         
        unset($item_options);
        
        foreach ($sorted_options as $cat => $item_options) {
             
             if (count($sorted_options[$cat]) > 1) {
                $sorted_options[$cat]['code'] = $this->replace_tag($this->option_templates['multiple'],'cat',$cat);
                $this_option_code = '';
                foreach ($item_options as $item_option) {
                    $this_option_code .= "<option value=\"{$item_option->id}\">{$item_option->name}</option>".PHP_EOL;
                }
                
                $this_option_code = '<select name="options[]">'.$this_option_code.'</select>';
                
                $sorted_options[$cat]['code'] = $this->replace_tag($sorted_options[$cat]['code'],'options',$this_option_code);
                                
             } else {
                $item_option = $item_options[0];
                
                $sorted_options[$cat]['code'] = $this->replace_tag(
                    $this->option_templates['single'],
                    'detail',
                    function($args) use ($item_option) {
                        return $item_option->$args[1]; 
                    }
                );
                
                $sorted_options[$cat]['code'] = $this->replace_tag(
                    $sorted_options[$cat]['code'],
                    'checkbox',
                    "<input type=\"checkbox\" name=\"options[]\" value={$item_option->id} \>"                    
                );
             }
        }
        
        $option_code = '';
        
        foreach ($sorted_options as $sorted_option) {
            $option_code .= $sorted_option['code'].PHP_EOL;
        }              
        
        return $option_code;
        
          
    }    
    
    
    function load_drivers() {      
      $this->load_driver(array('cart','checkout','account'));      
    }
    
    function load_driver($type) {
        
        if (is_array($type)) {
        
            $return = TRUE;
        
            foreach($type as $this_type) {
                if (!$this->load_driver($this_type)) {
                    $return = FALSE;
                }                
            }
            
            return $return;
        }
    
    
        $type = strtolower($type);
    
        $driver = $this->SC->Config->get_setting("{$type}_driver");
        
        $driver_parent_file = "core/libraries/{$type}_drivers/".ucfirst($type)."_Driver.php";        
        
        if (file_exists($driver_parent_file)) {
            include_once $driver_parent_file;
        } else {
            trigger_error("Driver type \"$type\" doesn't exist");
            return FALSE;
        }
        
        $driver_file = "core/libraries/{$type}_drivers/$driver.php";        
        if (file_exists($driver_file)) { 
            include_once $driver_file;             
        } else {
            trigger_error("Driver \"$driver\" of type \"$type\" doesn't exist");
            return FALSE;
        }
        
        $namespaced_driver = ucfirst($type).'_Driver\\'.$driver;
        
        $driver_object = "{$type}_driver";
        
        $this->$driver_object = new $namespaced_driver;
        $this->loaded_drivers[$type] = $driver;                
        
    }
    
    function add_javascript($src,$script='') {
      if (is_array($src)) {
        $this->js[] = $src;
        return TRUE;
      }
      
      $this->js[] = array($src,$script);
    }  
    
    function insert_javascript($input) {
    
      $output = '';
      
      if ( ! count($this->js)) {
        return $input;
      }
      
      foreach ($this->js as $js) {
        $output .= "<script type='text/javascript' ".(($js[0])?"src='{$js[0]}'":"").">{$js[1]}</script>".PHP_EOL;
      }
      $output .= '</head>'.PHP_EOL;
      
      
      return str_ireplace('</head>',$output,$input);
       
    }    
    
  }
