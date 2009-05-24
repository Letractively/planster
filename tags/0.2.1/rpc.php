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
 * $Id: rpc.php 129 2006-04-29 16:03:19Z stefan $
 */

	require_once('config/config.inc.php');
	require_once(src_path . '/common.inc.php');
	require_once(src_path . '/Date.inc.php');
	require_once(src_path . '/Event.inc.php');

	if (!array_key_exists('action', $_REQUEST) || !array_key_exists('event', $_GET)) die('error#1');

	$eventID = $_GET['event'];

	function showEvent($id, $state = EVENT_STATE_NONE, $extra = NULL) {
		echo 'content|';
		$event = new Event($id);
		$event->load();
		echo $event->get($state, $extra);
	}

	function dateID() {
		if (!array_key_exists('date', $_GET)) die('error#2');
		return $_GET['date'];
	}

	function showInviteForm($eventID, $name = '', $mail = '', $warnName = false, $warnMail = false) {
		echo 'dialog|';

		$page = new Page();
		$page->assign('id', $eventID);
		$page->assign('name', $name);
		$page->assign('mail', $mail);
		$page->assign('sendMail', $warnMail || !$warnName);
		if ($warnName && empty($name)) $page->assign('warnName', true);
		if ($warnMail && empty($mail)) $page->assign('warnMail', true);

		$page->display('inviteForm.tpl');
	}

	function showCloneForm($eventID, $warnOwner = false) {
		$name = try_key('name', $_GET);
		if (empty($name)) {
			$event = new Event($eventID);
			$event->load();
			$name = $event->getName();
		}

		echo 'dialog|';

		$page = new Page();
		$page->assign('id', $eventID);
		$page->assign('name', $name);
		$page->assign('owner', try_key('owner', $_GET));
		$page->assign('expires', try_key('expires', $_GET) ? $_GET['expires'] : 2);
		$page->assign('maxMonths', max_age_for_event);
		$page->assign('warnOwner', $warnOwner);
		$page->assign('cloneOwner', try_key('cloneOwner', $_GET) != 'false');
		$page->assign('cloneDates', try_key('cloneDates', $_GET) == 'true');
		$page->assign('clonePeople', try_key('clonePeople', $_GET) == 'true');
		$page->assign('cloneStatus', try_key('cloneStatus', $_GET) == 'true');
		if (!(try_key('clonePeople', $_GET) == 'true' && try_key('cloneDates', $_GET) == 'true')) {
			$page->assign('disableCloneStatus', true);
		}

		$page->display('cloneForm.tpl');
	}

	switch($_REQUEST['action']) {
		case 'getAddDateForm':
			echo 'newdate|';
			$page = new Page();
			$page->assign('id', $eventID);
			$page->display('adddate.tpl');
			break;
		case 'adddate':
			$date = new Date($eventID);
			$date->setDate(dateID());
			$date->save();

			showEvent($eventID);
			break;
		case 'addDates':
			$items = explode(',', try_key('items', $_GET));

			sort($items);

			foreach ($items as $item) {
				// just to make sure that no "empty" dates are added
				if ($item != '') {
					$date = new Date($eventID);
					$date->setDate(date('D, M d', $item));
					$date->save();
				}
			}

			showEvent($eventID);
			break;
		case 'switch_orientation':
			$event = new Event($eventID);
			$event->load();
			$event->switchOrientation();
			$event->save();
			
			echo 'content|';
			echo $event->get();
			break;
		case 'editstatus':
			$userID = require_key('uid', $_GET);
			showEvent($eventID, EVENT_STATE_EDIT_PERSON, $userID);
			break;
		case 'editdate':
			$dateID = dateID();

			$event = new Event($eventID);
			$event->load();

			$date = new Date($eventID, $dateID);
			$date->load();

			$page = new Page();
			$page->assign('id', $eventID);
			$page->assign('date', $date);
			$page->assign('first', $date->isEarliest());
			$page->assign('last', $date->isLast());
			$page->assign('horizontal', $event->getOrientation() == ORIENTATION_HORIZONTAL);

			if (array_key_exists('full', $_GET)) {
				showEvent($eventID, EVENT_STATE_EDITDATE, $dateID);
			} else {
				echo 'dateTitle' . $dateID . '|';
				$page->display('editdate.tpl');
			}
			break;
		case 'savedate':
			$dateID = dateID();
			$title = $_GET['title'];

			$date = new Date($eventID, $dateID);
			$date->setDate($title);
			$date->save();

			echo 'dateTitle' . $dateID . '|';

			$page = new Page();
			$page->assign('id', $eventID);
			$page->assign('date_id', $dateID);
			$page->assign('date_title', $date->getDate());
			$page->display('date-title.tpl');
			break;
		case 'moveEarlier':
			$dateID = &dateID();
			$date = new Date($eventID, $dateID);
			$date->earlier();

			showEvent($eventID, EVENT_STATE_EDITDATE, $dateID);
			break;
		case 'moveLater':
			$dateID = dateID();
			$date = new Date($eventID, $dateID);
			$date->later();
			
			showEvent($eventID, EVENT_STATE_EDITDATE, $dateID);
			break;
		case 'getInviteForm':
			showInviteForm($eventID);
			break;
		case 'invite':
			$mail = require_key('mail', $_GET);
			$name = require_key('name', $_GET);
			$sendMail = (require_key('sendMail', $_GET) == "true");

			if (($sendMail == "true" && empty($mail)) || empty($name)) {
				showInviteForm($eventID, $name, $mail, true, $sendMail);
			} else {
				$person = new Person($eventID);
				$person->setName($name);
				if ($sendMail) $person->invite($eventID, $mail);
				$person->save();

				showEvent($eventID, EVENT_STATE_INVITED, array('mail' => $mail, 'name' => $name));
			}
			break;
		case 'getCloneForm':
			showCloneForm($eventID);
			break;
		case 'clone':
			$name = require_key('name', $_GET);
			$owner = require_key('owner', $_GET);
			$expires = require_key('expires', $_GET);
			$cloneOwner = (require_key('cloneOwner', $_GET) == "true");
			$cloneDates = (require_key('cloneDates', $_GET) == "true");
			$clonePeople = (require_key('clonePeople', $_GET) == "true");
			$cloneStatus = ((require_key('cloneStatus', $_GET) == "true") && $clonePeople && $cloneDates);
			if (!$cloneOwner && empty($owner)) {
				showCloneForm($eventID, true);
			} else {
				echo 'dialog|';
				$event = new Event($eventID);
				$event->load();

				$clone = $event->clone(	$name,
							$cloneDates,
							$clonePeople,
							$cloneStatus,
							$cloneOwner,
							$owner,
							$expires);
				$clone->save();

				$page = new Page();
				$page->assign('id', $event->getID());
				$page->assign('newID', $clone->getID());
				$page->assign('url', base_url . 'show.php?id=' . $clone->getID());
				$page->display('cloneOK.tpl');
			}
			break;
	}
?>
