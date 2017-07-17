<?php
$mo_home_domain="http://dailymail.co.uk/";

$years = ['2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016'];

$current_year = "2017";

$sql_create_count_table = 'CREATE TABLE archive_count (
    entry_id INT(6) AUTO_INCREMENT NOT NULL PRIMARY KEY,
    publication_date DATE,
    word VARCHAR(20),
    article_text TEXT(10000),
    article_link TEXT(10000),
    thumbnail_link TEXT(10000)
    )';

$sql_create_today_count_table = 'CREATE TABLE today_count (
    entry_id INT(6) AUTO_INCREMENT NOT NULL PRIMARY KEY,
    publication_date DATE,
    word VARCHAR(20),
    article_text TEXT(1000),
    article_link TEXT(10000),
    thumbnail_link TEXT(10000)
    )';

$sql_create_yearly_table = 'CREATE TABLE yearly_count (
    entry_id INT(6) AUTO_INCREMENT NOT NULL PRIMARY KEY,
    year VARCHAR(4),
    word VARCHAR(20),
    count INT(3)
    )';

$sql_create_today_count_table = 'CREATE TABLE random_articles (
    entry_id INT(6) AUTO_INCREMENT NOT NULL PRIMARY KEY,
    publication_date DATE,
    word VARCHAR(20),
    article_text TEXT(1000),
    article_link TEXT(10000),
    thumbnail_link TEXT(10000)
    )';

$sql_create_visited_table = 'CREATE TABLE visited_links (
    entry_id INT(6) AUTO_INCREMENT NOT NULL PRIMARY KEY,
    article_link TEXT(10000)
    )';

$list_of_bad_words = array (
    4 => ['boob','bust','pert','pout','racy','sexy','slim','trim','vamp'],
    5 => ['ample','busty','leggy','perky','saucy','thigh','toned','yummy'],
    6 => ['assets','curves','fuller','gushes','skimpy','skinny','steamy','teases'],
    7 => ['ageless','braless','flashes','flaunts','midriff','scantily','sizable','slender'],
    8 => ['cleavage','enviable','flashing','plunging','sideboob','sizzling'],
    9 => ['posterior','revealing'],
    10 => ['skin-tight','super-slim'],
    11 => ['eye-popping'],
    14 => ['figure-hugging']
);

$xpath_archive_article_query_string = "//ul[contains(concat(' ', normalize-space(@class), ' '), ' archive-articles ')]/li";
$xpath_article_query_string = "//div[@class='beta']//div[contains(concat(' ', normalize-space(@class), ' '), 'femail')]//li | //div[@class='beta']//div[contains(concat(' ', normalize-space(@class), ' '), 'tvshowbiz')]//li";

function currentYearArchiveSearch() {
    global $current_year;

    // Get all links on year page - return as an array
    $current_year_links = getDailyArchiveLinks("http://www.dailymail.co.uk/home/sitemaparchive/year_".$current_year.".html", "//ul[contains(concat(' ', normalize-space(@class), ' '), ' split ')]/li");
	// 1. Query archive_count table for all current year links - return publication_date
	// 2. Loop through current_year_links and filter out the ones whose date already appears in above article_string_array
	// 3. Send unvisited links to getDailyArchiveLinks function

	//setVisitedLinks($current_year_links);
    // Get all links from visited_links table
    // $links_not_visited = array_diff($current_year_links, getVisitedLinks());
    // var_dump($links_not_visited);
}

/*
    Gets all the Date links from a Yearly archive page and return them as an array
*/
function getDailyArchiveLinks($url, $xpath_string) {
    $html = file_get_contents($url);
    $dom = new \DOMDocument('1.0', 'UTF-8');

    $internalErrors = libxml_use_internal_errors(true); // set error level
    $dom->loadHTML($html);
    libxml_use_internal_errors($internalErrors); // Restore error level

    $xpath = new DomXpath($dom);
    $daily_link_list = $xpath->query($xpath_string); // Returns all list items from a yearly page

    $daily_headline_list = array();
    foreach ($daily_link_list as $article) {
    	$node = $xpath->query('descendant::a/attribute::href', $article);
    	array_push($article_links, "http://www.dailymail.co.uk" . $node->item(0)->textContent); // Gets all daily links
    }
    return $article_links; // Returns an array of daily links
}

