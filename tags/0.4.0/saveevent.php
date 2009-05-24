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
 * $Id: saveevent.php 561 2007-08-30 00:27:44Z stefan $
 */

require_once ('config/config.inc.php');
require_once (src_path . '/Event.inc.php');
require_once (src_path . '/RootPage.inc.php');
require_once (src_path . '/WelcomeMail.inc.php');
require_once (libdesire_path . 'view/Page.inc.php');
require_once (libdesire_path . 'util/util.inc.php');
require_once (libdesire_path . 'util/io.inc.php');

$eventName = require_key ('eventName', $_POST);
$owner = require_key ('userName', $_POST);
$mail = require_key ('mail', $_POST);

if (empty ($eventName) || empty ($owner))
{
	header ('location: register.php?warn');
	die ();
}

$event = new Event ();
$event->setName ($eventName);
$event->setOwner ($mail);
$event->expiresInMonths ($_POST ['expires']);

$person = new Person ($event->getID ());
$person->setName ($owner);
$person->save ();

if (array_key_exists ('id', $_POST))
{
	$event->setId ($_POST ['id']);
}

$ok = $event->save ();

if ($ok && !empty ($mail))
{
	$msg = new WelcomeMail ($mail, $owner, $event);
	$msg->send ();
}

$root = new RootPage ('Save PLAN');
$page = new Page ();
if (!$ok) $page->assign ('error', 'Please fill in all the fields');
$page->assign ('id', $event->getId ());
$page->assign ('mail', $mail);
$root->assign ('body', $page->fetch ('saveevent.tpl'));
$root->display ();
?>
