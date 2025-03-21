<?php
/**
 * Hello World entry point file for Hello World Component
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://dev.joomla.org/component/option,com_jd-wiki/Itemid,31/id,tutorials:components/
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
//putenv("TZ=AEST");
// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');
require_once (JPATH_COMPONENT.DS.'helper/gpo.php');

 $jinput = Joomla\CMS\Factory::getApplication()->getInput();

// Require specific controller if requested
$controller = $jinput->get('controller');
//var_dump ($controller);
//echo JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
if($controller = $jinput->get('controller')) {

  require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
}else{
	$controller='news';
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
	$controller='News';	
}

	
// Create the controller
$classname	= 'GpoController'.$controller;
$controller = new $classname();

// Perform the Request task
$controller->execute( $jinput->get('task') );

// Redirect if set by the controller
$controller->redirect();

?>
