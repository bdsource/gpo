<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );


class GpoViewTopics extends JViewLegacy
{
	
	function display($tpl = null)
	{
		GpoHelper::addSubmenu('topics');
        if (JVERSION >= '3.0')
        {
            //$this->sidebar = JHtmlSidebar::render();
        }
		parent::display( $tpl );
	}
	
	
	function all()
	{
		$this->tip_title( 'All' );
		$this->tip_new();
		
		
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=topics&task=delete',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="How to create New Topic"><span class="icon-delete"></span>Delete All</a>', '' );
		JToolBarHelper::spacer('10');
		
		
		$this->tip_main_index();				
		$tpl='all';
		$this->display($tpl);
	}
	

	function new_howto()
	{
		$this->tip_title( 'All' );
		
		$this->tip_news_search();
		
		$this->tip_main_index();				
		$tpl='new_howto';
		$this->display($tpl);
	}
	
	
	function edit()
	{
		$this->tip_title( 'Edit' );
		$this->tip_new();
		
		$bar = & JToolBar::getInstance('toolbar');
		$title = 'Save Topic';

		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="#" id="item_save" title="' . $title . '"><span class="icon-publish"></span>Save</a>', '' );
		JToolBarHelper::spacer('10');		
		
		$title = 'Delete Topic';
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="#" id="item_delete" title="Delete"><span class="icon-delete"></span>Delete</a>', '' );
		JToolBarHelper::spacer('10');
		
		$this->tip_main_index();
		viewHtmlAddPrototype();
		$tpl='edit';
		$this->display($tpl);
	}
	
	
	
	function confirm_delete()
	{
		$this->tip_title( 'Delete Topic' );
		$this->tip_main_index();
		$tpl='confirm_delete';
		$this->display($tpl);
	}
	
	
	
	function confirm_delete_all()
	{
		$this->tip_title( 'Delete All Topics' );
		$this->tip_main_index();
		$tpl='confirm_delete_all';
		$this->display($tpl);
	}
	
	
	
	function tip_title( $page )
	{
		JToolBarHelper::title(   'Topics <small><small>[' . $page .']</small></small>', 'generic.png' );
                
                $document = & JFactory::getDocument();
	        $document->setTitle(JText::_('Topics [' . $page . ']'));
	}
	
	
	function tip_main_index()
	{
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=topics',false );
		//$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Show all Lists"><span class="icon-32-cancel"></span>Close</a>', '' );
		JToolBarHelper::spacer('10');
	}
	

	function tip_new()
	{
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=topics&task=set_search',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Start a New Topic"><span class="icon-new"></span>Create Topic</a>', '' );
		JToolBarHelper::spacer('10');
	}
	
	function tip_news_search()
	{
		$bar = & JToolBar::getInstance('toolbar');
		$title = 'Save Topic';
		
		$href = JRoute::_( 'index.php?option=com_gpo&controller=news&task=search',false );
		$bar->appendButton( 'Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="News Search"><span class="icon-search"></span>News Search</a>', '' );
		JToolBarHelper::spacer('10');
	} 
}

