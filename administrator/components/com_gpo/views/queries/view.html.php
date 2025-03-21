<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );


class GpoViewQueries extends JViewLegacy
{

	function display($tpl = null)
	 {
		GpoHelper::addSubmenu('queries');
        if (JVERSION >= '3.0')
        {
            //$this->sidebar = JHtmlSidebar::render();
        }
		parent::display( $tpl );
	 }
	

	
	function checklist()
	{
		JToolBarHelper::title( 'Cleanup Queries', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Cleanup Queries' ));
		
		$tpl='checklist';
		$this->display($tpl);		
	}
        
        function dateupdate()
	{
               
		JToolBarHelper::title( 'PublishDateUpdate', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'PublishDateUpdate' ));
                
		$tpl='dateupdate';
		$this->display($tpl);		
	}
}