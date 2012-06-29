<?php

/**
 * Error Handling
 *
 * This file contains the throw_error function and the registers SC's
 * shutdown function
 *
 * @package Debug
 */

/**
 * Error Handler
 *
 * Throws an error, either dumping it at the end of the script, or storing
 * it in a file to be viewed by the store administrator
 *
 * @return null
 *
 * @param int $levels A binary integer containing the levels of error to throw
 * @param string $message The error message
 * @param string $file The file containing the error
 * @param string $line The line the error is on
 * @param string $context The context of the error
 *
 */
function throw_error($levels,$message,$file,$line,$context) {

    if (! error_reporting() ) return;
    
    global $ERRORS;
    global $CONFIG;
    
    $levels = strrev(decbin($levels));
    $level = $CONFIG['ERROR_LEVELS'];
    
    foreach($CONFIG['ERROR_LEVELS'] as $key => $this_level) {
      if (!isset($levels[$key]) or !$levels[$key]) {
        unset($level[$key]);
      }
    }
    
    $level = implode($level,', ');
    
    $ERRORS[] = array(
      'level' => $level,
      'message' => $message,
      'file' => $file,
      'line' => $line,
      'context' => $context
    );
    
    if ($CONFIG['LOG_ERRORS']) {
      $log_file = fopen($CONFIG['LOG_FILE_LOCATION'],'a');
      fwrite($log_file,date('c')." $level:".PHP_EOL."\t$message".PHP_EOL."\tin file $file on line $line.".PHP_EOL);
    }
    
    return NULL;   
}

set_error_handler('throw_error');

/**
 * This is the shutdown function closure
 */
register_shutdown_function(function() {  

  chdir(dirname(dirname(__DIR__)));

  global $CONFIG;
  global $ERRORS;
  global $SC;
  global $start_time;
  
  if ($CONFIG['DUMP_SESSION'] || ($CONFIG['SHOW_ERRORS'] && count($ERRORS))) : ?>  
<div id="sc_debug_controls">&Dagger;</div>
<div id="sc_debug_footer">    
    <?php
    if (isset($_SESSION) && $CONFIG['DUMP_SESSION']) {    
        require 'core/debug/print_session.php';
        echo 'exectime: '.round((microtime(true) - $start_time)*pow(10,3),2).' msecs';
    }
  
        if ($CONFIG['SHOW_ERRORS']) : ?>
            <?php foreach ($ERRORS as $error) : ?>
    <div class="php_error">
        <strong><?=$error['level']?>:</strong> <?=$error['message']?> in file <strong><?=$error['file']?></strong> at line <strong><?=$error['line']?></strong>.
        <?=($CONFIG['SHOW_ERROR_CONTEXT'])?'<br />'.$error['context']:''?>
    </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php if (!property_exists($SC,'Page_loading') && !SIMPLECART_IS_IN_CP) : ?>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <?php endif ?>
    <script type="text/javascript">
        $(document).ready(function(){           
            $('#sc_debug_footer').hide();                 
            $('#sc_debug_controls').click(function() {
                $('#sc_debug_footer').slideToggle(function() {
                    if ($(this).is(':visible')) {
                        $(document).scroll(function() {
                            var vis_height = $(document).height() - $(window).height() - 10;
                            if ($(this).scrollTop() > vis_height && $('#sc_debug_footer').is(':visible')) {
                                $('#sc_debug_footer').slideUp('fast');        
                            } else if ($(this).scrollTop() < vis_height && ! $('#sc_debug_footer').is(':visible')) {                                
                                $('#sc_debug_footer').slideDown('fast');        
                            }
                        });
                    } else {
                        $(document).unbind('scroll');
                    }   
                });                                   
            });
        });
    </script>
</div>
    <?php endif; ?>
<?
});
