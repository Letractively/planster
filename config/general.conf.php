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
 * $Id: general.conf.php 158 2006-05-19 00:36:15Z stefan $
 */

	error_reporting(E_ALL);

	define('planster_version', '0.2.2');

	define('adodb_path', '/usr/share/adodb');
	define('src_path', 'src/planster');

	define('max_age_for_event', 6); // months

	define('smarty_src_path', 'src/smarty');
	define('smarty_template_dir', 'templates/templates');
	define('smarty_compile_dir', 'templates/compiled');

	define('mail_from', 'noreply@planster.net');
	define('date_format', 'Y-m-d');

	define('rdf_max_items', 5);

	// re-initialize the database after changing these values
	define('MAX_EVENT_TITLE_LENGTH', 50);
	define('MAX_USER_NAME_LENGTH', 30);
	define('MAX_MAIL_ADDRESS_LENGTH', 50);
	define('MAX_DATE_LENGTH', 16);

	// do not change these unless you *know* what you're doing
	define('EVENT_ID_LENGTH', 12);
	define('EXTRA_FIELD_LENGTH', 20);
?>
