<?php
$ip = $_SERVER['REMOTE_ADDR'];
if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
  $ip = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
}
echo $ip;
echo $_SERVER['HTTP_X_FORWARDED_FOR'];
?>
