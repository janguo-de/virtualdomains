<?php /**
* @version		$Id: virtualdomains.php 13 2013-03-30 00:14:57Z michel $
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
	private $_hostparams = null;
	private $_curhost = array();
	private $input = null;



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
		$this->input = JFactory::getApplication()->input;
		parent::__construct( $subject, $config );
	}

	/**
	 *  onAfterInitialise
	 */
	public function onAfterInitialise()
	{
		jimport('joomla.filesystem.folder');
		if(!JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_virtualdomains')) {
			return false;
		}
		jimport('joomla.user.authentication');
		if(version_compare(JVERSION, '3.2', 'lt')) {
			jimport('joomla.application.router');
		}

		$app 	= JFactory::getApplication();
		$db 	= JFactory::getDBO();
		$user 	= JFactory::getUser();
		$conf 	= JComponentHelper::getParams('com_virtualdomains');
		$uri = JUri::getInstance();
		
		// we just have to give full access for all users in backend - nothing else has to be done
		if ( $app->isAdmin() )
		{

			if(!$conf->get('denyadminaccess', 0)) {
				//Full access for all users in backend
				$this->fullAccess();
			}
			return; // Dont do anymore in backend
		}
		
		// this is done when the frontend is called by the ajax check function from VD backend
		if($this->input->get('option') == 'com_virtualdomains') {
			$this->hostCheck();
		}

		// strip the www from hostname
		$this->_curhost = str_replace( 'www.', '', $uri->getHost() );

		// Cachebuster - special param for apps registeredurlparams 
		// prevents getting wrong things from cache store  
		if (!empty($app->registeredurlparams))
		{
			$registeredurlparams = $app->registeredurlparams;
		}
		else
		{
			$registeredurlparams = new stdClass;
		}

		$registeredurlparams->vdcachbuster = 'WORD';
		$app->registeredurlparams = $registeredurlparams;
		$this->input->set('vdcachbuster',$this->_curhost);

		// get current domains settings 
		$currentDomain = $this->getCurrentDomain();

		//let joomla do its work, if the domain is not managed by VD
		if ($currentDomain === null) return;

		// set the vd id to users session
		$user->set('virtualdomain_id', $currentDomain->id);
		// add viewlevel(s) for the current domain to the user object
		$vdUser = new vdUser($user->get('id'));
		// there may be viewlevels inherited from other domains 
		$viewlevels =  (array) $currentDomain ->params->get('access');
		// add current domains viewlevel
		$viewlevels[] = $currentDomain ->viewlevel;
		// override method addAuthorisedViewLevels 
		vdJAccess::addAuthorisedViewLevels($user->get('id'), $viewlevels);
		// add viewlevels to the user object
		$vdUser->addAuthLevel($viewlevels);


		// Get the params
		$this->_hostparams = $currentDomain ->params;

		// legacy global var for the developers needs
		if(isset($currentDomain->Team_ID) && $currentDomain->Team_ID) $GLOBALS['Team_ID'] = $currentDomain->Team_ID;

		// override the original config with domain specific settings
		$this->setConfig();

		//Set the route, if necessary
		if ( !$this->reRoute( $currentDomain , $uri ) )
		{
			$this->setActions();
		}
    
	    // check if admin denied access to some components	
		$this->checkComponent();
		
		// set default languaeg if required
		$this->setLangVars();

		//set the template
		if ( $currentDomain->template )
		{
			$this->addRequest('template' , $currentDomain ->template );
				
			if( $currentDomain ->template_style_id ) {
				$this->addRequest( 'templateStyle' , $currentDomain ->template_style_id);
			}
		}
		
		// filter menues if required
		$this->filterMenus($currentDomain ->menuid);

		//set all requests
		$this->setRequests();
	}

	/**
	 *
	 * Method to add or set a var to the request
	 */
	private function addRequest( $var, $value )
	{
		$this->_request[$var] = $value;
	}	

	/**
	 * Check if a component is denied for current domain
	 */
	private function checkComponent() {
	
		// get denied components
		$componentsDenied = (array) $this->_hostparams->get('components');
		if(!count($componentsDenied)) return;
	
		$input = JFactory::getApplication()->input;
		$option = false;
	
		//try to get current component from input
		if (!($option = $input->get('option'))) {
	
			//try to get current component from mene
			$menu = JMenu::getInstance('site',array());
			$active = $menu->getActive();
			if ($active && $active->type == 'component') {
				$option = $active->component;
			}
		}
	
		// check if component is denied
		if($option && in_array($option, $componentsDenied)) {
			JFactory::getLanguage()->load('lib_joomla');
			if (class_exists('Exception')) {
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'), 404);
			} else {
				JError::raiseError(404, JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
			}
		}
	}
	
	/**
	 *
	 * Method to check, if current menu item is the domains home
	 * @param object $curDomain
	 */
	private function checkHome(&$curDomain) {
	
		$menu = JMenu::getInstance('site',array());
		$menuItem = $menu->getItem(( int ) $curDomain->menuid );
			
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
	
		// may be we are routed to a component by a form
		$option = $app->input->get('option');
		if($option && ($menuItem->component != $option )) {
			$curDomain->isHome = false;
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
	 * Method to hide/show Menu items and translate home item
	 *
	 * @param int $default - Current domains home menu item
	 */
	private function filterMenus($default)
	{
	
		$menu = new vdMenuFilter();
		$menu->filterMenues($this->_hostparams, $default);
	}
	
	/**
	 * Gives the user access on all domains - used for the backend
	 */
	private function fullAccess() {

		// Needed for searching articles on backend,
		// thanks to Javi
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		
		// get VD domains from db
		$db->setQuery("SELECT * FROM #__virtualdomain WHERE published > 0");		
		$allDomains = $db->loadObjectList();

		if ($error = $db->getErrorMsg())
		{
			if(class_exists('Exception')) {
				throw new Exception($error, 500);
			} else {
				JError::raiseWarning(500, $error);
			}
				
			return false;
		}
		
		// current user
		$vdUser = new vdUser($user->get('id'));

		// assign all viewlevels to user session
		foreach($allDomains as $domain) {
			$viewlevels[] = $domain ->viewlevel;
			$vdUser->addAuthLevel($viewlevels);
		}
	}

	/**
	 * Method to get the uri base path
	 * we dont need the $path .= '/';
	 * joomla does this for us
	 */
	private function getBase()
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
	 * Get domains data from database
	 * @return object|NULL <mixed, NULL>
	 */	
	private function getCurrentDomain() {
			
		static $instance;
			
		$vd = JComponentHelper::getComponent('com_virtualdomains');
		$app = JFactory::getApplication();
			
		if(!empty($instance)) return $instance;
			
		$db = JFactory::getDbo();
		$db->setQuery(
				"SELECT * FROM #__virtualdomain
				WHERE `domain` = ".$db->Quote($this->_curhost ). " AND published > 0"
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
	
		$this->checkHome($curDomain);
	
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
	
	/**
	 * Get default home menu item
	 * @return _defaultmenu <object| NULL>
	 */
	private function getDefaultmenu() {
		static $_defaultmenu;
		
		if(!empty($_defaultmenu)) return $_defaultmenu;
		
		$menu = JMenu::getInstance('site',array());
		$_defaultmenu = $menu->getDefault();

		//fallback if default home item was not found 			
		if($_defaultmenu === 0) {
			$lang = JFactory::getLanguage();
			$db = JFactory::getDbo();
			
			// first try to find a language specific home item
			$query  = "SELECT * FROM #__menu WHERE home = 1 AND language = ".$db->Quote(JFactory::getLanguage()->getTag())." AND published >0";
			$db->setQuery($query);
			$_defaultmenu = $db->loadObject();
			
			// language specific home item was not found - get the global one
			if($_defaultmenu === null) {
				$query  = "SELECT * FROM #__menu WHERE home = 1 AND language = '*' AND published >0";
				$db->setQuery($query);
				$_defaultmenu = $db->loadObject();
			}
		}
	
		return $_defaultmenu;	
	}	
	
	/**
	 * Return the host check on backend request
	 */	
	private function hostCheck() {
		$app = JFactory::getApplication();
		// Joomla 3.2 will throw an error, if language filter is set
		if(method_exists($app, 'setLanguageFilter')) {
			$app->setLanguageFilter(false);
		}
		$host = $_SERVER['HTTP_HOST'];
		$data = json_encode(array('hostname'=>$host));
	
		ob_clean();
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('content-type: application/json; charset=utf-8');
		header("access-control-allow-origin: *");
		echo json_encode($data);
		exit;
	}
	
	/**
	 *
	 *  Routes to VD-Hosts home, if necessery
	 */	
	private function reRoute($curDomain, &$uri )
	{
		//Is this the default domain?
		if($curDomain->home == 1) return;
	
		if ( $this->params->get( 'noreroute' ) )
		{
			return false;
		}
	
		// get domains home item
		$menu = JMenu::getInstance('site', array());
		$menuItem = $menu->getItem( ( int ) $curDomain->menuid );
		if ( !$menuItem )
		{
			//item is lost
			return false;
		}

		// if / is called this should be home and we have to rewrite the request uri
		$rewrite = (str_replace('/', '', $_SERVER['REQUEST_URI'] ) == '');

		// the menu link
		$menulink = $menuItem->link;
	
		// change menu properties to point to the new home item
		$this->switchMenu( $menu, $menuItem );
	
		//do nothing else, if we are not on frontpage
		if ( !$curDomain->isHome )
		{
			return false;
		}	
		
		// set domains home item as active item
		$menu->setActive( $curDomain->menuid );
		// push itemid to the request
		$this->addRequest( 'Itemid', $curDomain->menuid );
	
		//rewrite the uri
		$link = $menulink . "&Itemid=" . $curDomain->menuid;
	
		//Parse the new Url	
		$parse = parse_url( $this->getBase() . $link );
	
		//Build the new Query
		if($rewrite) {
			$request = array();
			parse_str( $parse['query'], $request );
	
			$this->_request = array_merge( $request, $this->_request );
			$parse['query'] = JURI::buildQuery( $this->_request );
	
			//rewrite some server environment vars to fool joomla
			$_SERVER['QUERY_STRING'] = $parse['query'];
		}
		$_SERVER['REQUEST_URI'] = $this->getBase() . $link;
		$_SERVER['PHP_SELF'] = $this->getBase() . $parse['path'];
		$_SERVER['SCRIPT_NAME'] = $this->getBase() . $parse['path'];
		$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . '/'. preg_replace( '#[/\\\\]+#', '/', $parse['path'] );
	
		//set userdefined actions
		$this->setActions( 1 );
	
		return true;
	}	

	/**
	 *
	 *  Method to set userdefined params to $REQUEST or $GLOBALS
	 */
	private function setActions( $home = 0 )
	{
		$db = JFactory::getDBO();
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
								$this->addRequest( $result[$i]->name, $value );
								break;
						}
					}
				}
			}
		}
	}

	/**
	 * Change the domains global settings
	 */
	private function setConfig() {
		$config = JFactory::getConfig();
		// options that can be changed
		$options = array('MetaDesc', 'sitename', 'list_limit', 'mailfrom', 'fromname');
	
		if ( is_object( $this->_hostparams ) )
		{
			// keywords is different fro Joomlas var MetaKeys - set it separately
			if ( trim( $this->_hostparams->get( 'keywords' ) ) )
			{
				$config->set( 'MetaKeys', $this->_hostparams->get( 'keywords' ) );
			}
	
			// Now iter over the available options that can be overrided
			foreach($options as $option) {
				if ( trim( $this->_hostparams->get( strtolower($option) ) ) )
				{
					$config->set( $option, $this->_hostparams->get( strtolower($option) ) );
				}					
			}
		}	
	}
	
	/**
	 * Method to set a domain specific default language
	 */
	private function setLangVars()
	{
		// default language is not set

		if ( !$this->_hostparams->get( 'language' ) )
		{
			return;
		}

		$hash = method_exists('JApplicationHelper', 'getHash') ? JApplicationHelper::getHash('language') : JApplication::getHash('language');

		//Joomla Language selection is active?  do nothing
		$joomlacookie = $this->input->cookie->get($hash);
		if($joomlacookie) {
			return;
		}

		// we have to override the joomla method to set the language
		$lang = new vdLanguage();
		$lang_code = $this->_hostparams->get( 'language' );

		$lang->setDefault($this->_hostparams->get( 'language' ));
		$lang = JFactory::getLanguage();
		
		// don't override Joomfish cookie if present
		$jfcookie = $this->input->cookie->get('jfcookie');
		if ( isset( $jfcookie["lang"] ) && $jfcookie["lang"] != "" )
		{
			return;
		}
		
		// set Joomfish cookie 
		$conf = JFactory::getConfig();
		$cookie_domain 	= $conf->get('config.cookie_domain', '');
		$cookie_path 	= $conf->get('config.cookie_path', '/');
		setcookie($hash , $lang_code, time() + 365 * 86400, $cookie_path, $cookie_domain);

		// set the request var Joomla and Joomfish style
		$this->addRequest( 'lang', $lang_code );
		$_POST['lang'] = $lang_code ;
		$this->addRequest( 'language', $lang_code);
	}

	/**
	 * Method to set the requests
	 */
	private function setRequests() {
		if(count($this->_request)) {
			foreach( $this->_request as $key=>$var) {
				// set the request
				$this->input->set($key, $var);
				// legacy method
				if (class_exists('JRequest')) {
					JRequest::setVar($key,$var,'get');
					JRequest::setVar($key,$var,'post');
				}
			}
		}		
	}	
	
	/**
	 *
	 *  Method to switch the menu to the VD-hosts home
	 */
	private function switchMenu( & $menu, &$newhome )
	{
	
		// nohome should be a reference to menu object
		$nohome = $menu->getDefault();
		
		// unset old home item
		if($nohome !== 0) {
			$nohome->home = null;
		}
		
		// set new home item
		$newhome->home = 1;
		$menu->setDefault( $newhome->id);
	}
	
}

