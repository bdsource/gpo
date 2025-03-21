<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');
use Joomla\CMS\Router\Route;

class GpoModelCitationsquotes extends JModelList
{
	var $total;
	var $data;

	function __construct()
	{
		parent::__construct();
		//$this->limit = Joomla\CMS\Factory::getApplication()->getInput()->get('limit', '100', '', 'int');
		$this->limit = Joomla\CMS\Factory::getApplication()->getInput()->get('limit', '100', '', 'int');

		//$this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', '', 'int');
		$this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', '', 'int');

		$this->type='quotes';
		//$this->path = "/home/palpers/gp-uploads/citations/" . $this->type . "/";
        $this->path = "/home/gpo/gp-uploads/citations/" . $this->type . "/";
	}
	/*
	 * get the unpublished citation
	 */	
	function getUnPublishedById( $id )
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
        
	/*
	 * get the published citation
	 */
        
	function getPublishedById( $id )
	{
		if( (int)$id === (int)'0' )
		{
			return false;
		}
	
		$this->tbl_name = $this->make_table_name( true );
		$query = "SELECT * FROM " . $this->tbl_name . " WHERE `id`= " . $this->_db->quote( $id ) . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		$data = $this->_db->loadObject();
		
		if( empty( $data->id ) )
		{
			return false;
		}
		return $data;
	}    
	/*
	 */
	
