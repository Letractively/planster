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
 * $Id: event.php 389 2007-03-14 14:47:48Z stefan $
 */

require_once ('config/config.inc.php');
require_once (src_path . '/Person.inc.php');
require_once (src_path . '/Event.inc.php');
require_once (src_path . '/RootPage.inc.php');
require_once (libdesire_path . 'util/io.inc.php');
require_once (libdesire_path . 'util/util.inc.php');

$eventID = require_key ('id', $_GET);

if (!has_json_post_data ())
{
	session_start ();

	if (!array_key_exists ('trace', $_SESSION)) {
		$_SESSION ['trace'] = array();
	}
	
	$event = new Event ($eventID);

	if (!$event->load ())
	{
		$root = new RootPage ('Illegal event');
		$page = new Page ();
		$root->assign ('body', $page->fetch ('illegalevent.tpl'));
		$root->display ();
		die ();
	}

	if (in_array ($eventID, $_SESSION ['trace'])) {
		$key = array_search($eventID, $_SESSION ['trace']);
		unset ($_SESSION ['trace'] [$key]);
	}
	$_SESSION ['trace'] [] = $eventID;

	$page = new Page ();
	$page->assign ('event', $event);

	$root = new RootPage ($event->getName ());
	$root->setID ($eventID);
	$root->assign ('body', $page->fetch ('event.tpl'));
	$root->display ();
}
else
{
	$data = json_get_post ();

	$personID = require_attribute ('user_id', $data);
	$personName = require_attribute ('name', $data);

	$statusData = require_attribute ('status', $data);

	$person = new Person ($eventID, $personID);
	$person->load ();
	
	if (empty ($personName))
	{
		$person->erase ();
	}
	else
	{
		$status = array ();
		foreach ($statusData as $item)
		{
			$status [$item->date] = $item->status;
		}

		$person->setStatus ($eventID, $status);

		if ($personName != $person->getName ())
		{
			$person->setName ($personName);
			$person->save ();
		}
	}
	json_msg ('UPDATE_STATUS_OK');
}
?>
