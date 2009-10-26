<?php
/*
 * PLANster
 * Copyright (C) 2005/2006 Stefan Ott. All rights reserved.
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
 * $Id: saveevent.php 157 2006-05-19 00:35:52Z stefan $
 */

	require_once('config/config.inc.php');
	require_once(src_path . '/Event.inc.php');
	require_once(src_path . '/Page.inc.php');
	require_once(src_path . '/RootPage.inc.php');
	require_once(src_path . '/WelcomeMail.inc.php');

	$name = @$_POST['eventName'];
	$username = @$_POST['userName'];
	$mail = @$_POST['mail'];
	if (empty($name) || empty($username) || empty($mail)) {
		header('location: register.php?warn');
		die();
	}
	$orientation = @$_POST['orientation'];

	$event = new Event();
	$event->setName($name);
	$event->setOwner($mail);
	$event->expiresInMonths($_POST['expires']);

	$person = new Person($event->getID());
	$person->setName($username);
	$person->save();

	if (array_key_exists('id', $_POST)) {
		$event->setId($_POST['id']);
	}

	$error = $event->save();

	if (empty($error)) {
		$msg = new WelcomeMail($mail, $username, $event);
		$msg->send();
	}

	$root = new RootPage('Save event');
	$page = new Page();
	$page->assign('error', $error);
	$page->assign('id', $event->getId());
	$page->assign('mail', $mail);
	$root->assign('body', $page->fetch('saveevent.tpl'));
	$root->display();
?>