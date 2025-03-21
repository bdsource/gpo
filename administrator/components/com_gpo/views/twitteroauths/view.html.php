    <?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );


class GpoViewTwitteroauths extends JViewLegacy
{
	function all()
	{
		$this->tip_title( 'All' );

        if(empty($this->items)){
            $this->tip_new_twitter();
            $this->tip_new_bitly();
        }
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_( 'index.php?option=com_gpo&controller=news',false );
        $bar->appendButton( 'Custom', '<a href="' . $href . '" title="Close: Back to the News [Published] Page"><span class="icon-32-cancel"></span>Close</a>', '' );
        $this->tip_main_index();
		$tpl='all';
		parent::display($tpl);
	}
	

	function new_howto()
	{
		$this->tip_title( 'All' );
		
		$this->tip_news_search();
		
		$this->tip_main_index();				
		$tpl='new_howto';
		parent::display($tpl);
	}
	
	
	function edit()
	{
		$this->tip_title( 'Edit' );
		$this->tip_new();
		
		$bar = & JToolBar::getInstance('toolbar');
		$title = 'Save Topic';

		$bar->appendButton( 'Custom', '<a href="#" id="item_save" title="' . $title . '"><span class="icon-32-publish"></span>Save</a>', '' );
		JToolBarHelper::spacer('10');		
		
		$title = 'Delete Topic';
		$bar->appendButton( 'Custom', '<a href="#" id="item_delete" title="Delete"><span class="icon-32-delete"></span>Delete</a>', '' );
		JToolBarHelper::spacer('10');
		
		$this->tip_main_index();
		viewHtmlAddPrototype();
		$tpl='edit';
		parent::display($tpl);
	}

    function connect()
    {
        $this->tip_title('Twitter Connect');
        $title = "Twitter Settings";

        $title_do = 'Save';
        $bar = & JToolBar::getInstance('toolbar');
        JToolBarHelper::save('save', $title_do);

        $tpl = 'connect';
        parent::display($tpl);
    }
	
	
	
	function confirm_delete()
	{
		$this->tip_title( 'Delete Topic' );
		$this->tip_main_index();
		$tpl='confirm_delete';
		parent::display($tpl);
	}
	
	
	
	function confirm_delete_all()
	{
		$this->tip_title( 'Delete All Topics' );
		$this->tip_main_index();
		$tpl='confirm_delete_all';
		parent::display($tpl);
	}
	
	
	
	function tip_title( $page )
	{
		JToolBarHelper::title(   'Twitter Settings <small><small>[' . $page .']</small></small>', 'generic.png' );
	}
	
	
	function tip_main_index()
	{
		$bar = & JToolBar::getInstance('toolbar');
		$href = JRoute::_( 'index.php?option=com_gpo&controller=topics',false );
		//$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Show all Lists"><span class="icon-32-cancel"></span>Close</a>', '' );
		JToolBarHelper::spacer('10');
	}


	function tip_new_twitter()
	{
		$bar = & JToolBar::getInstance('toolbar');	
		$href = JRoute::_( 'index.php?option=com_gpo&controller=twitteroauths&type=twitter&task=create',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Start a New Twitter Connection"><span class="icon-32-new"></span>Connect Twitter</a>', '' );
		JToolBarHelper::spacer('10');
	}

    function tip_new_bitly()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_( 'index.php?option=com_gpo&controller=twitteroauths&type=bitly&task=create',false );
        $bar->appendButton( 'Custom', '<a href="' . $href . '" title="Start a New Bitly Connection"><span class="icon-32-new"></span>Connect Bitly</a>', '' );
        JToolBarHelper::spacer('10');
    }

	
	function tip_news_search()
	{
		$bar = & JToolBar::getInstance('toolbar');
		$title = 'Save Topic';
		
		$href = JRoute::_( 'index.php?option=com_gpo&controller=news&task=search',false );
		$bar->appendButton( 'Custom', '<a href="' . $href . '" title="News Search"><span class="icon-32-search"></span>News Search</a>', '' );
		JToolBarHelper::spacer('10');
	} 
}

