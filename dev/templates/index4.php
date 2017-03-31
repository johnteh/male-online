<?php
require_once("mo.php");
require_once("conf.php");
require_once("db.php");
ini_set("error_reporting","-1");
ini_set("display_errors","On");

$sort_array = array();
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
    <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js" integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw=" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
    <script src="jquery.resize.js"></script>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
    <link rel="stylesheet" href="css/styles.css?v=1.0">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Bigshot+One" rel="stylesheet">


    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <![endif]-->
</head>

<body>
    <div class="site-wrapper">
        <!-- <div id="off-canvas-menu" class="navigation-menu">
            <li>test</li>
            <li>test</li>
            <li>test</li>
            <li>test</li>
            <li>test</li>
            <li>test</li>
            <li>test</li>
            <li>test</li>
            <li>test</li>
            <li>test</li>
            <li>test</li>
            <li>test</li>
        </div> -->
        <header class="main-header">
            <!-- <nav class="nav-icon" data-bind="menu">
                <span></span>
                <span></span>
                <span></span>
            </nav> -->
            <a class="site-logo" href="/index3.php">
                <p class="close">
                    <span class="flam-text">Male </span>
                    <span class="thin-text">Online</span>
                </p>
            </a>
            <p class="archive-icon" data-bind="archive" >ARCHIVE<span>&nbsp;></span></p>
        </header>
        <main class="main-content">
            <div class="graph-wrapper">
                <?php foreach (getBadWords() as $word): ?>
                    <?php include 'word-graph.php' ?>
                <?php endforeach ?>
            </div>
        </main>
        <footer>
            Footer footer
        </footer>
    </div>
    <script src="//localhost:35729/livereload.js"></script>
</body>
</html>
<script>
(function(jQuery) {
    var MaleOnlineFunctions = function ($){
        var self = this;
        var $chartistWordValues= [];
        var $chartistWordLabels = [];
        var $mychart;

        self.init = function() {
            // menuToggle();
            // archiveToggle();
            // sidebarSelection();
            // // $('[for="year-today"]').trigger('click');
            // $('#sidebar-tab').tabs();
            // toggleCollapse();
            // $('.content-wrapper').resize(resize);
            wordGraph();
        };
        self.resize = function() {
            if ( $($mychart).length > 0 ) {
                $($mychart).get(0).__chartist__.update();
            }
        };
        self.archiveToggle = function() {
            $('[data-bind="archive"]').on('click', function() {
                $('.site-wrapper').toggleClass('archive');
                var $sbar = $('#sidebar-tab');
                // if ($($sbar).hasClass('active')) {
                //     $($sbar).children('.sidebar-panel').css('display', 'block');
                // } else {
                //     setTimeout(function () {
                //         $($sbar).children('.sidebar-panel').css('display', 'none');
                //     }, 500);
                // }
            });
        };
        self.menuToggle = function() {
            $('[data-bind="menu"]').on('click', function() {
                $('.site-wrapper').toggleClass('menu');
            });
        };
        self.sidebarSelection = function() {
            $('[data-bind="sidebar-year-selection"]').on('click', function() {
                $sidebar_value = $(this).prev().val();
                $main_component ="";
                $data = "";
                if ($sidebar_value == "today") {
                    $.get("daily-list.php", function(data) {
                        $('.main-content').html(data);
                        self.toggleCollapse();
                    });
                } else {
                    $.ajax({
                        url: "yearly-list.php",
                        type: "POST",
                        data: {
                            year: $sidebar_value
                        },
                        success: function(data) {
                            $('.main-content').html(data);
                        }
                    });
                }
            });
            $('[data-bind="sidebar-word-selection"]').on('click', function() {
                $sidebar_value = $(this).prev().val();
                if (typeof $previous_value === "undefined" || $sidebar_value != $previous_value) {
                    $previous_value = $sidebar_value;
                    $main_component ="";
                    $data = "";
                    $.ajax({
                        url: "word-graph.php",
                        type: "POST",
                        data: {
                            word: $sidebar_value
                        },
                        success: function(data) {
                            $('.main-content').html(data);
                            self.wordGraph();
                        }
                    });
                }
            });
        };
        self.toggleCollapse = function() {
            $('[data-toggle="collapse"]').on('click', function() {
                // if ($(this).attr('aria-expanded') == "true") {
                //     $(this).attr('aria-expanded', 'false');
                //     $($(this).data('target')).slideUp(300);
                // } else {
                //     var $current = $('.results-list li[aria-expanded="true"]');
                //     $($current).attr('aria-expanded', 'false');
                //     $($($current).data('target')).slideUp(300);
                //
                //     var $target = $(this).data('target');
                //     $(this).attr('aria-expanded', 'true');
                //     $($target).slideDown(300);
                // }
                var $target = $(this).data('target');
                $($target).dialog({
                    modal: true,
                    show: 'fade',
                    hide: 'fade',
                    closeText: "X",
                    resizable: false,
                    draggable: false,
                    open: function(event, ui) {
                        $('body').addClass('modal-open');
                    },
                    beforeClose: function(event, ui) {
                        $('body').removeClass('modal-open');
                        $(this).dialog('destroy');
                    }
                });
            });
        };
        self.wordGraph = function() {
            $('.chart').each(function() {
                var $id = $(this).attr('id');
                var $word = $(this).attr('id');
                var $graph_vals = $(this).find('.word-value');
                var $graph_labels = $(this).find('.word-key');
                $($graph_vals).each(function() {
                    $chartistWordValues.push(parseInt($(this).text()));
                });
                $($graph_labels).each(function() {
                    $chartistWordLabels.push(parseInt($(this).text()));
                });
                var data = {
                    series: [$chartistWordValues],
                    labels: $chartistWordLabels
                };
                var options = {
                    lineSmooth: true,
                    showArea: true,
                    fullWidth: true,
                    chartPadding: {
                      right: 10
                    },
                    axisX: {
                        showGrid: false,
                        showLabel: true
                    },
                    axisY: {
                        offset: 0,
                        showGrid: false,
                        showLabel: false,
                    }
                };
                $mychart = new Chartist.Line('.'+ $word, data, options);
                //$mychart = $('.'+ $word +'-chart');
                // $mychart.get(0).__chartist__.update(data);
                $chartistWordLabels = [];
                $chartistWordValues = [];
            });
        };
        return {
            init: init,
        }
    };
    // Setup the global object and run init on document ready
    $(function(){
        window.MaleOnlineFunctions = MaleOnlineFunctions(jQuery);
        window.MaleOnlineFunctions.init();
    });
})(jQuery);
</script>
