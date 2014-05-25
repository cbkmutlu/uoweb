<?php
include_once 'inc/config.php';

if (!array_key_exists('id', $_REQ))
	return;

$id = intval($_REQ['id'], 0);

if ($id < 0 || $id > 65535)
	return;

$key = "texture-$id";

if (array_key_exists('hue', $_REQ)) {
	$ihue = intval($_REQ['hue'], 0);

	$grayonly = ($ihue & 0x8000) != 0;
	$hue = $ihue & 0x7FFF;

	if ($hue > 0 && $hue <= 3000) {
		$key .= "-$ihue";
	} else {
		unset($hue);
	}
}

include_once 'inc/redis.php';

$png = $rd->get($key);

if (!$png) {
	include_once 'inc/mongo.php';

	$c = $md->textures;
	$data = $c->find(['_id' => $id], ['_id' => false, 'png' => true])->getNext()['png'];

	$png = $data->bin;

	if (!$png)
		return;

	if ($hue) {
		$c = $md->hues;
		$colors = $c->find(['_id' => $hue], ['_id' => false, 'rgb' => true])->getNext()['rgb'];
		if ($colors) {
			$img = imagecreatefromstring($png);
			imagesavealpha($img, true);

			include_once 'inc/hue.php';

			if ($grayonly)
				HuePartial($img, $colors);
			else
				HueAll($img, $colors);

			ob_start();
			imagepng($img);
			$png = ob_get_contents();
			ob_end_clean();
			$rd->set($key, $png);
		} else {
			$rd->set($key, $png);
		}
	} else {
		$rd->set($key, $png);
	}
}

header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=864000');
header('Vary: Accept-Encoding');
header('Content-Type: image/png');
header("Content-Disposition: filename=$key.png");
echo $png;
?>
