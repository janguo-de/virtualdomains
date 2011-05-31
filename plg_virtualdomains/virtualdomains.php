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


class plgSystemVirtualdomains extends JPlugin
{

	private $_db = null;
	private $_request = array();
	private $_hostparams = array();
	private $_curhost = array();

	

	
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
	
   /**
    * Method to hide/show Menu items and translate home iiten
    * 
    * @param int $default - Current domains home menu item
    */	
	private function filterMenus($default) 
	{
        
		$menu = new vdMenuFilter();
		$menu->filterMenues($this->_hostparams, $default);							
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
		$this->_curhost = str_replace( 'www.', '', $uri->getHost() );
		
		
		$currentDomain = $this->_getCurrentDomain();
		
		//TODO: Since default domain is found in the table, some things are to proceed...
		//let joomla do its work, if its the main domain
		if ($currentDomain === null) return;


		$user = &JFactory::getUser();
		

		$vdUser = new vdUser($user->get('id'));
		
		$viewlevels =  (array) $currentDomain ->params->get('access');
		
		$viewlevels[] = $currentDomain ->viewlevel;
		$vdUser->addAuthLevel($viewlevels);

		// Set Meta Data
		$this->_hostparams = $currentDomain ->params;		
		
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

		//Set the route, if necessary
		if ( !$this->_reRoute( $currentDomain , $uri ) )
		{
			$this->setActions();
		}
		
		//set the template
		if ( $currentDomain ->template )
		{
				JRequest::setVar( 'template', $currentDomain ->template );
		}
				
		$this->filterMenus($currentDomain ->menuid); 		
	}

	
	private function _getCurrentDomain() {
			
			static $instance;
			
			$vd = JComponentHelper::getComponent('com_virtualdomains');		
			$app = JFactory::getApplication();
			
			if(!empty($instance)) return $instance;
			
			$db = JFactory::getDbo();
			$db->setQuery(
				"SELECT * FROM #__virtualdomain
				  WHERE `domain` = ".$db->Quote($this->_curhost )			
			);

			$curDomain = $db->loadObject();
			
			if($db->getError() ) {
				return null;
			}
			
			if($curDomain === null) {
				return null;
			}			
	
			$curDomain->params = new JObject(json_decode($curDomain->params));

		  //Set the global override styles settings, if not configured
			if($curDomain->params->get('override') === '') $curDomain->params->set('override',$vd->params->get('override'));			
			
            $router = JSite::getRouter();

			$uri = JURI::getInstance();
		
		    $curDomain->query_link = $router->parse( clone ( $uri ) );
		  
		    $curDomain->activeItemId  = ( int )$curDomain->query_link['Itemid'];

			
			// Standard Domain uses Joomla settings
			if($curDomain->home == 1) {
				$curDomain->template = null;
				$curDomain->menuid = null;
			}
		    
			$menu = & JMenu::getInstance('site',array());
		
			$menuItem = & $menu->getItem(( int ) $curDomain->menuid );
		
			$origHome = $menu->getDefault();
			
			$curDomain->isHome = false;
			
			//do nothing, if we are not on frontpage
			if (  ( int )$curDomain->query_link['Itemid'] === ( int )$origHome->id )
			{
				$curDomain->isHome = true;
			} 
			
			
			//override style?
			switch($curDomain->params->get('override')) {
				
				case 1:			
					if(!$curDomain->isHome ) {
						$curDomain->template = null;
					}		
					break;
				case '0':
					$curDomain->template = null;
					break;		
			}
		    
			$instance =$curDomain; 
			
			return $instance;
	}
	
	
	
	/**
	 * 
	 *  Routes to VD-Hosts home, if necessery
	 */

