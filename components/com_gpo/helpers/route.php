<?php
/**
 * @version		$Id: route.php,v 1.1.1.1 2010/03/11 15:00:30 cwill4521 Exp $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

/**
 * Content Component Route Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class GpoHelperRoute
{
	private $lookup_result;
	function getMenuItemId( $view, $id )
	{ 
	   
	   
		$db = JFactory::getDBO();		
		switch( $view )
		{
			case 'region':
			$query = 'SELECT `id` FROM `#__menu` WHERE `type`="component" AND `link` = CONCAT( "index.php?option=com_content&view=article&id=","' . $id . '") LIMIT 0,1';
				break;
			case 'citation':
				exit( 'GPO helper/router.php - would need to make the template run thru joomla' );
				break;
			case 'article':
				exit( 'GPO helper/router.php - this needs com_gpo&view=article&id="blha"' );
				break;				
		}
		$db->setQuery( $query );
		$Itemid = $db->loadResult();			

		
		if( empty( $Itemid ) )
		{
			$query = 'SELECT `id` FROM `#__menu` WHERE `type`="component" AND `link` = "index.php?option=com_gpo&view=region" LIMIT 0,1';
			$db->setQuery( $query );
			$Itemid = $db->loadResult();
		}
		
		$menu = JFactory::getApplication()->getMenu(); //&JSite::getMenu();		
		if( !empty( $Itemid ) )
		{
			$menu->setActive( $Itemid );
		}else{		
			$menuItem = &$menu->getActive();
			$Itemid = $menuItem->id;
		}
		return "$Itemid";
	}
	
	
	function lookup()
	{
//was $_a if it breaks things
		if( $this->lookup_result )
		{
			return $this->lookup_result;
		}
		$db = JFactory::getDBO();
		$query = "SELECT `id`,`alias` FROM `#__menu` WHERE `link`='index.php?option=com_gpo' LIMIT 0,1";
		$db->setQuery( $query );
		$this->lookup_result = $db->loadObject();
		return $this->lookup_result;
	}
	
	
	
	function getBase( $alias='' )
	{
	    $jinput = JFactory::getApplication()->input;
		$uri = JUri::getInstance();
	
		$route = 	$jinput->get('REQUEST_URI', 0, 'SERVER');
		$app = JFactory::getApplication();
		if( $app->getCfg('sef') )
		{
			
//
//			if($app->getCfg('sef_suffix') && !(substr($route, -9) == 'index.php' || substr($route, -1) == '/'))
//			{
//				if($format = $uri->getVar('format', 'html'))
//				{
//					$route .= '.'.$format;
//					$uri->delVar('format');
//				}
//			}

			if($app->getCfg('sef_rewrite'))
			{
				//Transform the route
				$route = str_replace('index.php/', '', $route);
			}else{
				if( strpos( $route, 'index.php') === false )
				{
					$route .="index.php/";
				}
			}
			
			if( !empty( $alias) )
			{
				$route .= $alias;
			}
		}
		return $route;
	}




	/**
	 * Returns a reference to the global JRouter object, only creating it if it
	 * doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $menu = &JRouter::getInstance();</pre>
	 *
	 * @access	public
	 * @param string  $client  The name of the client
	 * @param array   $options An associative array of options
	 * @return	JRouter	A router object.
	 */
	function &getInstance($client, $options = array())
	{
		static $instances;

		if (!isset( $instances )) {
			$instances = array();
		}

		if (empty($instances[$client]))
		{
			//Load the router object
			//$info =& JApplicationHelper::getClientInfo($client, true);
			$path = JPATH_SITE . DS . 'components/com_gpo' . DS . 'helpers' . DS . 'route.php';
			//$path = JPATH_COMPONENT.DS.'helpers'.DS.'route.php';
			if(file_exists($path))
			{
				require_once $path;

				// Create a JRouter object
				$classname = 'GpoHelperRoute'.ucfirst($client);
				$instance = new $classname($options);
			}
			else
			{
				$error = JError::raiseError( 500, 'Unable to load router: '.$client);
				return $error;
			}

			$instances[$client] = & $instance;
		}

		return $instances[$client];
	}
	
	
	/*
	 * Lookup the alias and region in the content table, this might become super seeded once a system is created to allow multiple names for regions.
	 */
	function getContentByAlias( $vars='' )
	{
		
		$size = count( $vars );
		
		$jnow		= JFactory::getDate();
		//$now		= $jnow->toMySQL();
         $now		= $jnow->toSql();
		$db = JFactory::getDBO();
		$nullDate	= $db->getNullDate();

		switch( $size )
		{
			//region
			case 1:
				$region = str_replace(":","-",$vars['0']);
				if( in_array( $region, array('intro','region','region-index') ) )				
				{
					return 0;	
				}
				
				if( strpos( $region, 'staff-notes' ) === false )
				{
					if( strpos( $region, '-index' ) === false )
					{
						$alias = $region . '-index';						
					}else{
						$alias = $region;
					}

				}else{
					$alias = $region;
					strtr( $region,'staff-notes-','' );
				}
				break;
			//region + alias
			case 2:
			case 3:
				$region = str_replace(":","-",$vars['0']);	
				$alias = str_replace(":","-",$vars['1']);
				if( $size == 3 )
				{
					$alias .= str_replace(":","-",$vars['2']);
				}
			
				if( in_array( $alias, array('index','intro') ) )
				{
					$alias = $region . '-index';
						
				}
				break;
			default:
				return 0;
				break;
		}

//this needs to be confirmed
//		$query = 'SELECT a.id' .
//					' FROM #__content AS a' .
//					' LEFT JOIN #__categories AS cc ON cc.id = a.catid' .
//					' WHERE cc.alias=' . $db->quote( $region ) .
//					' AND a.alias =' . $db->quote( $alias ) .
////					' AND cc.published="1"' .
////					' AND a.state = 1' .
////					' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )' .
////					' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'.
//					' LIMIT 0,1';
					
		$query = 'SELECT a.id' .
					' FROM #__content AS a' .
					' LEFT JOIN #__categories AS cc ON cc.id = a.catid' .
					' WHERE a.alias =' . $db->quote( $alias ) .
					' LIMIT 0,1';
		$db->setQuery( $query );
		return $db->loadResult();
	}
	
	

    
	function getRegionAliasById( $id )
	{
		$db = JFactory::getDBO();
		$query = 'SELECT cc.alias' .
					' FROM #__categories AS cc' .
					' WHERE cc.id=' . $db->quote( $id ) .
					' AND cc.published="1"' .
					' LIMIT 0,1';

		$db->setQuery( $query );
		return $db->loadResult();
	}

	
	
	/*
	 * Lookup the alias and region in the content table, this might become super seeded once a system is created to allow multiple names for regions.
	 */
	function getContentAliasById( $id )
	{
//		$jnow		=& JFactory::getDate();
//		$now		= $jnow->toMySQL();
		$db = JFactory::getDBO();
//		$nullDate	= $db->getNullDate();
		$query = 'SELECT `a`.`alias`' .
					' FROM #__content AS a' .
					' WHERE a.id=' . $db->quote( $id ) .
//					' AND a.state = 1' .
//					' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )' .
//					' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'.
					' LIMIT 0,1';
		$db->setQuery( $query );					
		return $db->loadResult();
	}
	
	
	function getAliasByContentId( $id )
	{
		$db = &JFactory::getDBO();							
		$query = 'SELECT `a`.`alias`, `cc`.`alias` as `region`' .
					' FROM #__content AS a' .
					' INNER JOIN #__categories AS cc ON `cc`.`id`=`a`.`catid`' .
					' WHERE a.id=' . $db->quote( $id ) .
//					' AND a.state = 1' .
//					' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )' .
//					' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'.
					' LIMIT 0,1';
		$db->setQuery( $query );
		$o = $db->loadObject();
		if( $o->region . '-index' === $o->alias )
		{
			return $o->region;
		}
		return $o->region . '/' . $o->alias;		
		}
        
        
    function getGroupById($groupId)
	{
        $db = &JFactory::getDBO();
		$query = "SELECT `id`,`name` FROM `#__gpo_groups` WHERE `id` = ". $db->quote($groupId)."";
		$db->setQuery( $query );
		return $db->loadAssoc('id');
	}
    
    function urlFriendlyGroupName($groupName)
    {
        //create url friendly name
        return strtolower(str_replace(' ', '-', $groupName));
    }
    
}
?>
