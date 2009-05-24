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
 * $Id: show.php 142 2006-05-01 01:30:13Z stefan $
 */

	require_once('config/config.inc.php');
	require_once(src_path . '/DateList.inc.php');
	require_once(src_path . '/Event.inc.php');
	require_once(src_path . '/RootPage.inc.php');
	require_once(src_path . '/PersonList.inc.php');

	if (!array_key_exists('id', $_GET)) {
		header('location: index.php');
	}

	$event = new Event($_GET['id']);
	if (!$event->load()) {
		$root = new RootPage('Illegal event');
		$page = new Page();
		$root->assign('body', $page->fetch('illegalevent.tpl'));
		$root->display();
		die();
	}

	if (array_key_exists('switch', $_GET)) {
		$event->switchOrientation();
		$event->save();
		header('location: show.php?id=' . $_GET['id']);
		die();
	}

	$page = new RootPage($event->getName());
	$page->setID($_GET['id']);

	$state = EVENT_STATE_NONE;
	$extra = NULL;

	if (array_key_exists('invite', $_GET)) {
		$extra = array();
		if (array_key_exists('warnMail', $_GET)) $extra['mail'] = true;
		if (array_key_exists('warnName', $_GET)) $extra['name'] = true;
		$state = EVENT_STATE_INVITE;
	} else if (array_key_exists('invited', 	$_GET)) {
		$extra = array();
		$extra['name'] = try_key('name', $_GET);
		$extra['mail'] = try_key('mail', $_GET);
		$state = EVENT_STATE_INVITED;
	} else if (array_key_exists('adddate',	$_GET)) {
		$state = EVENT_STATE_ADDDATE;
	} else if (array_key_exists('editdate',	$_GET)) {
		$extra = $_GET['editdate'];
		$state = EVENT_STATE_EDITDATE;
	} else if (array_key_exists('edit',	$_GET)) {
		$extra = $_GET['edit'];
		$state = EVENT_STATE_EDIT_PERSON;
	} else if (array_key_exists('clone',	$_GET)) {
		$extra = array();
		if (array_key_exists('warnStatus', $_GET)) $extra['status'] = true;
		if (array_key_exists('warnOwner', $_GET)) $extra['owner'] = true;
		$state = EVENT_STATE_CLONE;
	} else if (array_key_exists('cloned',	$_GET)) {
		$extra = $_GET['cloned'];
		$state = EVENT_STATE_CLONED;
	}

	$page->assign('body', $event->get($state, $extra));
	$page->display();
?>
