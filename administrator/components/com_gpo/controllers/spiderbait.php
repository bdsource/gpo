<?php
/**
 * Hello Controller for Hello World Component
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
class GpoControllerSpiderbait extends GpoController
{
	function __construct()
	{
		parent::__construct();
		$this->registerTask( '','showall');
		$this->registerTask( 'add','edit');
	}


	function edit()
	{
		$model =& $this->getModel( 'Spiderbait' );
		$view =& $this->getView( 'Spiderbait', 'html' );
		$spiderbait =& $model->getById( Joomla\CMS\Factory::getApplication()->getInput()->get( 'id', '0','GET','int') );
		$view->spiderbait=&$spiderbait;
		$view->edit();
	}




	function showall()
	{
		$model =& $this->getModel( 'Spiderbait' );
		$model->getDisplays();

		$view =& $this->getView( 'Spiderbait', 'html' );
		$view->total = $model->total;
		$view->rows = $model->data;

		$view->showall();
	}



	function save()
	{
		$model = $this->getModel('Spiderbait');

		if ($model->store()) {
			$msg = JText::_( 'Spiderbait Saved!' );
		} else {
			$msg = JText::_( 'Error Saving Spiderbait' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_gpo&controller=spiderbait';
		$this->setRedirect($link, $msg);
	}


	function remove()
	{
		$model = $this->getModel('Spiderbait');
		$model->delete();
		$msg = '';
		$link = 'index.php?option=com_gpo&controller=spiderbait';
		$this->setRedirect($link, $msg);
	}



	function cancel()
	{
		$msg = '';
		$link = 'index.php?option=com_gpo&controller=spiderbait';
		$this->setRedirect($link, $msg);
	}
}
?>
