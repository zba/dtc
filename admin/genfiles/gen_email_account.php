<?php

require("genfiles/remote_mail_list.php");
require("genfiles/gen_qmail_email_account.php");
require("genfiles/gen_postfix_email_account.php");
require("genfiles/gen_maildrop_userdb.php");

function mail_account_generate(){
	global $conf_mta_type;

	switch($conf_mta_type){
	case "postfix":
		mail_account_generate_postfix();
		break;
	default:
	case "qmail":
		mail_account_generate_qmail();
		break;
	}

	// always generate maildrop
	// this will allow qmail to use maildrop along with postfix
	mail_account_generate_maildrop();

}

?>