	private function _reRoute($curDomain, &$uri )
	{
		
		//Is this the default domain?
		if($curDomain->home == 1) return;  

		if ( $this->params->get( 'noreroute' ) )
		{
			return false;
		}
		
		$menu = & JMenu::getInstance('site',array());
		
		$menuItem = & $menu->getItem(( int )$curDomain->menuid );
		
		$origHome = $menu->getDefault();  

		
		if ( !$menuItem )
		{
			//item is lost
			return false;
		}
		
		$menulink = $menuItem->link;

		
		$this->_switchMenu( $menu,$menuItem );


		//do nothing, if we are not on frontpage
		if ( $curDomain->isHome )
		{
			return false;
		}

		$menu->setActive( $curDomain->menuid );
		$this->setRequest( 'Itemid', $curDomain->menuid );

		$this->setJoomfishLang();

		//rewrite the uri
		$link = $menulink . "&Itemid=" . $curDomain->menuid;

		//Parse the new Url
		//var_dump( $this->_getBase() );
		///var_Dump( $link );
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
	private function _switchMenu( & $menu, &$newhome )
	{
		
		$nohome = & $menu->getDefault();
		$nohome->home = null;
		$newhome->home = 1;
		$menu->setDefault( $newhome->id);
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
		//There is no JoomFish for now :-(
		return;
		if ( !$this->_hostparams->get( 'language' ) )
		{
			return;
		}
       $lang = new vdLanguage();
		$lang_code = $this->_hostparams->get( 'language' );
       $lang->setDefault($this->_hostparams->get( 'language' ));
       $lang = JFactory::getLanguage();
	   $lang_codes 	= JLanguageHelper::getLanguages('lang_code');
		$default_lang = JComponentHelper::getParams('com_languages')->get('site',$this->_hostparams->get( 'language' ));
		$sef 	= $lang_codes[$default_lang]->sef;
		$jfcookie = JRequest::getVar( 'jfcookie', null, "COOKIE" );
		if ( isset( $jfcookie["lang"] ) && $jfcookie["lang"] != "" )
		{
			return;
		}
               $conf = JFactory::getConfig();
				$cookie_domain 	= $conf->get('config.cookie_domain', '');
				$cookie_path 	= $conf->get('config.cookie_path', '/');
				setcookie(JUtility::getHash('language'), $lang_code, time() + 365 * 86400, $cookie_path, $cookie_domain);
				// set the request var
		JRequest::setVar('language',$lang_code);		
		$this->setRequest( 'lang', $sef );
		$this->setRequest( 'language', $this->_hostparams->get( 'language' ));
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
 * Dummy JMenu Class
 * @author michel
 *
 */

class vdMenuFilter extends JMenu {
	/**
	 * 
	 * Method to Filter Menu Items 
	 * @param array $items - Array of menu item id's
	 * @param string $filter - show/hide
	 */
	
	function filterMenues($params, $default) {
		 $filter = $params->get( 'menumode' );
	
		 $items = $params->get( 'menufilter' );
		 $translatations = $params->get( 'translatemenu' );
		 $lang =  JFactory::getLanguage()->getTag() ;
	
		//Get the instance
		$menu = & parent::getInstance('site',array());
		 
		//Set all defaults on default
		//TODO: Allow language specific home items
		if($default) {
			$menu->setDefault($default, $lang);
			$menu->setDefault($default,'*');
			$menu->setDefault($default);
		}

		//if(!$filter) return;					
		//Check every item
		foreach($menu->_items  as $item) {
 			//Translate if translation available
			if ($item->home) {
 				if(isset($translatations->$lang) && ($menutranslation = trim($translatations->$lang))) {
 					$item->title = $menutranslation;
 				}
			}	
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

class vdLanguage extends JLanguage {
	
	function setDefault($lang) {
			$refresh = & JFactory::getLanguage();
			$refresh->metadata['tag'] = $lang;

			$refresh->default	= $lang;
			$new = & JFactory::getLanguage();	

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
	 * @param array $viewlevels
	 */
	function addAuthLevel($viewlevels) {		
		//No access levels assigned to this domain? return...
		if(!count($viewlevels)) return;		
		//is the user not logged in 
		if(!$this->id) {
			//we give him minimum public accesslevel and make him a guest
			$user = new JUser();
			$user->guest = 1;
			$user->_authLevels[] = 1;			
		} else {
			//prepare users accesslevel 
			$user = new JUser($this->id);
        	$user->_authLevels=  JAccess:: getAuthorisedViewLevels($user->id);			
		}
		
		//Now add all access levels assigned to this domain
		foreach($viewlevels as $viewlevel) {
			if($viewlevel)
					$user->_authLevels[] = $viewlevel;
		}
		
		//put this to the session
		$session = JFactory::getSession();							
		$session->set('user', $user);
	         
	}
}