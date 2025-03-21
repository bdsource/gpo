<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );


class GpoViewLocations extends JViewLegacy
{
		function display($tpl = null)
	{
		GpoHelper::addSubmenu('locations');
        if (JVERSION >= '3.0')
        {
           // $this->sidebar = JHtmlSidebar::render();
        }
		parent::display( $tpl );
	}
	
	function cpanel()
	{
		JToolBarHelper::title(  'Location <small><small>[Start]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location [Start]' ));
		
		$tpl='cpanel';
		$this->display($tpl);
	}
	
	
	
	function admin_location_links()
	{
		JToolBarHelper::title(  'Location <small><small>[Admin Locations Link]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location [Admin Locations Link]' ));
//New Location		
		$this->tip_create_new_location();
//Save Page info
		$bar = & JToolBar::getInstance('toolbar');
		//$bar->appendButton( 'Link', 'save', 'save','#',false,true );
    $bar->appendButton( 'Custom', '<a href="#save" id="toolbar-Link" ><span class="icon-32-save"></span>Save</a>', '');
//Close page
		$this->tip_close();
		
		viewHtmlAddPrototype();
		$tpl='admin_location_links';
		$this->display($tpl);	
	}
	
	
	
	function admin_region_list()
	{
		JToolBarHelper::title(   'Location <small><small>[Admin Region List]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location [Admin Region List]' ));
		
//New Location		
		$this->tip_create_new_location();
		
		$bar = & JToolBar::getInstance('toolbar');
		//$bar->appendButton( 'Link', 'save', 'save','#',false,true );
    $bar->appendButton( 'Custom', '<a href="#save" id="toolbar-Link" ><span class="icon-32-save"></span>Save</a>', '');
//Close page
		$this->tip_close();
		
		viewHtmlAddPrototype();
		$tpl='admin_region_list';
		$this->display($tpl);	
	}
	
	
	
	function admin_country_list()
	{
		JToolBarHelper::title(   'Location <small><small>[Admin Country List]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location [Admin Country List]' ));
		
//New Location		
		$this->tip_create_new_location();
				
		$bar = & JToolBar::getInstance('toolbar');
		//$bar->appendButton( 'Link', 'save', 'save','#',false,true );
		$bar->appendButton( 'Custom', '<a href="#save" id="toolbar-Link" ><span class="icon-32-save"></span>Save</a>', '');
//Close page
		$this->tip_close();
		
		viewHtmlAddPrototype();
		$tpl='admin_country_list';
		$this->display($tpl);	
	}
	
	
	function public_region_list()
	{
		JToolBarHelper::title(   'Location <small><small>[Public Region List]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location [Public Region List]' ));

//New Location		
		$this->tip_create_new_location();
				
		$bar = & JToolBar::getInstance('toolbar');
		//$bar->appendButton( 'Link', 'save', 'save','#',false,true );
    $bar->appendButton( 'Custom', '<a href="#save" id="toolbar-Link" ><span class="icon-32-save"></span>Save</a>', '');
//Close page
		$this->tip_close();
				
		viewHtmlAddPrototype();
		$tpl='public_region_list';
		$this->display($tpl);	
	}
	
	
	
	function public_country_list()
	{
		JToolBarHelper::title(   'Location <small><small>[Public Country List]</small></small>', 'generic.png' );
		
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location [Public Country List]' ));
//New Location		
		$this->tip_create_new_location();
				
		$bar = & JToolBar::getInstance('toolbar');
		//$bar->appendButton( 'Link', 'save', 'save','#',false,true );
		$bar->appendButton( 'Custom', '<a href="#save" id="toolbar-Link" ><span class="icon-32-save"></span>Save</a>', '');
//Close page
		$this->tip_close();

		viewHtmlAddPrototype();
		$tpl='public_country_list';
		$this->display($tpl);	
	}
	
	
	function location_list()
	{
		JToolBarHelper::title( 'Location <small><small>[Edit Location List]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location [Edit Location List]' ));
		
//New Location		
		$this->tip_create_new_location();
				
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Link', 'save', 'save','#',false,true );
//Close page
		$this->tip_close();
				
		$tpl='location_list';
		$this->display($tpl);
	}
	
    function location_translate()
	{
		JToolBarHelper::title(   'Location <small><small>[Translate Location Names]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Translate Location Names' ));
		
        //Close page
		$this->tip_close();
				
		$tpl='location_translate';
		$this->display($tpl);
	}
    
	
	function admin_location_new()
	{
		JToolBarHelper::title(   'Location <small><small>[New]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location [New]' ));

//Close page
		$this->tip_close();

		$tpl='add';
		$this->display($tpl);
	}
	
	
	function admin_location_delete()
	{
		JToolBarHelper::title(   'Location <small><small>[Delete]</small></small>', 'generic.png' );
		
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location [Delete]' ));
//Close page
		$this->tip_close();		
		$tpl='delete';
		$this->display($tpl);
	}	
	

	function create_legal_list()
	{
		JToolBarHelper::title(   'Location <small><small>[Legal List]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location [Legal List]' ));

		$bar = & JToolBar::getInstance('toolbar');		
		$bar->appendButton( 'Custom', '<a href="#" id="save-changes" title="Save Legal List." ><span class="icon-32-save"></span>Save</a>', '' );
		JToolBarHelper::spacer('10');
		
//Close page
		$this->tip_close();		
		$tpl='create_legal_list';
		$this->display($tpl);		
	}
	
		
	function checklist()
	{
		JToolBarHelper::title(   'Location <small><small>[Checklist]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location [Checklist]' ));
		
