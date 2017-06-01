<?php
require_once("mo.php");
require_once("conf.php");
require_once("db.php");
ini_set("error_reporting","-1");
ini_set("display_errors","On");

$sort_array = array();
$matched_articles = array();
$mo_homepage_url = "http://www.dailymail.co.uk/home/index.html";
$xpath_article_query_string = "//div[@class='beta']//div[contains(concat(' ', normalize-space(@class), ' '), 'femail')]//li | //div[@class='beta']//div[contains(concat(' ', normalize-space(@class), ' '), 'tvshowbiz')]//li";
?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title>The Male Online</title>
    <meta name="description" content="The Male Online">
    <meta name="author" content="SitePoint">
    <meta name="viewport" content="width=device-width; initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no;" />

    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
    <script src="js/jquery.resize.js"></script>
    <script src="js/jquery.randomColor.js"></script>
    <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js" integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw=" crossorigin="anonymous"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/rangeslider.min.js"></script>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
    <link rel="stylesheet" href="css/rangeslider.css?v=1.0">
    <link rel="stylesheet" href="css/styles.css?v=1.0">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Bigshot+One" rel="stylesheet">


    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <![endif]-->
</head>

<body>
    <div class="site-wrapper">
        <header class="main-header">
            <a class="site-logo" href="/index4.php">
                <span class="flam-text">Male </span>
                <span class="thin-text">Online</span>
            </a>
            <nav class="nav-icon" data-bind="menu">
                <span></span>
                <span></span>
                <span></span>
            </nav>
        </header>
        <main class="container">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a id="today-tab" href="#today" aria-controls="today" role="tab" data-toggle="tab">Today</a></li>
                <li role="presentation"><a id="trends-tab" href="#trends" aria-controls="trends" role="tab" data-toggle="tab">Trends</a></li>
                <li role="presentation"><a id="years-tab" href="#years" aria-controls="years" role="tab" data-toggle="tab">Years</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane tab-pane-today active" id="today">
                    <?php getListOfArticleLinks([$mo_homepage_url], $xpath_article_query_string); ?>
                    <?php cleanTable('today_count'); ?>
                    <?php setTodaysArticles($matched_articles); ?>
                    <h4>On today's homepage</h4>
                    <?php include 'daily-list.php' ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="trends">
                    <h4>Mentions over time</h4>
                    <div class="trends-grid">
                        <?php foreach (getBadWords() as $word): ?>
                            <?php include 'word-graph.php' ?>
                        <?php endforeach ?>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="years">
                    <h4>Mentions during the year: <span id="slider-output" class="year-range-slider">2001</span></h4>
                    <input type="range" min="2001" max="2017" value="2001" step="1" data-rangeslider>

                    <div class="graph-container yearly-chart clearfix">
                    <?php foreach ($years as $year): ?>
                        <?php $yearlyResults = getYearlyTotals($year); ?>
                        <ul class="hidden-word-results chart-values-<?php echo $year ?>">
                            <?php foreach ($yearlyResults as $row): ?>
                                <li><span class="yearly-word-key"><?php echo $row['word'] ?></span>
                                <span class="yearly-word-value"><?php echo $row['count'] ?></span></li>
                            <?php endforeach ?>
                        </ul>
                    <?php endforeach ?>
                    <canvas id="yearsChart" height="600"></canvas>
                </div>
            </div>
        </main>
        <!-- <footer>
            Footer footer
        </footer> -->
    </div>
    <script src="//localhost:35729/livereload.js"></script>
</body>
</html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js"></script>
<script src="js/scripts.js"></script>
