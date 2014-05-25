<?php
include_once 'inc/config.php';

if (!array_key_exists('id', $_REQ))
	return;

$id = intval($_REQ['id'], 0);

if ($id < 0 || $id > 65535)
	return;

$key = "itemdata-$id";

include_once 'inc/redis.php';

$itemdata = $rd->get($key);

if (!$itemdata) {
	include_once 'inc/mongo.php';

	$c = $md->itemdata;
	$data = $c->find(['_id' =>  $id], ['png' => false])->getNext();

	$itemdata = json_encode($data);
	$rd->set($key, $itemdata);
}

header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=3600');
header('Vary: Accept-Encoding');
header('Content-Type: application/javascript');
echo $itemdata;
?>
