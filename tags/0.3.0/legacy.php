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
 * $Id: legacy.php 399 2007-03-15 01:49:12Z stefan $
 */

require_once ('config/config.inc.php');
require_once (src_path . '/Clone.inc.php');
require_once (src_path . '/Event.inc.php');
require_once (src_path . '/Person.inc.php');
require_once (src_path . '/RootPage.inc.php');
require_once (libdesire_path . 'json/JSON.php');
require_once (libdesire_path . 'util/util.inc.php');

$eventID = require_key ('eventID', $_GET);
$action = require_key ('act', $_GET);
$json = new Services_JSON ();

$root = new RootPage ();

function callScript ($url)
{
	fopen ('http://' . $_SERVER ['HTTP_HOST'] . '/' . $url, 'ro');
}

if (! Event::validID ($eventID) > 0)
{
	die ('SYNTAX ERROR IN 0<br />READY');
}

$page = new Page ();
$event = new Event ($eventID);
$event->load ();

switch ($action)
{
	case 'addPerson':
		$page->assign ('legacy_addPerson', true);
		break;
	case 'savePerson':
		$name = require_key ('name', $_POST);
		$mail = try_key ('mail', $_POST);
		$sendMail = try_key ('sendMail', $_POST);

		$invitation = new Invitation ($name, $mail, $sendMail, $eventID, true);
		$event->invite ($invitation);

		$page->assign ('legacy_addPerson', true);
		break;
	case 'addGroup':
		$page->assign ('legacy_addGroup', true);
		break;
	case 'editGroup':
		$groupID = require_key ('groupID', $_GET);
		$page->assign ('legacy_editGroup', $groupID);
		break;
	case 'saveGroup':
		$name = require_key ('name', $_POST);
		$groupID = try_key ('groupID', $_GET);

		if ($groupID)
		{
			$group = new Group ($groupID);
		}
		else
		{
			$group = new Group ();
		}
		$group->setName ($name);
		$group->setEvent ($eventID);
		$group->save ();

		if (!$groupID)
		{
			$page->assign ('legacy_addGroup', true);
		}
		break;
	case 'addItem':
		$page->assign ('legacy_addItem', true);
		break;
	case 'saveItem':
		$name = require_key ('name', $_POST);
		$groups = array ();

		foreach ($_POST as $key => $item)
		{
			if (preg_match ('/^[0-9]+$/', $key))
			{
				$groups [] = $key;
			}
		}
		if (count ($groups) < 1) $groups [] = DEFAULT_GROUP;

		foreach ($groups as $group)
		{
			$date = new Date ();
			$date->setDate ($name);
			$date->setEvent ($eventID);
			$date->setGroup (new Group ($group));
			$date->save ();
		}
		$page->assign ('legacy_addItem', true);
		break;
	case 'editPLAN':
		$page->assign ('legacy_editPLAN', true);
		break;
	case 'savePLAN':
		$title = require_key ('name', $_POST);
		$event->setName ($title);
		$event->save ();

		$page->assign ('legacy_editPLAN', true);
		break;
	case 'clonePLAN':
		$page->assign ('legacy_clonePLAN', true);
		break;
	case 'clonePLANsave':
		$expires = require_key ('expires', $_POST);
		$cloneDates = array_key_exists ('cloneDates', $_POST);
		$clonePeople = array_key_exists ('clonePeople', $_POST);
		$cloneStatus = array_key_exists ('cloneStatus', $_POST);

		$clone = $event->clone ($expires, $cloneDates, $clonePeople, $cloneStatus);
		$clone->save ();
		$id = $clone->getID ();
		$root->assign ('message', 'The PLAN has been cloned. The new PLAN is <a href="' . base_url . "$id\">$id</a>");

		$page->assign ('legacy_clonePLAN', true);
		break;
	case 'switch':
		callScript ($eventID . '/switch');
		header ('location: ' . $eventID);
		break;
	case 'editDate':
		$dateID = require_key ('dateID', $_GET);
		$page->assign ('editDate', $dateID);
		break;
	case 'saveDate':
		$title = require_key ('date', $_POST);
		$dateID = require_key ('dateID', $_POST);

		$date = new Date ($dateID);
		$date->load ();

		if (!empty ($title))
		{
			$date->setDate ($title);
			$date->save ();
		}
		header ('location: ' . $eventID);
		break;
	case 'editPerson':
		$personID = require_key ('person', $_GET);
		$page->assign ('editPerson', $personID);
		break;
	case 'saveStatus':
		$event = new Event ($eventID);
		$personID = require_key ('edit_id', $_POST);
		$personName = require_key ('personName', $_POST);

		if (!$status = try_key ('status', $_POST))
		{
			$status = array ();
		}

		$person = new Person ($eventID, $personID);
		$person->load ();

		$person->setStatus ($eventID, $status);

		if ($personName != $person->getName ())
		{
			$person->setName ($personName);
			$person->save ();
		}

		header ('location: ' . $eventID);
		break;
}

$page->assign ('event', $event);
$root->assign ('body', $page->fetch ('event.tpl'));

$root->setTitle ($event->getName ());
$root->assign ('id', $event->getID ());
$root->display ('root.tpl');
?>
