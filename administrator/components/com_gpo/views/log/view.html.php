<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class GpoViewLog extends JViewLegacy
{
	function display($tpl = null)
	{
		GpoHelper::addSubmenu('logs');
        if (JVERSION >= '3.0')
        {
            //$this->sidebar = JHtmlSidebar::render();
        }
		parent::display( $tpl );
	}
	
	function showLast()
	{
		JToolBarHelper::title(   JText::_( 'Back-end Admin Members Activity Log' ), 'generic.png' );
		JToolBarHelper::spacer('10');
		$tpl='logs';
		$this->display($tpl);
	}
}

