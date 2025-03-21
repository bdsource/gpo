<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');


class GpoModelFindreplace extends JModelList
{
    
    private $currentLang;
    private $tableSeparator;
    
    private $DPTable              = '#__gpo_datapages';
    private $DPPreambleTable      = '#__gpo_datapage_preamble_values';
    private $DPMasterListTable    = '#__gpo_preambles_switches_master_list';
    private $DPHierarchyTable     = '#__gpo_datapage_hierarchy';
    private $DPFRTSearchHistory   = '#__gpo_frt_search_history';
    private $DPFRTUpdateHistory   = '#__gpo_frtdp_update_history';
    public  $metaColumns          = array();
    public  $findTerm              = NULL;
    
    ## For FRT History Flags
    private  $affectedColumns      = array();
    private  $affectedLocationIDs  = array();
    private  $affectedLanguages    = array();
    private  $currentUser          = NULL;
    
    public  $tableName;
    public  $columnName;
    public  $total = 0;
    public  $from  = "";
    public  $to;
    public  $isCaseSensitive;
    public  $isRegex;
    public  $uniqid;
    
	function __construct( $p_options )
	{
		parent::__construct();
		
		if( $p_options ) {
		    $this->from = $p_options['from'];
            $this->findTerm = $this->from;
            $this->findTerm = $this->_db->escape($this->findTerm,true);
            
            ## 
            #  mysql_real_escape_string() does not escape % and _
            #  These are wildcards in MySQL
            #  so you need to escape %,_ through your own mechanism
            ## 
            ### Escape '%'
            if( strpos($this->from, '%') !== false ) {
                $this->findTerm = addcslashes($this->from, '%');
            } 
            ## Escape '_'
            if ( strpos($this->from, '_') !== false ) {
                 $this->findTerm = addcslashes($this->from, '_');
            }
            
			$this->to              = $p_options['to'];
			$this->isCaseSensitive = ($p_options['case_sensitive'])     ? TRUE : FALSE;
			$this->isRegex         = ($p_options['regular_expression']) ? TRUE : FALSE;
		}
        
        $this->currentLang    = strtolower($p_options['currentLang']);
        $this->tableSeparator = '_';
        $this->metaColumns = array('id','location_id','location','created_at','updated_at','published_at');
        $this->affectedLanguages = array( $this->currentLang );
        $this->currentUser = JFactory::getUser();
        $this->uniqid = uniqid();
        
        $this->_initializeTableNames($p_options);
	}

	
     function _initializeTableNames($p_options) {
        
        /* 
         * initialize DP table names according to lang switch
         * 
         */
        
        $this->DPTable = $this->_makeTableName($this->DPTable);
        $this->DPPreambleTable = $this->_makeTableName($this->DPPreambleTable);
        
        // Initialize on which table or column to search
        if( !empty($p_options) ) {
            
            if( stripos($p_options['table_name'], 'preamble') !==false ) {
               $this->tableName  = $this->DPPreambleTable;
               $this->columnName = trim($p_options['column_name']) . '_p';
            }else {
               $this->tableName  = $this->DPTable;
               $this->columnName = trim($p_options['column_name']);
            }
            
        }
    }
    
    
    function _makeTableName($inputName) {
        if( empty($inputName) ) {
            return $inputName;
        }
        
        #For English we won't add language prefix as it is the default
        if('en' == $this->currentLang || empty($this->currentLang)) {
            $tableName = trim($inputName);
        }else {
            $tableName = trim($inputName . $this->tableSeparator . $this->currentLang);
        }
        
        return $tableName;
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
         
       foreach( $p_search_result as $key => $row ) {
       	  $replacedContent = ($isCaseSensitive) ? str_replace($from, $to, $row->content)
                                                : str_ireplace($from, $to, $row->content);
          
          $replacedArray[$key] = clone $row;
          $replacedArray[$key]->content = $replacedContent;
          
          if('en' == $this->currentLang) 
          {
              $replacedContentES = ($isCaseSensitive) ? str_replace($from, $to, $row->content_es)
                                                      : str_ireplace($from, $to, $row->content_es);
              
              $replacedContentFR = ($isCaseSensitive) ? str_replace($from, $to, $row->content_fr)
                                                      : str_ireplace($from, $to, $row->content_fr);
              $replacedArray[$key]->content_es = $replacedContentES;
              $replacedArray[$key]->content_fr = $replacedContentFR;
          }
          
          /*
          if( $isCaseSensitive ) {
            $replacedContent = str_replace( $from, $to, $content );
            $replacedArray[$key] = clone $row;
            $replacedArray[$key]->content = $replacedContent;
       	    //$replacedArray["$row->id"] = str_replace( $from, $to, $row->content ); //case sensitive replace 
       	  }else {
            $replacedContent   = str_ireplace( $from, $to, $content );
            $replacedContentES = str_ireplace( $from, $to, $row->content_es );
            $replacedContentFR = str_ireplace( $from, $to, $row->content_fr );
            $replacedArray[$key] = clone $row;
            $replacedArray[$key]->content = $replacedContent;
            $replacedArray[$key]->content_es = $replacedContentES;
            $replacedArray[$key]->content_fr = $replacedContentFR;
       	  	//$replacedArray["$row->id"] = str_ireplace( $from, $to, $row->content ); //case insensitive replace
       	  }
          */
       }
       
       //array_slice($p_search_result,0,5);
       //array_slice($replacedArray, 0,5);
       
       return $replacedArray;
	}


