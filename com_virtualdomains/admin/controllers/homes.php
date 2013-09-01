<?php /**
 * @version		$virtualdomain.php
 * @package		Virtualdomain
 * @subpackage 	Controllers
 * @copyright	Copyright (C) 2010, . All rights reserved.
 * @author     	Michael Liebler {@link http://www.janguo.de}
* @copyright	Copyright (C) 2008 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Virtualdomains is free software. This version may have been modified pursuant to the
* GNU General Public License, and as distributed it includes or is derivative
* of works licensed under the GNU General Public License or other free or open
* source software licenses. See COPYRIGHT.php for copyright notices and
* details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controlleradmin');

/**
 * VirtualdomainVirtualdomain Controller
 *
 * @package    Virtualdomain
 * @subpackage Controllers
 */
class VirtualdomainsControllerHomes extends VirtualdomainsController
{
    /**
     * Constructor
     */
    protected $_viewname = 'homes';

    public function __construct( $config = array() )
    {
        
    	$lang = JFactory::getLanguage();
    	$lang->load('com_menus');
    	JSubMenuHelper::addEntry(
			JText::_('COM_MENUS_SUBMENU_MENUS'),
			'index.php?option=com_menus&view=menus',
			false
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_MENUS_SUBMENU_ITEMS'),
			'index.php?option=com_menus&view=items',
			false
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_VD_SUBMENU_HOMES'),
			'index.php?option=com_virtualdomains&view=homes',
			true
		);
    	parent::__construct( $config );
        JRequest::setVar( 'view', $this->_viewname );

    }
   
    /**
     * VirtualdomainsControllerVirtualdomain::cancel()
     * Cancels the Editing Form
     * @return void
     */
    function cancel()
    {
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $this->setRedirect( 'index.php?option=com_virtualdomains&view=homes' );

    }

    /**
     * Stores the user configuration
     */
    
    public function save() {

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );
        $app = JFactory::getApplication();
        $db = &JFactory::getDBO();

        $post = JRequest::getVar( 'jform', array(), 'post', 'array' );
         
        $domains = $post['domain'];  
        $languages = $post['language'];
        $domainhomes = $post['domainhome'];
        
        $link = 'index.php?option=com_virtualdomains&view=homes';
        $msg = "";
        $msgtype = "";
 
        if(count($domains))  {
        	foreach ($domains as $key => $value) {        		        		
        		//Store the home in the virtualdomains table, if a domain exists. Wlse store home in the menu table
        		if($value) {
        			$home = 0;
        			$menuid = $key;
        		} else {
        			$home = 1;
        			$menuid = 0;
        		}         		

        	    //Set home and language in the menu table
        		$query = "UPDATE #__menu SET home = ".(int) $home.', language = '.$db->Quote($languages[$key]).' WHERE id ='.(int) $key;
        		$db->setQuery($query);        	
        		if(!$db->query()) {
					$msgtype = "warning";
					$msg = "DB Error";
				}        		
        		
        		//If user changed the domain, delete home item from old domain
        		if($value &&  $domainhomes[$key]) {
        		    $query = "UPDATE #__virtualdomain_menu SET domain=  ".$db->Quote($value)." WHERE menu_id =".(int) $key ." AND domain=".$db->Quote($domainhomes[$key]);
        			$db->setQuery($query);        	
        			if(!$db->query()) {
						$msgtype = "warning";
						$msg = "DB Error";
					}
        		} elseif(!$value) {
        			//old home is to delete
        			$query = "DELETE FROM #__virtualdomain_menu WHERE menu_id =".(int) $key ." AND domain=".$db->Quote($domainhomes[$key]);
        		    $db->setQuery($query);        	
        			if(!$db->query()) {
						$msgtype = "warning";
						$msg = "DB Error";
					}        			
        		} else {
        			$query = "INSERT INTO #__virtualdomain_menu SET domain=  ".$db->Quote($value).", menu_id =".(int) $key;
        			$db->setQuery($query);        	
        			if(!$db->query()) {
						$msgtype = "warning";
						$msg = "DB Error";
					}
        		}
 
						
        	}        	
        }


   		$this->setRedirect( $link, $msg, $msgtype );
    	
    }
    
    /**
     * makes the selected items "home"
     */
    public function home()
    {
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );
        $app = JFactory::getApplication();
        $db = &JFactory::getDBO();

        $post = JRequest::getVar( 'jform', array(), 'post', 'array' );
		
        //Redirect link
        $link = 'index.php?option=com_virtualdomains&view=homes';
        $msg = "";
        $msgtype = "";
        //Make the assigned items "home"
		if(count($post['assigned'] ))  {
			foreach($post['assigned'] as $itemId) {
				
				$db->setQuery('UPDATE #__menu SET home=-1 WHERE id = '. (int) $itemId);
				if(!$db->query()) {
					$msgtype = "warning";
					$msg = "DB Error";
				}
			
			}
		} 
  
        $this->setRedirect( $link, $msg, $msgtype );
    }
    

 function unhome()
    {

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $db = &JFactory::getDBO();
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger( $cid );
        $msg = JText::_('Items removed from the list of home items' );
        $msgtype = "";
        $items = JRequest::getVar( 'jform', array(), 'post', 'array' );
        if ( count( $cid ) < 1 )
        {
            JError::raiseError( 500, JText::_( 'Select an item to delete' ) );
        
        } else {
        	
        	foreach($cid as $itemId) {
				if($items['domainhome'][$itemId]) {
				     $db->setQuery('DELETE FROM #__virtualdomain_menu WHERE domain = '.  $db->Quote($items['domainhome'][$itemId]).' AND menu_id ='.(int)$itemid);
					if(!$db->query()) {
						$msgtype = "warning";
						$msg = "DB Error";
					}					
				} else {
					$db->setQuery('UPDATE #__menu SET home=0 WHERE id = '. (int) $itemId);
					if(!$db->query()) {
						$msgtype = "warning";
						$msg = "DB Error";
					}
				}				
			}
        }
        
        $link = 'index.php?option=com_virtualdomains&view=homes';
        $this->setRedirect( $link, $msg, $msgtype  );
    }


} // class
 ?>