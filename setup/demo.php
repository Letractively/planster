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
 * $Id: demo.php 389 2007-03-14 14:47:48Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../config/config.inc.php'));
require_once (src_path . '/Date.inc.php');
require_once (src_path . '/Event.inc.php');
require_once (src_path . '/Group.inc.php');
require_once (src_path . '/RootPage.inc.php');
require_once (libdesire_path . 'util/io.inc.php');
require_once (libdesire_path . 'json/JSON.php');

$root = new RootPage ();
$page = new Page ();

function doResetEvent ($id, $title, $days, $names, $states)
{
	$dateIDs = array ();

	$event = new Event ($id);
	$event->erase ();

	$event->setName ($title);
	$event->setOwner (mail_from);

	$event->save ();
	
	foreach ($days as $category => $entries)
	{
		$group = new Group ();
		$group->setName ($category);
		$group->setEvent ($event->getId());
		$group->save ();

		foreach ($entries as $day)
		{
			$date = new Date ();
			$date->setDate ($day);
			$date->setEvent ($event->getId ());
			$date->setGroup ($group);
			$date->save ();
			$dateIDs [] = $date->getId ();
		}
	}

	for ($i = 0; $i < count ($names); $i++)
	{
		$person = new Person ($event->getId ());
		$person->setName ($names [$i]);
		$person->save ();

		$status = Array ();
		for ($j = 0; $j < count ($states [$i]); $j++)
		{
			$day = $dateIDs [$j];
			$status [ $dateIDs [$j] ] = $states [$i] [$j];
		}
		$person->setStatus ($event->getId (), $status);
	}
}

function resetEvent1 ()
{
	$title = 'Trip to the sea';
	$id = 'demo00000001';
	$days = array (
		'weekdays'	=>	array ('Monday', 'Tuesday', 'Wednesday',
							'Thursday', 'Friday'),
		'weekend'	=>	array ('Saturday', 'Sunday')
	);
	$names = array ('Jack', 'Al', 'Fred');
	$states = array (
		array (1, 1, 2, 2, 2, 1, 3),
		array (2, 3, 2, 1, 1, 1, 1),
		array (3, 3, 3, 2, 1, 1, 3)
	);

	doResetEvent ($id, $title, $days, $names, $states);
	return $id;
}

function resetEvent2 ()
{
	$title = 'Dinner';
	$id = 'demo00000002';
	$days = array (
		'Animals'	=>	array ('Fish', 'Veal'),
		'Vegetables'	=>	array ('Broccoli', 'Onion soup')
	);
	$names = array ('Eugene', 'Tom', 'George');
	$states = array (
		array (1, 1, 2, 1),
		array (2, 1, 1, 3),
		array (2, 2, 1, 1)
	);

	doResetEvent ($id, $title, $days, $names, $states);
	return $id;
}

if (array_key_exists ('reinit', $_GET))
{
	$demos = array ();
	$demos [] = resetEvent1 ();
	$demos [] = resetEvent2 ();
	$page->assign ('demos', $demos);
	$root->assign ('body', $page->fetch ('demo-done.tpl'));
}
else
{
	$root->assign ('body', $page->fetch ('demo-menu.tpl'));
}
$root->display ();
?>
