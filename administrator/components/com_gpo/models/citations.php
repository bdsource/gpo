<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

/*
 *  Citations model
 */

class GpoModelCitations extends JModelList
{
	var $total;
	var $data;

	function __construct()
	{
	    $jinput = JFactory::getApplication()->input;
		parent::__construct();
		$this->limit = (int)'10';
		$this->limitstart =  $jinput->getVar('limitstart', '0', '', 'int');
	}
	
	
	
	/*
	 * Check to make sure that the type is allowed
	 */
	function setType( $type )
	{
		$allowed = explode(",", 'news,quotes');
		if(  in_array( $type, $allowed ) )
		{
			$this->type = $type;
			return $this;
		}
		die( 'Type is wrong, this should not happen.' );
	}
		
	
//	getBlank( 'published' ) - true
//	getBlank( 'unpublished' ) - falses
	function getBlank( $published )
	{
		$fields = $this->fields( $published );
		$data = new StdClass();
		foreach( $fields as $field )
		{
			$data->$field='';	
		}
		return $data;
	}
	
	
/*	
	function getBlankLive()
	{
		$fields = $this->fieldsLive();
		$data = new StdClass();
		foreach( $fields as $field )
		{
			$data->$field='';	
		}
		return $data;
	}

	
	
	function fieldsLive()
	{
		switch( $this->type )
		{
			case 'news':
				$fields = "id,published,title,subtitle,source,publisher,volume,issue,page,city,category,author,keywords,content,websource,entered,modified,notes,sourcedoc,public";			
				break;
			case 'quotes':
				$fields = "id,published,title,subtitle,source,publisher,volume,issue,page,city,category,author,keywords,content,websource,entered,modified,notes,sourcedoc,public";			
				break;
		}
//		$fields = "id,published,title,subtitle,source,publisher,volume,issue,page,city,category,author,keywords,content,websource,entered,modified,notes,sourcedoc,public";
		return explode(",",$fields);
	}
*/	
	
	/*
	 * this is used for unpublished blank entry
	 * live_id
	 */
	function fields( $published=false)
	{
		switch( $this->type )
		{
			case 'news':
				if( $published === false )
				{
					$fields="id,published,title,subtitle,source,publisher,volume,issue,page,city,category,author,keywords,content,websource,entered,modified,notes,sourcedoc,public";
				}else{
					$fields = "id,published,title,subtitle,source,publisher,volume,issue,page,city,category,author,keywords,content,websource,entered,modified,notes,sourcedoc,public";								
				}

				break;
			case 'quotes':
				if( $published === false )
				{
					$fields="id,published,title,subtitle,source,publisher,volume,issue,page,city,category,author,keywords,content,websource,entered,modified,notes,sourcedoc,public";
				}else{
					$fields = "id,published,title,subtitle,source,publisher,volume,issue,page,city,category,author,keywords,content,websource,entered,modified,notes,sourcedoc,public";								
				}
				break;
		}
		return explode(",",$fields);
	}

	
	/*
	 * creates the citation table name
	 */
	function make_table_name( $published=false )
	{
		return '`#__gpo_citations_' . $this->type . ( ( $published === false ) ? '_unpublished' : '' ) .'`';
	}
	
	
		
	/*
	 * get the unpublished citation
	 */	
	function getById( $id )
	{
		if( (int)$id === (int)'0' )
		{
			return false;
		}
	
		$this->tbl_name = $this->make_table_name( false );
		
		$query = "SELECT * FROM " . $this->tbl_name . " WHERE `id`= " . $this->_db->quote( $id ) . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		$data = $this->_db->loadObject();
		
		if( empty( $data->id ) )
		{
			return false;
		}
		return $data;
	}


