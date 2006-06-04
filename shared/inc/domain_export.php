<?php

function exportSqlTable($table_name,$filter_field,$filter_value){
        global $conf_mysql_db;
        global $pro_mysql_pop_table;

        $out = '$' . "$table_name = array();\n";

        $fields = mysql_list_fields($conf_mysql_db, $table_name);
        $columns = mysql_num_fields($fields);

        $field_names = array();
        for($i=0;$i<$columns;$i++){
          $fld_name = mysql_field_name($fields, $i);
          if(($table_name == $pro_mysql_pop_table && strlen($fld_name) > 0) || ($fld_name != "id" && strlen($fld_name) > 0)){
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


        $out = "<?php\n";
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

        $out .= "\$exported_admin_path = \"$adm_path\";\n\n";
        $out .= exportSqlTable($pro_mysql_domain_table,"name",$domain_name);
        $out .= exportSqlTable($pro_mysql_subdomain_table,"domain_name",$domain_name);
        $out .= exportSqlTable($pro_mysql_pop_table,'mbox_host',$domain_name);
        $out .= exportSqlTable($pro_mysql_ftp_table,'hostname',$domain_name);
        $out .= exportSqlTable($pro_mysql_ssh_table,'hostname',$domain_name);
        $out .= exportSqlTable($pro_mysql_whois_table,'domain_name',$domain_name);
        $out .= exportSqlTable($pro_mysql_nameservers_table,'domain_name',$domain_name);
        $out .= exportSqlTable($pro_mysql_acc_http_table,'domain',$domain_name);
        $out .= exportSqlTable($pro_mysql_acc_ftp_table,'sub_domain',$domain_name);
        $out .= exportSqlTable($pro_mysql_acc_email_table,'domain_name',$domain_name);
        return $out."\n?>";
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
//  echo "mkdir $real_path\n";
  mkdir($real_path);

  $dtc_sql_config  = $real_path."/dtc_sql_config";
//  echo "mkdir $dtc_sql_config\n";
  mkdir($dtc_sql_config);

  $dtc_sql_dump_path = $dtc_sql_config."/$domain_name";
//  echo "mkdir ".$dtc_sql_dump_path."\n";
  mkdir($dtc_sql_dump_path);

  $dtc_sql_dump_filename = $dtc_sql_dump_path."/dtc_dump.php";
  $dtc_domain_files_path = $real_path."/domain_files";
//  echo "mkdir $dtc_domain_files_path\n";
  mkdir($dtc_domain_files_path);

  // Get the dump
//  echo "Dumping SQL config for $domain_name...<br>\n";
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
//  echo "Copying domain files for $domain_name...<br>\n";
  $cmd = "cp -auf $adm_path/$domain_name $dtc_domain_files_path";
//  echo "$cmd<br>\n";
  $last_line = exec($cmd,$output,$return_var);

//  echo "Compressing export for $domain_name...<br>\n";
  $old_dir = getcwd();
  chdir($path_to);
  $cmd = "tar -cvzf $domain_name.dtc.tar.gz dtc_export";
  $last_line = exec($cmd,$output,$return_var);
  $cmd = "rm -r dtc_export";
//  $last_line = exec($cmd,$output,$return_var);
  chdir($old_dir);
}

function domainImport($path_from,$adm_login){
  global $pro_mysql_admin_table;
  global $pro_mysql_domain_table;
  global $pro_mysql_ftp_table;
  global $pro_mysql_ssh_table;
  global $pro_mysql_pop_table;
  global $pro_mysql_subdomain_table;

  global $conf_main_site_ip;
  $q = "SELECT path FROM $pro_mysql_admin_table WHERE adm_login='$adm_login'";
  $r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
  $n = mysql_num_rows($r);
  if($n != 1){
    echo "Cannot find adm_login: $adm_login";
    return false;
  }
  $adm = mysql_fetch_array($r);
  $adm_path = $adm["path"];
  
  if(!file_exists($path_from)){
    echo "File does not exsits: $path_from";
    return false;
  }
  $basename = basename($path_from);
  $path = substr($path_from,0,strlen($path_from) - strlen($basename));
  $old_dir = getcwd();
  chdir($path);
//  echo "Uncompressing $basename...";
  $cmd = "tar -xzf $basename";
  $last_line = exec($cmd,$output,$return_var);
//  echo "done!<br>\n";

  $od = $path."dtc_export/dtc_sql_config";
  if (!is_dir($od)) {
    echo "This is not a directory: $od";
    return false;
  }
  if (($dh = opendir($od)) != true) {
    echo "Cannot open directory: $od";
    return false;
  }
//  echo "Parsing dir $od...\n";
  while (($file = readdir($dh)) !== false) {
    if($file == "." || $file == ".."){
      continue;
    }
    #echo "fichier : $file : type : " . filetype($od . "/" . $file) . "\n";
    $sql_dump_file = $od . "/" . $file . "/dtc_dump.php";
    if(!file_exists($sql_dump_file)){
      echo "Could not find sql dump: $sql_dump_file";
    }
    include($sql_dump_file);
    $domain_name = urldecode($domain[0]["name"]);
    $q = "SELECT * FROM $pro_mysql_domain_table WHERE name='$domain_name';";
    $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
    $domain_exists = mysql_num_rows($r);
    # This is the "add domain" mode
    if($domain_exists == 0){
      $q = "INSERT INTO $pro_mysql_domain_table (name,safe_mode,sbox_protect,owner,
      default_subdomain,generate_flag,
      quota,max_email,max_lists,max_ftp,max_subdomain,

      ip_addr,primary_dns,other_dns,primary_mx,other_mx,
      whois,hosting,du_stat,gen_unresolved_domain_alias,txt_root_entry,txt_root_entry2,
      catchall_email,registrar_password,max_ssh,domain_parking)

      VALUES('".$domain[0]["name"]."','".$domain[0]["safe_mode"]."','".$domain[0]["sbox_protect"]."','$adm_login',
      '".$domain[0]["default_subdomain"]."','yes',
      '".$domain[0]["quota"]."','".$domain[0]["max_email"]."','".$domain[0]["max_lists"]."','".$domain[0]["max_ftp"]."','".$domain[0]["max_subdomain"]."',

      '$conf_main_site_ip','".$domain[0]["primary_dns"]."','".$domain[0]["other_dns"]."','".$domain[0]["primary_mx"]."','".$domain[0]["other_mx"]."',
      '".$domain[0]["whois"]."','".$domain[0]["hosting"]."','0','".$domain[0]["gen_unresolved_domain_alias"]."','".$domain[0]["txt_root_entry"]."','".$domain[0]["txt_root_entry2"]."',
      '".$domain[0]["catchall_email"]."','".$domain[0]["registrar_password"]."','".$domain[0]["max_ssh"]."','".$domain[0]["domain_parking"]."');";
      $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
    # This is the "merge" domain mode
    }else{
      $sql_d = mysql_fetch_array($r);
      if($sql_d["owner"] != $domain[0]["owner"]){
        echo "Domain exists for another admin: import aborted";
        return false;
      }
      $q = "UPDATE $pro_mysql_domain_table
      SET safe_mode='".addslashes($domain[0]["safe_mode"])."',
      sbox_protect='".addslashes($domain[0]["sbox_protect"])."',
      default_subdomain='".addslashes(urldecode($domain[0]["default_subdomain"]))."',
      generate_flag='yes',
      quota='0',
      max_email='".addslashes($domain[0]["max_email"])."',
      max_lists='".addslashes($domain[0]["max_lists"])."',
      max_ftp='".addslashes($domain[0]["max_ftp"])."',
      max_subdomain='".addslashes($domain[0]["max_subdomain"])."',
      primary_dns='".addslashes(urldecode($domain[0]["primary_dns"]))."',
      other_dns='".addslashes(urldecode($domain[0]["other_dns"]))."',
      primary_mx='".addslashes(urldecode($domain[0]["primary_mx"]))."',
      other_mx='".addslashes(urldecode($domain[0]["other_mx"]))."',
      whois='".addslashes($domain[0]["whois"])."',
      hosting='".addslashes($domain[0]["hosting"])."',
      gen_unresolved_domain_alias='".addslashes($domain[0]["gen_unresolved_domain_alias"])."',
      txt_root_entry='".addslashes(urdecode($domain[0]["txt_root_entry"]))."',
      txt_root_entry2='".addslashes(urldecode($domain[0]["txt_root_entry2"]))."',
      catchall_email='".addslashes(urldecode($domain[0]["catchall_email"]))."',
      registrar_password='".addslashes(urdecode($domain[0]["registrar_password"]))."',
      max_ssh='".addslashes($domain[0]["max_ssh"])."',
      domain_parking='".addslashes(urdecode($domain[0]["domain_parking"]))."'
      WHERE name='$domain_name';";
      $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
    }
    $n = sizeof($subdomain);
    for($i=0;$i<$n;$i++){
      $q = "SELECT subdomain_name FROM $pro_mysql_subdomain_table WHERE domain_name='$domain_name' AND subdomain_name='".addslashes(urldecode($subdomain[$i]["subdomain_name"]))."';";
      $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
      $n1 = mysql_num_rows($r);
      if($n1 == 0){
        $q = "INSERT INTO $pro_mysql_subdomain_table (safe_mode,sbox_protect,domain_name,subdomain_name,
        path,webalizer_generate,register_globals,login,pass,
        w3_alias,associated_txt_record,generate_vhost)
        VALUES ('".addslashes($subdomain[$i]["safe_mode"])."',
        '".addslashes($subdomain[$i]["sbox_protect"])."',
        '".addslashes(urldecode($subdomain[$i]["domain_name"]))."',
        '".addslashes(urldecode($subdomain[$i]["subdomain_name"]))."',
        '".addslashes(urldecode($subdomain[$i]["path"]))."',
        '".addslashes($subdomain[$i]["webalizer_generate"])."',
        '".addslashes($subdomain[$i]["register_globals"])."',
        '".addslashes(urldecode($subdomain[$i]["login"]))."',
        '".addslashes(urldecode($subdomain[$i]["pass"]))."',
        '".addslashes($subdomain[$i]["w3_alias"])."',
        '".addslashes(urldecode($subdomain[$i]["associated_txt_record"]))."',
        '".addslashes($subdomain[$i]["generate_vhost"])."');";
        $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
      }else{
        $q = "UPDATE $pro_mysql_subdomain_table
        SET safe_mode='".addslashes($subdomain[$i]["safe_mode"])."',
        sbox_protect='".addslashes($subdomain[$i]["sbox_protect"])."',
        path='".addslashes(urldecode($subdomain[$i]["path"]))."',
        webalizer_generate='".addslashes($subdomain[$i]["webalizer_generate"])."',
        register_globals='".addslashes($subdomain[$i]["register_globals"])."',
        login='".addslashes(urldecode($subdomain[$i]["login"]))."',
        pass='".addslashes(urldecode($subdomain[$i]["pass"]))."',
        w3_alias='".addslashes($subdomain[$i]["w3_alias"])."',
        associated_txt_record='".addslashes(urldecode($subdomain[$i]["associated_txt_record"]))."',
        generate_vhost='".addslashes($subdomain[$i]["generate_vhost"])."'
        WHERE domain_name='$domain_name' AND subdomain_name='".addslashes(urldecode($subdomain[$i]["subdomain_name"]))."';";
        $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
      }
    }
    $n = sizeof($pop_access);
    for($i=0;$i<$n;$i++){
      $q = "SELECT id FROM $pro_mysql_pop_table WHERE id='".$pop_access[$i]["id"]."' AND mbox_host='$domain_name';";
      $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
      $n1 = mysql_num_rows($r);
      if($n1 == 0){
        $q = "INSERT INTO $pro_mysql_pop_table (id,uid,gid,home,shell,mbox_host,crypt,passwd,active,start_date,expire_date,quota_size,
        type,memo,du,another_perso,redirect1,redirect2,localdeliver,pop3_login_count,pop3_transfered_bytes,imap_login_count,imap_transfered_bytes,
        last_login,bounce_msg,spf_protect,clamav_protect,
        spam_mailbox_enable,spam_mailbox,pass_next_req,pass_expire,iwall_protect,fullemail,vacation_flag,vacation_text)
        VALUES('".addslashes(urldecode($pop_access[$i]["id"]))."',
        '".addslashes(urldecode($pop_access[$i]["uid"]))."',
        '".addslashes(urldecode($pop_access[$i]["gid"]))."',
        '".$adm_path."/".$domain_name."/Mailboxs/".addslashes(urldecode($pop_access[$i]["id"]))."',
        '".addslashes(urldecode($pop_access[$i]["shell"]))."',
        '".addslashes(urldecode($pop_access[$i]["mbox_host"]))."',
        '".addslashes(urldecode($pop_access[$i]["crypt"]))."',
        '".addslashes(urldecode($pop_access[$i]["passwd"]))."',
        '".addslashes(urldecode($pop_access[$i]["active"]))."',
        '".addslashes(urldecode($pop_access[$i]["start_date"]))."',
        '".addslashes(urldecode($pop_access[$i]["expire_date"]))."',
        '".addslashes(urldecode($pop_access[$i]["quota_size"]))."',

        '".addslashes(urldecode($pop_access[$i]["type"]))."',
        '".addslashes(urldecode($pop_access[$i]["memo"]))."',
        '".addslashes(urldecode($pop_access[$i]["du"]))."',
        '".addslashes(urldecode($pop_access[$i]["another_perso"]))."',
        '".addslashes(urldecode($pop_access[$i]["redirect1"]))."',
        '".addslashes(urldecode($pop_access[$i]["redirect2"]))."',
        '".addslashes(urldecode($pop_access[$i]["localdeliver"]))."',
        '".addslashes(urldecode($pop_access[$i]["pop3_login_count"]))."',
        '".addslashes(urldecode($pop_access[$i]["pop3_transfered_bytes"]))."',
        '".addslashes(urldecode($pop_access[$i]["imap_login_count"]))."',
        '".addslashes(urldecode($pop_access[$i]["imap_transfered_bytes"]))."',

        '".addslashes(urldecode($pop_access[$i]["last_login"]))."',
        '".addslashes(urldecode($pop_access[$i]["bounce_msg"]))."',
        '".addslashes(urldecode($pop_access[$i]["spf_protect"]))."',
        '".addslashes(urldecode($pop_access[$i]["clamav_protect"]))."',

        '".addslashes(urldecode($pop_access[$i]["spam_mailbox_enable"]))."',
        '".addslashes(urldecode($pop_access[$i]["spam_mailbox"]))."',
        '".addslashes(urldecode($pop_access[$i]["pass_next_req"]))."',
        '".addslashes(urldecode($pop_access[$i]["pass_expire"]))."',
        '".addslashes(urldecode($pop_access[$i]["iwall_protect"]))."',
        '".addslashes(urldecode($pop_access[$i]["fullemail"]))."',
        '".addslashes(urldecode($pop_access[$i]["vacation_flag"]))."',
        '".addslashes(urldecode($pop_access[$i]["vacation_text"]))."');";
        $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
      }else{
        $q = "UPDATE $pro_mysql_pop_table
        SET uid='".addslashes(urldecode($pop_access[$i]["uid"]))."',
        gid='".addslashes(urldecode($pop_access[$i]["gid"]))."',
        home=''".$adm_path."/".$domain_name."/Mailboxs/".addslashes(urldecode($pop_access[$i]["id"]))."',
        shell=''".addslashes(urldecode($pop_access[$i]["shell"]))."',
        crypt='".addslashes(urldecode($pop_access[$i]["crypt"]))."',
        passwd='".addslashes(urldecode($pop_access[$i]["passwd"]))."',
        active='".addslashes(urldecode($pop_access[$i]["active"]))."',
        start_date='".addslashes(urldecode($pop_access[$i]["start_date"]))."',
        expire_date='".addslashes(urldecode($pop_access[$i]["expire_date"]))."',
        quota_size='".addslashes(urldecode($pop_access[$i]["quota_size"]))."',
        type='".addslashes(urldecode($pop_access[$i]["type"]))."',
        memo='".addslashes(urldecode($pop_access[$i]["memo"]))."',
        du='".addslashes(urldecode($pop_access[$i]["du"]))."',
        another_perso='".addslashes(urldecode($pop_access[$i]["another_perso"]))."',
        redirect1='".addslashes(urldecode($pop_access[$i]["redirect1"]))."',
        redirect2='".addslashes(urldecode($pop_access[$i]["redirect2"]))."',
        localdeliver='".addslashes(urldecode($pop_access[$i]["localdeliver"]))."',
        pop3_login_count'".addslashes(urldecode($pop_access[$i]["pop3_login_count"]))."',
        pop3_transfered_bytes='".addslashes(urldecode($pop_access[$i]["pop3_transfered_bytes"]))."',
        imap_login_count='".addslashes(urldecode($pop_access[$i]["imap_login_count"]))."',
        imap_transfered_bytes='".addslashes(urldecode($pop_access[$i]["imap_transfered_bytes"]))."',
        last_login='".addslashes(urldecode($pop_access[$i]["last_login"]))."',
        bounce_msg='".addslashes(urldecode($pop_access[$i]["bounce_msg"]))."',
        spf_protect='".addslashes(urldecode($pop_access[$i]["spf_protect"]))."',
        clamav_protect='".addslashes(urldecode($pop_access[$i]["clamav_protect"]))."',
        spam_mailbox_enable='".addslashes(urldecode($pop_access[$i]["spam_mailbox_enable"]))."',
        spam_mailbox='".addslashes(urldecode($pop_access[$i]["spam_mailbox"]))."',
        pass_next_req='".addslashes(urldecode($pop_access[$i]["pass_next_req"]))."',
        pass_expire='".addslashes(urldecode($pop_access[$i]["pass_expire"]))."',
        iwall_protect='".addslashes(urldecode($pop_access[$i]["iwall_protect"]))."',
        fullemail='".addslashes(urldecode($pop_access[$i]["fullemail"]))."',
        vacation_flag='".addslashes(urldecode($pop_access[$i]["vacation_flag"]))."',
        vacation_text='".addslashes(urldecode($pop_access[$i]["vacation_text"]))."'
        WHERE id='".addslashes(urldecode($pop_access[$i]["id"]))."' AND mbox_host='$domain_name'";
        $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
      }
    }
    $n = sizeof($ftp_access);
    for($i=0;$i<$n;$i++){
      $acc_path = urldecode($ftp_access[$i]["homedir"]);
      if(!strstr($acc_path,$adm_path)){
        echo "Cannot find adm path in the ftp account ".urldecode($ftp_access[$i]["login"])."\n";
        continue;
      }
      $new_acc_path = str_replace($exported_admin_path,$adm_path,$acc_path);
      $q = "SELECT login FROM $pro_mysql_ftp_table WHERE login='".$ftp_access[$i]["login"]."' AND hostname='$domain_name';";
      $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
      $n1 = mysql_num_rows($r);
      if($n1 == 0){
        $q2 = "SELECT login FROM $pro_mysql_ftp_table WHERE login='".$ftp_access[$i]["login"]."';";
        $r2 = mysql_query($q)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
        $n2 = mysql_num_rows($r2);
        if($n2 > 0){
          echo "Cannot create ftp login ".$ftp_access[$i]["login"].": username exists already in the database!!!";
          continue;
        }else{
          $q = "INSERT INTO $pro_mysql_ftp_table (login,uid,gid,password,homedir,count,fhost,faddr,ftime,fcdir,fstor,fretr,creation,
          ts,frate,fcred,brate,bcred,flogs,size,shell,hostname,login_count,last_login,dl_bytes,ul_bytes,dl_count,ul_count,vhostip)
          VALUES('".addslashes(urldecode($ftp_access[$i]["login"]))."',
          '65534',
          '65534',
          '".addslashes(urldecode($ftp_access[$i]["password"]))."',
          '".addslashes($new_acc_path)."',
          '".addslashes(urldecode($ftp_access[$i]["count"]))."',
          '".addslashes(urldecode($ftp_access[$i]["fhost"]))."',
          '".addslashes(urldecode($ftp_access[$i]["faddr"]))."',
          '".addslashes(urldecode($ftp_access[$i]["ftime"]))."',
          '".addslashes(urldecode($ftp_access[$i]["fcdir"]))."',
          '".addslashes(urldecode($ftp_access[$i]["fstor"]))."',
          '".addslashes(urldecode($ftp_access[$i]["fretr"]))."',
          '".addslashes(urldecode($ftp_access[$i]["creation"]))."',
          
          '".addslashes(urldecode($ftp_access[$i]["ts"]))."',
          '".addslashes(urldecode($ftp_access[$i]["frate"]))."',
          '".addslashes(urldecode($ftp_access[$i]["fcred"]))."',
          '".addslashes(urldecode($ftp_access[$i]["brate"]))."',
          '".addslashes(urldecode($ftp_access[$i]["bcred"]))."',
          '".addslashes(urldecode($ftp_access[$i]["flogs"]))."',
          '".addslashes(urldecode($ftp_access[$i]["size"]))."',
          '".addslashes(urldecode($ftp_access[$i]["shell"]))."',
          '".$domain_name."',
          '".addslashes(urldecode($ftp_access[$i]["login_count"]))."',
          '".addslashes(urldecode($ftp_access[$i]["last_login"]))."',
          '".addslashes(urldecode($ftp_access[$i]["dl_bytes"]))."',
          '".addslashes(urldecode($ftp_access[$i]["ul_bytes"]))."',
          '".addslashes(urldecode($ftp_access[$i]["dl_count"]))."',
          '".addslashes(urldecode($ftp_access[$i]["ul_count"]))."',
          '".addslashes(urldecode($ftp_access[$i]["vhostip"]))."');";
        }
      }else{
        $q = "UPDATE $pro_mysql_ftp_table
        SET password='".addslashes(urldecode($ftp_access[$i]["password"]))."',
        homedir='".addslashes($new_acc_path)."',
        count='".addslashes(urldecode($ftp_access[$i]["count"]))."',
        fhost='".addslashes(urldecode($ftp_access[$i]["fhost"]))."',
        faddr='".addslashes(urldecode($ftp_access[$i]["faddr"]))."',
        ftime='".addslashes(urldecode($ftp_access[$i]["ftime"]))."',
        fcdir='".addslashes(urldecode($ftp_access[$i]["fcdir"]))."',
        fstor='".addslashes(urldecode($ftp_access[$i]["fstor"]))."',
        fretr='".addslashes(urldecode($ftp_access[$i]["fretr"]))."',
        creation='".addslashes(urldecode($ftp_access[$i]["creation"]))."',
        ts='".addslashes(urldecode($ftp_access[$i]["ts"]))."',
        frate='".addslashes(urldecode($ftp_access[$i]["frate"]))."',
        fcred='".addslashes(urldecode($ftp_access[$i]["fcred"]))."',
        brate='".addslashes(urldecode($ftp_access[$i]["brate"]))."',
        bcred='".addslashes(urldecode($ftp_access[$i]["bcred"]))."',
        flogs='".addslashes(urldecode($ftp_access[$i]["flogs"]))."',
        size='".addslashes(urldecode($ftp_access[$i]["size"]))."',
        shell='".addslashes(urldecode($ftp_access[$i]["shell"]))."',
        login_count='".addslashes(urldecode($ftp_access[$i]["login_count"]))."',
        vhostip='".addslashes(urldecode($ftp_access[$i]["vhostip"]))."' WHERE
        hostname='".$domain_name."' AND login='".addslashes(urldecode($ftp_access[$i]["login"]))."';";
      }
    }

    $n = sizeof($ssh_access);
    for($i=0;$i<$n;$i++){
      $acc_path = urldecode($ssh_access[$i]["homedir"]);
      if(!strstr($acc_path,$adm_path)){
        echo "Cannot find adm path in the ssh account ".urldecode($ssh_access[$i]["login"])."\n";
        continue;
      }
      $new_acc_path = str_replace($exported_admin_path,$adm_path,$acc_path);
      $q = "SELECT login FROM $pro_mysql_ssh_table WHERE login='".$ssh_access[$i]["login"]."' AND hostname='$domain_name';";
      $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
      $n1 = mysql_num_rows($r);
      if($n1 == 0){
        $q2 = "SELECT login FROM $pro_mysql_ssh_table WHERE login='".$ssh_access[$i]["login"]."';";
        $r2 = mysql_query($q)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
        $n2 = mysql_num_rows($r2);
        if($n2 > 0){
          echo "Cannot create ssh login ".$ssh_access[$i]["login"].": username exists already in the database!!!";
          continue;
        }else{
          $q = "INSERT INTO $pro_mysql_ssh_table (login,uid,gid,password,homedir,count,fhost,faddr,ftime,fcdir,fstor,fretr,creation,
          ts,frate,fcred,brate,bcred,flogs,size,shell,hostname,login_count,last_login,dl_bytes,ul_bytes,dl_count,ul_count,vhostip)
          VALUES('".addslashes(urldecode($ssh_access[$i]["login"]))."',
          '65534',
          '65534',
          '".addslashes(urldecode($ssh_access[$i]["password"]))."',
          '".addslashes($new_acc_path)."',
          '".addslashes(urldecode($ssh_access[$i]["count"]))."',
          '".addslashes(urldecode($ssh_access[$i]["fhost"]))."',
          '".addslashes(urldecode($ssh_access[$i]["faddr"]))."',
          '".addslashes(urldecode($ssh_access[$i]["ftime"]))."',
          '".addslashes(urldecode($ssh_access[$i]["fcdir"]))."',
          '".addslashes(urldecode($ssh_access[$i]["fstor"]))."',
          '".addslashes(urldecode($ssh_access[$i]["fretr"]))."',
          '".addslashes(urldecode($ssh_access[$i]["creation"]))."',
          
          '".addslashes(urldecode($ssh_access[$i]["ts"]))."',
          '".addslashes(urldecode($ssh_access[$i]["frate"]))."',
          '".addslashes(urldecode($ssh_access[$i]["fcred"]))."',
          '".addslashes(urldecode($ssh_access[$i]["brate"]))."',
          '".addslashes(urldecode($ssh_access[$i]["bcred"]))."',
          '".addslashes(urldecode($ssh_access[$i]["flogs"]))."',
          '".addslashes(urldecode($ssh_access[$i]["size"]))."',
          '".addslashes(urldecode($ssh_access[$i]["shell"]))."',
          '".$domain_name."',
          '".addslashes(urldecode($ssh_access[$i]["login_count"]))."',
          '".addslashes(urldecode($ssh_access[$i]["last_login"]))."',
          '".addslashes(urldecode($ssh_access[$i]["dl_bytes"]))."',
          '".addslashes(urldecode($ssh_access[$i]["ul_bytes"]))."',
          '".addslashes(urldecode($ssh_access[$i]["dl_count"]))."',
          '".addslashes(urldecode($ssh_access[$i]["ul_count"]))."',
          '".addslashes(urldecode($ssh_access[$i]["vhostip"]))."');";
          $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
        }
      }else{
        $q = "UPDATE $pro_mysql_ssh_table
        SET password='".addslashes(urldecode($ssh_access[$i]["password"]))."',
        homedir='".addslashes($new_acc_path)."',
        count='".addslashes(urldecode($ssh_access[$i]["count"]))."',
        fhost='".addslashes(urldecode($ssh_access[$i]["fhost"]))."',
        faddr='".addslashes(urldecode($ssh_access[$i]["faddr"]))."',
        ftime='".addslashes(urldecode($ssh_access[$i]["ftime"]))."',
        fcdir='".addslashes(urldecode($ssh_access[$i]["fcdir"]))."',
        fstor='".addslashes(urldecode($ssh_access[$i]["fstor"]))."',
        fretr='".addslashes(urldecode($ssh_access[$i]["fretr"]))."',
        creation='".addslashes(urldecode($ssh_access[$i]["creation"]))."',
        ts='".addslashes(urldecode($ssh_access[$i]["ts"]))."',
        frate='".addslashes(urldecode($ssh_access[$i]["frate"]))."',
        fcred='".addslashes(urldecode($ssh_access[$i]["fcred"]))."',
        brate='".addslashes(urldecode($ssh_access[$i]["brate"]))."',
        bcred='".addslashes(urldecode($ssh_access[$i]["bcred"]))."',
        flogs='".addslashes(urldecode($ssh_access[$i]["flogs"]))."',
        size='".addslashes(urldecode($ssh_access[$i]["size"]))."',
        shell='".addslashes(urldecode($ssh_access[$i]["shell"]))."',
        login_count='".addslashes(urldecode($ssh_access[$i]["login_count"]))."',
        vhostip='".addslashes(urldecode($ssh_access[$i]["vhostip"]))."' WHERE
        hostname='".$domain_name."' AND login='".addslashes(urldecode($ssh_access[$i]["login"]))."';";
        $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
      }
    }

  }
  closedir($dh);


  chdir($old_dir);
  return true;
}

?>