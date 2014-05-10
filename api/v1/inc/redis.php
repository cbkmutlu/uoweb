<?php
$rhost = parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_HOST);
$rport = parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PORT);
$rpass = parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PASS);

$rd = new Redis();
$rd->pconnect($rhost, $rport);
$rd->auth($rpass);
?>