//Close page
		$this->tip_close();		
		$tpl='checklist';
		$this->display($tpl);		
	}
	
	
	function tip_create_new_location()
	{
		$bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=locations&task=location_new',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Add a New Location"><span class="icon-32-new"></span>New Location</a>', '' );				
	}
	
	
	function tip_close()
	{
		$bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=locations',false );
                $bar->appendButton('Custom',  '<a class="btn btn-default toolbar-btn-padding" href="' . 
                        $href . '" title="Close"><span class="icon-cancel"></span>Close</a>', '');
	} 
/*	
	function edit()
	{
		
		$oQuote =& $this->oQuote;

		$isNew		= ( $oQuote->id < 1 );

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		$text = JText::_( $text );
		$title = JText::_( 'Location' ). ' <small><small>[ ' . $text.' ]</small></small>';
		if( isset( $oNews->live_id ) && !empty( $oQuote->live_id ) )
		{
			$title .= ' <small><small>Live Id: ' . $oQuote->live_id . '</small></small>';
		}
		JToolBarHelper::title(   $title );
		//JToolBarHelper::save();
		$bar = & JToolBar::getInstance('toolbar');
		if( $this->oNews->id > 0 )
		{
			$href = JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=publish&id=' . $oQuote->id );
			$bar->appendButton( 'Custom', '<a href="' . $href . '" id="publish_news"><span class="icon-32-publish" title="Publish"></span>
	Publish</a>', '' );
			JToolBarHelper::spacer('10');
		}
		
		$bar->appendButton( 'Link', 'save', 'save','#',false,true );
		
		
		JToolBarHelper::cancel( 'cancel', 'Close' );
		$tpl = 'edit';
		$this->assignRef('oQuote',$oQuote );
		$this->display($tpl);
	}

	function showall()
	{
		JToolBarHelper::title(   JText::_( 'Gun Policy Manager - Quotes' ), 'generic.png' );
		
		JToolBarHelper::addNewX( 'create', 'New Record');
		JToolBarHelper::spacer('10');
		
		$bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=unpublished' );
		$html_total = ( $this->unpublishedTotal > 0 ) ? '( ' . $this->unpublishedTotal . ' )' : '';
		$bar->appendButton( 'Custom', '<a href="' . $href . '"><span class="icon-32-unpublish" title="Unpublished"></span>Unpublished List' . $html_total . '</a>', '' );
		
		$tpl='showall';
		$this->display($tpl);
	}
*/
    
     /*
     * Group related methods
     * 
     */

    function admin_group_new() 
    {
        JToolBarHelper::title('Create <small>[New]</small> Group', 'generic.png');
        
        $document = &JFactory::getDocument();
        $document->setTitle(JText::_( 'Create [New]' ));

        $bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=locations&task=group_list' );
		$bar->appendButton('Link', 'forward', 'View All Groups', $href);

        $this->tip_close();

        $tpl = 'add_group';
        $this->display($tpl);
    }
    
     /*
     * Group related methods
     * 
     */

    function admin_state_or_province_new() 
    {
        JToolBarHelper::title('Create <small>[New]</small> Group', 'generic.png');
        
        $document = &JFactory::getDocument();
        $document->setTitle(JText::_( 'Create [New]' ));

        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_( 'index.php?option=com_gpo&controller=locations&task=states_list' );
        $bar->appendButton('Link', 'forward', 'View All States and Provinces', $href);
        $this->tip_close();

        $tpl = 'add_state_or_province';
        $this->display($tpl);
    }
    
    function states_list()
    {
        JToolBarHelper::title( 'Location States/Provinces <small>[States/Provinces List]</small>', 'generic.png' );       
        $document = &JFactory::getDocument();
        $document->setTitle(JText::_( 'Location [States/Provinces List]' ));
	$bar = & JToolBar::getInstance('toolbar');
	$href = JRoute::_( 'index.php?option=com_gpo&controller=locations&task=state_or_province_new' );
	$bar->appendButton('Link', 'new', 'Create New State/Province', $href);

	$this->tip_close();
	$tpl='states_list';
	$this->display($tpl);
    }
    
    function group_list()
    {
		JToolBarHelper::title( 'Location Group <small>[Group List]</small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location Group [Group List]' ));
		
        $bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=locations&task=group_new' );
		$bar->appendButton('Link', 'new', 'Create New Group', $href);

		$this->tip_close();		
		$tpl='group_list';
		$this->display($tpl);
    }
    
    function group_edit()
	{
		JToolBarHelper::title( 'Location Group <small>[Edit Group]</small>', 'generic.png' );		
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Location Group [Edit Group]' ));
		
        JToolBarHelper::save('group_edit');
        JToolBarHelper::apply('group_edit');
        
        //Preview DP
		$this->tip_preview_dp();
        
        $bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=locations&task=group_list' );
		$bar->appendButton('Link', 'forward', 'View All Groups', $href);

		$this->tip_close();
				
		$tpl='group_edit';
		$this->display($tpl);
	}
    
    
    function tip_preview_dp()
	{
		$bar      = & JToolBar::getInstance('toolbar');
        $liveSite = JURI::root();
		$url = JRoute::_( $liveSite . 'index.php?option=com_gpo&task=preview&' . 'group=' . Joomla\CMS\Factory::getApplication()->getInput()->get('groupid', false) );
        
	    $href = "javascript:popup=window.open('" . $url . "','GunPolicy.org Data Page - Preview','toolbar=no,
                location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600'); popup.focus();";
		
		$bar->appendButton( 'Custom', '<a  href="' . $href . '" title="Preview Group DP"><span class="icon-32-preview"></span>Preview Group DP</a>', '' );
	}

}
