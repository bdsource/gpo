<?php

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

class GpoViewSearch extends JViewLegacy {

    function display($tpl = null) {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_GPO_NEWS_SEARCH_TITLE'));
        $document->setMetadata('rights', JText::_('SITE_METADATA_RIGHTS'));
        $document->setMetadata('author', JText::_('SITE_METADATA_AUTHOR'));

        ## Leave this element empty. In search engine results, this space will be 
        ## filled by the header fields displayed in the GunPolicy News article.
        $document->setMetadata('description', " ");
        $keywords = JText::_('COM_GPO_METADATA_KEYWORDS');
        $document->setMetaData('keywords', $keywords);

        header("Content-type:text/html; charset=utf-8");
//echo 'test77:';echo($tpl);die();
        parent::display($tpl);
    }

    function help() {
        header("Content-type:text/html; charset=utf-8");

        $document = JFactory::getDocument();
        $document->setMetadata('rights', JText::_('SITE_METADATA_RIGHTS'));
        $document->setMetadata('author', JText::_('SITE_METADATA_AUTHOR'));

        parent::display('help');
    }

    function nowebsource() {
        header("Content-type:text/html; charset=utf-8");

        $document = JFactory::getDocument();
        $document->setMetadata('rights', JText::_('SITE_METADATA_RIGHTS'));
        $document->setMetadata('author', JText::_('SITE_METADATA_AUTHOR'));

        parent::display('nowebsource');
    }

    function members() {
        $tpl = 'members';
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_GPO_NEWS_SEARCH_TITLE'));
        $document->setMetadata('rights', JText::_('SITE_METADATA_RIGHTS'));
        $document->setMetadata('author', JText::_('SITE_METADATA_AUTHOR'));

        header("Content-type:text/html; charset=utf-8");
        parent::display($tpl);
    }

}

?>