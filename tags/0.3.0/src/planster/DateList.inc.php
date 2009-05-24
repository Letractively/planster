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
 * $Id: DateList.inc.php 369 2007-02-28 09:51:36Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));
require_once (adodb_path . '/adodb.inc.php');
require_once (libdesire_path . 'db/DBItem.inc.php');
require_once (src_path . '/Date.inc.php');

class DateList extends DBItem
{
	var $_event_id;
	var $_loaded;
	var $_data;

	function DateList ($event_id)
	{
		$this->_connect ();
		$this->_event_id = $event_id;
		$this->_loaded = false;
	}

/*	function getByID ($id)
	{
		$data = $this->get ();
		foreach ($data as $item)
		{
			if ($item->getID () == $id) return $item;
		}
	}*/

	function get ($groupID)
	{
		if ($this->_loaded) return $this->_data;

		$data = array ();

		$groupSQL = '';
		$groupOrder = '';
		if ($groupID > -1) {
			$groupSQL = "AND group_id=$groupID";
			$groupOrder = 'group_id ASC, ';
		}

		$recordSet = &$this->_conn->Execute ('SELECT * FROM ' .
			dbTablePrefix . "dates WHERE event_id=? $groupSQL " .
			"ORDER BY $groupOrder position ASC", $this->_event_id);

		while (!$recordSet->EOF)
		{
			$date_id = $recordSet->fields ['date_id'];
			$date_title = $recordSet->fields ['date'];

			$date = new Date ($date_id);
			$date->setDate ($date_title);
			$date->setEvent ($this->_event_id);

			$data [] = $date;

			$recordSet->MoveNext ();
		}
		$this->_loaded = true;
		$this->_data = $data;
		return $data;
	}

	function getAll ()
	{
		$data = $this->get (-1);
		$this->loaded = false;
		return $data;
	}
}
?>
