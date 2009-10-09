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
 * $Id: register.php 427 2007-03-16 02:56:31Z stefan $
 */

require_once ('config/config.inc.php');
require_once (libdesire_path . '/view/Page.inc.php');
require_once (src_path . '/RootPage.inc.php');

$root = new RootPage ('Create a PLAN');
$page = new Page ();
$page->assign ('maxMonths', max_age_for_event);

if (array_key_exists ('warn', $_GET)) {
	$page->assign ('warn', true);
}

$root->assign ('body', $page->fetch ('register.tpl'));
$root->display ();
?>