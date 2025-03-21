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
class GpoControllerSponsors extends GpoController
{
	function __construct()
	{
		parent::__construct();
        require_once(JPATH_COMPONENT.DS.'helper'.DS.'language.php');
		require_once(JPATH_COMPONENT.DS.'helper'.DS.'sponsors.php');
	    jimport( 'joomla.application.module.helper' );
		$this->registerTask( '','showall');
		$this->registerTask( 'add','edit');
	}


	function edit()
	{
		$model =& $this->getModel( 'Sponsors' );
		$view =& $this->getView( 'Sponsors', 'html' );
		
		$sponsors =& $model->getById( Joomla\CMS\Factory::getApplication()->getInput()->get( 'id', '0','GET','int') );
		$allSponsors = GpoSponsors::getAllAvailabeSponsors( );
		
		$view->sponsors=&$sponsors;
		$view->allSponsors=&$allSponsors;
		$view->edit();
	}




	function showall()
	{
		$model =& $this->getModel( 'Sponsors' );
		$model->getDisplays();

		$view =& $this->getView( 'Sponsors', 'html' );
		$view->total = $model->total;
		$view->rows = $model->data;

		$view->showall();
	}



	function save()
	{
		$model = $this->getModel('Sponsors');

		if ($model->store()) {
			$thePage = Joomla\CMS\Factory::getApplication()->getInput()->get( 'sb',array(),'POST','array' );
			$msg = JText::_( 'Sponsors to the page "'. $thePage['url'] .'" assignment saved!' );
		} else {
			$msg = JText::_( 'Error Saving Sponsors assignment' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_gpo&controller=sponsors';
		$this->setRedirect($link, $msg);
	}


	function remove()
	{
		$model = $this->getModel('Sponsors');
		$model->delete();
		$msg = '';
		$link = 'index.php?option=com_gpo&controller=sponsors';
		$this->setRedirect($link, $msg);
	}



	function cancel()
	{
		$msg = '';
		$link = 'index.php?option=com_gpo&controller=sponsors';
		$this->setRedirect($link, $msg);
	}
}
?>
