<?php /**
 * @version		$virtualdomain.php
 * @package		Virtualdomain
 * @subpackage 	Controllers
 * @copyright	Copyright (C) 2010, . All rights reserved.
 * @license #
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/**
 * VirtualdomainVirtualdomain Controller
 *
 * @package    Virtualdomain
 * @subpackage Controllers
 */
class VirtualdomainsControllerVirtualdomain extends VirtualdomainsController
{
    /**
     * Constructor
     */
    protected $_viewname = 'virtualdomain';

    public function __construct( $config = array() )
    {
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

        $this->setRedirect( 'index.php?option=com_virtualdomains&view=virtualdomain' );

        $model = $this->getModel( 'virtualdomain' );

        $model->checkin();
    }

    /**
     * VirtualdomainsControllerVirtualdomain::edit()
     * Edits an existing VirtualDomain
     * @return
     */
    public function edit()
    {
        $document = &JFactory::getDocument();

        $viewType = $document->getType();
        $viewType = $document->getType();
        $viewName = JRequest::getCmd( 'view', $this->_viewname );

        $view = &$this->getView( $viewName, $viewType );
        $view->setLayout( 'form' );
        $cid = JRequest::getVar( 'cid', array( 0 ), 'get', 'array' );
        $id = $cid[0];
        
        $model = &$this->getModel( $this->_viewname );
        
        if ( $id > 0 )
        {

            $item = $model->getItem();
            // If not already checked out, do so.

            if ( $item->checked_out == 0 )
            {

                if ( !$model->checkout() )
                {
                    // Check-out failed, go back to the list and display a notice.
                    $message = JText::sprintf( 'JError_Checkout_failed', $model->getError() );
                    $this->setRedirect( 'index.php?option=com_virtualdomains&view=virtualdomain', $message, 'error' );
                    return false;
                }
            }
        }

        JRequest::setVar( 'hidemainmenu', 1 );
        JRequest::setVar( 'layout', 'form' );
        JRequest::setVar( 'view', $this->_viewname );
        JRequest::setVar( 'edit', true );

        $view->setModel( $model, true );
        $view->display();
    }

    /**
     * stores the item
     */
    public function save()
    {
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );
        $app = JFactory::getApplication();
        $db = &JFactory::getDBO();

        $post = JRequest::getVar( 'jform', array(), 'post', 'array' );
        $cid = JRequest::getVar( 'cid', array( 0 ), 'post', 'array' );
        $post['id'] = ( int )$cid[0];
        $model = $this->getModel( 'virtualdomain' );
        $form = $model->getForm();
        if ( !$form )
        {
            JError::raiseError( 500, $model->getError() );
            return false;
        }
        $data = $model->validate( $form, $post );

        // Check for validation errors.
        if ( $data === false )
        {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ( $i = 0, $n = count( $errors ); $i < $n && $i < 3; $i++ )
            {
                if ( JError::isError( $errors[$i] ) )
                {
                    $app->enqueueMessage( $errors[$i]->getMessage(), 'notice' );
                } else
                {
                    $app->enqueueMessage( $errors[$i], 'notice' );
                }
            }

            if ( $model->getId() )
            {
                $link = 'index.php?option=com_virtualdomains&view=virtualdomain.&task=edit&cid[]=' . $model->getId();
            } else
            {
                $link = 'index.php?option=com_virtualdomains&view=virtualdomain.&task=edit';
            }
            // Redirect back to the edit screen.

            $this->setRedirect( $link, $msg );
            return;
        }
        if ( $model->store( $post ) )
        {
            $msg = JText::_( $this->_itemname . ' Saved' );
            $model->checkin();
        } else
        {
            $msg = $model->getError();
        }

        switch ( $this->getTask() )
        {
            case 'apply':
                $link = 'index.php?option=com_virtualdomains&view=virtualdomain.&task=edit&cid[]=' . $model->getId();
                break;

            case 'save':
            default:
                $link = 'index.php?option=com_virtualdomains&view=virtualdomain';
                break;
        }

        $this->setRedirect( $link, $msg );
    }
    public function publish()
    {
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger( $cid );

        if ( count( $cid ) < 1 )
        {
            JError::raiseError( 500, JText::_( 'Select an item to publish' ) );
        }

        $model = $this->getModel( 'virtualdomain' );
        if ( !$model->publish( $cid, 1 ) )
        {
            echo "<script> alert('" . $model->getError( true ) . "'); window.history.go(-1); </script>\n";
        }

        $this->setRedirect( 'index.php?option=com_virtualdomains&view=virtualdomain' );
    }

    
    public function setDefault() {
    			// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$pks = JRequest::getVar('cid', array(), 'post', 'array');
    	try
		{
			if (empty($pks)) {
				throw new Exception(JText::_('NO_DOMAIN_SELECTED'));
			}

			JArrayHelper::toInteger($pks);

			// Pop off the first element.
			$id = array_shift($pks);
			$model =  $this->getModel( 'virtualdomain' );
			$model->setDefault($id);
			$this->setMessage(JText::_('SUCCESS_DEFAULT_SET'));

		}
		catch (Exception $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		$this->setRedirect('index.php?option=com_virtualdomains&view=virtualdomain');
	}
    
    
    public function unpublish()
    {
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger( $cid );

        if ( count( $cid ) < 1 )
        {
            JError::raiseError( 500, JText::_( 'Select an item to unpublish' ) );
        }

        $model = $this->getModel( 'virtualdomain' );
        if ( !$model->publish( $cid, 0 ) )
        {
            echo "<script> alert('" . $model->getError( true ) . "'); window.history.go(-1); </script>\n";
        }

        $this->setRedirect( 'index.php?option=com_virtualdomains&view=' . $this->_viewname );
    }
    public function orderup()
    {
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $model = $this->getModel( 'virtualdomain' );
        $model->move( -1 );

        $this->setRedirect( 'index.php?option=com_virtualdomains&view=' . $this->_viewname );
    }

    public function orderdown()
    {
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $model = $this->getModel( 'virtualdomain' );
        $model->move( 1 );

        $this->setRedirect( 'index.php?option=com_virtualdomains&view=' . $this->_viewname );
    }

    public function saveorder()
    {
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $order = JRequest::getVar( 'order', array(), 'post', 'array' );
        JArrayHelper::toInteger( $cid );
        JArrayHelper::toInteger( $order );

        $model = $this->getModel( 'virtualdomain' );
        $model->saveorder( $cid, $order );

        $msg = JText::_( 'New ordering saved' );
        $this->setRedirect( 'index.php?option=com_virtualdomains&view=' . $this->_viewname, $msg );
    }
} // class
 ?>