<?php /**
 * @version		$Id: virtualdomains.php 10381 2008-06-01 03:35:53Z mliebler $
 * @package		Virtualdomains
 * @subpackage	plug_virtualdomains
 * @copyright	Copyright (C) 2008 - 2009 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Virtualdomains is free software. This version may have been modified pursuant
 * @author     	Michael Liebler {@link http://www.janguo.de}
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.menu' );
jimport( 'joomla.plugin.plugin' );
jimport('joomla.application.module.helper');
/*
* test
*/


class vdMenuFilter extends JMenu {
	/**
	 * 
	 * Method to Filter Menu Items 
	 * @param array $items - Array of menu item id's
	 * @param string $filter - show/hide
	 */
	
	function filterMenues($items, $filter, $default) {
		
		//Get the instance
		$menu = & parent::getInstance('site',array());
		
		//Set all defaults on default
		//TODO: Allow language specific home items
		$menu->setDefault($default, JFactory::getLanguage()->getTag());
		$menu->setDefault($default,'*');
		$menu->setDefault($default);

		//Check every item
		foreach($menu->_items  as $item) {
 
			switch($filter) {
				case "hide":
					//Delete menu item, if the item id  is in the items list
					if(in_array($item->id, $items)) {
			    		unset($menu->_items[$item->id]);
					}					
					 break;
				case "show":
					//Delete menu item, if the item id  is not in the items list
					if(!in_array($item->id, $items)) {
			    		unset($menu->_items[$item->id]);
					}							
			}
		}		
	}
}


class plgSystemVirtualdomains extends JPlugin
{

	private $_db = null;
	private $_request = array();
	private $_hostparams = array();

	

	
	/**
	 * Constructor
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	public function plgSystemVirtualdomains( &$subject, $config )
	{
		$this->_db = JFactory::getDBO();
		parent::__construct( $subject, $config );		
	}
	
	private function filterMenus($default) 
	{
		$filter = $this->_hostparams->get( 'menumode' );
		$items = $this->_hostparams->get( 'menufilter' );
		if(!$filter) return;
		$menu = new vdMenuFilter();
		$menu->filterMenues($items, $filter, $default );							
	}

	/**
	 *  onAfterInitialise
	 */
	public function onAfterInitialise()
	{

		jimport('joomla.user.authentication');
		$this->_hostparams = null;
		$app = &JFactory::getApplication();
		$db = &JFactory::getDBO();
		$user = &JFactory::getUser();

		if ( $app->isAdmin() )
		{
			return; // Dont run in backend
		}
		

		$uri = JURI::getInstance();
		$jos_host = str_replace( 'www.', '', $uri->getHost() );

		$defaultDomain = $this->_getDefaultDomain(); 
		
		//TODO: Since default domain is found in the table, some things are to proceed...
		//let joomla do its work, if its the main domain
		if ( $jos_host == $defaultDomain) return;

		//is there an entry for the domain returned by $uri->getHost() ?
		$query = "SELECT  * FROM #__virtualdomain as a WHERE domain = " . $db->Quote( $jos_host ) . " AND published > 0";
		
		$db->setQuery( $query );
		$row = $db->loadObject();
		
		$user = &JFactory::getUser();
		

		//$vdUser = new vdUser($user->get('id'));
		$vdUser->addAuthLevel($row->viewlevel);

		// Set Meta Data
		$this->_hostparams = $this->_getParams( $row->params );
		
		$config = &JFactory::getConfig();
		if ( is_object( $this->_hostparams ) )
		{
			if ( trim( $this->_hostparams->get( 'metadesc' ) ) )
			{
				$config->setValue( 'config.MetaDesc', $this->_hostparams->get( 'metadesc' ) );
			}
			if ( trim( $this->_hostparams->get( 'keywords' ) ) )
			{
				$config->setValue( 'config.MetaKeys', $this->_hostparams->get( 'keywords' ) );
			}
			if ( trim( $this->_hostparams->get( 'metatitle' ) ) )
			{
				$config->setValue( 'config.sitename', $this->_hostparams->get( 'metatitle' ) );
			}

		}
		//return, if no result
		if ( !$row ) return;

		//set the template
		if ( $row->template )
		{
			$jv = new JVersion();
			//let 1.6 set the template by itself - otherwise the templates params won't be set
			if ( $jv->RELEASE > 1.5 )
			{
				JRequest::setVar( 'template', $row->template );
			} else
			{
				$app->setTemplate( $row->template );
			}
		}

		//Set the route, if necessary
		if ( !$this->_reRoute( $row, $uri ) )
		{
			$this->setActions();
		}
		
		$this->filterMenus($row->menuid); 		
	}

	
	private function _getDefaultDomain() {
			
			static $instance;
			
			if(!empty($instance)) return $instance;
			
			$db = JFactory::getDbo();

			$db->setQuery(
				"SELECT domain FROM #__virtualdomain
				  WHERE `home` = ".$db->Quote('1')			
			);
			
			$instance = $db->loadResult();
			
			return $instance;
	}
	
