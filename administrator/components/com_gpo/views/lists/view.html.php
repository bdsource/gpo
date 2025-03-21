<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );


class GpoViewLists extends JViewLegacy
{
   function display($tpl = null)
	{
		GpoHelper::addSubmenu('lists');
        if (JVERSION >= '3.0')
        {
            //$this->sidebar = JHtmlSidebar::render();
        }
		parent::display( $tpl );
	}
	
	function pick()
	{
		JToolBarHelper::title(   'Lists <small><small>[Choose a Drop-down]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Lists [Choose a Drop-down]' ));
						
		$tpl='pick';
		$this->display($tpl);		
	}
	
	
	function view_by_type()
	{
		JToolBarHelper::title(   'Lists <small><small>[Unique Entries for &lsquo;' . $this->type. '&rsquo;]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Lists [Unique Entries for &lsquo;'.$this->type.'&rsquo;]' ));
	
		$this->tip_order();
		
		$this->tip_unique();
		$this->tip_unique_all();
		
		$this->tip_main_index();
        // Add close button into the list
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_( 'index.php?option=com_gpo&controller=lists',false );
        $bar->appendButton( 'Custom', '<a href="' . $href . '" title="Close: Back to the News [Published] Page"><span class="icon-32-cancel"></span>Close</a>', '' );
		$this->add_prototype();
		$tpl='view_type';
		$this->display($tpl);		
	}
	
	function view_by_type_not()
	{
		JToolBarHelper::title(   'Lists <small><small>[Entries Not in the current Drop down list for &lsquo;' . $this->type. '&rsquo;]</small></small>', 'generic.png' );
	
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Lists [Entries Not in the current Drop down list for &lsquo;'.$this->type.'&rsquo;]' ));
                
		$this->tip_order();
		
		$this->tip_unique();
		$this->tip_unique_all();		
		$this->tip_main_index();
		$this->add_prototype();
		
		$tpl='view_by_type_not_in_list';
		$this->display($tpl);		
	}
	
//start - remove this	
	function view_by_type_unique()
	{
		JToolBarHelper::title(   'Lists <small><small>[Unique Entries for &lsquo;' . $this->type. '&rsquo;]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Lists [Unique Entries for &lsquo;'.$this->type.'&rsquo;]' ));
	
		$this->tip_order();
		
		$this->tip_unique();
		$this->tip_unique_all();		
		$this->tip_main_index();
		$this->add_prototype();
		
		$tpl='view_by_type_unique';
		$this->display($tpl);		
	}
//end - remove this		
	
	
	function view_by_type_order()
	{
		JToolBarHelper::title(   'Lists <small><small>[Drop-down List for &lsquo;' . $this->type. '&rsquo;]</small></small>', 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'Lists [Drop-down List for &lsquo;'.$this->type.'&rsquo;]' ));
	
		$title = "Save Order";
		$href = JRoute::_( '#',false );
		$bar = & JToolBar::getInstance('toolbar');
		
		$bar->appendButton( 'Custom', '<a href="#" id="action-save" title="' . $title . '"><span class="icon-32-save"></span>Save</a>', '' );
		JToolBarHelper::spacer('10');
		
		$this->tip_unique();
		
		$this->tip_main_index();
		$this->add_prototype();
		$tpl='view_by_type_order';
		$this->display($tpl);		
	}

	function editstaff(){
	    JToolBarHelper::title(   'Lists <small><small>[Staff Initials]</small></small>', 'generic.png' );
            
            $document = &JFactory::getDocument();
            $document->setTitle(JText::_( 'Lists [Staff Initials]' ));
            
	    $bar = &JToolBar::getInstance('toolbar');
	    JToolBarHelper::addNew('addstaff','Add staff');
	    $href = JRoute::_('index.php?option=com_gpo&controller=lists',false);
	    $bar->appendButton('Custom','<a href="'.$href.'"><span class="icon-32-cancel"></span>Close</a>','');
	    $tpl = 'editstaff';
	    $this->display($tpl);
	}
	function addstaff(){
	    JToolBarHelper::title(   'Lists <small><small>[Add Staff Initial]</small></small>', 'generic.png' );
            
            $document = &JFactory::getDocument();
            $document->setTitle(JText::_( 'Lists [Add Staff Initial]' ));
            
	    $bar = &JToolBar::getInstance('toolbar');
	    //$href = JRoute::_('index.php?option=com_gpo&controller=lists&task=addstaff',false);
	    //$bar->appendButton('Custom','<a href="'.$href.'"><span class="icon-32-save"></span>Save</a>','');
	    JToolBarHelper::save('addstaff','Save');
	    $href = JRoute::_('index.php?option=com_gpo&controller=lists&task=editstaff',false);
	    $bar->appendButton('Custom','<a href="'.$href.'"><span class="icon-32-cancel"></span>Close</a>','');
	    $tpl = 'addstaff';
	    $this->display($tpl);
	}
	
	
	function tip_order()
	{
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=lists&task=order&type=' . $this->type,false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Drop-down List"><span class="icon-32-preview"></span>Drop-down List</a>', '' );
		JToolBarHelper::spacer('10');
	}

	
	function tip_main_index()
	{
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=lists',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Show all Lists"><span class="icon-32-preview"></span>Lists</a>', '' );
		JToolBarHelper::spacer('10');
	}
	
//all not in unique list
	function tip_unique_all()
	{
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=lists&type=' . $this->type . '&task=viewall',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Show system Wide( slow ) Entries which are not in the Unique list"><span class="icon-32-preview"></span>Non Unique Entries System Wide (slow)</a>', '' );
		JToolBarHelper::spacer('10');
	}
	
	function tip_unique()
	{
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=lists&type=' . $this->type . '&task=view',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Show unique entries"><span class="icon-32-preview"></span>Unique Entries</a>', '' );
		JToolBarHelper::spacer('10');
	}

	function add_prototype()
	{
	/* Moved it in here to try and seperate out the html + javascript a little more */		
		$document = &JFactory::getDocument();
		$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');		
		$mootools = JURI::root(true).'/media/system/js/mootools.js';
		if( isset( $document->_scripts[$mootools]))
		{
			unset( $document->_scripts[$mootools]);
		}
	}
}

