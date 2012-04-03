<?php
//Error Handler
function throw_error($levels,$message,$file,$line,$context) {
    
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

register_shutdown_function(function() {  

  chdir(dirname(dirname(__DIR__)));

  global $CONFIG;
  global $ERRORS;
  global $start_time;
  
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
    <?=xdebug_get_profiler_filename();?>
  <?php endif;
});
