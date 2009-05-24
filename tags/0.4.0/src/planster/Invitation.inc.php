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
 * $Id: Invitation.inc.php 375 2007-03-12 00:13:50Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));

class Invitation
{
	var $_name;
	var $_mail;
	var $_sendMail;
	var $_eventID;
//	var $_warn;

	function Invitation ($name, $mail, $sendMail, $eventID) //, $warn)
	{
		$this->_name = $name;
		$this->_mail = $mail;
		$this->_sendMail = $sendMail;
		$this->_eventID = $eventID;
//		$this->_warn = $warn;
	}

	function isComplete ()
	{
		return (!empty ($this->_name) &&
				! (empty ($this->_mail) && $this->_sendMail));
	}

	function save ()
	{
		if (!$this->isComplete ()) return false;

		$person = new Person ($this->_eventID);
		$person->setName ($this->_name);
		if ($this->_sendMail)
		{
			$person->invite ($this->_eventID, $this->_mail);
		}
		$person->save ();

		return true;
	}

	function getName ()
	{
		return $this->_name;
	}

	function getMail ()
	{
		return $this->_mail;
	}

	function getSendMail ()
	{
		return $this->_sendMail;
	}

/*	function getWarn ()
	{
		return $this->_warn;
	}*/
}
?>
