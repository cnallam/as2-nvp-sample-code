<?php

/*
'**************************************************************************
' This work product is provided "AS IS" and without warranty. PayPal
' expressly disclaims all implied warranties, including but not
' limited to warranties of merchantability and fitness for a particular
' purpose.
'https://developer.paypal.com/docs/archive/express-checkout/ht-ec-orderAuthPayment-curl-etc/#link-howtouseexpresscheckouttocreateandprocessanorder
'**************************************************************************
*/

function DoCapture($TRANSACTIONID) {

	require "config.php";

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
		
	// Construct the parameter string that describes the DoCapture API call
	$paymentAmount = "5.00";
	$paymentType = "DoCapture";
	$currencyCodeType = "USD";
	$completeType = "Complete";


	$nvpstr = "&AMT=". $paymentAmount;
	$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;
	$nvpstr = $nvpstr . "&AUTHORIZATIONID=" . $TRANSACTIONID;
	$nvpstr = $nvpstr . "&COMPLETETYPE=" . $completeType;

	//NVPRequest for submitting to server
	$nvpreq = "METHOD=DoCapture" . "&VERSION=204" . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . $nvpstr;

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
    
	echo "<p><b>DoCapture Call:<b></p>"; 
	foreach($var as $v):
		if (strpos($v, 'TOKEN') === 0) {
			$msg = explode('=', $v);
		}	
		echo $v . "</br>"; //remove first "//" to print to screen
	endforeach;
}

?>
