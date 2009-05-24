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
 * $Id: Change.inc.php 143 2006-05-01 01:33:31Z stefan $
 */
require_once('config/config.inc.php');

require_once(src_path . '/DBItem.inc.php');

define('CHANGE_TYPE_CREATE', 'CRE');
define('CHANGE_TYPE_ADDDATE', 'ADD');
define('CHANGE_TYPE_MODIFY_ENTRY', 'MOD');
define('CHANGE_TYPE_INVITE', 'INV');

class Change extends DBItem {
	var $_extra;
	var $_id;
	var $_time;
	var $_type;
	var $_eventID;

	function Change($type, $eventID, $extra = '', $id = -1) {
		$this->_extra = $extra;
		$this->_eventID = $eventID;
		$this->_id = $id;
		$this->_type = $type;
		$this->_time = time();
	}

	function setTime($time) {
		$this->_time = strtotime($time);
	}

	function save() {
		$this->_connect();

		$rs = &$this->_conn->Execute('SELECT * FROM ' . dbTablePrefix . 'log WHERE entry_id=-1');
		$data = array(	'event_id'	=> $this->_eventID,
				'action'	=> $this->_type,
				'extra'		=> $this->_extra);
		$sql = &$this->_conn->GetInsertSQL($rs, $data);
		$this->_conn->Execute($sql);
	}

	function getTitle() {
		$titles = array(
			CHANGE_TYPE_CREATE 	=> 'Event created',
			CHANGE_TYPE_ADDDATE 	=> 'Item "' . $this->_extra . '" added',
			CHANGE_TYPE_MODIFY_ENTRY=> 'Entry for ' . $this->_extra . ' modified',
			CHANGE_TYPE_INVITE	=> 'Invited ' . $this->_extra
		);
		return $titles[$this->_type];
	}

	function getID() {
		return $this->_id;
	}

	function getDate($rdfFormat = false) {
		if ($rdfFormat) {
			// stupid workaround, since php4 doesn't know the
			// +00:00 timezone offset format
			$time = date('Y-m-d\TH:i', $this->_time);
			$offset = date('O', $this->_time);
			$offset = substr($offset, 0, 3) . ':' . substr($offset, 3, 2);

			return $time.$offset;
		} else {
			return $this->_time;
		}
	}

	function getDescription() {
		$desc = array(
			CHANGE_TYPE_CREATE 	=> 'The event was created',
			CHANGE_TYPE_ADDDATE 	=> 'The item "' . $this->_extra . '" was added to the event',
			CHANGE_TYPE_MODIFY_ENTRY=> 'Entry for ' . $this->_extra . ' was modified',
			CHANGE_TYPE_INVITE	=> $this->_extra . ' was invited to the event'
		);
		return $desc[$this->_type];
	}
}
?>
