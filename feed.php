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
 * $Id: feed.php 326 2007-02-13 06:50:59Z stefan $
 */

header ('Content-Type: text/xml; charset=utf-8');

require_once ('config/config.inc.php');
require_once (src_path . '/ChangeLog.inc.php');
require_once (src_path . '/Event.inc.php');
require_once (libdesire_path . 'util/util.inc.php');
require_once (libdesire_path . 'view/Page.inc.php');

$eventID = require_key ('id', $_GET);

$event = new Event ($eventID);

if ($event->load ())
{
	$log = new ChangeLog ($eventID);
	$log->load ();

	$page = new Page ();
	$page->assign ('event_title', $event->getName ());
	$page->assign ('event_id', $event->getID ());
	$page->assign ('items', $log->getItems ());
	$page->assign ('owner', $event->getOwner ());
	$latest = $log->getLatest ();
	$page->assign ('latest', &$latest);
	$page->assign ('event_url', base_url . $eventID);
	$page->display ('atom.tpl');
}
?>
