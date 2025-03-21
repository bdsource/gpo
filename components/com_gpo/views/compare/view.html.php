<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

class GpoViewCompare extends JViewLegacy
{
    function display($tpl = null)
    {
        $document = JFactory::getDocument();
        $document->setMetadata( 'rights', JText::_('SITE_METADATA_RIGHTS') );
        $document->setMetadata( 'author', JText::_('SITE_METADATA_AUTHOR') );
        
        if( !empty($this->metaTitle) ) {
           $document->setTitle($this->metaTitle);
        }else {
           $document->setTitle('Data Comparison from GunPolicy.org Facts.');
        }
        
        $document->setMetadata( 'description', $this->metaDesc);
        $document->setMetadata( 'keywords', $this->metaKeywords);
        
        $stylesheetUrl = JURI::base() . 'templates/gunpolicy/css/dpstyles.css?v=0.2';
        $document->addStyleSheet($stylesheetUrl, 'text/css', "screen");

        //$document->addScript(JURI::root(true) . '/templates/gunpolicy/javascript/datapage.js');

        /*
         * reduce the margin-top of the central column
         * to compensate the empty spidebait
         *
         *  
         
        $script = 'window.addEvent("domready", function() {' . "\n" .
                  '$("main2").setStyle("margin-top", "17px");' . "\n" .
                  '});';

        //$document->addScriptDeclaration($script);
        * 
        */
        parent::display($tpl);
    }

    function countrylist($tpl = 'countrylist')
    {

        $document = JFactory::getDocument();

        if( !empty($this->metaTitle) ) {
           $document->setTitle($this->metaTitle);
        }else {
           $document->setTitle('Data Comparison from GunPolicy.org Facts.');
        }
        
        $document->setMetadata( 'description', $this->metaDesc);
        $document->setMetadata( 'keywords', $this->metaKeywords);
        $document->setMetadata( 'rights', JText::_('SITE_METADATA_RIGHTS') );
        $document->setMetadata( 'author', JText::_('SITE_METADATA_AUTHOR') );
        
        $stylesheetUrl = JURI::base() . 'templates/gunpolicy/css/dpstyles.css';
        $document->addStyleSheet($stylesheetUrl, 'text/css', "screen");
        /*
         * We are using jquery here to solve an issue with IE. Earlier we did same thing using mootools 1.3 but it was not working on IE 
         * and I did not find a way to disable default mootools library loaded by Joomla. 
         * In fact two mootools library was not working on IE but works on (as per tests) firefox, chrome, safari.
         */

        //$jsUrl = JURI::root(true) . '/media/system/js/mootools-core.js';
        //$document->addScript($jsUrl);
       
        //$jsUrl = JURI::root(true) . '/media/system/js/mootools-more-uncompressed.js';
        //$document->addScript($jsUrl);


        //$document->addScript(JURI::root(true) . '/templates/gunpolicy/javascript/datapage.js');

        $jsUrl = JURI::root(true) . '/media/system/js/jquery1.6.2.js';
        $document->addScript($jsUrl);


        //$stylesheetUrl = JURI::base() . 'media/system/css/tablesorter/blue/style.css';
        //$document->addStyleSheet($stylesheetUrl, 'text/css', "screen");

        /*
         * reduce the margin-top of the central column
         * to compensate the empty spidebait
         *
         *  
         
        $script = '
		          window.addEvent("domready", function() {' . "\n" .
                  '$("main2").setStyle("margin-top", "17px");' . "\n" .
                  '});

                  ';

        //$document->addScriptDeclaration($script);
         * 
         */
        parent::display($tpl);
    }


}
?>
