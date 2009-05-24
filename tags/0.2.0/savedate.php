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
 * $Id: savedate.php 100 2006-04-26 00:47:10Z stefan $
 */

	require_once('config/config.inc.php');
	require_once(src_path . '/common.inc.php');
	require_once(src_path . '/Date.inc.php');

	require_key('date', $_POST);
	require_key('event', $_GET);

	$eventID = $_GET['event'];
	if (!empty($_POST['date'])) {
		if (array_key_exists('newTitle', $_POST)) {
			$date = new Date($eventID, $_POST['date']);
			$date->setDate($_POST['newTitle']);
		} else {
			$date = new Date($eventID);
			$date->setDate($_POST['date']);
		}
		echo $date->save();
	}
	header('location: show.php?id=' . $eventID);
?>
