<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');


/*
 * @author: murshed <khan.morshed@gmail.com>
 * @date: 2011-08-27
 * @purpose: Find & Replace tool related functions for 
 * other GPO tables, except "datapages" table. 
 * 
 * Use this model, For "Quotes, News, Q-Cites, N-Cites" etc. tables.
 * For "datapages" table: use findreplace.php instead.
 * 
 * 
 */

class GpoModelFindreplacecommon extends JModelList
{
	function __construct( $p_options )
	{
		parent::__construct();
		ini_set("max_execution_time",0);
		ini_set("memory_limit" , "512M");
		$this->total = 0;
		$this->from = "";
		$this->limit = Joomla\CMS\Factory::getApplication()->getInput()->get('limit', '100', '', 'int');
        $this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', '', 'int');
        $this->updatedIDs = '';
        
		if( $p_options ) {
		    $this->tableName = $p_options['table_name'];	
			$this->columnName = $p_options['column_name'];
			$this->from = $p_options['from'];
			$this->to = $p_options['to'];
			$this->isCaseSensitive = ($p_options['case_sensitive']) ? TRUE : FALSE;
		}

	}


	function frtInsertSearchHistory( $p_insert_array, $p_affected_rows, $p_username ) {
		
		$options = ($p_insert_array['case_sensitive']) ? 'case_sensitive' : 'case_insensitive';
		if( !empty($p_insert_array['regular_expression']) ) {
			$options .= ' regular_expression';
		}
		$insert_array = array( 'from' => $p_insert_array['from'],
		                       'to' => $p_insert_array['to'],
		  					   'table_name' => $p_insert_array['table_name'],
		 					   'column_name' => $p_insert_array['column_name'],
		                       'options' => $options,
		                       'total_updated_rows' => $p_affected_rows,
		                       'updated_ids' => $this->updatedIDs,
		                       'author' => $p_username,
		                       'type' => 'replace',
		                       'created_at' => $p_insert_array['created_at'],
		 					   'updated_at' => $p_insert_array['updated_at']
		                );
		if( empty($p_insert_array) || empty($insert_array) ) {
			return false;
		}
		$insert_array = (object)$insert_array;
		$ret = $this->_db->insertObject( "#__gpo_frt_common_search_history", $insert_array );
		return true;
	}
	

/*
 * insert last searched query.
 * we'll delete & update this last searched item everytime
 * 
 */	
	
    function frtInsertLastSearch( $p_insert_array, $p_username ) {
		
		$options = ($p_insert_array['case_sensitive']) ? 'case_sensitive' : 'case_insensitive';
		if( !empty($p_insert_array['regular_expression']) ) {
			$options .= ' regular_expression';
		}
		$insert_array = array( 'from' => $p_insert_array['from'],
		                       'to' => $p_insert_array['to'],
		  					   'table_name' => $p_insert_array['table_name'],
		 					   'column_name' => $p_insert_array['column_name'],
		                       'options' => $options,
		                       'author' => $p_username,
		 					   'type' => 'search',
		                       'created_at' => $p_insert_array['created_at'],
		 					   'updated_at' => $p_insert_array['updated_at']
		                );
		if( empty($p_insert_array) || empty($insert_array) ) {
			return false;
		}
		//first delete the last searched item
		$this->frtDeleteLastSearch($insert_array);
		//then insert the new searched item
		$insert_array = (object)$insert_array;
		$ret = $this->_db->insertObject( "#__gpo_frt_common_search_history", $insert_array );
		return true;
	}
	
	
    function frtDeleteLastSearch( $p_insert_array ) {
		
	
		if( empty($p_insert_array) ) {
			return false;
		}
		
		$query = "DELETE 
		              FROM `#__gpo_frt_common_search_history`  
		          WHERE 
		              `table_name`=" . $this->_db->quote($p_insert_array['table_name']) .
		         " AND `type`='search'";
		
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
		return $ret;
	}
	
	
	