	function getLocations( $id )
	{
		$query = "SELECT `lo`.`name` FROM `#__gpo_location` as `lo` LEFT JOIN `#__gpo_citations_locations_unpublished` as `loun` ON `lo`.`id`=`loun`.`location_id` WHERE `loun`.`ext_id`=" . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		$locations = $this->_db->loadColumn();
		return $locations;
	}

	
	function total( $published=false )
	{
		$this->tbl_name = $this->make_table_name( $published );		
		$query = "SELECT COUNT( `id` ) FROM " . $this->tbl_name . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	
	
	function getUnpublished()
	{
		$published = false;
		$total = $this->total( $published );	
		if( (int)$total === (int)'0')
		{
			return array();
		}
		$this->pagination = new JPagination( $total, $this->limitstart, $this->limit );
		
		$this->tbl_name = $this->make_table_name( $published );		
		$query = "SELECT `id`,`title`, `modified` FROM " . $this->tbl_name;
		$query .= " ORDER BY `modified` ASC ";
		
		$this->_db->setQuery( $query, $this->pagination->limitstart, $this->pagination->limit );
		$data = $this->_db->loadAssocList();
		return $data;
	}
/*	
	function getShowAll()
	{
		$total = $this->totalUnpublished();	
		if( (int)$total === (int)'0')
		{
			return array();
		}
		$this->pagination = new JPagination( $total, $this->limitstart, $this->limit );
		
		
		$query = "SELECT `id`,`title`, `modified` FROM `#__gpo_citations_unpublished`";
		$query .= " ORDER BY `modified` ASC ";
		
		$this->_db->setQuery( $query, $this->pagination->limitstart, $this->pagination->limit );
		$data = $this->_db->loadAssocList();
		return $data;
	}
*/
	
	
	function getPublished()
//	function getShowAllLive()
	{
		$published = true;
		$total = $this->total( $published );	

		if( (int)$total === (int)'0')
		{
			return array();
		}
		$this->pagination = new JPagination( $total, $this->limitstart, $this->limit );

		$this->tbl_name = $this->make_table_name( $published );
		$query = "SELECT `id`,`title`, `public` FROM " . $this->tbl_name;
		$query .= " ORDER BY `published` DESC ";
	
		$this->_db->setQuery( $query, $this->pagination->limitstart, $this->pagination->limit );
		$data = $this->_db->loadAssocList();
		return $data;
	}
	
	
	
	/*
	 * 1)check to see if there is already an unpublished version of a live version in the system
	 * 2)if not, copy to unpublished
	 * 3)redirect to the correct edit
	 */
	function copyLiveToUnpublish( $id )
	{
		
		$query = "SELECT `id`,`live_id` FROM `#__gpo_citations_unpublished` WHERE `live_id`=" . $this->_db->quote( $id ) . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		$oLive = $this->_db->loadObject();
		if( !empty( $oLive->id) )
		{
			return $oLive;
		}
		
		$query = "SELECT * FROM `#__gpo_citations` WHERE `id`=" . $this->_db->quote( $id ) . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		$oLive = $this->_db->loadObject();
		$oUnpublish = $this->getBlank();
		foreach( $oUnpublish as $key=>$value )
		{
			$oUnpublish->$key = ( isset( $oLive->$key ) ) ? $oLive->$key : '';	
		}
		$oUnpublish->live_id = $oUnpublish->id;
		$oUnpublish->id ='';
		$ret = $this->_db->insertObject( '#__gpo_citations_unpublished', $oUnpublish, 'id' );
		if( $ret )
		{
			$query = "INSERT INTO `#__gpo_citations_locations_unpublished`( `id`,`location_id`,`ext_id` )";
			$query .= "SELECT '' as `id`, `location_id`, '" . $oUnpublish->id . "' as `ext_id` FROM `#__gpo_citations_locations` WHERE `ext_id`=" . $this->_db->quote( $id );
			$this->_db->setQuery( $query );
			$this->_db->execute();
			return $oUnpublish;
		}else{
			$o = null;
			$o->id = '0';
			return $o;
		}
	}
	
	
	function moveUnpublishToLive( $data, $options )
	{
		$live_id = $options['live_id'];
		$unpublished_id = $options['unpublished_id'];
		
		if( !empty( $live_id ) )
		{
			$data->id = $live_id;
			unset( $data->entered );
			
			if(  isset( $options['minor'] ) && (int)$options['minor'] === (int)'1' )
			{
				unset( $data->modified );	
			}else{
				$unix_timestamp = ( isset( $_SERVER['REQUEST_TIME'] ) ) ? $_SERVER['REQUEST_TIME'] : date('U');
				$data->modified=date('Y-m-d H:i:s', $unix_timestamp );
			}

			$ret = $this->_db->updateObject( '#__gpo_citations', $data, 'id', true );
//Delete locations from live
			$query = "DELETE FROM `#__gpo_citations_locations` WHERE `ext_id`=" . $this->_db->quote( $live_id );
			$this->_db->setQuery( $query );
			$ret = $this->_db->execute();
//Insert new locations
			$query = "INSERT INTO `#__gpo_citations_locations`( `id`,`location_id`,`ext_id` )";

			$query .= "SELECT '' as `id`, `location_id`, '" . $live_id . "' as `ext_id` FROM `#__gpo_citations_locations_unpublished` WHERE `ext_id`=" . $this->_db->quote( $unpublished_id );
			$this->_db->setQuery( $query );
			$this->_db->execute();
//Delete unpublished news + locations
			$this->deleteUnpublishedById( $unpublished_id );
			
			$front_end = str_replace( "administrator",'',JURI::base(true));
			$link = $front_end . JRoute::_('index.php?option=com_gpo&controller=citations&task=article&id=' . $live_id );
			$msg = 'Published: <a href="' . $link . '" target="_blank">click here to view it live</a>';
			
			$link = 'index.php?option=com_gpo&controller=citations&task=showall';
			JController::setRedirect( $link, $msg );
			JController::redirect();
			
		}else if( empty( $data->live_id ) )
		{
			$unix_timestamp = ( isset( $_SERVER['REQUEST_TIME'] ) ) ? $_SERVER['REQUEST_TIME'] : date('U');
//Insert new oCitation into the live table
			$data->id = '';
			$data->entered=date('Y-m-d H:i:s', $unix_timestamp );
			$data->modified=$data->entered;

			$ret = $this->_db->insertObject( '#__gpo_citations', $data, 'id', true );
			$live_id = $data->id;
//Insert new locations
			$query = "INSERT INTO `#__gpo_citations_locations`( `id`,`location_id`,`ext_id` )";

			$query .= "SELECT '' as `id`, `location_id`, '" . $live_id . "' as `ext_id` FROM `#__gpo_citations_locations_unpublished` WHERE `ext_id`=" . $this->_db->quote( $unpublished_id );
			$this->_db->setQuery( $query );
			$this->_db->execute();
//Delete unpublished news + locations
			$this->deleteUnpublishedById( $unpublished_id );		
			$front_end = str_replace( "administrator",'',JURI::base(true));
//FIX THIS LINK			
			$link = $front_end . JRoute::_('index.php?option=com_gpo&controller=citations&task=article&id=' . $live_id );
			$msg = 'Published: <a href="' . $link . '" target="_blank">click here to view it live</a>';
			
			$link = 'index.php?option=com_gpo&controller=citations&task=showall';
			JController::setRedirect( $link, $msg );
			JController::redirect();
		}
	}
	
	
	
	/*
	 * Delete the unpublished news record including its references in the locations section
	 */
	function deleteUnpublishedById( $id )
	{
		$query = "DELETE FROM `#__gpo_citations_unpublished` WHERE `id`=" . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		$ret = $this->_db->execute();
		if( $ret !== true )
		{
			return false;
		}
		$query = "DELETE FROM `#__gpo_citations_locations_unpublished` WHERE `ext_id`=" . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		$ret = $this->_db->execute();
		return $ret;
	}
	
	
	
	function getRule( $field, $message = false )
	{
		$rules=array(
					"id"=>array("required"=>true),
					"published"=>array('required'=>true,'currentdate'=>true),
					"title"=>array("required"=>true),
					"source"=>array("required"=>true),
					"keywords"=>array("required"=>true),
					"content"=>array("required"=>true),
					"websource"=>array("required"=>true),
					"gpnheader"=>array("required"=>true),
					"locations"=>array("required"=>true)		
					);
		$messages = array(
						"id"=>array(
							"id"=>"citations_id",
							"message"=>"id should not be empty"
							),
						"published"=>array(
								"id"=>"citations_published",
								"message"=>"Published needs to be todays date or a past date"
								),
						"title"=>array(
								"id"=>"citations_title",
								"message"=>"title should not be empty"
								),
						"source"=>array(
								"id"=>"citations_source",
								"message"=>"source should not be empty"
								),
						"keywords"=>array(
								"id"=>"citations_keywords",
								"message"=>"keywords should not be empty"
								),
						"content"=>array(
								"id"=>"citations_content",
								"message"=>"content should not be empty"
								),
						"websource"=>array(
								"id"=>"citations_websource",
								"message"=>"websource should not be empty"
								),
						"locations"=>array(
								"id"=>"citations_locations_label",
								"message"=>"At least 1 location is required"
								)
					);			
		if( $message === false )
		{
			if( !empty( $field ) )
			{
				return ( isset( $rules[$field]) ) ? $rules[$field] : false;	
			}
			return false;
		}else if( $message === true )
		{
			if( isset( $messages[$field]) )
			{
				return $messages[$field];
			}
			return false;
		} 
	}

	
	
	function rule( $field, $value )
	{
		$pass = true;
		
		$rule = $this->getRule($field);

		if( $rule !== false )
		{
			if( $pass === true && $rule['required'] === true && required( $value ) !== true ){$pass=false;}
			if( $pass === true && $rule['currentdate'] === true && currentDate( $value) !== true){$pass=false;}
			
			if( $pass !== true )
			{
				print_r( $field );
				return $this->getRule($field, true);
			}
		}
		return $pass;
	}
	
	
	function stripslashes_recursive($value) {
                                    $value = is_array($value) ?
                                    array_map(array($this, 'stripslashes_recursive'), $value) :
                                    stripslashes($value);
                                    return $value;
                  }
	function save( $input )
	{   
		$clean = array();
		$fields = $this->fieldsLive();
		foreach( $fields as $key )
		{
			$clean[$key] = ( isset( $input[$key] ) ) ? $input[$key] : null;
		}
		$clean['locations'] = $input['locations'];
		if( isset( $clean['id']) && $clean['id'] === '0')
		{
			$ignore_id = true;
		}

		//check rules
		$return = array();
		//$clean = JRequest::_stripSlashesRecursive( $clean );
                                    $data = (object) $this->stripslashes_recursive((array) $data);
		foreach( $clean as $key=>$value )
		{
			if( $key !== 'id' )
			{
				$r = $this->rule( $key, $value );
				if( $r !== true )
				{
					$return[]=$r;
				}
			}else{
				if( $ignore_id !== true )
				{
					$r = $this->rule( $key, $value );
					if( $r !== true )
					{						
						$return[]=$r;
					}
				}
			}
		}
		
		if( count( $return ) > 0 )
		{			
			$js = "<script>";
			$js .= <<<EOJS
$('adminForm').select( '[class="error_warning"]').invoke('removeClassName','error_warning');
$('message_box').update('');
EOJS;
			$js .= json_encode( $return);
			$js .= <<<EOJS
.each(function(obj){
 	$( obj.id).up(0).select('label').invoke('addClassName','error_warning');
	var oA =new Element('a')
			.writeAttribute('href','#' + obj.id)
			.update( obj.message.capitalize() );
	$( 'message_box').insert({bottom:oA});
});
window.scrollTo(0,0);

EOJS;
				$js .="</script>";
				return $js;
		}
		//this needs to happen to deal with the format
		$clean['published'] = date('Y-m-d H:i:s', strtotime( $clean['published'] ) );
		
		$oLocations= $clean['locations'];
		foreach( $oLocations as $location )
		{
			$sql_part[]= "`name`=" . $this->_db->quote( $location );
		}
		$query = "SELECT `id` FROM `#__gpo_location` WHERE " . implode( " OR ", $sql_part );
		$this->_db->setQuery( $query );
		$oLocations = $this->_db->loadColumn();
//save - data
		$data = $clean;
		unset( $data['locations']);
		
		$unix_timestamp = ( isset( $_SERVER['REQUEST_TIME'] ) ) ? $_SERVER['REQUEST_TIME'] : date('U');
		if( $clean['id'] === '0')
		{
			$clean['entered']=date('Y-m-d H:i:s', $unix_timestamp );
			$clean['modified']=date('Y-m-d H:i:s', $unix_timestamp );
			$data = (object)$clean;
			$ret = $this->_db->insertObject( '#__gpo_citations_unpublished', $data, 'id' );
		}else{
			$clean['modified']=date('Y-m-d H:i:s', $unix_timestamp );
			$data = (object)$clean;
			$ret = $this->_db->updateObject( '#__gpo_citations_unpublished', $data, 'id', true );
		}
		if( !$ret )
		{
			return 'An error occured whilst saving';
		}
//save - locations
		$unpublished_id = $data->id;
		
		$query = "DELETE FROM `#__gpo_citations_locations_unpublished` WHERE `ext_id`=" . $this->_db->quote( $unpublished_id );
		$this->_db->setQuery( $query );
		$ret = $this->_db->execute();
		
		
		if( count( $oLocations ) > 0 )
		{
			$query = "INSERT IGNORE INTO `#__gpo_citations_locations_unpublished` ( `id`,`location_id`,`ext_id` ) VALUES ";			
			$sql_parts=array();
			foreach( $oLocations as $location )
			{
				$sql_parts[]= "(NULL," . $this->_db->quote( $location ) . "," . $this->_db->quote( $unpublished_id ) . ")";
			}
			$query .= implode(",",$sql_parts);
			$query .= ";";
			$this->_db->setQuery( $query );
			$ret = $this->_db->execute();
		}
		
		if( isset($_POST['new_record']) && $_POST['new_record'] === '1' )
		{
			$link = JRoute::_( 'index.php?option=com_gpo' ) . '&controller=citations&task=create';
			
		}else{
			$link = JRoute::_( 'index.php?option=com_gpo' ) . '&controller=citations';
		}
		$js = "<script>window.location='" . $link . "'</script>";
		echo $js;
		exit();
	}
	
	/*
	 * id = id of table
	 * type = published / unpublished
	 */
	function copy_from_news( $id )
	{
		$this->setType( 'news' );
		$tbl_name = $this->make_table_name('news', false );
		$tbl_name = ( $type === 'published' ) ? '#__gpo_news' : '#__gpo_news_unpublished';
$quey_select = <<<SQL
`id`
`published`
`title`
`subtitle`
`source`
`category`
`byline`
`content`
`websource`
`entered`
`modified`
SQL;
		$query_select = str_replace("\n",",",$query_select );
		$query = "SELECT " . $query_select . " FROM `" . $tbl_name . "` WHERE `id`=" . $this->_db->quote( $id ). " LIMIT 0,1";
		$this->_db->setQuery( $query );
		$oItem = $this->_db->loadObject();
		$oUnpublish = $this->getBlank();
		foreach( $oUnpublish as $key=>$value )
		{
			$oUnpublish->$key = ( isset( $oItem->$key ) ) ? $oItem->$key : '';	
		}
		$oUnpublish->id = '';
		
		//add the new timestamp
		$unix_timestamp = ( isset( $_SERVER['REQUEST_TIME'] ) ) ? $_SERVER['REQUEST_TIME'] : date('U');
		$oUnpublish->modified = date('Y-m-d H:i:s', $unix_timestamp );
//NOTE: do we need to set the public to be private by default? probably - this system still needs creating

		$ret = $this->_db->insertObject( '#__gpo_citations_unpublished', $oUnpublish, 'id' );
		if( $ret )
		{
			//DELETE FIRST
			$query = "DELETE FROM `#__gpo_citations_locations_unpublished` WHERE `id`=" . $this->_db->quote( $oUnpublish->id );
			$this->_db->setQuery( $query );
			$this->_db->execute();
			
			$tbl_name = ( $type === 'published' ) ? '#__gpo_news_locations' : '#__gpo_news_locations_unpublished';
			$query = "INSERT INTO `#__gpo_citations_locations_unpublished`( `id`,`location_id`,`ext_id` )";
			$query .= "SELECT '' as `id`, `location_id`, '" . $oUnpublish->id . "' as `ext_id` FROM `" . $tbl_name . "` WHERE `ext_id`=" . $this->_db->quote( $id );
			$this->_db->setQuery( $query );
			$this->_db->execute();
			return $oUnpublish->id;
		}
		return false;
	}
	
	/*
	 * id = id of table
	 * type = published / unpublished
	 */
	function copy_from_quotes( $id, $type )
	{
		$tbl_name = ( $type === 'published' ) ? '#__gpo_quotes' : '#__gpo_quotes_unpublished';
		$query = "SELECT * FROM `" . $tbl_name . "` WHERE `id`=" . $this->_db->quote( $id ). " LIMIT 0,1";
		$this->_db->setQuery( $query );
		$oItem = $this->_db->loadObject();
		$oUnpublish = $this->getBlank();
		foreach( $oUnpublish as $key=>$value )
		{
			$oUnpublish->$key = ( isset( $oItem->$key ) ) ? $oItem->$key : '';	
		}
		$oUnpublish->id = '';
		//add the new timestamp
		$unix_timestamp = ( isset( $_SERVER['REQUEST_TIME'] ) ) ? $_SERVER['REQUEST_TIME'] : date('U');
		$oUnpublish->modified = date('Y-m-d H:i:s', $unix_timestamp );
//NOTE: do we need to set the public to be private by default? probably - this system still needs creating
//		echo '<pre>' . print_r( $oUnpublish, true ) . '</pre>';
//		exit();
		$ret = $this->_db->insertObject( '#__gpo_citations_unpublished', $oUnpublish, 'id' );
		if( $ret )
		{
			$tbl_name = ( $type === 'published' ) ? '#__gpo_quotes_locations' : '#__gpo_quotes_locations_unpublished';
			$query = "INSERT INTO `#__gpo_citations_locations_unpublished`( `location_id`,`ext_id` )";
			$query .= "SELECT  `location_id`, '" . $oUnpublish->id . "' as `ext_id` FROM `" . $tbl_name . "` WHERE `ext_id`=" . $this->_db->quote( $id );
			$this->_db->setQuery( $query );
			$this->_db->execute();
			return $oUnpublish->id;
		}
		return false;
	}

	function addCitationRelation ( $quote_id, $citation_id, $type = 'quotes', $status='unpublished' ){
	    if(empty($quote_id) OR empty($citation_id)) return false;
	    $data = new stdClass();
	    $data->type = $type;
	    $data->type_id = $quote_id;
	    $data->citation_id = $citation_id;
        $data->status = $status;
	    $result = $this->_db->insertObject( '#__gpo_citation_relation', $data, 'id' );
	    return $result;

	}

	/**
	 * This function will retrieve the citations list from _gpo_citation_relation table to show which citations used this quote
	 * @param <int> $id
	 * @param <string> Type of data; news/quote
	 * @return <array> list of citation_id
	 */
	function getCitationsRelations( $id, $type = 'quotes' ){
	    $query = "SELECT `citation_id` FROM `#__gpo_citation_relation` WHERE `type`=".$this->_db->Quote($type)." AND `type_id`=".$this->_db->Quote($id);
	    $this->_db->setQuery($query);
	    $citations = $this->_db->loadColumn();
	    return $citations;
	}

	function updateCitationRelation ( $old_citation_id, $new_citation_id, $type, $old_status='unpublished' ){
        $new_status = ($old_status == 'unpublished' ? 'published' : 'unpublished');
	    $query = "UPDATE `#__gpo_citation_relation` SET `citation_id`=".$this->_db->Quote($new_citation_id).",  `status`='$new_status' WHERE `type`=".$this->_db->Quote($type)." AND `citation_id` =".$this->_db->Quote($old_citation_id) ." AND  `status`='$old_status'";
        // echo $query;
	    $this->_db->setQuery($query);
	    $result = $this->_db->execute();
	    //echo $this->_db->getQuery();
	    return $result;
	}

	function deleteCitationRelation ( $citation_id, $type, $type_id ){
	    $query = "DELETE FROM `#__gpo_citation_relation` WHERE  `type`=".$this->_db->Quote($type)." AND `type_id`=".$this->_db->Quote($type_id) ." AND `citation_id` = ".$this->_db->Quote($citation_id);
	    $this->_db->setQuery($query);
	    $result = $this->_db->execute();
	    return $result;
	}
	
}
?>
