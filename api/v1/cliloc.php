<?php
include_once 'inc/config.php';

if (!array_key_exists('id', $_REQ))
	return;

$id = preg_replace('/\D/', '', $_REQ['id']);

if (!$id)
	return;

$id = intval($id);

include_once 'inc/mongo.php';

$c = $md->cliloc_enu;
$data = $c->find(['_id' => $id],['_id' => false])->getNext()['text'];

header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=3600');
header('Vary: Accept-Encoding');
echo $data;
?>
