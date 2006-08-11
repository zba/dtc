<?php

header ("Content-type: image/png");

$use_img_cache = "yes";
function MENU_TREE_calc_cache_path(){
	$conf_img_cache_path = "../imgcache";

	$cacheimg_filename = $_REQUEST["lang"] . "_" . $_REQUEST["text"] . $_REQUEST["color"] . $_REQUEST["link"] . ".png";
	$cacheimg_filename = str_replace("/","_",$cacheimg_filename);
	$imgfile_path = $conf_img_cache_path . "/" . $cacheimg_filename;
	return $imgfile_path;
}
$file_cache_path = MENU_TREE_calc_cache_path();

// Params are:
// $text -> Text to be drawn
// $color -> The color to use (0 = normal with shadow, 1 = selected with no shadow)
// $link -> The current selected link address for this picture like gplhost.com/mailbox
// $sign -> The current sign to use for drawing (minus, tree, endtree, vline, hline, plus or none)
//          Example value can be: tree/hline

$selected = explode("/",$_REQUEST["link"]);
$color=$_REQUEST["color"];
$nbr_recursion = sizeof($selected)-1;
$recurs_x_decal = 16;
$curent_x_decal = $recurs_x_decal*$nbr_recursion;

$im_width = 220;
$im_height = 32;
$im_break = 6;
$im_backb = $im_height-($im_break+1);

$gfx_start_pos = $curent_x_decal + 16;

$im = ImageCreate ($im_width, $im_height) or die ("Cannot Initialize new GD image stream");
if($color == 0){
	$darkblue_color = ImageColorAllocate ($im, 0xCC, 0xCC, 0xCC);
}else{
	$darkblue_color = ImageColorAllocate ($im, 0xCC, 0xCC, 0xFF);
}
$lightblue_color = ImageColorAllocate ($im, 220, 220, 255);
//$background_color = ImageColorAllocate ($im, 51, 255, 204);

$black_color = ImageColorAllocate ($im, 0, 0, 0);

if($_REQUEST["lang"] == "zh"){
//	include("gb2utf.php");
//	$utf = gb2utf8($_REQUEST["text"]);
	$utf = iconv("GB2312","UTF-8",$_REQUEST["text"]);
	if($color == 0){
		$text_color = ImageColorAllocate ($im, 0x00, 0x00, 0x0);
		$txt_x_pos = 25+16;
		$txt_y_pos = 22;
	}else{
		$text_color = ImageColorAllocate ($im, 0x44, 0x44, 0xCC);
		$txt_x_pos = 26+16;
		$txt_y_pos = 23;
	}
	imagettftext ( $im, 11, 0, $gfx_start_pos+$txt_x_pos, $txt_y_pos, $text_color, "/usr/share/dtc/client/inc/ukai.ttf", $utf );
//	ImageString ($im, $font, $gfx_start_pos+$txt_x_pos, $txt_y_pos,  $_REQUEST["text"], $text_color);
}else if($_REQUEST["lang"] == "pl"){
	$utf= iconv("ISO-8859-2","UTF-8",$_REQUEST["text"]);
	$font = 2;
	if($color == 0){
		$text_color = ImageColorAllocate ($im, 0x00, 0x00, 0x0);
		$txt_x_pos = 25+16;
		$txt_y_pos = 22;
//		ImageString ($im, $font, $gfx_start_pos+$txt_x_pos, $txt_y_pos+1,  $_REQUEST["text"], $black_color);
	}else if($color == 1){
		$text_color = ImageColorAllocate ($im, 0x44, 0x44, 0xCC);
		$txt_x_pos = 26+16;
		$txt_y_pos = 23;
	}
	imagettftext ( $im, 9, 0, $gfx_start_pos+$txt_x_pos, $txt_y_pos, $text_color, "/usr/share/dtc/client/inc/verdana.ttf", $utf );
}else{
	$font = 2;
	if($color == 0){
		$text_color = ImageColorAllocate ($im, 0x00, 0x00, 0x0);
		$txt_x_pos = 25+16;
		$txt_y_pos = 22;
//		ImageString ($im, $font, $gfx_start_pos+$txt_x_pos, $txt_y_pos+1,  $_REQUEST["text"], $black_color);
	}else if($color == 1){
		$text_color = ImageColorAllocate ($im, 0x44, 0x44, 0xCC);
		$txt_x_pos = 26+16;
		$txt_y_pos = 23;
	}
	imagettftext ( $im, 9, 0, $gfx_start_pos+$txt_x_pos, $txt_y_pos, $text_color, "/usr/share/dtc/client/inc/arial.ttf", $_REQUEST["text"] );
//	ImageString ($im, $font, $gfx_start_pos+$txt_x_pos, $txt_y_pos,  $_REQUEST["text"], $text_color);
}

