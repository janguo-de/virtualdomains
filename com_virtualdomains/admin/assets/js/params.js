 /*
 * @date		$Date$
 * @revision    $Rev$
 * @id 			$Id$
 * @version		romacron com_virtualdomains $
 * @package		com_virtualdomains Webmaster
 * @copyright	Copyright © 2010 - All rights reserved.
 * @author		romacron
 * @authorMail	info@romacron.de
 * @website		http://www.romacron.de
 *
 * @description Administrator Component Configuration
 * Helper to set and remove key-pattern fields
 * 
 */
var vd = {
    add_key : function(args){
        var    objFRM = window.parent[0].document;
        var newP = objFRM.createElement("p");
        var inp = objFRM.createElement("input");
        inp.setAttribute('value', '')    ;
        inp.setAttribute('name', 'params[costomParameterKey][]')    ;

        newP.appendChild(inp);

        var rPattern =       objFRM.getElementById('remove_pattern').innerHTML;
        newP.innerHTML =  newP.innerHTML + rPattern

        var paramsBlock = objFRM.getElementById('existing_params');
        paramsBlock.appendChild(newP);
    }
    , remove_key : function(args){
        n =  args.parentNode;
        n.innerHTML = "";

    }
}
