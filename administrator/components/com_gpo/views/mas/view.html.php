<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class GpoViewMas extends JViewLegacy {
    
    
    function display($tpl = null) 
    {
	    GpoHelper::addSubmenu('massshootings');
            if (JVERSION >= '3.0')
            {
                //$this->sidebar = JHtmlSidebar::render();
            }
	    parent::display( $tpl );
    }
    

    function getLookupBox() {
        $id = (int) $this->id;
        $state = Joomla\CMS\Factory::getApplication()->getInput()->get('state','');
	$task = Joomla\CMS\Factory::getApplication()->getInput()->get('task');
        if(empty($state)){
            $state = (Joomla\CMS\Factory::getApplication()->getInput()->get('task','') == 'unpublished') ? 'unpublished' : 'published';
        }
	if(!($task=='edit' || $task=='create')){
        $lookupbox = '<small><small>
                    <form style="padding-left:40px;display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                        <input type="hidden" name="task" value="lookup" />
                        <input type="hidden" name="controller" value="mas" />
                        <span>Lookup ID: N<input type="text" name="id" value="" style="width:50px;"/>
                        <input type="hidden" name="state" value="'.$state.'" />
                        <input type="submit" value="Go" style="margin-left:-5px;" /></span>
                    </form>
                    ';
        if (!empty($id)) {
            $lookupbox .= '<span><form style="padding-left:20px;display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                    <input type="hidden" name="task" value="lookup" />
                    <input type="hidden" name="controller" value="mas" />
                    <input type="hidden" name="lookupdirection" value="prev" />
                    <input type="hidden" name="id" value="' . $id . '"/>
                    <input type="hidden" name="state" value="'.$state.'" />
                    <input type="submit" value="Prev" />
                </form>
                <form style="display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                    <input type="hidden" name="task" value="lookup" />
                    <input type="hidden" name="controller" value="mas" />
                    <input type="hidden" name="lookupdirection" value="next" />
                    <input type="hidden" name="id" value="' . $id . '"/>
                    <input type="hidden" name="state" value="'.$state.'" />
                    <input type="submit" value="Next" />
                </form></span>
            ';
        }
	}
        $lookupbox .= '</small></small>';
        return $lookupbox;
    }

    function edit() {
//title

     
        $this->isNew = ( $this->oMas->id < 1 );
        $text = $this->isNew ? JText::_('Create New') : JText::_('Edit');
        $text = JText::_($text);
        $title = JText::_('Mas <small><small>[' . $text . ']</small></small>');
        if (isset($this->oMas->live_id) && !empty($this->oMas->live_id)) {
            $title .= ' <small><small>Live Id: ' . $this->oMas->live_id . '</small></small>';
        }
        $title .= $this->getLookupBox();
        JToolBarHelper::title(JText::_($title), 'generic.png');
        
        $document = & JFactory::getDocument();
	    $document->setTitle(JText::_(strip_tags($title)));
        
        $bar = & JToolBar::getInstance('toolbar');

        if ($this->isNew) {
//Force content to be blank
            $this->oQuote->content = "";
        }
//Clear Form			
        $href = JRoute::_('#', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="clear_form" title="Clear all text from this form"><span class="icon-trash"></span>Clear Form</a>', '');
        JToolBarHelper::spacer('10');

//Save and Create Another
        $title = ( $this->isNew ) ? "Save this new Mas item, then create another" : "Save this Mas item, then create another";
        $href = JRoute::_('#', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="save_create_another" title="' . $title . '"><span class="icon-save"></span>Save + Clone</a>', '');
        JToolBarHelper::spacer('10');
        
//Save and Publish
        $title = ( $this->can_publish ) ? "Save and Publish this Mas Item" : "Save this Mas Item and Goto Approve for Publishing";
        $href = JRoute::_('#save_publish');
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="item_publish" title="' . $title . '"><span class="icon-publish"></span>Save + Publish</a>', '');
        $this->tip_published();
        
////Save Mas & then Clone to Quotes
//        $title = "Save & Clone this Mas Item to Quotes Unpublished Queue";
//        $href = JRoute::_('#',false);
//        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="item_saveAndCloneToQuotes" title="' . $title . '"><span class="icon-save"></span>Save & Clone to Quotes</a>', '');
//        JToolBarHelper::spacer('10');

        if (strpos($_SERVER['HTTP_REFERER'], 'task=unpublished') === false) {
            $href = JRoute::_('index.php?option=com_gpo&controller=mas', false);
        } else {
            $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=unpublished', false);
        }
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="item_close" title="Close Without Saving"><span class="icon-cancel"></span>Close Without Saving</a>', '');

      /* Moved it in here to try and seperate out the html + javascript a little more */
      //Include prototype
        viewHtmlAddPrototype();
        $document = &JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/administrator/templates/bluestork/js/mas_location.js?v=3');
        $document->addStyleSheet(JURI::root(true) . '/media/system/css/calendar-jos.css', 'text/css', 'all', array('title' => 'green'));
        $document->addScript(JURI::root(true) . '/media/system/js/calendar.js');
        $document->addScript(JURI::root(true) . '/media/system/js/calendar-setup.js');

        $tpl = 'edit';
        $this->display($tpl);
    }

    function published() {
        JToolBarHelper::title(JText::_('MAS <small><small>[Published]</small></small> ' . $this->getLookupBox()), 'generic.png'); 
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Published] '));

        if ($this->can_publish) {
            $bar = & JToolBar::getInstance('toolbar');
            JToolBarHelper::custom('export', 'download', '', 'Export', false);
            $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=export', false);
            //$bar->appendButton('Custom', '<a href="' . $href . '" title="Export Mas data"><span class="icon-32-export"></span>Export</a>', '');
            JToolBarHelper::spacer('10');
        }

        $this->tip_reindex();
        //$this->twitteroauths();
        //$this->tip_mailout();
        $this->tip_search();
        $this->tip_back_to_search();
        $this->tip_find_replace();
        $this->tip_create_new();
        //$this->tip_published();
        $this->tip_unpublished();
        
         // Options button.
        if (JFactory::getUser()->authorise('core.admin', 'com_gpo')) 
        {
            JToolBarHelper::preferences('com_gpo');
        }
        
        $tpl = 'published';
        $this->display($tpl);
    }

    function unpublished() {
        JToolBarHelper::title(JText::_('MAS <small><small>[Unpublished]</small></small>' . $this->getLookupBox()), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Unpublished] '));
        
        $bar = & JToolBar::getInstance('toolbar');

        $this->tip_reindex();
        $this->tip_back_to_search();
      //  $this->tip_build();

        //$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Mail latest articles"><span class="icon-32-mail"></span>Send Mail ('.($this->mailPending ? $this->mailPending : 0).')</a>', '' );

        //$this->tip_mailout();
        $this->tip_create_new();
        $this->tip_published();


        //remove all unpublished
        JToolbarHelper::custom('unpublished_empty','remove','','Empty Queue',false);
        $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=unpublished_empty', false);
        //$bar->appendButton('Custom', '<a href="' . $href . '" id="unpublish_empty" title="Delete all the unpublished Mas Items in this queue"><span class="icon-32-trash"></span>Empty Queue</a>', '');
        $tpl = 'unpublished';
        $this->display($tpl);
    }

    function publish() {
        JToolBarHelper::title(JText::_('MAS <small><small>[Publish]</small></small>' . $this->getLookupBox()), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Publish] '));
        
        $bar = & JToolBar::getInstance('toolbar');

//edit
        $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=edit&id=' . $this->oMas->id, false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Edit"><span class="icon-edit"></span>Edit</a>', '');

        $this->tip_close();

//Include prototype
        viewHtmlAddPrototype();
        $tpl = 'publish';
        $this->display($tpl);
    }

    function delete() {
        JToolBarHelper::title(JText::_('MAS <small><small>[Delete]</small></small>' . $this->getLookupBox()), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Delete] '));

        $this->tip_close();

//Include prototype
        viewHtmlAddPrototype();
        $tpl = 'delete';
        $this->display($tpl);
    }

    function empty_unpublished() {
        JToolBarHelper::title(JText::_('MAS <small><small>[Empty Unpublished]</small></small>' . $this->getLookupBox()), 'generic.png');

        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Empty Unpublished] '));
        
        $this->tip_close();

        $tpl = 'empty_unpublished';
        $this->display($tpl);
    }

    function lookup() {
        $bar = & JToolBar::getInstance('toolbar');

        $this->tip_reindex();
        $this->tip_back_to_search();
        $this->tip_search();

        if (!empty($this->id)) {
            $state = Joomla\CMS\Factory::getApplication()->getInput()->get('state');

            JToolBarHelper::title(JText::_('MAS <small><small>[Lookup]</small></small>' . $this->getLookupBox()), 'generic.png');
            
            $document = & JFactory::getDocument();
	    $document->setTitle(JText::_('MAS [Lookup]'));
//edit
            if('unpublished'==$state){
                $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=edit&state='.$state.'&id=' . $this->id, false);
                $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Open this Mas in Edit mode"><span class="icon-edit"></span>Edit</a>', '');
            } else {
                $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=edit&state='.$state.'&live_id=' . $this->id, false);
                $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Open this Mas in Edit mode"><span class="icon-edit"></span>Edit</a>', '');
            }

//            //citation button
//            if('published'==$state){
//                $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=createcitation&id=' . $this->id);
//                $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Create a separate Citation from this Mas item, then open it for editing"><span class="icon-new"></span>Citation</a>', '');
//            }
            
            //delete button
            if ($this->can_publish) {
                if('unpublished'==$state){
                    $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=unpublished_delete&id=' . $this->id, false);
                    $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Delete this unpublished Mas Item"><span class="icon-trash"></span>Delete</a>', '');
                } else {
                    $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=published_delete&id=' . $this->id, false);
                    $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Delete this live Mas Item"><span class="icon-trash"></span>Delete</a>', '');
                }
                JToolBarHelper::spacer('10');
            }
        } else {
            JToolBarHelper::title(JText::_('MAS <small><small>[Lookup]</small></small>'), 'generic.png');
            
            $document = & JFactory::getDocument();
	    $document->setTitle(JText::_('MAS [Lookup]'));
        }

//close
        $href = JRoute::_('index.php?option=com_gpo&controller=mas', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close without saving"><span class="icon-cancel"></span>Close</a>', '');


        if (!empty($this->oMas->id)) {
            $tpl = 'edit_disabled';
//Include prototype
            viewHtmlAddPrototype();
            $document = &JFactory::getDocument();
            $document->addScript(JURI::root(true) . '/administrator/templates/bluestork/js/mas_location.js');
            $document->addStyleSheet(JURI::root(true) . '/media/system/css/calendar-jos.css', 'text/css', 'all', array('title' => 'green'));
            $document->addScript(JURI::root(true) . '/media/system/js/calendar.js');
            $document->addScript(JURI::root(true) . '/media/system/js/calendar-setup.js');
        } else {
            $tpl = 'lookup';
        }
        $this->display($tpl);
    }

    function search() {
        $title = JText::_('MAS <small><small>[Search]</small></small>');
        $title .= $this->getLookupBox();
        JToolBarHelper::title(JText::_($title), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Search] '));

        $bar = & JToolBar::getInstance('toolbar');

        $this->tip_reindex();
        //JToolbarHelper::custom('search','search','','Search',false);
        $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=search', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="toolbar-Link" title="Search Published Mas"><span class="icon-search"></span>Search</a>', '');
        
        JToolBarHelper::spacer('10');
              
        //$this->tip_published();
        JToolBarHelper::cancel('cancel', 'Close');
        
        //Include prototype
        viewHtmlAddPrototype();
        $document = JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/administrator/templates/bluestork/js/mas_location.js?v=3');
        $document->addStyleSheet(JURI::root(true) . '/media/system/css/calendar-jos.css', 'text/css', 'all', array('title' => 'green'));
        $document->addScript(JURI::root(true) . '/media/system/js/calendar.js');
        $document->addScript(JURI::root(true) . '/media/system/js/calendar-setup.js');

        $tpl = "searchform";
        $this->display($tpl);
    }

    function searchResults() {
        $title = JText::_('MAS <small><small>[Search]</small></small>');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Search] '));
        
        $title .= $this->getLookupBox();
        JToolBarHelper::title(JText::_($title), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Search] '));

        $bar = & JToolBar::getInstance('toolbar');

        $this->tip_reindex();

        if (isset($_GET['mas'])) {
            $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" onclick="reviseSearch();" href="#" title="Revise Search"><span class="icon-search"></span>Revise Search</a>', '');
        }

       
        $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=search', false);
        JToolbarHelper::custom('search','search','','New Search',false);
        //$bar->appendButton('Custom', '<a href="' . $href . '" title="Search Published Mas Items"><span class="icon-48-search"></span>New Search</a>', '');
        JToolBarHelper::spacer('10');

