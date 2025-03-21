<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

jimport( 'joomla.application.component.view' );

class GpoViewGroup extends JViewLegacy
{
	function display($tpl = null)
	{
    
		//global $mainframe;
		$mainframe =& JFactory::getApplication();

        $document  =& JFactory::getDocument();
		$params	   =& $mainframe->getParams('com_content');
		if( isset( $this->article->parameters ) )
		{
			$aparams =& $this->article->parameters;
			$params->merge($aparams);
		}
		$this->assignRef('params' , $params);

		$this->_meta();
		$this->_displayBreadCrumb();

	    if( 1 == $this->cp )
		{
		   echo DatapageHelper::getDPJs( 'stylesheet' );    
		}
		
		$this->_displayArticle();
		
		$this->_displayLocations();
		$this->_displayArticles();
		echo $this->article->citations;
		echo $this->article->footer;
		return;
	}


	function _meta() {

        $mainframe = & JFactory::getApplication();

        //global $mainframe;
        $document = & JFactory::getDocument();

        //FIX: this needs to be correct html
        if ($mainframe->getCfg('MetaTitle') == '1') {

            if (!isset($this->article) && isset($this->article->location)) {
//				$str = 'Guns in ' . $this->article->location . ': Firearm Law, Small Arms and their Impact';
//				$str = 'Test 123 ' . $this->article->location;
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
            $document->setDescription(htmlentities($this->article->metadesc, ENT_QUOTES));
        }

        if ($this->article->metakey) {
            $document->setMetadata('keywords', htmlentities($this->article->metakey, ENT_QUOTES));
        }

        //if ($mainframe->getCfg('MetaAuthor') == '1') {
        $document->setMetadata( 'rights', JText::_('SITE_METADATA_RIGHTS') );
        $document->setMetadata( 'author', JText::_('SITE_METADATA_AUTHOR') );
        //}

        jimport('joomla.html.parameter');

        $mdata = new JParameter($this->article->metadata);
        $mdata = $mdata->toArray();
        foreach ($mdata as $k => $v) {
            if ($v) {
                $document->setMetadata($k, $v);
            }
        }
//		$document->setMetaData('robots', 'noindex, nofollow');
    }



	function _displayLocations()
	{
		if( !isset( $this->locations['0']))
		{
			return;
		}
		$tpl = 'locations';
		parent::display( $tpl );
	}


	
	function _displayArticles()
	{
		if( !isset( $this->articles['0']))
		{
			return;
		}
		$tpl = 'articles';
		parent::display( $tpl );
	}


	
	function _displayBreadCrumb()
	{
		if( !isset( $this->breadcrumbs['0'] ))
		{
			return;
		}
		$tpl = 'breadcrumbs';
		parent::display( $tpl );
	}
	

	
	function _displayArticle()
	{
		
		if( !isset( $this->article ))
		{
			return;
		}
		$tpl = 'article';
		parent::display( $tpl );
	}
}
?>
