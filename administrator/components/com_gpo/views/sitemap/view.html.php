<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );
//JHTML::_('behavior.modal','a.popup');

class GpoViewSitemap extends JViewLegacy
{
	var $subComponentName = 'Sitemap';
	
		function display($tpl = null)
	{
		GpoHelper::addSubmenu('sitemap');
        if (JVERSION >= '3.0')
        {
            //$this->sidebar = JHtmlSidebar::render();
        }
		parent::display( $tpl );
	}
	
	function configuration(){
            JToolBarHelper::title(  $this->subComponentName . '<small><small>[Start]</small></small>', 'generic.png' );
            
            $document = &JFactory::getDocument();
            $document->setTitle(JText::_( $this->subComponentName . '[Start]' ));

            $tpl='configuration';
            $this->display($tpl);
        }


        function saveconfigs(){
            JToolBarHelper::title(  $this->subComponentName . '<small><small>[Start]</small></small>', 'generic.png' );
            
            $document = &JFactory::getDocument();
            $document->setTitle(JText::_( $this->subComponentName . '[Start]' ));

            $tpl='configuration_save';
            $this->display($tpl);
        }
}