//Include prototype
        viewHtmlAddPrototype();

        $tpl = "searchresults";
        $this->display($tpl);
    }

    function build() {
        $title = JText::_('MAS <small><small>[Build]</small></small>');
        JToolBarHelper::title(JText::_($title), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Build] '));
        
        $this->tip_close();

        $tpl = 'build';
        $this->display($tpl);
    }

    function reindex() {
        $title = JText::_('MAS <small><small>[Update]</small></small>');
        $title .= $this->getLookupBox();
        JToolBarHelper::title(JText::_($title), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Update] '));

        $this->tip_search();
        $this->tip_published();
        $this->tip_close();

        $tpl = 'reindex';
        $this->display($tpl);
    }
    
    
        
	
	/*
	 * 
	 * Find & Rpelace tool
	 * functions
	 * 
	 */
	
	function frt_add()
	{
		
		JToolBarHelper::title( 'MAS Find & Replace [Search]', 'generic.png' );
                
                $document = & JFactory::getDocument();
		$document->setTitle(JText::_('[MAS] MAS Find & Replace - Add New Search'));
		
		$title = "Search in a specific column";
		$title_do = 'Search';
        $this->add_jquery();
		
		$bar = & JToolBar::getInstance('toolbar');	
		
		//search button
		JToolBarHelper::save('frt', 'Search');
		
		//last searched button
		$LastSearchedLink = 'index.php?option=com_gpo&controller=mas&task=frt&action=add'.
                            '&swap[from]='.urlencode($this->lastSearchedQuery->from).
                            '&swap[to]='.urlencode($this->lastSearchedQuery->to).
                            '&swap[column_name]='.urlencode($this->lastSearchedQuery->column_name).
                            '&swap[case_sensitive]='.urlencode($this->lastSearchedQuery->options);
		
		$href = JRoute::_( $LastSearchedLink,false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Last Searhed Query"><span class="icon-search"></span>Last Searched</a>', '' );
		
		//history button
		$href = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=frt&action=history',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="View Past Replaces History"><span class="icon-edit"></span>History</a>', '' );	
		
		JToolBarHelper::spacer(10);
		JToolBarHelper::divider();
		JToolBarHelper::spacer(10);
		
		$href = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=published',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close: Back to the MAS [Published] Page"><span class="icon-cancel"></span>Close</a>', '' );
		
		$tpl='frt_add';
		$this->display($tpl);
	}
	
    function frt_results()
	{
		
		
		JToolBarHelper::title( 'MAS Find & Replace [Results]', 'generic.png' );
                
                $document = & JFactory::getDocument();
		$document->setTitle(JText::_('Results - MAS Find & Replace'));
                
		$title = 'Replace Selected Records';
		$this->add_jquery();
		
		//JToolBarHelper::back();
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=frt&action=add',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close: Back to the Search Form of Mas Find & Replace"><span class="icon-cancel"></span>Cancel</a>', '' );
		
		JToolBarHelper::spacer(10);
		JToolBarHelper::divider();
		JToolBarHelper::spacer(10);
		
		JToolBarHelper::publishList( 'frt_replace', $title );
			
		$tpl='frt_results';
		$this->display($tpl);
	}
	
	
    function frt_history()
	{
		
		JToolBarHelper::title( 'MAS Find & Replace [History]', 'generic.png' );
                
                $document = & JFactory::getDocument();
		$document->setTitle(JText::_('History - MAS Find & Replace'));
                
                
		$bar = & JToolBar::getInstance('toolbar');
	  	
		//new search button
		$href = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=frt&action=add',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="New Search: Perform New Search on Mas"><span class="icon-search"></span>New Search</a>', '' );
	    
		//Last searched button
		$LastSearchedLink = 'index.php?option=com_gpo&controller=mas&task=frt&action=add'.
                            '&swap[from]='.urlencode($this->lastSearchedQuery->from).
                            '&swap[to]='.urlencode($this->lastSearchedQuery->to).
                            '&swap[column_name]='.urlencode($this->lastSearchedQuery->column_name).
                            '&swap[case_sensitive]='.urlencode($this->lastSearchedQuery->options);
		
		$href = JRoute::_( $LastSearchedLink,false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Last Searhed Query"><span class="icon-search"></span>Last Searched</a>', '' );
		
		
		//close button
		$href = JRoute::_( 'index.php?option=com_gpo&controller=mas',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close: Back to the Mas [Published] Page"><span class="icon-cancel"></span>Close</a>', '' );
	
		$tpl='frt_history';
		$this->display($tpl);				
	}
	
    function add_prototype()
	{
	    /* Moved it in here to try and seperate out the html + javascript a little more */		
		$document = &JFactory::getDocument();
    $document->addScript( JURI::root(true).'/includes/js/joomla.javascript.js');
		$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');		
		$mootools = JURI::root(true).'/media/system/js/mootools.js';
		if( isset( $document->_scripts[$mootools]))
		{
			unset( $document->_scripts[$mootools]);
		}
	}
    
    function add_jquery() 
    {
        $document = &JFactory::getDocument();
        $document->addScript(JURI::root(true).'/media/system/js/jquery1.6.2.js');
    }

    function export() {
        $title = JText::_('MAS <small><small>[Export]</small></small>');
        JToolBarHelper::title(JText::_($title), 'generic.png');
       
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Export]'));
        
        $bar = & JToolBar::getInstance('toolbar');

        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" id="submit-download" href="#" title="Export first, then download, unzip, upload to NoCigar"><span class="icon-save"></span>Download</a>', '');
        JToolBarHelper::spacer('10');


        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" id="submit-export-nocigar" href="#" title="Export 4 NoCigar"><span class="icon-download"></span>Export 4 NoCigar</a>', '');
        JToolBarHelper::spacer('10');

        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" id="submit-export" href="#" title="Process Export"><span class="icon-download"></span>Export</a>', '');
        JToolBarHelper::spacer('10');
        $this->tip_close();

        $tpl = 'export';
        $this->display($tpl);
    }

    function maillist() {
        $title = JText::_('MAS <small><small>[Mail List] <span style="font-size:11px;font-weight:400;color:#666666;">Mas articles queued for posting to daily E-mail digests</span></small></small>');
        JToolBarHelper::title(JText::_($title), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Mail List] MAS articles queued for posting to daily E-mail digests'));

        $bar = & JToolBar::getInstance('toolbar');

        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" id="submit-send" href="#" title="Send Emails"><span class="icon-mail"></span>Publish All to Digest</a>', '');
        JToolBarHelper::spacer('10');
        $this->tip_published();
        $this->tip_unpublished();
        $this->tip_close();

//Include prototype
        viewHtmlAddPrototype();
        $tpl = 'email_public';
        $this->display($tpl);
    }

    function mailOutSummary() {
        $title = JText::_('MAS <small><small>[Mail Out Summary]</small></small>');
        JToolBarHelper::title(JText::_($title), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('MAS [Mail Out Summary]'));

	$this->tip_published();
	$this->tip_unpublished();
        $this->tip_close();

        $tpl = 'mailout_summary';
        $this->display($tpl);
    }

    function tip_reindex() {
        if ($this->can_publish && $this->shouldReIndex) {
            $bar = & JToolBar::getInstance('toolbar');
            JToolbarHelper::custom('reindex','redo','','Update',false);
            $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=reindex', false);
            //$bar->appendButton('Custom', '<a href="' . $href . '" title="Update the Mas index to show any recent changes"><span class="icon-32-upload"></span>Update</a>', '');
            JToolBarHelper::spacer('10');
        }
    }

