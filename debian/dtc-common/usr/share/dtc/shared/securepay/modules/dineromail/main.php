<?php

// Should return a decimal with added gateway fees.
function dineromail_calculate_fee($amount){
/*
Fees: ( described in https://argentina.dineromail.com/CostosLimites.asp )
COLLECT AND RECEIVE MONEY THROUGH DineroMail HAVE THE FOLLOWING COSTS:
Receive Funds: 2.99% + $ 0.50 (fixed)
Credit card sales by up to 24 shares: 4.99% + $ 1.50 (fixed)
Sale and other payment methods: Up to 2.99% + $ 1.50 (fixed) depending on Monthly Sales Transaction price:
		Up to $ 5,000: 3.99% + $ 1.50
		More than $ 5,000 to $ 25,000: 3.5% + $ 1.50
		More than $ 25,000 to $ 50,000: 3.2% + $ 1.50
		Over $ 50,000: 2.99% + $ 1.50
International Trade: A commission will be charged 5.5% + $ 1.50 whatever the medium of payment used.
Withdraw Funds - Bank Transfer: $ 3.00
Withdraw Funds - Branch Banking Cheque: $ 6.62
Withdraw Funds - Receive Money Order: $ 37.19
Withdraw Funds - Check Mail: $ 18.18
The advertised do not include VAT (Iva = 21% in Argentina)
The committees agreed to the payment may be transferred wholly or partly to the buyer.
*/
	//global $secpayconf_dineromail_tipospago; // this will be used on module upgrade to manage different fees
	global $secpayconf_dineromail_cargocomision;
	global $secpayconf_dineromail_porcentajecomision;
	$total = $amount + ($amount * $secpayconf_dineromail_porcentajecomision / 100) + $secpayconf_dineromail_cargocomision;
	return $total;
}

// Display the payment link option
function dineromail_display_icon($product_id,$amount,$item_name,$return_url,$use_recurring = "no"){
	global $secpayconf_dineromail_nrocuenta;
	global $secpayconf_dineromail_tipospago;
	global $secpayconf_dineromail_logo_url;
	
	$ncta = preg_split('/\//',$secpayconf_dineromail_nrocuenta);
	
	$amount = round(floatval(str_replace(",",".",$amount)), 2);
	
	$out = '<form action="https://argentina.dineromail.com/Shop/Shop_Ingreso.asp" method="post">'."\n";
	$out .= '<input type="hidden" name="NombreItem" value="'.$item_name.'">'."\n"; // name of the phurchased service
	$out .= '<input type="hidden" name="TipoMoneda" value="1">'."\n"; // currency: 1=pesos 2=dollar
	$out .= '<input type="hidden" name="PrecioItem" value="'.str_replace(',','.',$amount).'">'."\n"; // payment ammount
	$out .= '<input type="hidden" name="E_Comercio" value="'.$ncta[0].'">'."\n"; // dineromail account (without the "/" part and without the final digit)
	$out .= '<input type="hidden" name="NroItem" value="'.$product_id.'">'."\n"; // item id
	$out .= '<input type="hidden" name="image_url" value="http://">'."\n"; // image of the company to place in the payslip
	$out .= '<input type="hidden" name="DireccionExito" value="'.$_SERVER['HTTP_HOST'].$return_url.'">'."\n"; // where to redirect once the payslip is done and ok
	$out .= '<input type="hidden" name="DireccionFracaso" value="'.$_SERVER['HTTP_HOST'].$return_url.'">'."\n"; // where to redirect when the data is wrong
	$out .= '<input type="hidden" name="DireccionEnvio" value="0">'."\n"; // if is set to 1, the customer can write s shipping address
	$out .= '<input type="hidden" name="Mensaje" value="0">'."\n"; // if is set to 1, the customer can send a message to the seller
	$out .= '<input type="hidden" name="MediosPago" value="'.$secpayconf_dineromail_tipospago.'">'."\n"; //payment method:
		// No ingreses ningún valor para aceptar todos los medios de pago.
		// Ingresa “2” para códigos de barras, “7” para fondos en cuenta DineroMail, “13” para Transferencia
		// bancaria. Pago en 1, 3, 6, 9, 12, 18 y 24 cuotas con tarjeta de crédito ingresa “4”, “5”, “6”, “14”, “15”, “16” y
		// “17” respectivamente. Para Plan Z ingresa “18”. O combina los métodos que desees mediante comas (Ej: 4,5,6,13)
	$out .= '<input type="hidden" name="TRX_ID" value="'.$product_id.'">'."\n"; // transaction id
	$out .= '<input type="hidden" name="usr_nombre" value="">'."\n"; // customer's name
	$out .= '<input type="hidden" name="usr_apellido" value="">'."\n"; // customer's surname
	$out .= '<input type="hidden" name="usr_tel_numero" value="">'."\n"; // customer's phone number
	$out .= '<input type="hidden" name="usr_email" value="">'."\n"; // customer's email
	$out .= '<input type="image" src="';
	if (empty($secpayconf_dineromail_logo_url))
		{
		$out .= 'https://argentina.dineromail.com/imagenes/post-login/boton-comprar-01.gif';
		}
	else
		{
		$out .= $secpayconf_dineromail_logo_url;
		}
	$out .= '" border="0" name="submit" alt="';
	$out .= _("Pay by DineroMail") . '">';
	$out .= '</form>'."\n";

	return $out;
}

$secpay_modules[] = array(
	"display_icon" => "dineromail_display_icon",
	"use_module" => $secpayconf_use_dineromail,
	"calculate_fee" => "dineromail_calculate_fee",
	"instant_account" => _("No")
);

?>
