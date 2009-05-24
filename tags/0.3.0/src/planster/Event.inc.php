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
 * $Id: Event.inc.php 389 2007-03-14 14:47:48Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));
require_once (adodb_path . '/adodb.inc.php');
require_once (src_path . '/Clone.inc.php');
require_once (src_path . '/DateList.inc.php');
require_once (src_path . '/Invitation.inc.php');
require_once (src_path . '/PersonList.inc.php');
require_once (src_path . '/GroupList.inc.php');
require_once (src_path . '/PLAN.inc.php');

class Event extends PLAN
{
	function Event ($id = NULL)
	{
		$this->_connect ();
		$this->setId ($id == NULL ? $this->generateID () : $id);
		$this->setOrientation (ORIENTATION_VERTICAL);
		$this->expiresInMonths (6);
	}

	function invite ($invitation) 
	{
		$invitation->save ();
	}

	function clone ($expires, $cloneDates, $clonePeople, $cloneStatus)
	{
		$clone = new Clone ($this->getName (), $this->getOwner (),
				$expires, true, $cloneDates, $clonePeople,
								$cloneStatus);
		$clone->setMaster ($this);
		$clone->save ();

		return $clone;
	}

	function getPeople ()
	{
		$people = new PersonList ($this->id);
		return $people->get ();
	}

	function getGroups ()
	{
		$groupList = new GroupList ($this->id);
		return $groupList->get ();
	}

	// ---------- static methods ---------- //

	function validID ($id)
	{
		return preg_match ('/^[0-9a-zA-Z]{12}$/', $id);
	}
}
?>
