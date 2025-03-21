<?php

defined('_JEXEC') or die('Restricted Access');
jimport('joomla.application.component.view');

class GpoViewHome extends JViewLegacy {

    function display($tpl = null) { 
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_GPO_HOMEPAGE_TITLE'));
        $document->setMetadata('keywords', JText::_('COM_GPO_HOMEPAGE_METADATA_KEYWORDS'));
        $document->setMetadata('description', JText::_('COM_GPO_HOMEPAGE_METADATA_DESCRIPTION'));
        $document->setMetadata('rights', JText::_('SITE_METADATA_RIGHTS'));
        $document->setMetadata('author', JText::_('SITE_METADATA_AUTHOR'));

        $stylesheetUrl = JURI::base() . 'templates/gunpolicy/css/fpstyles.css?v=3';
        $document->addStyleSheet($stylesheetUrl, 'text/css', "screen");

        //$jsUrl = JURI::base().'media/system/js/jquery1.6.2.js';
        //$document->addScript($jsUrl);
        //$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
        /* $document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
          echo '<script type="text/javascript">
          jQuery.noConflict();
          </script>'; */


        //JHTML::script('jquery.jcarousel-core.min.js', 'media/system/jcarousel/');

        $cssUrl = JURI::base() . 'media/system/jcarousel/tango/skin.css';
        $document->addStyleSheet($cssUrl);
        $document->addStyleSheet(JURI::base() . 'media/system/jcarousel/jcarousel.basic.css');
        
        $carousel = "         
        
function mycarousel_initCallback(carousel)
            {
                // Disable autoscrolling if the user clicks the prev or next button.
                carousel.buttonNext.bind('click', function() {
                    carousel.startAuto(0);
                });

                carousel.buttonPrev.bind('click', function() {
                    carousel.startAuto(0);
                });

                // Pause autoscrolling if the user moves with the cursor over the clip.
                carousel.clip.hover(function() {
                    carousel.stopAuto();
                }, function() {
                    carousel.startAuto();
                });
            }
            jQuery(document).ready(function($) {
                $('#mycarousel').jcarousel({
                    scroll: 1,
                    auto: 3,
                    wrap:'circular',
                    initCallback: mycarousel_initCallback,
                    buttonNextHTML: null,
                    buttonPrevHTML: null

                });
                $('.ctls').hover(function(){
                        $('#mycarousel').data('jcarousel').stopAuto();
                    }, function(){
                        $('#mycarousel').data('jcarousel').startAuto();
                    }
                );
                 $('.ctls').click(function(){
                    $('#mycarousel').data('jcarousel').stopAuto(0);
                })
                

                $('#fcprev').click(function(){
                    var carousel = $('#mycarousel').data('jcarousel');
                    carousel.prev();
                });
                $('#fcnext').click(function(){
                    var carousel = $('#mycarousel').data('jcarousel');
                    carousel.next();
                });
            });
        ";
        $document->addScriptDeclaration($carousel);
        /*
         * reduce the margin-top of the central column 
         * to compensate the empty spidebait
         * 
         *  */
        $script = 'window.addEvent("domready", function() {' . "\n" .
                '$("main2").setStyle("margin-top", "17px");' . "\n" .
                '});';

        //$document->addScriptDeclaration( $script );
       
        parent::display($tpl);
    }
    
    public function assignRef($key, $val)
	{
		if (is_string($key) && substr($key, 0, 1) != '_')
		{
			$this->$key = &$val;
			return true;
		}

		return false;
	}

}

?>