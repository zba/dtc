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
            $out .= "\t\"" . $field_names[$j] . '"' . " => " . '"' . $row[$field_names[$j]] . '"';
          }
          $out .= ");\n";
        }
        $out .= "\n";
        return $out;
}

function exportDomain($domain_name,$path_to){
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
        $out .= exportSqlTable($pro_mysql_ftp_http_table,'sub_domain',$domain_name);
        $out .= exportSqlTable($pro_mysql_registry_table,'sub_domain',$domain_name);
        return $out;
}

?>