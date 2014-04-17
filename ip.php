<?php
$ip = $_SERVER['REMOTE_ADDR'];
if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
  $a = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
  end($a);
  $ip = prev($a);
}
echo $ip;
?>
