<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/*
$this->oUser =& JFactory::getUser();
if( $this->oUser->usertype !== 'Super Administrator' )
{
	return '';
}
*/
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once(  JPATH_ROOT . DS . 'components' . DS . 'com_gpo' . DS .'helpers' . DS . 'gpo.php');

//echo JURI::base();
$data = GpoGetTypeFromCache( 'topics.all' );
//if( $data !== false )
if( false )
{
	echo $data;
	return;
}
$db =& JFactory::getDBO();
$query = "SELECT `seo`,`topic_name` FROM `#__gpo_topic` ORDER BY `window_title`;";
$db->setQuery( $query );
$items = $db->loadObjectList();
if( empty( $items ) )
{
	GpoSaveTypeToCache( 'topics.all', '' );
	return;
}
$jView = new JViewLegacy();
$total = count( $items );
$split = floor( $total / 2 );
//print_r($items);die();
if( $total <= 1 )
{
	$html = '<table><tr><td>';
	foreach( $items as $item )
	{
		$html .= '<a href="' . JURI::base() . $item->seo . '">' . $jView->escape( $item->topic_name ) . '</a>';
	}	
	$html .= '</td></tr></table>';	
}else{

	$html = '
<style>
#topics {
width:100%;
}
#topics td{
	vertical-align:top;
}
#topics td a{
	display:block;
	padding:2px;
}
</style>		
<table id="topics">';
		
	$html .= '<tr>';
	
	$html .= '<td>';
 
	for( $i=0;$i<$split;++$i )
	{
		$item = $items[ $i ];
		$html .= '<a href="' . JURI::base() . $item->seo . '">' . $jView->escape( $item->topic_name ) . '</a>';
	}
	$html .= '</td>';
	
	$html .= '<td>';
	for( $i=$split;$i<$total;++$i )
	{
		$item = $items[ $i ];
		$html .= '<a href="' . JURI::base() . $item->seo . '">' . $jView->escape( $item->topic_name ) . '</a>';
	}
	$html .= '</td>';
	
	$html .= '</tr>';
	$html .= '</table>';
    
    ##footnote
    $html .= '<p>&nbsp;</p><h5> 
                 For earlier articles, use the Search News feature above
              </h5>';

}

GpoSaveTypeToCache( 'topics.all', $html );
//for the moment whilst testing.
//GpoDeleteFromCache( 'topics.all' );
echo $html;
