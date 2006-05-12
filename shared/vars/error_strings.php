<?php
/**
 * @package DTC
 * @version $Id: error_strings.php,v 1.5 2006/05/12 16:09:08 seeb Exp $
 * 
 */



/**
 * new localization for new account
 * @see dtc/client/new_account.php
 * 
 */
/*
NOT VALIDATED
TRANSACTION FINISHED AND APPROVED
PAYMENT CANCELED
PAYMENT FAILED

"Cannot reselect transaction for id $extapi_pay_id: registration failed!"
"Cannot reselect user: registration failed!"
"Cannot reselect product: registration failed!"

*/

$txt_err_payment_not_valid=array(
	"fr" => "TRANS ",
	"en" => "NOT VALIDATED",
	"hu" => "TRANS ",
	"it" => "TRANS ",
	"nl" => "TRANS ",
	"ru" => "TRANS ",
	"de" => "TRANS ",
	"zh" => "TRANS ",
	"pl" => "TRANS ",
	"es" => "TRANS ",
	"pt" => "TRANS "
);
$txt_err_payment_finish_approved=array(
	"fr" => "TRANS ",
	"en" => "TRANSACTION FINISHED AND APPROVED",
	"hu" => "TRANS ",
	"it" => "TRANS ",
	"nl" => "TRANS ",
	"ru" => "TRANS ",
	"de" => "TRANS ",
	"zh" => "TRANS ",
	"pl" => "TRANS ",
	"es" => "TRANS ",
	"pt" => "TRANS "
);
$txt_err_payment_=array(
	"fr" => "TRANS ",
	"en" => "PAYMENT CANCELED",
	"hu" => "TRANS ",
	"it" => "TRANS ",
	"nl" => "TRANS ",
	"ru" => "TRANS ",
	"de" => "TRANS ",
	"zh" => "TRANS ",
	"pl" => "TRANS ",
	"es" => "TRANS ",
	"pt" => "TRANS "
);

$txt_err_payment_failed=array(
	"fr" => "TRANS ",
	"en" => "PAYMENT FAILED",
	"hu" => "TRANS ",
	"it" => "TRANS ",
	"nl" => "TRANS ",
	"ru" => "TRANS ",
	"de" => "TRANS ",
	"zh" => "TRANS ",
	"pl" => "TRANS ",
	"es" => "TRANS ",
	"pt" => "TRANS "
);
$txt_err_register_cant_reselect_=array(
	"fr" => "TRANS ",
	"en" => "Cannot reselect transaction for id $extapi_pay_id: registration failed!",
	"hu" => "TRANS ",
	"it" => "TRANS ",
	"nl" => "TRANS ",
	"ru" => "TRANS ",
	"de" => "TRANS ",
	"zh" => "TRANS ",
	"pl" => "TRANS ",
	"es" => "TRANS ",
	"pt" => "TRANS "
);
$txt_err_register_cant_reselect_=array(
	"fr" => "TRANS ",
	"en" => "Cannot reselect user: registration failed!",
	"hu" => "TRANS ",
	"it" => "TRANS ",
	"nl" => "TRANS ",
	"ru" => "TRANS ",
	"de" => "TRANS ",
	"zh" => "TRANS ",
	"pl" => "TRANS ",
	"es" => "TRANS ",
	"pt" => "TRANS "
);
$txt_err_register_cant_reselect_=array(
	"fr" => "TRANS ",
	"en" => "Cannot reselect product: registration failed!",
	"hu" => "TRANS ",
	"it" => "TRANS ",
	"nl" => "TRANS ",
	"ru" => "TRANS ",
	"de" => "TRANS ",
	"zh" => "TRANS ",
	"pl" => "TRANS ",
	"es" => "TRANS ",
	"pt" => "TRANS "
);

