<?php
function HuePartial($img, $colors) {
    $x = imagesx($img);
	$y = imagesy($img);

	for($i = 0; $i < $x; $i++) {
		for($j = 0; $j < $y; $j++) {
			$c = imagecolorat($img, $i, $j);
			if ($c & 0xFF000000)
				continue;

			$c = $c & 0xFFFFFF;

			$idx = -1;

			$r = ($c >> 16) & 0xFF;
			$g = ($c >> 8) & 0xFF;
			$b = ($c) & 0xFF;

			$red = ($r * 249 + 1014) >> 11;
			$green = ($g * 249 + 1014) >> 11;
			$blue = ($b * 249 + 1014) >> 11;

			if ($red == $green && $red == $blue)
				$idx = $red;

			if ($idx >= 0) {
				$color = $colors[$idx];
				//$col = imagecolorallocate($img, ($color >> 16) & 0xFF, ($color >> 8) & 0xFF, $color & 0xFF);
				imagesetpixel($img, $i, $j, $color & 0xFFFFFF);
			}
		}
	}
}

function HueAll($img, $colors) {
    $x = imagesx($img);
	$y = imagesy($img);

	for($i = 0; $i < $x; $i++) {
		for($j = 0; $j < $y; $j++) {
			$c = imagecolorat($img, $i, $j);
			if ($c & 0xFF000000)
				continue;

			$c = $c & 0xFFFFFF;

			$idx = -1;

			$idx = ((($c >> 16) & 0xFF) * 249 + 1014) >> 11;

			if ($idx >= 0) {
				$color = $colors[$idx];
				//$col = imagecolorallocate($img, ($color >> 16) & 0xFF, ($color >> 8) & 0xFF, $color & 0xFF);
				imagesetpixel($img, $i, $j, $color & 0xFFFFFF);
			}
		}
	}
}
?>