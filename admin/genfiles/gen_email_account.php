<?php

require("genfiles/gen_qmail_email_account.php");
require("genfiles/gen_postfix_email_account.php");

function mail_account_generate()
{
	mail_account_generate_postfix();
	mail_account_generate_qmail();
}

?>
