<?php
class page
{
      // Defaults
      public $title='Untitled Page';
      public $body='';
      public $scripts = array();
      public $customscripts = array();
      public $stylesheets = array();
      public $customstyles = array();
      public $favicon = 'favicon.ico';
      public $head='';
      
      //Add JS script
      function addJScript($script,$customscript='')
      {
                 $this->scripts[] = array('scriptfile'=>$script, 'customscript'=>$customscript);
      }
      function addStyleSheet($sheet,$customstyle='') {
               if (!empty($sheet)) {
                 $this->stylesheets[] = $sheet;
               } elseif (!empty($customstyle)) {
                 $this->customstyles[]=$customstyle;
               }
      }


      //Generation Script
      function renderPage()
      {
               if (empty($this->stylesheets)&&empty($this->customstyles)) {
                 $this->stylesheets[]='style.css';
               }
               //Generate Opening Html
               echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                     <html xmlns="http://www.w3.org/1999/xhtml">
                     <head>
                     <link rel="shortcut icon" href="'.$this->favicon.'">
                     ';
               //Header
               foreach($this->scripts as $value) {
                 echo '<script type="text/javascript" '.(!empty($value['scriptfile']) ? 'src="'.$value['scriptfile'].'"' : '').'>'.$value['customscript'].'</script>'.PHP_EOL;
               }
               echo '<title>'.$this->title.'</title>'.PHP_EOL;
               foreach ($this->stylesheets as $value) {
                 echo '<link rel="stylesheet" type="text/css" href="'.$value.'" />'.PHP_EOL;
               }
               foreach ($this->customstyles as $value) {
                 echo '<style>';
                 echo $value;
                 echo '</style>'.PHP_EOL;
               }
               echo $this->head;
               echo '</head>';

               //Body
               echo '<body>';
               echo $this->body;
               echo '</body>';

               echo '</html>';
      }
}
?>
