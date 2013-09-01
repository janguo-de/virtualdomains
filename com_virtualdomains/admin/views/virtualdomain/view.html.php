<?php /**
 * @version		$Id:virtualdomain.php 1 2010-09-24 23:14:34Z  $
 * @package		Virtualdomain
 * @subpackage 	Views
 * @copyright	Copyright (C) 2010, . All rights reserved.
 * @license #
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class VirtualdomainsViewVirtualdomain extends JViewLegacy
{

    public function display( $tpl = null )
    {
        $app = &JFactory::getApplication( '' );

        if ( $this->getLayout() == 'form' )
        {

            $this->_displayForm( $tpl );
            return;
        }
        $doc = JFactory::getDocument();
        if(version_compare(JVERSION, '3.0', 'lt')) {
        	$doc->addScript('components/com_virtualdomains/assets/js/jquery.min.js');
        } else {
        	JHtml::_('jquery.framework');
        }
        $doc->addScript('components/com_virtualdomains/assets/js/hostcheck.js');
        $context = 'com_virtualdomains' . '.' . strtolower( $this->getName() ) . '.list.';
        $filter_state = $app->getUserStateFromRequest( $context . 'filter_state', 'filter_state', '', 'word' );
        $filter_order = $app->getUserStateFromRequest( $context . 'filter_order', 'filter_order', $this->get( 'DefaultFilter' ), 'cmd' );
        $filter_order_Dir = $app->getUserStateFromRequest( $context . 'filter_order_Dir', 'filter_order_Dir', '', 'word' );
        $search = $app->getUserStateFromRequest( $context . 'search', 'search', '', 'string' );
        $search = JString::strtolower( $search );

        // Get data from the model
        $items = &$this->get( 'Data' );
        $total = &$this->get( 'Total' );
        $pagination = &$this->get( 'Pagination' );
        
        /*component parameters @romacron*/
        $cParams = &JComponentHelper::getParams( 'com_virtualdomains' );
        $this->assign( 'cParams', $cParams );

        //create the lists
        $lists = array();
        $lists['state'] = JHTML::_( 'grid.state', $filter_state );
        // table ordering
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['order'] = $filter_order;
        // search filter
        $lists['search'] = $search;
        $items = $this->get( 'Data' );
        //pagination
        $pagination = &$this->get( 'Pagination' );

        $this->assignRef( 'user', JFactory::getUser() );
        $this->assign( 'lists', $lists );
        $this->assign( 'items', $items );
        $this->assign( 'total', $total );
        $this->assign( 'pagination', $pagination );
        parent::display();
    }

    /**
     *  Displays the form
     * @param string $tpl   
     */
    public function _displayForm( $tpl )
    {
        global $alt_libdir;

        JLoader::import( 'joomla.form.formvalidator', $alt_libdir );
        JHTML::stylesheet( 'fields.css', 'administrator/components/com_virtualdomains/assets/' );
        if(version_compare(JVERSION, '3', 'lt')) {
        	JHTML::stylesheet( 'bootstrap-forms.css', 'administrator/components/com_virtualdomains/assets/' );
        }
        $db = &JFactory::getDBO();
        $uri = &JFactory::getURI();
        $user = &JFactory::getUser();
        $document = JFactory::getDocument();
        $this->form = $this->get( 'Form' );

        $this->lists = array();


        //get the item
        $this->item = &$this->get( 'item' );
        
        if(!version_compare(JVERSION,'3.0','lt')) {
        	$this->form->bind(JArrayHelper::fromObject($this->item));
        } else {
        	$this->form->bind($this->item);
        }      

        $this->isNew = ( $this->item->id < 1 );

        // Edit or Create?
        if ( $this->isNew )
        {
            // initialise new record
            $this->item->published = 1;
        }
        $this->paramFields = $this->get( 'ParamFields' );
        $this->lists['published'] = JHTML::_( 'select.booleanlist', 'published', 'class="inputbox"', $this->item->published );

        $code = $this->_getJs();
        
        $document->addScriptDeclaration($code); 
		$this->_tabs();
        parent::display( $tpl );
    }
    
    private function _tabs() {
    	$this->tabs = array();
    	if(version_compare(JVERSION,'3.0','lt')) {    		
    		$this->tabsstart = JHtml::_('tabs.start','vd-sliders-'.$this->item->id, array('useCookie'=>1));
    		$this->tabsend = JHtml::_('tabs.end');
    		$this->endtab = "";
    		$this->tabs['details'] = JHtml::_('tabs.panel',JText::_('Details'), 'details');
    		$this->tabs['siteconfig'] = JHtml::_('tabs.panel',JText::_('Main Config'), 'advanced-config');
			$this->tabs['menufilter'] = JHtml::_('tabs.panel',JText::_('Menu_Filter'), 'advanced-menus');
			$this->tabs['accesslevels'] = JHtml::_('tabs.panel',JText::_('Access_Level_Inheritance'), 'advanced-accesslevel');
			$this->tabs['translation'] = JHtml::_('tabs.panel',JText::_('Translation'), 'advanced-translation');			
			$this->tabs['custom-params'] = JHtml::_('tabs.panel',JText::_( 'Custom Parameters' ), 'custom-params');
    		
    	} else {
    		$this->tabsstart = JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details'));
    		$this->tabsend = JHtml::_('bootstrap.endTabSet');
    		$this->endtab = JHtml::_('bootstrap.endTab');
    		$this->tabs['details'] = JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('Details'));
    		$this->tabs['siteconfig'] = JHtml::_('bootstrap.addTab', 'myTab', 'advanced-config', JText::_('Site_Config'));
    		$this->tabs['menufilter'] = JHtml::_('bootstrap.addTab', 'myTab', 'advanced-menus', JText::_('Menu_Filter'));    		
    		$this->tabs['accesslevels'] = JHtml::_('bootstrap.addTab', 'myTab', 'advanced-accesslevel', JText::_('Access_Level_Inheritance'));
    		$this->tabs['translation'] = JHtml::_('bootstrap.addTab', 'myTab', 'advanced-translation', JText::_('Translation'));    		
    		$this->tabs['custom-params'] = JHtml::_('bootstrap.addTab', 'myTab', 'custom-params', JText::_( 'Custom Parameters' ));

    	}
    	
    }
    
    private function _getJs() {
    	$js = "
    		function switchMenuMode () {
     				var form = $('jform_params_menumode');
     				if(form.value == 'show' || form.value == 'hide') {
     					$('jform_params_menufilter').disabled=false;
     				} else {
     					$('jform_params_menufilter').disabled=true;
    				}    		
    		}
    		
    		window.addEvent('domready', function() {
    			switchMenuMode (); 
    			$('jform_params_menumode').addEvent('change',function(){
					switchMenuMode (); 
				});
    		});
    	";
    	return $js;
    }
} ?>