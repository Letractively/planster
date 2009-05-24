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
 * $Id: savestatus.php 157 2006-05-19 00:35:52Z stefan $
 */

	require_once('config/config.inc.php');
	require_once(src_path . '/common.inc.php');
	require_once(src_path . '/Person.inc.php');
	require_once(src_path . '/Event.inc.php');

	require_key('edit_id', $_POST);
	require_key('personName', $_POST);
	require_key('id', $_POST);

	$personID = $_POST['edit_id'];
	$personName = $_POST['personName'];
	$eventID = $_POST['id'];

	$status = @$_POST['status'];

	$person = new Person($eventID, $personID);
	$person->load();
	$person->setStatus($eventID, $status);

	if ($personName != $person->getName()) {
		$person->setName($personName);
		$person->save();
	}

	header('location: show.php?id=' . $eventID);
?>
