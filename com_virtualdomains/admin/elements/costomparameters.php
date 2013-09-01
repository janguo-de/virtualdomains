<?php /**
 * @date		$Date$
 * @revision    $Rev$
 * @id 			$Id$
 * @version		romacron com_virtualdomains $
 * @package		com_virtualdomains Webmaster
 * @copyright	Copyright © 2010 - All rights reserved.
 * @author		romacron
 * @license		GNU/GPL, see LICENSE.php
 * @authorMail	info@romacron.de
 * @website		http://www.romacron.de
 *
 * @description Shows The Form with Costom Parameter KEYs
 * This is the KEY Pattern
 * The settet Keys can be used at every VD form.
 * Note: only possible Keys will be set
 * 
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JElementCostomparameters extends JElement
{
    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    var $_name = 'Costomparameters';

    function fetchElement( $name, $value, &$node, $control_name )
    {
        $doc = &JFactory::getDocument();

        $doc->addScript( JURI::base() . 'components/com_virtualdomains/assets/js/params.js' );

        $cParams = &JComponentHelper::getParams( 'com_virtualdomains' );

        $k = $cParams->get( 'costomParameterKey' );

        $paramsHtml = "<div id=\"existing_params\">";
        if ( $k )
        {
            if ( !is_array( $k ) )
            {
                $paramsHtml .= '<p><input name="params[costomParameterKey][]" value="' . $k . '" type="text">
                <a href="javascript:void(null);" title="' . JText::_( 'REMOVE' ) . '" onclick="vd.remove_key(this);"><img src="images/publish_x.png"/></a></p>';
            } else
            {
                foreach ( $k as $key )
                {
                    $paramsHtml .= '<p><input name="params[costomParameterKey][]" value="' . $key . '" type="text">
                <a href="javascript:void(null);" title="' . JText::_( 'REMOVE' ) . '" onclick="vd.remove_key(this);"><img src="images/publish_x.png"/></a></p>';
                }
            }
        }
        $paramsHtml .= "</div>";

        $paramsHtml .= "<a href=\"javascript:void(null);\" onclick=\"vd.add_key();\">" . JText::_( 'ADD PARAMS FIELD' ) . "</a>";
        $paramsHtml .= '<p id="vd_key_pattern">
              <div style="display:none" id="remove_pattern"><a href="javascript:void(null);" title="' . JText::_( 'REMOVE' ) .
            '" onclick="vd.remove_key(this);">
        <img src="images/publish_x.png"/>
        </a></div>';
        return $paramsHtml;

    }

} ?>
