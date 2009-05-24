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
 * $Id: Person.inc.php 375 2007-03-12 00:13:50Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));
require_once (adodb_path . '/adodb.inc.php');
require_once (src_path . '/Change.inc.php');
require_once (libdesire_path . 'db/DBItem.inc.php');
require_once (src_path . '/InviteMail.inc.php');

define ('STATUS_YES', 1);
define ('STATUS_NO', 2);
define ('STATUS_MAYBE', 3);
define ('STATUS_UNKNOWN', 4);

class Person extends DBItem
{
	var $_name;
	var $_eventID;
	var $_id;
	var $_status_strings;
	var $_status_icons;

	function Person ($eventID, $id = -1)
	{
		$this->_connect();
		$this->_eventID = $eventID;
		$this->_id = $id;
		$this->_status_strings = array (
			STATUS_YES	=> 'Yes',
			STATUS_NO	=> 'No',
			STATUS_MAYBE	=> 'Maybe',
			STATUS_UNKNOWN	=> '-'
		);
		$this->_status_icons = array (
			STATUS_YES	=> 'ok.gif',
			STATUS_NO	=> 'no.gif',
			STATUS_MAYBE	=> 'maybe.gif',
		);
	}

	function load ()
	{
		$rs = $this->_conn->Execute ('SELECT * FROM ' . dbTablePrefix .
					'people WHERE person_id=?', $this->_id);
		$this->_name = $rs->fields ['name'];
	}

	function save ()
	{
		$rs = $this->_conn->Execute ('SELECT * FROM ' . dbTablePrefix .
					'people WHERE person_id=?', $this->_id);
		$data = array (	'name' 		=> $this->_name,
				'event_id'	=> $this->_eventID);
		if ($this->_id < 0)
		{
			$sql = $this->_conn->GetInsertSQL ($rs, $data);
		}
		else
		{
			$sql = $this->_conn->GetUpdateSQL ($rs, $data);
		}
		$this->_conn->Execute ($sql);
		if ($this->_id < 0)
		{
			$this->_id = $this->_conn->Insert_Id ();
		}
		return $this->_conn->ErrorMsg ();
	}

	function erase ()
	{
		$this->_conn->Execute ('DELETE FROM ' . dbTablePrefix . 'people'
			. ' WHERE person_id=?', $this->_id);
		$this->_conn->Execute ('DELETE FROM ' . dbTablePrefix . 'status'
			. ' WHERE person_id=?', $this->_id);
		
	}

	function setName ($name) {
		if (!preg_match ('/^ *$/', $name))
		{
			$this->_name = $name;
		}
	}

	function getName ()
	{
		return stripslashes (htmlspecialchars ($this->_name));
	}

	function getId ()
	{
		return $this->_id;
	}

	function getStatus ($event_id, $date)
	{
		$date_id = $date->getId ();
		$query = 'SELECT status FROM ' . dbTablePrefix . 'status WHERE person_id=? AND event_id=? AND date_id=?';
		$recordSet = $this->_conn->Execute ($query, 
				array ($this->_id, $event_id, $date_id));

		if (count ($recordSet->fields) <= 1)
		{
			return STATUS_UNKNOWN;
		}
		else
		{
			return intval ($recordSet->fields ['status']);
		}
	}

	function getStatusText ($status)
	{
		return $this->_status_strings [$status];
	}

	function getStatusIcon ($status)
	{
		return $this->_status_icons [$status];
	}

	function getText ($event_id, $date)
	{
		return 'vielleicht';
	}

	function getNameForm ()
	{
		$page = new Page ();
		$page->assign ('person', $this);
		$page->assign ('id', $this->_eventID);
		return $page->fetch ('personNameForm.tpl');
	}

	function setStatus ($event_id, $status_array)
	{
		$this->_conn->Execute('DELETE FROM ' . dbTablePrefix .
				'status WHERE person_id=? AND event_id=?',
						array($this->_id, $event_id));
		foreach ($status_array as $date_id => $status)
		{
			$query = 'INSERT INTO ' . dbTablePrefix .
					'status SET person_id=?, event_id=?, '
					. 'date_id=?, status=?';
			$this->_conn->Execute($query, array($this->_id,
						$event_id, $date_id, $status));
		}
		$change = new Change(CHANGE_TYPE_MODIFY_ENTRY, $event_id,
								$this->_name);
		$change->save();
	}

	function invite ($eventID, $mail)
	{
		$event = &new Event ($eventID);
		$event->load ();
		$mail = &new InviteMail ($mail, $this->_name, &$event);
		$mail->send ();

		$change = new Change (CHANGE_TYPE_INVITE, $eventID,
								$this->_name);
		$change->save ();
	}
}
?>
