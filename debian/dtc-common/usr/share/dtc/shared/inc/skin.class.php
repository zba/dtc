<?php
/**
 * @package DTC
 * @name GetSkin
 * @author Sebastian 'SeeB' Pachla <seeb@seeb.net.pl>
 * @return $skin (name)
 * @copyright GPL
 * @version $Id: $
 * $Log: $
 */

class getSkin{
    var $config_skin;
    var $conf_mysql_host;
    var $conf_mysql_login;	
    var $conf_mysql_pass;
    var $conf_mysql_db;
    
    function getSkin($conf_mysql_host,$conf_mysql_login,$conf_mysql_pass,$conf_mysql_db){
		    $this->conf_mysql_host=$conf_mysql_host;
		    $this->conf_mysql_login=$conf_mysql_login;	
		    $this->conf_mysql_pass=$conf_mysql_pass;
		    $this->conf_mysql_db=$conf_mysql_db;
		    $this->connect2base();
		    $this->skin();
	    	
		if($this->connect2base() == false){
			die("Cannot connect to database !!!");
		}// end if
    }// end getskin - constructor

    function connect2base(){
		$ressource_id = @mysql_connect("$this->conf_mysql_host", "$this->conf_mysql_login", "$this->conf_mysql_pass");
		if($ressource_id == false)	return false;
		return @mysql_select_db($this->conf_mysql_db)or die("Cannot select db: $this->conf_mysql_db");
    }// end connect2base

    function skin(){
		$query = "SELECT * FROM config WHERE 1 LIMIT 1;";
		$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());	
		$row = mysql_fetch_array($result);
		$this->config_skin=$row['skin'];
		return $this->config_skin; 
	}// end skin
}// end class
?>