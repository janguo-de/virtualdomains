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
		jimport('joomla.application.router');
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
		if ( $currentDomain->template )
		{
			JRequest::setVar( 'template', $currentDomain ->template );
			if( $currentDomain ->template_style_id ) {
				JRequest::setVar( 'templateStyle', $currentDomain ->template_style_id );
			}			
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


			
		// Standard Domain uses Joomla settings
		if($curDomain->home == 1) {
			$curDomain->template = null;
			$curDomain->menuid = null;
		}

		$this->_checkHome($curDomain);

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

		$instance = $curDomain;
			
		return $instance;
	}


	private function getDefaultmenu() {
		static $_defaultmenu;
		if(!empty($_defaultmenu)) return $_defaultmenu;
		$menu = & JMenu::getInstance('site',array());
		$_defaultmenu = $menu->getDefault();
		$db = JFactory::getDbo();
		//fallback
			
		if($_defaultmenu === 0) {
			$lang = JFactory::getLanguage();
			$query  = "SELECT * FROM #__menu WHERE home = 1 AND language = ".$db->Quote(JFactory::getLanguage()->getTag())." AND published >0";
			$db->setQuery($query);
			$_defaultmenu = $db->loadObject();
			if($_defaultmenu === null) {
				$query  = "SELECT * FROM #__menu WHERE home = 1 AND language = '*' AND published >0";
				$db->setQuery($query);
				$_defaultmenu = $db->loadObject();
			}
		}

		return $_defaultmenu;

	}

	/**
	 *
	 * Method to check, if current menu item is the domains home
	 * @param object $curDomain
	 */
	private function _checkHome(&$curDomain) {

		$menu = & JMenu::getInstance('site',array());
			

		$menuItem = & $menu->getItem(( int ) $curDomain->menuid );
			
		$app = JFactory::getApplication();
			
		$router = $app->getRouter();
			
		$uri = JURI::getInstance();
		
		$mode_sef 	= ($router->getMode() == JROUTER_MODE_SEF) ? true : false;

		$origHome = $this->getDefaultmenu();
			
		$curDomain->isHome = false;
			
		$curDomain->query_link = $router->parse( clone ( $uri ) );


		$curDomain->activeItemId  = ( int )$curDomain->query_link['Itemid'];
			
		//do nothing, if we are not on frontpage
		if (  ( int )$curDomain->query_link['Itemid'] === ( int )$origHome->id  )
		{
			$curDomain->isHome = true;
		}

		//its clear: we are not at home
		if(!$curDomain->isHome) return;

		if($mode_sef) {
			$route	= $uri->getPath();
			$route_lowercase = JString::strtolower($route);

			// Handle an empty URL (special case)
			if (empty($route)) {
				$curDomain->isHome = false;
			} elseif($route_lowercase === '/') {
				$curDomain->isHome = true;
			} else {
				$items = array_reverse($menu->getMenu());
		
				$found = false;
					
				foreach ($items as $item) {
					$length = strlen($item->route); //get the length of the route

					if ($length > 0 && JString::strpos($route_lowercase.'/', $item->route.'/') === 0 && $item->type != 'menulink') {
						$route = substr($route, $length);
						if ($route) {
							$route = substr($route, 1);
						}
						$found = true;
						break;
					}
				}

				//this is the case, if active menu item has changed before
				if(!$found) {
					$curDomain->isHome = false;
					return;
				}
			}
		}
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


		$rewrite = (str_replace('/','',$_SERVER['REQUEST_URI'] ) == '');


		$origHome = $this->getDefaultmenu();


		if ( !$menuItem )
		{
			//item is lost
			return false;
		}

		$menulink = $menuItem->link;


		$this->_switchMenu( $menu,$menuItem );


		//do nothing, if we are not on frontpage
		if ( !$curDomain->isHome )
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

		$parse = parse_url( $this->_getBase() . $link );

		//Build the new Query
		if($rewrite) {
			$request = array();
			parse_str( $parse['query'], $request );

			$this->_request = array_merge( $request, $this->_request );
			$parse['query'] = JURI::buildQuery( $this->_request );
				
			//rewrite some server environment vars to fool joomla
			$_SERVER['QUERY_STRING'] = $parse['query'];
		}
		$_SERVER['REQUEST_URI'] = $this->_getBase() . $link;
		$_SERVER['PHP_SELF'] = $this->_getBase() . $parse['path'];
		$_SERVER['SCRIPT_NAME'] = $this->_getBase() . $parse['path'];
		$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . DS . preg_replace( '#[/\\\\]+#', DS, $parse['path'] );

		//set userdefined actions
		$this->setActions( 1 );
		//return true;
		//var_dump($this->_request);
		if(count($this->_request)) {
			foreach( $this->_request as $key=>$var) {
				JRequest::setVar($key,$var,'get');
				JRequest::setVar($key,$var,'post');
			}
		}

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
		if($nohome !== 0)
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

	public static function explodeQuery($sUrl) {
		$aUrl = parse_url($sUrl);
		$aUrl['query_params'] = array();
		$aPairs = explode('&', $aUrl['query']);
		//DU::show($aPairs);
		foreach($aPairs as $sPair) {
			if (trim($sPair) == '') {
				continue;
			}
			list($sKey, $sValue) = explode('=', $sPair);
			$aUrl['query_params'][$sKey] = urldecode($sValue);
		}
		return $aUrl;
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