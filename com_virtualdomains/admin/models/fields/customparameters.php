<?php /**
 * @date		$Date: 2011-01-09 14:15:41 +0100 (So, 09 Jan 2011) $
 * @revision    $Rev: 22 $
 * @id 			$Id: costomparameters.php 22 2011-01-09 13:15:41Z romacron $
 * @version		romacron com_virtualdomains $
 * @package		com_virtualdomains Webmaster
 * @copyright	Copyright © 2010 - All rights reserved.
 * @author		romacron
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

class JFormFieldCustomparameters extends JFormField
{
    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    public $type = 'Customparameters';

	protected function getInput()
    {
            $doc = &JFactory::getDocument();

        $doc->addScript( JURI::base() . 'components/com_virtualdomains/assets/js/params16.js' );

        $cParams = &JComponentHelper::getParams( 'com_virtualdomains' );

        $k = $cParams->get( 'costomParameterKey' );

        $paramsHtml = "<div style='clear:both'  id=\"existing_params\">";
        if ( $k )
        {
            if ( !is_array( $k ) )
            {
                $paramsHtml .= '<fieldset class="text"><input class="keyname" name="jform[costomParameterKey][]" value="' . $k . '" type="text">
                <a href="javascript:void(null);" title="' . JText::_( 'REMOVE' ) . '" onclick="vd.remove_key(this);"><img src="images/publish_x.png"/></a></fieldset>';
            } else
            {
                foreach ( $k as $key )
                {
                    $paramsHtml .= '<fieldset class="text"><input name="jform[costomParameterKey][]" value="' . $key . '" type="text">
                <a href="javascript:void(null);" title="' . JText::_( 'REMOVE' ) . '" onclick="vd.remove_key(this);"><img src="images/publish_x.png"/></a></fieldset>';
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
