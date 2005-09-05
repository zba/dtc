<?php

include_once "webnic_settings.php";
require_once "webnic_submit.php";

// input parameters

// $source = webnic username
// $password = webnic password
// $domain_details = hash of the details below 

// Parameters to be Submitted:
// source 	Reseller's source
// otime 	Order time, format YYYY-MM-DD HH:MM:SS. Example: 2000-02-20 12:03:33
// url  	This is the full path URL to your template CGI. The Webnic server will communicate its results to this CGI.
// ochecksum 	MD5 validation key between reseller and Webnic.
// domainname 	Domain to be registered. Currently support romanize domain for com/net/org, cc, biz, info, com.cn/net.cn/org.cn/cn. ( example : domain.cc, domain.net)
// encoding 	Encoding type that support multilingual domain name. Accepted type: big5/bg/sjis/euc/kuc/iso8859-8/iso8859-6/iso8859-1
// lang 	Language tag parameter that is compulsory for IDN registration. ( see below )
// term 	Total years for registration. Accepted range: 1 - 10 years. Maximum 5 years for .cn/.com.cn/.net.cn/.org.cn
// ns1 	Primary DNS
// ns2 	Secondary DNS
// reg_company 	Registrant
// reg_fname 	Registrant's First name
// reg_lname 	Registrant's Last name
// reg_addr1 	Registrant's address 1
// reg_addr2 	Registrant's address 2
// reg_state 	Registrant's State
// reg_city 	Registrant's City
// reg_postcode 	Registrant's Postal Code
// reg_telephone 	Registrant's Telephone ( Format : +123.1234567890 )
// reg_fax 	Registrant’s Fax ( Format : +123.1234567890 )
// reg_country 	Registrant’s Country
// reg_email  	Registrant's E-mail ( must be in correct format )
// flag_adm  	1 : Administrative contact is same as registrant’s contact
// adm_company 	Administrative contact organization
// adm_fname 	Admin first name
// adm_lname 	Admin last name
// adm_addr1 	Admin address 1
// adm_addr2 	Admin address 2
// adm_state 	Admin state
// adm_city 	Admin city
// adm_postcode 	Admin postal code
// adm_telephone  	Admin Telephone ( Format : +123.1234567890 )
// adm_fax 	Admin Fax ( Format : +123.1234567890 )
// adm_country 	Admin country
// adm_email  	Admin E-mail
// flag_tec  	1 : Technical contact is same as registrant’s contact
// tec_company 	Technical contact organization
// tec_fname 	Technical first name
// tec_lname 	Technical last name
// tec_addr1 	Technical address 1
// tec_addr2 	Technical address 2
// tec_state 	Technical state
// tec_city 	Technical city
// tec_postcode 	Technical postal code
// tec_telephone  	Technical Telephone ( Format : +123.1234567890 )
// tec_fax 	Technical Fax ( Format : +123.1234567890 )
// tec_country 	Technical country
// tec_email  	Technical E-mail
// flag_bil  	1 : Billing contact is same as registrant’s contact
// bil_company 	Billing contact organization
// bil_fname 	Billing first name
// bil_lname 	Billing last name
// bil_addr1 	Billing address 1
// bil_addr2 	Billing address 2
// bil_state 	Billing state
// bil_city 	Billing city
// bil_postcode 	Billing postal code
// bil_telephone  	Billing Telephone ( Format : +123.1234567890 )
// bil_fax 	Billing Fax ( Format : +123.1234567890 )
// bil_country 	Billing country
// bil_email  	Billing E-mail
// username 	Unified login Username (Optional)
// password 	Password for Unified login (Optional)
// .US ONLY
// purpose 	P1 - Business (for profit) Use
// 		P2 - Non-profit Business or Organization Use
// 		P3 - Personal Use
// 		P4 - Educational Purposes
// 		P5 - Government Purposes
// nexus 	C11 - Citizen or National of the United States
// 		C12 - A permanent resident of the United States or any U.S. territory/possession.
// 		C21 - Incorporated within one of the U.S. states or U.S. territory
// 		C31 - An entity that regularly engages in lawful activities (sales of goods or services or other business, commercial or non-commercial, including not-for-profit relations in the United States)
// 		C32 - An entity that has an office or facility in the U.S.