//ImageString ($im, 3, $gfx_start_pos+200, 4,  "$_REQUEST["link"]", $text_color);


// Draw the black border
$alpha_color = imagecolorresolvealpha ( $im, 0, 0, 0, 127);
$black_border = array( 	  $gfx_start_pos+0,           $im_break,
						  $gfx_start_pos+$im_break,   0,
						  $im_width-1, 0,
						  $im_width-1, $im_height-1,
						  $gfx_start_pos+$im_break,   $im_height-1,
						  $gfx_start_pos+0,           $im_backb);

ImagePolygon ( $im, $black_border, $im_break, $black_color);
ImagePolygon ( $im, $black_border, $im_break, $alpha_color);

// Draw the light part of the 3D effect
ImageLine( $im, $gfx_start_pos+$im_break, $im_height-2, $gfx_start_pos+1, $im_backb, $lightblue_color);
ImageLine( $im, $gfx_start_pos+1, $im_backb, $gfx_start_pos+1, $im_break, $lightblue_color);
ImageLine( $im, $gfx_start_pos+1, $im_break, $gfx_start_pos+$im_break, 1, $lightblue_color);
ImageLine( $im, $gfx_start_pos+$im_break, 1, $gfx_start_pos+$im_width-2, 1, $lightblue_color);

// Draw the dark part of the 3D effect
ImageLine( $im, $im_width-2, 2, $im_width-2, $im_height-2, $darkblue_color);
ImageLine( $im, $im_width-2, $im_height-2, $gfx_start_pos+7, $im_height-2, $darkblue_color);

// Draw the transparency polygon...
$alpha_array = array( $gfx_start_pos+0,0,
					$gfx_start_pos+$im_break-1,0,
					$gfx_start_pos+0,$im_break-1);
imagefilledpolygon ( $im, $alpha_array, 3, $alpha_color);

$alpha_array = array( 	$gfx_start_pos+0,$im_height-1,
						$gfx_start_pos+0,$im_height-$im_break,
						$gfx_start_pos+$im_break-1,$im_height-1);
imagefilledpolygon ( $im, $alpha_array, 3, $alpha_color);

$alpha_array = array( 	0,0,
						0,$im_height,
						$gfx_start_pos,$im_height,
						$gfx_start_pos,0,
						0,0);
imagefilledpolygon ( $im, $alpha_array, 3, $alpha_color);
$alpha_array = array( 	0,0,
						$gfx_start_pos-1,0,
						$gfx_start_pos-1,$im_height,
						0,$im_height,
						0,0);
imagefilledpolygon ( $im, $alpha_array, 3, $alpha_color);


// Draw the polygon for the plus or minus sign
$im_hori_center = ($im_height / 2)-1;
$im_start_point = 2;
$im_half_size = 5;
$im_sign_space = 0;
$im_tree_adjust = 11;

function makeSquare($cellnum){
	global $im;
	global $im_start_point;
	global $im_hori_center;
	global $im_half_size;
	global $im_sign_space;
	global $black_color;
	global $recurs_x_decal;

	$plus_array = array(
$recurs_x_decal*$cellnum+						$im_start_point,$im_hori_center - $im_half_size,
$recurs_x_decal*$cellnum+						$im_start_point+($im_half_size*2),$im_hori_center - $im_half_size,
$recurs_x_decal*$cellnum+						$im_start_point+($im_half_size*2),$im_hori_center + $im_half_size,
$recurs_x_decal*$cellnum+						$im_start_point,$im_hori_center + $im_half_size
						);
	ImagePolygon ( $im, $plus_array, 4, $black_color);

	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_sign_space,$im_hori_center, $recurs_x_decal*$cellnum+ $im_start_point+($im_half_size*2)-$im_sign_space, $im_hori_center, $black_color);
}

function makeMinus($cellnum){
	global $im;
	global $im_start_point;
	global $im_hori_center;
	global $im_half_size;
	global $im_sign_space;
	global $black_color;
	global $im_height;
	global $recurs_x_decal;

	makeSquare($cellnum);
	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_hori_center+$im_half_size, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_height, $black_color);
}
function makePlus($cellnum){
	global $im;
	global $im_start_point;
	global $im_hori_center;
	global $im_half_size;
	global $im_sign_space;
	global $black_color;
	global $recurs_x_decal;

	makeSquare($cellnum);
	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_hori_center-($im_half_size-$im_sign_space), $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_hori_center+($im_half_size-$im_sign_space), $black_color);
}

