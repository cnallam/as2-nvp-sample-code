<?php

/*
'**************************************************************************
' This work product is provided "AS IS" and without warranty. PayPal
' expressly disclaims all implied warranties, including but not
' limited to warranties of merchantability and fitness for a particular
' purpose.
' https://developer.paypal.com/docs/archive/express-checkout/ht-ec-orderAuthPayment-curl-etc/#link-howtouseexpresscheckouttocreateandprocessanorder
'**************************************************************************
*/

require "config.php";

//start the session
session_start();
				
//turn php errors on
ini_set("track_errors", true);

// PayPal API Credentials
$API_UserName = $USER;
$API_Password = $PWD;
$API_Signature = $SIGNATURE;

//Create a unique invoice id
date_default_timezone_set('America/Chicago');
$createInvoiceID = date('dmyhis');

//Define the PayPal Redirect URLs.
//This is the URL that the buyer is first sent to do authorize payment with their paypal account
//change the URL depending if you are testing on the sandbox or the live PayPal site
$API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
$PAYPAL_URL = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
		
// Construct the parameter string that describes the SetExpressCheckout API call

$paymentAction = "Authorization";
$paymentAmount = $_REQUEST["PaymentAmount"];
  $_SESSION["PaymentAmount"] = $paymentAmount; //store for next API call  
$paymentType = "Order";
  $_SESSION["PaymentType"] = $paymentType;		//store for next API call 
$returnURL = "https://rmcgovernppl-tech.com/qa/Qleap/classic_order_example/GetExpressCheckoutDetails.php";
$cancelURL = "https://rmcgovernppl-tech.com/qa/Main.html";
$currencyCodeType = "USD";
  $_SESSION["currencyCodeType"] =$_REQUEST["currencyCodeType"];;	// store for next API call 

$nvpstr = "&PAYMENTREQUEST_n_PAYMENTACTION=". $paymentAction;
$nvpstr = "&PAYMENTREQUEST_0_AMT=". $paymentAmount;
$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymentType;
$nvpstr = $nvpstr . "&RETURNURL=" . $returnURL;
$nvpstr = $nvpstr . "&CANCELURL=" . $cancelURL;
$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_INVNUM=" . $createInvoiceID;
$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;


//NVPRequest for submitting to server
$nvpreq = "METHOD=SetExpressCheckout" . "&VERSION=204" . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . $nvpstr;

//setting the curl parameters.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$API_Endpoint);
curl_setopt($ch, CURLOPT_VERBOSE, 1);

//turning off the server and peer verification(TrustManager Concept).
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POST, 1);

//setting the nvpreq as POST FIELD to curl
curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

//getting response from server
$response = curl_exec($ch);
$decodeResponse = rawurldecode($response);

//parse the response data
$var = explode('&', $decodeResponse);

foreach($var as $v):
	if (strpos($v, 'ACK') === 0) {
		$msg = explode('=', $v);
		foreach($msg as $m):
			$ack = $m;
		endforeach;
	}
	if (strpos($v, 'L_ERRORCODE0') === 0) {
		$msg = explode('=', $v);
		foreach($msg as $m):
			$error_code = $m;
		endforeach;
	}
	if (strpos($v, 'L_LONGMESSAGE0') === 0) {
		$msg = explode('=', $v);
		foreach($msg as $m):
			$error_message = $m;
		endforeach;
	}
	if (strpos($v, 'CORRELATIONID') === 0) {
		$msg = explode('=', $v);
		foreach($msg as $m):
			$debug_id = $m;
		endforeach;
	}
	if (strpos($v, 'TOKEN') === 0) {
		$msg = explode('=', $v);
		foreach($msg as $m):
			$TOKEN = $m;
			$_SESSION["token_id"] = $TOKEN;
		endforeach;
	}	
	//echo $v . "</br>"; //remove first "//" to print to screen
endforeach;

if ($ack == "Failure") {
	echo "error  code: " . $error_code . "<br />";
	echo "error description: " . $error_message . "<br />";
	echo "debug id: " . $debug_id;	
} else {
// Redirect to paypal.com here
$payPalURL = $PAYPAL_URL . $TOKEN;
header("Location: ".$payPalURL);
exit;
}

?>