$txt_err_register_succ=array (
	"fr" => "Registration successfull!",
	"en" => "Registration successfull!",
	"hu" => "Registration successfull!",
	"it" => "Registration successfull!",
	"nl" => "Registration successfull!",
	"ru" => "Registration successfull!",
	"de" => "Registration successfull!",
	"zh" => "Registration successfull!",
	"pl" => "Registration successfull!",
	"es" => "Registration successfull!",
	"pt" => "Registration successfull!"
);
/** end new arrays **/
$txt_err_email_format = array(
  "fr" => "Mauvais format du login mail: il doit être composé uniquement de lettre non capitales, de nombre ou du signe \"-\".<br>\n",
  "en" => "Incorect mail login format: it should be made only with lowercase letters or numbers or the \"-\" sign.<br>\n",
  "hu" => "TRANS Incorect mail login format: it should be made only with lowercase letters or numbers or the \"-\" sign.<br>\n",
  "it" => "Formato del login email errato: dovrebbe essere composto solo da lettere minuscole o numeri o dal simbolo \"-\".<br>\n",
  "nl" => "TRANS Incorect mail login format: it should be made only with lowercase letters or numbers or the \"-\" sign.<br>\n",
  "ru" => "TRANS Incorect mail login format: it should be made only with lowercase letters or numbers or the \"-\" sign.<br>\n",
  "de" => "TRANS Incorect mail login format: it should be made only with lowercase letters or numbers or the \"-\" sign.<br>\n",
  "zh" => "TRANS Incorect mail login format: it should be made only with lowercase letters or numbers or the \"-\" sign.<br>\n",
  "pl" => "B³êdny format maila lub loginu: mo¿esz u¿yæ ma³ych liter, cyfr lub znaku \"-\" (minus).<br>\n",
  "es" => "TRANS Incorect mail login format: it should be made only with lowercase letters or numbers or the \"-\" sign.<br>\n",
  "pt" => "TRANS Incorect mail login format: it should be made only with lowercase letters or numbers or the \"-\" sign.<br>\n");

$txt_err_mailbox_does_not_exists_in_db = array(
  "fr" => "Cette boite au lettre n'existe pas dans la base de donnée !<br>\n",
  "en" => "Mailbox does no exists in database!<br>\n",
  "hu" => "TRANS Mailbox does no exists in database!<br>\n",
  "it" => "La Mailbox non esiste nel database!<br>\n",
  "nl" => "TRANS Mailbox does no exists in database!<br>\n",
  "ru" => "TRANS Mailbox does no exists in database!<br>\n",
  "de" => "TRANS Mailbox does no exists in database!<br>\n",
  "zh" => "TRANS Mailbox does no exists in database!<br>\n",
  "pl" => "Nie ma takiej skrzynki!<br>\n",
  "es" => "TRANS Mailbox does no exists in database!<br>\n",
  "pt" => "TRANS Mailbox does no exists in database!<br>\n");

$txt_err_email_exists_as_mailinglist = array(
  "fr" => "Cette boite existe déjà dans la base en tant que liste de publipostage !<br>\n",
  "en" => "Mailbox allready exist in database as a mailing list!<br>\n",
  "hu" => "TRANS Mailbox allready exist in database as a mailing list!<br>\n",
  "it" => "La Mailbox esiste già nel database come mailing list!<br>\n",
  "nl" => "TRANS Mailbox allready exist in database as a mailing list!<br>\n",
  "ru" => "TRANS Mailbox allready exist in database as a mailing list!<br>\n",
  "de" => "TRANS Mailbox allready exist in database as a mailing list!<br>\n",
  "zh" => "TRANS Mailbox allready exist in database as a mailing list!<br>\n",
  "pl" => "Taka skrzynka ju¿ istnieje na liscie mailingowej!<br>\n",
  "es" => "TRANS Mailbox allready exist in database as a mailing list!<br>\n",
  "pt" => "TRANS Mailbox allready exist in database as a mailing list!<br>\n");

$txt_err_password_format = array(
  "fr" => "Les mots de passes doivent être fait uniquement de caractères, de chiffres (a-zA-Z0-9) et doivent faire entre 6 et 16 caractères.<br>\n",
  "en" => "Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.<br>\n",
  "hu" => "TRANS Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.<br>\n",
  "it" => "La password è costituita solo da caratteri standard e numeri (a-zA-Z0-9) e dovrebbe essere di lunghezza compresa fra i 6 e i 16 caratteri.<br>\n",
  "nl" => "TRANS Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.<br>\n",
  "ru" => "TRANS Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.<br>\n",
  "de" => "TRANS Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.<br>\n",
  "zh" => "TRANS Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.<br>\n",
  "pl" => "Has³o jest tworzone ze standardowego zestawu znaków (a-zA-Z0-9) i jego d³ugo¶æ mo¿e siê zawieraæ miêdzy 6 a 16 znaków.<br>\n",
  "es" => "TRANS Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.<br>\n",
  "pt" => "TRANS Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.<br>\n");

$txt_clear_array=array(
	"fr" => "TRANS ",
	"en" => "TRANS ",
	"hu" => "TRANS ",
	"it" => "TRANS ",
	"nl" => "TRANS ",
	"ru" => "TRANS ",
	"de" => "TRANS ",
	"zh" => "TRANS ",
	"pl" => "TRANS ",
	"es" => "TRANS ",
	"pt" => "TRANS "
);
?>