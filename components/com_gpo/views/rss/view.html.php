<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

jimport( 'joomla.application.component.view' );

class GpoViewRss extends JViewLegacy
{
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
        
        $title       = 'Design Your Own Gun Policy News Feed';
        $description = 'Custom-build a global or regional news feed using your own choice of keywords in firearm law and policy, armed violence reduction and small arms.';
        
        if( !empty($this->url) ) {
            $title       = "Design Your Own Gun Policy News Feed";
            $description = 'Custom-build a global or regional news feed using your own choice of keywords in firearm law and policy, armed violence reduction and small arms.';
        }
        
		$document->setTitle( $title );        
        $document->setMetadata( 'description', $description);
        $document->setMetadata( 'rights', JText::_('SITE_METADATA_RIGHTS') );
        $document->setMetadata( 'author', JText::_('SITE_METADATA_AUTHOR') );
        
		parent::display( $tpl );
	}
}
?>
