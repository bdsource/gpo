<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );


class GpoViewCleanup extends JViewLegacy
{
	
	function add()
	{
		$title = ( $this->task === 'add' ) ? 'Add':"Edit";
		JToolBarHelper::title(   'Cleanup <small><small>[' . $title .']</small></small>', 'generic.png' );
		
		$title = "Save to the system";
		$href = JRoute::_( '#',false );
		$bar = & JToolBar::getInstance('toolbar');
		
		$title_do = ( $this->task === 'add' ) ? 'Add':"Save";
		$bar->appendButton( 'Custom', '<a href="#" id="action-save" title="' . $title . '"><span class="icon-32-save"></span>' . $title_do . '</a>', '' );
		JToolBarHelper::spacer('10');
				
		$this->tip_main_index();

		$this->add_prototype();
		
		$tpl='add';
		parent::display($tpl);		
	}


	function all()
	{
		JToolBarHelper::title(   'Cleanup <small><small>[All]</small></small>', 'generic.png' );


/*
		$href = JRoute::_( 'index.php?option=com_gpo&controller=cleanup&task=picker',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Back to Table Picker"><span class="icon-32-publish"></span>Pick Table</a>', '' );

		$bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=cleanup&task=issues',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="View all current issues"><span class="icon-32-new"></span>All Issues ( Very slow to load )</a>', '' );
*/
		
		$this->tip_new();	
		$this->add_prototype();
		
		$tpl='all';
		parent::display($tpl);				
	}
	
	
	
	function results()
	{
		JToolBarHelper::title(   'Cleanup <small><small>[Results]</small></small>', 'generic.png' );
		
		$bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=cleanup&task=add',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Add"><span class="icon-32-new"></span>Add</a>', '' );

		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=cleanup',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="View  all items to be cleaned up"><span class="icon-32-cancel"></span>View all Cleanup</a>', '' );
		
		$tpl='results';
		parent::display($tpl);		
	}
	
	
	
	function issues()
	{
		JToolBarHelper::title(   'Cleanup <small><small>[Overview of fields which may need cleaning up]</small></small>', 'generic.png' );
		
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=cleanup',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="View  all items to be cleaned up"><span class="icon-32-cancel"></span>View all Cleanup</a>', '' );
					
		$tpl='results_all';
		parent::display($tpl);				
	}
	
	
	function viewByTable()
	{
		JToolBarHelper::title(   'Cleanup <small><small>[`' . $this->table . '`.`' . $this->field . '` has ' . $this->total . ' records need cleaning for <span style="font-size:200%;">' . $this->cleanup->from. '</span>]</small></small>', 'generic.png' );
		
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=cleanup',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="View  all items to be cleaned up"><span class="icon-32-cancel"></span>View all Cleanup</a>', '' );
					
		$tpl='results_all_by_table';
		parent::display($tpl);				
	}
	
	
	function picker()
	{
		JToolBarHelper::title(   'Cleanup <small><small>[Pick a Table to analyis]</small></small>', 'generic.png' );
		
		$this->tip_main_index();

		$tpl='table_pick';
		parent::display($tpl);						
	}
	
	
	function picker_results()
	{
		JToolBarHelper::title(   'Cleanup <small><small>[Overview of what may need cleaning up]</small></small>', 'generic.png' );
		
		$bar = & JToolBar::getInstance('toolbar');
		
		$title = "Cleanup all fields at once, this is quicker";
		$title_do = 'Clean All';
		$bar->appendButton( 'Custom', '<a href="#" id="clean-all" title="' . $title . '"><span class="icon-32-save"></span>' . $title_do . '</a>', '' );
		JToolBarHelper::spacer('10');
						
		$this->tip_main_index();
		$this->add_prototype();

		$tpl='table_results';
		parent::display($tpl);				
	}

	
	function tip_main_index()
	{
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=cleanup',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="View All Cleanup items"><span class="icon-32-cancel"></span>View all Cleanup</a>', '' );
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
	
	function tip_new()
	{
		$bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=cleanup&task=add',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Add"><span class="icon-32-new"></span>Add</a>', '' );
		JToolBarHelper::spacer('10');
	}
}