	/*
	 * 
	 * replaces the searched items with the replacement one
	 * and returns the final result array
	 * 
	 */
	function frtSearchReplace( $p_search_result, $p_options=array() ) { 
       $from = $p_options['from'];
       $to = $p_options['to'];
       $isCaseSensitive = ( $p_options['case_sensitive'] ) ? TRUE : FALSE;
       $replacedArray = array();
             
       foreach( $p_search_result as $row ) {
       	  if( $isCaseSensitive ) {
       	    $replacedArray["$row->id"] = str_replace( $from, $to, $row->content ); //case sensitive replace 
       	  }else {
       	  	$replacedArray["$row->id"] = str_ireplace( $from, $to, $row->content ); //case insensitive replace
       	  }  
       }
       return $replacedArray;
	}

	
	
	/*
	 * Search for the specified terms in the
	 * specific column & return the result set
	 */
	function frtPerformSearch( ) {
		
	    if( empty($this->tableName) || empty($this->columnName) ) {
	    	return false;
	    }
            
            $sql = "SET NAMES utf8";
            $this->_db->setQuery( $sql );
            $this->_db->execute();
	    
	    $sql = "    SELECT count(`t`.`id`)
					FROM `#__$this->tableName` as `t` 
					WHERE `t`.`$this->columnName` LIKE ";
		//$sql   .=   ($this->isCaseSensitive) ? ' BINARY ' : ' ' ;
		$sql   .=   $this->_db->quote( "%".$this->from ."%" );
		$sql   .=   ($this->isCaseSensitive) ? ' COLLATE utf8_bin ' : ' COLLATE utf8_general_ci ' ;
                			
		$this->_db->setQuery( $sql );
	    $total = $this->_db->loadRow();
            $this->total = $total[0];

            $this->pagination = new JPagination($this->total, $this->limitstart, $this->limit);
	    
		$sql = "
					SELECT 
                    `t`.*, 
                    `t`.`id`, 
                    `t`.`$this->columnName` as content 
			   ";
		
		if ( 'gpo_citations_quotes' == $this->tableName ) {
			$sql .= ", `t`.author "; 
		}
		
					
		$sql .= "    FROM `#__$this->tableName` as `t` 
					
					WHERE `t`.`$this->columnName` LIKE ";
		
		//$sql   .=   ($this->isCaseSensitive) ? ' BINARY ' : ' ' ;
		
		$sql   .=   $this->_db->quote( "%".$this->from ."%" );
		
		$sql   .=   ($this->isCaseSensitive) ? ' COLLATE utf8_bin ' : ' COLLATE utf8_general_ci ' ;
		
		$sql   .=   " ORDER BY " . Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id') . ' ' . 
					Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_dir', 'DESC') . ' ';
					
		$this->_db->setQuery( $sql, $this->pagination->limitstart, $this->pagination->limit );
		$data = $this->_db->loadObjectList();
		//$this->total = count( $data );
		
		return $data;		
	}
	


	/*
	 * find corresponding matches in the qcites table
	 * related with Quotes search
	 * 
	 */
	
	function frtQcitesFinding( $p_columnname=NULL ) {
		
	    $tableName = 'gpo_citations_quotes';
	    $columnName = !empty( $p_columnname ) ? $p_columnname : $this->columnName;	    
		
	    if( empty($tableName) || empty($columnName) || $columnName == 'sourcedoc') {
	    	return false;
	    }
	    
	    $sql = "    SELECT count(`t`.`id`)
					FROM `#__$tableName` as `t` 
					WHERE `t`.`$columnName` LIKE ";
		//$sql   .=   ($this->isCaseSensitive) ? ' BINARY ' : ' ' ;
		$sql   .=   $this->_db->quote( "%".$this->from ."%" );
		$sql   .=   ($this->isCaseSensitive) ? ' ' : ' COLLATE utf8_general_ci ' ;
					
	    $this->_db->setQuery( $sql );
	    $total = $this->_db->loadRow();
	    
            $totalRows = $total[0];
	    return $totalRows;		
	}
	

