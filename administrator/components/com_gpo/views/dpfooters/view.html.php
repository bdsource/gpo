<?php
/**
 * Hello View for Hello World Component
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://dev.joomla.org/component/option,com_jd-wiki/Itemid,31/id,tutorials:components/
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class GpoViewDpfooters extends JViewLegacy 
{

	function display($tpl = null)
	{
		GpoHelper::addSubmenu('dpfooters');
        if (JVERSION >= '3.0')
        {
            //$this->sidebar = JHtmlSidebar::render();
        }
		parent::display( $tpl );
	}

	function edit()
	{
		$dpfooters =& $this->dpfooters;

		$isNew		= ($dpfooters->id < 1);

		$text = $isNew ? JText::_( 'Create New' ) : JText::_( 'Edit' );
		$text = JText::_( $text );
		JToolBarHelper::title(   JText::_( 'dpfooters' ).': <small><small>[ ' . $text.' ]</small></small>' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'dpfooters' ).': [ ' . $text.' ]' );
                
		JToolBarHelper::save();
		if( $isNew )
		{
			JToolBarHelper::cancel();
		}else{
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		$tpl = 'edit';
		//$this->assignRef('dpfooters',$dpfooters);
		$this->dpfooters = &$dpfooters;


		$this->display($tpl);
	}



	function showall()
	{
		JToolBarHelper::title(   JText::_( 'dpfooters' ), 'generic.png' );
                
                $document = &JFactory::getDocument();
                $document->setTitle(JText::_( 'dpfooters' ));
                
		JToolBarHelper::addNew();
		JToolBarHelper::spacer('10');
		//JToolBarHelper::deleteList();
    $bar = & JToolBar::getInstance('toolbar');
		$href = 'onclick="customDeletFun()"';
    $bar->appendButton( 'Custom', '<a href="#" '.$href.' title="Delete" ><span class="icon-32-delete"></span>Delete</a>', '' );

		$tpl='showall';
		$this->display($tpl);
	}
}

