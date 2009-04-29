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
 * $Id: date.php 389 2007-03-14 14:47:48Z stefan $
 */

require_once ('config/config.inc.php');
require_once (src_path . '/Date.inc.php');
require_once (src_path . '/Event.inc.php');
require_once (src_path . '/Group.inc.php');
require_once (libdesire_path . 'util/io.inc.php');

function moveDate ($src, $dstId)
{
	$dst = new Date ($dstId);
	$dst->load ();

	$src->moveTo ($dst);
}

$data = json_get_post ();

if ($dstDateId = try_attribute ('positionOf', $data))
{
	$date = new Date (require_key ('id', $_GET));
	$date->load ();

	moveDate ($date, $dstDateId);

	json_msg ('MOVE_ITEM_OK');
}
else if ($id = try_key ('id', $_GET))
{
	$name = require_attribute ('name', $data);

	$date = new Date ($id);
	$date->load ();

	if (empty ($name))
	{
		$date->erase ();
	}
	else
	{
		$date->setDate ($name);
		$date->save ();
	}
	json_msg ('RENAME_ITEM_OK');
}
else
{
	$name = require_attribute ('name', $data);
	$eventID = require_key ('eventID', $_GET);
	$groups = try_attribute ('groups', $data);
	if (!$groups) $groups = array (DEFAULT_GROUP);

	foreach ($groups as $group)
	{
		$date = new Date ();
		$date->setDate ($name);
		$date->setEvent ($eventID);
		$date->setGroup (new Group ($group));
		if (!$date->save ())
		{
			json_msg ('ADD_ITEM_FAIL');
			die ();
		}
	}
	json_msg ('ADD_ITEM_OK');
}
?>
