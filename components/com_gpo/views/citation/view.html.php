<?php

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

class GpoViewCitation extends JViewLegacy {

    function display($tpl = null) {
        switch ($this->type) {
            case 'news':
//				break;
            case 'quotes':
                $tpl = $this->type;
                break;
            default:
                break;
        }
        
        /*
        $document = JFactory::getDocument();
        $document->setMetadata( 'rights', JText::_('SITE_METADATA_RIGHTS') );
        $document->setMetadata( 'author', JText::_('SITE_METADATA_AUTHOR') );
        */
        
        parent::display($tpl);
    }

    function archive($tpl = null) {
        switch ($this->type) {
            case 'news':
                $tpl = 'news_archive';
                break;
            default:
                $tpl = 'quotes_archive';
                break;
        }

        $document = JFactory::getDocument();
        $document->setMetadata( 'rights', JText::_('SITE_METADATA_RIGHTS') );
        //$document->setMetadata( 'author', JText::_('SITE_METADATA_AUTHOR') );
        
        parent::display($tpl);
    }

    function citation_home() {

        $document = JFactory::getDocument();
        $document->setMetadata( 'rights', JText::_('SITE_METADATA_RIGHTS') );
        //$document->setMetadata( 'author', JText::_('SITE_METADATA_AUTHOR') );
        
        parent::display('citation_archive_home');
    }

}

?>