function makeTree($cellnum){
	global $im;
	global $im_start_point;
	global $im_hori_center;
	global $im_half_size;
	global $im_sign_space;
	global $black_color;
	global $im_height;
	global $recurs_x_decal;
	global $im_tree_adjust;

	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, 0, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_height, $black_color);
	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_half_size+$im_tree_adjust, $recurs_x_decal*$cellnum+ $im_start_point+($im_half_size*2), $im_half_size+$im_tree_adjust, $black_color);
}

function makeEndTree($cellnum){
	global $im;
	global $im_start_point;
	global $im_hori_center;
	global $im_half_size;
	global $im_sign_space;
	global $black_color;
	global $im_height;
	global $recurs_x_decal;
	global $im_tree_adjust;

	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, 0, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_height/2, $black_color);
	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_half_size+$im_tree_adjust, $recurs_x_decal*$cellnum+ $im_start_point+($im_half_size*2), $im_half_size+$im_tree_adjust, $black_color);
}

function makeHline($cellnum){
	global $im;
	global $im_start_point;
	global $im_hori_center;
	global $im_half_size;
	global $im_sign_space;
	global $black_color;
	global $im_height;
	global $recurs_x_decal;

	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point-6+$im_sign_space,$im_hori_center+1, $recurs_x_decal*$cellnum+2+ $im_start_point+($im_half_size*2)-$im_sign_space, $im_hori_center+1, $black_color);
}

function makeVline($cellnum){
	global $im;
	global $im_start_point;
	global $im_hori_center;
	global $im_half_size;
	global $im_sign_space;
	global $black_color;
	global $im_height;
	global $recurs_x_decal;

	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, 0, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_height, $black_color);
}

$array_of_sign = explode("/",$_REQUEST["link"]);

for($i=0;$i<sizeof($array_of_sign);$i++){
	// Draw the plus sign
	if($array_of_sign[$i]=="plus"){
		makePlus($i);
	}else if($array_of_sign[$i]=="minus"){
		makeMinus($i);
	}else if($array_of_sign[$i] == "tree"){
		makeTree($i);
	}else if($array_of_sign[$i] == "endtree"){
		makeEndTree($i);
	}else if($array_of_sign[$i] == "hline"){
		makeHline($i);
	}else if($array_of_sign[$i] == "vline"){
		makeVline($i);
	}
}
$icon_x = $gfx_start_pos+$txt_x_pos - 19;
// Select the icon according to the link
$addrlink = explode("/",$_REQUEST["addrlink"]);
if(sizeof($addrlink) == 1){
	switch($addrlink[0]){
	case "myaccount":
		$icon_im = imagecreatefrompng("my-account.png");
		break;
	case "reseller":
		$icon_im = imagecreatefrompng("reseller.png");
		break;
	case "password":
		$icon_im = imagecreatefrompng("password.png");
		break;
	case "database":
		$icon_im = imagecreatefrompng("databases.png");
		break;
	case "help":
		$icon_im = imagecreatefrompng("help.png");
		break;
	default:
		$icon_im = imagecreatefrompng("domains.png");
		break;
	}
	if(substr($addrlink[0],0,3) == "vps"){
		$icon_im = imagecreatefrompng("virtual-server.png");
	}
}else{
	switch($addrlink[1]){
	case "stats":
		$icon_im = imagecreatefrompng("stats.png");
		break;
	case "subdomains":
		$icon_im = imagecreatefrompng("subdomains.png");
		break;
	case "ftp-accounts":
		$icon_im = imagecreatefrompng("ftp-accounts.png");
		break;
	case "ssh-accounts":
		$icon_im = imagecreatefrompng("ssh-accounts.png");
		break;
	case "adddomain":
		$icon_im = imagecreatefrompng("adddomain.png");
		break;
	case "nickhandles":
		$icon_im = imagecreatefrompng("nickhandles.png");
		break;
	case "whois":
		$icon_im = imagecreatefrompng("nickhandles.png");
		break;
	case "nameservers":
		$icon_im = imagecreatefrompng("nameservers.png");
		break;
	case "mailboxs":
		$icon_im = imagecreatefrompng("mailboxs.png");
		break;
	case "dns":
		$icon_im = imagecreatefrompng("nameservers.png");
		break;
	case "package-installer":
		$icon_im = imagecreatefrompng("package-installer.png");
		break;
	case "mailing-lists":
		$icon_im = imagecreatefrompng("mailing-lists.png");
		break;
	default:
	}
}
$icon_x -= 16;
if($color == 1)
	$icon_y = 3;
else
	$icon_y = 2;
imagecopy($im,$icon_im, $icon_x, $icon_y, 0, 0, 28, 28);

// Save file if not found
if(!file_exists($file_cache_path) && $use_img_cache== "yes"){
	ImagePng($im,$file_cache_path);
}

ImagePng ($im);
ImageDestroy($im);

?>
