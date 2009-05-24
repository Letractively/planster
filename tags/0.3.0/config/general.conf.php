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
 * $Id: general.conf.php 436 2007-03-16 12:45:40Z stefan $
 */
 	$base = realpath (dirname (__FILE__) . '/../');

	error_reporting (E_ALL);

	define ('planster_version', '0.3.0');
	define ('dblayout_version', '0.3');

	define ('adodb_path', '/usr/share/adodb');
	define ('src_path', $base . '/src/planster');

	define ('max_age_for_event', 12); // months

	define ('smarty_src_path', $base . '/src/smarty');
	define ('smarty_template_dir', $base . '/templates/templates');
	define ('smarty_compile_dir', $base . '/templates/compiled');

	define ('mail_from', 'noreply@planster.net');
	define ('date_format', 'Y-m-d');

	define ('rdf_max_items',		5);

	// re-initialize the database after changing these values
	define ('MAX_EVENT_TITLE_LENGTH',	50);
	define ('MAX_USER_NAME_LENGTH',		30);
	define ('MAX_MAIL_ADDRESS_LENGTH',	50);
	define ('MAX_DATE_LENGTH',		16);
	define ('MAX_GROUP_NAME_LENGTH',	50);

	// do not change these unless you *know* what you're doing
	define ('EVENT_ID_LENGTH',		12);
	define ('EXTRA_FIELD_LENGTH',		20);

	define ('DEFAULT_GROUP',		1);

	define ('ORIENTATION_HORIZONTAL',	1);
	define ('ORIENTATION_VERTICAL',		2);
?>
