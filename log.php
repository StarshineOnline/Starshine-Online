<?php

if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

if( array_key_exists('type', $_POST) && array_key_exists('message', $_POST) )
{
	log_admin::log($_POST['type'], $_POST['message']);
}

?>