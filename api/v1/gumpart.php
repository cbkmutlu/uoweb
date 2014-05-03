<?php
include_once 'inc/config.php';

if (!array_key_exists('id', $_REQ))
	return;

$id = preg_replace('/\D/', '', $_REQ['id']);

if (!strlen($id))
	return;

$id = intval($id);

include_once 'inc/mongo.php';

$c = $md->gumpart;
$data = $c->find(['_id' => $id],['_id' => false])->getNext()['png'];

header('Vary: Accept-Encoding');
header('Content-Type: image/png');
header("Content-Disposition: filename=gumpart-$id.png");
echo $data->bin;
?>
