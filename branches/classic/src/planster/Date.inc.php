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
 * $Id: Date.inc.php 570 2007-08-30 22:03:43Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));
require_once (adodb_path . '/adodb.inc.php');
require_once (src_path . '/Change.inc.php');
require_once (src_path . '/Group.inc.php');
require_once (libdesire_path . 'db/DBItem.inc.php');

class Date extends DBItem
{
	var $_date;
	var $_id;
	var $_event;
	var $_position;
	var $_sync;
	var $_group;

	function Date ($id = -1)
	{
		$this->_connect ();
		$this->_id = $id;
		$this->_sync = false;
		$this->_event = -1;
		$this->_group = new Group (DEFAULT_GROUP);
	}

	function _getDB ()
	{
		return $this->_conn->Execute ('SELECT * FROM ' . dbTablePrefix
					. 'dates WHERE date_id=?', $this->_id);
	}

	function load ()
	{
		$db = $this->_getDB ();
		$this->_date = $db->fields ['date'];
		$this->_position = $db->fields ['position'];
		$this->_event = $db->fields ['event_id'];
		$this->_sync = true;

		$this->_group = new Group ($db->fields ['group_id']);
	}

	function save ($silent = false)
	{
		$rs = $this->_getDB ();
		$data = array (	'date'		=> $this->_date,
				'event_id'	=> $this->_event,
				'group_id'	=> $this->_group->getId ());

		if ($this->_id < 0)
		{
			$this->_position = $this->getMaxPosition () + 1;
			$data ['position'] = $this->_position;

			$sql = $this->_conn->GetInsertSQL ($rs, $data);
			$this->_conn->Execute ($sql);
			if ($this->_conn->ErrorNo () != 0) return false;

			$this->_id = $this->_conn->Insert_ID ();

			if (!$silent)
			{
				$change = new Change (CHANGE_TYPE_ADDDATE,
						$this->_event, $this->_date);
				$change->save ();
			}
		}
		else
		{
			$data ['position'] = $this->_position;
			$sql = $this->_conn->GetUpdateSQL ($rs, $data);
			if (!empty ($sql))
			{
				$this->_conn->Execute ($sql);
				if ($this->_conn->ErrorNo () != 0) return false;
			}
		}
		$this->_sync = true;
		return true;
	}

	function erase ()
	{
		$this->_conn->Execute ('DELETE FROM ' . dbTablePrefix . 'dates'
			. ' WHERE date_id=?', $this->_id);
		$this->_conn->Execute ('DELETE FROM ' . dbTablePrefix . 'status'
			. ' WHERE date_id=?', $this->_id);
	}

	function getMaxPosition ()
	{
		$rs = &$this->_conn->Execute ('SELECT MAX(position) FROM ' .
			dbTablePrefix .'dates WHERE event_id=?', $this->_event);
		return $rs->fields [0];
	}

	function getPosition ()
	{
		return $this->_position;
	}

	function isEarliest ()
	{
		if (!$this->_sync) $this->load ();
		return $this->_position == 1;
	}

	function setEvent ($eventID)
	{
		if (!$this->_sync && ($this->_id > -1)) $this->load ();
		$this->_event = $eventID;
	}

	function getEvent ()
	{
		return $this->_event;
	}

	function getGroupID ()
	{
		return $this->_group->getId ();
	}

	function setGroup ($group)
	{
		$this->_group = $group;
	}

	function getGroup ()
	{
		return $this->_group;
	}

	function isLast ()
	{
		if (!$this->_sync) $this->load ();
		return $this->_position == $this->getMaxPosition ();
	}

	function moveTo ($dst) {
		if (!$this->_sync) $this->load ();
		if ($dst->getPosition () > $this->getPosition ())
		{
			while ($dst->getPosition () > $this->getPosition ())
			{
				$this->later ();
				$dst->load ();
				$this->load ();
			}
		}
		else
		{
			while ($dst->getPosition () < $this->getPosition ())
			{
				$this->earlier ();
				$dst->load ();
				$this->load ();
			}
		}
	}

	function earlier ()
	{
		if (!$this->_sync) $this->load ();
		if (!$this->isEarliest ())
		{
			$this->_conn->StartTrans ();
			$this->_conn->Execute ('UPDATE ' . dbTablePrefix .
				'dates SET position=0 WHERE position=?',
							$this->_position);
			$this->_conn->Execute ('UPDATE ' . dbTablePrefix .
				'dates SET position=? WHERE position=?',
				array($this->_position, $this->_position - 1));
			$this->_conn->Execute ('UPDATE ' . dbTablePrefix .
				'dates SET position=? WHERE position=0',
							$this->_position - 1);
			$this->_conn->CompleteTrans ();
		}
	}

	function later ()
	{
		if (!$this->_sync) $this->load ();
		if (!$this->isLast ())
		{
			$this->_conn->StartTrans ();
			$this->_conn->Execute ('UPDATE ' . dbTablePrefix .
				'dates SET position=0 WHERE position=?',
							$this->_position);
			$this->_conn->Execute ('UPDATE ' . dbTablePrefix .
				'dates SET position=? WHERE position=?',
				array($this->_position, $this->_position + 1));
			$this->_conn->Execute ('UPDATE ' . dbTablePrefix .
				'dates SET position=? WHERE position=0',
							$this->_position + 1);
			$this->_conn->CompleteTrans ();
		}
	}

	function setDate ($date)
	{
		if (!$this->_sync && ($this->_id > -1)) $this->load ();
		if (!preg_match ('/^ *$/', $date))
		{
			$this->_date = $date;
		}
	}

	function getDate ()
	{
		return stripslashes (htmlspecialchars ($this->_date));
	}

	function getId ()
	{
		return $this->_id;
	}

	function countOK ()
	{
		$rs = $this->_conn->Execute ('SELECT count(status) FROM ' .
			dbTablePrefix . 'status WHERE event_id=? AND ' .
			'date_id=? AND status=?', 
			array ($this->getEvent (), $this->_id, STATUS_YES));
		return $rs->fields[0];
	}

	function sum ()
	{
		$sum = $this->countOK ();
		$event = new Event ($this->getEvent ());
		$event->load ();
		if ($event->getSumType () == SUM_BOTH)
		{
			$rs = $this->_conn->Execute ('SELECT count(status) ' .
			'FROM ' . dbTablePrefix . 'status WHERE event_id=? AND'
			. ' date_id=? AND status=?',
			array ($this->getEvent (), $this->_id, STATUS_MAYBE));
			$maybe = $rs->fields[0];
			$sum += ($maybe * .5);
		}
		return $sum;
	}
}
?>
