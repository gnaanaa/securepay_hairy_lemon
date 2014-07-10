<?php session_start(); 
/**************************************** 
* File		: index.php
* Author	: Gnana - gnaanaa@gmail.com
* Date		: 18/03/2013
* Desc		:
 PHP payment form that accepts (Name, Address, Email Address, Product(only one product can be purchased in an order), Product Quantity, Comment).
 When the form is submitted the customer should be taken to a hosted payment page on the Secure Paytech site to accept credit card payment details for the selected product.  Test merchant account details should be used and should construct a suitable unique order reference.
 After payment has been made successfully a thank you page should be displayed and the customer sent a confirmation email.
 Also the order reference, users name, address, email address, product id, quantity, comment, date, time and ip address should be stored in a MySQL database. 
 ****************************************
*/
?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>HairyLemon Payment Page</TITLE>
</HEAD>
<BODY>
<?php
/*
* Class  : dbFunctions
* Desc   : Simple DB operations using mysqli are initiated
*/
class dbFunctions {

	var $con;
	
	function __construct() {
		$this->con = new mysqli("localhost", "gnaanaa_payment", "payment", "gnaanaa_payment");
	}
	
	function __destruct() {
		$this->con->close();
	}
	// Convert DB query result to an array
	function resultToArray($result) {
		$rows = array();
		while($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		return $rows;
	}
	// Generic function to select records
	function selectRecords($sql){
		if (!$result = $this->con->query($sql)) return false;
		$rows = $this->resultToArray($result);
		return $rows;
	}
	// Generic function to insert or update
	function insertUpdateRecords($sql){
		$result = $this->con->query($sql);
		return $result;
	}
	// Get last inserted ID
	function getLastInsert(){
		return $this->con->insert_id;
	}
}

// Initiate dbFunctions class
$dbFunctions = new dbFunctions();
// Validation
$errors = '';
// Step 1 : If post, validate all the post variables
if($_SERVER['REQUEST_METHOD'] == 'POST'){

	if ($_POST['name'] != "") {
		$_POST['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
		if ($_POST['name'] == "") {
			$errors .= 'Please enter a valid name.<br/><br/>';
		}
	} else {
		$errors .= 'Please enter your name.<br/>';
	}
	
	if ($_POST['address'] != "") {
		$_POST['address'] = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
		if ($_POST['address'] == "") {
			$errors .= 'Please enter a valid address.<br/><br/>';
		}
	} else {
		$errors .= 'Please enter your address.<br/>';
	}
	
	if ($_POST['email'] != "") {  
		$_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);  
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {  
			$errors .= $_POST['email']." is <strong>NOT</strong> a valid email address.<br/><br/>";  
		}  
	} else {  
		$errors .= 'Please enter your email address.<br/>';  
	}
	
	if ($_POST['quantity'] != "") {
		$_POST['quantity'] = filter_var($_POST['quantity'], FILTER_SANITIZE_STRING);
		if (!filter_var($_POST['quantity'], FILTER_VALIDATE_INT)) {  
			$errors .= $_POST['quantity']." is <strong>NOT</strong> a valid integer.<br/><br/>";  
		}  
	} else {
		$errors .= 'Please enter quantity.<br/>';
	}
	
	if ($_POST['comment'] != "") {
		$_POST['comment'] = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
		if ($_POST['comment'] == "") {
			$errors .= 'Please enter a valid comment.<br/><br/>';
		}
	} else {
		$errors .= 'Please enter your comment.<br/>';
	}
}

// Step 2 : If posted with no validation errors, proceed to prepare payment gateway
if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($errors)){
	// get POST values and store in DB
	$name = $_POST['name'];
	$address = $_POST['address'];
	$email = $_POST['email'];
	$product = $_POST['product'];
	$productPrice = $dbFunctions->selectRecords("SELECT ItemPrice FROM items WHERE ItemID ='".mysql_real_escape_string($product)."'");
	$price = $productPrice[0]['ItemPrice'];
	$quantity = $_POST['quantity'];
	$amount = $quantity*$price;
	$comment = $_POST['comment'];
	$merchantID = $_POST['merchantID'];
	$returnURL = $_POST['returnURL'];
	$cancelURL = $_POST['cancelURL'];
	$orderReference = $_POST['orderReference'];
	$ip = $_SERVER ['REMOTE_ADDR'];
	$transactionTime = date('Y/m/d H:i:s');
	$staus = false;
	
	// Insert transaction record and store transaction ID into SESSION with trnasaction status false
	$query = "insert INTO transactions (orderReference, userName, userAddress, userEmail, ItemID, quantity, comment, ipAddress, staus, transactionTime) 
				VALUES ('$orderReference', '$name', '$address', '$email', '$product', '$quantity', '$comment', '$ip', '$staus', '$transactionTime')"; 
	$insert = $dbFunctions->insertUpdateRecords($query);
	$transactionID = $dbFunctions->getLastInsert();
	
	// set session variables
	$_SESSION['transactionID'] = $transactionID;
	$_SESSION['orderReference '] = $orderReference;
	
	//what post fields?
	$fields = array(
	   'amount'=>$amount,
	   'merchantID'=>$merchantID,
	   'returnURL'=>$returnURL.'&transactionID='.$transactionID.'&orderReference='.$orderReference,
	   'cancelURL'=>$cancelURL.'&transactionID='.$transactionID.'&orderReference='.$orderReference,
	   'orderReference'=>$orderReference,
	);

	if($insert){
		//where are we posting to?
		$url = 'https://merchant.securepaytech.com/paymentpage/index.php';
		?>
		<form action='<?php echo $url?>' method='post' name='mypaymentform'>
		<?php
		foreach ($fields as $a => $b) {
			echo "<input type='hidden' name='".$a."' value='".$b."'>";
		}
		?>
		</form>
		<script language="JavaScript">
			document.mypaymentform.submit();
		</script>
		<?php
	}
}// Step 3 : If transaction completed from payment gateway, proceed updating the transaction status back to database.
else if(isset($_GET['transactionStatus']) && isset($_GET['transactionID']) && isset($_GET['orderReference'])) 
{
	$transactionStatus = $_GET['transactionStatus'];
	$transactionID = $_GET['transactionID'];
	$orderReference = $_GET['orderReference'];
	
	// Check on the validity of transaction
	if($_SESSION['orderReference '] == $orderReference )
	{
		if($transactionStatus) // Payment is successful
		{
			// update the transaction status to true
			$query = "UPDATE transactions SET staus = '$transactionStatus' WHERE transactionID ='$transactionID'"; 
			$update = $dbFunctions->insertUpdateRecords($query);
			if($update)
			{
				// Print success message
				print "<H1>Transaction succes! <BR /> Thank you!</H1>";
				// send trnasaction success email
				$userDetails = $dbFunctions->selectRecords("SELECT userName, userEmail, ItemID, quantity FROM transactions WHERE transactionID ='$transactionID'");
				$userName = $userDetails[0]['userName'];
				$userEmail = $userDetails[0]['userEmail'];
				$ItemID = $userDetails[0]['ItemID'];
				$quantity = $userDetails[0]['quantity'];
				$productPrice = $dbFunctions->selectRecords("SELECT ItemName, ItemPrice FROM items WHERE ItemID ='$ItemID'");
				$price = $productPrice[0]['ItemPrice'];
				$ItemName = $productPrice[0]['ItemName'];
				$body = "Dear $userName,\n\n your recent purchase of $quantity numbers of $ItemName total price of \$ ".$quantity*$price." is successful.\n\n HairyLemon Payment Admin.";
				$ehead = "From: admin@gnaanaa.com\r\n";
				// Send success email
				if($mailsend=mail("$userEmail","Transaction succesful!","$body","$ehead"))
				{
					print "<H2>An Email sent to you for the confirmation!</H2>";
				}else{
					print "<H2>Email unsuccess!</H2>";
				}
				
			}
		}else	// Cancelled transaction
		{
			print "<H1>Transaction Cancelled! <BR /> Thank you!</H1>";
		}
	}
	else	// Came here with not valid session
	{
		print "<H1>Session Expired! <BR /> Please start over!</H1>";
	}
	?>
	<a href="index.php">Go to purchase page</a>
	<?php
}
else // Step 0 : New to purchase page or If POST with validation errors, welcome to the home page 
{
?>
	<H1>
	<?php
	echo "Purchase Page";
	// Get unique order reference
	$orderReference = uniqid("Ref-",true);
	?>
	</H1>
	<FORM action="" method="POST" id="mypaymentform">
	<DIV><?php echo $errors ?></DIV>
	 <UL>
		 <LI>Name<INPUT type="text" class="textField" placeholder="Name" name="name" value=<?php echo (isset($_POST['name']))? $_POST['name']:''; ?> ></LI>
		 <LI>Address<INPUT type="text" class="textField" placeholder="Address" name="address" value=<?php echo (isset($_POST['address']))? $_POST['address']:''; ?> ></LI>
		 <LI>Email address<INPUT type="text" class="textField" placeholder="Email address" name="email"value=<?php echo (isset($_POST['email']))? $_POST['email']:''; ?> ></LI>
		 <LI>Product<SELECT type="select" name="product" />
		 <?php
			foreach ($rows = $dbFunctions->selectRecords("SELECT* FROM items") as $row){
				if(isset($_POST['product']) && $_POST['product'] == $row['ItemID']){
					print '<option value ="'. $row['ItemID'] .' " selected >'. $row['ItemName'] .' | $'. number_format($row['ItemPrice'],2) .'</option>';
				}else{
					print '<option value ="'. $row['ItemID'] .'">'. $row['ItemName'] .' | $'. number_format($row['ItemPrice'],2) .'</option>';
				}
			}
		 ?>
		 </SELECT>
		 <LI>Quantity<INPUT type="text" class="textField" placeholder="Quantity" name="quantity" value=<?php echo (isset($_POST['quantity']))? $_POST['quantity']:''; ?> ></LI>
		 <LI>Comment<INPUT type="text" class="textField" placeholder="Comment" name="comment" value=<?php echo (isset($_POST['comment']))? $_POST['comment']:''; ?> ></LI>
	 </UL>
	 <INPUT type="hidden" name="merchantID" value="TESTDIGISPL1" />
	 <INPUT type="hidden" name="returnURL" value="http://www.gnaanaa.com/hairylemon_payment/index.php?transactionStatus=1" />
	 <INPUT type="hidden" name="cancelURL" value="http://www.gnaanaa.com/hairylemon_payment/index.php?transactionStatus=0" />
	 <INPUT type="hidden" name="orderReference" value="<?php echo $orderReference ?>"/>
	 <INPUT id="sub" type="submit" value="Pay by Credit Card" />
	</FORM>	
<?php
}
?>
</BODY>
</HTML>