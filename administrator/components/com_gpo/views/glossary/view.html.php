<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');


class GpoViewGlossary extends JViewLegacy
{
	function display($tpl = null)
		{
			GpoHelper::addSubmenu('glossary');
			if (JVERSION >= '3.0')
			{
				//$this->sidebar = JHtmlSidebar::render();
			}
			parent::display( $tpl );
		}

    function getQuickEditBox()
    {


        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
        $published = Joomla\CMS\Factory::getApplication()->getInput()->get('published', 1);
	$task = Joomla\CMS\Factory::getApplication()->getInput()->get('task');
        //echo $state;
	if(!($task=='edit' || $task=='create')){
     /*   $quickeditbox = '<small><small>
                    <form style="padding-left:40px;display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                        <input type="hidden" name="task" value="lookup" />
                        <input type="hidden" name="controller" value="glossary" />
                        <span>Edit ID: G<input type="text" name="id" value="" style="width:50px;"/>
                        <input type="hidden" name="published" value="' . $published . '" />
                        <input style="margin-left:-5px" type="submit" value="Go" /></span>
                    </form>
                    ';
	*/
        if (!empty($id)) {
            $quickeditbox .= '<span><form style="padding-left:20px;display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                    <input type="hidden" name="task" value="lookup" />
                    <input type="hidden" name="controller" value="glossary" />
                    <input type="hidden" name="lookupdirection" value="prev" />
                    <input type="hidden" name="published" value="' . $published . '" />
                    <input type="hidden" name="id" value="' . $id . '"/>
                    <input type="submit" value="Prev" />
                </form>
                <form style="display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                    <input type="hidden" name="task" value="lookup" />
                    <input type="hidden" name="controller" value="glossary" />
                    <input type="hidden" name="lookupdirection" value="next" />
                    <input type="hidden" name="id" value="' . $id . '"/>
                    <input type="hidden" name="published" value="' . $published . '" />
                    <input type="submit" value="Next" />
                </form></span>
            ';
        }
	}
        $quickeditbox .= '</small></small>';
        return $quickeditbox;
    }

    function published()
    {
        $title = JText::_('Glossary: <small><small>[Published]</small></small>') . $this->getQuickEditBox();
        JToolBarHelper::title($title, 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Glossary: [Published]'));
                
        $bar = & JToolBar::getInstance('toolbar');
        JToolBarHelper::custom('search', 'search', '', 'Search', false);
        $href = JRoute::_('index.php?option=com_gpo&controller=glossary&task=search', false);
        //$bar->appendButton('Custom', '<a href="' . $href . '" id="toolbar-Link" title="Search Glossary"><span class="icon-48-search"></span>Search</a>', '');
        $document = &JFactory::getDocument();
        $mootools = JURI::root(true) . '/media/system/js/mootools.js';
        if (isset($document->_scripts[$mootools])) {
            //unset( $document->_scripts[$mootools]);
        }
        $this->tip_find_replace();
        $this->tip_create();
        //$this->tip_unpublished();
        
        $tpl = 'published';
        $this->display($tpl);
    }


    function edit($tpl = 'edit')
    {

        if ($this->isNew) {
            $title = JText::_('Glossary: <small><small>[Create New]</small></small>'. $this->getQuickEditBox());
            JToolBarHelper::title($title, 'generic.png');
            
            $document = & JFactory::getDocument();
	    $document->setTitle(JText::_(strip_tags($title)));
        } else {
            $title = JText::_('Glossary: <small><small>[Edit Glossary]</small></small>'. $this->getQuickEditBox());
            JToolBarHelper::title($title, 'generic.png');
            
            $document = & JFactory::getDocument();
	    $document->setTitle(JText::_(strip_tags($title)));
        }
        $document = &JFactory::getDocument();
        $mootools = JURI::root(true) . '/media/system/js/mootools.js';
        if (isset($document->_scripts[$mootools])) {
            //unset( $document->_scripts[$mootools]);
        }
        $bar = & JToolBar::getInstance('toolbar');
        $title = ($this->can_publish) ? "Save and Publish this Quote" : "Save this Quote and Goto Approve for Publishing";
        $href = JRoute::_('#save_publish');
        JToolBarHelper::save();
        //$bar->appendButton('Custom', '<a href="' . $href . '" id="item_publish" title="' . $title . '"><span class="icon-32-publish"></span>Save</a>', '');
        $this->tip_close();
        $tpl = 'edit';
        $this->display($tpl);
    }


    function tip_published()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=glossary&type=' . $this->type . '&task=published', false);
        $html_total = ($this->unpublishedTotal > 0) ? '( ' . $this->unpublishedTotal . ' )' : '';
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="List of Published Citations"><span class="icon-unpublish"></span>Published List</a>', '');
    }


