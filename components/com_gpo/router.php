<?php
/**
 * @version		$Id: router.php,v 1.1.1.1 2010/03/11 15:00:30 cwill4521 Exp $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
//$info =& JApplicationHelper::getClientInfo($client, true);
//echo '<pre>' . print_r( JApplicationHelper::getPath('class','com_gpo'), true) . '</pre>';
//require_once( $info->getPath() . DS . 'components/com_gpo'.DS.'helpers'.DS.'route.php');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once( JPATH_SITE . DS . 'components/com_gpo' . DS . 'helpers' . DS . 'route.php');

function GpoBuildRoute($query)
{
 

	$app = JFactory::getApplication();
	$segments = array();
	
	//$helper = GpoHelperRoute::getInstance('');
	$var = new GpoHelperRoute();
    $helper   = $var->getInstance('');
	$menuItem = $helper->lookup();
	
	if( isset( $menuItem->id) )
	{
		$query['Itemid']=$menuItem->id;
	}else{
		echo 'error: create menu index.php?option=com_gpo';
		exit();	
	}
	

	$query['task']= ( isset( $query['task'] ) ) ? $query['task'] : 'home';
	//remember to remove the parts of the query we dont want.
	//we will by default remove unset($query['task']); after so feel free to ignore this


	switch( $query['task'] )
	{
		case 'latest':
			$segments['0'] = 'latest';
			break;

//this will be the new home page			
		case 'home':
			$segments['0'] = 'home';
			unset($query['view']);
			break;	

case 'cats':
			$segments['0'] = 'cats';
			unset($query['view']);
			break;	
        
			
		case 'search':
			$segments['0'] = 'component/gpo/search';
			$segments['1'] = ( isset( $query['view'] ) ) ? $query[ 'view' ] : '';
			unset($query['view']);
			break;
		case 'topic':
#fix
//at the moment this is broken.
			$segments['0']=$query['task'];
			$segments['1']=$query['id'];
			$segments['1']= str_replace( 'firearms/topic/', '', $segments['1'] );
			if( !empty( $query['rss'] ) )
			{
				$segments['2'] = 'rss';
				unset( $query['rss'] );
			}
			unset($query['task']);
			unset($query['id']);
			break;		

		case 'news':
			$segments['0']=$query['task'];
			$segments['1']=$query['id'];
			if( $segments[ '1'] === 'archive' )
			{
				if( isset( $query[ 'y' ] ) )
				{
					$segments['2'] = $query[ 'y' ];
					unset( $query['y'] );
					if( isset( $query[ 'm' ] ) )
					{
						$segments['3'] = $query[ 'm' ];
						unset( $query['m'] );
						if( isset( $query[ 'd' ] ) )
						{
							$segments['4'] = $query[ 'd' ];
							unset( $query['d'] );	
						}	
					}	
				}
			}
			unset( $query['task'] );
			unset( $query['id'] );
			break;

		case 'citation':
			$segments['0']='citation';
			$segments['1']=$query['type'];
			$segments['2']=$query['id'];
			unset($query['task']);
			unset($query['type']);			
			unset($query['id']);
			break;
		
		case 'rss':
			$segments['0']='rss';
			$segments['1']=$query['view'];
			$segments['2'] = $query['id'];
			unset($query['id']);
			unset($query['view']);
			break;
			
//not location as its not very native
//old version in archive 2010/03/07
		case 'region':
			$segments['0']='region';
			
			if( isset( $query['region']) )
			{
				if( $app->getCfg('sef') != '1' )
				{
					$segments['1']=$query['region'];
					unset( $query['region']);
					if( isset( $query['id']) )
					{
						$segments['2']=$query['id'];
						unset( $query['id']);
					}
				}else{
				    $helpobj = new GpoHelperRoute();
					$helper = $helpobj->getInstance('');
					if( !isset( $query['search'] ) )
					{
						$start = 1;
						if( isset( $query['cp'] ) ){
						  $segments['1'] = 'cp';
						  unset( $query['cp'] );
						  $start++;
						}
						$segments[$start++]=$helper->getRegionAliasById( $query['region'] );
						unset( $query['region']);
						if( isset( $query['id']) )
						{
							$segments[$start++]=$helper->getContentAliasById( $query['id'] );
							unset( $query['id']);
						}
					}else{
						unset( $query['search']);
						$segments['1']=$query['region'];
						unset( $query['region']);
						if( isset( $query['alias']) )
						{
							if( strpos( $query['alias'], '-index' ) === false )
							{
								$segments['2']=$query['alias'];
							}
							unset( $query['alias']);
						}
					}
					
				}
			}
       
			break;

        case 'group':
            $segments['0'] = 'group';
            $groupId = $query['id'];
            $helper = &GpoHelperRoute::getInstance('');
            $groupDetails = $helper->getGroupById( $groupId );
            $segments['1'] = $helper->urlFriendlyGroupName($groupDetails['name']);
            unset($query['task']);
            unset($query['id']);
        break;
    
		case 'find_facts':
			$segments['0'] = 'find-gun-policy-facts';
			break;
			
		case 'test':
			$segments['0'] = 'test';
			break;	
		
        case 'preview':
			$segments['0'] = 'preview';
            $segments['1'] = $query['location'];
            $segments['2'] = $query['group'];
            unset($query['task']);
			unset($query['location']);			
			unset($query['group']);
            break;
        
		case 'msearch':
			$segments['0'] = 'msearch';
			break;
			
		case 'compare':
			$segments[0] = 'compare';
			unset($query['view']);
			break;
		case 'compareyears':
			$segments[0] = 'compareyears';
			unset($query['view']);
			break;

        case 'glossary':
			$segments['0']='glossary';
			$segments['1']=$query['id'];

			unset($query['task']);
			unset($query['type']);
			unset($query['id']);
			break;

		default:
//can look up location - then know what to do from there. region / subregion / country
			$segments['0'] = 'home';
			break;
	}

//remove standard lines from query
	if( isset( $query['task'] ) ){ unset( $query['task'] ); }

	return $segments;
}


function GpoParseRoute($segments)
{
	$app = JFactory::getApplication();
	$value1 = new GpoHelperRoute();
	$helper = $value1->getInstance('');
	//$helper = GpoHelperRoute::getInstance('');
	
	$vars = array();
	$segments['0']= ( isset( $segments['0'] ) ) ? $segments['0'] : 'home'; 
	switch( $segments['0'])
	{
		case 'latest':
			$vars['task'] = 'latest';
			break;
		case 'home':
			$vars['task'] = 'home';
			break;	

		case 'cats':
			$vars['task'] = 'cats';
			break;	
		
		case 'search':
			$vars['task'] = 'search';
			$vars['view'] = $segments['1'];
			break;
        case 'group':
			$vars['task'] = 'group';
            $vars['groupName'] = $segments['1'];
			break;
		case 'topic':
			$vars['task'] = 'topic';
			$pos = strpos( $_SERVER[ 'REQUEST_URI' ], 'firearms/topic/' );
			$id = substr ( $_SERVER[ 'REQUEST_URI' ], $pos );
			$vars['id'] = $id;

			if( substr ( $vars[ 'id' ], -4) === '/rss' )
			{
				$vars[ 'rss' ] = '1';
				$vars['id'] = substr_replace( $vars['id'], '', -4 );
			}else{
				$vars[ 'rss' ] = '0';
			}
			break;
		case 'region':
			$vars['task'] = ( in_array('cp',$segments) ) ? 'cp' : 'region';
			$start = ( in_array('cp',$segments) ) ? 2 : 1;
						
			if( $app->getCfg('sef') != '1' )
			{
				$vars['id']=$segments['1'];	
			}else{
				//lookup here
				$size = count($segments);
				for( $i=$start;$i<$size;++$i)
				{
					$v[]=$segments[$i];
				}
				$vars['id'] = $helper->getContentByAlias( $v );
                //var_dump($vars['id']);
			}

			$vars['Itemid'] = $helper->getMenuItemId( 'region', $vars['id'] );
            //var_dump($vars['Itemid']);
			break;
		case 'news':
			$vars['task']=$segments['0'];
			$vars['id']=$segments['1'];
			if( !empty( $vars[ 'id' ] ) && $vars[ 'id' ] === 'archive' )
			{
				$vars[ 'task' ] = 'news_archive';
				$vars[ 'y' ] = ( isset( $segments[ '2' ] ) ) ? $segments[ '2' ] : '';
				$vars[ 'm' ] = ( isset( $segments[ '3' ] ) ) ? $segments[ '3' ] : '';
				$vars[ 'd' ] = ( isset( $segments[ '4' ] ) ) ? $segments[ '4' ] : '';
			}
			break;

		case 'citation':
			$vars['task']='citation';
			$vars['type']=$segments['1'];			
			$vars['id']=$segments['2'];
			break;

        case 'glossary':
            $vars['task'] = 'glossary';
            $vars['id'] = $segments[1];
            break;
		case 'rss':
			$vars['task']='rss';
			$vars['view']= ( isset( $segments['1'] ) ) ? $segments['1'] : '';
			$vars['id'] = ( isset( $segments['2'] ) ) ? $segments['2'] : '';
			break;
		case 'find:gun-policy-facts':
				$vars['task']='find_facts';
				break;
		case 'test':
				$vars['task']='test';
				break;
        case 'preview':
				$vars['task']     = 'preview';
			    $vars['location'] = $segments['1'];			
			    $vars['group']    = $segments['2'];
				break;
		case 'msearch':
				$vars['task']='msearch';
				break;
		case 'compare':
				$vars['task']='compare';
                if( strtolower($segments[1]) == 'group' ) {
                   $vars['base_location'] = $segments[2];
                   $vars['is_group'] = 1;
                }
                else if( strtolower($segments[1]) == 'region' ) {
                   $vars['base_location'] = $segments[2];
                   $vars['is_region'] = 1;
                }
                else {
                   $vars['base_location'] = $segments[1];
                   $vars['is_group']  = 0;
                   $vars['is_region'] = 0;
                }
                $vars['column'] = ($vars['is_group'] || $vars['is_region']) ? $segments[3] : $segments[2];
                $vars['selected_locations'] = ($vars['is_group'] || $vars['is_region']) ? $segments[4] : $segments[3];
				break;
        case 'compareyears':
				$vars['task']='compareyears';
                if( strtolower($segments[1]) == 'group' ) {
                   $vars['base_location'] = $segments[2];
                   $vars['is_group'] = 1;
                }
                else if( strtolower($segments[1]) == 'region' ) {
                   $vars['base_location'] = $segments[2];
                   $vars['is_region'] = 1;
                }
                else {
                   $vars['base_location'] = $segments[1];
                   $vars['is_group']  = 0;
                   $vars['is_region'] = 0;
                }
                $vars['column'] = ($vars['is_group'] || $vars['is_region']) ? $segments[3] : $segments[2];
                $vars['base_location'] = ($vars['is_group'] || $vars['is_region']) ? $segments[2] : $segments[1];
				break;
		default:
			//redirect to latest
			//can look up location - then know what to do from there. region / subregion / country
			$vars['task'] = '';
			break;
	}
	return $vars;
}
