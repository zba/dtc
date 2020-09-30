<?php

$skinGeneralPath = "gfx/skin";
////////////////////////////////////////////////////////////
// Find the selected skin in already "loaded skin table", //
// and retrun it's index. Return -1 if not found          //
////////////////////////////////////////////////////////////
function getSkinIndex($skinName){
	global $skinTable;

	$nbrLoaded = sizeof($skinTable);
	for($i=0;$i<$nbrLoaded;$i++){
		$curSkin = &$skinTable[$i];
		if($curSkin["skinName"] == $skinName){
			return $i;
		}
	}
	return -1;
}

$skinCssString = "";

////////////////////////////////////////////////////////////
// Skin one content and retrun the resulting html element //
////////////////////////////////////////////////////////////
function skin($skinpath,$content,$title){
	global $skinTable;
	global $skinGeneralPath;
	global $skinCssString;

	if($skinpath == "")	$skinpath = "green";

	// If skin function not allready in memory
	$skinIndex = getSkinIndex($skinpath);
	if($skinIndex == -1){
		$isSkinOk = false;
		// Include the php file containing the code
		if(!file_exists("$skinGeneralPath/$skinpath/skin.php"))	die("Skin $skinpath not found !!!");
		include("$skinGeneralPath/$skinpath/skin.php");

		$skinIndex = getSkinIndex($skinpath);
		if($skinIndex == -1)	die("Cannot find included skin $skinpath");
	}else{
		$isSkinOk = true;
	}
	// Find (or create if 1st load) the skin function
	$mySkin = &$skinTable[$skinIndex];
	$functionCode = &$mySkin["functionCode"];
	if($isSkinOk == false){
		$functionName = create_function('$skinpath,$content,$title,$skinGeneralPath',$functionCode);
		$mySkin["functionName"] = $functionName;
		if($mySkin["skinCss"] != ""){
			$cssFile = "$skinGeneralPath/".$mySkin["skinName"]."/".$mySkin["skinCss"];
			$skinCssString .= "<link rel=\"stylesheet\" href=\"$cssFile\" type=\"text/css\">\n";
		}
	}else{
		$functionName = $mySkin["functionName"];
	}
	// Call it
	return $functionName($skinpath,$content,$title,$skinGeneralPath);
}

if(file_exists("$dtcshared_path/$skinGeneralPath/$conf_skin/layout.php")){
	require("$dtcshared_path/$skinGeneralPath/$conf_skin/layout.php");
}
if(file_exists("$dtcshared_path/$skinGeneralPath/$conf_skin/gfx_defaults.php")){
	require("$dtcshared_path/$skinGeneralPath/$conf_skin/gfx_defaults.php");
}

?>