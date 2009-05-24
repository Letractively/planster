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
 * $Id: Clone.inc.php 389 2007-03-14 14:47:48Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));
require_once (adodb_path . '/adodb.inc.php');
require_once (src_path . '/PLAN.inc.php');

class Clone extends PLAN
{
	var $_cloneOwner;
	var $_cloneDates;
	var $_clonePeople;
	var $_cloneStatus;
	var $_master;
	var $_dateIDMap;

	function Clone ($name, $owner, $expires, $_cloneOwner,
				$_cloneDates, $_clonePeople, $_cloneStatus)
	{
		$this->setName ($name);
		$this->setOwner ($owner);
		$this->expiresInMonths($expires);
		$this->_cloneOwner = $_cloneOwner;
		$this->_cloneDates = $_cloneDates;
		$this->_clonePeople = $_clonePeople;
		$this->_cloneStatus = $_cloneStatus;
		$this->_dateIDMap = array();
	}

	function setMaster ($master)
	{
		$this->_master = $master;
		if (empty ($this->_name)) {
			$this->setName ($master->getName ());
		}
	}

	function isComplete ()
	{
		return (!empty ($this->name) &&	!(empty ($this->owner) &&
			!$this->_cloneOwner) &&	!($this->_cloneStatus &&
				(!$this->_cloneDates || !$this->_clonePeople))
		);
	}

	function getCloneOwner ()
	{
		return $this->_cloneOwner;
	}

	function getCloneDates ()
	{
		return $this->_cloneDates;
	}

	function getClonePeople ()
	{
		return $this->_clonePeople;
	}

	function getCloneStatus ()
	{
		return $this->_cloneStatus;
	}

	function _copyFromMaster ()
	{
		$this->setOrientation ($this->_master->getOrientation ());
		if ($this->_cloneOwner)
		{
			$this->setOwner ($this->_master->getOwner ());
		}
	}

	function _copyGroups ()
	{
		$groupList = new GroupList ($this->_master->getID ());

		// returns a mapping oldgroup => newgroup
		return $groupList->clone ($this->getID ());
	}

	function _copyDates ()
	{
		$groupMapping = $this->_copyGroups ();

		foreach ($groupMapping as $oldGroup => $newGroup)
		{
			$dateList = new DateList ($this->_master->getID ());
			$dates = $dateList->get ($oldGroup);

			foreach ($dates as $date)
			{
				$clone = &new Date ();
				$clone->setEvent ($this->getID ());
				$clone->setDate ($date->getDate ());
				$clone->setGroup (new Group ($newGroup));
				$clone->save ();

				if ($this->_cloneStatus)
				{
					$dateID = $date->getID ();
					$cloneID = $clone->getID ();
					$this->_dateIDMap [$dateID] = $cloneID;
				}
			}
		}
	}

	function _copyPeople ()
	{
		$peopleList = &new PersonList ($this->_master->getID ());
		$people = $peopleList->get ();

		foreach ($people as $person)
		{
			$clone = &new Person ($this->getID ());
			$clone->setName ($person->getName ());
			$clone->save ();

			if ($this->_cloneDates && $this->_cloneStatus)
			{
				$dateList = &new DateList
						($this->_master->getID ());
				$status = array();
				$dates = $dateList->getAll ();

				foreach ($dates as $date)
				{
					$mappedID = $this->_dateIDMap
							[$date->getID()];
					$dateStatus = $person->getStatus
						($this->_master->getID(), 
									$date);
					$status [$mappedID] = $dateStatus;
				}
				$clone->setStatus ($this->getID (), $status);
			}
		}
	}

	function _copyStatus ()
	{
		$peopleList = &new PersonList ($this->_master->getID ());
		$people = $peopleList->get ();

		$dateList = &new DateList ($this->_master->getID ());
		$dates = $dateList->get ();
	}

	function save ()
	{
		if (!$this->isComplete ()) return false;

		$this->_connect ();

		$this->id = $this->generateID ();
		$this->_copyFromMaster ();
		parent :: save ();

		if ($this->_cloneDates)
		{
			$this->_copyDates ();	
		}
		if ($this->_clonePeople)
		{
			$this->_copyPeople ();	
		}
	}
}
?>
