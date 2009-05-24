<?php
/*
 * PLANster
 * Copyright (C) 2005/2006 Stefan Ott. All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * $Id: setup.php 151 2006-05-18 23:51:18Z stefan $
 */

	// comment the following line to enable the setup script
	die('Site already configured');

	require_once('config/config.inc.php');
	require_once(adodb_path . '/adodb.inc.php');
	require_once(src_path . '/Page.inc.php');

	$page = new Page('eventor setup');
	$dsn = dbDriver.'://'.dbUser.':'.dbPass.'@'.dbHost.'/'.dbName;

	if (array_key_exists('initdb', $_GET)) {
		$errors = array();
		$queries = array(
			'DROP TABLE ' . dbTablePrefix . 'events',
			'CREATE TABLE ' . dbTablePrefix . 'events (event_id varchar(' . EVENT_ID_LENGTH . ') NOT NULL, name varchar(' . MAX_EVENT_TITLE_LENGTH . ') NOT NULL, owner varchar(' . MAX_MAIL_ADDRESS_LENGTH . ') NOT NULL, orientation tinyint(1) NOT NULL default 1, expires DATE NOT NULL, PRIMARY KEY (event_id), UNIQUE KEY event_id (event_id))',
			'DROP TABLE ' . dbTablePrefix . 'people',
			'CREATE TABLE ' . dbTablePrefix . 'people (person_id int NOT NULL auto_increment, event_id varchar(' . EVENT_ID_LENGTH . ') NOT NULL, name varchar(' . MAX_USER_NAME_LENGTH . ') NOT NULL default "", PRIMARY KEY (person_id), UNIQUE KEY date_id (person_id))',
			'DROP TABLE ' . dbTablePrefix . 'dates',
			'CREATE TABLE ' . dbTablePrefix . 'dates (date_id int NOT NULL auto_increment, event varchar(' . EVENT_ID_LENGTH . ') NOT NULL, date varchar(' . MAX_DATE_LENGTH . ') NOT NULL, position int(3) NOT NULL, PRIMARY KEY (date_id), UNIQUE KEY date_id (date_id))',
			'DROP TABLE ' . dbTablePrefix . 'status',
			'CREATE TABLE ' . dbTablePrefix . 'status (person_id int NOT NULL, event_id varchar(' . EVENT_ID_LENGTH . ') NOT NULL, date_id int NOT NULL, status tinyint(1) NOT NULL)',
			'DROP TABLE ' . dbTablePrefix . 'log',
			'CREATE TABLE ' . dbTablePrefix . 'log (entry_id BIGINT NOT NULL AUTO_INCREMENT , event_id VARCHAR(' . EVENT_ID_LENGTH . ') NOT NULL, action ENUM(\'CRE\', \'ADD\', \'MOD\', \'INV\') NOT NULL, extra VARCHAR(' . EXTRA_FIELD_LENGTH . ') NOT NULL, time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE KEY entry_id (entry_id))'
		);

		$conn = @ADONewConnection($dsn);
		if (!$conn) die('Could not connect to the database');
		$conn->StartTrans();

		foreach($queries as $query) {
			$conn->Execute($query);
			$errors[] = $conn->ErrorMsg();
		}
		$tests = $queries;
	} else {
		$tests = array();
		$tests[] = 'Testing if the smarty template compile directory is writable';
		$errors[] = is_writable(smarty_compile_dir) ? '' : 'No - check permissions';

		$tests[] = 'Checking for ADOdb';
		if (!is_file(adodb_path . '/adodb.inc.php')) {
			$errors[] = 'ADOdb not found / not accessible in ' . adodb_path . ' - check path and permissions';
		} else if (!is_readable(adodb_path . '/adodb.inc.php')) {
			$errors[] = 'Could not read ' . adodb_path . '/adodb.inc.php - check permissions';
		} else {
			$errors[] = '';
		}

		$tests[] = 'Testing the db connection';
		$conn = @ADONewConnection($dsn);
		if (!$conn) $errors[] = 'Could not connect to the database';
		
	}

	$page->assign('tests', $tests);
	$page->assign('errors', $errors);

	$page->display('setup.tpl');

?>
