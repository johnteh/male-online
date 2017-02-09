<?php
    require_once("mo.php");
    require_once("conf.php");
    require_once("db.php");
?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>The HTML5 Herald</title>
  <meta name="description" content="The Male Online">
  <meta name="author" content="SitePoint">
  <meta name="viewport" content="width=device-width; initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no; " />

  <link rel="stylesheet" href="css/styles.css?v=1.0">

  <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
  <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js" integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw=" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Eczar:800" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

  <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
  <![endif]-->
</head>

<body>

<main>
    <header>
        <p>The Male Online</p>
        <nav data-bind="navigation">
            <span></span>
            <span></span>
            <span></span>
        </nav>
    </header>
    <div class="mo-main">

    </div>
    <div id="sidebar-tab" class="mo-sidebar-container" data-bind="sidebar">
        <ul class="mo-sidebar-tabs">
            <li><a href="#tab-1">Words</a></li>
            <li><a href="#tab-2">Years</a></li>
        </ul>
        <div id="tab-1">
            <ul>
                <?php foreach ( $list_of_bad_words as $word_array) { ?>
                    <?php foreach ( $word_array as $word_display_sidebar) { ?>
                        <li><input type="radio" name="sidebar-word" value="<?php echo $word_display_sidebar ?>" id="year-<?php echo $word_display_sidebar ?>">
                            <label for="year-<?php echo $word_display_sidebar ?>" data-bind="sidebar-selection"><?php echo $word_display_sidebar ?></label>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
        <div id="tab-2">
            <ul class="mo-sidebar-content">
                <li><input type="radio" name="sidebar-year" value="today" id="year-today">
                    <label for="year-today" data-bind="sidebar-selection">Today</label>
                </li>
                <?php foreach (range(2017, 1996) as $year_display_sidebar) { ?>
                    <li><input type="radio" name="sidebar-year" value="<?php echo $year_display_sidebar ?>" id="year-<?php echo $year_display_sidebar ?>">
                        <label for="year-<?php echo $year_display_sidebar ?>" data-bind="sidebar-selection"><?php echo $year_display_sidebar ?></label>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</main>

<script src="//localhost:35729/livereload.js"></script>
</body>
</html>
<script>
(function(jQuery) {
	var MaleOnlineFunctions = function ($){
		var self = this;

		self.init = function() {
            navToggle();
            sidebarSelection();
            $('[for="year-today"]').trigger('click');
            $('#sidebar-tab').tabs();
		};
        self.navToggle = function() {
            $('[data-bind="navigation"]').on('click', function() {
                if (!$(this).hasClass('active')) {
                    $('[data-bind="sidebar"]').css('right', '-0');
                    $(this).addClass('active');
                } else {
                    $('[data-bind="sidebar"]').css('right', '-200px');
                    $(this).removeClass('active');
                }

            });
        };
        self.sidebarSelection = function() {
            $('[data-bind="sidebar-selection"]').on('click', function() {
                $sidebar_value = $(this).prev().val();
                $main_component ="";
                $data = "";
                if ($sidebar_value == "today") {
                    $.get("daily-list.php", function(data) {
                        $('.mo-main').html(data);
                    });
                } else {
                    $.ajax({
                        url: "yearly-list.php",
                        type: "POST",
                        data: {
                            year: $sidebar_value
                        },
                        success: function(data) {
                            $('.mo-main').html(data);
                        }
                    });
                }


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
