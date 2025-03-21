<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');


class GpoModelDatapages extends JModelLegacy {

    public $total;
    public $data;

    ##DP Tables##
    private $currentLang;
    private $tableSeparator;
    private $DPTable = '#__gpo_datapages';
    private $DPPreambleTable = '#__gpo_datapage_preamble_values';
    private $DPMasterListTable = '#__gpo_preambles_switches_master_list';
    private $DPHierarchyTable = '#__gpo_datapage_hierarchy';
    private $DPGlossaryTable  =  '#__gpo_datapage_glossary';


    function __construct($currentLang = 'en') { 
    $jinput = JFactory::getApplication()->input;
       
        
        parent::__construct();
        $this->limit = (int) '10';
        $this->limitstart = $jinput->get('limitstart', 0, 'int');
        $this->currentLang = strtolower($currentLang);
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
        $query .= " WHERE `name`='" . $p_location_name . "'";

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


    function gettophierarchy($lang ) {
        
        //var_dump($lang );
        
        if('en' ==$lang)
        {
            $query = "SELECT 	`id`,`column_title` ,`column_name` as `column_alias` FROM " .
            $this->_db->quoteName('#__gpo_datapage_hierarchy') .
            " WHERE `parent_id` is NULL " .
            " AND `sort_order` >= " . $this->_db->Quote($sortOrder);

        }elseif('es' == $lang)
        {
                    $query = "SELECT 	`id`,`column_title_es` ,`column_name` as `column_alias` FROM " .
        $this->_db->quoteName('#__gpo_datapage_hierarchy') .
        " WHERE `parent_id` is NULL " .
        " AND `sort_order` >= " . $this->_db->Quote($sortOrder);
        }elseif('fr' == $lang)
        {

            $query = "SELECT 	`id`,`column_title_fr` ,`column_name` as `column_alias` FROM " .
            $this->_db->quoteName('#__gpo_datapage_hierarchy') .
            " WHERE `parent_id` is NULL " .
            " AND `sort_order` >= " . $this->_db->Quote($sortOrder);
        }else
        {
            $query = "SELECT 	`id`,`column_title` ,`column_name` as `column_alias` FROM " .
            $this->_db->quoteName('#__gpo_datapage_hierarchy') .
            " WHERE `parent_id` is NULL " .
            " AND `sort_order` >= " . $this->_db->Quote($sortOrder);

        }

        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();
        return $data;
    }


    function getDpDataByLocationId($p_country_id) {
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
                "WHERE 
		              `location_id`= " . $this->_db->quote($location_id) .
                " LIMIT 0,1";

        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();

        if (empty($data->id)) {
            return false;
        }
        return $data;
    }

    function getCitationDataofquotes($var1){
        $query = "SELECT `id`,`title`,`source`,`content`,`page`,`city`,`publisher` FROM `#__gpo_citations_quotes`";
        $query .= " WHERE `id`='" . $var1 . "'";
        if (!empty($query))
        {
            $some= array();
            $this->_db->setQuery($query);
            $data = $this->_db->loadObject();
            $some = $data;
            return $data; 
                
        }else{
            exit;
        }
        
    
    }

    function getCitationDataofnews($var1){
        $query = "SELECT `id`,`title`,`source`,`content`,`page` FROM `#__gpo_citations_news`";
        $query .= " WHERE `id`='" . $var1 . "'";

        if (!empty($query))
        {
            $this->_db->setQuery($query);
            $data = $this->_db->loadObject();
            return $data;
                
        }else{
            exit;
        }
        
    }

    function getDPDataByCategoryAndLang($p_category_name, $location_id, $language ='en') {
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
                    "WHERE 
                    `location_id`= " . $this->_db->quote($location_id) .
                " LIMIT 0,1";

        $this->_db->setQuery($query);
        $data = $this->_db->loadObjectList();

        if ( empty($data) ) {
            return false;
        }
        
        return $data;
    }




    function getDPPreambleByCategoryAndLocation($p_category_name, $location_id, $language ='en') {
        if ( !isset($p_category_name) && empty($p_category_name) ) {
            return false;
        }

        if (empty($language)) {
            return false;
        }

        if ('en' == $language) {
            $DPTableName = '#__gpo_datapage_preamble_values';
        } else if ('es' == $language) {
            $DPTableName = '#__gpo_datapage_preamble_values_es';
        } else if ('fr' == $language) {
            $DPTableName = '#__gpo_datapage_preamble_values_fr';
        }

        $query = "SELECT 
                id, location_id, location," .
                $this->_db->quoteName($p_category_name .'_p') . " as `columnPreamble`
		  FROM " . 
                        $this->_db->quoteName($DPTableName) . 
                    "WHERE 
                    `location_id`= " . $this->_db->quote($location_id) .
                " LIMIT 0,1";

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


    function getGlossaryByColumnAlias($p_column_alias)
    {
        $query = "SELECT 
        $this->DPGlossaryTable.`id`, 
        $this->DPHierarchyTable.`column_name` as category,
        $this->DPGlossaryTable.`title`,
        $this->DPGlossaryTable.`subtitle`,
        $this->DPGlossaryTable.`content`,
        $this->DPGlossaryTable.`published` 
        FROM 
        $this->DPHierarchyTable 
        JOIN 
        $this->DPGlossaryTable 
        ON 
        $this->DPHierarchyTable.`gcite_id` = $this->DPGlossaryTable.`id`    
        WHERE  
        $this->DPHierarchyTable.`column_name` = " . $this->_db->quote($p_column_alias);
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        return $data;
    }

    // function gettophierarchy($id) {
    //     $query = "SELECT `column_name` FROM `#__gpo_datapage_hierarchy` WHERE `parent_id`= ". $this->_db->quote($p_country_id) ."  ORDER BY `id` ASC";
    //     $this->_db->setQuery($query);
    //     $data = $this->_db->loadAssocList();
    //     return $data;
    // }


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



    /**
     * @param  $insert_id
     * @return void
     */

    /*
     * publish or unpublish a DP
     * by id and location_id
     * 
     */


    /*
     * 
     * Find & Replace Tool Function
     * 
     */


    function getDPHierarchy() {
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
    
    #################################################################
    #######################################
    ################################
    ######################## DP Data automation script starts 
    ######################## from here 
    ################################
    #######################################
    #################################################################
    function getAllDPSourceDataByYear($categoryName, $sheetData) {
        if ( empty($categoryName) || empty($sheetData) ) {
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