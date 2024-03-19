<?php
/*
**************************************************************************
 This work product is provided "AS IS" and without warranty. PayPal
 expressly disclaims all implied warranties, including but not
 limited to warranties of merchantability and fitness for a particular
 purpose.
**************************************************************************
*/
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Authorization and Capture</title>
</head>

<body>
	<!-- Set up a container element for the button -->
	<form name="frm" method="post" action="DoAuthorization.php">
		<p><label for="authorizeID">Transaction ID:</label>
		<input type="text" id="authorizeID" name="authorizeID"></p>
		<button type="submit">Authorize and Capture</button>
	</form>
</body>

</html>
