<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

jimport( 'joomla.application.component.view' );

class GpoViewLatest extends JViewLegacy
{
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->setTitle( JTEXT::_('COM_GPO_NEWS_LATEST_TITLE') );
        $document->setMetadata( 'rights', JText::_('SITE_METADATA_RIGHTS') );
        $document->setMetadata( 'author', JText::_('SITE_METADATA_AUTHOR') );
        
        $keywords = JTEXT::_('COM_GPO_METADATA_KEYWORDS') ;
        $document->setMetadata( 'keywords', $keywords );
        

        ## Leave this element empty. In search engine results, this space will be 
        ## filled by the header fields displayed in the GunPolicy News article.
        $document->setMetadata( 'description', " " );
		parent::display( $tpl );
	}
	
	public function assign($key, &$val)
	{
		if (is_string($key) && substr($key, 0, 1) != '_')
		{
			$this->$key = &$val;
			return true;
		}

		return false;
	} 
	
public function assignRef($key, &$val)
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
