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
 * $Id: show.php 76 2006-04-25 04:11:36Z stefan $
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
	$page->assign('body', array_key_exists('edit', $_GET) ? $event->get($_GET['edit']) : $event->get());
	$page->display();
?>
