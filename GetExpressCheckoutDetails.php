<?php

/*
'**************************************************************************
' This work product is provided "AS IS" and without warranty. PayPal
' expressly disclaims all implied warranties, including but not
' limited to warranties of merchantability and fitness for a particular
' purpose.
'**************************************************************************
*/

require "config.php";
require "DoExpressCheckoutPayment.php";

//start the session
session_start();

// PayPal API Credentials
$API_UserName = $USER;
$API_Password = $PWD;
$API_Signature = $SIGNATURE;

//Get Information from the querystring
$TOKEN = $_GET['token'];
$payer_id = $_GET['PayerID'];

//change the URL depending if you are testing on the sandbox or the live PayPal site
$API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";

// Construct the parameter string that describes the GetExpressCheckoutDetails API Call
$nvpstr = "&TOKEN=" . $TOKEN;

//NVPRequest for submitting to server
$nvpreq = "METHOD=GetExpressCheckoutDetails" . "&VERSION=204" . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . $nvpstr;

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


//parse the response data
//get order details
$decodeResponse = rawurldecode($response);
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
	//complete payment flow
	DoExpressCheckoutPayment($TOKEN, $payer_id);
}

?>