	/**
	 * 
	 *  Routes to VD-Hosts home, if necessery
	 */

	private function _reRoute( $row, &$uri )
	{

		if ( $this->params->get( 'noreroute' ) )
		{
			return false;
		}
		$db = &JFactory::getDBO();

		//get the domains frontpage menu item
		$query = "SELECT link" . "\n FROM #__menu" . "\n WHERE id = " . ( int )$row->menuid . "\n AND published = '1'";
		$db->setQuery( $query );
		$menulink = $db->loadResult();
		if ( !$menulink )
		{
			//item is lost
			return false;
		}

		//check the Item ID, that joomla will return, if we are on frontpage
		$query = "SELECT id " . "\n FROM #__menu " . "\n WHERE home = 1 ";
		$db->setQuery( $query );
		$orighome = $db->loadResult();

		$this->_switchMenu( $orighome, $row->menuid );

		$router = JSite::getRouter();

		$query_link = $router->parse( clone ( $uri ) );

		//do nothing, if we are not on frontpage
		if ( !isset( $query_link['Itemid'] ) or ( ( int )$query_link['Itemid'] != ( int )$orighome ) )
		{
			return false;
		}
		$menu = &JSite::getMenu();
		$menu->setActive( $row->menuid );
		$this->setRequest( 'Itemid', $row->menuid );

		$this->setJoomfishLang();

		//rewrite the uri
		$link = $menulink . "&Itemid=" . $row->menuid;

		//Parse the new Url
		var_dump( $this->_getBase() );
		var_Dump( $link );
		$parse = parse_url( $this->_getBase() . $link );

		//Build the new Query
		$request = array();
		parse_str( $parse['query'], $request );
		$this->_request = array_merge( $request, $this->_request );
		$parse['query'] = JURI::buildQuery( $this->_request );

		//Not shure, whether this make sense...
		//$uri->setQuery($parse['query'] );

		//rewrite some server environment vars to fool joomla
		$_SERVER['QUERY_STRING'] = $parse['query'];
		$_SERVER['REQUEST_URI'] = $this->_getBase() . $link;
		$_SERVER['PHP_SELF'] = $this->_getBase() . $parse['path'];
		$_SERVER['SCRIPT_NAME'] = $this->_getBase() . $parse['path'];
		$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . DS . preg_replace( '#[/\\\\]+#', DS, $parse['path'] );

		//set userdefined actions
		$this->setActions( 1 );

		JRequest::set( $this->_request, 'get', false );
		JRequest::set( $this->_request, 'post', false );
		return true;
	}

	/**
	 * 
	 *  Method to get the VD-Hosts params
	 */

	public function _getParams( $dparams )
	{

		if ( isset( $dparams ) )
		{
			$params = json_decode( $dparams );
			if ( !is_object( $params ) )
			{
				$params = json_decode( $params );
			}
			$params = JArrayHelper::fromObject( $params );
			$dparams = new JObject();
			$dparams->setProperties( $params );
			return $dparams;
		}
	}