        function publish( $oItem )
	{
		$live_id= (isset( $oItem->live_id) ) ? $oItem->live_id : null;
		$unpublished_id = $oItem->id;

		$aPublish = Joomla\CMS\Factory::getApplication()->getInput()->get( 'publish','0','POST','array');
		if( isset( $aPublish['approve'] ) && (int)$aPublish['approve'] === (int)'1' )
		{
			$aPublish['live_id']=$live_id;
			$aPublish['unpublished_id']=$unpublished_id;
			$r = $this->moveUnpublishToPublished( $oItem, $aPublish );
			return $r;
		}
		$response = new stdClass();
		$response->msg = 'You have not ticked the approve box';
		$response->link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type .'&task=publish&id=' . $unpublished_id , false );
		return $response;
	}
	
	
	
	function readyForPublishing( $oItem )
	{
		$aPublish = Joomla\CMS\Factory::getApplication()->getInput()->get( 'publish','0','POST','array');
		if( isset( $aPublish['approve'] ) && (int)$aPublish['approve'] === (int)'1' )
		{
			$id = $oItem->id;
			$query = "INSERT IGNORE INTO `#__gpo_awaiting_published` (`id`,`ext_id`,`ext_table`)VALUES(NULL ," . $this->_db->quote( $id ) . ",'qc');";
			$this->_db->setQuery( $query );
			$ret = $this->_db->execute();
			$response = new stdClass();
			$response->msg = 'Citation has been queued for publishing. A Super Administrator will approve it shortly.';
			$response->link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type, false );
			$response->pass= true;		
			return $response;		
		}
	}
		
	
	
	function moveUnpublishToPublished( $data, $options )
	{
		$live_id = $options['live_id'];
		$unpublished_id = $options['unpublished_id'];

		$blankObject = $this->fields(true);
		$data = (object)array_intersect_key( (array)$data,(array)$blankObject );
			
		$tbl_name = $this->make_table_name( true,false );

		$unix_timestamp = ( isset( $_SERVER['REQUEST_TIME'] ) ) ? $_SERVER['REQUEST_TIME'] : date('U');
		$unix_timestamp_date = date('Y-m-d H:i:s', $unix_timestamp );
											
		if( !empty( $live_id ) )
		{
			$data->id = $live_id;
			unset( $data->live_id );
			unset( $data->entered );
			
			if(  isset( $options['minor'] ) && (int)$options['minor'] === (int)'1' )
			{
				unset( $data->modified );	
			}else{
				$data->modified = $unix_timestamp_date;
			}
			
			$ret = $this->_db->updateObject( $tbl_name, $data, 'id', true );
		}else if( empty( $data->live_id ) )
		{
//Insert new oCitation into the live table
			$data->id = '';
			$data->entered = $unix_timestamp_date;
			$data->modified = $unix_timestamp_date;

			$ret = $this->_db->insertObject( $tbl_name, $data, 'id', true );
		}
		$live_id = $data->id;
		
//Delete unpublished item
		$this->deleteUnpublishedById( $unpublished_id );
			
		//$front_end = JURI::root();//str_replace( "administrator",'',JURI::base(true));
		//$link = JRoute::_($front_end.'index.php?option=com_gpo&task=citation&type=' . $this->type . '&id=' . $live_id,false );		
		$link = Route::link("site", 'index.php?option=com_gpo&task=citation&type=' . $this->type . '&id=' . $live_id);

		$response = new stdClass();
		$response->live_id = $live_id;
		$response->msg = 'Published QCite <b>' . $live_id . '</b> <a href="' . $link . '" target="_blank">click here to view it live</a>';
//maybe change this to a splash screen. Once it works.		
		$response->link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type, false );
		$response->pass= true;

		return $response;		
	}
	



	/*
	 * Delete the unpublished quote record including its references in the locations section
	 */
	function deleteUnpublishedById( $id )
	{
		$tbl_name = $this->make_table_name( false );
		$query = "DELETE FROM " . $tbl_name . " WHERE `id`=" . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		$ret = $this->_db->execute();
		/* Feb 1, 2011: I don't know why this query is executed. so I disabled 
		    $query = "DELETE FROM `#__gpo_citations_locations_unpublished` WHERE `ext_id`=" . $this->_db->quote( $id ) . " AND `ext_type`=" . $this->_db->quote( $this->type );
		    $this->_db->setQuery( $query );
		    $ret = $this->_db->execute();
		*/
		//echo $this->_db->getQuery();
		//var_dump($ret);

		$this->deleteFromQueue( $id );		
		return $ret;
	}

	
	function deleteFromQueue( $id )
	{
		$query = "DELETE FROM `#__gpo_awaiting_published` WHERE `ext_table`='qc' AND `ext_id`=" . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		$ret = $this->_db->execute();
	}
	
	
	
	/*
	 * Delete the published news record including its references in the locations section
	 */
	function deletePublishedById( $id )
	{
		$tbl_name = $this->make_table_name( false );
		$query = "DELETE `cu`.* FROM  " . $tbl_name . " as `cu` WHERE `cu`.`live_id`=" . $this->_db->quote( $id ) . ";";
		$this->_db->setQuery( $query );
		$ret = $this->_db->execute();
		if( $ret !== true )
		{
			die( 'Fatal error: problem deleting Published item:cu' );
		}
		
//swap to published table		
		$tbl_name = $this->make_table_name( true );
		$query = "DELETE `c`.* FROM " . $tbl_name . " as `c` WHERE `c`.`id`=" . $this->_db->quote( $id ) . ";";
		$this->_db->setQuery( $query );
		$ret = $this->_db->execute();
		if( $ret !== true )
		{
			die( 'Fatal error: problem deleting Published item:c' );
		}		
		return $ret;
	}
	
	
	
	function canPublish( $oCitation )
	{
		$response = new stdClass();
		$response->pass = false;
		$response->msg = 'This is not ready for publishing';
		$response->link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=edit&id=' . $oCitation->id, false );

		$live_id= (isset( $oCitation->live_id) ) ? $oCitation->live_id : null;
		$unpublished_id = $oCitation->id;
		
		$clean = array();
		$fields = $this->fields( true );
		$fields->locations = "";
		
		foreach( $fields as $key )
		{
			$clean[$key] = ( isset($oCitation->$key) ) ? $oCitation->$key : '';
		}

		foreach( $clean as $key=>$value )
		{
			$r = $this->rule( $key, $value );
			if( $r === false )
			{
				echo $r;
				return $response;
			}
		}
		$response->pass = true;
		return $response;		
	}



	function published()
	{
		$search_id = Joomla\CMS\Factory::getApplication()->getInput()->get('search_id', '', '', 'string');
		$search_ext_id = Joomla\CMS\Factory::getApplication()->getInput()->get('search_ext_id', '', '', 'string');
		$author = Joomla\CMS\Factory::getApplication()->getInput()->get('author', '', '', 'string');
                
                $where = "";		
		if( !empty( $search_id ) )
		{
			$where = "where `id`=" . $this->_db->quote( $search_id );
		}else if( !empty( $search_ext_id ) )
		{
			$search_ext_id = str_replace( "Q",'', $search_ext_id );
			$search_ext_id = str_replace( "q",'', $search_ext_id );
			
			$where = "where `ext_id`=" . $this->_db->quote( $search_ext_id );
		}else if( !empty( $author ) )
		{
			$where = "where `author`=" . $this->_db->quote( $author );
		}

		$this->unpublishedTotal = $this->total( false, $where );
		$this->total = $this->total( true, $where );

		if( (int)$this->total === (int)'0')
		{
			return array();
		}
		
		if( (int)$this->total === 1 && !empty( $search_id ))
		{
			$url = JRoute::_( 'index.php?option=com_gpo&controller=citations&task=edit&type='.$this->type . '&live_id='. $search_id,false );
			$mainframe =& JFactory::getApplication();
			//global $mainframe;
			$mainframe->redirect( $url );
		}
		
                
		$this->pagination = new JPagination( $this->total, $this->limitstart, $this->limit );				
		$tbl_name = $this->make_table_name( true );		
		$query = "SELECT *, CONCAT('Q',`ext_id`) as `ext_id` FROM " . $tbl_name;
		$query .= " " . $where;
		
		$orderby = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', '', '', 'string');
		$orderbydir = ( Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', '', '', 'string') === 'asc' ) ? 'ASC' : 'DESC';		
		if( in_array( $orderby, array( 'published', 'id' ) ) )
		{
				$query .= " ORDER BY `" . $orderby . "` " . $orderbydir;			
		}else{
			$query .= " ORDER BY `id` DESC ";
		}		
//		ftp_debug( $_POST, '$_POST', true, false );
//		ftp_debug( $query, '$query', true, false );
		
		$this->_db->setQuery( $query, $this->pagination->limitstart, $this->pagination->limit );
		$data = $this->_db->loadAssocList();
		return $data;
	}
	
	
	
	function unpublished()
	{
		$this->userSearch = Joomla\CMS\Factory::getApplication()->getInput()->get('search', '', '', 'string');
		$author = Joomla\CMS\Factory::getApplication()->getInput()->get('author', '', '', 'string');

		$where = "";
		
		$this->userSearch = "";
		if( !empty( $this->userSearch ) )
		{
			$where = "where `id` LIKE " . $this->_db->quote( "%" . $this->userSearch . "%" ) . " OR `ext_id` LIKE " . $this->_db->quote( "%" . $this->userSearch . "%" );
		}else if( !empty( $author ) )
		{
			$where = "where `author`=" . $this->_db->quote( $author );
		}

		$this->total = $this->total( false, $where );
		if( (int)$this->total === (int)'0')
		{
			return array();
		}
		$this->pagination = new JPagination( $this->total, $this->limitstart, $this->limit );
		
		$tbl_name = $this->make_table_name( false );		

		$query = "SELECT `c` . * , `ap`.`id` AS `ap_id`,CONCAT('Q',`c`.`ext_id`) as `ext_id` FROM " . $tbl_name . " AS `c` LEFT JOIN `#__gpo_awaiting_published` AS `ap` ON `c`.`id` = `ap`.`ext_id` AND `ap`.`ext_table` = 'qc'";
		$query .= " ORDER BY `modified` ASC";
		
		$this->_db->setQuery( $query, $this->pagination->limitstart, $this->pagination->limit );
		$data = $this->_db->loadAssocList();
		return $data;
	}
	
	

	/*
	 * creates the citation table name
	 */
	function make_table_name( $published=false,$quotes=true )
	{
		if( $quotes )
		{
			$quote = "`";
		}else{
			$quote = "";
		}
		return $quote . '#__gpo_citations_' . $this->type . ( ( $published === false ) ? '_unpublished' : '' ) . $quote;
	}
	
	
		
	function emptyUnpublished()
	{
		$tbl_name = $this->make_table_name( false );
		$query = "TRUNCATE TABLE " . $tbl_name;
		$this->_db->setQuery( $query );
		$this->_db->execute();
		
		$query = "DELETE FROM `#__gpo_awaiting_published` WHERE `ext_table`='qc'";
		$this->_db->setQuery( $query );
		$this->_db->execute();
		
		$response = new stdClass();
		$response->pass = false;
		$response->msg = 'Unpublished Citations have been deleted';
		$response->link = 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=published';
		return $response;
	}
	
	
	
	function total( $published=false, $where="" )
	{
		$this->tbl_name = $this->make_table_name( $published );		
		$query = "SELECT COUNT( `id` ) FROM " . $this->tbl_name . " "  . $where . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	
	
	/*
	 * this is used for unpublished blank entry
	 * live_id
	 */
	function fields( $published=false)
	{
		if( $published === false )
		{
			$fields="id,live_id,published,currentdate,title,source,publisher,volume,issue,page,city,author,content,websource,entered,modified,notes,share,ext_id,affiliation,sourcedoc";
		}else{
			$fields="id,published,currentdate,title,source,publisher,volume,issue,page,city,author,content,websource,entered,modified,notes,share,ext_id,sourcedoc";
		}
		return (object)array_fill_keys( array_values( explode(",",$fields) ), '' );
	}

                  function stripslashes_recursive($value) {
                                    $value = is_array($value) ?
                                    array_map(array($this, 'stripslashes_recursive'), $value) :
                                    stripslashes($value);
                                    return $value;
                  }

	function save( $input, $boolean_answer = false )
	{		

		$fields = $this->fields();
		$blankObject = $this->fields(false);
		$data = (object)array_intersect_key( (array)$input,(array)$blankObject );		
		if( isset( $data->id ) && $data->id === '0')
		{
			$ignore_id = true;
		}
		//check rules
		$return = array();
		//$data = (object)JRequest::_stripSlashesRecursive( (array)$data );
                                    $data = (object) $this->stripslashes_recursive((array) $data);
		
		//ftp_debug( $data, 'data before', true, false );
		$allow_breaks = array( 'notes','content' );
		foreach( $data as $key=>$value )
		{
                        $value = str_replace('<', '&lt;', $value);
                        $value = str_replace('>', '&gt;', $value);
			$value = GpoCleanInput( $value, 'quotes' );
			if( !in_array( $key, $allow_breaks ) )
			{
			    $value = GpoSingleLine( $value );
			}
			$data->$key = $value;	
		}
		//ftp_debug( $data, 'data after');
                
		foreach( $data as $key=>$value )
		{
			if( $key !== 'id' )
			{
				$r = $this->rule( $key, $value );
				if( $r !== true )
				{
					$return[]=$r;
				}
			  }
                            else{
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
//page prefix		
		if( !empty( $data->page ) )
		{
			if( strlen( $data->page ) < 2 )
			{
				$return[] = $this->getRule( 'page', true );				
			}else  if( substr( $data->page,0,1 ) !== 'p' )
			{
				$return[] = $this->getRule( 'page', true );
			}
		}
		
		if( count( $return ) > 0 )
		{
			if( $boolean_answer )
			{
				return false;
			}	
			$js = "<script>";
			$js .= <<<EOJS
$('adminForm').select( '.error_warning').invoke('removeClassName','error_warning');
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
		if( !empty( $data->published ) )
		{
                        
                          $date = new DateTime($data->published);   
			  $data->published = $date->format('Y-m-d');     
                        
		    }else{
			echo 'data->published is blank';
			exit();
			//$data->published = new DateTime('1000-10-10');
                }
                
                
		$tbl_name = $this->make_table_name( false,false );		
		$unix_timestamp = ( isset( $_SERVER['REQUEST_TIME'] ) ) ? $_SERVER['REQUEST_TIME'] : date('U');
		$data->modified = date( 'Y-m-d H:i:s', $unix_timestamp );		
		if( $data->id === '0')
		{
			$data->entered = date( 'Y-m-d H:i:s', $unix_timestamp );
			$ret = $this->_db->insertObject( $tbl_name, $data, 'id' );
		}else{
                                     
		       $ret = $this->_db->updateObject( $tbl_name, $data, 'id', false );
		       //ftp_debug( $data, 'data update');
		   }
		if( !$ret )
		{
			if( $boolean_answer )
			{
				return false;
			}
			return 'An error occured whilst saving';
		}		
				
		if( $boolean_answer )
		{
			return true;
		}
                
		$this->new_id = $data->id;
                
		if( isset($_POST['new_record']) && $_POST['new_record'] === '1' )
		{
			$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&task=create&type=' . $this->type, false );
			
		}else{
			$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type .'&task=unpublished', false );
		}
		$js = "<script>window.location='" . $link . "'</script>";	
		return $js;
	}
	
	
	
	/*
	 * Copy from news/quote to citation news/quote
	 */
	function copyFromType( $item,$published=false )
	{
//move item->id to ext_id
		$item->ext_id = $item->id;
		
		$blankObject = $this->fields(false);
		$data = (object)array_intersect_key( (array)$item,(array)$blankObject );

		$data->live_id='';
		$data->id = '';
//set timestamps		
		$unix_timestamp = ( isset( $_SERVER['REQUEST_TIME'] ) ) ? $_SERVER['REQUEST_TIME'] : date('U');
		$data->modified = date( 'Y-m-d H:i:s', $unix_timestamp );				
		$data->entered = $data->modified;
		
//set default share
		$data->share = '1';

		$tbl_name = $this->make_table_name( false,false );
		$ret = $this->_db->insertObject( $tbl_name, $data, 'id' );
		$citation_id = $this->_db->insertid();
		$response = new stdClass();
		$response->citation_id = $citation_id;
		$response->pass = $ret;
		$response->pass = (boolean)$ret;	
		$response->link = 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=edit&id=' . $data->id;
		return $response;
	}

	function copyForEdit( $id )
	{
//If already in edit, stop.
		$tbl_name = $this->make_table_name( false );
		$query = "SELECT `id`,`live_id` FROM " . $tbl_name . " WHERE `live_id`=" . $this->_db->quote( $id ) . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		$oLive = $this->_db->loadObject();
//this should be checking live_id!!!		
		if( !empty( $oLive->live_id) )
		{
			return $oLive;
		}
//set timestamps		
		$unix_timestamp = ( isset( $_SERVER['REQUEST_TIME'] ) ) ? $_SERVER['REQUEST_TIME'] : date('U');
		$data->modified = date( 'Y-m-d H:i:s', $unix_timestamp );
		
//Create an edit		
		$tbl_name = $this->make_table_name( true );		
		$query = "SELECT * FROM " . $tbl_name . " WHERE `id`=" . $this->_db->quote( $id ) . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		$blankObject = $this->fields(false);
		$data = (object)array_intersect_key( (array)$item,(array)$blankObject );

		$data->live_id = $data->id;
		$data->id = '';
		
		$tbl_name = $this->make_table_name( false,false );
		$ret = $this->_db->insertObject( $tbl_name, $data, 'id' );

        if( $ret )
		{
			return $data;
		}
		$o = null;
		$o->id = '0';
		return $o;
	}
	
	
	
	function getInfoForLookup( $id, $state = 'published' )
	{
        
        $isUnpublished = ($state == 'unpublished') ? true : false;
//get data from _unpublished table
        if( true === $isUnpublished) {
            $tbl_name = $this->make_table_name( false );
            $query = "SELECT * FROM " . $tbl_name . " WHERE `id`=" . $this->_db->quote( $id ) . " LIMIT 0,1";
            $this->_db->setQuery( $query );
            $oLive = $this->_db->loadObject();

            return $oLive;
        }else {
            
//get data from live quotes table		
            $tbl_name = $this->make_table_name( true );
            $query = "SELECT * FROM " . $tbl_name . " WHERE `id`=" . $this->_db->quote( $id ) . " LIMIT 0,1";
            $this->_db->setQuery( $query );
            $item = $this->_db->loadObject();
		
        	$blankObject = $this->fields(false);
            $data = (object)array_intersect_key( (array)$item,(array)$blankObject );

            $data->live_id = $data->id;
            $data->id = '';
                
            return $data;
        }
        
	}
	
	
	function getRule( $field, $message = false )
	{
		$rules=array(
					"id"=>array("required"=>true),
					"title"=>array("required"=>true),					
					"published"=>array('required'=>true,'currentdate'=>true),
					"author"=>array("required"=>true),
					"content"=>array("required"=>true),
//					"websource"=>array("required"=>true),
					"affiliation"=>array("empty"=>true),
					"source"=>array("required"=>true),
					"city"=>array("required"=>true)				
					);
		$messages = array(
						"id"=>array(
							"id"=>"citations_id",
							"message"=>"id should not be empty"
							),
						"title"=>array(
								"id"=>"citations_title",
								"message"=>"title should not be empty"
								),
						"published"=>array(
								"id"=>"citations_published",
								"message"=>"Published needs to be todays date or a past date"
								),
						"author"=>array(
								"id"=>"citations_author",
								"message"=>"author should not be empty"
								),
						"content"=>array(
								"id"=>"citations_content",
								"message"=>"content should not be empty"
								),
						"affiliation"=>array(
								"id"=>"citations_affiliation",
								"message"=>"affiliation should be empty"
								),
						"page"=>array(
								"id"=>"citations_page",
								"message"=>"Page number(s) must be prefixed. See pop-up help tip for this field"
								),
						"source"=>array(
								"id"=>"citations_source",
								"message"=>"Source must not be empty"
								),
						"city"=>array(
								"id"=>"citations_city_label",
								"message"=>"City must not be empty"
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
			if( $pass === true && $rule['empty'] === true && empty( $value) !== true){$pass=false;}			
			if( $pass !== true )
			{
				return $this->getRule($field, true);
			}
		}
		return $pass;
	}



	function filterAuthor( $published = true )
	{
		$author = Joomla\CMS\Factory::getApplication()->getInput()->get('author', '', '', 'string');
		$task = Joomla\CMS\Factory::getApplication()->getInput()->get('author', '', '', 'string');

		$tbl_name = $this->make_table_name( $published );
		$filter	= null;
				
		$query = 'SELECT DISTINCT( `author` ) AS value, `author` AS text FROM ' . $tbl_name . ' ORDER BY `author` ASC';
		// Initialize variables
		$db	= & JFactory::getDBO();
		$items = array();
		$items[] = JHTML::_('select.option', '0', '- '.JText::_('Select Author').' -');
		$db->setQuery($query);
		$items = array_merge( $items, $db->loadObjectList());

		$html = JHTML::_('select.genericlist',  $items, 'author', 'class="inputbox" style="width:275px;" onchange="document.adminForm.submit( );"', 'value', 'text', $author);
		return $html;
	}	
	
	
	function isBuildInProgress()
	{
		$filename_inprogress = 'inprogress.txt';
		exec( "ls -l " . $this->path . " | awk '{print $9}'", $output );

		if( in_array( $filename_inprogress, $output ) )
		{
			return true;
		}
		return false;
	}
	
	
	
	function isReIndexInProgress()
	{
		$filename = $this->path . 'reindex_inprogress.txt';
		if( file_exists( $filename ) )
		{
			return true;
		}
		return false;
	}
	
	
	
	function shouldReIndexForSphinx()
	{
		$filename = $this->path . 'reindex.txt';
		if( file_exists( $filename ) )
		{
			return true;
		}
		return false;
	}

	
		
	function setReIndex()
	{
		$filename = $this->path . 'reindex.txt';
		touch( $filename );
	}

    
    /* function to get next quotes citatoion id */
    function getNextById($id,$state) {
        if($state!='published'){$state= '_'.$state;}else{$state = '';}
        if ((int) $id === (int) '0') {
            return false;
        }
        $query = "SELECT `id` FROM #__gpo_citations_quotes".$state." WHERE `id` > " . $this->_db->quote($id) . " ORDER BY `id` ASC LIMIT 1";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        
        if (empty($data->id)) {
            return false;
        }
        return $data->id;
    }
    
    /* function to get prevous quotes citatoion id */
    function getPrevById($id,$state) {
        if($state!='published'){$state= '_'.$state;}else{$state = '';}
        if ((int) $id === (int) '0') {
            return false;
        }
        $query = "SELECT `id` FROM #__gpo_citations_quotes".$state." WHERE `id` < " . $this->_db->quote($id) . " ORDER BY `id` DESC LIMIT 1";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        if (empty($data->id)) {
            return false;
        }
        return $data->id;
    }
	
}
?>
