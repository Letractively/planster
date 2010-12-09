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
 * $Id: InviteMail.inc.php 358 2007-02-27 08:21:15Z stefan $
 */

require_once (realpath (dirname (__FILE__) . '/../../config/config.inc.php'));
require_once (libdesire_path . 'db/DBItem.inc.php');
require_once (libdesire_path . 'view/Page.inc.php');

class InviteMail extends Page
{
	var $_to;
	var $_event;
	var $_username;

	function InviteMail ($address, $name, $event)
	{
		$this->_to = &$address;
		$this->_event = &$event;
		$this->_username = &$name;
	}

	function send ()
	{
		$event = &$this->_event;
		$body = &new Page ();
		$body->assign ('name', &$this->_username);
		$body->assign ('event_name', $event->getName ());
		$body->assign ('url', base_url . $event->getID ());
		mail ($this->_to, 'PLANster invitation for '.$event->getName (),
					$body->fetch ('invitation-mail.tpl'),
							'From: '.mail_from);
	}
}
?>