/*
    Get all the links from the year link provided
*/
function getListOfArticleLinks($ary_of_links, $query_string) {
    global $matched_articles;
    global $list_of_bad_words;
    $pub_date = '';

    /*
        link = http://www.dailymail.co.uk/home/sitemaparchive/day_19941014.html
        or
        link = http://www.dailymail.co.uk/home/index.html
    */
    foreach ($ary_of_links as $link) {
        error_log($link, 0);
        $html = file_get_contents($link);
        $dom = new \DOMDocument('1.0', 'UTF-8');

        $internalErrors = libxml_use_internal_errors(true); // set error level
        $dom->loadHTML($html);
        libxml_use_internal_errors($internalErrors); // Restore error level

        $xpath = new DomXpath($dom);
        $articles = $xpath->query($query_string);

        foreach ($articles as $article) { // article = DOMElement
            if ( is_object($article) ) {
                $node_text = $article->nodeValue; // article[nodeValue] = string to search eg "She dropped her Chanel diamond ring" etc
                $node_text = preg_replace('/\b[A-Za-z0-9]{1,x}\b\s?/i', '', $node_text); // removes javascript
                $node_text = preg_replace('/\s+/', ' ', $node_text); // replace large whitespaces with a single whitespace
                $article_string_array = preg_split('/[\s,]+/', $node_text); // split string on any space into array

                foreach ($article_string_array as $article_word) { // loops through array of article[nodeValue]
                    $article_word = preg_replace('/[^A-Za-z\-]/', '', $article_word); // remove numerical & special characters

                    if (isset($list_of_bad_words[strlen($article_word)])) { // if the 'article word' length matches a 'bad words' length
                        foreach ($list_of_bad_words[strlen($article_word)] as $badword) {
                            if (strcasecmp($article_word, $badword) == 0) { // case-insensitive string comparison

                                // different date function for 'archive' or 'daily'
                                if (strpos($link, 'sitemaparchive') == true) {
                                    $linkURL = explode('/', $link);
                                    $pub_date = preg_split("/[_.]/", end($linkURL))[1]; // get the date from the article url
                                } else {
                                    $pub_date = date("Y-m-d");
                                }

                                $matched_article['date'] = $pub_date;
                                $matched_article['word'] = $badword;
                                $matched_article['article_text'] = $node_text;
                                $matched_article['article_link'] = $xpath->query('descendant::a/attribute::href', $article)->item(0)->nodeValue;
                                $thumbnail = $xpath->query('descendant::a/img/attribute::data-src', $article);
                                if (strpos($link, 'index') == true) {
                                    $matched_article['thumbnail_link'] = $thumbnail->item(0)->nodeValue;
                                }
                                array_push($matched_articles, $matched_article);
                            }
                        }
                    }
                }
            }
        }
    }
}
/* SETTERS */
function setFoundArticlesToCurrentDB($q_links) {
    $db = new Db();
    $sql = 'INSERT INTO archive_count (publication_date, word, article_text, article_link, thumbnail_link) VALUES (?, ?, ?, ?, ?)';
    $stmt = $db->connect()->prepare($sql);

    foreach($q_links as $value) {
        $stmt->bind_param('sssss', $value['date'], $value['word'], $value['article_text'], $value['article_link'], $value['thumbnail_link']);
        $stmt->execute();
    }
}
function setTodaysArticles($q_links) {
    $db = new Db();
    $sql = 'INSERT INTO today_count (publication_date, word, article_text, article_link, thumbnail_link) VALUES (?, ?, ?, ?, ?)';
    $stmt = $db->connect()->prepare($sql);

    foreach($q_links as $value) {
        $stmt->bind_param('sssss', $value['date'], $value['word'], $value['article_text'], $value['article_link'], $value['thumbnail_link']);
        $stmt->execute();
    }
}
function setYearlyTotalsForWordByYear($year, $word) {
    $sql_count_yearly = 'INSERT INTO yearly_count (year, word, count) VALUES (?,?,?)';
    $db = new Db();
    if ( $stmt = $db->connect()->prepare($sql_count_yearly) ) {
        foreach($result as $row) {
           $word = $row['word'];
           $count = $row['total'];
           $stmt->bind_param('ssi', $year, $row['word'], $row['total']);
           $stmt->execute();
        }
    } else {
        echo $db->error();
    }
}
function setVisitedLinks($v_links) {
    $db = new Db();
    $sql = 'INSERT INTO visited_links (article_link) VALUES (?)';
    $stmt = $db->connect()->prepare($sql);

    foreach($v_links as $link) {
        $stmt->bind_param('s', $link);
        $stmt->execute();
    }
}
function populateRandomArticles($word) {
    $db = new Db();
    $sql = "INSERT INTO random_articles (publication_date, word, article_text, article_link, thumbnail_link) SELECT publication_date, word, article_text, article_link, thumbnail_link FROM archive_count WHERE word = '$word' ORDER BY rand() LIMIT 20";
    $stmt = $db->connect()->prepare($sql);
    $stmt->execute();
}
/* GETTERS */
function getVisitedLinks() {
    $sql_get_visited = "SELECT article_link FROM visited_links";
    $db = new Db();
    return $db->fetch($sql_get_visited, 'article_link');
}
function getDailyCount() {
    $sql_select_daily = "SELECT word, count(*) AS total FROM today_count GROUP BY word ORDER BY total DESC";
    $db = new Db();
    return $db->select($sql_select_daily);
}
function getWeeklyCount($today, $lastSevenDays) {
    $sql_select_weekly = "SELECT word, count(*) AS 'total' FROM archive_count WHERE publication_date BETWEEN '$today' AND '$lastSevenDays' GROUP BY word";
    $db = new Db();
    return $db->select($sql_select_weekly);
}
function getCurrentCountsForYear($year) {
    $sql_select_yearly = "SELECT word, count(*) AS 'total' FROM archive_count WHERE publication_date BETWEEN '$year-01-01' AND '$year-12-31' GROUP BY word";
    $db = new Db();
    return $db->select($sql_select_yearly);
}
function getCurrentCountsForYearByWord($year, $word) {
    $sql_select_yearly = "SELECT word, count(*) AS 'total' FROM archive_count WHERE publication_date LIKE '$year-%' AND word = '$word'";
    $db = new Db();
    return $db->select($sql_select_yearly);
}
function getYearlyTotals($year) {
    $sql_count_yearly = "SELECT * FROM yearly_count WHERE year=$year ORDER BY word ASC";
    $db = new Db();
    return $db->select($sql_count_yearly);
}
function getAllYearlyTotals() {
    $sql_count_yearly = "SELECT * FROM yearly_count";
    $db = new Db();
    return $db->select($sql_count_yearly);
}
function getWordCount($word) {
    $sql_select_word = "SELECT year, count FROM yearly_count WHERE word = '$word'";
    $db = new Db();
    return $db->select($sql_select_word);
}
function getDailyArticlesFromWord($word) {
    $sql_select_word_articles = "SELECT article_text, article_link, thumbnail_link FROM today_count WHERE word = '$word'";
    $db = new Db();
    return $db->select($sql_select_word_articles);
}
function getTableNames() {
	$sql_select_table_names = "SHOW TABLES";
    $db = new Db();
    return $db->fetch($sql_select_table_names, 'Tables_in_moail_online_test');
}
/* Other functions */
function cleanTable($table) {
    $sql_clean_table = "DELETE FROM $table";
    $db = new Db();
    $db->query($sql_clean_table);
}
function getBadWords() {
    $list_of_bad_words_sorted = [];
    global $list_of_bad_words;
    foreach ($list_of_bad_words as $value) {
        foreach($value as $val) {
            array_push($list_of_bad_words_sorted, $val);
        }
    }
    asort($list_of_bad_words_sorted);
    return $list_of_bad_words_sorted;
}
function getActiveBadWords() {
	$sql_get_active_words = "SELECT DISTINCT word FROM archive_count ORDER BY word ASC";
	$db = new Db();
	return $db->select($sql_get_active_words);
}
function randomArticleByWord($word) {
    $sql_random_article = "SELECT article_text, article_link FROM random_articles WHERE word = '$word' ORDER BY rand() LIMIT 1";
    $db = new Db();
    return $db->select($sql_random_article);
}
function cmp($a, $b) {
    if ($a['count'] == $b['count']) {
        return 0;
    }
    return ($a['count'] < $b['count']) ? -1 : 1;
}
function removeWordFromArchive($word) {
	$sql_delete_from_archive = "DELETE FROM archive_count WHERE word = '$word'";
    $db = new Db();
    $db->query($sql_delete_from_archive);
}
function removeWordFromYearly($word) {
	$sql_delete_from_yearly = "DELETE FROM yearly_count WHERE word = '$word'";
    $db = new Db();
    $db->query($sql_delete_from_yearly);
}
?>
