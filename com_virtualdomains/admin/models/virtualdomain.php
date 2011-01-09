  <?php /**
 * @version		$Id:virtualdomain.php  1 2010-10-23 15:29:07Z  $
 * @package		Virtualdomains
 * @subpackage 	Models
 * @copyright	Copyright (C) 2010, . All rights reserved.
 * @license #
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * VirtualdomainsModelVirtualdomain 
 * @author 
 */

class VirtualdomainsModelVirtualdomain extends VirtualdomainsModel
{

    protected $_default_filter = 'a.domain';

    /**
     * Constructor
     */

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Method to build the query
     *
     * @access private
     * @return string query	
     */

    protected function _buildQuery()
    {
        return parent::_buildQuery();
    }

    /**
     * Method to store the Item
     *
     * @access	public
     * @return	boolean	True on success
     */
    public function store( $data )
    {
        $row = &$this->getTable();
        /**
         * Example: get text from editor 
         * $Text  = JRequest::getVar( 'text', '', 'post', 'string', JREQUEST_ALLOWRAW );
         */

        // Bind the form fields to the table
        if ( !$row->bind( $data ) )
        {
            $this->setError( $this->_db->getErrorMsg() );
            return false;
        }

        // Make sure the table is valid
        if ( !$row->check() )
        {
            $this->setError( $this->_db->getErrorMsg() );
            return false;
        }

        /**
         * Clean text for xhtml transitional compliance
         * $row->text		= str_replace( '<br>', '<br />', $Text );
         */

        // Store the table to the database
        if ( !$row->store() )
        {
            $this->setError( $this->_db->getErrorMsg() );
            return false;
        }
        $this->setId( $row->{$row->getKeyName()} );
        return $row->{$row->getKeyName()};
    }

    /**
     * @notice für welche !J version ist dieser Part?
     * 
     * VirtualdomainsModelVirtualdomain::getParamFields()
     * 
     * @return
     */
    public function getParamFields()
    {
        $item = $this->getItem();

        $returnParametersObject = array();

        /*saved Parameters for the curren VirtualDomain*/
        $params = ( array )$item->params;

        $keys = $this->loadComponentKeyPattern();
        /*
        * keys from pattern will be removed from params
        * to find paramters in this VD config wich have no key in KeyPatterns)
        */
        $tmpParams = $params;
        /*sync key patterns*/
        $i = 0;
        if ( !is_array( $keys ) && !empty( $keys ) )
        {
            /*only one key was predefined*/
            $returnParametersObject[$i]->name = $keys;
            if ( $params[$keys] )
            {
                $returnParametersObject[$i]->value = $params[$keys];
                /* memorice that we have settet an valid keyPattern with value*/

            }

        } elseif ( is_array( $keys ) && !empty( $keys ) )
        {

            foreach ( $keys as $key )
            {
                $returnParametersObject[$i]->name = $key;
                unset( $tmpParams[$key] );
                /*key pattern has value*/
                if ( $params[$key] )
                {
                    $returnParametersObject[$i]->value = $params[$key];
                    /* memorice that we have settet an valid keyPattern with value*/

                }
                $i++;
            }
            /* some keys was predefined*/
        } else
        {
            /*no key was predefinded*/
            return null;
        }

        /* show parameters they are not defined in Key pattern(Perhaps the key was deleted in pattern and now he is swimming here)*/

        return $returnParametersObject;

    }

    /**
     * VirtualdomainsModelVirtualdomain::loadComponentKeyPattern()
     * Loads the Component predifined keys
     * @return string or array
     */
    function loadComponentKeyPattern()
    {
        $cParams = &JComponentHelper::getParams( 'com_virtualdomains' );

        return $k = $cParams->get( 'costomParameterKey' );
    }

    /**
     * Method to build the Order Clause
     *
     * @access private
     * @return string orderby	
     */

    protected function _buildContentOrderBy()
    {
        $app = &JFactory::getApplication( '' );
        $context = $this->option . '.' . strtolower( $this->getName() ) . '.list.';
        $filter_order = $app->getUserStateFromRequest( $context . 'filter_order', 'filter_order', $this->getDefaultFilter(), 'cmd' );
        $filter_order_Dir = $app->getUserStateFromRequest( $context . 'filter_order_Dir', 'filter_order_Dir', '', 'word' );
        $this->_query->order( $filter_order . ' ' . $filter_order_Dir );
    }

    /**
     * Method to build the Where Clause 
     *
     * @access private
     * @return string orderby	
     */

    protected function _buildContentWhere()
    {

        $app = &JFactory::getApplication( '' );
        $context = $this->option . '.' . strtolower( $this->getName() ) . '.list.';
        $filter_state = $app->getUserStateFromRequest( $context . 'filter_state', 'filter_state', '', 'word' );
        $filter_order = $app->getUserStateFromRequest( $context . 'filter_order', 'filter_order', $this->getDefaultFilter(), 'cmd' );
        $filter_order_Dir = $app->getUserStateFromRequest( $context . 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $search = $app->getUserStateFromRequest( $context . 'search', 'search', '', 'string' );

        if ( $search )
        {
            $this->_query->where( 'LOWER(a.domain) LIKE ' . $this->_db->Quote( '%' . $search . '%' ) );
        }
        if ( $filter_state )
        {
            if ( $filter_state == 'P' )
            {
                $this->_query->where( "a.published = 1" );
            } elseif ( $filter_state == 'U' )
            {
                $this->_query->where( "a.published = 0" );
            } else
            {
                $this->_query->where( "a.published > -2" );
            }
        }
    }

} ?>