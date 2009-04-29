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
 * $Id: title.php 493 2007-04-27 01:06:43Z stefan $
 */

require_once ('config/config.inc.php');
require_once (src_path . '/Event.inc.php');
require_once (libdesire_path . 'util/io.inc.php');
require_once (libdesire_path . 'util/util.inc.php');

$eventID = require_key ('eventID', $_GET);

$data = json_get_post ();
$title = require_attribute ('title', $data);
$sumtype = require_attribute ('sumtype', $data);

$event = new Event ($eventID);
$event->load ();
$event->setName ($title);
$event->setSumType ($sumtype);
$event->save ();

$page = new Page ();
$page->assign ('id', $eventID);
$page->assign ('title', $event->getName ());

json_data ('title', $title);
?>
