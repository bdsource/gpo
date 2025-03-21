<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );


class GpoViewKeywords extends JViewLegacy
{
		function display($tpl = null)
	{
		GpoHelper::addSubmenu('keywords');
        if (JVERSION >= '3.0')
        {
            //$this->sidebar = JHtmlSidebar::render();
        }
		parent::display( $tpl );
	}
	
	function create_legal_list()
	{
		JToolBarHelper::title(   'Keywords <small><small>[Legal List]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Keywords [Legal List]' ));

		$bar = & JToolBar::getInstance('toolbar');		
		$bar->appendButton( 'Custom', '<a href="#" id="save-changes" title="Save Legal List." ><span class="icon-32-save"></span>Save</a>', '' );
		JToolBarHelper::spacer('10');
		
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=keywords',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Keyword Errors"><span class="icon-32-edit"></span>Keyword Errors</a>', '' );
				
		$tpl='create_legal_list';
		$this->display($tpl);		
	}
	
		
	function checklist()
	{
		JToolBarHelper::title(   'Keywords <small><small>[Errors]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Keywords [Errors]' ));
		
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=keywords&task=create_legal_list',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Edit Keyword List"><span class="icon-32-edit"></span>Edit Keyword List</a>', '' );
					
		$tpl='checklist';
		$this->display($tpl);		
	}
}