    /*
	 * 
	 * replaces the searched items with the replacement one
	 * and returns the final result array
	 * 
	 */
	function frtSearchCorrectionManualReplace( $p_search_result, $p_options=array() ) {
       $replacedArray = array();
       
       foreach( $p_search_result as $key => $row ) {
       	    //$replacedArray["$row->id"] =  $row->content ;
           $replacedArray[$key] = clone $row;
           $replacedArray[$key]->content = $row->content;
           
           if('en' == $this->currentLang) 
           {
              $replacedArray[$key]->content_es = $row->content_es;
              $replacedArray[$key]->content_fr = $row->content_fr;
           }
       }
       
       return $replacedArray;
	}
	
    function isMetaColumn($columnName) {
        
        if ( empty($columnName) ) {
            return true;
        }
        
        if( in_array($columnName, $this->metaColumns) ) {
            return true;
        }
        
        return false;
    }
	
	/*
	 * Search for the specified terms in the
	 * specific column & return the result set
     * 
	 */
	function frtPerformSearch($USJurisdictionIDs = NULL) {

        if (empty($this->tableName)) {
            return false;
        }

        if ($_POST['swap']['all_categories']) {
            $sql_search = "";
            $sql_search_fields = Array();
            $sql = "SHOW COLUMNS FROM " . $this->tableName;
            $this->_db->setQuery($sql);
            $res = $this->_db->loadAssocList();
            $data = array();

            foreach ($res as $val) {
                $colum = $val['Field'];
                
                if( $this->isMetaColumn($colum) ) {
                    continue; #dont search meta columns
                }
                
                $sql = "
                       SELECT 
                             `t`.`id`, 
                             `t`.`location_id`,
                             `t`.`location`, 
                             `t`.`$colum` as content,
					         `l`.`name` ,
					         `l`.`name_es`,
					         `l`.`name_fr` ";
                             
                if('en' == $this->currentLang) {
                    $sql .= ",
                            `tes`.`$colum` as content_es,
                            `tfr`.`$colum` as content_fr ";
                }
                     
				$sql    .= "FROM 
                             `$this->tableName` as `t` 
					   LEFT JOIN 
                             `#__gpo_location` as `l` 
					   ON 
                             `l`.`id` = `t`.`location_id` ";
                                 
                if('en' == $this->currentLang) {
                    $sql .= "LEFT JOIN 
                                `{$this->tableName}_es` as `tes` 
					         ON 
                                `tes`.`location_id` = `t`.`location_id` "; 
                                
                    $sql .= "LEFT JOIN 
                                `{$this->tableName}_fr` as `tfr` 
					         ON 
                                `tfr`.`location_id` = `t`.`location_id` "; 
                }

				$sql .=	"WHERE
                             `t`.`$colum` LIKE ";
                $sql .= ($this->isCaseSensitive) ? ' BINARY ' : ' ';
                $sql .= $this->_db->quote("%" . $this->findTerm . "%",false);
                       
                if (!empty($USJurisdictionIDs)) {
                    $sql .= " AND `t`.`location_id` IN (" . $USJurisdictionIDs . ") ";
                } else {
                    $sql .= " AND `t`.location_id != 0 ";
                }
                $sql .= " ORDER BY `t`.`id` ASC";
                //echo $sql;
                $this->_db->setQuery($sql);
                
                $res = $this->_db->loadObjectList();
                if (!empty($res)) {
                    foreach ($res as $k => $v) {
                        //$res[$k] = $v;
                        $res[$k]->column = $colum;
                    }
                    $data = array_merge($data, $res);
                    $this->total = count($data);
                }

            }
        } else {
            $sql = "
					SELECT 
                           `t`.`id`, 
                           `t`.`location_id`, 
                           `t`.`location`,
                           `t`.`$this->columnName` as content,
					       `l`.`name`,
					       `l`.`name_es`,
					       `l`.`name_fr` ";
            
            if('en' == $this->currentLang) {
                    $sql .= ",
                            `tes`.`$this->columnName` as `content_es`,
                            `tfr`.`$this->columnName` as `content_fr` ";
            }
            
			$sql .=	"FROM 
                           `$this->tableName` as `t` 
					LEFT JOIN 
                           `#__gpo_location` as `l` 
					ON 
                           `l`.`id` = `t`.`location_id` ";
            
            
            if('en' == $this->currentLang) {
                    $sql .= "LEFT JOIN 
                                `{$this->tableName}_es` as `tes` 
					         ON 
                                `tes`.`location_id` = `t`.`location_id` "; 
                                
                    $sql .= "LEFT JOIN 
                                `{$this->tableName}_fr` as `tfr` 
					         ON 
                                `tfr`.`location_id` = `t`.`location_id` ";
            }
            
            
			$sql .=	"WHERE 
                           `t`.`$this->columnName` LIKE ";

            $sql .= ($this->isCaseSensitive) ? ' BINARY ' : ' ';

            $sql .= $this->_db->quote("%" . $this->findTerm . "%",false);
            
            if( !empty($USJurisdictionIDs) ) {
                $sql .= " AND `t`.`location_id` IN (" . $USJurisdictionIDs . ") ";
            }else {
                $sql .= " AND `t`.location_id != 0 ";
            }
            $sql .= " ORDER BY `t`.`id` ASC";
            //echo $sql;
            $this->_db->setQuery($sql);
            $data = $this->_db->loadObjectList();
            foreach ($data as $k => $v) {
                $data[$k]->column = $this->columnName;
                $data[$k] = $v;
            }
            
            $this->total = count($data);
        }
        
        return $data;
    }

