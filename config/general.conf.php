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
 * $Id: general.conf.php 100 2006-04-26 00:47:10Z stefan $
 */

	error_reporting(E_ALL);

	define('planster_version', '0.2.0');

	define('adodb_path', '/usr/share/adodb');
	define('src_path', 'src/planster');

	define('max_age_for_event', 6); // months

	define('smarty_src_path', 'src/smarty');
	define('smarty_template_dir', 'templates/templates');
	define('smarty_compile_dir', 'templates/compiled');

	define('mail_from', 'noreply@planster.net');
	define('date_format', 'Y-m-d');

	define('rdf_max_items', 5);
?>
