<?php
/*
 * libdesire database handler
 * Copyright (C) 2006 Stefan Ott. All rights reserved.
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
 * $Id: DBItem.inc.php 76 2006-11-12 00:45:57Z stefan $
 */

require_once (dirname (__FILE__) . '/../config/libdesire.inc.php');
require_once (adodb_path . '/adodb.inc.php');

class DBItem
{
	var $_conn;

	function _connect ()
	{
		global $DB_CONNECTION;
		if (!$DB_CONNECTION)
		{
			$dsn = dbDriver . '://' . dbUser . ':' . dbPass . '@' .
							dbHost . '/' . dbName;
			$DB_CONNECTION = @ADONewConnection ($dsn);

			if (!$DB_CONNECTION)
			{
				if (defined ('db_error_page'))
				{
					header ('location: ' . db_error_page);
				}
				else
				{
					die ('Database error');
				}
			}
		}
		$this->_conn = &$DB_CONNECTION;
	}
}
?>