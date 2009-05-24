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
 * $Id: addtogroup.php 389 2007-03-14 14:47:48Z stefan $
 */

require_once ('config/config.inc.php');
require_once (src_path . '/Date.inc.php');
require_once (src_path . '/Group.inc.php');
require_once (libdesire_path . 'util/io.inc.php');
require_once (libdesire_path . 'util/util.inc.php');

$groupID = require_key ('group', $_GET);
$dateID = require_key ('date', $_GET);

$date = new Date ($dateID);
$date->load ();
$date->setGroup (new Group ($groupID));
$date->save ();

json_msg ('ADD_TO_GROUP_OK');
?>
