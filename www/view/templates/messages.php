<?php foreach(get_errors() as $error){ ?>
//h関数追加
  <p class="alert alert-danger"><span><?php print $error; ?></span></p>
<?php } ?>
<?php foreach(get_messages() as $message){ ?>
//h関数追加
  <p class="alert alert-success"><span><?php print $message; ?></span></p>
<?php } ?>