    function tip_unpublished()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=glossary&task=unpublished&type=' . $this->type, false);
        $html_total = ($this->unpublishedTotal > 0) ? '( ' . $this->unpublishedTotal . ' )' : '';
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '"><span class="icon-unpublish" title=" Go to the Unpublished Citations queue"></span>Unpublished' . $html_total . '</a>', '');
    }


    function tip_create()
    {
        $bar = &JToolBar::getInstance('toolbar');
        JToolBarHelper::custom('create', 'new', '', 'Create New', false);
        $href = JRoute::_('index.php?option=com_gpo&controller=glossary' . '&task=create', false);
        //$bar->appendButton('Custom', '<a href="' . $href . '" title="Create a new definition"><span class="icon-32-new"></span>Create New</a>', '');
    }

    function tip_find_replace()
	{
		$bar = & JToolBar::getInstance('toolbar');
                                    JToolBarHelper::custom('frt', 'search', '', 'Find & Replace', false);
		$href = JRoute::_( 'index.php?option=com_gpo&controller=glossary&task=frt', false );
		//$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Glossary: Find & Replace"><span class="icon-32-search"></span>Find & Replace</a>', '' );
		JToolBarHelper::spacer('10');		
	}

    function tip_close()
    {
        $bar = & JToolBar::getInstance('toolbar');
        if (strpos($_SERVER['HTTP_REFERER'], 'task=unpublished') === false) {
            $href = JRoute::_('index.php?option=com_gpo&controller=glossary&type=' . $this->type, false);
        } else {
            $href = JRoute::_('index.php?option=com_gpo&controller=glossary&type=' . $this->type . '&task=unpublished', false);
        }
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close without saving"><span class="icon-cancel"></span>Close</a>', '');
    }
        
        
	
	/*
	 * 
	 * Find & Rpelace tool
	 * functions
	 * 
	 */
	
	function frt_add()
	{
		
		JToolBarHelper::title( 'Glossary Find & Replace [Search]', 'generic.png' );
                
                $document = & JFactory::getDocument();
		$document->setTitle(JText::_('[Glossary] Glossary Find & Replace - Add New Search'));
		
		$title = "Search in a specific column";
		$title_do = 'Search';
		$this->add_jquery();
		
		$bar = & JToolBar::getInstance('toolbar');	
		
		//search button
		JToolBarHelper::save('frt', 'Search');
		
		//last searched button
		$LastSearchedLink = 'index.php?option=com_gpo&controller='.$this->controller.'&task=frt&action=add'.
                            '&swap[from]='.urlencode($this->lastSearchedQuery->from).
                            '&swap[to]='.urlencode($this->lastSearchedQuery->to).
                            '&swap[column_name]='.urlencode($this->lastSearchedQuery->column_name).
                            '&swap[case_sensitive]='.urlencode($this->lastSearchedQuery->options);
		
		$href = JRoute::_( $LastSearchedLink,false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Last Searhed Query"><span class="icon-search"></span>Last Searched</a>', '' );
		
		//history button
		$href = JRoute::_( 'index.php?option=com_gpo&controller='.$this->controller.'&task=frt&action=history',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="View Past Replaces History"><span class="icon-edit"></span>History</a>', '' );	
		
		JToolBarHelper::spacer(10);
		JToolBarHelper::divider();
		JToolBarHelper::spacer(10);
		JToolbarHelper::cancel('published','Close');
		$href = JRoute::_( 'index.php?option=com_gpo&controller='.$this->controller.'&task=published',false );
		//$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Close: Back to the glossary [Published] Page"><span class="icon-32-cancel"></span>Close</a>', '' );
		
		$tpl='frt_add';
		$this->display($tpl);
	}
	
    function frt_results()
	{
		
		JToolBarHelper::title( 'Glossary Find & Replace [Results]', 'generic.png' );
                
                $document = & JFactory::getDocument();
		$document->setTitle(JText::_('Results - Glossary Find & Replace'));
                
		$title = 'Replace Selected Records';
		$this->add_jquery();
		
		//JToolBarHelper::back();
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller='.$this->controller.'&task=frt&action=add',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close: Back to the Search Form of Glossary Find & Replace"><span class="icon-cancel"></span>Cancel</a>', '' );
		
		JToolBarHelper::spacer(10);
		JToolBarHelper::divider();
		JToolBarHelper::spacer(10);
		
		JToolBarHelper::publishList( 'frt_replace', $title );
			
		$tpl='frt_results';
		$this->display($tpl);
	}
	
	
    function frt_history()
	{
		
		JToolBarHelper::title( 'Glossary Find & Replace [History]', 'generic.png' );
                
                $document = & JFactory::getDocument();
		$document->setTitle(JText::_('History - Glossary Find & Replace'));
		
		$bar = & JToolBar::getInstance('toolbar');
	  	
		//new search button
		$href = JRoute::_( 'index.php?option=com_gpo&controller='.$this->controller.'&task=frt&action=add',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="New Search: Perform New Search on Glossary"><span class="icon-search"></span>New Search</a>', '' );
	    
		//Last searched button
		//var_dump( $this->lastSearchedQuery );
		$LastSearchedLink = 'index.php?option=com_gpo&controller='.$this->controller.'&task=frt&action=add'.
                            '&swap[from]='.urlencode($this->lastSearchedQuery->from).
                            '&swap[to]='.urlencode($this->lastSearchedQuery->to).
                            '&swap[column_name]='.urlencode($this->lastSearchedQuery->column_name).
                            '&swap[case_sensitive]='.urlencode($this->lastSearchedQuery->options);
		
		$href = JRoute::_( $LastSearchedLink,false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Last Searhed Query"><span class="icon-search"></span>Last Searched</a>', '' );
		
		
		//close button
		$href = JRoute::_( 'index.php?option=com_gpo&controller='.$this->controller,false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close: Back to the Glossary [Published] Page"><span class="icon-cancel"></span>Close</a>', '' );
	
		$tpl='frt_history';
		$this->display($tpl);				
	}
	
    function add_jquery() {    
        $document = &JFactory::getDocument();
        $document->addScript(JURI::root(true).'/media/system/js/jquery1.6.2.js');
    }
    
    function search(){

      $title = JText::_('Glossary <small><small>[Search]</small></small>');
      
      JToolBarHelper::title(JText::_($title), 'generic.png');
      
      $document = & JFactory::getDocument();
      $document->setTitle(JText::_('Glossary [Search]'));
		
      $bar = & JToolBar::getInstance('toolbar');

      $href = JRoute::_('index.php?option=com_gpo&controller=glossary&task=searchresult', false);
      if(Joomla\CMS\Factory::getApplication()->getInput()->get('task')!='searchresult'){
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" onclick="jQuery(\'.adminFormG\').submit();" id="toolbar-Link" title="Search Glossary"><span class="icon-search"></span>Search</a>', '');
      }
      $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="#reset" id="reset-button" title="Reset Form"><span class="icon-purge"></span>Reset Form</a>', '');
      $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="#close" id="close-button" title="Close"><span class="icon-cancel"></span>Close</a>', '');

      $tpl = 'searchform';

      $this->display($tpl);
    }
    
    function searchresult(){
        $title = JText::_('Glossary: <small><small>[Published]</small></small>') . $this->getQuickEditBox();
        JToolBarHelper::title($title, 'generic.png');
        
        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('Glossary: [Published]'));
      
        $document = &JFactory::getDocument();
        $mootools = JURI::root(true) . '/media/system/js/mootools.js';
        if (isset($document->_scripts[$mootools])) {
            //unset( $document->_scripts[$mootools]);
        }
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=glossary&task=search', false);
        //$bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" onclick="jQuery(\'.adminFormG\').submit();" id="toolbar-Link" title="Search Glossary"><span class="icon-search"></span>Search</a>', '');
        $this->tip_find_replace();
        $this->tip_create();
        $tpl = 'published';
        $this->display($tpl);
    }

}

