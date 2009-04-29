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
 * $Id: editstatus.php 360 2007-02-27 09:47:07Z stefan $
 */

require_once ('config/config.inc.php');
require_once (src_path . '/Cell.inc.php');
require_once (src_path . '/DateList.inc.php');
require_once (src_path . '/GroupList.inc.php');
require_once (src_path . '/Person.inc.php');
require_once (libdesire_path . 'view/Page.inc.php');
require_once (libdesire_path . 'util/io.inc.php');

$eventID = require_key ('eventID', $_GET);
$personID = require_key ('personID', $_GET);

$groups = new GroupList ($eventID);
$person = new Person ($eventID, $personID);
$person->load ();

$data = array ();

foreach ($groups->get () as $group)
{
	foreach ($group->getChildren () as $date)
	{
		$status = $person->getStatus ($eventID, $date);
		$cell = new Cell ($person, $date, $status);
		$cell->edit ();
		$data [] = array (
			'id'	=> $date->getId (),
			'html'	=> $cell->get ()
		);
	}
}

$controls = new Page ();
$controls->assign ('id', $eventID);

json_data ('user', $data, array (
	'id'		=> $personID,
	'nameForm'	=> $person->getNameForm (),
	'controls'	=> $controls->fetch ('personFormControls.tpl')
));
?>
