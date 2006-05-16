<?php
/**
 * @package DTC
 * @version $Id: error_strings.php,v 1.12 2006/05/16 15:32:58 thomas Exp $
 * 
 */

/**
 * TG: Added some code to check password format of new admins
 *
 */

$txt_err_dtc_login_format = array(
  "fr" => "Mauvais format du mot de passe du login administrateur: il doit jtre composi uniquement de lettre non capitales, de nombre ou du signe \"-\" et doit faire entre 4 et 16 caractères.<br>\n",
  "en" => "Incorect admin login format: it should be made only with lowercase letters or numbers or the \"-\" sign and should be between 4 and 16 chars long.<br>\n",
  "hu" => "TRANS Incorect admin login format: it should be made only with lowercase letters or numbers or the \"-\" sign and should be between 4 and 16 chars long.<br>\n",
  "it" => "TRANS Formato del login email errato: dovrebbe essere composto solo da lettere minuscole o numeri o dal simbolo \"-\" and should be between 4 and 16 chars long.<br>\n",
  "nl" => "TRANS Incorect admin login format: it should be made only with lowercase letters or numbers or the \"-\" sign and should be between 4 and 16 chars long.<br>\n",
  "ru" => "TRANS Incorect admin login format: it should be made only with lowercase letters or numbers or the \"-\" sign and should be between 4 and 16 chars long.<br>\n",
  "de" => "TRANS Incorect admin login format: it should be made only with lowercase letters or numbers or the \"-\" sign and should be between 4 and 16 chars long.<br>\n",
  "zh" => "TRANS Incorect admin login format: it should be made only with lowercase letters or numbers or the \"-\" sign and should be between 4 and 16 chars long.<br>\n",
  "pl" => "TRANS B³êdny format admin lub loginu: mo¿esz u¿yæ ma³ych liter, cyfr lub znaku \"-\" (minus) and should be between 4 and 16 chars long.<br>\n",
  "es" => "TRANS Incorect admin login format: it should be made only with lowercase letters or numbers or the \"-\" sign and should be between 4 and 16 chars long.<br>\n",
  "pt" => "TRANS Incorect admin login format: it should be made only with lowercase letters or numbers or the \"-\" sign and should be between 4 and 16 chars long.<br>\n");

/**
 * new localization for new account
 * @see dtc/client/new_account.php
 * 
 */
/*
NOT VALIDATED $txt_err_payment_not_valid[$lang]
TRANSACTION FINISHED AND APPROVED $txt_err_payment_finish_approved[$lang]
PAYMENT CANCELED $txt_err_payment_cancel[$lang]
PAYMENT FAILED $txt_err_payment_failed[$lang]

"Cannot reselect transaction for id $extapi_pay_id: registration failed!" $txt_err_register_cant_reselect_trans[$lang]
"Cannot reselect user: registration failed!" $txt_err_register_cant_reselect_user[$lang]
"Cannot reselect product: registration failed!" $txt_err_register_cant_reselect_product[$lang]
$txt_err_register_succ[$lang]
*/
// TG remark to Seeb: This didn't work as the code that makes this variable is called AFTER this file...
// That code is now moved before the include. We don't need the global here as it's not in a function.
// When you see this text, please just delete it, together with that global variable.
//global $extapi_pay_id; // bug in my script - seeb



$txt_err_payment_not_valid=array(
	"fr" => "NON VALIDE",
	"en" => "NOT VALIDATED",
	"hu" => "NOT VALIDATED",
	"it" => "NOT VALIDATED",
	"nl" => "NOT VALIDATED",
	"ru" => "NOT VALIDATED",
	"de" => "NOT VALIDATED",
	"zh" => "NOT VALIDATED",
	"pl" => "P£ATNO¦Æ NIESPRAWDZONA",
	"se" => "TRANS SVENSKA",
	"es" => "NOT VALIDATED",
	"pt" => "NOT VALIDATED"
);
$txt_err_payment_finish_approved=array(
	"fr" => "TRANSACTION FINIE ET APPROUVEE",
	"en" => "TRANSACTION FINISHED AND APPROVED",
	"hu" => "TRANSACTION FINISHED AND APPROVED",
	"it" => "TRANSACTION FINISHED AND APPROVED",
	"nl" => "TRANSACTION FINISHED AND APPROVED",
	"ru" => "TRANSACTION FINISHED AND APPROVED",
	"de" => "TRANSACTION FINISHED AND APPROVED",
	"zh" => "TRANSACTION FINISHED AND APPROVED",
	"pl" => "TRANZAKCJA ZAKOÑCZONA I ZAAKCEPTOWANA",
	"se" => "TRANS SVENSKA",
	"es" => "TRANSACTION FINISHED AND APPROVED",
	"pt" => "TRANSACTION FINISHED AND APPROVED"
);
$txt_err_payment_cancel=array(
	"fr" => "PAYMENT ABANDONNE",
	"en" => "PAYMENT CANCELED",
	"hu" => "PAYMENT CANCELED",
	"it" => "PAYMENT CANCELED",
	"nl" => "PAYMENT CANCELED",
	"ru" => "PAYMENT CANCELED",
	"de" => "PAYMENT CANCELED",
	"zh" => "PAYMENT CANCELED",
	"pl" => "P£ATNO¦Æ ANULOWANA",
	"se" => "TRANS SVENSKA",
	"es" => "PAYMENT CANCELED",
	"pt" => "PAYMENT CANCELED"
);

