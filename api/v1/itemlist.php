<?php
include_once 'inc/config.php';

if (!array_key_exists('id', $_REQ))
	return;

$id = intval($_REQ['id'], 0);

if (!is_numeric($id) || $id < 0 || $id > 65535)
	return;

$count = 10;

if (array_key_exists('count', $_REQ)) {
	$count = intval($_REQ['count'], 0);

	if (!is_numeric($count) || $count <= 0)
		$count = 10;

	if ($count > 50)
		$count = 50;
}

$key = "itemlist-$id-$count";

$rhost = parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_HOST);
$rport = parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PORT);
$rpass = parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PASS);

$rd = new Redis();
$rd->pconnect($rhost, $rport);
$rd->auth($rpass);

$itemlist = $rd->get($key);

if (!$itemlist) {
	include_once 'inc/mongo.php';

	$c = $md->itemdata;
	$data = $c->find(['_id' => ['$gte' => $id]], ['png' => false];
	$data->sort(['_id' => 1]);
	$data->limit($count);

	$itemlist = json_encode(iterator_to_array($data));
	$rd->set($key, $itemlist);
}

header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=3600');
header('Vary: Accept-Encoding');
header('Content-Type: application/javascript');
echo $itemlist;
?>
