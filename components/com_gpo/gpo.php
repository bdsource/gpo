<?php
/**
* @version		$Id: gpo.php,v 1.1.1.1 2010/03/11 15:00:30 cwill4521 Exp $
* @package		Joomla
* @subpackage	Polls
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// Make sure the user is authorized to view this page
/*
$user = & JFactory::getUser();
if (!$user->authorize( 'com_poll', 'manage' )) {
	$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}
*/
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'gpo.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'route.php');

//die();

// Component Helper
jimport('joomla.application.component.helper');
$config = JFactory::getConfig();


if( $config->get('sef') == '1' )
{ 
    $obj = new GpoHelperRoute();
	$helper = $obj->getInstance('');
	$menuItem = $helper->lookup();
	
	$menuItem->base = $obj->getBase( $menuItem->alias );
        
}else{ 
	@$menuItem->base = JURI::base(); 
}

$jinput = JFactory::getApplication()->input; 
$input = new JInput();
$post = $input->getArray($_GET);



/*if( empty($task) && !empty($view) ){
	$task = $view;
}


if( empty( $task ) )
{
	if( $config->get('sef') === '1' )
	{
//this was pre changing default in router.php
//		$url = $menuItem->base . '/latest';	
		$url = '/' . $menuItem->alias . '/home';
	}else{
		$url = "index.php?option=com_gpo&task=home";
	}
//	ftp_debug( $menuItem );
	JFactory::getApplication::redirect( $url );
	return;
}*/
$jinput = JFactory::getApplication()->input; 
$input = new JInput();
$post = $input->getArray($_GET);

$controller = JControllerLegacy::getInstance('gpo');

//$controller->execute(JFactory::getApplication()->input->get($post['task']));
$controller->execute(Joomla\CMS\Factory::getApplication()->getInput()->get('task'));
$controller->redirect();

/*$controller	= new GpoController( );

$controller->execute( $task );

$controller->redirect();*/
?>