$txt_err_payment_failed=array(
	"fr" => "ECHEC DU PAYMENT",
	"en" => "PAYMENT FAILED",
	"hu" => "PAYMENT FAILED",
	"it" => "PAYMENT FAILED",
	"nl" => "PAYMENT FAILED",
	"ru" => "PAYMENT FAILED",
	"de" => "PAYMENT FAILED",
	"zh" => "PAYMENT FAILED",
	"pl" => "P£ATNO¦Æ NIEUDANA",
	"se" => "TRANS SVENSKA",
	"es" => "PAYMENT FAILED",
	"pt" => "PAYMENT FAILED"
);

if(isset($extapi_pay_id)){
$txt_err_register_cant_reselect_trans=array(
	"fr" => "Impossible de resélectionner la transaction numéro $extapi_pay_id: echec de l'enregistrement!",
	"en" => "Cannot reselect transaction for id $extapi_pay_id: registration failed!",
	"hu" => "Cannot reselect transaction for id $extapi_pay_id: registration failed!",
	"it" => "Cannot reselect transaction for id $extapi_pay_id: registration failed!",
	"nl" => "Cannot reselect transaction for id $extapi_pay_id: registration failed!",
	"ru" => "Cannot reselect transaction for id $extapi_pay_id: registration failed!",
	"de" => "Cannot reselect transaction for id $extapi_pay_id: registration failed!",
	"zh" => "Cannot reselect transaction for id $extapi_pay_id: registration failed!",
	"pl" => "Nie mogê ponownie wybraæ transakcji dla ID $extapi_pay_id: Rejestracja nieudana!",
	"se" => "TRANS SVENSKA",
	"es" => "Cannot reselect transaction for id $extapi_pay_id: registration failed!",
	"pt" => "Cannot reselect transaction for id $extapi_pay_id: registration failed!"
);
}

$txt_err_register_cant_reselect_user=array(
	"fr" => "Impossible de resélectionner l'utilisateur: echec de l'enregistrement!",
	"en" => "Cannot reselect user: registration failed!",
	"hu" => "Cannot reselect user: registration failed!",
	"it" => "Cannot reselect user: registration failed!",
	"nl" => "Cannot reselect user: registration failed!",
	"ru" => "Cannot reselect user: registration failed!",
	"de" => "Cannot reselect user: registration failed!",
	"zh" => "Cannot reselect user: registration failed!",
	"pl" => "Nie mogê ponownie wybraæ u¿ytkownika: Rejestracja nieudana!",
	"se" => "TRANS SVENSKA",
	"es" => "Cannot reselect user: registration failed!",
	"pt" => "Cannot reselect user: registration failed!"
);
$txt_err_register_cant_reselect_product=array(
	"fr" => "Impossible de resélectionner le produit: échec de l'enregistrement!",
	"en" => "Cannot reselect product: registration failed!",
	"hu" => "Cannot reselect product: registration failed!",
	"it" => "Cannot reselect product: registration failed!",
	"nl" => "Cannot reselect product: registration failed!",
	"ru" => "Cannot reselect product: registration failed!",
	"de" => "Cannot reselect product: registration failed!",
	"zh" => "Cannot reselect product: registration failed!",
	"pl" => "Nie mogê ponownie wybraæ produktu: Rejestracja nieudana!",
	"se" => "TRANS SVENSKA",
	"es" => "Cannot reselect product: registration failed!",
	"pt" => "Cannot reselect product: registration failed!"
);

$txt_err_register_succ=array (
	"fr" => "Enregistrement réussit!",
	"en" => "Registration successfull!",
	"hu" => "Registration successfull!",
	"it" => "Registration successfull!",
	"nl" => "Registration successfull!",
	"ru" => "Registration successfull!",
	"de" => "Registration successfull!",
	"zh" => "Registration successfull!",
	"pl" => "Rejestracja przebieg³a pomy¶nie",
	"se" => "TRANS SVENSKA",
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
  "se" => "TRANS SVENSKA",
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
  "se" => "TRANS SVENSKA",
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
  "se" => "TRANS SVENSKA",
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
  "se" => "TRANS SVENSKA",
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
	"se" => "TRANS SVENSKA",
	"pl" => "TRANS ",
	"es" => "TRANS ",
	"pt" => "TRANS "
);
?>