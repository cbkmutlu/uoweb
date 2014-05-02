<?php
include_once 'inc/config.php';
include_once 'inc/mongo.php';

$c = $md->shards;
$shards = $c->find([], ['_id' => false]);
if ($shards == NULL)
	return;

header('Content-Type: application/json');
header('Vary: Accept-Encoding');
echo json_encode(iterator_to_array($shards), JSON_PRETTY_PRINT);
?>
