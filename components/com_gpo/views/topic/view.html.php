<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

jimport( 'joomla.application.component.view' );

class GpoViewTopic extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->tip_meta();	
		parent::display( $tpl );
	}
	
	
	function tip_meta()
	{
		
		$document  =& JFactory::getDocument();
        $topicName = htmlspecialchars( $this->topic->get('topic_name') );

        $title = JTEXT::_('COM_GPO_TOPICS_TITLE');
        $keywords = JText::_('COM_GPO_METADATA_KEYWORDS');

        if( !empty($topicName) ) {
            $title    = $topicName . ' – Firearm law and policy bulletin';
            $keywords = strtolower($topicName) . ', ' . $keywords;
        }
        
        #Empty field, to be filled by search engines. Not to be filled by the Joomla ‘Global’ metadata settings.
        $description = ' ';
       
        $document->setTitle( $title );
        $document->setMetadata( 'description',$description );
        $document->setMetadata( 'keywords',$keywords );
        $document->setMetadata( 'rights', JText::_('SITE_METADATA_RIGHTS') );
        $document->setMetadata( 'author', JText::_('SITE_METADATA_AUTHOR') );
        
        /*
        $meta = json_decode( $this->topic->get( 'meta' ) );
        $title = $this->topic->get( 'window_title' );
		$document->setMetadata( 'keywords', $meta->keywords );
		$document->setDescription( $meta->description );
		$document->setMetadata( 'author', $meta->author );
        */
        
	}
}
