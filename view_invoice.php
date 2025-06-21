<?php
session_start();
include('db.php');

if(!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = $_GET['booking_id'] ?? 0;
$customer_email = $_SESSION['customer_email'];

// Verify that the invoice belongs to the logged-in customer
$sql = "SELECT p.* FROM payment p 
        JOIN roombook r ON p.id = r.id 
        WHERE p.id = '$booking_id' AND r.Email = '$customer_email'";
$result = mysqli_query($con, $sql);

if(mysqli_num_rows($result) == 0) {
    header("Location: customer_invoices.php");
    exit();
}

$invoice = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
    <meta charset="utf-8">
    <title>Invoice #<?php echo $invoice['id']; ?></title>
    <!-- Use the same styles as print.php -->
    <style>
       	<style>
		/* reset */

*
{
	border: 0;
	box-sizing: content-box;
	color: inherit;
	font-family: inherit;
	font-size: inherit;
	font-style: inherit;
	font-weight: inherit;
	line-height: inherit;
	list-style: none;
	margin: 0;
	padding: 0;
	text-decoration: none;
	vertical-align: top;
}

/* content editable */

*[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

*[contenteditable] { cursor: pointer; }

*[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 1em 0.5em #DEF; }

span[contenteditable] { display: inline-block; }

/* heading */

h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }

/* table */

table { font-size: 75%; table-layout: fixed; width: 100%; }
table { border-collapse: separate; border-spacing: 2px; }
th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
th, td { border-radius: 0.25em; border-style: solid; }
th { background: #EEE; border-color: #BBB; }
td { border-color: #DDD; }

/* page */

html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; }
html { background: #999; cursor: default; }

body { box-sizing: border-box; height: 11in; margin: 0 auto; overflow: hidden; padding: 0.5in; width: 8.5in; }
body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }

/* header */

header { margin: 0 0 3em; }
header:after { clear: both; content: ""; display: table; }

header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
header address p { margin: 0 0 0.25em; }
header span, header img { display: block; float: right; }
header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
header img { max-height: 100%; max-width: 100%; }
header input { cursor: pointer; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

/* article */

article, article address, table.meta, table.inventory { margin: 0 0 3em; }
article:after { clear: both; content: ""; display: table; }
article h1 { clip: rect(0 0 0 0); position: absolute; }

article address { float: left; font-size: 125%; font-weight: bold; }

/* table meta & balance */

table.meta, table.balance { float: right; width: 36%; }
table.meta:after, table.balance:after { clear: both; content: ""; display: table; }

/* table meta */

table.meta th { width: 40%; }
table.meta td { width: 60%; }

/* table items */

table.inventory { clear: both; width: 100%; }
table.inventory th { font-weight: bold; text-align: center; }

table.inventory td:nth-child(1) { width: 26%; }
table.inventory td:nth-child(2) { width: 38%; }
table.inventory td:nth-child(3) { text-align: right; width: 12%; }
table.inventory td:nth-child(4) { text-align: right; width: 12%; }
table.inventory td:nth-child(5) { text-align: right; width: 12%; }

/* table balance */

table.balance th, table.balance td { width: 50%; }
table.balance td { text-align: right; }

/* aside */

aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; }
aside h1 { border-color: #999; border-bottom-style: solid; }

/* javascript */

.add, .cut
{
	border-width: 1px;
	display: block;
	font-size: .8rem;
	padding: 0.25em 0.5em;	
	float: left;
	text-align: center;
	width: 0.6em;
}

.add, .cut
{
	background: #9AF;
	box-shadow: 0 1px 2px rgba(0,0,0,0.2);
	background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
	background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
	border-radius: 0.5em;
	border-color: #0076A3;
	color: #FFF;
	cursor: pointer;
	font-weight: bold;
	text-shadow: 0 -1px 2px rgba(0,0,0,0.333);
}

.add { margin: -2.5em 0 0; }

.add:hover { background: #00ADEE; }

.cut { opacity: 0; position: absolute; top: 0; left: -1.5em; }
.cut { -webkit-transition: opacity 100ms ease-in; }

tr:hover .cut { opacity: 1; }

@media print {
	* { -webkit-print-color-adjust: exact; }
	html { background: none; padding: 0; }
	body { box-shadow: none; margin: 0; }
	span:empty { display: none; }
	.add, .cut { display: none; }
}

@page { margin: 0; }
		</style>
    </style>
</head>
<body>
    <!-- Use the same HTML structure as print.php but with the customer's invoice data -->
    <header>
        <h1>Invoice</h1>
        <address>
            <p>EL SANCHEL,</p>
            <p>Bicutan, <br>Parañaque,<br>Philippines.</p>
            <p>0970 784 9353</p>
        </address>
        <span><img alt="" src="assets/img/ELSANCH.png"></span>
    </header>
    <article>
        <h1>Recipient</h1>
        <address>
            <p><?php echo $invoice['title'].$invoice['fname']." ".$invoice['lname']; ?> <br></p>
        </address>
        <table class="meta">
            <tr>
                <th><span>Invoice #</span></th>
                <td><span><?php echo $invoice['id']; ?></span></td>
            </tr>
            <tr>
                <th><span>Date</span></th>
                <td><span><?php echo $invoice['cout']; ?> </span></td>
            </tr>
        </table>
        <table class="inventory">
            <thead>
                <tr>
                    <th><span>Item</span></th>
                    <th><span>No of Days</span></th>
                    <th><span>Rate</span></th>
                    <th><span>Quantity</span></th>
                    <th><span>Price</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span><?php echo $invoice['troom']; ?></span></td>
                    <td><span><?php echo $invoice['noofdays']; ?> </span></td>
                    <td><span data-prefix>₱</span><span><?php echo number_format($invoice['ttot'] / ($invoice['guest'] * $invoice['noofdays']), 2); ?></span></td>
                    <td><span><?php echo $invoice['guest'];?> </span></td>
                    <td><span data-prefix>₱</span><span><?php echo number_format($invoice['ttot'], 2); ?></span></td>
                </tr>
                <tr>
                    <td><span><?php echo $invoice['tbed']; ?> Bed</span></td>
                    <td><span><?php echo $invoice['noofdays']; ?></span></td>
                    <td><span data-prefix>₱</span><span><?php echo number_format($invoice['btot'] / ($invoice['guest'] * $invoice['noofdays']), 2); ?></span></td>
                    <td><span><?php echo $invoice['guest'];?> </span></td>
                    <td><span data-prefix>₱</span><span><?php echo number_format($invoice['btot'], 2); ?></span></td>
                </tr>
            </tbody>
        </table>
        <table class="balance">
            <tr>
                <th><span>Total</span></th>
                <td><span data-prefix>₱</span><span><?php echo number_format($invoice['fintot'], 2); ?></span></td>
            </tr>
            <tr>
                <th><span>Amount Paid</span></th>
                <td><span data-prefix>₱</span><span>0.00</span></td>
            </tr>
            <tr>
                <th><span>Balance Due</span></th>
                <td><span data-prefix>₱</span><span><?php echo number_format($invoice['fintot'], 2); ?></span></td>
            </tr>
        </table>
    </article>
    <aside>
        <h1><span>Contact us</span></h1>
        <div>
            <p align="center">Email :- elsanchel.business@gmail.com</p>
            <p align="center">----------------------------------------------------------</p>
            <p align="center">Facebook :- https://www.facebook.com/elsanchel</p>
            <p align="center">----------------------------------------------------------</p>
            <p align="center">Web :- https://www.tiktok.com/@elsanchel</p>
            <p align="center">----------------------------------------------------------</p>
            <p align="center">Phone :- 0970 784 9353 </p>
        </div>
    </aside>
</body>
</html>