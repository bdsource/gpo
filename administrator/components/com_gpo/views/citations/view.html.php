<?php
/**
 * Hello View for Hello World Component
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://dev.joomla.org/component/option,com_jd-wiki/Itemid,31/id,tutorials:components/
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );


class GpoViewCitations extends JViewLegacy
{
	function start()
	{
		JToolBarHelper::title(   JText::_( 'Gun Policy Manager - Citations' ), 'generic.png' );
		$tpl='splash';
		parent::display($tpl);		
	}
}

