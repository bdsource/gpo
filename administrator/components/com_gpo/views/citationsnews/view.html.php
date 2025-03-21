<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );


class GpoViewCitationsnews extends JViewLegacy
{
	var $type='news';
	
	function display($tpl = null)
	{
		GpoHelper::addSubmenu('ncite');
        if (JVERSION >= '3.0')
        {
            //$this->sidebar = JHtmlSidebar::render();
        }
		parent::display( $tpl );
	}
	
	function edit()
	{
//title
		$this->isNew	= ( $this->oCitation->id < 1 );
		$text = $this->isNew ? JText::_( '[Create New]' ) : JText::_( '[Edit]' );
		$text = JText::_( $text );
		$title = JText::_( 'Citations: News (NCites) <small>' . $text.'</small>' );
		if( isset( $this->oCitation->live_id ) && !empty( $this->oCitation->live_id ) )
		{
			$title .= ' <small><small>Live Id: ' . $this->oCitation->live_id . '</small></small>';
		}
		JToolBarHelper::title(   JText::_( $title ), 'generic.png' );
                
                $document = & JFactory::getDocument();
	        $document->setTitle(JText::_(strip_tags($title)));
                
		$bar = & JToolBar::getInstance('toolbar');		
//preview
		if( empty( $this->isNew ) )
		{
			$url = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type .'&task=preview&id=' . $this->oCitation->id, false );
			$href = "javascript:popup=window.open('" . $url . "','GunPolicyCitation - Preview','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=650,height=600'); popup.focus();";
			$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Any new changes will not show up on the preview till they have been saved." ><span class="icon-preview" title="Preview"></span>Preview</a>', '' );
		}else{
			$href = JRoute::_( '#',false );
			$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="clear_form" title="Clear all text from this form"><span class="icon-trash"></span>Clear Form</a>', '' );
			JToolBarHelper::spacer('10');			
		}
		
//Save and Create Another
		$title = ( $this->isNew ) ? "Save this new citation, then create another" : "Save this citation, then create another";
		$href = JRoute::_( '#',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="save_create_another" title="' . $title . '"><span class="icon-save"></span>Save + Create</a>', '' );
		JToolBarHelper::spacer('10');
		
//save and publish
		$title = ( $this->can_publish ) ? "Save and Publish this citation" : "Save this citation and Goto Approve for Publishing";
		$href = JRoute::_( '#save_publish' );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="item_publish" title="' . $title . '"><span class="icon-publish"></span>Save + Publish</a>', '' );	
//Published List		
		$this->tip_published();
//Close
		if( strpos( $_SERVER['HTTP_REFERER'], 'task=unpublished' ) === false )
		{
			$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type .'&task=published', false );
		}else{
			$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=unpublished',false );
		}
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="item_close" title="Close Without Saving"><span class="icon-cancel"></span>Close Without Saving</a>', '' );
		
/* Moved it in here to try and seperate out the html + javascript a little more */		
		$document = &JFactory::getDocument();
		$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');		
		$document->addScript( JURI::root(true).'/administrator/templates/bluestork/js/citations_location.js');
		$document->addStyleSheet( JURI::root(true).'/media/system/css/calendar-jos.css', 'text/css', 'all', array('title'=>'green'));
		$document->addScript( JURI::root(true).'/media/system/js/calendar.js');
		$document->addScript( JURI::root(true).'/media/system/js/calendar-setup.js');
		$mootools = JURI::root(true).'/media/system/js/mootools.js';
		if( isset( $document->_scripts[$mootools]))
		{
			unset( $document->_scripts[$mootools]);
		}
		$tpl = 'edit';
		$this->display($tpl);
	}



	function published()
	{
		$title =  'Citations: News <small><small>[Published]</small></small>' ;
                $title .= $this->getLookupBox();
		JToolBarHelper::title(   JText::_($title), 'generic.png' );
		
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_('Citations: News (NCites) [Published] '));
		
                //$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');
		$mootools = JURI::root(true).'/media/system/js/mootools.js';
		if( isset( $document->_scripts[$mootools]))
		{
			//unset( $document->_scripts[$mootools]);
		}
		$this->tip_reindex();		
		$this->tip_back_to_search();		
		$this->tip_search();
