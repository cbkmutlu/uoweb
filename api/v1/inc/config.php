<?php
	error_reporting(E_ALL & ~E_NOTICE);

	$_REQ = array();

	reset($_POST);
	while ( list($key,$data) = each($_POST) )
		$_REQ[$key] = $data;
	reset($_GET);
	while ( list($key,$data) = each($_GET) )
		$_REQ[$key] = $data;
?>
