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

/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class GpoViewSponsors extends JViewLegacy
{

		function display($tpl = null)
	{
		GpoHelper::addSubmenu('sponsors');
        if (JVERSION >= '3.0')
        {
            //$this->sidebar = JHtmlSidebar::render();
        }
		parent::display( $tpl );
	}
	
	function edit()
	{
		$sponsors =& $this->sponsors;

		$isNew		= ($sponsors->id < 1);

		$text = $isNew ? JText::_( 'Create New' ) : JText::_( 'Edit' );
		$text = JText::_( $text );
		JToolBarHelper::title(   JText::_( 'Sponsors' ).': <small><small>[ ' . $text.' ]</small></small>' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Sponsors' ).': [ ' . $text.' ]' );
                
		JToolBarHelper::save();
		if( $isNew )
		{
			JToolBarHelper::cancel();
		}else{
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		$tpl = 'edit';
		//$this->assignRef('sponsors',$sponsors);
		$this->sponsors = &$sponsors;

		$this->display($tpl);
	}



	function showall()
	{
		JToolBarHelper::title(   JText::_( 'Sponsors' ), 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Sponsors' ) );
                
		JToolBarHelper::addNew();
		JToolBarHelper::spacer('10');
		JToolBarHelper::deleteList();

		$tpl='showall';
		$this->display($tpl);
	}
}

