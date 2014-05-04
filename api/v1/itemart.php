<?php
include_once 'inc/config.php';

if (!array_key_exists('id', $_REQ))
	return;

$id = intval($_REQ['id'], 0);

if (!is_numeric($id) || $id < 0 || $id > 65535)
	return;

$key = "itemart-$id";
$id = intval($id);

if (array_key_exists('hue', $_REQ)) {
	$hue = intval($_REQ['hue'], 0);

	if (is_numeric($hue) && $hue > 0 && $hue <= 3000) {
		$key .= "-$hue";
	} else {
		unset($hue);
	}
}

$rhost = parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_HOST);
$rport = parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PORT);
$rpass = parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PASS);

$rd = new Redis();
$rd->pconnect($rhost, $rport);
$rd->auth($rpass);

$png = $rd->get($key);

if (!$png) {
	include_once 'inc/mongo.php';

	$c = $md->itemdata;
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
			$x = imagesx($img);
			$y = imagesy($img);

			for($i = 0; $i < $x; $i++) {
				for($j = 0; $j < $y; $j++) {
					$c = imagecolorat($img, $i, $j);

					if ($c & 0xFF000000)
						continue;

					$c = $c & 0xFFFFFF;

					// Selective
					/*$r = ($c >> 16) & 0xFF;
					$g = ($c >> 8) & 0xFF;
					$b = ($c) & 0xFF;

					$red = ($r * 249 + 1014) >> 11;
					$green = ($g * 249 + 1014) >> 11;
					$blue = ($b * 249 + 1014) >> 11;

					// Normally R == G == B
					if ($red == $green || $red == $blue)
						$idx = $red;
					if ($green == $blue)
						$idx = $blue;

					if (isset($idx)) {*/
						// Hue All
						$idx = ((($c >> 16) & 0xFF) * 249 + 1014) >> 11;
						$color = $colors[$idx];
						//$col = imagecolorallocate($img, ($color >> 16) & 0xFF, ($color >> 8) & 0xFF, $color & 0xFF);
						imagesetpixel($img, $i, $j, $color & 0xFFFFFF);
					/*}*/
				}
			}

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
header('Cache-Control: public, max-age=3600');
header('Vary: Accept-Encoding');
header('Content-Type: image/png');
header("Content-Disposition: filename=$key.png");
echo $png;
?>