//    function tip_mailout() {
//        if ($this->can_publish) {
//            $bar = & JToolBar::getInstance('toolbar');
//            $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=maillist', false);
//            $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Mail latest articles"><span class="icon-mail"></span>EMail Queue (' . ($this->mailPending ? $this->mailPending : 0) . ')</a>', '');
//            JToolBarHelper::spacer('10');
//        }
//    }

    function tip_close() {
        $bar = & JToolBar::getInstance('toolbar');
        if (strpos($_SERVER['HTTP_REFERER'], 'task=unpublished') === false) {
            $href = JRoute::_('index.php?option=com_gpo&controller=mas', false);
        } else {
            $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=unpublished', false);
        }
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close without saving"><span class="icon-cancel"></span>Close</a>', '');
    }

    function tip_search() {
        $bar = & JToolBar::getInstance('toolbar');
        JToolBarHelper::custom('search', 'search', '', 'Search', false);
        $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=search', false);
        //$bar->appendButton('Custom', '<a href="' . $href . '" title="Search Published Mas Items"><span class="icon-48-search"></span>Search</a>', '');
        JToolBarHelper::spacer('10');
    }

//    function twitteroauths() {
//        $bar = & JToolBar::getInstance('toolbar');
//        $href = JRoute::_('index.php?option=com_gpo&controller=twitteroauths', false);
//        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Add twitter and bitly api"><span class="icon-edit"></span>Twitter Settings</a>', '');
//        //JToolBarHelper::spacer('5');
//    }

    function tip_find_replace()
	{
		$bar = & JToolBar::getInstance('toolbar');
                                    JToolBarHelper::custom('frt', 'search', '', 'Find & Replace', false);
		$href = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=frt', false );
		//$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Mas: Find & Replace"><span class="icon-48-search"></span>Find & Replace</a>', '' );
		JToolBarHelper::spacer('10');		
	}
    
    function tip_back_to_search() {
        if (!isset($_COOKIE['gpo_admin_mas_last_search']))
            return;
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=search&back=1', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Back to Last Search Results"><span class="icon-search"></span>Last Searched</a>', '');
        JToolBarHelper::spacer('10');
    }

    function tip_published() {
        $bar = & JToolBar::getInstance('toolbar');
        JToolbarHelper::custom('published','list-view','','Published List',false);
        $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=published', false);
        //$bar->appendButton('Custom', '<a href="' . $href . '" title="List of Published Mas Items"><span class="icon-32-publish"></span>Published List</a>', '');
        JToolBarHelper::spacer('10');
    }

   function tip_build() {
        if ($this->can_publish) {
            $bar = & JToolBar::getInstance('toolbar');
            JToolbarHelper::custom('build','plus','','Build',false);
             $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=build', false);
            //$bar->appendButton('Custom', '<a href="' . $href . '" title="Rebuild the entire Mas database from an imported text file"><span class="icon-32-new"></span>Build</a>', '');
        }
     }

    function tip_unpublished() {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=unpublished', false);
        $html_total = ( $this->unpublishedTotal > 0 ) ? ' (' . $this->unpublishedTotal . ')' : '';
        JToolBarHelper::custom('unpublished', 'list-view', '', 'Unpublished Queue' . $html_total, false);
        //$bar->appendButton('Custom', '<a href="' . $href . '" title="Go to the Unpublished Mas queue"><span class="icon-32-unpublish"></span>Unpublished Queue' . $html_total . '</a>', '');
        JToolBarHelper::spacer('10');
    }

    function tip_create_new() {
        $bar = & JToolBar::getInstance('toolbar');
        JToolBarHelper::custom('create', 'new', '', 'Create New', false);
        $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=create', false);
        //$bar->appendButton('Custom', '<a href="' . $href . '" title="Create a new Mas record"><span class="icon-32-new"></span>Create New</a>', '');
        JToolBarHelper::spacer('10');
    }

    function tip_create_topic() {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=topics&task=create', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="createTopic" title="GoTo Topic"><span class="icon-new"></span>Go to Topic</a>', '');
        JToolBarHelper::spacer('10');
    }

}
