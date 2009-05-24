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
 * $Id: Group.inc.php 360 2007-02-27 09:47:07Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));
require_once (adodb_path . '/adodb.inc.php');
require_once (src_path . '/Change.inc.php');
require_once (libdesire_path . 'db/DBItem.inc.php');

class Group extends DBItem
{
	var $_id;
	var $_event;
	var $_name;

	function Group ($id = -1)
	{
		$this->_connect ();
		$this->_id = $id;
		$this->_event = -1;
		$this->_name = '';
	}

	function _getDB ()
	{
		return $this->_conn->Execute ('SELECT * FROM ' . dbTablePrefix
				. 'groups WHERE group_id=?', $this->_id);
	}

	function load ()
	{
		$db = $this->_getDB ();
		$this->_name = $db->fields ['group_name'];
		$this->_event = $db->fields ['event_id'];
	}

	function save ()
	{
		if (!$this->_isValid ()) return false;

		$rs = $this->_getDB ();
		$data = array (
			'group_name'	=> $this->_name,
			'event_id'	=> $this->_event
		);

		if ($this->_id < 0)
		{
			$sql = $this->_conn->GetInsertSQL ($rs, $data);
			$this->_conn->Execute ($sql);
			$this->_id = $this->_conn->Insert_ID ();
		} else {
			$rs = $this->_getDB ();
			$data = array ('group_name' => $this->_name);
			$sql = $this->_conn->GetUpdateSQL ($rs, $data);
			$this->_conn->Execute ($sql);
		}

		return true;
	}

	function erase ()
	{
		$this->_conn->Execute ('UPDATE ' . dbTablePrefix . 'dates' .
			' SET group_id=? WHERE group_id=?', array (
				DEFAULT_GROUP, $this->_id
			));
		$this->_conn->Execute ('DELETE FROM ' . dbTablePrefix . 'groups'
			. ' WHERE group_id=?', $this->_id);
	}

	function _isValid ()
	{
		return !preg_match ('/^ *$/', $this->_name);
	}

	function setEvent ($eventID)
	{
		$this->_event = $eventID;
	}

	function getEvent ()
	{
		return $this->_event;
	}

	function getId ()
	{
		return $this->_id;
	}

	function getName ()
	{
		return $this->_name;
	}

	function setName ($name)
	{
		$this->_name = $name;
	}

	function getChildren ()
	{
		$dates = new DateList ($this->_event);
		return $dates->get ($this->_id);
	}
}
?>
