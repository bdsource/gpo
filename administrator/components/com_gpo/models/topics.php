<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once( JPATH_COMPONENT . DS . 'helper/topic.php' );
require_once( JPATH_COMPONENT . DS . 'helper/spiderbait.php' );
/*
id
title
url
search_for
this should be linked to the save... to create from

 */
class GpoModelTopics extends JModelList
{
	var $total;
	var $data;

	function __construct()
	{
		parent::__construct();
		$this->limit = (int)'10';
		$this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart');
	}
	
	
	
	function getById( $id )
	{
//get data
		$this->topic = new GpoTopic( $id );
		return $this->topic->getAll();
	}
	
	function getBySearchHash( $input )
	{
		$query = 'SELECT `id` FROM `#__gpo_topic` WHERE `search_hash`=' . $this->_db->quote( $input );
		$this->_db->setQuery( $query );
		$id = $this->_db->loadResult();
		$this->topic = new GpoTopic( $id );
		return $this->topic->getAll();
	}
	
	
	
	function getAll()
	{
		$query = 'SELECT `id`,`seo`,`window_title`,`page_headline` FROM `#__gpo_topic` ORDER BY `window_title`;';
		$this->_db->setQuery( $query );
		$data = $this->_db->loadObjectList();
		return $data;
	}
	
	
	function save( $input )
	{
/*
		$input = array(
							'seo' => 'firearms/topic/apple2',
            				'window_title' => 'Guns in Norway',
            				'page_headline' => 'News of all gun crime in Norway', 
            				'page_headline_sub' => 'Small arms',
            				'spiderbait' => 'Gun crime facts for norway',
							'meta' => 'META'
						);
*/
		$input[ 'seo' ] = strtolower( $input[ 'seo' ] );
		$a_seo = str_split( $input[ 'seo' ] );
		//$bad = array( " ", ":", "-" );
		$bad = array( " ", ":" );
		foreach( $a_seo as $v )
		{
			if( in_array( $v, $bad ) )
			{
				echo '<p style="color:#ff0000;">Please check the seo field, &quot;spaces&quot;, &quot;:&quot; and &quot;-&quot; are not allowed, consider using &quot;_&quot; this is a joomla limitation</p>';
				return;
			}
		}
		if( !empty( $input[ 'id' ] ) )
		{
			$cTopic = new GpoTopic( $input[ 'id' ] );	
		}else{
			$cTopic = new GpoTopic( $input[ 'seo' ] );
		}

		if( $cTopic->get('id') !== false 
			&& ( $input['id'] !== $cTopic->get( 'id' ) ) 
		)
		{
			echo '<p style="color:#ff0000;">The SEO you have selected is in use</p>';
			return;
		}
		
		$response = $cTopic->save( $input );
		if( $response['status'] !== true )
		{
			echo '<p style="color:#ff0000;">An error has occured whilst saving, please try again.</p>';
			return;
		}
		$topic = $response['data'];
//save spiderbait
		$cSpiderbait = new GpoSpiderbait( $input[ 'seo'] );					
		$response = $cSpiderbait->save( array(
													'url' => $input[ 'seo'],
													'text' => $input[ 'spiderbait' ]
												)
										);
		$front_end = str_replace( "administrator",'',JURI::base(true));
		$href = $front_end . $input[ 'seo' ];
		$this->response = '
<p><a href="' . $href . '" target="_blank">Saved! Take me to the Topic ( frontend )</a></p>
';
		if( $input['id'] !== $topic->id )
		{
			$this->response = '
<script>
window.location="' . JRoute::_( 'index.php?option=com_gpo&controller=topics&id=' . $topic->id, false ) . '";
</script>
';
		}
	}
	
	
	function deleteBy( $topic )
	{
			$query = 'DELETE `s` FROM `#__gpo_spiderbait` WHERE `url_hash`=' . md5( $topic->seo ) . ';';
			$this->_db->setQuery( $query );
			$this->_db->execute();
			
			
			$query = 'DELETE FROM `#__gpo_topic` WHERE `id`=' . $topic->id . ';';
			$this->_db->setQuery( $query );
			$this->_db->execute();
			
			$filename = JPATH_SITE . '/components/com_gpo/cache/topics.all.php';
			unlink( $filename );
	}
	
	
	function deleteAll()
	{
			$query = '
TRUNCATE `#__gpo_topic`;
';
			$this->_db->setQuery( $query );
			$this->_db->execute();
			
			$query = '
DELETE FROM `#__gpo_spiderbait` WHERE `url` REGEXP "^firearms/topic.*";
';
			$this->_db->setQuery( $query );
			$this->_db->execute();
			
			$filename = JPATH_SITE . '/components/com_gpo/cache/topics.all.php';
			unlink( $filename );
			return true;
	}	
	
	
	
	function response()
	{
		return $this->response;
	}
}