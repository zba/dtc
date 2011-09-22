<?php

$panel_type = "none";
require_once("../shared/autoSQLconfig.php");
require_once("$dtcshared_path/dtc_lib.php");

require_once("authme.php");

function echo_the_headers($filesize,$content_type_prim,$content_type_sec){
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	// always modified
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	// HTTP/1.0
	header("Pragma: no-cache");

	header("Content-Type: $content_type_prim/$content_type_sec");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: $filesize");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public", false);
	header("Expires: 0");
}

$q = "SELECT * FROM tik_attach WHERE id='".$_REQUEST["id"]."'";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1){
	die("Attachement not found!");
}
$a = mysql_fetch_array($r);
$content_type_prim = $a["ctype_prim"];
$content_type_sec = $a["ctype_sec"];
$binary = pack("H*" , $a["datahex"]);

if($content_type_prim == "image" && isset($_REQUEST["minipic"]) && $_REQUEST["minipic"] == "yes"
		&& ($content_type_sec == "jpeg" || $content_type_sec == "gif" || $content_type_sec == "png")){
	$fname = tempnam("/tmp","dtc_thumb");
	file_put_contents($fname,$binary);
	$thumb = new Imagick($fname);
	$thumb->thumbnailImage(80,null);
	$thumb->writeImage($fname);
	$filesize = filesize($fname);
	$binary = file_get_contents($fname);
	unlink($fname);
}else{
	$filesize = strlen($binary);
}
echo_the_headers($filesize,$content_type_prim,$content_type_sec);

// Date in the past
echo $binary;

?>
