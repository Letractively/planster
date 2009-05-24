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
 * $Id: PersonList.inc.php 358 2007-02-27 08:21:15Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));
require_once (adodb_path . '/adodb.inc.php');
require_once (libdesire_path . 'db/DBItem.inc.php');
require_once (src_path . '/Person.inc.php');

class PersonList extends DBItem
{
	var $_event_id;

	function PersonList ($event_id)
	{
		$this->_connect ();
		$this->_event_id = $event_id;
	}

	function get ()
	{
		$data = array ();

		$query = 'SELECT * FROM ' . dbTablePrefix .
				'people WHERE event_id=? ORDER BY name ASC';
		$recordSet = &$this->_conn->Execute ($query, $this->_event_id);

		while (!$recordSet->EOF)
		{
			$id = $recordSet->fields ['person_id'];
			$name = $recordSet->fields ['name'];

			$person = new Person ($this->_event_id, $id);
			$person->setName ($name);

			$data [] = $person;

			$recordSet->MoveNext ();
		}
		return $data;
	}
}
?>
