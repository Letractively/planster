<?php session_start (); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<?php // -*- mode: sgml-html; mmm-classes: html-php -*-

include("planster_tests.php");
// above set $suite to self-test suite

$title = 'phpUnit test run';
?>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <STYLE TYPE="text/css">
<?php
	include ("stylesheet.css");
?>
        </STYLE>
    </head>
    <body>
        <h1><?php echo $title; ?></h1>
<?php
	if (isset($only)) {
        $suite = new TestSuite($only);
	}

	$result = new PrettyTestResult;
	$suite->run($result);
	$result->report();
?>
    </body>
</html>