//frt
		$this->tip_find_replace();

//create
		$this->tip_create();
//unpublished
		$this->tip_unpublished();		

		$tpl='published';					
		$this->display($tpl);
	}	

	
	
	function unpublished()
	{
		$title =  'Citations: News (NCites) <small>[Unpublished]</small>' ;
        $title .= $this->getLookupBox();
		JToolBarHelper::title(   JText::_($title), 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_('Citations: News (NCites) [Unpublished] '));
                
		$this->tip_reindex();		
		$this->tip_back_to_search();		
		$this->tip_search();		
//create
		$this->tip_create();
//published
		$this->tip_published();	
//empty unpublished
		$bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=unpublished_empty',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="unpublish_empty" title="Delete all the unpublished Citations in this queue"><span class="icon-trash"></span>Empty Queue</a>', '' );
		JToolBarHelper::spacer('10');
		
		$tpl='unpublished';
		$this->display($tpl);
	}
	
	

	function empty_unpublished()
	{
		JToolBarHelper::title(   JText::_( 'Citations: News <small><small>[Empty Unpublished]</small></small>' ), 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_('Citations: News (NCites) [Empty Unpublished] '));

		$this->tip_close();					

		$tpl = 'empty_unpublished';
		$this->display($tpl);
	}
	
	
	
	function publish()
	{
		JToolBarHelper::title(   JText::_( 'Citations: News (NCites) <small>[Publish]</small>' ), 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_('Citations: News (NCites) [Publish] '));
                
		$bar = & JToolBar::getInstance('toolbar');	
//preview
		$url = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type .'&task=preview&id=' . $this->oCitation->id, false );
		$href = "javascript:popup=window.open('" . $url . "','GunPolicyCitation - Preview','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=650,height=600'); popup.focus();";
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '"><span class="icon-preview" title="Preview"></span>Preview</a>', '' );	
//edit	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=edit&id=' . $this->oCitation->id, false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '"><span class="icon-edit" title="Edit Citation"></span>Edit Citation</a>', '' );	
//close		
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=unpublished', false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '"><span class="icon-cancel" title="Close"></span>Close Without Saving</a>', '' );			
		
		$document = &JFactory::getDocument();
		$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');
		$mootools = JURI::root(true).'/media/system/js/mootools.js';
		if( isset( $document->_scripts[$mootools]))
		{
			unset( $document->_scripts[$mootools]);
		}
		$tpl = 'publish';
		$this->display($tpl);
	}
	
	
	
	function delete()
	{
		JToolBarHelper::title(   JText::_( 'Citations: News <small><small>[Delete]</small></small>' ), 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_('Citations: News (NCites) [Delete] '));
                
		$bar = & JToolBar::getInstance('toolbar');	
//Delete
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="#" id="item_delete"><span class="icon-delete" title="Delete News Item"></span>Delete</a>', '' );	
//close		
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=published',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '"><span class="icon-cancel" title="Close without deleting"></span>Close</a>', '' );

		$tpl = 'delete';
		$this->display($tpl);
	}
	
	
	
	function preview()
	{
		$tpl = 'preview';
		$this->display($tpl);
	}






	function search()
	{
		$title = JText::_( 'Citations: News <small><small>[Search]</small></small>' );
		JToolBarHelper::title(   JText::_( $title ), 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_('Citations: News (NCites) [Search] '));
		
		$bar = & JToolBar::getInstance('toolbar');

		$this->tip_reindex();
		
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=search',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="toolbar-Link" title="Search Published NCite"><span class="icon-search"></span>Search</a>', '' );
		
		//$this->tip_published();
        $this->tip_close();
		
		$tpl = "searchform";
		$this->display($tpl);		
	}
	
	

	function searchResults()
	{
		$title = JText::_( 'Citations: News <small><small>[Search]</small></small>' );
		JToolBarHelper::title(   JText::_( $title ), 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_('Citations: News (NCites) [Search] '));
		
		$bar = & JToolBar::getInstance('toolbar');

		$this->tip_reindex();
						
		if( isset( $_GET['citation'] ) )
		{
			$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" onclick="reviseSearch();" href="#" title="Revise Search"><span class="icon-search"></span>Revise Search</a>', '' );						
		}


		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=search',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Search Published NCite"><span class="icon-search"></span>New Search</a>', '' );
		JToolBarHelper::spacer('10');

		$document = &JFactory::getDocument();
		$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');
		$mootools = JURI::root(true).'/media/system/js/mootools.js';
		if( isset( $document->_scripts[$mootools]))
		{
			unset( $document->_scripts[$mootools]);
		}		
					
		$tpl = "searchresults";
		$this->display($tpl);		
	}
	

	
	function reindex()
	{
		$title = JText::_( 'Citations: News <small><small>[Update]</small></small>' );
		JToolBarHelper::title(   JText::_( $title ), 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_('Citations: News (NCites) [Update] '));
		
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
				
		JToolBarHelper::title( 'NCites Find & Replace [Search]', 'generic.png' );
                
                $document = & JFactory::getDocument();
		$document->setTitle(JText::_('[NCites] NCites Find & Replace - Add New Search'));
		
		$title = "Search in a specific column";
		$title_do = 'Search';
		$this->add_jquery();
		
		$bar = & JToolBar::getInstance('toolbar');	
		
		//search button
		JToolBarHelper::save('frt', 'Search');
		
		//last searched button
		$LastSearchedLink = 'index.php?option=com_gpo&controller=citations&type=news&task=frt&action=add'.
                            '&swap[from]='.urlencode($this->lastSearchedQuery->from).
                            '&swap[to]='.urlencode($this->lastSearchedQuery->to).
                            '&swap[column_name]='.urlencode($this->lastSearchedQuery->column_name).
                            '&swap[case_sensitive]='.urlencode($this->lastSearchedQuery->options);
		
		$href = JRoute::_( $LastSearchedLink,false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Last Searhed Query"><span class="icon-search"></span>Last Searched</a>', '' );
		
		//history button
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=news&task=frt&action=history',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="View Past Replaces History"><span class="icon-edit"></span>History</a>', '' );	
		
		JToolBarHelper::spacer(10);
		JToolBarHelper::divider();
		JToolBarHelper::spacer(10);
		
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=news&task=published',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close: Back to the NCite [Published] Page"><span class="icon-cancel"></span>Close</a>', '' );
		
		$tpl='frt_add';
		$this->display($tpl);
	}
	
    function frt_results()
	{
		
		JToolBarHelper::title( 'NCites Find & Replace [Results]', 'generic.png' );
                
                $document = & JFactory::getDocument();
		$document->setTitle(JText::_('Results - NCites Find & Replace'));
                
		$title = 'Replace Selected Records';
		$this->add_jquery();
		
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=news&task=frt&action=add',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close: Back to the Search Form of NCites Find & Replace"><span class="icon-cancel"></span>Cancel</a>', '' );
		
		JToolBarHelper::spacer(10);
		JToolBarHelper::divider();
		JToolBarHelper::spacer(10);
		
		JToolBarHelper::publishList( 'frt_replace', $title );
			
		$tpl='frt_results';
		$this->display($tpl);
	}
	
	
    function frt_history()
	{
		
		JToolBarHelper::title( 'NCites Find & Replace [History]', 'generic.png' );
                
                $document = & JFactory::getDocument();
		$document->setTitle(JText::_('History - NCites Find & Replace'));
                
		$bar = & JToolBar::getInstance('toolbar');
	  	
		//new search button
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=news&task=frt&action=add',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="New Search: Perform New Search on News"><span class="icon-search"></span>New Search</a>', '' );
	    
		//last searched button
		$LastSearchedLink = 'index.php?option=com_gpo&controller=citations&type=news&task=frt&action=add'.
                            '&swap[from]='.urlencode($this->lastSearchedQuery->from).
                            '&swap[to]='.urlencode($this->lastSearchedQuery->to).
                            '&swap[column_name]='.urlencode($this->lastSearchedQuery->column_name).
                            '&swap[case_sensitive]='.urlencode($this->lastSearchedQuery->options);
		
		$href = JRoute::_( $LastSearchedLink,false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Last Searhed Query"><span class="icon-search"></span>Last Searched</a>', '' );
		
		//close button
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=news',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close: Back to the NCites [Published] Page"><span class="icon-cancel"></span>Close</a>', '' );
	
		$tpl='frt_history';
		$this->display($tpl);				
	}
	
	
    function add_jquery() {
        
        $document = &JFactory::getDocument();
        $document->addScript(JURI::root(true).'/media/system/js/jquery1.6.2.js');
    }
	
    
	function tip_published()
	{
		$bar = & JToolBar::getInstance('toolbar');		
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type .'&task=published', false );
		$html_total = ( $this->unpublishedTotal > 0 ) ? '( ' . $this->unpublishedTotal . ' )' : '';
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="List of Published Citations"><span class="icon-unpublish"></span>Published List</a>', '' );		
	}


    function tip_unpublished()
	{		
		$bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&task=unpublished&type=' . $this->type, false );
		$html_total = ( $this->unpublishedTotal > 0 ) ? '( ' . $this->unpublishedTotal . ' )' : '';
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '"><span class="icon-unpublish" title=" Go to the Unpublished Citations queue"></span>Unpublished Queue' . $html_total . '</a>', '' );
	}
	
		
	function tip_create()
	{
		$bar = & JToolBar::getInstance('toolbar');				
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=create',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Create a new citation"><span class="icon-new"></span>Create New</a>', '' );		
	}
	
	
	function tip_reindex()
	{
		if( $this->can_publish && $this->shouldReIndex )
		{
			$bar = & JToolBar::getInstance('toolbar');			
			$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type .'&task=reindex',false );
			$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Update the NCite index to show any recent changes"><span class="icon-upload"></span>Update</a>', '' );
			JToolBarHelper::spacer('10');			
		}
	}
	
	
	function tip_search()
	{
		$bar = & JToolBar::getInstance('toolbar');		
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type .'&task=search', false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Search Published NCite Items"><span class="icon-search"></span>Search</a>', '' );
		JToolBarHelper::spacer('10');		
	}

    function tip_find_replace()
	{
		$bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=news&task=frt', false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="NCites: Find & Replace"><span class="icon-search"></span>Find & Replace</a>', '' );
		JToolBarHelper::spacer('10');
	}

	function tip_back_to_search()
	{
		if( !isset( $_COOKIE[ $this->cookie_name_last_search ] ) ) return;
		$bar = & JToolBar::getInstance('toolbar');		
		$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type .'&task=search&back=1', false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Back to Last Search Results"><span class="icon-search"></span>Last Searched</a>', '' );
		JToolBarHelper::spacer('10');			
	}
	
	
	
	function tip_close()
	{
		$bar = & JToolBar::getInstance('toolbar');	
		if( strpos( $_SERVER['HTTP_REFERER'], 'task=unpublished' ) === false )
		{
			$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type,false );
		}else{
			$href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type .'&task=unpublished',false );
		}		
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close without saving"><span class="icon-cancel"></span>Close</a>', '' );
	}	
  
   /* function to add lookup form fields in view window */
    function getLookupBox() {
        //$id = $state=='unpublished'?Joomla\CMS\Factory::getApplication()->getInput()->get('id'):Joomla\CMS\Factory::getApplication()->getInput()->get('live_id');
       // $state = Joomla\CMS\Factory::getApplication()->getInput()->get('state','');
       	$state = Joomla\CMS\Factory::getApplication()->getInput()->get('state');
        $id = $this->currentId;
        if(empty($state)){
            //$state = (Joomla\CMS\Factory::getApplication()->getInput()->get('task','') == 'unpublished') ? 'unpublished' : 'published';
            $state = (Joomla\CMS\Factory::getApplication()->getInput()->get('task') == 'unpublished') ? 'unpublished' : 'published';
        }
        $name_input_field = $state=='unpublished'?'id':'live_id';
        $lookupbox = '<small><small>
                    <form style="padding-left:40px;display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                        <input type="hidden" name="task" value="lookup" />
                        <input type="hidden" name="controller" value="citations" />
                        <input type="hidden" name="type" value="news" />
                        <span>Lookup ID: NC<input type="text" name="'.$name_input_field.'" value="" style="width:50px;"/>
                        <input type="hidden" name="state" value="'.$state.'" />
                        <input type="submit" value="Go" style="margin-left:-5px;" /></span>
                    </form>
                    ';
        if (!empty($id)) {
            $lookupbox .= '<span><form style="padding-left:20px;display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                    <input type="hidden" name="task" value="lookup" />
                    <input type="hidden" name="controller" value="citations" />
                    <input type="hidden" name="lookupdirection" value="prev" />
                    <input type="hidden" name="type" value="news" />
                    <input type="hidden" name="'.$name_input_field.'" value="' . $id . '"/>
                    <input type="hidden" name="state" value="'.$state.'" />
                    <input type="submit" value="Prev" />
                </form>
                <form style="display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                    <input type="hidden" name="task" value="lookup" />
                    <input type="hidden" name="controller" value="citations" />
                    <input type="hidden" name="lookupdirection" value="next" />
                    <input type="hidden" name="type" value="news" />
                    <input type="hidden" name="'.$name_input_field.'" value="' . $id . '"/>
                    <input type="hidden" name="state" value="'.$state.'" />
                    <input type="submit" value="Next" />
                </form></span>
            ';
        }
        $lookupbox .= '</small></small>';
        return $lookupbox;
    }
  
    /* view function for lookup functionality */
    function lookup(){
        $state = Joomla\CMS\Factory::getApplication()->getInput()->get('state');
        $this->isNew	= ( $this->oCitation->id < 1 );
        $text = $this->isNew ? JText::_( '[New]' ) : JText::_( '[Edit]' );
        $text = JText::_( $text );
        $title = JText::_( 'Citations: News <small><small>' . $text.'</small></small>' );
        if( isset( $this->oCitation->live_id ) && !empty( $this->oCitation->live_id ) ){
          $title .= ' <small><small>Live Id: ' . $this->oCitation->live_id . '</small></small>';
        }
        $bar = & JToolBar::getInstance('toolbar');		
        JToolBarHelper::title(JText::_('Citations: News (NCites) <small>[Lookup]</small>' . $this->getLookupBox()), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Citations: News (NCites) [Lookup]'));
        
        $href = JRoute::_('index.php?option=com_gpo&controller=citations&task=edit&type='.Joomla\CMS\Factory::getApplication()->getInput()->get('type').'&id='.$this->oCitation->id.'&live_id=' .$this->oCitation->live_id, false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Open this News in Edit mode"><span class="icon-edit"></span>Edit</a>', '');
        $href = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type .'&task=published', false );
        $bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="item_close" title="Close"><span class="icon-cancel"></span>Close</a>', '' );
        
        $document = &JFactory::getDocument();
        $document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');		
        $document->addScript( JURI::root(true).'/administrator/templates/bluestork/js/citations_location.js');
        $document->addStyleSheet( JURI::root(true).'/media/system/css/calendar-jos.css', 'text/css', 'all', array('title'=>'green'));
        $document->addScript( JURI::root(true).'/media/system/js/calendar.js');
        $document->addScript( JURI::root(true).'/media/system/js/calendar-setup.js');
        $mootools = JURI::root(true).'/media/system/js/mootools.js';
        if( isset( $document->_scripts[$mootools])){
          unset( $document->_scripts[$mootools]);
        }
        $tpl = 'lookup';
        $this->display($tpl);
    }
}

