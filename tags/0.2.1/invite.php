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
 * $Id: invite.php 116 2006-04-28 15:08:52Z stefan $
 */

	require_once('config/config.inc.php');
	require_once(src_path . '/common.inc.php');
	require_once(src_path . '/Person.inc.php');
	require_once(src_path . '/Event.inc.php');

	$eventID = require_key('event', $_GET);
	$name = require_key('name', $_POST);
	$mail = require_key('mail', $_POST);
	$sendMail = (try_key('sendMail', $_POST) == "on");

	$warn = array();
	if (empty($name)) $warn[] = 'Name';
	if ($sendMail && empty($mail)) $warn[] = 'Mail';

	if (count($warn) > 0) {
		$url = 'show.php?id=' . $eventID . '&invite';
		foreach ($warn as $item) {
			$url .= '&warn' . $item;
		}
		header('location: ' . $url);
		die();
	}
	$person = new Person($eventID);
	$person->setName($_POST['name']);
	if ($sendMail) $person->invite($eventID, $_POST['mail']);
	echo $person->save();
	header('location: show.php?id=' . $eventID . '&invited&mail=' . urlencode($mail) . '&name=' . urlencode($name));
?>