    	/*
	 * find corresponding matches in the qcites table
	 * related with Quotes search
	 * 
	 */
	
	function frtNcitesFinding( $p_columnname=NULL ) {
		
	    $tableName = 'gpo_citations_news';
	    $columnName = !empty( $p_columnname ) ? $p_columnname : $this->columnName;	    
		
		if( empty($tableName) || empty($columnName) ) {
	    	return false;
	    }
	    
	    $sql = "    SELECT count(`t`.`id`)
					FROM `#__$tableName` as `t` 
					WHERE `t`.`$columnName` LIKE ";
		//$sql   .=   ($this->isCaseSensitive) ? ' BINARY ' : ' ' ;
		$sql   .=   $this->_db->quote( "%".$this->from ."%" );
		$sql   .=   ($this->isCaseSensitive) ? ' ' : ' COLLATE utf8_general_ci ' ;
					
		$this->_db->setQuery( $sql );
	    $total = $this->_db->loadRow();
	    
        $totalRows = $total[0];
		return $totalRows;		
	}
	

    
	
	/*
	 * update the rows with the posted items
	 */
	function frtUpdateRows( $p_search_options ) {
	   $cids = Joomla\CMS\Factory::getApplication()->getInput()->get('cid', false);
	   $total = 0;
	   
	   $this->tableName = empty( $this->tableName ) ? $p_search_options['table_name'] : $this->tableName;	
	   $columnName = $p_search_options['column_name'];
       $this->columnName = empty( $this->columnName ) ? $columnName : $this->columnName;
       $updatedIDs = array();
	   foreach( $cids as $key => $val ) {
	   	  $updatedValue = Joomla\CMS\Factory::getApplication()->getInput()->get( 'replace'.$val, false, 'POST', 'none', JREQUEST_ALLOWRAW );
	   	  //if ( $updatedValue ){ //empty value will be allowed
	   	  	$updateQuery = $this->createUpdateQuery( $this->tableName, $this->columnName, $updatedValue, $val );
	   	  	$this->_db->setQuery($updateQuery);
	   	  	$result = $this->_db->execute();
	   	  	$total++;
	   	  	$updatedIDs[] = $val;
	   	  //}
	   }
	   $this->updatedIDs = implode(', ', $updatedIDs);
	   return $total;
	}
	
	
	/*
	 * makes the update-sql query 
	 */
	function createUpdateQuery( $p_t_name, $p_c_name, $p_val, $p_id ) {
		
		$sql = "UPDATE 
		               `#__$p_t_name` 
		        SET 
		               `#__$p_t_name`.`$p_c_name` = " . $this->_db->Quote($p_val) .
		        " WHERE 
		               `#__$p_t_name`.`id` = " . $this->_db->Quote($p_id);
		return $sql;
	}
	
	
	
    /*
	 * get first 300 rows of the frt history(only replaces) table   
	 */
	function frtGetHistory( $p_tablename ) {
		
		$sql = "SELECT 
		     		  *
		        FROM 		   
		              `#__gpo_frt_common_search_history` 
		        WHERE `table_name` = " . $this->_db->Quote( $p_tablename ) .
		        " AND `type`='replace' " .          
		        "ORDER BY 
		              `id` DESC   
		        LIMIT 600";
		        
		$this->_db->setQuery( $sql );
		$data = $this->_db->loadObjectList( );
		return $data;
	}
	
    /*
	 * get last searched item 
	 * 
	 */
	function frtGetLastSearchedQuery( $p_tablename ) {
		
		if( empty($p_tablename) ){
			return false;
		}
		
		$sql = "SELECT 
		     		  *
		        FROM 		   
		              `#__gpo_frt_common_search_history` 
		        WHERE 
		              `table_name` = " . $this->_db->Quote( $p_tablename ) .         
		        "AND 
		             `type` = 'search' ";
		        
		$this->_db->setQuery( $sql );
		$data = $this->_db->loadObject();
		return $data;
	}
	
}
?>
