<?php
include_once 'inc/config.php';

if (!array_key_exists('id', $_REQ))
	return;

$id = preg_replace('/\D/', '', $_REQ['id']);

if (!strlen($id))
	return;

$id = intval($id);

if (array_key_exists('hue', $_REQ)) {
	$hue = preg_replace('/\D/', '', $_REQ['hue']);
	if (!strlen($hue)) {
		unset($hue);
	} else {
		$hue = intval($hue);
	}
}

include_once 'inc/mongo.php';

$c = $md->itemdata;
$data = $c->find(['_id' => $id], ['_id' => false, 'png' => true])->getNext()['png'];

$png = $data->bin;

if (!$png)
	return;

if (isset($hue)) {
	$c = $md->hues;
	$colors = $c->find(['_id' => $hue], ['_id' => false, 'colors' => true])->getNext()['colors'];
	if ($colors) {
		$img = imagecreatefromstring($png);

		$x = imagesx($img);
		$y = imagesy($img);

		for($i = 0; $i < $x; $i++) {
			for($j = 0; $j < $y; $j++) {
				$c = imagecolorat($img, $i, $j);

				if (!$c)
					continue;

				$r = ($c >> 16) & 0x1F;
				$g = ($c >> 8) & 0x1F;
				$b = ($c) & 0x1F;

				if ($r == $g && $r == $b) {
					$color = $colors[$r];

					//$col = imagecolorallocate($img, $color >> 10, $color >> 5, $color);
					$col = imagecolorallocate($img, 255, 0, 0);
					imagesetpixel($img, $i, $j, $col);
				}
			}
		}
	}
}

header('Vary: Accept-Encoding');
header('Content-Type: image/png');
header("Content-Disposition: filename=itemart-$id.png");

if ($img)
	imagepng($img);
else
	echo $png;
?>
