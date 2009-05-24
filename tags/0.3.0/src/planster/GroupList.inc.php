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
 * $Id: GroupList.inc.php 359 2007-02-27 09:37:14Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));
require_once (adodb_path . '/adodb.inc.php');
require_once (libdesire_path . 'db/DBItem.inc.php');
require_once (src_path . '/Group.inc.php');

class GroupList extends DBItem
{
	var $_event_id;
	var $_loaded;
	var $_data;

	function GroupList ($event_id)
	{
		$this->_connect ();
		$this->_event_id = $event_id;
		$this->_loaded = false;
	}

	function get ()
	{
		if ($this->_loaded) return $this->_data;

		$group = new Group (DEFAULT_GROUP);
		$group->load ();
		$group->setEvent ($this->_event_id);
		$data = array ($group);
		
		$recordSet = &$this->_conn->Execute ('SELECT * FROM ' .
			dbTablePrefix . 'groups WHERE event_id=? ORDER BY ' .
			'group_id ASC', $this->_event_id);

		while (!$recordSet->EOF)
		{
			$group_id = $recordSet->fields ['group_id'];
			$group_name = $recordSet->fields ['group_name'];

			$group = new Group ($group_id);
			$group->setName ($group_name);
			$group->setEvent ($this->_event_id);

			$data [] = $group;

			$recordSet->MoveNext ();
		}
		$this->_loaded = true;
		$this->_data = $data;
		return $data;
	}

	// returns a mapping oldgroup => newgroup
	function clone ($newEvent)
	{
		if (!$this->_loaded) $this->get ();
		$mapping = array (DEFAULT_GROUP => DEFAULT_GROUP);

		foreach ($this->_data as $group)
		{
			if ($group->getId () == DEFAULT_GROUP) continue;
			$clone = new Group ();
			$clone->setName ($group->getName ());
			$clone->setEvent ($newEvent);
			$clone->save ();
			$mapping [$group->getId()] = $clone->getId ();
		}

		return $mapping;
	}
}
?>
