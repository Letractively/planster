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
 * $Id: Date.inc.php 143 2006-05-01 01:33:31Z stefan $
 */
require_once('config/config.inc.php');

require_once(adodb_path . '/adodb.inc.php');

require_once(src_path . '/Change.inc.php');
require_once(src_path . '/DBItem.inc.php');

class Date extends DBItem {
	var $_date;
	var $_id;
	var $_event;
	var $_position;
	var $_sync;

	function Date($event, $id = -1) {
		$this->_connect();
		$this->_event = $event;
		$this->_id = $id;
		$this->_sync = false;
	}

	function _getDB() {
		return $this->_conn->Execute('SELECT * FROM ' . dbTablePrefix . 'dates WHERE date_id=?', $this->_id);
	}

	function load() {
		$db = &$this->_getDB();
		$this->_date = $db->fields['date'];
		$this->_position = $db->fields['position'];
		$this->_sync = true;
	}

	function save($silent = false) {
		$rs = &$this->_getDB();
		$data = array('date' => $this->_date, 'event' => $this->_event);

		if ($this->_id < 0) {
			$this->_position = $this->getMaxPosition() + 1;
			$data['position'] = $this->_position;

			$sql = &$this->_conn->GetInsertSQL($rs, $data);
			$this->_conn->Execute($sql);
			$this->_id = $this->_conn->Insert_ID();

			if (!$silent) {
				$change = new Change(CHANGE_TYPE_ADDDATE, $this->_event, $this->_date);
				$change->save();
			}
		} else {
			$data['position'] = $this->_position;
			$sql = &$this->_conn->GetUpdateSQL($rs, $data);
			if (!empty($sql)) $this->_conn->Execute($sql);
		}
		$this->_sync = true;
		return $this->_conn->ErrorMsg();
	}

	function getMaxPosition() {
		$rs = &$this->_conn->Execute('SELECT MAX(position) FROM ' . dbTablePrefix . 'dates WHERE event=?', $this->_event);
		return $rs->fields[0];
	}

	function isEarliest() {
		if (!$this->_sync) $this->load();
		return $this->_position == 1;
	}

	function isLast() {
		if (!$this->_sync) $this->load();
		return $this->_position == $this->getMaxPosition();
	}

	function earlier() {
		if (!$this->_sync) $this->load();
		if (!$this->isEarliest()) {
			$this->_conn->Execute('UPDATE ' . dbTablePrefix . 'dates SET position=0 WHERE position=?', $this->_position);
			$this->_conn->Execute('UPDATE ' . dbTablePrefix . 'dates SET position=? WHERE position=?', array($this->_position, $this->_position - 1));
			$this->_conn->Execute('UPDATE ' . dbTablePrefix . 'dates SET position=? WHERE position=0', $this->_position - 1);
		}
	}

	function later() {
		if (!$this->_sync) $this->load();
		if (!$this->isLast()) {
			$this->_conn->Execute('UPDATE ' . dbTablePrefix . 'dates SET position=0 WHERE position=?', $this->_position);
			$this->_conn->Execute('UPDATE ' . dbTablePrefix . 'dates SET position=? WHERE position=?', array($this->_position, $this->_position + 1));
			$this->_conn->Execute('UPDATE ' . dbTablePrefix . 'dates SET position=? WHERE position=0', $this->_position + 1);
		}
	}

	function setDate($date) {
		if (!$this->_sync) $this->load();
		$this->_date = str_replace('|', '&#124', $date);
	}

	function getDate() {
		return $this->_date;
	}

	function getId() {
		return $this->_id;
	}
}
?>