/**
 *
 * Dummy JMenu Class
 * @author michel
 */
class vdMenuFilter extends JMenu {
	/**
	 *
	 * Method to Filter Menu Items
	 * @param array $items - Array of menu item id's
	 * @param string $filter - show/hide
	 */

	function filterMenues($params, $default) {
		//Menu filter settings for current domain
		$filter = $params->get( 'menumode' );
		$items = $params->get( 'menufilter' );
		$translatations = $params->get( 'translatemenu' );

		$lang =  JFactory::getLanguage()->getTag() ;

		//Get the instance
		$menu = parent::getInstance('site',array());
			
		//Set all defaults on default
		//TODO: Allow language specific home items
		if($default) {
			$menu->setDefault($default, $lang);
			$menu->setDefault($default,'*');
			$menu->setDefault($default);
		}

		//Check each item
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

/**
 * Dummy JLanguage class
 */
class vdLanguage extends JLanguage {

	public function setDefault($lang) {
		$refresh = JFactory::getLanguage();
		$refresh->metadata['tag'] = $lang;

		$refresh->default	= $lang;
		$new = JFactory::getLanguage();

	}
}

/**
 *
 * Dummy JAccess class
 * Override viewlevels
 * @author michel
 *
 */
class vdJAccess extends JAccess {

	public static function addAuthorisedViewLevels($userId, $viewlevels)
	{


		$guestUsergroup = JComponentHelper::getParams('com_users')->get('guest_usergroup', 1);

		// Get a database object.
		$db = JFactory::getDbo();

		// Build the base query.
		$query = $db->getQuery(true)
		->select('id, rules')
		->from($db->quoteName('#__viewlevels'));

		// Set the query for execution.
		$db->setQuery($query);

		// Build the view levels array.
		foreach ($db->loadAssocList() as $level)
		{
			$rules = (array) json_decode($level['rules']);

			//The magic: guest usergroup must never be configured in database and is now added dynamically
			if(in_array($level['id'], $viewlevels) &! in_array($guestUsergroup, $rules)) {
				$rules[] = $guestUsergroup;
			}
			
			self::$viewLevels[$level['id']] = $rules;

		}
	}
}

/**
 *
 * Dummy User Class
 * Override authlevels
 * @author michel
 *
 */
class vdUser extends JUser {

	function __construct($identifier) {
		parent::__construct($identifier);
	}

	/**
	 *
	 * This method pushs additional auth levels to the user object
	 *
	 * @param array $viewlevels
	 */
	public function addAuthLevel($viewlevels) {
		//No access levels assigned to this domain? return...

		if(!count($viewlevels)) return;
		//is the user not logged in

		$user = JFactory::getUser();

		if(!$this->id) {
			$user->guest = 1;
		}
		
		$user->_authLevels=  $user->getAuthorisedViewLevels();

		//Now add all access levels assigned to this domain
		foreach($viewlevels as $viewlevel) {
			if($viewlevel && !in_array($viewlevel, $user->_authLevels)) {
				$user->_authLevels[] = (int) $viewlevel;
			}
		}
			
		//put this to the session
		$session = JFactory::getSession();
		$session->set('user', $user);
	}
}
