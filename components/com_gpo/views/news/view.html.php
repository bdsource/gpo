<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );

class GpoViewNews extends JViewLegacy
{
	function display($tpl = null)
	{
		//$this->tip_meta();
                $this->tip_meta_abstract();
		parent::display( $tpl );
	}
	
        
	function membersOnly($tpl = null)
	{
		$this->tip_meta();
		$tpl = 'members_only';
		parent::display( $tpl );
	}
	
	
	function archive()
	{
                $this->tip_meta_general();
		$this->_displayBreadCrumb();
		$tpl = 'archive';
		parent::display( $tpl );	
	}
	
	
	function archive_year()
	{
                $this->tip_meta_general();
		$this->_displayBreadCrumb();
		$tpl = 'archive_year';
		parent::display( $tpl );	
	}
	
	function archive_month()
	{
                $this->tip_meta_general();
		$this->_displayBreadCrumb();
		$tpl = 'archive_month';
		parent::display( $tpl );
	}
	
	function archive_day()
	{
                $this->tip_meta_general();
		$this->_displayBreadCrumb();
		$tpl = 'archive_day';
		parent::display( $tpl );
	}
	
	function archive_bad( $type )
	{
                $this->tip_meta_general();
		$tpl = 'archive_bad_' . $type;
		parent::display( $tpl );
	}
	
	
	function tip_meta() {
            $document = & JFactory::getDocument();
            //$title = JApplication::getCfg('sitename') . ' — ' . $this->oNews->title;
            $title =    Joomla\CMS\Factory::getApplication()->getCfg('sitename') . ' — ' . $this->oNews->gpnheader;

            $document->setTitle($title);
            $document->setMetadata('rights', JText::_('SITE_METADATA_RIGHTS'));
            $document->setMetadata('author', JText::_('SITE_METADATA_AUTHOR'));

            $keywords = array();

            //( !empty( $this->oNews->source ) ? $keywords[] = $this->oNews->source : '' );
            //( !empty( $this->oNews->category ) ? $keywords[] = $this->oNews->category : '' );

            if (count($this->oNews->locations) >= 1) {
                $keywords[] = implode(", ", $this->oNews->locations);
            }

            (!empty($this->oNews->keywords) ? $keywords[] = $this->oNews->keywords : '' );

            if (count($keywords)) {
                $document->setMetadata('keywords', implode(", ", $keywords));
            }

            //$document->setDescription( $desc );
            //$document->setMetadata( 'keywords', $meta->key );
            //$document->setMetadata( 'author', $meta->author );
    }

    function tip_meta_general() {
        $document = & JFactory::getDocument();
        $title =    Joomla\CMS\Factory::getApplication()->getCfg('sitename') . ' — ' . JText::_('SITE_METADATA_ARCHIVE');

        $document->setTitle($title);
        $document->setMetadata('rights', JText::_('SITE_METADATA_RIGHTS'));
        $document->setMetadata('author', JText::_('SITE_METADATA_AUTHOR'));
        $document->setMetadata('keywords', JText::_('SITE_METADATA_KEYWORDS'));
    }
    
    function tip_meta_abstract() {
        $this->tip_meta();
        $document = JFactory::getDocument();
        if (count($this->oNews->locations) >= 1) {
            $keywords = implode(", ", $this->oNews->locations);
        }
        $keywordsMeta = $keywords . ', ' . JText::_('SITE_METADATA_KEYWORDS_NEWSABSTRACT');
        $document->setMetadata('keywords', $keywordsMeta);
    }

    function _displayBreadCrumb() {
        $tpl = 'archive_breadcrumbs';
        parent::display($tpl);
    }

}