    /*
	 * Search for the specified terms in the
	 * specific column & return the result set
	 */
	function frtPerformCorrectionManualSearch() {

        if (empty($this->tableName)) {
            return false;
        }

        if ($_POST['swap']['all_categories']) {
            $sql_search = "";
            $sql_search_fields = Array();
            $sql = "SHOW COLUMNS FROM " . $this->tableName;
            $this->_db->setQuery($sql);
            $res = $this->_db->loadAssocList();
            $data = array();
            foreach ($res as $val) {
                $colum = $val['Field'];
                
                if( $this->isMetaColumn($colum) ) {
                    continue; #dont search meta columns
                }
                
                $sql = "
					SELECT 
                           `t`.`id`, 
                           `t`.`location_id`, 
                           `t`.`location`, 
                           `t`.`$colum` as content,
					       `l`.`name`, 
                           `l`.`name_es`, 
                           `l`.`name_fr` ";
                
                if( 'en' == $this->currentLang ) {
                    $sql .= ",
                            `tes`.`$colum` as content_es,
                            `tfr`.`$colum` as content_fr ";
                }
                
				$sql .=	"FROM 
                            `$this->tableName` as `t` 
					    LEFT JOIN 
                            `#__gpo_location` as `l` 
					    ON 
                            `l`.`id` = `t`.`location_id` ";
                
                ## For English we need to show FR, ES values too
                if('en' == $this->currentLang) {
                    $sql .= "LEFT JOIN 
                                `{$this->tableName}_es` as `tes` 
					         ON 
                                `tes`.`location_id` = `t`.`location_id` "; 
                                
                    $sql .= "LEFT JOIN 
                                `{$this->tableName}_fr` as `tfr` 
					         ON 
                                `tfr`.`location_id` = `t`.`location_id` "; 
                }
                
				$sql .=	"WHERE 
                            `t`.`$colum` REGEXP ";
                $sql .= "'[0-9]{5,}' AND `t`.location_id != 0 	ORDER BY `t`.`id` ASC";

                $this->_db->setQuery($sql);

                $res = $this->_db->loadObjectList();
                if (!empty($res)) {
                    foreach ($res as $k => $v) {
                        //$res[$k] = $v;
                        $res[$k]->column = $colum;
                    }
                    $data = array_merge($data, $res);
                    $this->total = count($data);
                }
            }
        } else {
            $sql = "
					SELECT `t`.`id`, 
                           `t`.`location_id`, 
                           `t`.`location`, 
                           `t`.`$this->columnName` as content,
					       `l`.`name`, 
                           `l`.`name_es`, 
                           `l`.`name_fr` ";
            
            if('en' == $this->currentLang) {
                    $sql .= ",
                            `tes`.`$this->columnName` as `content_es`,
                            `tfr`.`$this->columnName` as `content_fr` ";
            }
                
			$sql .=	"FROM 
                        `$this->tableName` as `t` 
					LEFT JOIN 
                        `#__gpo_location` as `l` 
					ON 
                        `l`.`id` = `t`.`location_id` ";
            
            if('en' == $this->currentLang) {
                    $sql .= "LEFT JOIN 
                                `{$this->tableName}_es` as `tes` 
					         ON 
                                `tes`.`location_id` = `t`.`location_id` "; 
                                
                    $sql .= "LEFT JOIN 
                                `{$this->tableName}_fr` as `tfr` 
					         ON 
                                `tfr`.`location_id` = `t`.`location_id` ";
            }
            
			$sql .= "WHERE `t`.`$this->columnName` REGEXP ";
            $sql .= "'[0-9]{5,}' AND `t`.location_id != 0 ORDER BY `t`.`id` ASC";
            $this->_db->setQuery($sql);

            $data = $this->_db->loadObjectList();
            foreach ($data as $k => $v) {
                $data[$k]->column = $this->columnName;
                $data[$k] = $v;
            }
            $this->total = count($data);
        }
        return $data;
    }
  
