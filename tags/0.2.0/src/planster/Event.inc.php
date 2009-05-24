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
 * $Id: Event.inc.php 93 2006-04-25 15:38:41Z stefan $
 */
require_once('config/config.inc.php');

require_once(src_path . '/Change.inc.php');
require_once(src_path . '/DateList.inc.php');
require_once(src_path . '/DBItem.inc.php');
require_once(src_path . '/Page.inc.php');
require_once(src_path . '/PersonList.inc.php');

require_once(adodb_path . '/adodb.inc.php');

define('ORIENTATION_HORIZONTAL', 1);
define('ORIENTATION_VERTICAL', 2);

class Event extends DBItem {
	var $id;
	var $name;
	var $owner;
	var $orientation;
	var $orientation_text;
	var $expires;

	function Event($id = NULL) {
		$this->_connect();
		$this->id = $id == NULL ? $this->generateID() : $id;
		$this->orientation = ORIENTATION_HORIZONTAL;
		$this->expiresInMonths(6);

		$this->orientation_text = array(
			ORIENTATION_HORIZONTAL	=> 'horizontal',
			ORIENTATION_VERTICAL	=> 'vertical'
		);
	}

	function expires($unixtime) {
		$this->expires = $unixtime;
	}

	function expiresInMonths($months) {
		$months = min($months, max_age_for_event);
		$month = date('m') + $months;
		$year = date('Y');
		if ($month > 12) {
			$month -= 12;
			$year++;
		}
		$this->expires(strtotime("$year-$month-" . date('d')));
	}

	function getExpiration() {
		return $this->expires;
	}

	function generateID() {
		define('event_id_chars', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
		define('event_id_minlen', 12);
		define('event_id_maxlen', 12);

		do {
			srand((double) microtime() * 1000000);
			$result = '';
			$length = rand(constant('event_id_minlen'), constant('event_id_maxlen'));
			$chars = constant('event_id_chars');
			$charcount = strlen($chars);

			for ($i=0; $i < $length; $i++) {
				$result .= $chars[rand(0, $charcount-1)];
			}
			$rs = $this->_conn->Execute('SELECT * FROM ' . dbTablePrefix . 'events WHERE event_id=?', $result);
		} while (!$rs->EOF);
		return $result;
	}

	function setOwner($address) {
		$this->owner = $address;
	}

	function getOwner() {
		return $this->owner;
	}

	function setName($name) {
		$this->name = $name;
	}

	function setId($id) {
		$this->id = $id;
	}

	function getId() {
		return $this->id;
	}

	function getOrientation() {
		return $this->orientation;
	}

	function getOrientationText() {
		return $this->orientation_text[$this->orientation];
	}

	function switchOrientation() {
		$this->orientation = $this->orientation == ORIENTATION_HORIZONTAL ? ORIENTATION_VERTICAL : ORIENTATION_HORIZONTAL;
	}

	function getName() {
		return $this->name;
	}

	function _insert($rs, $data) {
		$sql = $this->_conn->GetInsertSQL($rs, $data);
		if (!empty($sql)) $this->_conn->Execute($sql);
	}

	function _update($rs, $data) {
		$sql = $this->_conn->GetUpdateSQL($rs, $data);
		if (!empty($sql)) $this->_conn->Execute($sql);
	}

	function save() {
		$rs = $this->_conn->Execute('SELECT * FROM ' . dbTablePrefix . 'events WHERE event_id=?', $this->id);

		$data = array();
		$data['event_id'] = $this->id;
		$data['name'] = $this->name;
		$data['orientation'] = $this->orientation;
		$data['owner'] = $this->owner;

		if ($rs->EOF) {
			$data['expires'] = $this->expires;
			$this->_insert($rs, $data);

			$change = new Change(CHANGE_TYPE_CREATE, $this->id);
			$change->save();
		} else {
			$this->_update($rs, $data);
		}

		return $this->_conn->ErrorMsg();
	}

	function load() {
		$recordSet = &$this->_conn->Execute('SELECT name,orientation,owner FROM ' . dbTablePrefix . 'events WHERE event_id=?', $this->id);
		if ($recordSet->_numOfRows == 0) return false;
		$this->name = $recordSet->fields['name'];
		$this->owner = $recordSet->fields['owner'];
		$this->orientation = $recordSet->fields['orientation'];
		return true;
	}

	function erase() {
		$this->_conn->Execute('DELETE FROM ' . dbTablePrefix . 'dates WHERE event=?', $this->id);
		$this->_conn->Execute('DELETE FROM ' . dbTablePrefix . 'people WHERE event_id=?', $this->id);
		$this->_conn->Execute('DELETE FROM ' . dbTablePrefix . 'status WHERE event_id=?', $this->id);
		$this->_conn->Execute('DELETE FROM ' . dbTablePrefix . 'events WHERE event_id=?', $this->id);
		$this->_conn->Execute('DELETE FROM ' . dbTablePrefix . 'log WHERE event_id=?', $this->id);
	}

	function get($edit = -1, $editDate = NULL) {
		if ($editDate != NULL) $_GET['editdate'] = $editDate;
		$people = new PersonList($this->id);
		$dateList = new DateList($this->id);
		$dates = &$dateList->get();
	
		$page = new Page();
		$page->assign('id', $this->id);
		$page->assign('dates', $dates);
		$page->assign('people', $people->get());
		if ($edit > -1) $page->assign('edit', $edit);
		if (array_key_exists('adddate', $_GET)) $page->assign('addDate', true);
		if (array_key_exists('addperson', $_GET)) $page->assign('addPerson', true);
		if (array_key_exists('invite', $_GET)) {
			$invite = &new Page();
			$invite->assign('id', $this->id);
			if (array_key_exists('warn', $_GET)) {
				if ($_GET['warn'] == 'name') {
					$invite->assign('warnName', 'true');
				} else if ($_GET['warn'] == 'mail') {
					$invite->assign('warnMail', 'true');
				}
			}
			$page->assign('inviteForm', $invite->fetch('inviteForm.tpl'));
		}
		if (array_key_exists('editdate', $_GET)) {
			$form = new Page();
			$form->assign('date', $dateList->getByID($_GET['editdate']));
			$form->assign('id', $this->id);
			$form->assign('first', $_GET['editdate'] == $dates[0]->getID());
			$form->assign('last', $_GET['editdate'] == $dates[count($dates)-1]->getID());
			$form->assign('horizontal', $this->orientation == ORIENTATION_HORIZONTAL);
			$page->assign('editdate', $_GET['editdate']);
			$page->assign('editDateForm', $form->fetch('editdate.tpl'));
		}
		return $page->fetch('event-' . $this->getOrientationText() . '.tpl');
	}
}
?>
