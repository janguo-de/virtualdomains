<?php
/**
 * @version		$Id$:homes.php  $LastChangeRevision$ $LastChangeDate$ $LastChangedBy$ $
 * @package		Virtualdomains
 * @subpackage	Joomla
 * @copyright	Copyright (C) 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Menu Item List Model for Menus.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_virtualdomains
 * @since		1.6
 */
class virtualdomainsModelHomes  extends JModelList 
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'menutype', 'a.menutype',
				'title', 'a.title',
				'alias', 'a.alias',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'language', 'a.language',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'lft', 'a.lft',
				'rgt', 'a.rgt',
				'level', 'a.level',
				'path', 'a.path',
				'client_id', 'a.client_id',
				'home', 'a.home',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context.'.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);


		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		
		$domain = $this->getUserStateFromRequest($this->context.'.filter.domain', 'filter_domain', '');
		$this->setState('filter.domain', $domain);
		
		// Component parameters.
		$params	= JComponentHelper::getParams('com_menus');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.lft', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.language');
		$id	.= ':'.$this->getState('filter.domain');
		$id	.= ':'.$this->getState('filter.search');


		return parent::getStoreId($id);
	}

	/**
	 * Finds the default menu type.
	 *
	 * In the absence of better information, this is the first menu ordered by title.
	 *
	 * @return	string	The default menu type
	 * @since	1.6
	 */
	protected function getDefaultMenuType()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true)
			->select('menutype')
			->from('#__menu_types')
			->order('title');
		$db->setQuery($query, 0, 1);
		$menuType = $db->loadResult();

		return $menuType;
	}

	
	public  function getDomainNames () {
	
		static $instance;
		if (!empty($instance)) return $instance;
		
		$db		= $this->getDbo();
		$query	= $db->getQuery(true)
			->select('domain')
			->from('#__virtualdomain')
			->order('domain');
		$db->setQuery($query);
		$instance = $db->loadResultArray();
		return $instance;
	} 	
	
	/**
	 * Builds an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery	A query object.
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__menu` AS a');

		// Join over the menu types
		$query->select('t.title AS menu_title');
		$query->join('LEFT', '`#__menu_types` AS t ON t.menutype = a.menutype');

		// Join over the domains
		$query->select('vd.domain AS domain');
		$query->join('LEFT', '`#__virtualdomain_menu` AS vd ON vd.menu_id = a.id');		
		
		// Join over the language
		$query->select('l.title AS language_title, l.image as image');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the users.
		$query->select('u.name AS editor');
		$query->join('LEFT', '`#__users` AS u ON u.id = a.checked_out');

		//Join over components
		$query->select('c.element AS componentname');
		$query->join('LEFT', '`#__extensions` AS c ON c.extension_id = a.component_id');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Exclude the root category.
		$query->where('a.id > 1');
		$query->where('a.client_id = 0');

		// Filter on the published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by search in title, alias or id
		if ($search = trim($this->getState('filter.search'))) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else if (stripos($search, 'link:') === 0) {
				if ($search = substr($search, 5)) {
					$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
					$query->where('a.link LIKE '.$search);
				}
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('('.'a.title LIKE '.$search.' OR a.alias LIKE '.$search.' OR a.note LIKE '.$search.')');
			}
		}

		// Filter the items over the parent id if set.
		$parentId = $this->getState('filter.parent_id');
		if (!empty($parentId)) {
			$query->where('p.id = '.(int)$parentId);
		}

		// Filter on the access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = '.(int) $access);
		}

		// Filter on the level.
		if ($level = $this->getState('filter.level')) {
			$query->where('a.level <= '.(int) $level);
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = '.$db->quote($language));
		}
		
		// Filter on the language.
		if ($domain = $this->getState('filter.domain')) {
			$query->where('vd.domain = '.$db->quote($domain));
		}
		
		//home items only
		$query->where('(a.home = 1 OR a.home = -1 OR vd.domain IS NOT NULL)' );
		
		//component items only
		$query->where('a.type =  '.$db->quote('component'));
		
		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.lft')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));


		
		//echo nl2br(str_replace('#__','jos_',(string)$query)).'<hr/>';
		return $query;
	}
}
