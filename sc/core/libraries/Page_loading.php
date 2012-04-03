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
    
      if (is_array($tag)) {
        foreach($tag as $name => $content) {
            $input = $this->replace_tag($input,$name,$content);
        }
        
        return $input;
      }
      
      //Find the tag in the page
      if (preg_match_all("/\[\[($tag(\|[^(\]\])]+)*)\]\]/i",$input,$matches)) {
        if (is_callable($replacement)) {
          $function = $replacement;
          $replacement = array();
          
          foreach($matches[1] as $key => $match) {
            $match = explode('|',$match);
            
            foreach ($match as &$this_match) {
                if (strpos($this_match,'i--') !== FALSE) { //This is simply an db id
                  $this_match = substr($this_match,3);
                  
                  $item = \Model\Item::find('first',array('conditions' => array('number = ?',$this_match))); 
                        
                  $this_match = $item->id;      
                }    
            } 
            
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
      
      $tags = array(
        'name' => function($args) {

            $item = \Model\Item::find($args[1]);          
            return $item->name;        
        },
        
        'desc' => function($args) {
      
            $item = \Model\Item::find($args[1]);          
            return $item->description;      
        },
        
        'detail' => function($args) {
      
            $item = \Model\Item::find($args[2]);          
            return $item->$args[1];         
        }
      );
      
      //Replace "detail" tags.    
      $input = $this->replace_tag($input,$tags);
      
      return $input;
    
    }     
    
    function initialize_item_templates() {
        //First, load the item template
        $this->SC->load_library('Config');
        $item_template = $this->SC->Config->get_setting('item_template');
        if (!$item_template) $item_template = 'default';
        
        $template_file = "item_templates/$item_template.html";
        
        $raw_template = file_get_contents("item_templates/$item_template.html");
        
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
        
        preg_match_all('/<!--SPECIALTAG (.*?)\s*-->(.*?)<!--ENDSPECIALTAG-->/s',$raw_template,$special_tags);
        foreach ($special_tags[1] as $key => $tag) {
            $tag = explode(' ',$tag);    
            
            foreach ($tag as &$this_tag) {
                if (strpos($this_tag,'i--') !== FALSE) {
                  $this_tag = substr($this_tag,3);
                  
                  $item = \Model\Item::find('first',array('conditions' => array('number = ?',$this_tag))); 
                        
                  $this_tag = $item->id;      
                } 
            }
            
            $the_tags = preg_quote(implode('|',$tag));
            
            $this->special_tags[] = array(
                'regex' => "/\[\[$the_tags\]\]/",
                'template' => $special_tags[2][$key]
            );
        }                
        
        
        return TRUE;
    }
    
    function run_item_templates($input) {      
    
        $this->SC->load_library('Items');
    
        $SC = &$this->SC;                                         
    
        $tags = $this->tags;        
    
        $output = $this->replace_tag($input,'item',function($args) use ($tags,$SC) { 
            //Insert the item numbers into the raw template            
            return str_replace('%i',$item,$tags['item']);
        });
        unset($tags['item']);                        
        
        $add_to_cart_callback = function($args) use ($tags,$SC) { 
            //Insert the item numbers into the raw template
            $add_to_cart_code = str_replace('%i',$args[1],$tags['add_to_cart']);  
            
            $add_to_cart_code = '<form class="sc_add_to_cart_form sc_item_'.$args[1].'" method="POST" action="'.sc_ajax('add_item/'.$args[1]).'">'           
                .$add_to_cart_code.
                '</form>';
            
            $add_to_cart_code = $SC->Page_loading->replace_tag($add_to_cart_code,array(
                'message_area' => '<div class="sc_message_area" style="display:none;"></div>',
                'add_button' => '<input type="image" src="'.sc_asset('button','add_to_cart').'" alt="Add To Cart" />'
            )); 
                       
            $add_to_cart_code = $SC->Page_loading->replace_tag($add_to_cart_code,
                'qty_selection',
                (($SC->Items->item_flag($args[1],'hide_qty')) 
                    ? '<input class="sc_qty_input" type="hidden' 
                    : '<label class="sc_qty_label sc_label_right">qty:</label><input type="text'
                )
                .'" size=1 name="item_qty" value="'.
                (($min = $SC->Items->item_flag($args[1],'min'))
                    ? $min[1]
                    : 1
                )
                .'" />'
            );
            
            return $add_to_cart_code;
        };
        
        $output = $this->replace_tag($input,array(
            'add_to_cart' => $add_to_cart_callback,
            'addtocart' => $add_to_cart_callback //Hack to allow the old tag >:(
        ));
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
        
        //Run through and replace any special tags
        foreach ($this->special_tags as $special_tag) {     
            $output = preg_replace($special_tag['regex'],$special_tag['template'],$output);
        }        
        
        return $output;                            
    }    
    
    function generate_options_code($item) {    
        
        $this->SC->load_library('Items');
    
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
        
        $i = 0;
        foreach ($sorted_options as $cat => $item_options) {
             
             if (count($sorted_options[$cat]) > 1) {
                $sorted_options[$cat]['code'] = $this->replace_tag($this->option_templates['multiple'],'cat',$cat);
                $this_option_code = '';
                foreach ($item_options as $item_option) {
                    $this_option_code .= "<option value=\"{$item_option->id}\">{$item_option->name} (+\${$item_option->price})</option>".PHP_EOL;
                }
                
                $this_option_code = '<select name="options['.$i.']">'.$this_option_code.'</select>';
                
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
                    '<input type="'.(
                        ($this->SC->Items->option_flag($item_option->id,'req')) 
                        ? 'hidden'  
                        : 'checkbox'
                    )."\" name=\"options[$i]\" value={$item_option->id} />".
                    (($this->SC->Items->option_flag($item_option->id,'allow_qty')) ? "<input type=\"text\" name=\"option_qty[$i]\" class=\"sc_option_qty\" size=1 value=1 />" : '')
                );
             }
             $i++;
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
