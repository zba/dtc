<?php

global $dtcshared_path;
$mods_path = "$dtcshared_path/" . 'dtcrm/modules';
if(FALSE === ($dir = scandir($mods_path))){
	echo "<font color=\"red\">"._("Could not list modules in")." $mods_path" . "</font>";
}else{
	$nbr_file = sizeof($dir);
	$registry_api_modules = array();
	for($i=0;$i<$nbr_file;$i++){
		$file = $dir[$i];
		$mymod_path = $mods_path . "/$file";
		if ($file != "." && $file != ".." && is_dir($mymod_path)){
			require($mymod_path. "/main.php");
		}
	}
}

function display_all_registry_config (){
	global $registry_api_modules;

	$out = "";
	$nbr_modules = sizeof($registry_api_modules);
	for($i=0;$i<$nbr_modules;$i++){
		$dsc = $registry_api_modules[$i]["configure_descriptor"];
		$dsc["forward"] = array("rub","sousrub");
		$out .= configEditorTemplate ($dsc);
	}
	return $out;
}

?>