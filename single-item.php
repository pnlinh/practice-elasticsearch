<?php
// Bootstrap loads the necessary files to Query.
require 'src/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="assets/third-party/favicon.ico">

<title>Elastic Search Example (View Item)</title>

<!-- Bootstrap core CSS -->
<link href="assets/third-party/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/third-party/font-awesome/css/font-awesome.min.css" rel="stylesheet">

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<link href="assets/third-party/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

<!-- Custom CSS  -->
<link href="assets/css/screen.css" rel="stylesheet">

<!-- IE Emulation  -->
<script src="assets/third-party/js/ie-emulation-modes-warning.js"></script>

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
    <div class="navbar-header">
        <!-- No Small Navigation needed -->
        <a class="navbar-brand" href="#"><b>Elastic Search</b> Example (View Item)</a>
    </div>
    </div>
</nav>

<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
    <div class="container text-center">
        <h1><?=$product['name']?></h1>
        <p><?=$product['description']?></p>
    </div>
</div>

<!-- start:Container -->
<div class="container">

    <div class="row">
        <div class="col-xs-6 col-xs-offset-3">
            <a href="index.php?<?=$_SERVER['QUERY_STRING']?>" class="btn btn-primary"><span class="glyphicon glyphicon-chevron-left"></span> Back</a>

            <a href="#" class="btn btn-success"><span class="glyphicon glyphicon-usd"></span> Buy now for $<?=$product['price']?>!</a>
            <a href="#" class="btn btn-info"><span class="glyphicon glyphicon-tag"></span> <?=$product['quantity']?> In Stock</a>
        </div>
    </div>

</div>
<!-- end:Container -->

<hr>

<footer>
    <div class="container">
        <p>
            <a href="../LICENSE">Open Source MIT</a>
            &copy;2017 Jesse Boyer | <a href="https://jream.com" target="_blank">JREAM</a>
            </p>
    </div> <!-- /container -->
</footer>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="assets/third-party/js/jquery.min.js"></script>
<script src="assets/third-party/bootstrap/js/bootstrap.min.js"></script>

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="assets/third-party/js/ie10-viewport-bug-workaround.js"></script>

<!-- Custom JS-->
<script src="assets/js/app.js"></script>


</body>
</html>

