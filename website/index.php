<?php
/**
 * This makes our search work and supplies the variables.
 * I did not want to use MVC to keep it super simple.
 */
require 'inc/search.php';
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

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
    <div class="navbar-header">
        <!-- No Small Navigation needed -->
        <a class="navbar-brand" href="#"><b>Elastic Search</b> PHP &amp; JS Example</a>
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
            <!-- Notice we are using GET -->
            <form id="search-form" method="get" action="index.php">
                <input type="text" id="search-query-input" name="query" class="input-lg" placeholder="Search..">
                <button id="search-query-btn" class="btn btn-lg btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
            </form>
            <small>Try <code>Bread</code> for an example</small>
        </div>

    </div>
</div>

<!-- start:Container -->
<div class="container">

    <!-- start:FilterDisplay -->
    <?php if (!empty($query)): ?>
        <div class="row" id="filters-wrapper">
            <div class="col-xs-6 col-xs-offset-3">
                <strong>Price:</strong>

                <?php foreach ($aggregations['aggregations']['price_ranges']['buckets'] as $bucket):?>
                    <a href="?query=<?=$query;?>&page=<?=$page;?>&startprice=<?=$bucket['from'];?>&endprice=<?=$bucket['to'];?>&status=<?=$status or '';?>&category=<?=$category or '';?>" class="<?=$bucket['from'] == $startPrice && $bucket['to'] == $endPrice ? 'active' : '';?>">
                        <?=$bucket['from']?> - <?=$bucket['to'];?> (<?=$bucket['doc_count'];?>)
                    </a>
                <?php endforeach;?>

                <br />

                <strong>Status:</strong>

                <?php foreach ($aggregations['aggregations']['statuses']['buckets'] as $bucket):?>
                    <a href="?query=<?=$query;?>&page=<?=$page;?>&status=<?=urlencode($bucket['key']);?>&startprice=<?=$startPrice or '';?>&endprice=<?=$endPrice or '';?>&category=<?=$category or '';?>" class="<?=$bucket['key'] == $status ? 'active' : '';?>">
                        <?=ucfirst($bucket['key']);?> (<?=$bucket['doc_count'];?>)
                    </a>
                <?php endforeach;?>

                <br />

                <strong>Category:</strong>

                <?php foreach ($aggregations['aggregations']['categories']['categories_count']['buckets'] as $bucket):?>
                    <a href="?query=<?=$query;?>&page=<?=$page;?>&category=<?=urlencode($bucket['key']);?>&status=<?=$status or '';?>&startprice=<?=$startPrice or '';?>&endprice=<?=$endPrice or '';?>" class="<?=$bucket['key'] == $category ? 'active' : '';?>">
                        <?=ucfirst($bucket['key']);?> (<?=$bucket['doc_count'];?>)
                    </a>
                <?php endforeach;?>
            </div>
        </div>
    <?php endif;?>
    <!-- end:FilterDisplay -->

    <!-- start:QueryDisplay -->
    <?php if (!empty($hits)): ?>
        <div class="row" id="results-text">
            <div class="col-xs-8 col-xs-offset-2">
                Displaying results <?=($from + 1);?> to <?=$to;?> of <?=$total;?>.
            </div>
        </div>

        <?php foreach ($hits as $hit):?>
            <div class="row">
                <div class="col-xs-8 col-xs-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="/product/view/<?=$hit['_id'];?>"><?=$hit['_source']['name'];?></a>
                        </div>

                        <div class="panel-body">
                            <p><?=$hit['_source']['description'];?></p>

                            <strong>Price:</strong> <?=$hit['_source']['price'];?>
                            <br />
                            <strong>Status:</strong> <?=ucfirst($hit['_source']['status']);?>
                            <br />
                            <strong>Categories:</strong>

                            <?php foreach ($hit['_source']['categories'] as $c):?>
                                <?=$c['name'];?> &nbsp;
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach;?>


        <!-- start:PaginationDisplay -->
        <div class="row">
            <div class="pagination-wrapper col-xs-8 col-xs-offset-2">
                <nav>
                    <ul class="pagination">
                        <li>
                            <a href="?query=<?=urlencode($query);?>&page=<?=($page - 1);?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <li <?=($i == $page) ? 'class="active"' : '';?>><a href="?query=<?=urlencode($query);?>&page=<?=$i;?>&status=<?=$status or '';?>&startprice=<?=$startPrice or '';?>&endprice=<?=$endPrice or '';?>&category=<?=$category or '';?>"><?=$i;?></a></li>
                        <?php endfor;?>

                        <li>
                            <a href="?query=<?=urlencode($query);?>&page=<?=($page + 1);?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    <!-- end:PaginationDisplay -->

    <?php elseif (isset($hits)): ?>
        <div class="row" id="no-results">
            <div class="col-xs-6 col-xs-offset-3">
                <p>No results!</p>
            </div>
        </div>
    <?php endif;?>
    <!-- end:QueryDisplay -->

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

</body>
</html>

