<?php

defined('_JEXEC') or die('Restricted Access');
 use Joomla\Registry\Registry as JRegistry;
jimport('joomla.application.component.view');

class GpoViewRegion extends JViewLegacy {

    function display($tpl = null) {

        //die('it is');
        //global $mainframe;
        $mainframe =  JFactory::getApplication();
        $document =  JFactory::getDocument();
        $params =  $mainframe->getParams('com_content');
        if (isset($this->article->parameters)) {
            $aparams =  $this->article->parameters;
           // $params->merge($aparams);
        }
       // $this->assignRef('params', $params);
      $this->params = $params;


        $this->_meta();
        $this->_displayBreadCrumb();

       // if (1 == $this->cp) {
            echo DatapageHelper::getDPJs('stylesheet');
       // }

        $this->_displayArticle();

        $this->_displayLocations();
        $this->_displayArticles();
        echo $this->article->citations;
        if ($this->article->isPageNotFound != true) {
            echo $this->article->footer;
        }
        return;
    }

    function _meta() {

        $mainframe = & JFactory::getApplication();
        //global $mainframe;
        $document = & JFactory::getDocument();
        $document->setMetadata('rights', JText::_('SITE_METADATA_RIGHTS'));
        $document->setMetadata('author', JText::_('SITE_METADATA_AUTHOR'));

        //FIX: this needs to be correct html
        if ($mainframe->getCfg('MetaTitle') == '1') {

            if (!isset($this->article) && isset($this->article->location)) {
                $str = $this->article->title;
                $document->setTitle($str);
                //$mainframe->addMetaTag('title', $str );
                $document->setMetadata('title', $str);
            } else {
                $document->setTitle($this->article->title);
                //$mainframe->addMetaTag('title', $this->article->title );
                $document->setMetadata('title', $this->article->title);
            }
        }


        if ($this->article->metadesc) {
            $document->setDescription(htmlspecialchars($this->article->metadesc));
        }

        if ($this->article->metakey) {
            $document->setMetadata('keywords', htmlspecialchars($this->article->metakey));
        }

        //if ($mainframe->getCfg('MetaAuthor') == '1') {
        //$mainframe->addMetaTag('author', $this->article->author);
        //$document->setMetadata('author', $this->article->author);
        //}

        if (in_array($this->currentLangCode, array('en', 'es', 'fr')) && $this->article->alias != 'region-index') {
            $displayLocName = $this->regionObj->{$this->locationString};
            $metaDescription = JText::_('COM_GPO_DP_DESCRIPTION');
            $metaDescription = str_replace('#', $displayLocName, $metaDescription);
            $metaDescription = str_replace('[Location]', $displayLocName, $metaDescription);
            $metaKeywords = $displayLocName . ', ' . JText::_('COM_GPO_METADATA_KEYWORDS');
            $document->setMetadata('description', $metaDescription);
            $document->setMetadata('keywords', $metaKeywords);
        }

        jimport('joomla.html.parameter');

        //$mdata = new JParameter($this->article->metadata);
        $mdata = new JRegistry($this->article->metadata);
        $mdata = $mdata->toArray();
        foreach ($mdata as $k => $v) {
            if ($v) {
                $document->setMetadata($k, $v);
            }
        }
//		$document->setMetaData('robots', 'noindex, nofollow');
    }

    function _displayLocations() {
        if (!isset($this->locations['0'])) {
            return;
        }
        $tpl = 'locations';
        parent::display($tpl);
    }

    function _displayArticles() {
        if (!isset($this->articles['0'])) {
            return;
        }
        $tpl = 'articles';
        parent::display($tpl);
    }

    function _displayBreadCrumb() {
        if (!isset($this->breadcrumbs['0'])) {
            return;
        }
        $tpl = 'breadcrumbs';
        parent::display($tpl);
    }

    function _displayArticle() {

        if (!isset($this->article)) {
            return;
        }
        $tpl = 'article';
        parent::display($tpl);
    }

}

?>