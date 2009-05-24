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
 * $Id: rpc.php 79 2006-04-25 14:13:05Z stefan $
 */

	require_once('config/config.inc.php');
	require_once(src_path . '/common.inc.php');
	require_once(src_path . '/Date.inc.php');
	require_once(src_path . '/Event.inc.php');

	if (!array_key_exists('action', $_REQUEST) || !array_key_exists('event', $_GET)) die('error#1');

	$eventID = $_GET['event'];

	function showEvent($id, $editDate = NULL) {
		echo 'content|';
		$event = new Event($id);
		$event->load();
		echo $event->get(-1, $editDate);
	}

	function dateID() {
		if (!array_key_exists('date', $_GET)) die('error#2');
		return $_GET['date'];
	}

	function showInviteForm($eventID, $name = '', $mail = '', $warnEmpty = false) {
		echo 'invite|';

		$page = new Page();
		$page->assign('id', $eventID);
		$page->assign('name', $name);
		$page->assign('mail', $mail);
		if ($warnEmpty && empty($name)) $page->assign('warnName', true);
		if ($warnEmpty && empty($mail)) $page->assign('warnMail', true);

		$page->display('inviteForm.tpl');
	}

	switch($_REQUEST['action']) {
		case 'adddate':
			$date = new Date($eventID);
			$date->setDate(dateID());
			$date->save();

			showEvent($eventID);
			break;
		case 'addperson':
			if (!array_key_exists('name', $_GET)) die();

			if (!empty($_GET['name'])) {
				$person = new Person($eventID);
				$person->setName($_GET['name']);
				$person->save();
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
			echo 'content|';
			$event = new Event($eventID);
			$event->load();
			echo $event->get($_GET['uid']);
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
				showEvent($eventID, $dateID);
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

			showEvent($eventID, $dateID);
			break;
		case 'moveLater':
			$dateID = dateID();
			$date = new Date($eventID, $dateID);
			$date->later();
			
			showEvent($eventID, $dateID);
			break;
		case 'getInviteForm':
			showInviteForm($eventID);
			break;
		case 'invite':
			$mail = require_key('mail', $_GET);
			$name = require_key('name', $_GET);

			if (empty($mail) || empty($name)) {
				showInviteForm($eventID, $name, $mail, true);
			} else {
				$person = new Person($eventID);
				$person->setName($name);
				$person->invite($eventID, $mail);
				$person->save();
	
				showEvent($eventID);
			}
			break;
	}
?>
