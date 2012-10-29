<?php /**
 * @version		$Id:virtualdomain.php 1 2010-09-24 23:14:34Z  $
 * @package		Virtualdomain
 * @subpackage 	Tables
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

        $db = &JFactory::getDBO();
        $uri = &JFactory::getURI();
        $user = &JFactory::getUser();
        $document = JFactory::getDocument();
        $form = $this->get( 'Form' );

        $lists = array();

        $editor = &JFactory::getEditor();

        //get the item
        $item = &$this->get( 'item' );
        
        if(!version_compare(JVERSION,'3.0','lt')) {
        	$form->bind(JArrayHelper::fromObject($item));
        } else {
        	$form->bind($item);
        }      

        $isNew = ( $item->id < 1 );

        // Edit or Create?
        if ( $isNew )
        {
            // initialise new record
            $item->published = 1;
        }

        $lists['published'] = JHTML::_( 'select.booleanlist', 'published', 'class="inputbox"', $item->published );

        $paramFields = $this->get( 'ParamFields' );
        $this->assign( 'form', $form );

        $code = $this->_getJs();
        
        $document->addScriptDeclaration($code); 
        
        $this->assignRef( 'lists', $lists );
        $this->assignRef( 'editor', $editor );
        $this->assignRef( 'item', $item );
        $this->assignRef( 'paramFields', $paramFields );
        $this->assignRef( 'isNew', $isNew );
        parent::display( $tpl );
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