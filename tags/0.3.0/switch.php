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
 * $Id: switch.php 389 2007-03-14 14:47:48Z stefan $
 */

require_once ('config/config.inc.php');
require_once (src_path . '/Event.inc.php');
require_once (libdesire_path . 'util/util.inc.php');
require_once (libdesire_path . 'util/io.inc.php');

$eventID = require_key ('eventID', $_GET);

$event = new Event ($eventID);
$event->load ();
$event->switchOrientation ();
$event->save ();

json_msg ('SWITCH_OK');
?>
