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

<title>Elastic Search Example</title>

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

<?php
// Prefilled Search Items for Pagination
// $query = isset($_GET['query']) ? urldecode($_GET['query']) : false;
// $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
?>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
    <div class="navbar-header">
        <!-- No Small Navigation needed -->
        <a class="navbar-brand" href="#"><b>Elastic Search</b> Example</a>
    </div>
    </div>
</nav>

<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
    <div class="container">

        <div class="col-md-4 text-right">
            <h3>Search</h3>
        </div>
        <div class="col-md-4">
            <!-- The form does not need method="post" because AJAX is handling it -->
            <form id="search-form" action="search.php">
                <input type="text" id="search-query-input" name="query" class="input-lg" placeholder="Search..">
                <button id="search-query-btn" class="btn btn-lg btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
            </form>
            <small>Try <code>Bread</code> for an example</small>
        </div>

    </div>
</div>

<div class="container">
    <!-- Example row of columns -->
    <div class="row">
    <div class="col-md-4">
        <h2>Menu</h2>
        <div id="menu">
            <!-- Dynamic JS Results from Elasticsearch -->
        </div>
    </div>
    <div class="col-md-8">
        <h2>Search Results</h2>
        <div id="search-results">
            <!-- Dynamic JS Results from Elasticsearch -->
            <p>
            You haven't searched for anything yet, please fill out the <span class="focus" data-id="search-query-input">Search Form</a>.
            </p>
        </div>

        <div id="search-pagination">
            <!-- Dynamic JS Results from Elasticsearch -->
        </div>
    </div>
    </div>

</div> <!-- /container -->

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
<script src="assets/third-party/jquery-ui-custom/jquery-ui.min.js"></script>
<script src="assets/third-party/bootstrap/js/bootstrap.min.js"></script>

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="assets/third-party/js/ie10-viewport-bug-workaround.js"></script>

<!-- Custom JS -->
<script src="assets/js/app.js"></script>

</body>
</html>

