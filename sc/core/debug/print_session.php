<div id="sc_session_dump">Session: <a href="<?=sc_location('/core/debug/delete_session.php')?>">(Clear)</a><br/>
  <?php foreach ($_SESSION as $key => $value) : ?>
    <?=$key?> => <?php better_print_r($value) ?> <a href="<?=sc_location('/core/debug/delete_session.php?key='.$key)?>">[x]</a><br />
  <?php endforeach ?>
</div> <!--#sc_session_dump-->
