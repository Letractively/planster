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
 * $Id: DBItem.inc.php 143 2006-05-01 01:33:31Z stefan $
 */
require_once('config/config.inc.php');

require_once(adodb_path . '/adodb.inc.php');

require_once(src_path . '/Page.inc.php');
require_once(src_path . '/RootPage.inc.php');

class DBItem {
	var $_conn;

	function _connect() {
		if (!$this->_conn) {
			$dsn = dbDriver . '://' . dbUser . ':' . dbPass . '@' . dbHost . '/' . dbName;
			$this->_conn = @ADONewConnection($dsn);
			if (!$this->_conn) {
				$root = new RootPage('Currently Offline');
				$page = new Page();
				$root->assign('body', $page->fetch('dberror.tpl'));
				$root->display();
				die();
			}
		}
	}
}
?>