// LANGUAGE for IDN

// "AFR"    Afrikaans
// "ALB"    Albanian
// "ARA"    Arabic
// "ARM"    Armenian
// "AZE"    Azerbaijani
// "BAQ"    Basque
// "BUL"    Bulgarian
// "BUR"    Burmese
// "CAT"    Catalan
// "CHI"    Chinese (Traditional)
// "CHI"    Chinese (Simplified)
// "SCR"    Croatian
// "CZE"    Czech
// "DAN"    Danish
// "DUT"    Dutch
// "ENG"    English
// "EST"    Estonian
// "FAO"    Faroese
// "FIN"    Finnish
// "FRE"    French
// "GEO"    Georgian
// "DEU"    German
// "HEB"    Hebrew
// "HIN"    Hindi
// "HUN"    Hungarian
// "ICE"    Icelandic
// "ITA"    Italian
// "JPN"    Japanese
// "KOR"    Korean
// "KUR"    Kurdish
// "LAO"    Lao
// "LAV"    Latvian
// "LIT"    Lithuanian
// "MAC"    Macedonian
// "MAL"    Malayalm
// "NEP"    Nepali
// "NOR"    Norwegian
// "PER"    Persian
// "POL"    Polish
// "POR"    Portuguese
// "RUM"    Romanian
// "RUS"    Russian
// "SAN"    Sanskirt
// "SCC"    Serbian
// "SLO"    Slovak
// "SLV"    Slovenian
// "SPA"    Spanish
// "SWA"    Swahili
// "SWE"    Swedish
// "SYR"    Syriac
// "TAM"    Tamil
// "THA"    Thai
// "TIB"    Tibetan
// "TUR"    Turkish
// "UKR"    Ukrainian
// "URD"    Urdu
// "UZB"    Uzbek
// "VIE"    Vietnamese

// ochecksum is as follows:

// Note: MD5 validation code (ochecksum) is produced as follows:
// ochecksum = md5(source+otime+md5(password))
// "Password" refers to the Webnic partner's password. otime are identical to variables submitted by partner during registration.

// returns 99 if the input parameters are wrong
// returns error code as follows for other errors

// ok  	 message
// 0 	"YYYY-MM-DD" Register successful, return expire date.
// 1 	Registrant error message return by registry
// 2 	IP authentication fail
// 3 	Partner authentication fail
// 4 	Partner not enough credit balance
// 5 	Registry: https connection error
// 6 	Invalid IP called
// 7 	Require field missing / Invalid format
// 8 	Invalid domain extension
// 9 	User ID not valid
// 10 	Race domain are no longer accepted for registration

// returns details of registration if succesful
function domainRegistration($source, $password, $domain_details)
{
	$current_time=date("Y-m-d H:i:s");
	// current time in format: YYYY-MM-DD HH:MM:SS / Y-m-d H:i:s
        $post_param_hash["otime"]=$current_time;
	// URL to send post back to
	if (isset($domain_details["url"])){
		$post_param_hash["url"] = $domain_details["url"];
	}
	// MD5 validation key between reseller and Webnic.
	//  ochecksum = md5(source+otime+md5(password))
	$post_param_hash["ochecksum"] = md5($source . $current_time . md5($password));
	// domainname - Domain to be registered. 
	if (isset($domain_details["domainname"]))
	{
		$post_param_hash["domainname"]=$domain_details["domainname"];
	} else {
		return "99\nNo Domain Name Specified in request\n";
	}
	// encoding -  Encoding type that support multilingual domain name.

	// lang - Language tag parameter that is compulsory for IDN registration.

	// term -  Total years for registration. (1-10) 

	// ns1 -  Primary DNS
	
	// ns2 -  Secondary DNS

	// reg_company - Registrant


        $url = "https://pay.web.cc/new/cgi-bin/pn_reg.cgi";
        return webnic_submit($url, $source, $post_param_hash);
}
