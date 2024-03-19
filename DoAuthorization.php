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
require "DoCapture.php";

//start the session
session_start();
				
//turn php errors on
ini_set("track_errors", true);

// PayPal API Credentials
$API_UserName = $USER;
$API_Password = $PWD;
$API_Signature = $SIGNATURE;

//change the URL depending if you are testing on the sandbox or the live PayPal site
$API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
		
// Construct the parameter string that describes the DoAuthorzation API call
$authorization_id = $_REQUEST['authorizeID'];
$paymentAmount = "5.00";
$currencyCodeType = "USD";

$nvpstr = "&AMT=". $paymentAmount;
$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;
$nvpstr = $nvpstr . "&TRANSACTIONID=" . $authorization_id;

//NVPRequest for submitting to server
$nvpreq = "METHOD=DoAuthorization" . "&VERSION=204" . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . $nvpstr;

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
	if (strpos($v, 'TRANSACTIONID') === 0) {
		$msg = explode('=', $v);
		foreach($msg as $m):
			$TRANSACTIONID = $m;
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
	DoCapture($TRANSACTIONID);
}

?>
