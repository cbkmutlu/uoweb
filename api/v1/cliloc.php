<?php
include_once 'inc/config.php';

if (!array_key_exists('id', $_REQ))
	return;

$id = intval($_REQ['id'], 0);

if (!is_numeric($id) || $id < 0)
	return;

include_once 'inc/mongo.php';

$c = $md->cliloc_enu;
$data = $c->find(['_id' => $id],['_id' => false])->getNext()['text'];

header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=864000');
header('Vary: Accept-Encoding');
echo $data;
?>
