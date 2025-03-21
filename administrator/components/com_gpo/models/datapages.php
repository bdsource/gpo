<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

class GpoModelDatapages extends JModelList {

    public $total;
    public $data;

    ##DP Tables##
    private $currentLang;
    private $tableSeparator;
    private $DPTable = '#__gpo_datapages';
    private $DPPreambleTable = '#__gpo_datapage_preamble_values';
    private $DPMasterListTable = '#__gpo_preambles_switches_master_list';
    private $DPHierarchyTable = '#__gpo_datapage_hierarchy';

    function __construct($currentLang = 'en') {
      $jinput = JFactory::getApplication()->input;        

        parent::__construct();
        $this->limit = (int) '10';
        $this->limitstart = $jinput->get('limitstart', '0', '', 'int');
        $this->currentLang = $jinput->get('lang', 'en', '', 'STRING');
        //$session = JFactory::getSession(); 
        //$this->currentLang = $session->get('lang');
        $this->currentLang = strtolower($this->currentLang);
      //echo '----------'.$this->currentLang;die();

        $this->tableSeparator = '_';
        $this->_initializeTableNames();
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

    function _initializeTableNames() {

        /*
         * initialize DP table names according to lang switch
         * 
         */

        $this->DPTable = $this->_makeTableName($this->DPTable);
        $this->DPPreambleTable = $this->_makeTableName($this->DPPreambleTable);
    }

    /*
     *  Get the location of a news item ( could work for quotes and citations to )
     * 	return an array of all locations - any manipulation should be done here
     */

    function getLocations($id) {
        $ids = array(
            'regions' => array(),
            'sub_regions' => array(),
            'countries' => array(),
        );
        $query = "SELECT * FROM `#__gpo_qa_locations_unpublished` WHERE `article_id`= " . $this->_db->quote($id);
        $this->_db->setQuery($query);
        $locations = $this->_db->loadAssocList();

        if (count($locations) == 0) {
            return false;
        }
        foreach ($locations as $location) {
            switch ($location['type']) {
                case 'country':
                    $ids['countries'][] = $location['ext_id'];
                    break;
                case 'subregion':
                    $ids['sub_regions'][] = $location['ext_id'];
                    break;
                case 'region':
                    $ids['regions'][] = $location['ext_id'];
                    break;
            }
        }
        if (count($ids['regions']) > 0) {
            $query = "SELECT `id`,`name` FROM `#__gpo_regions` WHERE `id` IN( " . implode(",", $ids['regions']) . ") ";
            $this->_db->setQuery($query);
            $regions = $this->_db->loadAssocList('id');
        }
        if (count($ids['sub_regions']) > 0) {
            $query = "SELECT `id`,`name` FROM `#__gpo_sub_regions` WHERE `id` IN( " . implode(",", $ids['sub_regions']) . ") ";
            $this->_db->setQuery($query);
            $sub_regions = $this->_db->loadAssocList('id');
        }
        if (count($ids['countries']) > 0) {
            $query = "SELECT `id`,`name` FROM `#__gpo_countries` WHERE `id` IN( " . implode(",", $ids['countries']) . ") ";
            $this->_db->setQuery($query);
            $countries = $this->_db->loadAssocList('id');
        }

        $data = array();
        foreach ($locations as $location) {
            switch ($location['type']) {
                case 'country':
                    if (isset($countries[$location['ext_id']])) {
                        $key = 'c' . $location['ext_id'];
                        $value = $countries[$location['ext_id']]['name'];
                        $data[$key] = $value;
                    }
                    break;
                case 'subregion':
                    if (isset($sub_regions[$location['ext_id']])) {
                        $key = 'sr' . $location['ext_id'];
                        $value = $sub_regions[$location['ext_id']]['name'];
                        $data[$key] = $value;
                    }
                    break;
                case 'region':
                    if (isset($regions[$location['ext_id']])) {
                        $key = 'r' . $location['ext_id'];
                        $value = $regions[$location['ext_id']]['name'];
                        $data[$key] = $value;
                    }
                    break;
            }
        }
        return $data;
    }

    /*
     * get all location data 
     * 
     */

    function getAllLocationData($orderBy = NULL) {
        $query = "SELECT `id`,`name`,`type`,`display` FROM `#__gpo_location` where `type`!='state_province'";
        if (!empty($orderBy)) {
            $query .= " ORDER BY `$orderBy`";
        }
        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();
        return $data;
    }

    /*
     * get All DP Info with the 
     * corresponding location info.
     * 
     */

    function getAllDPWithLocations($filter_order = 'name', $filter_order_Dir = 'asc', $location_filter = '') {

        $query = "SELECT  
		               `#__gpo_location`.`id`,
		               `#__gpo_location`.`name`,
                               `#__gpo_location`.`name` as `name_en`,
                               `#__gpo_location`.`name_es`,
                               `#__gpo_location`.`name_fr`,
		               `#__gpo_location`.`type`,
		               `#__gpo_location`.`display`,
		               `$this->DPTable`.`id` as `dp_id`,
		               `$this->DPTable`.`location_id`,
		               `$this->DPTable`.`created_at`,
		               `$this->DPTable`.`updated_at`,
		               `$this->DPTable`.`published_at`,
		               `$this->DPTable`.`published`
		          FROM 
                  " .
                $this->DPTable
                .
                " RIGHT JOIN 
		              `#__gpo_location`
		          ON 
		              $this->DPTable.`location_id` = `#__gpo_location`.`id` "
                . "WHERE "
                . "   `#__gpo_location`.`type` != 'state_province'";
        if ($location_filter) {
            $query .= " AND `#__gpo_location`.`name` LIKE '%$location_filter%' ";
        }
        if ($filter_order) {
            $query .= " ORDER BY {$this->_db->quoteName($filter_order)} $filter_order_Dir";
        }

        $this->_db->setQuery($query);
        $data = $this->_db->loadObjectList();

        if (empty($data)) {
            return false;
        }
        return $data;
    }

    function getLocationById($p_location_id) {
        if (empty($p_location_id)) {
            return false;
        }
        $query = "SELECT `id`,`name`,`type`,`display` FROM `#__gpo_location`";
        $query .= " WHERE id='" . $p_location_id . "'";

        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        return $data;
    }
    
    function getLocationByName($p_location_name) {
        if (empty($p_location_name)) {
            return false;
        }
        $query = "SELECT `id`,`name`,`type`,`display` FROM `#__gpo_location`";
        $query .= " WHERE `name`=" . $this->_db->quote($p_location_name);

        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        return $data;
    }

    function getRegions() {
        $query = "SELECT `name` FROM `#__gpo_location` WHERE `type`='region' ORDER BY `name` ASC";
        $this->_db->setQuery($query);
        $data = $this->_db->loadColumn();
        return $data;
    }

    function getSubRegions() {
        $query = "SELECT `name` FROM `#__gpo_location` WHERE `type`='subregion' ORDER BY `name` ASC";
        $this->_db->setQuery($query);
        $data = $this->_db->loadColumn();
        return $data;
    }

    function getCountries() {
        $query = "SELECT `id`, `name` FROM `#__gpo_location` WHERE `type`='country' AND `display`=1 ORDER BY `name` ASC";
        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();
        return $data;
    }

    function getDpDataByLocationId($p_country_id) {

//echo 'DPTable: '.$this->DPTable;die(); 
        if (!isset($p_country_id) && empty($p_country_id)) {
            return false;
        }

        $query = "SELECT 
		        * 
		  FROM "
                  .
                        $this->_db->quoteName($this->DPTable)
                  .
                  "WHERE 
		        `location_id`= " . $this->_db->quote($p_country_id) .
                  " LIMIT 0,1";

        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();

        if (empty($data->id)) {
            return false;
        }
        return $data;
    }

    function getDpDataByLocationAndLang($p_country_id, $language) {
        if (!isset($p_country_id) && empty($p_country_id)) {
            return false;
        }

        if (empty($language)) {
            return false;
        }

        if ('en' == $language) {
            $DPTableName = '#__gpo_datapages';
        } else if ('es' == $language) {
            $DPTableName = '#__gpo_datapages_es';
        } else if ('fr' == $language) {
            $DPTableName = '#__gpo_datapages_fr';
        }

        $query = "SELECT 
		        * 
		  FROM " .
                        $this->_db->quoteName($DPTableName) .
                " WHERE 
		        `location_id`= " . $this->_db->quote($p_country_id) .
                " LIMIT 0,1";

        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();

        if (empty($data->id)) {
            return false;
        }
        return $data;
    }

    function getDPDataByCategoryAndLang($p_category_name, $language) {
        if ( !isset($p_category_name) && empty($p_category_name) ) {
             return false;
        }

        if (empty($language)) {
            return false;
        }

        if ('en' == $language) {
            $DPTableName = '#__gpo_datapages';
        } else if ('es' == $language) {
            $DPTableName = '#__gpo_datapages_es';
        } else if ('fr' == $language) {
            $DPTableName = '#__gpo_datapages_fr';
        }

        $query = "SELECT 
		        id, location_id, location, `$p_category_name` as `columnValue`
		  FROM " . 
                        $this->_db->quoteName($DPTableName) . 
                  "";

        $this->_db->setQuery($query);
        $data = $this->_db->loadObjectList();

        if ( empty($data) ) {
            return false;
        }
        
        return $data;
    }
    
     
    function getDPByLocationId($p_location_id) {
        if (!isset($p_location_id) && empty($p_location_id)) {
            return false;
        }

        $query = "SELECT 
		                * 
		          FROM 
		              $this->DPTable
		          LEFT JOIN 
		              $this->DPPreambleTable
		          ON 
		              $this->DPTable.`location_id` = $this->DPPreambleTable.`location_id`    
		          WHERE 
		              $this->DPTable.`location_id`= " . $this->_db->quote($p_location_id);

        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();

        if (empty($data->id)) {
            return false;
        }
        return $data;
    }

    function getDpPreamblesByLocationId($p_country_id) {
        if (!isset($p_country_id) && empty($p_country_id)) {
            return false;
        }

        $query = "SELECT * FROM $this->DPPreambleTable WHERE `location_id`= " . $this->_db->quote($p_country_id) . " LIMIT 0,1";
        //$query = "SELECT * FROM `#__gpo_datapage_preamble_values` WHERE `id`= " . $this->_db->quote( $p_country_id ) . " LIMIT 0,1";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();

        if (empty($data->id)) {
            return false;
        }
        return $data;
    }

    function getDpPreamblesByLocationAndLang($p_country_id, $language) {
        if (!isset($p_country_id) && empty($p_country_id)) {
            return false;
        }

        if (empty($language)) {
            return false;
        }

        if ('en' == $language) {
            $DPPreambleTableName = '#__gpo_datapage_preamble_values';
        } else if ('es' == $language) {
            $DPPreambleTableName = '#__gpo_datapage_preamble_values_es';
        } else if ('fr' == $language) {
            $DPPreambleTableName = '#__gpo_datapage_preamble_values_fr';
        }

        $query = "SELECT 
                         * 
                  FROM 
                         $DPPreambleTableName
                  WHERE     
                         `location_id`= " . $this->_db->quote($p_country_id) .
                " LIMIT 0,1";

        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();

        if (empty($data->id)) {
            return false;
        }

        return $data;
    }

    function getDPMetaDataInfo() {
        $query = "SHOW  COLUMNS  FROM " . $this->_db->quoteName($this->DPTable);

        $this->_db->setQuery($query);
        $data = $this->_db->loadColumn();

        return $data;
    }

    function getDPPreamblesMetaData() {
        $query = "SHOW 
		               COLUMNS 
				  FROM " .
                $this->_db->quoteName($this->DPPreambleTable);

        $this->_db->setQuery($query);
        $data = $this->_db->loadColumn();

        return $data;
    }

    function getAllDpData($p_location_id = false) {
        $query = "SELECT * FROM $this->DPTable ";
        if (isset($p_location_id) && !empty($p_location_id)) {
            $query .= 'WHERE 
		              `location_id`= ' . $this->_db->quote($p_location_id);
        }
        $query .= " ORDER BY id ASC";

        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();

        if (empty($data)) {
            return false;
        }
        return $data;
    }

    function saveDP($input) {
 $jinput = JFactory::getApplication()->input;
        $create_dp_status = $jinput->get('create_dp_status', false);
        $passed_location_id = $jinput->get('passed_location_id', false);
        $location_name = $jinput->get('location', false);
        $update_time = $jinput->get('update_time', false);
        $updated_at = $jinput->get('updated_at', false);
        if ($create_dp_status && $passed_location_id) {
            $resultC = $this->createDP($passed_location_id, $location_name);
            return $resultC;
        }
        $metaData = $this->getDPMetaDataInfo();
        $updateArray = array();
        foreach ($metaData as $key => $val) {
            if (in_array($val, array('id', 'location', 'location_id', 'updated_at'))) {
                continue;
            }
            $updateArray["$val"] = $input["$val"];
        }
        //get the timestamp at the updation of data
        $unix_timestamp = ( isset($_SERVER['REQUEST_TIME']) ) ? $_SERVER['REQUEST_TIME'] : date('U');
        $updatedAt = date('Y-m-d H:i:s', $unix_timestamp);

        if ($update_time) {
            $updatedAt = $updated_at; //update the time posted from the form
        }

        $updateQuery = "UPDATE " .
                $this->_db->quoteName($this->DPTable) .
                " SET ";
        foreach ($updateArray as $key => $val) {
            $updateQuery .= $this->_db->quoteName($key) . ' = ' . $this->_db->Quote($val) . ', ';
        }
        $updateQuery .= $this->_db->quoteName('updated_at') . ' = ' . $this->_db->Quote($updatedAt);

        $whereClause = " WHERE " . $this->_db->quoteName('id') . ' = ' . $this->_db->Quote($input['id']);

        $updateQuery = $updateQuery . $whereClause;

        if (!empty($input['id']) && !empty($updateQuery)) {
            $this->_db->setQuery($updateQuery);
            $result = $this->_db->execute();

            return $result;
        }

        return false;
    }

    function saveDPPreambles($input) {
    	 $jinput = JFactory::getApplication()->input;
        $create_dp_status = $jinput->get('create_dp_status', false);
        $passed_location_id = $jinput->get('passed_location_id', false);
        $location_name = $jinput->get('location', false);
        $update_time = $jinput->get('update_time', false);
        $updated_at = $jinput->get('updated_at', false);

        if ($create_dp_status && $passed_location_id) {
            $resultC = $this->createDP($passed_location_id, $location_name);
            return $resultC;
        }

        $metaData = $this->getDPPreamblesMetaData();
        $udpdateArray = array();
        foreach ($metaData as $key => $val) {
            if (in_array($val, array('id', 'location', 'location_id', 'updated_at'))) {
                continue;
            }
            $updateArray["$val"] = $input["$val"];
        }
        //get the timestamp at the updation of data
        $unix_timestamp = ( isset($_SERVER['REQUEST_TIME']) ) ? $_SERVER['REQUEST_TIME'] : date('U');
        $updatedAt = date('Y-m-d H:i:s', $unix_timestamp);
        if ($update_time) {
            $updatedAt = $updated_at; //update the time posted from the form
        }

        $updateQuery = "UPDATE " .
                $this->_db->quoteName($this->DPPreambleTable) .
                " SET ";
        foreach ($updateArray as $key => $val) {
            $updateQuery .= $this->_db->quoteName($key) . ' = ' . $this->_db->Quote($val) . ', ';
        }
        $updateQuery .= $this->_db->quoteName('updated_at') . ' = ' . $this->_db->Quote($updatedAt);

        $whereClause = " WHERE " . $this->_db->quoteName('id') . ' = ' . $this->_db->Quote($input['id']);
        $updateQuery = $updateQuery . $whereClause;

        if (!empty($input['id']) && !empty($updateQuery)) {
            $this->_db->setQuery($updateQuery);
            $result = $this->_db->execute();

            return $result;
        }
        return false;
    }

    function addTopLevelColumn($h1Column, $beforeafter, $columnNamehierarchy, $columnTitle, $columnType, $hyperLinkName, $hyperLink) {
       $jinput = JFactory::getApplication()->input;
        $this->oUser = JFactory::getUser();
        if ($this->oUser->get('isRoot')) {
            //echo 'it is root<br />';
            $insertPosition = ($beforeafter == "selected_before") ? "before" : "after";
            $currentSortOrder = $this->getsortorder($h1Column, NULL);
            $newSortOrder = $this->calculateSortOrder($currentSortOrder, $insertPosition);

            /*
              $inserttoplevelcolumn = "INSERT INTO " .
              $this->_db->quoteName('#__gpo_datapage_hierarchy') .
              "(`id`,`column_name`,`parent_id`,`sort_order`,`active`,`column_type`,`external_hyperlink_name`,`external_hyperlink`)" .
              "VALUES(NULL, $columnNamehierarchy,NULL,'$newSortOrder','1','$columnType','$hyperLinkName','$hyperLink' )";
             */

            ##it's a top level column. Top level columns don't have any parent
            $dataArrayHierarchy = array(
                'id' => NULL,
                'column_name' => $columnNamehierarchy,
                'parent_id' => NULL,
                'column_title' => $columnTitle,
                'sort_order' => $newSortOrder,
                'active' => 1,
                'column_type' => $columnType,
                'external_hyperlink_name' => $hyperLinkName,
                'external_hyperlink' => $hyperLink
            );
           // print_r( $dataArrayHierarchy);die();


            if (!empty($dataArrayHierarchy)) {
                //before insert the new column, update sort order of other top level nodes
                $this->updateSortOrderOfSameNodes(NULL, $newSortOrder); //<--- what does this ?
                $result = $this->_addToDPHierarchy($dataArrayHierarchy);

            }

            return $result;
        }
    }

    function updateSortOrderOfSameNodes($parentId, $sortOrder) {
        if (is_null($parentId) OR !$parentId OR $parentId='') {
            $findNodesQuery = "SELECT * FROM " .
                    $this->_db->quoteName('#__gpo_datapage_hierarchy') .
                    " WHERE `parent_id` is NULL " .
                    " AND `sort_order` >= " . $this->_db->Quote($sortOrder);
        } else {
            $findNodesQuery = "SELECT * FROM " . $this->_db->quoteName('#__gpo_datapage_hierarchy') .
                    " WHERE `parent_id` = $parentId " .
                    " AND `sort_order` >= " . $this->_db->Quote($sortOrder);
        }
//echo $findNodesQuery; die();
        $this->_db->setQuery($findNodesQuery);
        $data = $this->_db->loadObjectList();

        foreach ($data as $val) {
            $id = $val->id;
            $currentSortOrder = $val->sort_order;
            $newSortOrder = $this->calculateSortOrder($currentSortOrder, 'after');

            if (!empty($id) && !empty($newSortOrder)) {
                $updateSortOrderQuery = "UPDATE " .
                        $this->_db->quoteName('#__gpo_datapage_hierarchy') .
                        " SET `sort_order` = " . $this->_db->Quote($newSortOrder) .
                        " WHERE `id` = " . $this->_db->Quote($id);

                $this->_db->setQuery($updateSortOrderQuery);
                $result = $this->_db->execute();
                //echo $this->_db->getQuery() . $this->_db->getErrorMsg() . '<br />';
            }
        }

        return $result;
    }

    /**
     * Checks if a column/field exists in a table structure
     * @param  $column Column name
     * @param  $table Table name (without prefix). Prefix will be auto added
     * @return bool Returns TRUE if exists otherwise false;
     */
    function isTableFieldExists($column, $table) {
        $table_name = "#__" . $table;
        $fields = $this->_db->getTableColumns($table_name, true); //getTableColumns
        $fields = $fields[$table_name];
        foreach ($fields as $field => $type) {
            if ($column == $field) {
                return true;
            }
        }
        return false;
    }

    function isColumnNameExists($column_name, $table = 'gpo_datapage_hierarchy') {
        $this->_db->setQuery("SELECT `id` FROM `#__$table`  WHERE `column_name` LIKE " . $this->_db->Quote($column_name));
        $result = $this->_db->loadRowList();
        if (count($result)) {
            unset($result); //free up memeory
            return true;
        }
        return false;
    }

    function cleanDPColumnNameString($columnName) {

        //strip quotes if any
        $columnName = strtolower(str_replace(array('"', "'"), '', $columnName));

        //glue the words by underscore
        $columnName = field_title($columnName);

        if (strlen($columnName) > 61) {
            $columnName = substr($columnName, 0, 61); // cut the name to get 64 chars
        }

        return $columnName;
    }

    function addNewColumn() {

        $jinput = JFactory::getApplication()->input;
        $columnName = $jinput->get('column_name', '', 'STRING');
        $columnName = $this->cleanDPColumnNameString($columnName);

        //check if column name (alias) already exists in gpo_datapages table
        if ($this->isTableFieldExists($columnName, 'gpo_datapages')) {
            return 'Sorry, the same column (alias) already exists in Datapages table';
        }
        if ($this->isColumnNameExists($columnName, 'gpo_datapage_hierarchy')) {
            return 'Sorry, the same column (alias) already exists in Datapages Hierarchy table';
        }
        if ($this->isColumnNameExists($columnName, 'gpo_preambles_switches_master_list')) {
            return 'Sorry, the same column (alias) already exists in Preambles Switches Master List table';
        }

        $columnTitle = $jinput->get('column_title', false, 'STRING');
        $columnType = $jinput->get('column_type', false, 'STRING');

       /* echo '$columnType='.$columnType; echo '<br/>';
        echo '$columnTitle='.$columnTitle; echo '<br/>';
        echo '$columnName='.$columnName; echo '<br/>';

        die();*/

        $hyperLinkName = $jinput->get('external_hyperlink_name', false, 'STRING');
        $hyperLink = $jinput->get('external_hyperlink', false, 'STRING');

        $columnNamePreamble = $columnName . '_p';
        //check if column name preamble already exists in gpo_datapage_preamble_values table
        if ($this->isTableFieldExists($columnNamePreamble, 'gpo_datapage_preamble_values')) {
            return 'Sorry, the same column already exists in Datapages Preamable table';
        }
        $columnNamePreamble = $this->_db->quoteName($columnNamePreamble);
//echo '$columnNamePreamble='.$columnNamePreamble; echo '<br />';
        $is_gateway = $jinput->get('is_gateway', 0);
        $pid = $jinput->get('ParentId');
        $ParentIdHierarchy = $pid;

        $h1Column = $jinput->get('h1column', false, 'STRING');
        $h2Column = $jinput->get('h2column', false, 'STRING');
        $h3Column = $jinput->get('h3column', false, 'STRING');

        $beforeafter = $jinput->get('insert_position', false, 'STRING');
        $defaultPreamble = $jinput->get('default_preamble', false, 'STRING');
        $defaultSwitch = $jinput->get('default_switch', false, 'STRING');
        ##For Link Category, Use the Source Site Name as the default Switch
        if (1 == $columnType) {
            $defaultSwitch = $hyperLinkName;
        }
        $usjs = $jinput->get('usjs', false);
        $defaultCheckjs = $jinput->get('checkjs', false, 'STRING');
        $selectedjs = $jinput->get('selectedjs', false, 'STRING');
        $locationList = $jinput->get('locations', false, 'STRING');
        $locationList2 = $jinput->get('locations2', false, 'STRING');
        $locationList3 = $jinput->get('locations3', false, 'STRING');
        $locationsArray = array();
        if (is_array($locationList)) {
            $locationsArray = $locationList;
        }
        if (is_array($locationList2)) {
            $locationsArray = array_merge($locationsArray, $locationList2);
        }

        if (is_array($locationList3)) {
            $locationsArray = array_merge($locationsArray, $locationList3);
        }

        if (empty($columnName) || empty($columnNamePreamble)) {
            return false;
        }

        //if (empty($h3Column) && empty($h2Column) && !empty($h1Column)) {
        if (empty($h3Column) && empty($h2Column) && !empty($h1Column) && ($beforeafter == 'selected_after' || $beforeafter == 'selected_before')) {
/*echo '$h3Column='.$h3Column; echo '<br/>';
echo '$h2Column='.$h2Column; echo '<br/>';
echo '$h1Column='.$h1Column; echo '<br/>';
echo '$beforeafter='.$beforeafter; echo '<br />';
die();*/
            $retVal = $this->addTopLevelColumn($h1Column, $beforeafter, $columnName, $columnTitle, $columnType, $hyperLinkName, $hyperLink);
            return $retVal;
        } else {
            $sortOrder = $this->getsortorder($h2Column, $h3Column);
            $tmpsortorder = ($beforeafter == "selected_before") ? "before" : "after";
            $getcalculateSortOrder = $this->calculateSortOrder($sortOrder, $tmpsortorder);

            $dataArrayHierarchy = array(
                'id' => NULL,
                'column_name' => $columnName,
                'parent_id' => $ParentIdHierarchy,
                'column_title' => $columnTitle,
                'sort_order' => $getcalculateSortOrder,
                'active' => 1,
                'is_gateway' => $is_gateway,
                'column_type' => $columnType,
                'external_hyperlink_name' => $hyperLinkName,
                'external_hyperlink' => $hyperLink
            );

            $dataArrayMasterList = array('column_name' => $columnName,
                'parent_id' => $ParentIdHierarchy,
                'preamble' => $defaultPreamble,
                'switches' => $defaultSwitch,
                'active' => 1,
                'language' => 'en'
            );

            ##Need to update preambles? (For all Locations)
            if (!empty($defaultPreamble) && !empty($defaultCheckjs)) {

                $updatePreambleWhereClause = '';
                $updatePreambleValues = 1;
            }

            ##Need to update preambles? (For US Jurisdictions only)
            else if (!empty($defaultPreamble) && !empty($usjs)) {

                $USLocations = $this->getAllUSJurisdictions();
                $locaiton_list = implode(',', $USLocations);
                $updatePreambleWhereClause = " WHERE location_id IN(" . $locaiton_list . ") ";
                $updatePreambleValues = 1;
            }

            ##Need to update preambles? (For Selected Locations only)
            else if (!empty($defaultPreamble) && !empty($selectedjs) && count($locationsArray) > 0) {

                $loc_list = implode(',', $locationsArray);
                $updatePreambleWhereClause = " WHERE location_id IN(" . $loc_list . ") ";
                $updatePreambleValues = 1;
            } else {
                $updatePreambleWhereClause = '';
                $updatePreambleValues = 0;
            }

            ##processing done. now run all the queries one by one.
            ##1.## Insert to DP tables
            if (!empty($columnName)) {

                $addToDP = $this->_addColumnToDatapage($columnName);
                if (!$addToDP) {
                    ##main query failed, adding column failed. Abort, return to the base.
                    return $addToDP;
                }

                ##2.## Update DP Values,for external hyperlink
                if ($columnType == 1 && (!empty($h2Column) || !empty($h1Column))) {

                    if (!empty($selectedjs) && is_array($locationsArray)) {
                        $loc_list = implode(',', $locationsArray);
                        $updateDPWhereClause = " WHERE location_id IN(" . $loc_list . ") ";
                    } else if (!empty($usjs)) {
                        $USLocations = $this->getAllUSJurisdictions();
                        $locaiton_list = implode(',', $USLocations);
                        $updateDPWhereClause = " WHERE location_id IN(" . $locaiton_list . ") ";
                    }

                    $updateDPValues = $this->_updateDPValues($columnName, $hyperLink, $updateDPWhereClause);
                }
            }
/* echo $columnNamePreamble; die();*/ 
            ##3.## Add to gpo_datapage_preambles table
            if (!empty($columnNamePreamble)) {
//echo 'izvrseno';die();
                $result = $this->_addColumnToDatapagePreamble($columnNamePreamble);
            }

            /*
             * before inserting the new column with the new sort order
             * we need to update the sort-order of the existing nodes
             * by the parent id.
             *
             */
            $this->updateSortOrderOfSameNodes($ParentIdHierarchy, $getcalculateSortOrder);

            ##4.## Add to Datapage Hierarchy table
            $result = $this->_addToDPHierarchy($dataArrayHierarchy);


            ##5.## Add to gpo_preambles_switches_master_list table       
            $result = $this->_addToDPMasterList($dataArrayMasterList);

            ##6.## Update default preambles where applicable
            if (!empty($defaultPreamble) && $updatePreambleValues) {

                $this->_updateDefaultPreambles($columnNamePreamble, $defaultPreamble, $updatePreambleWhereClause);
            }

            return $result;
        }//else end
    }

    //tonmoy...............    
    function getsortorder($h2column, $h3column) {

        $querysortorder = "SELECT sort_order
         FROM `#__gpo_datapage_hierarchy` ";
        if (!empty($h3column)) {
            $querysortorder .= 'WHERE 
                      `id`= ' . $this->_db->quote($h3column);
        } else if (!empty($h2column)) {
            $querysortorder .= 'WHERE 
                      `id`= ' . $this->_db->quote($h2column);
        }
        if (!empty($querysortorder)) {
            $this->_db->setQuery($querysortorder);
            $result = $this->_db->loadObject();
        }

        return $result->sort_order;
    }

    //morshed function
    function calculateSortOrder($currentSortOrder, $insertPosition = 'after') {
        $parts = explode('.', $currentSortOrder);
        $reverseParts = array_reverse($parts);

        $lastSegment = $reverseParts[0];

        if ('after' == $insertPosition) {
            if ($lastSegment == '9' ||
                    $lastSegment == '99' ||
                    $lastSegment == '999' ||
                    $lastSegment == '9999' ||
                    $lastSegment == '99999'
            ) {
                $lastSegment .= '1';
            } else {
                $lastSegment += 1;
            }
        } else {
            if ($lastSegment != 1) {
                $lastSegment -= 1;
            }
        }

        $reverseParts[0] = $lastSegment;
        $newSortOrder = implode('.', array_reverse($reverseParts));

        return $newSortOrder;
    }

    function _addColumnToDatapage($columnName) {

        /* dejans */
        //$this->_db->setQuery("SHOW COLUMNS FROM `#__gpo_datapages` LIKE '".$columnName."'");
        $application = JFactory::getApplication();
        $db=JFactory::getDBO();
        $en=$db->getTableColumns("#__gpo_datapages");
        $es=$db->getTableColumns("#__gpo_datapages_es");
        $fr=$db->getTableColumns("#__gpo_datapages_fr");
        if (array_key_exists($columnName,$en) || array_key_exists($columnName,$es) || array_key_exists($columnName,$fr)) {
            $application->enqueueMessage('Alias already exist', 'error');
            return false;
        }
        /* dejans end */
        
        
        $columnName = trim($columnName);
        if (empty($columnName)) {
            return false;
        }

        $columnName = $this->_db->quoteName($columnName);

        $alterQueryEn = "ALTER TABLE " .
                $this->_db->quoteName('#__gpo_datapages') .
                " ADD " .
                $columnName .
                " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL " .
                " AFTER `published` ";

        $alterQueryEs = "ALTER TABLE " .
                $this->_db->quoteName('#__gpo_datapages_es') .
                " ADD " .
                $columnName .
                " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL " .
                " AFTER `published` ";

        $alterQueryFr = "ALTER TABLE " .
                $this->_db->quoteName('#__gpo_datapages_fr') .
                " ADD " .
                $columnName .
                " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL " .
                " AFTER `published` ";

        $this->_db->setQuery($alterQueryEn);
        $result1 = $this->_db->execute();

        $this->_db->setQuery($alterQueryEs);
        $result2 = $this->_db->execute();

        $this->_db->setQuery($alterQueryFr);
        $result3 = $this->_db->execute();

        return $result1;
    }

    function _updateDPValues($columnName, $updateValue, $whereClause = '') {

        if (empty($columnName)) {
            return false;
        }

        $columnName = $this->_db->quoteName($columnName);

        $updateValuesEn = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapages') .
                " SET " . $columnName . " = '" . $updateValue . "'"
                . ' ' . $whereClause;

        $updateValuesEs = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapages_es') .
                " SET " . $columnName . " = '" . $updateValue . "'"
                . ' ' . $whereClause;

        $updateValuesFr = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapages_fr') .
                " SET " . $columnName . " = '" . $updateValue . "'"
                . ' ' . $whereClause;

        $this->_db->setQuery($updateValuesEn);
        $result = $this->_db->execute();

        $this->_db->setQuery($updateValuesEs);
        $result = $this->_db->execute();

        $this->_db->setQuery($updateValuesFr);
        $result = $this->_db->execute();

        return $result;
    }

    /*
     * update column data 
     * 
     */

    function _updateColumnData($columnName, $updateValue, $locationId, $language = '') {

        if (empty($columnName) || empty($locationId)) {
            return false;
        }

        if (empty($language)) {
            return false;
        }

        $columnName = $this->_db->quoteName($columnName);
        $whereClause = 'WHERE `location_id`=' . $locationId;
        $updateValue = $this->_db->Quote($updateValue);

        $updateValuesEn = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapages') . " " .
                "SET " .
                $columnName . " = " . $updateValue . ", " .
                'updated_at' . " = '" . date('Y-m-d H:i:s') . "' " .
                $whereClause;

        $updateValuesEs = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapages_es') . " " .
                "SET " .
                $columnName . " = " . $updateValue . ", " .
                'updated_at' . " = '" . date('Y-m-d H:i:s') . "' " .
                $whereClause;

        $updateValuesFr = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapages_fr') . " " .
                "SET " .
                $columnName . " = " . $updateValue . ", " .
                'updated_at' . " = '" . date('Y-m-d H:i:s') . "' " .
                $whereClause;


        if ('all' == $language || 'en' == $language) {
            $this->_db->setQuery($updateValuesEn);
            $resultEn = $this->_db->execute();
        }

        if ('all' == $language || 'es' == $language) {
            $this->_db->setQuery($updateValuesEs);
            $resultEs = $this->_db->execute();
        }

        if ('all' == $language || 'fr' == $language) {
            $this->_db->setQuery($updateValuesFr);
            $resultFr = $this->_db->execute();
        }

        return $resultEn || $resultEs || $resultFr;
    }

    function _updateColumnPreamble($columnName, $updateValue, $locationId, $language = '') {

        if (empty($columnName) || empty($locationId)) {
            return false;
        }

        if (empty($language)) {
            return false;
        }

        $columnName = $this->_db->quoteName($columnName);
        $whereClause = 'WHERE `location_id`=' . $locationId;
        $updateValue = $this->_db->Quote($updateValue);

        $updateValuesEn = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapage_preamble_values') . " " .
                "SET " .
                $columnName . " = " . $updateValue . ", " .
                'updated_at' . " = '" . date('Y-m-d H:i:s') . "' " .
                $whereClause;

        $updateValuesEs = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapage_preamble_values_es') . " " .
                "SET " .
                $columnName . " = " . $updateValue . ", " .
                'updated_at' . " = '" . date('Y-m-d H:i:s') . "' " .
                $whereClause;

        $updateValuesFr = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapage_preamble_values_fr') . " " .
                "SET " .
                $columnName . " = " . $updateValue . ", " .
                'updated_at' . " = '" . date('Y-m-d H:i:s') . "' " .
                $whereClause;

        if ('all' == $language || 'en' == $language) {
            $this->_db->setQuery($updateValuesEn);
            $resultEn = $this->_db->execute();
        }

        if ('all' == $language || 'es' == $language) {
            $this->_db->setQuery($updateValuesEs);
            $resultEs = $this->_db->execute();
        }

        if ('all' == $language || 'fr' == $language) {
            $this->_db->setQuery($updateValuesFr);
            $resultFr = $this->_db->execute();
        }

        return $resultEn || $resultEs || $resultFr;
    }

    function _updateDefaultPreambles($columnNamePreamble, $defaultPreamble, $whereClause = '') {

        if (empty($columnNamePreamble)) {
            return false;
        }

        $updatePreambleQueryEn = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapage_preamble_values') .
                " SET " .
                $columnNamePreamble . " = " . $this->_db->Quote($defaultPreamble) .
                ' ' . $whereClause;

        $updatePreambleQueryEs = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapage_preamble_values_es') .
                " SET " .
                $columnNamePreamble . " = " . $this->_db->Quote($defaultPreamble) .
                ' ' . $whereClause;


        $updatePreambleQueryFr = "UPDATE " .
                $this->_db->quoteName('#__gpo_datapage_preamble_values_fr') .
                " SET " .
                $columnNamePreamble . " = " . $this->_db->Quote($defaultPreamble) .
                ' ' . $whereClause;

        $this->_db->setQuery($updatePreambleQueryEn);
        $result = $this->_db->execute();

        $this->_db->setQuery($updatePreambleQueryEs);
        $result = $this->_db->execute();

        $this->_db->setQuery($updatePreambleQueryFr);
        $result = $this->_db->execute();

        return $result;
    }

    function _addColumnToDatapagePreamble($columnNamePreamble) {

        $columnNamePreamble = trim($columnNamePreamble);
        if (empty($columnNamePreamble)) {
            return false;
        }


        $alterQueryPreambleEn = "ALTER TABLE " .
                $this->_db->quoteName('#__gpo_datapage_preamble_values') .
                " ADD " .
                $columnNamePreamble .
                " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL " .
                "AFTER `created_at` ";

        $alterQueryPreambleEs = "ALTER TABLE " .
                $this->_db->quoteName('#__gpo_datapage_preamble_values_es') .
                " ADD " .
                $columnNamePreamble .
                " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL " .
                "AFTER `created_at` ";

        $alterQueryPreambleFr = "ALTER TABLE " .
                $this->_db->quoteName('#__gpo_datapage_preamble_values_fr') .
                " ADD " .
                $columnNamePreamble .
                " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL " .
                "AFTER `created_at` ";
/*echo $alterQueryPreambleEn;
echo '<br />';
echo $alterQueryPreambleEs;
echo '<br />';
echo $alterQueryPreambleFr;
exit(); 
die();*/
        $this->_db->setQuery($alterQueryPreambleEn);
        $result1 = $this->_db->execute();

        $this->_db->setQuery($alterQueryPreambleEs);
        $result2 = $this->_db->execute();

        $this->_db->setQuery($alterQueryPreambleFr);
        $result3 = $this->_db->execute();

        return $result1;
    }

    function _addToDPHierarchy($dataArrayHierarchy = array()) {

        if (empty($dataArrayHierarchy)) {
            return false;
        }

        $dataObject = (object) $dataArrayHierarchy;
        //echo '$dataObject:<br />';print_r($dataObject) ; die();

        // Insert the object into the user profile table.
        $result = $this->_db->insertObject('#__gpo_datapage_hierarchy', $dataObject);
        //echo '$result:=';echo $result ; die();
        return $result;
    }

    function _addToDPMasterList($dataArrayMasterList = array()) {

        if (empty($dataArrayMasterList)) {
            return false;
        }

        $dataObjectEn = (object) $dataArrayMasterList;
        $dataObjectEs = (object) $dataArrayMasterList;
        $dataObjectFr = (object) $dataArrayMasterList;

        $dataObjectEs->language = 'es';
        $dataObjectFr->language = 'fr';

        // Insert the object into the user profile table.
        $resultEn = $this->_db->insertObject('#__gpo_preambles_switches_master_list', $dataObjectEn, id);
        $resultEs = $this->_db->insertObject('#__gpo_preambles_switches_master_list', $dataObjectEs, id);
        $resultFr = $this->_db->insertObject('#__gpo_preambles_switches_master_list', $dataObjectFr, id);

        return $resultEn;
    }

    /*
     * Get All the location list 
     * of US Jurisdictions 
     * 
     * That is all the locations linked 
     * under US (United States)
     * 
     */

    function getAllUSJurisdictions() {
        $locations = array();
        ## 194 is the Id of 'United States'
        $usjsQuery = "SELECT `lo`.`id`
                      FROM  `j25_gpo_location` AS `lo` 
                      INNER JOIN `j25_gpo_location_links` AS `link` ON `lo`.`id`=`link`.`link_id`
                      WHERE `lo`.`type` =  'jurisdiction' AND `link`.`location_id`=(SELECT `id` FROM `j25_gpo_location` WHERE `name` = 'United States')";
        $this->_db->setQuery($usjsQuery);
        $result = $this->_db->loadAssocList();

        foreach ($result as $location) {
            $locations[] = trim($location['id']);
        }

        return $locations;
    }

    function getUSJurisdictionNames() {
        $this->_db->setQuery("SELECT `lo`.`name` FROM `j25_gpo_location` AS `lo`
                               INNER JOIN 
                               `j25_gpo_location_links` AS `link` ON `lo`.`id`=`link`.`link_id`
                               WHERE 
                               `lo`.`type` = 'jurisdiction' 
                               AND 
                               `link`.`location_id`=(SELECT `id` FROM `j25_gpo_location` WHERE `name` = 'United States')                   
                               ORDER BY `lo`.`name`");

        $result = $this->_db->loadAssocList();
        $locations = array();

        foreach ($result as $location) {
            $locations[] = trim($location['name']);
        }

        return $locations;
    }

    function updateDP($updateArray, $id) {
        if (empty($id)) {
            return false;
        }
        if (empty($updateArray)) {
            return false;
        }
        $flag = 0;
        $updateQuery = "UPDATE " .
                $this->_db->quoteName($this->DPTable) .
                " SET  ";
        foreach ($updateArray as $key => $val) {
            $updateQuery .= $this->_db->quoteName($key) . ' = ' . $this->_db->Quote($val) . ', ';
            $flag = 1;
        }
        $updateQuery = substr($updateQuery, 0, -2);
        $whereClause = " WHERE " . $this->_db->quoteName('id') . ' = ' . $this->_db->Quote($id);

        $updateQuery = $updateQuery . $whereClause;

        if (!empty($id) && !empty($updateQuery) && $flag) {
            $this->_db->setQuery($updateQuery);
            $result = $this->_db->execute();
            return $result;
        }
        return false;
    }

    function createDP($location_id, $location_name) {
        if (empty($location_id)) {
            return false;
        }
        $location_name = $this->_db->Quote($location_name);
        $unix_timestamp = ( isset($_SERVER['REQUEST_TIME']) ) ? $_SERVER['REQUEST_TIME'] : date('U');
        $created_at = $this->_db->Quote(date('Y-m-d H:i:s', $unix_timestamp));

        $insertDPQuery = "INSERT INTO " .
                $this->_db->quoteName($this->DPTable) .
                " (`id`, `location_id`, `location`, `published`, `created_at`) " .
                "VALUES(NULL, $location_id, 0, $location_name, $created_at)";

        $insertDPPreamblesQuery = "INSERT INTO " .
                $this->_db->quoteName($this->DPPreambleTable) .
                " (`id`, `location_id`, `location`, `created_at`) " .
                "VALUES(NULL, $location_id, $location_name, $created_at)";

        if (!empty($insertDPQuery)) {
            $this->_db->setQuery($insertDPQuery);
            $result = $this->_db->execute();
            if (!empty($insertDPPreamblesQuery)) {
                $this->_db->setQuery($insertDPPreamblesQuery);
                $result = $this->_db->execute();
                $insert_id = $this->_db->insertid();
                $this->autofillDefaultPreambles($insert_id);
            }

            return $result;
        }
        return false;
    }

    /**
     * @param  $insert_id
     * @return void
     */
    function autofillDefaultPreambles($insert_id) {

        //get the default preamble values
        $preamble_master_list = $this->getPreamblesMasterList();
        $query = "UPDATE " . $this->_db->quoteName($this->DPPreambleTable) . " set  ";
        foreach ($preamble_master_list as $item) {
            if (!is_null($item->parent_id)) {
                $query .= " `" . $item->column_name . "_p`=" . $this->_db->Quote($item->preamble) . ", ";
            }
        }
        $query = rtrim($query, ', ');
        $query .= " WHERE `id`=" . $this->_db->Quote($insert_id);
        $this->_db->setQuery($query);

        $result = $this->_db->execute();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * publish or unpublish a DP
     * by id and location_id
     * 
     */

    function publishDP($dp_id, $location_id, $dp_status = 'publish') {
        if (empty($dp_id) || empty($location_id)) {
            return false;
        }
        $status = ( 'publish' == $dp_status ) ? 1 : 0;
        $unix_timestamp = ( isset($_SERVER['REQUEST_TIME']) ) ? $_SERVER['REQUEST_TIME'] : date('U');
        $publishedAt = date('Y-m-d H:i:s', $unix_timestamp);

        $updateQuery = "UPDATE " .
                $this->_db->quoteName($this->DPTable) .
                " SET  " .
                "`published` = " . $this->_db->Quote($status) . ", " .
                "`published_at` = " . $this->_db->Quote($publishedAt) .
                " WHERE " .
                "`id` = " . $this->_db->Quote($dp_id) . " AND " .
                "`location_id` = " . $this->_db->Quote($location_id);

        $this->_db->setQuery($updateQuery);
        $result = $this->_db->execute();

        return $result;
    }

    function cleanDPData($p_location_id, $type = 'dry_run') {
        $dp_data = $this->getAllDpData($p_location_id);
        $resultArray = array();
        foreach ($dp_data as $key => $row) {
            $updateArray = array(); //clear array for each location
            foreach ($row as $k => $v) {
                if (!empty($v) && !is_nondata_column($k)) {
                    $cleaned_data = clean_data($v);

                    if ($cleaned_data != $v) {
                        $updateArray[$k] = clean_data($v);
                        $temp_data = clean_data_type($v);
                        $temp_data['column'] = $k;
                        $updateArrayType[] = $temp_data;
                    }
                }
            }

            if ('run' == $type) {
                $result = $this->updateDP($updateArray, $row['id']);
            } else if ('dry_run' == $type) {
                if (!empty($updateArray)) {
                    foreach ($updateArrayType as $types_error) {
                        $type_desc = array('1' => 'is {q123} to is{q123}', '2' => 'is{q1}{q2} to is{q1} {q2}', '3' => 'double space');
                        $resultArray[] = '<p style="margin:0 0 0 10px">unclean data found in <b>' . $types_error['column'] . '</b> for the location <strong>' .
                                $row['location'] . ' of type ' . $types_error['type'] . '</strong></p>';
                    }
                }
            }

            if ($result) {
                foreach ($updateArrayType as $types_error) {
                    $resultArray[] = 'data type ' . $types_error['type'] . ' successfully cleaned for the location <strong>' . $row['location'] . '</strong> in <b>' . $types_error['column'] . '</b><br>';
                }
            }
        }
        return $resultArray;
    }

    function checkDPForMissingSources($p_location_id) {
        $dp_data = $this->getAllDpData($p_location_id);
        $resultArray = array();
        foreach ($dp_data as $key => $row) {
            foreach ($row as $k => $v) {
                if (!empty($v) && !is_nondata_column($k)) {
                    $arrayIndex = $row['location'] ? $row['location'] : $row['location_id'];
                    //$cleaned_data = clean_data($v);
                    if (is_any_missing_q_or_n_cite_found($v)) {
                        $resultArray[$arrayIndex][] = $k;
                    }
                }
            }
        }
        //ftp_debug( $resultArray, ' result array ');
        return $resultArray;
    }

    /*
     * 
     * Find & Replace Tool Function
     * 
     */

    function frtInsertSearchHistory($p_insert_array) {
        $p_insert_array = (object) $p_insert_array;

        if (empty($p_insert_array)) {
            return false;
        }
        $ret = $this->_db->insertObject("#__gpo_frt_search_history", $p_insert_array, 'id');
        return true;
    }

    function getColumnNameByHierarchyId($p_hierarchy_id) {
        if (empty($p_hierarchy_id)) {
            return false;
        }

        $columnTitle = 'column_title';
        if ($this->currentLang != 'en') {
            
        }

        $query = "SELECT 
		              `$this->DPHierarchyTable`.id,
		              `$this->DPHierarchyTable`.column_name,
		              `$this->DPHierarchyTable`.column_title,
		              ``.jargon_term   
		          FROM 
		              `$this->DPHierarchyTable`           
		          WHERE 
					   `id`=" . $this->_db->Quote($p_hierarchy_id) . " ";

        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        return $data->column_name;
    }

    function frtPerformSearch($p_options_array) {

        $tableName = $p_options_array['table_name'];
        $columnName = $p_options_array['column_name'];
        $from = $p_options_array['from'];

        if (empty($tableName) || empty($columnName)) {
            return false;
        }

        $sql = "
				 SELECT *
				 FROM `#__$tableName` as `t`
				 WHERE `t`.`$columnName` LIKE " . $this->_db->quote("%" . $from . "%") . "
				 ORDER BY `t`.`id` DESC;
			   ";
        $this->_db->setQuery($sql);
        $data = $this->_db->loadObjectList();
        $this->total = count($data);
        return $data;
    }

    /*
     *  
     * preambles and switches 
     * master list related functions 
     * 
     * */

    function getPreamblesMasterList() {
        $query = "SELECT 
                        * 
                 FROM " .
                $this->_db->quoteName($this->DPMasterListTable) .
                "WHERE 
                        `language` = " . $this->_db->Quote($this->currentLang);

        $this->_db->setQuery($query);
        $data = $this->_db->loadObjectList();
        $preambles_list = array();
        foreach ($data as $key => $val) {
            $preambles_list[$val->column_name] = $val;
        }

        return $preambles_list;
    }

    function updatePreamblesMasterList() {
        $column_names = $jinput->get('column_name', false);
        $preambles = $jinput->get('preambles', false, $_POST, 'none', JREQUEST_ALLOWHTML);
        $switches = $jinput->get('switches', false, $_POST, 'none', JREQUEST_ALLOWHTML);

        $affected_rows = 0;
        foreach ($column_names as $key => $col_name) {

            $updateQuery = "UPDATE 
   	  	                    $this->DPMasterListTable 
   	  	                 SET 
   	  	                    `preamble` = {$this->_db->Quote($preambles[$key])} 
   	  	                    ,`switches` = {$this->_db->Quote($switches[$key])}
   	  	                 WHERE
   	  	                    `column_name` = {$this->_db->Quote($col_name)}"
                    .
                    " AND 
                         `language` = " . $this->_db->Quote($this->currentLang);
            $this->_db->setQuery($updateQuery);
            $result = $this->_db->execute();
            if ($result) {
                $affected_rows++;
            }
        }

        return $affected_rows;
    }

    function getDPHierarchy() {
    	 $jinput = JFactory::getApplication()->input;
        $columnTitle = 'column_title';
        if ($this->currentLang != 'en') {
            $columnTitle = $columnTitle . '_' . $this->currentLang;
        }

        $query = "SELECT * FROM $this->DPHierarchyTable";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObjectList();
        $columns_list = array();
        foreach ($data as $row) {
            $columns_list[$row->column_name] = $row->{$columnTitle};
        }

        return $columns_list;
    }

    function getDPColumnsInfo() {
        $query = "SELECT * FROM $this->DPHierarchyTable";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObjectList();
        $columns_list = array();
        foreach ($data as $row) {
            $columns_list[$row->column_name] = $row;
        }

        return $columns_list;
    }

    /**
     * This method delets the column info record from *gpo_datapage_hierarchy table.
     * @param <string> $column_name name of column (should not be confused with table field/column)
     * @return <boolean>
     */
    function deleteDPColumnInfo($table_name, $column_name) {
        $query = "DELETE FROM `#__{$table_name}` WHERE `column_name`=" . $this->_db->Quote($column_name);
        $this->_db->setQuery($query);
        $result = $this->_db->execute();
        return $result;
    }

    function dropDPDropTableField($table_name, $field_name) {
        //$query1 = "SHOW COLUMNS FROM `#__{$table_name}` LIKE '{$field_name}'";
        $cols = $this->_db->getTableColumns('#__'.$table_name, true);
        //print_r($cols);die();
        //$this->_db->setQuery($query1);
        //$result1 = $this->_db->execute();
        if (array_key_exists($field_name, $cols)) {
            $query = "ALTER TABLE `#__{$table_name}` DROP {$this->_db->quoteName($field_name)}";
            $this->_db->setQuery($query);
            $result = $this->_db->execute();
        }
        return $result;
    }

    function updateDPHierarchyColumnInfo($column_name, $display_type = '', $gcite_id = '', $region_aggregation = '', $y_axix_label = '', $sort_order = '', $parent_id = '') {

        $query = "UPDATE 
                         $this->DPHierarchyTable 
                  SET 
                        `display_type` = " . $this->_db->Quote($display_type) . ", 
                        `gcite_id` = " . $this->_db->Quote($gcite_id) . ",
                        `region_aggregation_type` = " . $this->_db->Quote($region_aggregation) . ",
                        `vertical_chart_label` = " . $this->_db->Quote($y_axix_label) . ",
                        `sort_order` = " . $this->_db->Quote($sort_order);

        if (!empty($parent_id) && 'NULL' != $parent_id) {
            $query .= ",`parent_id` = " . $this->_db->Quote($parent_id);
        }

        $query .= " WHERE 
                         `column_name` = " . $this->_db->Quote($column_name);


        $this->_db->setQuery($query);
        $result = $this->_db->execute();
        return $result;
    }

    /*
     * Update only Column Title
     * 
     */

    function updateDPHierarchyColumnTitle($column_name, $column_title) {
        if (empty($column_name)) {
            return false;
        }

        $columnTitle = 'column_title';
        if ($this->currentLang != 'en') {
            $columnTitle = $columnTitle . '_' . $this->currentLang;
        }

        $query = "UPDATE 
                       $this->DPHierarchyTable  
                 SET 
                       `$columnTitle` = " . $this->_db->Quote($column_title) . "
                 WHERE 
                       `column_name`  = " . $this->_db->Quote($column_name);

        $this->_db->setQuery($query);
        $result = $this->_db->execute();
        return $result;
    }

    /*
     * 
     * Propagate Es/Fr preambles from the Preambles master list 
     * 
     */

    function propagatePreamblesFromMasterList($langCode) {
        if (empty($langCode)) {
            return false;
        }

        if ('en' == strtolower($langCode)) {
            return false;
        }

        $preamblesMasterList = $this->getPreamblesMasterList();
        $topLevelColumns = getTopLevelHeaders();

        $ignoreColumns = array(
            'id',
            'location_id',
            'location',
            'published',
            'created_at',
            'published_at'
        );

        foreach ($preamblesMasterList as $key => $val) {
            if (in_array($key, $ignoreColumns)) {
                continue;
            }

            if (in_array($key, $topLevelColumns)) {
                continue;
            }

            $columnName = $key . '_p';
            $updateQuery = "Update 
                                $this->DPPreambleTable 
                           SET 
                          " .
                    $this->_db->quoteName($columnName) . " = " .
                    $this->_db->Quote($val->preamble);

            $this->_db->setQuery($updateQuery);
            $result = $this->_db->execute();

//           if( !$result ) {
//               echo $this->_db->errorMsg;
//           }
        }

        return $result;
    }

    #################################################################
    ########################
    ########################
    ######################## DP Data automation script starts 
    ######################## from here 
    ########################
    #################################################################

    function getAllDPSourceDataByCategory($categoryName, $dataSource, $year = null) {
        if (empty($categoryName) || empty($dataSource)) {
            return null;
        }

        $tableName = $categoryName . '_' . $dataSource;
        if (!empty($year)) {
            return false;
        }

        $query = "SELECT 
                        * 
                 FROM 
                        $tableName 
                ";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObjectList();
        
        return $data;
    }
    
    #################################################################
    #######################################
    ################################
    ######################## DP Data automation script starts 
    ######################## from here 
    ################################
    #######################################
    #################################################################
    function getAllDPSourceDataByYear($sheetData) {
        if ( empty($sheetData) ) {
             return null;
        }
        
        //get the headers 
        $headerRow = $sheetData[1];
        $headerYears = array();
        $data = array();
        foreach( $headerRow as $key => $val ) {
            if( empty($val) ) {
                continue;
            }
            $headerYears[$key] = $val;
        }
        
        foreach( $sheetData as $rowNo => $rowData ) {
            if(1 == $rowNo) { continue; } //Row #1 is for the column headers
            
            $data[ $rowData['A'] ] = array(); //Country Names
            $qCite                 = $rowData['B']; //QCite for the row 
            
            foreach( $headerYears as $key => $val ) {
               $colValue = $rowData[$key];
               if ( !empty(trim($qCite)) ) {
                   //$colValue = trim($colValue) . '{' . $qCite . '}';
               }
               $data[ $rowData['A'] ][$val] = $colValue;
            }
            
        }
        
        return $data;
    }

}
?>