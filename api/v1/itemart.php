<?php
include_once 'inc/config.php';

if (!array_key_exists('n', $_REQ))
	return;

$n = preg_replace('/\D/', '', $_REQ['n']);

if (!$n)
	return;

$n = intval($n);

include_once 'inc/mongo.php';

$c = $md->itemdata;
$data = $c->find(['_id' => $n],['_id' => false, 'png' => true])->getNext()['png'];

header('Vary: Accept-Encoding');
header('Content-Type: image/png');
header("Content-Disposition: filename=itemart-$id.png");
echo $data->bin;
?>
