<?php
/**
 * This makes our search work and supplies the variables.
 * I did not want to use MVC to keep it super simple.
 */
require 'api/api-search.php';
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
        <a class="navbar-brand" href="index.php"><b>Elastic Search</b> PHP Example</a>
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
                <input type="text" id="search-query-input" name="query" class="input-lg" placeholder="Search.." value="<?=$query?>">
                <button id="search-query-btn" class="btn btn-lg btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
            </form>
            <small>Try <code>bread</code> for an example</small>
        </div>

    </div>
</div>

<!-- start:Container -->
<div class="container">


    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="alert alert-danger">
                <b>Wrong DATA:</b> The Category is just the TOTAL count of topics within the query. So if there are 21
                results no category will have over 21 categories. HOWEVER it is NOT searching via Categories because the
                search is for the TERMS and Categories is NESTED not a TERM I BELIEVE..

                <p>
                    NOTICE how the PRICE Works fine because its part mapped as an integer NOT a special nested type.
                    so the PROBLEM is the NESTED type searching. NOW, I can rethink a bit.
                </p>
            </div>
            <div class="alert alert-danger">
                <b>Issues:</b> I want to be able to filter down more SUBCATEGORIES, eg: Bread, Sports, etc in the URI.
            </div>
        </div>
    </div>

    <!-- start:FilterDisplay -->
    <?php if (!empty($query)): ?>
        <div class="row" id="filters-wrapper">
            <div class="col-md-8 col-md-offset-2">
                <div class="row">
                    <div class="col-md-1">
                        <strong>Price</strong>
                    </div>
                    <div class="col-md-9">
                        <?php foreach ($aggregations['aggregations']['price_ranges']['buckets'] as $bucket):?>
                            <a class="btn btn-default btn-xs" href="<?=$query_string(['startprice' => $bucket['from'], 'endprice' => $bucket['to']])?>" class="<?=$bucket['from'] == $startPrice && $bucket['to'] == $endPrice ? 'active' : '';?>">
                                $<?=$bucket['from']?> - $<?=$bucket['to'];?> (<b><?=$bucket['doc_count'];?></b>)
                            </a>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="filters-wrapper">
            <div class="col-md-8 col-md-offset-2">
                <div class="row">
                    <div class="col-md-1">
                        <strong>Status</strong>
                    </div>
                    <div class="col-md-9 text-align: left">
                    <?php foreach ($aggregations['aggregations']['statuses']['buckets'] as $bucket):?>
                        <a class="btn btn-default btn-xs" href="<?=$query_string(['status' => $bucket['key']])?>" class="<?=$bucket['key'] == $status ? 'active' : '';?>">
                            <?=ucfirst($bucket['key']);?> (<b><?=$bucket['doc_count'];?></b>)
                        </a>
                    <?php endforeach;?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="filters-wrapper">
            <div class="col-md-8 col-md-offset-2">
                <div class="row">
                    <div class="col-md-1">
                        <strong>Category</strong>
                    </div>
                    <div class="col-md-9 text-align: left">
                        <?php foreach ($aggregations['aggregations']['categories']['categories_count']['buckets'] as $bucket):?>
                            <a class="btn btn-primary btn-xs"  href="<?=$query_string(['category' => $bucket['key']])?>" class="<?=$bucket['key'] == $category ? 'active' : '';?>">
                                <?=ucfirst($bucket['key']);?> (<b><?=$bucket['doc_count'];?></b>)
                            </a>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif;?>
    <!-- end:FilterDisplay -->

    <!-- start:QueryDisplay -->
    <?php if (!empty($hits)): ?>
        <div class="row" id="results-text">
            <div class="col-xs-8 col-xs-offset-2">
                <div class="alert alert-info text-center">
                    Displaying results <?=($from + 1);?> to <?=$to;?> of <?=$total;?>.
                </div>
            </div>
        </div>

        <?php foreach ($hits as $hit):?>
            <div class="row">
                <div class="col-xs-8 col-xs-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                             <a href="single-item.php?product_id=<?=$hit['_id'];?>&query=<?=$query?>"><?=$hit['_source']['name'];?></a>
                             <span class="pull-right badge">id: <?=$hit['_id'];?></span>
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


                        <?php for ($i = 1; $i <= $page_count; $i++): ?>
                            <li <?=($i == $page) ? 'class="active"' : '';?>><a href="<?=$query_string?>"><?=$i;?></a></li>
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
                <div class="alert alert-warning">
                    <p>No results</p>
                </div>
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

<!-- Custom JS-->
<script src="assets/js/app.js"></script>


</body>
</html>

