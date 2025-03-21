<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

class GpoModelUpdateDPFromSourceFile extends JModelList {

    private $currentLang;
    private $tableSeparator;
    private $DPTable            = '#__gpo_datapages';
    private $DPPreambleTable    = '#__gpo_datapage_preamble_values';
    private $DPMasterListTable  = '#__gpo_preambles_switches_master_list';
    private $DPHierarchyTable   = '#__gpo_datapage_hierarchy';
    private $DPFRTSearchHistory = '#__gpo_dp_import_history';
    private $DPFRTUpdateHistory = '#__gpo_dp_importdata_update_history';
    public  $metaColumns        = array();
    public  $findTerm           = NULL;

    ## For FRT History Flags
    private $affectedColumns = array();
    private $affectedLocationIDs = array();
    private $affectedLanguages = array();
    private $currentUser = NULL;
    public $tableName;
    public $columnName;
    public $selectedColumn;

    public $total = 0;
    public $from = "";
    public $to;
    public $isCaseSensitive;
    public $isRegex;
    public $uniqid;

    function __construct($p_options) {
        parent::__construct();
	        $this->limit = Joomla\CMS\Factory::getApplication()->getInput()->get('limit', '10', '', 'int');
	        $this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', '', 'int');
        
        if ($p_options) {
            //$this->from = $p_options['from'];
            $this->findTerm = $this->from;
            $this->findTerm = $this->_db->escape($this->findTerm, true);
            $this->selectedColumn = $p_options['selectedColumn'];

            ## 
            #  mysql_real_escape_string() does not escape % and _
            #  These are wildcards in MySQL
            #  so you need to escape %,_ through your own mechanism
            ## 
            ### Escape '%'
            if (strpos($this->from, '%') !== false) {
                $this->findTerm = addcslashes($this->from, '%');
            }
            ## Escape '_'
            if (strpos($this->from, '_') !== false) {
                $this->findTerm = addcslashes($this->from, '_');
            }

            //$this->to = $p_options['to'];
           // $this->isCaseSensitive = ($p_options['case_sensitive']) ? TRUE : FALSE;
            //$this->isRegex = ($p_options['regular_expression']) ? TRUE : FALSE;
        }

        $this->currentLang = strtolower($p_options['currentLang']);
        $this->tableSeparator = '_';
        $this->metaColumns = array('id', 'location_id', 'location', 'created_at', 'updated_at', 'published_at');
        $this->affectedLanguages = array($this->currentLang);
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
        if (!empty($p_options)) {

            if (stripos($p_options['table_name'], 'preamble') !== false) {
                $this->tableName = $this->DPPreambleTable;
                $this->columnName = trim($p_options['column_name']) . '_p';
            } else {
                $this->tableName = $this->DPTable;
                $this->columnName = trim($p_options['column_name']);
            }
        }
    }

