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
 * $Id: Event.inc.php 143 2006-05-01 01:33:31Z stefan $
 */
require_once('config/config.inc.php');

require_once(adodb_path . '/adodb.inc.php');

require_once(src_path . '/common.inc.php');
require_once(src_path . '/Change.inc.php');
require_once(src_path . '/DateList.inc.php');
require_once(src_path . '/DBItem.inc.php');
require_once(src_path . '/Page.inc.php');
require_once(src_path . '/PersonList.inc.php');

define('ORIENTATION_HORIZONTAL', 1);
define('ORIENTATION_VERTICAL', 2);

define('EVENT_STATE_NONE', 		0);
define('EVENT_STATE_EDITING',		1);
define('EVENT_STATE_INVITE',		2);
define('EVENT_STATE_INVITED',		3);
define('EVENT_STATE_ADDDATE',		4);
define('EVENT_STATE_EDITDATE',		5);
define('EVENT_STATE_EDIT_PERSON',	6);
define('EVENT_STATE_CLONE',		7);
define('EVENT_STATE_CLONED',		8);

class Event extends DBItem {
	var $id;
	var $name;
	var $owner;
	var $orientation;
	var $orientation_text;
	var $expires;

	function clone($name, $clDates, $clPeople, $clStatus, $clOwner, $owner, $expires) {
		$event = &new Event();
		$event->setName(empty($name) ? $this->getName() : $name);
		$event->setOwner($clOwner ? $this->getOwner() : $owner);
		$event->setOrientation($this->getOrientation());
		$event->expiresInMonths($expires);

		if ($clStatus) {
			$dateMap = array();
		}

		if ($clDates) {
			$dateList = &new DateList($this->getID());
			$dates = &$dateList->get();

			foreach($dates as $date) {
				$cloneDate = &new Date($event->getID());
				$cloneDate->setDate($date->getDate());
				$cloneDate->save();
				if ($clStatus) {
					$dateMap[$date->getID()] = $cloneDate->getID();
				}
			}
		}
		if ($clPeople) {
			$peopleList = &new PersonList($this->id);
			$people = &$peopleList->get();

			foreach($people as $person) {
				$clone = &new Person($event->getID());
				$clone->setName($person->getName());
				$clone->save();
		
				if ($clDates && $clStatus) {
					$dateList = &new DateList($this->getID());
					$status = array();
					$dates = &$dateList->get();
					foreach($dates as $date) {
						$mappedID = $dateMap[$date->getID()];
						$dateStatus = $person->getStatus($this->getID(), $date);
						$status[$mappedID] = $dateStatus;
					}
					$clone->setStatus($event->getID(), $status);
				}
			}
		}
		return $event;
	}

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

	function setOrientation($orientation) {
		$this->orientation = $orientation;
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

	function get($state = EVENT_STATE_NONE, $extra = NULL) {
		$people = new PersonList($this->id);
		$dateList = new DateList($this->id);
		$dates = &$dateList->get();
	
		$page = new Page();
		$page->assign('id', $this->id);
		$page->assign('dates', $dates);
		$page->assign('people', $people->get());

		if ($state == EVENT_STATE_INVITE) {
			$invite = &new Page();
			$invite->assign('id', $this->id);
			$invite->assign('sendMail', true);
			$warn = $extra;
			if (array_key_exists('name', $warn)) {
				$invite->assign('warnName', 'true');
			}
			if (array_key_exists('mail', $warn)) {
				$invite->assign('warnMail', 'true');
			}
			$page->assign('inviteForm', $invite->fetch('inviteForm.tpl'));
		} else if ($state == EVENT_STATE_INVITED) {
			$mail = $extra['mail'];
			$name = $extra['name'];
			$invite = &new Page();
			$invite->assign('id', $this->id);
			$invite->assign('mail', $mail);
			$invite->assign('name', $name);
			$page->assign('inviteForm', $invite->fetch('inviteOK.tpl'));
		} else if ($state == EVENT_STATE_ADDDATE) {
			$page->assign('addDate', true);
		} else if ($state == EVENT_STATE_CLONE) {
			$warnStatus = try_key('status', $extra);
			$warnOwner = try_key('owner', $extra);
			$clone = &new Page();

			$clone->assign('maxMonths', max_age_for_event);
			$clone->assign('id', $this->getID());
			$clone->assign('nojs', true);
			$clone->assign('name', $this->getName());
			$clone->assign('cloneOwner', true);
			$clone->assign('warnOwner', $warnOwner);
			$clone->assign('warnStatus', $warnStatus);
			$page->assign('inviteForm', $clone->fetch('cloneForm.tpl'));
		} else if ($state == EVENT_STATE_CLONED) {
			$newID = $extra;
			$cloned = &new Page();
			$cloned->assign('id', $this->getID());
			$cloned->assign('newID', $newID);
			$cloned->assign('url', base_url . 'show.php?id=' . $newID);
			$page->assign('inviteForm', $cloned->fetch('cloneOK.tpl'));
		} else if ($state == EVENT_STATE_EDITDATE) {
			$form = new Page();
			$dateID = $extra;
			$form->assign('date', $dateList->getByID($dateID));
			$form->assign('id', $this->id);
			$form->assign('first', $dateID == $dates[0]->getID());
			$form->assign('last', $dateID == $dates[count($dates)-1]->getID());
			$form->assign('horizontal', $this->orientation == ORIENTATION_HORIZONTAL);
			$page->assign('editdate', $dateID);
			$page->assign('editDateForm', $form->fetch('editdate.tpl'));
		} else if ($state == EVENT_STATE_EDIT_PERSON) {
			$edit = $extra;
			$page->assign('edit', $edit);
		}

		return $page->fetch('event-' . $this->getOrientationText() . '.tpl');
	}
}
?>
