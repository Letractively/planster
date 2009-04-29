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
 * $Id: PLAN.inc.php 493 2007-04-27 01:06:43Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));
require_once (adodb_path . '/adodb.inc.php');
require_once (src_path . '/Change.inc.php');
require_once (libdesire_path . 'db/DBItem.inc.php');

define ('SUM_NONE', 0);
define ('SUM_YES', 1);
define ('SUM_BOTH', 2);

class PLAN extends DBItem
{
	var $id;
	var $name;
	var $owner;
	var $orientation;
	var $expires;

	function PLAN ()
	{
		die ('========== ABSTRACT CLASS ==========');
	}

	function expires ($unixtime)
	{
		$this->expires = $unixtime;
	}

	function expiresInMonths ($months)
	{
		$months = min ($months, max_age_for_event);
		$month = date ('m') + $months;
		$year = date ('Y');
		if ($month > 12) {
			$month -= 12;
			$year++;
		}
		$this->expires (strtotime ("$year-$month-" . date ('d')));
	}

	function getExpiration ()
	{
		return $this->expires;
	}

	function setId ($id)
	{
		$this->id = $id;
	}

	function getId ()
	{
		return $this->id;
	}

	function getOrientation ()
	{
		return $this->orientation;
	}

	function getOrientationText ()
	{
		$orientation_text = array (
			ORIENTATION_HORIZONTAL	=> 'horizontal',
			ORIENTATION_VERTICAL	=> 'vertical'
		);
		return $orientation_text [$this->orientation];
	}

	function switchOrientation ()
	{
		$this->orientation = $this->orientation ==
				ORIENTATION_HORIZONTAL ? ORIENTATION_VERTICAL :
							ORIENTATION_HORIZONTAL;
	}

	function setOrientation ($orientation)
	{
		$this->orientation = $orientation;
	}

	function generateID ()
	{
		$event_id_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNO' .
							'PQRSTUVWXYZ0123456789';
		$event_id_minlen = 12;
		$event_id_maxlen = 12;

		do {
			srand ((double) microtime () * 1000000);
			$result = '';
			$length = rand ($event_id_minlen, $event_id_maxlen);
			$chars = $event_id_chars;
			$charcount = strlen ($chars);

			for ($i=0; $i < $length; $i++) {
				$result .= $chars [rand (0, $charcount-1)];
			}
			$rs = $this->_conn->Execute (
					'SELECT * ' .
					'FROM ' . dbTablePrefix . 'events ' .
					'WHERE event_id=?', $result);
		}
		while (!$rs->EOF);

		return $result;
	}

	function setOwner ($address)
	{
		$this->owner = $address;
	}

	function getOwner ()
	{
		return $this->owner;
	}

	function setName ($name)
	{
		if (!preg_match ('/^ *$/', $name))
		{
			$this->name = $name;
		}
	}

	function getName ()
	{
		return stripslashes (htmlspecialchars ($this->name));
	}

	function _insert ($rs, $data)
	{
		$sql = $this->_conn->GetInsertSQL ($rs, $data);
		if (!empty ($sql)) $rs = $this->_conn->Execute ($sql);
	}

	function _update ($rs, $data)
	{
		$sql = $this->_conn->GetUpdateSQL ($rs, $data);
		if (!empty ($sql)) $this->_conn->Execute ($sql);
	}

	function save ()
	{
		$rs = $this->_conn->Execute (
					'SELECT * ' .
					'FROM ' . dbTablePrefix . 'events ' .
					'WHERE event_id=?', $this->id);
		$data = array (
			'event_id'	=> $this->id,
			'name'		=> $this->name,
			'orientation'	=> $this->orientation,
			'owner'		=> $this->owner,
			'sum_type'	=> $this->_sumType
		);

		if ($rs->EOF)
		{
			$data ['expires'] = $this->expires;
			$this->_insert ($rs, $data);
			if ($this->_conn->ErrorNo () != 0) return false;

			$change = new Change (CHANGE_TYPE_CREATE, $this->id);
			$change->save ();
		}
		else
		{
			$this->_update ($rs, $data);
		}

		$this->_conn->debug = 0;
		return $this->_conn->ErrorNo () == 0;
	}

	function load ()
	{
		$recordSet = &$this->_conn->Execute (
					'SELECT * ' .
					'FROM ' . dbTablePrefix . 'events ' .
					'WHERE event_id=?', $this->id);

		if ($recordSet->_numOfRows == 0) return false;

		$this->name = $recordSet->fields ['name'];
		$this->owner = $recordSet->fields ['owner'];
		$this->orientation = $recordSet->fields ['orientation'];
		$this->_sumType = $recordSet->fields ['sum_type'];

		return true;
	}

	function erase ()
	{
		$this->_conn->Execute ('DELETE FROM ' . dbTablePrefix .
					'dates WHERE event_id=?', $this->id);
		$this->_conn->Execute ('DELETE FROM ' . dbTablePrefix .
					'people WHERE event_id=?', $this->id);
		$this->_conn->Execute ('DELETE FROM ' . dbTablePrefix .
					'status WHERE event_id=?', $this->id);
		$this->_conn->Execute ('DELETE FROM ' . dbTablePrefix .
					'events WHERE event_id=?', $this->id);
		$this->_conn->Execute ('DELETE FROM ' . dbTablePrefix .
					'log WHERE event_id=?', $this->id);
		$this->_conn->Execute ('DELETE FROM ' . dbTablePrefix .
					'groups WHERE event_id=?', $this->id);
	}
}
?>
