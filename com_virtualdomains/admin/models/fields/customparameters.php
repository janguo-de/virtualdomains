<?php 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* @date		$Date: 2011-01-09 14:15:41 +0100 (So, 09 Jan 2011) $
* @revision    $Rev: 22 $
* @id 			$Id: costomparameters.php 22 2011-01-09 13:15:41Z romacron $
* @version		$Id$
* @package		Virtualdomain
* @subpackage 	Controllers
* @copyright	Copyright (C) 2010, . All rights reserved.
* @author     	Michael Liebler {@link http://www.janguo.de}
* @copyright	Copyright (C) 2008 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Virtualdomains is free software. This version may have been modified pursuant to the
* GNU General Public License, and as distributed it includes or is derivative
* of works licensed under the GNU General Public License or other free or open
* source software licenses. See COPYRIGHT.php for copyright notices and
* details.
* @description Shows The Form with Costom Parameter KEYs
* This is the KEY Pattern
* The settet Keys can be used at every VD form.
* Note: only possible Keys will be set
* 
*/

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

        /**
         * file doesn't exists 
         * @notice  js-pattern will get from 1.5. params.js (if its possible)
         */
        $doc->addScript( JURI::base() . 'components/com_virtualdomains/assets/js/params16.js' );

        $cParams = &JComponentHelper::getParams( 'com_virtualdomains' );

        $k = $cParams->get( 'costomParameterKey' );

        $paramsHtml = "<div style='clear:both' id=\"existing_params\">";
        if ( $k )
        {

            if ( !is_array( $k ) )
            {
                $paramsHtml .= '<fieldset class="text"><input class="keyname" name="jform[costomParameterKey][]" value="' . $k . '" type="text">
                <a class="jgrid"  href="javascript:void(null);" title="' . JText::_( 'REMOVE' ) .
                    '" onclick="vd.remove_key(this);"><span class="state unpublish"/><span class="text">' . JText::_( 'REMOVE' ) .
                    '</span></span></a></fieldset>';

            } else
            {

                foreach ( $k as $key )
                {
                    $paramsHtml .= '<fieldset class="text"><input name="jform[costomParameterKey][]" value="' . $key . '" type="text">
                <a class="jgrid"  href="javascript:void(null);" title="' . JText::_( 'REMOVE' ) .
                        '" onclick="vd.remove_key(this);"><span class="state unpublish"/><span class="text">' . JText::_( 'REMOVE' ) .
                        '</span></span></a></fieldset>';
                }
            }
        }
        $paramsHtml .= "</div>";

        $paramsHtml .= "<a href=\"javascript:void(null);\" onclick=\"vd.add_key();\">" . JText::_( 'ADD PARAMS FIELD' ) . "</a>";
        $paramsHtml .= '<p id="vd_key_pattern">
              <div style="display:none" id="remove_pattern"><a class="jgrid"  href="javascript:void(null);" title="' . JText::_( 'REMOVE' ) .
            '" onclick="vd.remove_key(this);"><span class="state unpublish"/><span class="text">' . JText::_( 'REMOVE' ) . '</span></span></a></div>';

        return $paramsHtml;
    }

} ?>
