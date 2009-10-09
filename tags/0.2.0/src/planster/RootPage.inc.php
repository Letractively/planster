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
 * $Id: RootPage.inc.php 76 2006-04-25 04:11:36Z stefan $
 */
require_once('config/config.inc.php');
require_once(smarty_src_path . '/Smarty.class.php');
require_once(src_path . '/Page.inc.php');

class RootPage extends Page {
	function RootPage($title) {
		parent::Page();
		$this->assign('title', $title);
		$this->assign('version', planster_version);
		$this->_id = -1;
	}

	function setID($id) {
		$this->assign('id', $id);
	}

	function display() {
		parent::display('root.tpl');
	}
}
?>