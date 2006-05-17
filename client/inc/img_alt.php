<?php
/**
 * @package DTC
 * @author seeb <seeb@seeb.net.pl>
 * @abstract alternative image creation from image file. File based on oryginal img.php by Thomas Goirand <thomas@goirand.fr>
 * @version $Id: $
 * @param $text, $color ,$link $sign
 *
 * $text -> Text to be drawn
 * $color -> The color to use (0 = normal with shadow, 1 = selected with no shadow)
 * $link -> The current selected link address for this picture like gplhost.com/mailbox
 * $sign -> The current sign to use for drawing (minus, tree, endtree, vline, hline, plus or none)
 *				Example value can be: tree/hline
 * @see img.php
 * $Log: img_alt.php,v $
 * Revision 1.3  2006/05/17 12:58:01  seeb
 * Adding to file cvs user log as comment
 * Please comment any change(s).
 * Tnx
 * /seeb
 *
 * Revision 1.2  2006/05/16 22:37:27  seeb
 * Adding to file cvs user log as comment
 * Please comment any change(s).
 * Tnx
 */

header ("Content-type: image/png");

$use_img_cache = "yes"; /// bylo yes
function MENU_TREE_calc_cache_path(){
	$conf_img_cache_path = "../imgcache";

	$cacheimg_filename = $_REQUEST["lang"] . "_" . $_REQUEST["text"] . $_REQUEST["color"] . $_REQUEST["link"] . ".png";
	$cacheimg_filename = str_replace("/","_",$cacheimg_filename);
	$imgfile_path = $conf_img_cache_path . "/" . $cacheimg_filename;
	return $imgfile_path;
}
$file_cache_path = MENU_TREE_calc_cache_path();

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

// select image to create button
if ($_REQUEST["link"]=="minus" or $_REQUEST["link"]=="plus" or $_REQUEST["link"]=="none"){
$im = ImageCreateFromPng("imglong.png") or die ("Nie mozna znalezc pliku");
}
else{
$im = ImageCreateFromPng("imgshort.png") or die ("Nie mozna znalezc pliku");
}

imageSaveAlpha($im, true);
ImageAlphaBlending($im, false);

if($color == 0){
	$darkblue_color = ImageColorAllocate ($im, 0xCC, 0xCC, 0xCC);
}else{
	$darkblue_color = ImageColorAllocate ($im, 0xCC, 0xCC, 0xFF);
}
$lightblue_color = ImageColorAllocate ($im, 220, 220, 255);
$background_color = imagecolorallocatealpha($im, 220, 220, 220, 127);
$black_color = ImageColorAllocate ($im, 0, 0, 0);

if($_REQUEST["lang"] == "zh"){
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
}else if($_REQUEST["lang"] == "pl"){
	$utf= iconv("ISO-8859-2","UTF-8",$_REQUEST["text"]);
	$font = 2;
	if($color == 0){
		$text_color = ImageColorAllocate ($im, 0x00, 0x00, 0x0);
		$txt_x_pos = 25+16;
		$txt_y_pos = 22;
	}else if($color == 1){
		$text_color = ImageColorAllocate ($im, 0x00, 0x00, 0xCC);
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
	}else if($color == 1){
		$text_color = ImageColorAllocate ($im, 0x44, 0x44, 0xCC);
		$txt_x_pos = 26+16;
		$txt_y_pos = 23;
	}
	imagettftext ( $im, 9, 0, $gfx_start_pos+$txt_x_pos, $txt_y_pos, $text_color, "/usr/share/dtc/client/inc/arial.ttf", $_REQUEST["text"] );
}


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