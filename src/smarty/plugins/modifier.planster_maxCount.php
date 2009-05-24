<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * PLANster max count modifier plugin
 *
 * Type:     modifier<br>
 * Name:     upper<br>
 * Purpose:  figure out the maximum count of all supplied dates
 * @author   Stefan Ott
 * @param string
 * @return string
 */
function smarty_modifier_planster_maxCount($groups)
{
	$max = 0;

	foreach ($groups as $group)
	{
		foreach ($group->getChildren() as $date)
		{
			$max = max ($max, $date->sum());
		}
	}
	return $max;
}

?>
