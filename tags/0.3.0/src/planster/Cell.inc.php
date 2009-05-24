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
 * $Id: Cell.inc.php 358 2007-02-27 08:21:15Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));
require_once (libdesire_path . 'view/Page.inc.php');
require_once (src_path . '/Event.inc.php');

class Cell
{
	var $_user;
	var $_date;
	var $_status;
	var $_edit;

	function Cell ($user, $date, $status)
	{
		$this->_user = $user;
		$this->_date = $date;
		$this->_status = $status;
		$this->_edit = false;
	}

	function edit ()
	{
		$this->_edit = true;
	}

	function get ()
	{
		$page = new Page ();
		$event = new Event ($this->_date->getEvent ());
		$event->load ();
		$page->assign ('person', $this->_user);
		$page->assign ('edit', $this->_edit);
		$page->assign ('status', $this->_status);
		$page->assign ('date', $this->_date);
		$page->assign ('event', $event);
		if ($this->_edit)
		{
			return $page->fetch ('statusCellEdit.tpl');
		}
		else
		{
			return $page->fetch ('statusCellShow.tpl');
		}
	}
}
?>
