<?php

function chmod_R($path, $filemode) {
   if (!is_dir($path))
       return chmod($path, $filemode);

   $dh = opendir($path);
   while ($file = readdir($dh)) {
       if($file != '.' && $file != '..') {
           $fullpath = $path.'/'.$file;
           if(!is_dir($fullpath)) {
             if (!chmod($fullpath, $filemode))
                 return FALSE;
           } else {
             if (!chmod_R($fullpath, $filemode))
                 return FALSE;
           }
       }
   }

   closedir($dh);

   if(chmod($path, $filemode))
     return TRUE;
   else
     return FALSE;
}

function recurse_chown_chgrp($mypath, $uid, $gid)
{
	$d = opendir ($mypath);
	while(($file = readdir($d)) !== false) {
		if ($file != "." && $file != "..") {

			$typepath = $mypath . "/" . $file ;

			//print $typepath. " : " . filetype ($typepath). "<BR>" ;
			if (filetype ($typepath) == 'dir') {
				recurse_chown_chgrp ($typepath, $uid, $gid);
			}
			if (is_numeric($uid))
			{
				chown($typepath, intval($uid));
			} else {
				chown($typepath, $uid);
			}
			if (is_numeric($gid))
			{
				chgrp($typepath, intval($gid));
			}  else {
				chgrp($typepath, $gid);
			}
		}
	}
}

// require("genfiles/gen_perso_vhost.php");
require("genfiles/gen_pro_vhost.php");
require("genfiles/gen_email_account.php");
require("genfiles/gen_named_files.php");
require("genfiles/gen_backup_script.php");
require("genfiles/gen_webalizer_stat.php");
require("genfiles/gen_ssh_account.php");
require("genfiles/gen_nagios.php");
require("genfiles/gen_fetchmail.php");

?>