    function _makeTableName($inputName) {
        if (empty($inputName)) {
            return $inputName;
        }

        #For English we won't add language prefix as it is the default
        if ('en' == $this->currentLang || empty($this->currentLang)) {
            $tableName = trim($inputName);
        } else {
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

    function frtSearchReplace($p_search_result, $p_options = array()) {
        $from = $p_options['from'];
        $to = $p_options['to'];
        $isCaseSensitive = ( $p_options['case_sensitive'] ) ? TRUE : FALSE;
        $replacedArray = array();

        foreach ($p_search_result as $key => $row) {
            $replacedContent = ($isCaseSensitive) ? str_replace($from, $to, $row->content) : str_ireplace($from, $to, $row->content);

            $replacedArray[$key] = clone $row;
            $replacedArray[$key]->content = $replacedContent;

            if ('en' == $this->currentLang) {
                $replacedContentES = ($isCaseSensitive) ? str_replace($from, $to, $row->content_es) : str_ireplace($from, $to, $row->content_es);

                $replacedContentFR = ($isCaseSensitive) ? str_replace($from, $to, $row->content_fr) : str_ireplace($from, $to, $row->content_fr);
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

    function frtSearchCorrectionManualReplace($p_search_result, $p_options = array()) {
        $replacedArray = array();

        foreach ($p_search_result as $key => $row) {
            //$replacedArray["$row->id"] =  $row->content ;
            $replacedArray[$key] = clone $row;
            $replacedArray[$key]->content = $row->content;

            if ('en' == $this->currentLang) {
                $replacedArray[$key]->content_es = $row->content_es;
                $replacedArray[$key]->content_fr = $row->content_fr;
            }
        }

        return $replacedArray;
    }

    function isMetaColumn($columnName) {

        if (empty($columnName)) {
            return true;
        }

        if (in_array($columnName, $this->metaColumns)) {
            return true;
        }

        return false;
    }


    /*
     * update the rows with the posted items of datapage items
     */

    function frtUpdateRowsDatapage($p_options) {
        $cids          = Joomla\CMS\Factory::getApplication()->getInput()->get('cid', false);
        $locationIDs   = Joomla\CMS\Factory::getApplication()->getInput()->get('location_id', false);
        $columnAliases = Joomla\CMS\Factory::getApplication()->getInput()->get('category_alias', false);
        $importType    = Joomla\CMS\Factory::getApplication()->getInput()->get('importType', false);
        
        $columnName  = $this->selectedColumn;
        $selectedLocationID = Joomla\CMS\Factory::getApplication()->getInput()->get('selectedLocationId', false);        
        $selectedColumnName = $this->selectedColumn;

        ##Es/Fr replace options when you're in English version
        $replaceES = Joomla\CMS\Factory::getApplication()->getInput()->get('replace_es', false);
        $replaceFR = Joomla\CMS\Factory::getApplication()->getInput()->get('replace_fr', false);
        $total = 0;
        $updatedLocationIDs = array();
        $updatedCategories  = array();
        
        if( 'by_category' == $importType ) {
            $currentRecords = $this->getDPDataByCategory($columnName); //load data for all locations
        } else {
            $currentRecords = array(); //initially empty
        }
        ##
        ## For English, we need to update Fr/Es versions too
        ##
        if ('en' == $this->currentLang) {
            $tableNameES = $this->tableName . '_' . 'es';
            $tableNameFR = $this->tableName . '_' . 'fr';
        }
        $this->tableName = empty($this->tableName) ? $p_options['table_name'] : $this->tableName;
        //$columnName = ( stripos($p_search_options['table_name'], 'preamble') !==false ) ? $p_search_options['column_name'].'_p' : $p_search_options['column_name'];
        //$this->columnName = empty( $this->columnName ) ? $columnName : $this->columnName;

        foreach ($cids as $key => $val) {
            $updatedValue  = Joomla\CMS\Factory::getApplication()->getInput()->get('replace'  . $key, false, 'POST', 'none', JREQUEST_ALLOWRAW);
            //$existingValue = Joomla\CMS\Factory::getApplication()->getInput()->get('existing' . $key, false, 'POST', 'none', JREQUEST_ALLOWRAW);
            
            $locationID   = ('by_category' == $importType) ? $locationIDs[$key]  : $selectedLocationID;
            
            $columnName   = ('by_category' == $importType) ? $selectedColumnName : $columnAliases[$key];
            if( 'by_location' == $importType ) {
                $currentRecords = $this->getDPDataByCategory($columnName, $locationID); //load data for all locations
            }
            
            $updateQuery = $this->createUpdateQuery($this->tableName, $columnName, $updatedValue, $locationID);
            $this->_db->setQuery($updateQuery);
            $result = $this->_db->execute();
            
            $this->affectedColumns[] = $columnName;
            $this->affectedLocationIDs[] = $val;
            $this->affectedLanguages[] = $this->currentLang;

            $updateHistoryArray = array('from' => $currentRecords[$locationID]['columnValueEn'],
                'to' => $updatedValue,
                'search_history_id' => $this->uniqid,
                'table_name' => $this->tableName,
                'column_name' => $columnName,
                'datapage_id' => $val,
                'language' => $this->currentLang,
                'location_name' => $locationID,
                'created_at' => date('Y-m-d H:i:s')
            );
            ### insert row level update history
            $this->frtInsertDPUpdateHistory($updateHistoryArray);

            if ('en' == $this->currentLang) {

                ##Update ES DP
                if ($replaceES == 1) {
                    //$updatedValueES = Joomla\CMS\Factory::getApplication()->getInput()->get('replace_es' . $key, false, 'POST', 'none', JREQUEST_ALLOWRAW);
                    //$currentRecord = $this->getTableRecord($tableNameES, $val);
                    $updateQueryES = $this->createUpdateQuery($tableNameES, $columnName, $updatedValue, $locationID);
                    $this->_db->setQuery($updateQueryES);
                    $resultES = $this->_db->execute();
                    $this->affectedLanguages[] = 'es';

                    $updateHistoryArray = array('from' => $currentRecords[$locationID]['columnValueEs'],
                        'to' => $updatedValue,
                        'search_history_id' => $this->uniqid,
                        'table_name' => $tableNameES,
                        'column_name' => $columnName,
                        'datapage_id' => $val,
                        'language' => 'es',
                        'location_name' => $locationID,
                        'created_at' => date('Y-m-d H:i:s')
                    );
                    ### insert row level update history
                    $this->frtInsertDPUpdateHistory($updateHistoryArray);
                }

                ##Update FR DP
                if ($replaceFR == 1) {
                    $updateQueryFR = $this->createUpdateQuery($tableNameFR, $columnName, $updatedValue, $locationID);
                    $this->_db->setQuery($updateQueryFR);
                    $resultFR = $this->_db->execute();
                    $this->affectedLanguages[] = 'fr';

                    $updateHistoryArray = array('from' => $currentRecords[$locationID]['columnValueFr'],
                        'to' => $updatedValue,
                        'search_history_id' => $this->uniqid,
                        'table_name' => $tableNameFR,
                        'column_name' => $columnName,
                        'datapage_id' => $val,
                        'language' => 'fr',
                        'location_name' => $locationID, //$currentRecord->location,
                        'created_at' => date('Y-m-d H:i:s')
                    );
                    ### insert row level update history
                    $this->frtInsertDPUpdateHistory($updateHistoryArray);
                }
            }
            $total++;
            $updatedLocationIDs[] = $locationID;
            $updatedCategories[]  = $columnName;
            
        }

        return array('total'              => $total, 
                     'updatedLocationIDs' => $updatedLocationIDs, 
                     'updatedCategories'  => $updatedCategories,
                     'affectedLanguages'  => $this->affectedLanguages,
                     'importType'         => $importType
               );
        
    }

    /*
     * update the rows with the posted items
     * 
     */

    function frtUpdateRows($p_search_options) {
        $cids = Joomla\CMS\Factory::getApplication()->getInput()->get('cid', false);
        $total = 0;

        //@TODO: need to check table and column name selection       
        $this->tableName = empty($this->tableName) ? $p_search_options['table_name'] : $this->tableName;
        $columnName = ( stripos($p_search_options['table_name'], 'preamble') !== false ) ? $p_search_options['column_name'] . '_p' : $p_search_options['column_name'];
        $this->columnName = empty($this->columnName) ? $columnName : $this->columnName;

        foreach ($cids as $key => $val) {
            $updatedValue = Joomla\CMS\Factory::getApplication()->getInput()->get('replace' . $val, false, 'POST', 'none', JREQUEST_ALLOWRAW);
            //if ( $updatedValue ){
            $updateQuery = $this->createUpdateQuery($this->tableName, $this->columnName, $updatedValue, $val);
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

    function createUpdateQuery($p_t_name, $p_c_name, $p_val, $p_location_id) {
        
        ## Update column for a specific location, if not exists return empty 
        if( empty($p_location_id) ) {
            return '';
        }

        $sql = "UPDATE 
                      `$p_t_name` 
		SET 
		      `$p_t_name`.`$p_c_name` = " . $this->_db->Quote($p_val) . ', ' .
                      "`updated_at` = '" . date('Y-m-d H:i:s') . "' " . 
                "WHERE 
		      `$p_t_name`.`location_id` = " . $this->_db->Quote($p_location_id);

        return $sql;
    }

    function insertSearchHistory($p_insert_array, $p_affected_rows) {

        if( !empty($p_insert_array['column_name']) ) {
            unset($p_insert_array['selectedColumn']);
        }
        
        if( !empty($p_insert_array['importOnlyBlankYears']) ) {
            $p_insert_array['importOnlyBlankYears'] = 'Import Only Blank Years';
        }
        
        $insert_array = array( 'import_type'        => $p_insert_array['importType'] ? $p_insert_array['importType'] : '',
                               'import_blank_years' => isset($p_insert_array['importOnlyBlankYears']) ? $p_insert_array['importOnlyBlankYears'] : '',
                               'file_name'          => $p_insert_array['sourceFile'] ? $p_insert_array['sourceFile'] : '',
                               'column_name'        => $p_insert_array['column_name'] ? $p_insert_array['column_name'] : '',
                               'options'            => implode(', ', $p_insert_array),
                               'total_updated_rows' => $p_affected_rows,
                               'language'           => implode(', ', array_unique($this->affectedLanguages)),
                               'affected_columns'   => $p_insert_array['affected_columns']   ? $p_insert_array['affected_columns']   : implode(', ', array_unique($this->affectedColumns)),
                               'affected_locations' => $p_insert_array['affected_locations'] ? $p_insert_array['affected_locations'] : implode(', ', $this->affectedLocationIDs),
                               'username'           => $this->currentUser->username,
                               'created_at'         => date('Y-m-d H:i:s'),
                               'updated_at'         => date('Y-m-d H:i:s')
                        );
        if (empty($p_insert_array) || empty($insert_array)) {
            return false;
        }
        $insert_array = (object) $insert_array;
        $ret = $this->_db->insertObject($this->DPFRTSearchHistory, $insert_array);

        ### Now update the uniqid with the real id
        $insertedId = $this->_db->insertid();
        $this->updateSearchHistoryId($insertedId);

        return true;
    }

    /*
     * makes the update-sql query 
     */

    function updateSearchHistoryId($insertedId) {

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

    function frtInsertDPUpdateHistory($p_insert_array) {

        $insert_array = array('from' => $p_insert_array['from'],
                              'to' => $p_insert_array['to'],
                              'table_name' => $p_insert_array['table_name'],
                              'column_name' => $p_insert_array['column_name'],
                              'search_history_id' => $p_insert_array['search_history_id'],
                              'language' => $p_insert_array['language'],
                              'datapage_id' => $p_insert_array['datapage_id'],
                              'location_name' => $p_insert_array['location_name'],
                              'created_at' => $p_insert_array['created_at']
                        );

        if (empty($p_insert_array) || empty($insert_array)) {
            return false;
        }

        $insert_array = (object) $insert_array;
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
                       `language` LIKE " . $this->_db->Quote('%' . $this->currentLang . '%') . " ";

        if ('en' == $this->currentLang) {
            $sql .= "OR `language` = ''  ";
        }

        $sql .= "ORDER BY 
		               `id` DESC";


        $this->total = $this->totalDPFRTSearchHistory();

        
        if( (int)$this->total === (int)'0')
        {
            return array();
        }            
        
        $this->pagination = new JPagination($this->total, $this->limitstart, $this->limit );

        $this->_db->setQuery( $sql, $this->pagination->limitstart, $this->pagination->limit );
        $data = $this->_db->loadObjectList();
        return $data;
    }

    /*
     * get all the rows of the frt update history table   
     */

    function frtGetRawUpdateHistory($id) {

        if (empty($id)) {
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

        $this->_db->setQuery($sql);

        $data = $this->_db->loadObjectList();
        return $data;
    }

    /*
     * 
     * get record by id 
     * 
     */

    function getTableRecord($tableName, $tableId) {
        if (empty($tableName) || empty($tableId)) {
            return false;
        }

        $sql = "SELECT 
		     		   *
		        FROM 		   
		               $tableName
                WHERE 
                       `id` = " . $this->_db->Quote($tableId) .
                "LIMIT 1";

        $this->_db->setQuery($sql);
        $data = $this->_db->loadObject();
        return $data;
    }

     /*
     * get All DP Info with the 
     * corresponding location info.
     * 
     */

    function getDPDataByCategory($categoryName, $locationID=NULL) {
        $query = "SELECT 
		               `DPEn`.`location_id` as `locID`,
		               `DPEn`.`created_at`,
		               `DPEn`.`updated_at`,
		               `DPEn`.{$categoryName} as columnValueEn,
                               `DPFr`.{$categoryName} as columnValueFr,
                               `DPEs`.{$categoryName} as columnValueEs
		  FROM " . 
                  " `$this->DPTable` as `DPEn`                 
                  LEFT JOIN 
                            `{$this->DPTable}_fr` as `DPFr` 
		          ON 
		              DPFr.`location_id` = `DPEn`.`location_id` 
                  LEFT JOIN 
                            `{$this->DPTable}_es` as DPEs 
		          ON 
		              DPEs.`location_id` = `DPEn`.`location_id` ";
        if( !empty($locationID) ) {
            $query .= "WHERE " . 
                      " `DPEn`.`location_id` = $locationID ";
        }
        
        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList('locID');

        if (empty($data)) {
            return false;
        }
        
        return $data;
    }
    
     /*
     * get All DP Info with the 
     * corresponding location info.
     * 
     */

    function getDPDataByLocation($categoryName, $locationID=NULL) {
        $query = "SELECT 
		               `DPEn`.`location_id` as `locID`,
		               `DPEn`.`created_at`,
		               `DPEn`.`updated_at`,
		               `DPEn`.{$categoryName} as columnValueEn,
                               `DPFr`.{$categoryName} as columnValueFr,
                               `DPEs`.{$categoryName} as columnValueEs
		  FROM " . 
                  " `$this->DPTable` as `DPEn`  
                  LEFT JOIN 
                            `{$this->DPTable}_fr` as `DPFr` 
		          ON 
		              DPFr.`location_id` = `DPEn`.`location_id` 
                  LEFT JOIN 
                            `{$this->DPTable}_es` as DPEs 
		          ON 
		              DPEs.`location_id` = `DPEn`.`location_id` ";
        if( !empty($locationID) ) {
            $query .= "WHERE " . 
                      " `DPEn`.`locID` = $locationID ";
        }
        
        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList('locID');

        if (empty($data)) {
            return false;
        }
        
        return $data;
    }

    /*
    * Total DPFRT Search History for pagination
    */


    function totalDPFRTSearchHistory()
    {            
        $query = "SELECT COUNT( `id` ) FROM " . $this->DPFRTSearchHistory . " "  . $where . " LIMIT 0,1";
        $this->_db->setQuery( $query );
        return $this->_db->loadResult();
    }


}
?>