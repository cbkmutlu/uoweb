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

$c = $md->gumpart;
$data = $c->find(['_id' => $id], ['_id' => false, 'png' => true])->getNext()['png'];

$png = $data->bin;

if (!$png)
	return;

if (isset($hue)) {
	$c = $md->hues;
	$colors = $c->find(['_id' => $hue], ['_id' => false, 'rgb' => true])->getNext()['rgb'];
	if ($colors) {
		$img = imagecreatefromstring($png);
		imagesavealpha($img, true);
		$x = imagesx($img);
		$y = imagesy($img);

		for($i = 0; $i < $x; $i++) {
			for($j = 0; $j < $y; $j++) {
				$c = imagecolorat($img, $i, $j);

				if ($c & 0xFF000000)
					continue;

				$c = $c & 0xFFFFFF;

				$r = ($c >> 16) & 0xFF;
				$g = ($c >> 8) & 0xFF;
				$b = ($c) & 0xFF;

				$red = ($r * 249 + 1014) >> 11;
				$green = ($g * 249 + 1014) >> 11;
				$blue = ($b * 249 + 1014) >> 11;

				if ($red == $green || $red == $blue)
					$idx = $red;
				if ($green == $blue)
					$idx = $blue;

				if (isset($idx)) {
					$color = $colors[$idx];
					//$col = imagecolorallocate($img, ($color >> 16) & 0xFF, ($color >> 8) & 0xFF, $color & 0xFF);
					imagesetpixel($img, $i, $j, $color & 0xFFFFFF);
				}
			}
		}
	}
}

header('Access-Control-Allow-Origin: *');
header('Vary: Accept-Encoding');
header('Content-Type: image/png');
header("Content-Disposition: filename=gumpart-$id.png");

if ($img)
	imagepng($img);
else
	echo $png;
?>