	/*
	 * update the rows with the posted items of datapage items
	 */
	function frtUpdateRowsDatapage( $p_search_options ) {
	   $cids = Joomla\CMS\Factory::getApplication()->getInput()->get('cid', false);
	   $columns = Joomla\CMS\Factory::getApplication()->getInput()->get('column_name', false);
       
       ##Es/Fr replace options when you're in English version
       $dontReplaceES = Joomla\CMS\Factory::getApplication()->getInput()->get('dont_replace_es', false);
       $dontReplaceFR = Joomla\CMS\Factory::getApplication()->getInput()->get('dont_replace_fr', false);
	   $total = 0;
	          
       ##
       ## For English, we need to update Fr/Es versions too
       ##
       if( 'en' == $this->currentLang ) {
           $tableNameES = $this->tableName . '_' . 'es';
           $tableNameFR = $this->tableName . '_' . 'fr';
       }
	   $this->tableName = empty( $this->tableName ) ? $p_search_options['table_name'] : $this->tableName;	
	   //$columnName = ( stripos($p_search_options['table_name'], 'preamble') !==false ) ? $p_search_options['column_name'].'_p' : $p_search_options['column_name'];
       //$this->columnName = empty( $this->columnName ) ? $columnName : $this->columnName;
       
	   foreach( $cids as $key => $val ) {
       
	   	    $updatedValue = Joomla\CMS\Factory::getApplication()->getInput()->get( 'replace'.$key, false, 'POST', 'none', JREQUEST_ALLOWRAW );
          
	   	    //$coulmnName = Joomla\CMS\Factory::getApplication()->getInput()->get( 'column_name'.$key, false, 'POST', 'none', JREQUEST_ALLOWRAW );
            $coulmnName = $columns[$key];
            $currentRecord = $this->getTableRecord($this->tableName,$val);
	   	    //if ( $updatedValue ){
	   	  	$updateQuery = $this->createUpdateQuery( $this->tableName, $coulmnName, $updatedValue, $val );
	   	  	$this->_db->setQuery($updateQuery);
            $result = $this->_db->execute();
            $this->affectedColumns[] = $coulmnName;
            $this->affectedLocationIDs[] = $val;
            $this->affectedLanguages[]   = $this->currentLang;
            
            $updateHistoryArray = array('from' => $currentRecord->{$coulmnName}, 
                                        'to'   => $updatedValue,
                                        'search_history_id' => $this->uniqid,
                                        'table_name' => $this->tableName,
                                        'column_name' => $coulmnName,
                                        'datapage_id' => $val,
                                        'language' => $this->currentLang,
                                        'location_name' => $currentRecord->location,
                                        'created_at' => date('Y-m-d H:i:s')
                                  );
            ### insert row level update history
	   	  	$this->frtInsertDPUpdateHistory($updateHistoryArray); 
            
            if( 'en' == $this->currentLang ) {
                
                ##Update ES DP
                if($dontReplaceES === false) {
                    $updatedValueES = Joomla\CMS\Factory::getApplication()->getInput()->get( 'replace_es'.$key, false, 'POST', 'none', JREQUEST_ALLOWRAW );
                    //if( !empty($updatedValueES) ) {
                        $currentRecord = $this->getTableRecord($tableNameES,$val);
                        $updateQueryES  = $this->createUpdateQuery( $tableNameES, $coulmnName, $updatedValueES, $val );
                        $this->_db->setQuery($updateQueryES);
                        $resultES = $this->_db->execute();
                        $this->affectedLanguages[]   = 'es';
                        
                        $updateHistoryArray = array('from'              => $currentRecord->{$coulmnName}, 
                                                    'to'                => $updatedValueES,
                                                    'search_history_id' => $this->uniqid,
                                                    'table_name'        => $tableNameES,
                                                    'column_name'       => $coulmnName,
                                                    'datapage_id'       => $val,
                                                    'language'          => 'es',
                                                    'location_name'     => $currentRecord->location,
                                                    'created_at'        => date('Y-m-d H:i:s')
                                              );
                        ### insert row level update history
                        $this->frtInsertDPUpdateHistory($updateHistoryArray); 
                    //}
                }
	   	  	
                ##Update FR DP
                if($dontReplaceFR === false) {
                    $updatedValueFR = Joomla\CMS\Factory::getApplication()->getInput()->get( 'replace_fr'.$key, false, 'POST', 'none', JREQUEST_ALLOWRAW );
                    //if( !empty($updatedValueFR) ) {
                        $currentRecord = $this->getTableRecord($tableNameFR,$val);
                        $updateQueryFR  = $this->createUpdateQuery( $tableNameFR, $coulmnName, $updatedValueFR, $val );
                        $this->_db->setQuery($updateQueryFR);
                        $resultFR = $this->_db->execute();
                        $this->affectedLanguages[]   = 'fr';
                        
                        $updateHistoryArray = array('from'              => $currentRecord->{$coulmnName}, 
                                                    'to'                => $updatedValueFR,
                                                    'search_history_id' => $this->uniqid,
                                                    'table_name'        => $tableNameFR,
                                                    'column_name'       => $coulmnName,
                                                    'datapage_id'       => $val,
                                                    'language'          => 'fr',
                                                    'location_name'     => $currentRecord->location,
                                                    'created_at'        => date('Y-m-d H:i:s')
                                              );
                        ### insert row level update history
                        $this->frtInsertDPUpdateHistory($updateHistoryArray); 
                   // }
                }
                
            }
            $total++;
	   	  //}
	   }
       
	   return $total;
	}
    
	
	/*
	 * update the rows with the posted items
	 */
	function frtUpdateRows( $p_search_options ) {
	   $cids = Joomla\CMS\Factory::getApplication()->getInput()->get('cid', false);
	   $total = 0;
	   
       //@TODO: need to check table and column name selection       
	   $this->tableName = empty( $this->tableName ) ? $p_search_options['table_name'] : $this->tableName;	
	   $columnName = ( stripos($p_search_options['table_name'], 'preamble') !==false ) ? $p_search_options['column_name'].'_p' 
			                                                                           : $p_search_options['column_name'];
       $this->columnName = empty( $this->columnName ) ? $columnName : $this->columnName;
       
	   foreach( $cids as $key => $val ) {
	   	    $updatedValue = Joomla\CMS\Factory::getApplication()->getInput()->get( 'replace'.$val, false, 'POST', 'none', JREQUEST_ALLOWRAW );
	   	  //if ( $updatedValue ){
	   	  	$updateQuery = $this->createUpdateQuery( $this->tableName, $this->columnName, $updatedValue, $val );
	   	  	$this->_db->setQuery($updateQuery);
	   	  	$result = $this->_db->execute();
	   	  	$total++;
	   	  //}
	   }
	   return $total;
	}
	
	
	/*
	 * makes the update-sql query 
	 */
	function createUpdateQuery( $p_t_name, $p_c_name, $p_val, $p_id ) {
		
		$sql = "UPDATE 
		               `$p_t_name` 
		        SET 
		               `$p_t_name`.`$p_c_name` = " . $this->_db->Quote($p_val) . ', ' . 
                       "`updated_at` = '" . date('Y-m-d H:i:s') . "' " .  
		        " WHERE 
		               `$p_t_name`.`id` = " . $this->_db->Quote($p_id);
        
		return $sql;
	}
	

    
	function frtInsertSearchHistory( $p_insert_array, $p_affected_rows ) {
		
		$options = ($p_insert_array['case_sensitive']) ? 'case_sensitive' : 'case_insensitive';
		if( !empty($p_insert_array['regular_expression']) ) {
			$options .= ' regular_expression';
		}
		$insert_array = array( 'from'               => $p_insert_array['from']?$p_insert_array['from']:'Manual Correction',
		                       'to'                 => $p_insert_array['to']?$p_insert_array['to']:'Manual Correction',
		  					   'table_name'         => $p_insert_array['table_name'],
		 					   'column_name'        => $p_insert_array['column_name'] ? $p_insert_array['column_name'] : 'All Categories',
		                       'options'            => $options,
		                       'total_updated_rows' => $p_affected_rows,
                               'language'           => implode( ', ', array_unique($this->affectedLanguages) ),
                               'affected_columns'   => implode( ', ', array_unique($this->affectedColumns) ),
                               'affected_locations' => implode(', ', $this->affectedLocationIDs),
                               'username'           => $this->currentUser->username,
		                       'created_at'         => $p_insert_array['created_at'],
		 					   'updated_at'         => $p_insert_array['updated_at']
		                );
		if( empty($p_insert_array) || empty($insert_array) ) {
		    return false;
		}
		$insert_array = (object)$insert_array;
		$ret = $this->_db->insertObject($this->DPFRTSearchHistory, $insert_array);
        
                ### Now update the uniqid with the real id
                $insertedId = $this->_db->insertid();
                $this->updateSearchHistoryId($insertedId);
        
		return true;
	}
    
    
    	/*
	 * makes the update-sql query 
	 */
	function updateSearchHistoryId( $insertedId ) {
		
		$sql = "UPDATE 
		               `$this->DPFRTUpdateHistory` 
		        SET 
		               `search_history_id` = " . $this->_db->Quote($insertedId) . ' ' . 
		        " WHERE 
		               `search_history_id` = " . $this->_db->Quote($this->uniqid);
        
        $this->_db->setQuery($sql);
	   	$result = $this->_db->execute();
        return $result;
	}

    
    function frtInsertDPUpdateHistory( $p_insert_array ) {
		
		$insert_array = array( 'from'               => $p_insert_array['from'],
		                       'to'                 => $p_insert_array['to'],
		  					   'table_name'         => $p_insert_array['table_name'],
		 					   'column_name'        => $p_insert_array['column_name'],
		                       'search_history_id'  => $p_insert_array['search_history_id'],
                               'language'           => $p_insert_array['language'],
                               'datapage_id'        => $p_insert_array['datapage_id'],
                               'location_name'      => $p_insert_array['location_name'],
		                       'created_at'         => $p_insert_array['created_at']
		                );
        
		if( empty($p_insert_array) || empty($insert_array) ) {
			return false;
		}
        
		$insert_array = (object)$insert_array;
		$ret = $this->_db->insertObject($this->DPFRTUpdateHistory, $insert_array);
		return true;
	}
	
	
    /*
	 * get first 100 rows of the frt history table   
	 */
	function frtGetHistory() {
		
		$sql = "SELECT 
		     		   *
		        FROM 		   
		               $this->DPFRTSearchHistory 
                WHERE 
                       `language` LIKE " . $this->_db->Quote('%'.$this->currentLang.'%') . " " ;
        
        if( 'en' == $this->currentLang ) {
            $sql .= "OR `language` = ''  "; 
        }
                
		$sql .= "ORDER BY 
		               `id` DESC       
		        LIMIT 1000";
		        
		$this->_db->setQuery( $sql );
		$data = $this->_db->loadObjectList( );
		return $data;
	}
    

