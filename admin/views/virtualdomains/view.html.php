<?php
/**
* @version		$Id: view.html.php 10381 2009-08-01 12:35:53Z mliebler $
 * @package		Virtualdomains
 * @subpackage	Virtualdomains
 * @author     	Michael Liebler {@link http://www.janguo.de}
 * @copyright	Copyright (C) 2008 - 2009 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Virtualdomains is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Virtual Domains component
 *
 * @static
 * @package		Joomla
 * @subpackage	Virtual Domains
 * @since 1.0
 */
class virtualdomainsViewvirtualdomains extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		$db		=& JFactory::getDBO();
		
		$query = "SELECT params,id FROM `#__plugins` WHERE `element` = 'virtualdomains' AND `folder` = 'system'";
		$db->setQuery($query);
		$tmp = $db->loadObject();
		$tmpparams = new JParameter($tmp->params);
		if($tmpparams->get('std_domain') == '') {
			?>
			<h3><?php echo JText::_('Configure Plugin') ?></h3>
			<p style="font-size:1.1em;font-weight:bold;color:red">Go to <a href="<?php echo JRoute::_('index.php?option=com_plugins&view=plugin&client=site&task=edit&cid[]='.$tmp->id) ?>">index.php?option=com_plugins</a> and configure the Standarddomain</p>
			<?php
			return;
		}
		
		$uri	=& JFactory::getURI();

		$filter_state		= $mainframe->getUserStateFromRequest( $option.'filter_state',		'filter_state',		'',				'word' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		// Get data from the model
		$items		= & $this->get( 'Data');
		if(count($items)) {
			$total		= & $this->get( 'Total');
		}
		$pagination = & $this->get( 'Pagination' );

		// build list of categories
		$javascript 	= 'onchange="document.adminForm.submit();"';


		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}
}