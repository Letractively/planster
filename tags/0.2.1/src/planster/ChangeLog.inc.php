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
 * $Id: ChangeLog.inc.php 105 2006-04-28 02:16:17Z stefan $
 */
require_once('config/config.inc.php');

require_once(src_path . '/Change.inc.php');
require_once(src_path . '/DBItem.inc.php');

class ChangeLog extends DBItem {
	var $_items;
	var $_eventID;

	function ChangeLog($eventID) {
		$this->_eventID = $eventID;
		$this->_items = array();
	}

	function load() {
		$this->_connect();

		$rs = &$this->_conn->Execute('SELECT * FROM ' . dbTablePrefix . 'log WHERE event_id=? ORDER BY time DESC LIMIT ' . rdf_max_items, $this->_eventID);
		while (!$rs->EOF) {
			$data = $rs->fields;
			
			$change = new Change($data['action'], $this->_eventID, $data['extra'], $data['entry_id']);
			$change->setTime($data['time']);
			$this->_items[] = $change;
			$rs->MoveNext();
		}
	}

	function getItems() {
		return $this->_items;
	}

	function getLatest() {
		$latest = &$this->_items[0];
		foreach ($this->_items as $item) {
			if ($item->getDate() > $latest->getDate())
				$lastest = &$item;
		}
		return $latest;
	}
}
?>
