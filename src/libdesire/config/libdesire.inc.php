<?php

function testConstant ($name)
{
	if (!defined ($name))
	{
		die ($name . ' not defined');
	}
}

function testConstants ()
{
	$constants = array (
		'smarty_src_path',
		'smarty_template_dir',
		'smarty_compile_dir',
		'adodb_path',
		'base_url',
		'dbDriver',
		'dbUser',
		'dbPass',
		'dbHost',
		'dbName'
	);

	foreach ($constants as $constant)
	{
		testConstant ($constant);
	}
}

function testConfig ()
{
	if (!ini_get ('always_populate_raw_post_data'))
	{
		die ('always_populate_raw_post_data disabled');
	}
}

define ('libdesire_path', realpath (dirname (__FILE__) . '/..') . '/');

testConstants ();
testConfig ();
?>
