<?php

//API Credentials
//You must provide a valid set of API credentials when making calls to PayPal API operations. 
//This allows PayPal to verify the account that's making the calls.
//https://developer.paypal.com/api/nvp-soap/get-started/#link-apicredentials

$server = "SANDBOX";
//$server = "LIVE";

if ($server == "LIVE") {
	# production credentials
	$USER = "";
	$PWD = "";
	$SIGNATURE = "";
        $VERSION = "";
} elseif ($server == "SANDBOX") {
	# sandbox credentials
	$USER = "mcgovernrtBusiness_api1.paypal.com";
	$PWD = "WFZDPG7UXWSKLB6M";
	$SIGNATURE = "A5HPsKuc5VEe9ppmlMcEfIQKtYKTAIwbRmFx2SnbOHEwvFhNPkFqFCrO";
        $VERSION = "204";
}
