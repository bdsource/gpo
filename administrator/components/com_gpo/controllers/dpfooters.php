<?php
/**
 * Dpfooters Controller for Dp Footers Component
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://dev.joomla.org/component/option,com_jd-wiki/Itemid,31/id,tutorials:components/
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Hello Hello Controller
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class GpoControllerDpfooters extends GpoController
{
	function __construct()
	{
		parent::__construct();
		require_once(JPATH_COMPONENT.DS.'helper'.DS.'dpfooters.php');
	    jimport( 'joomla.application.module.helper' );
		$this->registerTask( '','showall');
		$this->registerTask( 'add','edit');
	}


	function edit()
	{
		$model =& $this->getModel( 'dpfooters' );
		$view =& $this->getView( 'dpfooters', 'html' );
		
		$dpfooters =& $model->getById( Joomla\CMS\Factory::getApplication()->getInput()->get( 'id', '0','GET','int') );
		$allUsers = GpoDpfooters::getAllUsers( );
		
		$view->dpfooters=&$dpfooters;
		$view->allUsers=&$allUsers;
		$view->edit();
	}




	function showall()
	{
		$model =& $this->getModel( 'dpfooters' );
		$model->getDisplays();

		$view =& $this->getView( 'dpfooters', 'html' );
		$view->total = $model->total;
		$view->rows = $model->data;

		$view->showall();
	}



	function save()
	{
		$model = $this->getModel('dpfooters');

		if ($model->store()) {
			$thePage = Joomla\CMS\Factory::getApplication()->getInput()->get( 'sb',array(),'POST','array' );
			$msg = JText::_( 'dpfooters to the page "'. $thePage['url'] .'" assignment saved!' );
		} else {
			$msg = JText::_( 'Error Saving dpfooters assignment' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_gpo&controller=dpfooters';
		$this->setRedirect($link, $msg);
	}


	function remove()
	{
		$model = $this->getModel('dpfooters');
		$model->delete();
		$msg = '';
		$link = 'index.php?option=com_gpo&controller=dpfooters';
		$this->setRedirect($link, $msg);
	}



	function cancel()
	{
		$msg = '';
		$link = 'index.php?option=com_gpo&controller=dpfooters';
		$this->setRedirect($link, $msg);
	}
}
?>
