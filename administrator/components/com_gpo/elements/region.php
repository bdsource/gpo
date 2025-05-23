<?php
/**
* @version		$Id: region.php,v 1.1.1.1 2010/03/11 15:00:36 cwill4521 Exp $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JElementRegion extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Region';

	function fetchElement($name, $value, &$node, $control_name)
	{
		//global $mainframe;
    $mainframe = JFactory::getApplication();
    
		$db			= JFactory::getDBO();
		$doc 		= JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name.'['.$name.']';
//		$article =& JTable::getInstance('content');
//		if ($value) {
//			$article->load($value);
//		} else {
//			$article->title = JText::_('Select an Article');
//		}

		$query		= '
SELECT `cat`.`id`,`cat`.`title` as `name`
FROM `#__gpo_location` as `lo`
INNER JOIN `#__categories` as `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`)
ORDER BY `lo`.`name`
';
		$db->setQuery( $query );
		$items = $db->loadObjectList();
				
		$js = "
		function jSelectRegion( el, object) {
			document.getElementById(object + '_id').value = el[el.selectedIndex].value;
		}";
		$doc->addScriptDeclaration($js);

		$selectedId = $value;
		$html = JHTML::_('Select.genericlist',$items,"regions",'onchange="window.parent.jSelectRegion( this, \'' . $name . '\' );"',"id","name",$selectedId);	
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';
		return $html;
	}
}