	/**
	 * 
	 *  Method to set userdefined params to $REQUEST or $GLOBALS
	 */
	private function setActions( $home = 0 )
	{
		$db = &JFactory::getDBO();
		$db->setQuery( 'Select * From #__virtualdomain_params Where 1' );
		$result = $db->loadObjectList();

		$params = $this->_hostparams->getProperties();
		if ( count( $params ) )
		{
			for ( $i = 0; $i < count( $result ); $i++ )
			{
				foreach ( $params as $key => $value )
				{

					if ( !$home && $result[$i]->home ) continue;

					if ( $result[$i]->name == $key )
					{
						$value = urlencode( $value );
						$result[$i]->name = urlencode( $result[$i]->name );
						switch ( $result[$i]->action )
						{
							case 'globals':
								$GLOBALS[$result[$i]->name] = $value;
								break;
							case 'request':
								if ( $home )
								{
									$this->setRequest( $result[$i]->name, $value );
								} else
								{
									JRequest::setVar( $result[$i]->name, $value );
								}
								break;
						}
					}
				}
			}
		}

	}

	/**
	 * 
	 *  Method to switch the menu to the VD-hosts home
	 */
	private function _switchMenu( $orighome, $newhome )
	{

		$menu = &JSite::getMenu();
		$item = &$menu->getItem( $newhome );
		$nohome = &$menu->getItem( $orighome );
		$nohome->home = null;
		$item->home = 1;
		$menu->setDefault( $newhome );
		//$menu->setActive($newhome);
	}

	/**
	 * Method to add or set a var to the request
	 * we dont need the $path .= '/';
	 * joomla does this for us
	 */
	private function _getBase()
	{
		$path = '';
		if ( strpos( php_sapi_name(), 'cgi' ) !== false && !empty( $_SERVER['REQUEST_URI'] ) )
		{
			//Apache CGI
			$path = rtrim( dirname( str_replace( array( '"', '<', '>', "'" ), '', $_SERVER["PHP_SELF"] ) ), '/\\' );
			# var_dump($path);
		} else
		{
			//Others
			$path = rtrim( dirname( $_SERVER['SCRIPT_NAME'] ), '/\\' );
		}
		/*	$path .= '/';*/
		return $path;
	}

	/**
	 * 
	 * Sets the lang Variable, if not set by Joomfish
	 */

	private function setJoomfishLang()
	{
		if ( !$this->_hostparams->get( 'language' ) )
		{
			return;
		}
		$jfcookie = JRequest::getVar( 'jfcookie', null, "COOKIE" );
		if ( isset( $jfcookie["lang"] ) && $jfcookie["lang"] != "" )
		{
			return;
		}
		$this->setRequest( 'lang', $this->_hostparams->get( 'language' ) );
	}

	/**
	 * 
	 * Method to add or set a var to the request
	 */
	private function setRequest( $var, $value )
	{
		$this->_request[$var] = $value;
	}

}

/**
 * 
 * Dummy User Class
 * @author michel
 *
 */
class vdUser extends JUser {
	
	function __construct($identifier) {
		parent::__construct($identifier);		
	}
	
	/**
	 * 
	 * This method proceeds a pseudo login for guests
	 * The viewlevel is pushed to the user object 
	 * 
	 * @param int $viewlevel
	 */
	function addAuthLevel($viewlevel) {		
		if(!$this->id) {
			$user = new JUser();
			$user->guest = 1;
			$user->_authLevels[] = 1;			
			$user->_authLevels[] = $viewlevel;
		} else {
			$user = new JUser($this->id);
        	$user->_authLevels=  JAccess:: getAuthorisedViewLevels($user->id);
        	$user->_authLevels[] = $viewlevel;
			
		}
		$session = JFactory::getSession();							
		$session->set('user', $user);
	
         
	}
}