<?php
header ("Content-type: image/png");

$selected = explode("/",$link);
$nbr_recursion = sizeof($selected)-1;
$recurs_x_decal = 16;
$curent_x_decal = $recurs_x_decal*$nbr_recursion;

$im_width = 190;
$im_height = 20;
$im_break = 6;
$im_backb = $im_height-($im_break+1);

$back_col_r = 0;
$back_col_g = 100;
$back_col_b = 0;

$gfx_start_pos = $curent_x_decal + $recurs_x_decal;

$im = imagecreatetruecolor ($im_width, $im_height) or die ("Cannot Initialize new GD image stream");
$shade_to = imagecolorexactalpha ($im, 1, 255, 255,255);

ImageAlphaBlending($im, false);

$back_array = array( 	0,0,
						$im_width,0,
						$im_width,$im_height,
						0,$im_height,
						0,0);
imagefilledpolygon ( $im, $back_array, 5, $shade_to);

$back_color = ImageColorAllocate ($im, $back_col_r, $back_col_g, $back_col_b);

$darkblue_color = ImageColorAllocate ($im, 110, 110, 135);
$lightblue_color = ImageColorAllocate ($im, 180, 180, 220);
//$background_color = ImageColorAllocate ($im, 51, 255, 204);

$black_color = ImageColorAllocate ($im, 0, 0, 0);
/*
// Draw the black border
$black_border = array( 	  $gfx_start_pos+0,           $im_break,
						  $gfx_start_pos+$im_break,   0,
						  $im_width-1, 0,
						  $im_width-1, $im_height-1,
						  $gfx_start_pos+$im_break,   $im_height-1,
						  $gfx_start_pos+0,           $im_backb);

ImagePolygon ( $im, $black_border, $im_break, $black_color);

// Draw the light part of the 3D effect
ImageLine( $im, $gfx_start_pos+$im_break, $im_height-2, $gfx_start_pos+1, $im_backb, $lightblue_color);
ImageLine( $im, $gfx_start_pos+1, $im_backb, $gfx_start_pos+1, $im_break, $lightblue_color);
ImageLine( $im, $gfx_start_pos+1, $im_break, $gfx_start_pos+$im_break, 1, $lightblue_color);
ImageLine( $im, $gfx_start_pos+$im_break, 1, $gfx_start_pos+$im_width-2, 1, $lightblue_color);

// Draw the dark part of the 3D effect
ImageLine( $im, $im_width-2, 2, $im_width-2, $im_height-2, $darkblue_color);
ImageLine( $im, $im_width-2, $im_height-2, $gfx_start_pos+7, $im_height-2, $darkblue_color);
*/

// Draw the background to no color trancparency effect (shade...)
//$pixel_start = $gfx_start_pos+($im_height/2);
$pixel_start = 0;
for($i=$pixel_start;$i<$im_width;$i++){
	$back_col_r = 0;
	$back_col_g = 100;
	$back_col_b = 0;

	// Calculate the Alpha depending on the X position
	$y = ($i - $gfx_start_pos+($im_height/2)) * ($im_width - $gfx_start_pos+($im_height/2)) / 100;

	$x = $i - $pixel_start;
	$max = $im_width - $pixel_start;
	$c = ($x * 128) / $max;

	$newcolor = imagecolorresolvealpha ( $im, $back_col_r, $back_col_g, $back_col_b, $c);
	ImageLine( $im, $i, 0, $i, $im_height, $newcolor);
}

// Draw the transparency polygon...
$alpha_color = imagecolorresolvealpha ( $im, 0, 0, 0, 127);
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



////////////////////////////////////////////////////////////////
// Draw the polygon for the tree, endtree, plus or minus sign //
////////////////////////////////////////////////////////////////
$im_hori_center = ($im_height / 2)-1;
$im_start_point = 2;
$im_half_size = 4;
$im_sign_space = 3;

$style=array($black_color, IMG_COLOR_TRANSPARENT );
ImageSetStyle($im, $style);


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
	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_hori_center+$im_half_size+1, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_height, IMG_COLOR_STYLED);
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

	global $im;
	global $im_start_point;
	global $im_hori_center;
	global $im_half_size;
	global $im_sign_space;
	global $black_color;
	global $im_height;
	global $recurs_x_decal;

	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, 0, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_height, IMG_COLOR_STYLED);
	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size,   $im_height/2, $recurs_x_decal*$cellnum+ $im_start_point+$recurs_x_decal, $im_height/2, IMG_COLOR_STYLED);

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

	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size,   0, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_height/2, IMG_COLOR_STYLED);
	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size,   $im_height/2, $recurs_x_decal*$cellnum+ $im_start_point+$recurs_x_decal, $im_height/2, IMG_COLOR_STYLED);

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

	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point-($recurs_x_decal/2)+$im_sign_space,$im_height/2, $recurs_x_decal*$cellnum+2+ $im_start_point+($im_half_size*2)-$im_sign_space, $im_height/2, IMG_COLOR_STYLED);
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

	ImageLine( $im, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, 0, $recurs_x_decal*$cellnum+ $im_start_point+$im_half_size, $im_height, IMG_COLOR_STYLED);
}

$array_of_sign = explode("/",$link);

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

ImageAlphaBlending($im, false);

if($color == 0){
	$text_color = ImageColorAllocate ($im, 255, 255, 255);
	$txt_x_pos = 25;
	$txt_y_pos = 2;
//	ImageString ($im, 3, $gfx_start_pos+$txt_x_pos+1, $txt_y_pos+1,  "$text", $black_color);
	$alpha_color = imagecolorresolvealpha ( $im, 0, 0, 0, 0);
//	imagettftext ( $im, 12, 0, $gfx_start_pos+$txt_x_pos+1, $txt_y_pos+1, $alpha_color, "/Topaznew.ttf", $text);
}else if($color == 1){
	$text_color = ImageColorAllocate ($im, 0xFF, 0xCC, 0);
	$txt_x_pos = 26;
	$txt_y_pos = 3;
}
ImageString ($im, 3, $gfx_start_pos+$txt_x_pos, $txt_y_pos,  "$text", $text_color);
//imagettftext ( $im, 12, 0, $gfx_start_pos+$txt_x_pos, $txt_y_pos, $text_color, "/Topaznew.ttf", $text);

ImagePng ($im);
ImageDestroy($im);

?>