    /*
	 * get all the rows of the frt update history table   
	 */
	function frtGetRawUpdateHistory($id) {
        
        if(empty($id)) {
            return false;
        }
		
		$sql = "SELECT 
		     		   *
		        FROM 		   
		               $this->DPFRTUpdateHistory
                WHERE 
                       `search_history_id` = " . $this->_db->Quote($id) .  
		        "ORDER BY 
		               `id` ASC       
		        ";
		        
		$this->_db->setQuery( $sql );
        
		$data = $this->_db->loadObjectList( );
		return $data;
	}
    
    
     /*
      * 
	  * get record by id 
      * 
	  */
	function getTableRecord($tableName, $tableId) 
    {
        if( empty($tableName) || empty($tableId) ) {
            return false;        
        }
        
		$sql = "SELECT 
		     		   *
		        FROM 		   
		               $tableName
                WHERE 
                       `id` = " . $this->_db->Quote($tableId) .  
		        "LIMIT 1";
		        
		$this->_db->setQuery( $sql );
		$data = $this->_db->loadObject();
		return $data;
	}
    
    
    function frtUSJurisdictionsPerformSearch($us_jurisdictions_id) {
        $sql = "
					SELECT `t`.`id`, `t`.`location_id`, `t`.`location`, `t`.`$this->columnName` as content,
					        `l`.`name`, `l`.`name_es`, `l`.`name_fr`
					FROM `$this->tableName` as `t`
					LEFT JOIN `#__gpo_location` as `l`
					ON `l`.`id` = `t`.`location_id`
					WHERE `t`.`location_id` IN (" . $us_jurisdictions_id . ") AND `t`.`$this->columnName` LIKE ";

        $sql   .=   ($this->isCaseSensitive) ? ' BINARY ' : ' ' ;

        $sql   .=   $this->_db->quote( "%".$this->from ."%" )
            . " AND `t`.location_id != 0
					ORDER BY `t`.`id` ASC";
        $this->_db->setQuery( $sql );

        $data = $this->_db->loadObjectList();
        foreach($data as $k=>$v){
            $data[$k]->column = $this->columnName;
            $data[$k] = $v;
        }
        $this->total = count( $data );

        return $data;
    }
	
}
?>