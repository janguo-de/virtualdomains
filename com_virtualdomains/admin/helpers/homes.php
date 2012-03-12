<?php
/**
 * @version		$Id$:homes.php ressource.php $LastChangeRevision$ $LastChangeDate$ $LastChangedBy$ $
 * @package		Jimtawl
 * @subpackage	Joomla
 * @copyright	Copyright (C) 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
class VDHomesHelper {
	
	/**
	 * Get a list of menu links for one or all menus.
	 *
	 * @param	string	An option menu to filter the list on, otherwise all menu links are returned as a grouped array.
	 * @param	int		An optional parent ID to pivot results around.
	 * @param	int		An optional mode. If parent ID is set and mode=2, the parent and children are excluded from the list.
	 * @param	array	An optional array of states
	 */
	public static function getMenuLinks($menuType = null, $parentId = 0, $mode = 0, $published=array())
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text, a.level, a.menutype, a.type, a.template_style_id, a.checked_out');
		$query->from('#__menu AS a');
		$query->join('LEFT', '`#__menu` AS b ON a.lft > b.lft AND a.rgt < b.rgt');

		// Filter by the type
		if ($menuType) {
			$query->where('(a.menutype = '.$db->quote($menuType).' OR a.parent_id = 0)');
		}

		if ($parentId) {
			if ($mode == 2) {
				// Prevent the parent and children from showing.
				$query->join('LEFT', '`#__menu` AS p ON p.id = '.(int) $parentId);
				$query->where('(a.lft <= p.lft OR a.rgt >= p.rgt)');
			}
		}

		if (!empty($published)) {
			if (is_array($published)) $published = '(' . implode(',',$published) .')';
			$query->where('a.published IN ' . $published);
		}

		$query->where('a.published > 0');
		//home items only
		$query->where('a.home != '.(int) 1);
		
		//exclude unconfigured home items
		$query->where('a.home != '.(int) -1);
		
		//component items only
		$query->where('a.type =  '.$db->quote('component'));
		$query->group('a.id');
		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		$links = $db->loadObjectList();

		// Check for a database error.
		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
			return false;
		}

		// Pad the option text with spaces using depth level as a multiplier.
		foreach ($links as &$link) {
			$link->text = str_repeat('- ',$link->level).$link->text;
		}

		if (empty($menuType)) {
			// If the menutype is empty, group the items by menutype.
			$query->clear();
			$query->select('*');
			$query->from('#__menu_types');
			$query->where('menutype <> '.$db->quote(''));
			$query->order('title, menutype');
			$db->setQuery($query);

			$menuTypes = $db->loadObjectList();

			// Check for a database error.
			if ($error = $db->getErrorMsg()) {
				JError::raiseWarning(500, $error);
				return false;
			}

			// Create a reverse lookup and aggregate the links.
			$rlu = array();
			foreach ($menuTypes as &$type) {
				$rlu[$type->menutype] = &$type;
				$type->links = array();
			}

			// Loop through the list of menu links.
			foreach ($links as &$link) {
				if (isset($rlu[$link->menutype])) {
					$rlu[$link->menutype]->links[] = &$link;

					// Cleanup garbage.
					unset($link->menutype);
				}
			}

			return $menuTypes;
		} else {
			return $links;
		}
	}
	
}

