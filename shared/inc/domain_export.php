<?php

function exportSqlTable($table_name,$filter_field,$filter_value){
        global $conf_mysql_db;

        $out = '$' . "$table_name = array();\n";

        $fields = mysql_list_fields($conf_mysql_db, $table_name);
        $columns = mysql_num_fields($fields);

        $field_names = array();
        for($i=0;$i<$columns;$i++){
          $fld_name = mysql_field_name($fields, $i);
          if($fld_name != "id" && strlen($fld_name) > 0){
            $field_names[] = $fld_name;
          }
        }
        $columns = sizeof($field_names);

        $q = "SELECT * FROM $table_name WHERE $filter_field='$filter_value';";
        $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
        $num_rows = mysql_num_rows($r);
        for($i=0;$i<$num_rows;$i++){
          $row = mysql_fetch_array($r);
          $out .= '$' . "$table_name".'[]'." = array(\n";
          for($j=0;$j<$columns;$j++){
            if($j != 0){
              $out .= ",\n";
            }
            $out .= "\t\"" . $field_names[$j] . '"' . " => " . '"' . urlencode($row[$field_names[$j]]) . '"';
          }
          $out .= ");\n";
        }
        $out .= "\n";
        return $out;
}

function exportDomainSQL($domain_name){
        global $pro_mysql_domain_table;
        global $pro_mysql_subdomain_table;
        global $pro_mysql_pop_table;
        global $pro_mysql_ftp_table;
        global $pro_mysql_ssh_table;
        global $pro_mysql_whois_table;
        global $pro_mysql_nameservers_table;
        global $pro_mysql_acc_http_table;
        global $pro_mysql_acc_ftp_table;
        global $pro_mysql_acc_email_table;
        global $pro_mysql_registry_table;

        global $conf_mysql_db;

        $out = exportSqlTable($pro_mysql_domain_table,"name",$domain_name);
        $out .= exportSqlTable($pro_mysql_subdomain_table,"domain_name",$domain_name);
        $out .= exportSqlTable($pro_mysql_pop_table,'mbox_host',$domain_name);
        $out .= exportSqlTable($pro_mysql_ftp_table,'hostname',$domain_name);
        $out .= exportSqlTable($pro_mysql_ssh_table,'hostname',$domain_name);
        $out .= exportSqlTable($pro_mysql_whois_table,'domain_name',$domain_name);
        $out .= exportSqlTable($pro_mysql_nameservers_table,'domain_name',$domain_name);
        $out .= exportSqlTable($pro_mysql_acc_http_table,'domain',$domain_name);
        $out .= exportSqlTable($pro_mysql_acc_ftp_table,'sub_domain',$domain_name);
        $out .= exportSqlTable($pro_mysql_acc_email_table,'domain_name',$domain_name);
        return $out;
}

//dtc_sql_config/<domain_name>/dtc_dump.php -> dtc config dump (pop, ftp, ssh, etc...)
//user_db_dump/<dbname>/mysql_dump.sql -> user's database
//domain_files/<domain_name>/[files]
//dtc_dump_index.php
//
//    dtc_dump_info = array(
//      "admin_name" => "xxxx",

function exportDomain($domain_name,$path_to){
  global $export_domain_err;
  global $pro_mysql_domain_table;

  // Get the domain's file path
  $q = "SELECT owner FROM $pro_mysql_domain_table WHERE name='$domain_name'";
  $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
  $n = mysql_num_rows($r);
  if($n != 1){
    $export_domain_err = "Cannot find domain in db";
    return false;
  }
  $a = mysql_fetch_array($r);
  $adm_login = $a["owner"];
  $adm_path = getAdminPath($adm_login);

  // Prepare the folders
  $real_path = $path_to."/dtc_export";
  if(is_dir($real_path) || file_exists($real_path)){
    $export_domain_err = "$real_path exists!";
    return false;
  }

  // Create the dirs
  echo "mkdir $real_path\n";
  mkdir($real_path);

  $dtc_sql_config  = $real_path."/dtc_sql_config";
  echo "mkdir $dtc_sql_config\n";
  mkdir($dtc_sql_config);

  $dtc_sql_dump_path = $dtc_sql_config."/$domain_name";
  echo "mkdir ".$dtc_sql_dump_path."\n";
  mkdir($dtc_sql_dump_path);

  $dtc_sql_dump_filename = $dtc_sql_dump_path."/dtc_dump.php";
  $dtc_domain_files_path = $real_path."/domain_files";
  echo "mkdir $dtc_domain_files_path\n";
  mkdir($dtc_domain_files_path);

  // Get the dump
  echo "Dumping SQL config for $domain_name...<br>\n";
  $dtc_sql_dump = exportDomainSQL($domain_name);

  // Write the sql dump
  $fp = fopen($dtc_sql_dump_filename,"wb+");
  if($fp == NULL){
    $export_domain_err = "Can't open file $real_path";
    return false;
  }
  if(!fwrite($fp,$dtc_sql_dump)){
    $export_domain_err = "Can't write file $real_path";
    return false;
  }
  fclose($fp);

  // Copy all the domain files in the folder
  echo "Copying domain files for $domain_name...<br>\n";
  $cmd = "cp -auf $adm_path/$domain_name $dtc_domain_files_path";
  $last_line = exec($cmd,$output,$return_var);

  echo "Compressing export for $domain_name...<br>\n";
  $old_dir = getcwd();
  chdir($path_to);
  $cmd = "tar -cvzf $domain_name.dtc.tar.gz dtc_export";
  $last_line = exec($cmd,$output,$return_var);
  $cmd = "rm -r dtc_export";
  $last_line = exec($cmd,$output,$return_var);
  chdir($old_dir);
}

?>