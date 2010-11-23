<?php

require("dtc_storefront/dbconnect.php");

function getProductPrice($prod_id){
  $q = "SELECT * FROM product WHERE id='$prod_id';";
  $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".msyql_error());
  $n = mysql_num_rows($r);
  if($n != 1){
    return false;
  }else{
    $a = mysql_fetch_array($r);
    return $a["price_dollar"];
  }
}

function getTestimonials(){
  $q = "SELECT * FROM testimonials WHERE publish='yes' ORDER BY id";
  $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".msyql_error());
  $n = mysql_num_rows($r);
  $out = array();
  for($i=0;$i<$n;$i++){
    $out[] = mysql_fetch_array($r);
  }
  return $out;
}

function drawTestimonials($testim){
  $n = sizeof($testim);
  $out = "";
  for($i=0;$i<$n;$i++){
    $out .= '<div id="main-text_container">
<b>'.stripslashes($testim[$i]["company"]).'</b><br>
'.stripslashes($testim[$i]["comment"]).'<br>
<div align="right"><a href="'.$testim[$i]["url"].'" target="_blank">'.$testim[$i]["url"].'</a></div>
</div>';
  }
  return $out;
}

function drawTestimonialsForm($destination_script){
  $out = '<table width="1" height="1">
<tr>
<form action="'.$destination_script.'"><td align="right" style="white-space:nowrap">Web url :</td>
<td><input type="hidden" name="rub" value="about"><input type="hidden" name="sousrub" value="recordit">
<input size="40" type="text" name="addr_web" value="http://www."></td>
</tr><tr>
<td align="right" style="white-space:nowrap">Company name and your name :</td>
<td><input size="40" type="text" name="comp_name" value=""></td>
</tr>
<tr>
<td align="right" style="white-space:nowrap">Your comment :</td>
<td><textarea rows="5" cols="30" name="comment"></textarea></td>
</tr>
<tr>
<td align="right" style="white-space:nowrap">&nbsp;</td>
<td><input type="submit" value="Publish"></td>
</tr></table></form>';
  return $out;
}

function recordTestimonials($validation_script,$deletion_script,$administrator_email){
  $out = "Recording your query in database...<br>";
  $q = "INSERT INTO testimonials (url,company,comment) VALUES ('".mysql_real_escape_string($_REQUEST["addr_web"])."','".mysql_real_escape_string($_REQUEST["comp_name"])."','".mysql_real_escape_string($_REQUEST["comment"])."');";
  $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
  $reqid = mysql_insert_id();

  $out .= "Sending your query by mail for manual checking...<br><br>";
  $mail_content = "Somebody wants to write a testimonial.

Validate the request: http://".$_SERVER["HTTP_HOST"]."/".$validation_script."?id=".$reqid."
Delete the request: http://".$_SERVER["HTTP_HOST"]."/".$deletion_script."?id=".$reqid."

Company: ".stripslashes($_REQUEST["comp_name"])."
URL: ".$_REQUEST["addr_web"]."
Date: ".date("H:i w j-M-Y")."
Host: ".$_SERVER["REMOTE_ADDR"]."

--- MESSAGE ---
".stripslashes($_REQUEST["comment"])."
--- EOF ---
";

  mail("thomas@goirand.fr","[DTC] somebody wrote a testimonial",$mail_content,$mail_header);
  return $out;
}

function validateTestimonials(){
  $out = "Validating testimonial id ".$_REQUEST["id"];
  $q = "UPDATE testimonials SET publish='yes' WHERE id='".mysql_real_escape_string($_REQUEST["id"])."' AND publish='no';";
  $r = mysql_query($q)or die("Cannot query $q line ".__line__." file ".__FILE__." mysql said: ".mysql_error());
  $out .= "...done<br>";
  return $out;
}

function deleteTestimonials(){
  $out = "Deleting testimonial id '".$_REQUEST["id"]."'<br>";
  $q = "DELETE FROM testimonials WHERE id='".mysql_real_escape_string($_REQUEST["id"])."' AND publish='no' LIMIT 1;";
  $r = mysql_query($q)or die("Cannot query $q line ".__line__." file ".__FILE__." mysql said: ".mysql_error());
  $out .= "...done<br>";
  return $out;
}

?>