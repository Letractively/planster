<?php
/*
 * PLANster
 * Copyright (C) 2004-2007 Stefan Ott. All rights reserved.
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
 * $Id: setup.php 556 2007-08-16 03:29:16Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../config/config.inc.php'));
require_once (src_path . '/Date.inc.php');
require_once (src_path . '/Event.inc.php');
require_once (src_path . '/Group.inc.php');
require_once (src_path . '/RootPage.inc.php');
require_once (libdesire_path . 'util/io.inc.php');
require_once (libdesire_path . 'json/JSON.php');

$root = new RootPage ();
$page = new Page ();

$skipdb = false;

define ('LOG_MAIN', 1);
define ('LOG_SUB', 2);
define ('LOG_ERROR', 99);

class DBLayout extends DBItem
{
	var $_log;

	function DBLayout ()
	{
		$this->_connect ();
		$this->_log = array ();
	}

	function create ()
	{
		$this->_conn->StartTrans ();

		$sql = new Page ();
		$data = $sql->fetch ('dblayout-mysql.tpl');
		$queries = explode ("\n\n", $data);

		foreach ($queries as $query)
		{
			$this->_conn->Execute ($query);
		}
		$this->_conn->CompleteTrans ();

		return 'Created database layout';
	}

	function populate ()
	{
		$group = new Group ();
		$group->setName ('DEFAULT_GROUP');
		$group->save ();

		return 'Created default group';
	}

	function exists ()
	{
		$sql = 'SELECT * FROM ' . dbTablePrefix . 'events LIMIT 1';
		$res = $this->_conn->Execute ($sql);

		return ($res != NULL);
	}

	function detectVersion ()
	{
		$sql = 'DESCRIBE ' . dbTablePrefix . 'events';
		$res = $this->_conn->Execute ($sql);

		while (!$res->EOF)
		{
			if ($res->fields ['Field'] == 'sum_type') return 0.4;
			$res->moveNext ();
		}

		$sql = 'DESCRIBE ' . dbTablePrefix . 'dates';
		$res = $this->_conn->Execute ($sql);

		while (!$res->EOF)
		{
			if ($res->fields ['Field'] == 'group_id') return 0.3;
			$res->moveNext ();
		}

		$sql = 'SELECT * FROM ' . dbTablePrefix . 'log';
		$res = $this->_conn->Execute ($sql);

		if ($res) return 0.2;

		return 0.1;
	}

	function log ($level, $text)
	{
		$this->_log [] = array ('level' => $level, 'text' => $text);
	}

	function _update01to02 ()
	{
		$this->log (LOG_MAIN, 'Updating database layout from v0.1 to v0.2');
		// add the log table
		$this->_conn->StartTrans ();
		$page = new Page ();
		$data = $page->fetch ('dblayout-mysql-0.1to0.2.tpl');
		$queries = explode ("\n\n", $data);

		foreach ($queries as $query)
		{
			$this->log (LOG_SUB, $query);
			$this->_conn->Execute ($query);
			$error = $this->_conn->ErrorMsg ();

			if ($error) $this->log (LOG_ERROR, $error);
		}
		$this->_conn->CompleteTrans ();
		$this->log (LOG_MAIN, 'Update complete');
	}

	function _update02to03 ()
	{
		// add the groups table, the groups column in the date table
		// and the default group
		$this->_conn->StartTrans ();

		$this->log (LOG_MAIN, 'Updating database layout from v0.2 to v0.3');
		$page = new Page ();
		$data = $page->fetch ('dblayout-mysql-0.2to0.3.tpl');
		$queries = explode ("\n\n", $data);

		foreach ($queries as $query)
		{
			$this->log (LOG_SUB, $query);
			$this->_conn->Execute ($query);

			$error = $this->_conn->ErrorMsg ();

			if ($error) $this->log (LOG_ERROR, $error);
		}
		$this->_conn->CompleteTrans ();
		$this->log (LOG_MAIN, 'Update complete');
	}

	function _update03to04 ()
	{
		$this->log (LOG_MAIN, 'Updating database layout from v0.3 to v0.4');
		// add the log table
		$this->_conn->StartTrans ();
		$page = new Page ();
		$data = $page->fetch ('dblayout-mysql-0.3to0.4.tpl');
		$queries = explode ("\n\n", $data);

		foreach ($queries as $query)
		{
			$this->log (LOG_SUB, $query);
			$this->_conn->Execute ($query);
			$error = $this->_conn->ErrorMsg ();

			if ($error) $this->log (LOG_ERROR, $error);
		}
		$this->_conn->CompleteTrans ();
		$this->log (LOG_MAIN, 'Update complete');
	}

	function updateLayout ()
	{
		$layout = $this->detectVersion ();
		if ($layout < 0.2) $this->_update01to02 ();
		if ($layout < 0.3) $this->_update02to03 ();
		if ($layout < 0.4) $this->_update03to04 ();
	}

	function getLog ()
	{
		return $this->_log;
	}
}

class SiteChecker {
	var $_results;

	function SiteChecker ()
	{
		$this->_results = array ();
	}

	function _fail ($item)
	{
		$this->_results [$item] = 'NOK';
	}

	function _ok ($item)
	{
		$this->_results [$item] = 'OK';
	}

	function runAll ()
	{
		$this->_checkSmartyCompileDir ();
		$this->_checkADOdb ();
		if ($this->_checkPHPModules ())
		{
			$this->_checkDBConnection ();
		}
		$this->_checkApacheModules ();

		return $this->_results;
	}

	function _checkSmartyCompileDir ()
	{
		if (is_writable (smarty_compile_dir))
		{
			$this->_ok ('Smarty compile dir is writable');
		}
		else
		{
			$this->_fail ('Can not write to smarty compile dir '
				. '(' . smarty_compile_dir . ')');
		}
	}

	function _checkADOdb ()
	{
		if (@include adodb_path . '/adodb.inc.php')
		{
			$this->_ok ('ADOdb found');
		}
		else
		{
			$this->_fail ('ADOdb not found / not accessible in ' . adodb_path . ' - check path and permissions');
		}
	}

	function _checkApacheModules ()
	{
		$modules = apache_get_modules ();
		if (!in_array ('mod_rewrite', $modules))
		{
			$this->_fail ('Apache module mod_rewrite not loaded');
		}
		else
		{
			$this->_ok ('Apache module mod_rewrite loaded');
		}
	}

	function _checkPHPModules ()
	{
		$ok = true;

		switch (dbDriver)
		{
			case 'mysql':
			case 'mysqlt':
			case 'mysqli':
				$ok = function_exists ('mysql_connect');
				break;
			case 'postgres':
			case 'postgres7':
			case 'postgres8':
				$ok = function_exists ('pg_connect');
				break;
			default:
		}

		if (!$ok)
		{
			$this->_fail ('PHP module for database driver ' . dbDriver . ' missing');
			global $skipdb;
			$skipdb = true;
		}
		return $ok;
	}

	function _checkDBConnection ()
	{
		$dsn = dbDriver . '://' . dbUser . ':' . dbPass . '@' . dbHost
								. '/' . dbName;
		$conn = @ADONewConnection ($dsn);
		if ($conn)
		{
			$this->_ok ('Database connection works');
		}
		else
		{
			$this->_fail ('Failed to connect to the database');
		}
	}
}

if (array_key_exists ('initdb', $_GET))
{
	$msg = array ();
	$dbl = new DBLayout ();
	$msg [$dbl->create ()] = 'OK';
	$msg [$dbl->populate ()] = 'OK';

	$page->assign ('checks', $msg);
	$page->assign ('db', $dbl);
	$root->assign ('body', $page->fetch ('setup.tpl'));
}
else if (array_key_exists ('updatedb', $_GET))
{
	$dbl = new DBLayout ();
	$dbl->updateLayout ();
	$page->assign ('log', $dbl->getLog ());
	$root->assign ('body', $page->fetch ('setup-log.tpl'));
}
else
{
	$chk = new SiteChecker ();
	$result = $chk->runAll ();
	$page->assign ('checks', $result);
	if (!$skipdb)
	{
		$dbl = new DBLayout ();
		$page->assign ('db', $dbl);
	}
	$root->assign ('body', $page->fetch ('setup.tpl'));
}
$root->display ();
?>
