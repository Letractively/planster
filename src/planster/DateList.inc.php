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
 * $Id: DateList.inc.php 100 2006-04-26 00:47:10Z stefan $
 */
require_once(adodb_path . '/adodb.inc.php');
require_once(src_path . '/DBItem.inc.php');
require_once(src_path . '/Date.inc.php');

class DateList extends DBItem {
	var $_event_id;
	var $_loaded;
	var $_data;

	function DateList($event_id) {
		$this->_connect();
		$this->_event_id = $event_id;
		$this->_loaded = false;
	}

	function getByID($id) {
		$data = $this->get();
		foreach ($data as $item) {
			if ($item->getID() == $id) return $item;
		}
	}

	function get() {
		if ($this->_loaded) return $this->_data;

		$data = array();

		$recordSet = &$this->_conn->Execute('SELECT * FROM ' . dbTablePrefix . 'dates WHERE event=? ORDER BY position ASC', $this->_event_id);

		while (!$recordSet->EOF) {
			$date_id = $recordSet->fields['date_id'];
			$date_title = $recordSet->fields['date'];

			$date = new Date($this->_event_id, $date_id);
			$date->setDate($date_title);

			$data[] = $date;

			$recordSet->MoveNext();
		}
		$this->_loaded = true;
		$this->_data = $data;
		return $data;
	}
}
?>
