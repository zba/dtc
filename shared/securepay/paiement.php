<?php

require("$dtcshared_path/securepay/paiement_config.php");
if($conf_use_worldpay == "yes")	include("$dtcshared_path/securepay/gateways/worldpay.php");
if($conf_use_paypal == "yes")	include("$dtcshared_path/securepay/gateways/paypal.php");
require("$dtcshared_path/securepay/pay_functions.php");


?>