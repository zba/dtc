<?php

if( file_exists('PEAR.php') && file_exists('Crypt/CBC.php')){
  require_once "$dtcshared_path/dtcrm/srs/openSRS_base.php";
  require_once "$dtcshared_path/dtcrm/srs_base.php";
  require_once "$dtcshared_path/dtcrm/srs_nameserver.php";
  require_once "$dtcshared_path/dtcrm/srs_registernames.php";
}

?>