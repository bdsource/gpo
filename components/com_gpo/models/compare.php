<?php
defined('_JEXEC') or die ('Restricted Access');
jimport('joomla.application.component.model.php');

//get some helper method to use in charts
require_once(JPATH_ADMINISTRATOR.'/components/com_gpo/helper/datapage.php');
//include_once(JPATH_BASE . '/components/com_gpo/helpers/datapage.php');

class GpoModelCompare extends JModelLegacy
{
    
    var $citation_regex_pattern = '/\{([a-z][0-9]{1,11})\}/';
    var $column_display_type;
    //var $currentLangCode;
    var $allowedLangs   = array('es','fr');
    var $locationString = 'name';
    
    ##DP Tables##
    private $currentLangCode;
    private $tableSeparator;
  
    private $DPTable            = '#__gpo_datapages';
    private $DPPreambleTable    = '#__gpo_datapage_preamble_values';
    private $DPMasterListTable  = '#__gpo_preambles_switches_master_list';
    private $DPHierarchyTable   = '#__gpo_datapage_hierarchy';
    
    function __construct($currentLangCode='en')
    {
        parent::__construct();
        
        $this->currentLangCode = strtolower($currentLangCode[0]);
        $this->tableSeparator  = '_';
        
        $this->_initializeTableNames();
        
        /*
        $lang = JFactory::getLanguage();
        $langTag = $lang->getTag();
        if( strlen($langTag) >2 ) {
            $this->currentLangCode = strtolower(substr($langTag,0,-3));
        }
        */
        
        if( in_array($this->currentLangCode, $this->allowedLangs) ) {
            $this->locationString .= '_' . $this->currentLangCode; 
        }
        
    }

    
    /*
     * initialize DP table names according to lang switch
     * 
     */ 
    function _initializeTableNames() 
    {
        $this->DPTable = $this->_makeTableName($this->DPTable);
        $this->DPPreambleTable = $this->_makeTableName($this->DPPreambleTable);
    }
    
    
    function _makeTableName($inputName) 
    {
        if( empty($inputName) ) {
            return $inputName;
        }
        
        #For English we won't add language prefix as it is the default
        if('en' == $this->currentLangCode || empty($this->currentLangCode)) {
            $tableName = trim($inputName);
        }else {
            $tableName = trim($inputName . $this->tableSeparator . $this->currentLangCode);
        }
        
        return $tableName;
    }
    
     
    function isColumnExists($column_name)
    {
        $this->_db->setQuery("SELECT `id` FROM `#__gpo_datapage_hierarchy` WHERE `column_name` LIKE " . $this->_db->Quote(trim($column_name)));
        $result = $this->_db->loadRowList();
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getSwitchColumns()
    {
        $this->_db->setQuery("SELECT `column_name` FROM `#__gpo_datapage_hierarchy` WHERE `display_type` IN (" . $this->_db->Quote('switch_table') . ", " . $this->_db->Quote('switch_table_switch_sort') . ")");
        $switches = $this->_db->loadColumn();
        return $switches;
    }

    function getRankColumns()
    {
        $this->_db->setQuery("SELECT `column_name` FROM `#__gpo_datapage_hierarchy` WHERE `display_type`=" . $this->_db->Quote('rank_table'));
        return $this->_db->loadColumn();
    }

    function getBarChartColumns()
    {
        $this->_db->setQuery("SELECT `column_name` FROM `#__gpo_datapage_hierarchy` WHERE `display_type`=" . $this->_db->Quote('bar_chart'));
        return $this->_db->loadColumn();
    }
    
    function getYAxisLabelByColumn($column_name) {
        
        $defaultLabel = "Value";
        if( empty($column_name) ) {
            return $defaultLabel;
        }
        
        $allLabels = getYChartLabelOptions($this->currentLangCode);
        
        $this->_db->setQuery("SELECT * FROM `#__gpo_datapage_hierarchy` 
                              WHERE `column_name` = " . $this->_db->Quote($column_name));
        $row = $this->_db->loadAssoc();
        $verticalLabel = $allLabels[$row['vertical_chart_label']];
        
        if( !empty($verticalLabel) ){
            return $verticalLabel;
        }

        return $defaultLabel;
    }
    

    function isNoComparisonColumn($column_name)
    {
        $this->_db->setQuery("SELECT `display_type` FROM `#__gpo_datapage_hierarchy` WHERE `column_name`=" . $this->_db->Quote($column_name));
        $display_type = $this->_db->loadRow(0);
        $display_type = $display_type[0];
        if (empty($display_type) OR 'no_comparison' == $display_type) {
            return true; //comparision is not avialable
        } else {
            return false;
        }

    }


    /**
     * This method will return a list of switches options for that column
     * @param  $column_name The name (alias) of the column
     * @return mixed List on success, false on failure
     */
    function getColumnSwitches($column_name)
    {
        $sql = "SELECT 
                       `preamble`,
                       `switches`
                FROM 
                       $this->DPMasterListTable 
                WHERE 
                       `column_name` = {$this->_db->Quote($column_name)}
                AND 
                       `language` = '$this->currentLangCode'
                ";
        
        $this->_db->setQuery($sql);
        $result = $this->_db->loadObject();
        //echo $result->switches.'<br/>';
        //now remove anything with {} from swithces
        $pattern = "/\{.*\}/";
        $switches = preg_replace($pattern, '', $result->switches);
        $switches = str_replace(array("'"), '', $switches);
        //now explode them with / as delimeter
        $switches = explode('/', $switches);
        $result->switches = $switches;
        return $result;
    }

    function getLocationPreambleDPValue($location_id, $column_name)
    {
        $sql = "SELECT 
                      pv.`{$column_name}_p` as preamble_value, 
                      dp.`$column_name` as  dp_value 
                FROM 
                      $this->DPPreambleTable as pv, 
                      $this->DPTable as dp 
                WHERE 
                      pv.`location_id` = {$this->_db->Quote($location_id)} 
                AND 
                      dp.location_id = {$this->_db->Quote($location_id)}";
        $this->_db->setQuery($sql);
        $result =  $this->_db->loadObject();
        
        return $result;
    }

    function isSwitchColumn($column_name)
    {
        $switches = $this->getSwitchColumns();
        if (in_array(trim($column_name), $switches)) {
            return true;
        } else {
            return false;
        }
    }

    function getDisplayType($column_name)
    {
        $this->_db->setQuery("SELECT `display_type` FROM `#__gpo_datapage_hierarchy` WHERE `column_name` = " . $this->_db->Quote($column_name));
        $display_type = $this->_db->loadRow();
        return $display_type[0];
    }

    function isRankColumn($column_name)
    {
        $rank_columns = $this->getRankColumns();
        if (in_array(trim($column_name), $rank_columns)) {
            return true;
        } else {
            return false;
        }
    }

    function isBarChartColumn($column_name)
    {
        $bar_chart_columns = $this->getBarChartColumns();
        if (in_array(trim($column_name), $bar_chart_columns)) {
            return true;
        } else {
            return false;
        }
    }

    function getOrderOptions($column_name)
    {
        $this->_db->setQuery("SELECT 
                                    `sorting_orders` 
                              FROM 
                                    $this->DPMasterListTable 
                              WHERE 
                                    `language` = '$this->currentLangCode' 
                              AND 
                                    `column_name`=" . $this->_db->Quote($column_name) . " LIMIT 1");

        $options = $this->_db->loadObject();
        $options = $options->sorting_orders;
        if (empty($options)) return false;
        $options = explode('/', trim($options));
        return $options;
    }

    /* This method will be used later
    function sortSwitchTable($location_data, $column_name){
        $order_options = $this->getOrderOptions($column_name);
        if(!count($order_options) OR false === $order_options){ //just order alphabetically
            $location_data = $this->getOrderedArray($location_data, 'location', SORT_ASC,$column_name, SORT_DESC);
            return $location_data;
        }
        //var_dump($order_options);
        $sorted_location_data = array();

        foreach($order_options AS $order_key=>$order_value){
            foreach($location_data AS $key=>$location_info){
                //echo $location_info[]
                if(trim($location_info[$column_name])==trim($order_value)){
                    $sorted_location_data[] = $location_info;
                }
            }
        }
        //var_dump($sorted_location_data);
        return $sorted_location_data;
    }
    */

    function getChartMetaDesc($location_name, $preamble_value, $dp_value=NULL)
    {
        
        $DPHelper = new DatapageHelper();

        $html = '';
        
        //replace the # with location name
        if (!empty($location_name)) {
            $addPrefix = $DPHelper->isNeedToAddThe($location_name, true, $preamble_value);
            $displayName = ($addPrefix) ? "$addPrefix $location_name" : $location_name;
            $preamble_value = str_replace('#', $displayName, $preamble_value);
        }

        if (strpos($preamble_value, '*****') !== false) {
            $html .= str_replace('*****', '', $preamble_value);
        }
        else if (strpos($preamble_value, '****') !== false) {
            $html .= str_replace('****', '', $preamble_value);
        }
        else if (strpos($preamble_value, '~') !== false) {
            $html .= str_replace('~', '', $preamble_value);
        }

        $html = trim($html) . '... (open to display chart). Armed violence reduction, development, guns and peace.';
        return $html;
    }
    
    
    function preparePreamble($location_name, $preamble_value, $dp_value, $link = '', $link_title='')
    {
        //var_dump($location_name, $preamble_value, $DP_value);
        $DPHelper = new DatapageHelper();

        //clear the caret (^) symbols in DP value
        $pattern ='$\^(.*)\^$';
        if(preg_match($pattern, $dp_value)){
            $dp_value = preg_replace('$\^(.*)\^$', '<a href="' . $link . '" target="_blank" title="' . $link_title . '">$1</a>', $dp_value);
        } else {
            //we need a way to link only the value, not the citation itself
        }

        $html = '';
        if (strpos($dp_value, ';') !== false) {
            $dp_value = '<br><br>' . str_replace(';', '<br>', $dp_value);
        }
        //replace the # with location name
        if (!empty($location_name)) {
            $addPrefix = $DPHelper->isNeedToAddThe($location_name, true, $preamble_value);
            $displayName = ($addPrefix) ? "$addPrefix $location_name" : $location_name;
            $preamble_value = str_replace('#', $displayName, $preamble_value);
        }

        if (strpos($preamble_value, '*****') !== false) {
            $html .= str_replace('*****', $dp_value, $preamble_value);
        }
        else if (strpos($preamble_value, '****') !== false) {
            $html .= str_replace('****', $dp_value, $preamble_value);
        }
        else if (strpos($preamble_value, '~') !== false) {
            //replace with preamble value including the link to DP
            $html .= str_replace('~',  $dp_value, $preamble_value);
        }
        else
        {

            $html .= $preamble_value . ' ' . $dp_value;

        }
        return $html;
    }

    function prepareSwitchTable(&$location_data, &$column_name, &$base_location_info, $isGroup=NULL)
    {
        //sort the location data based on 'switch' values
        if ('switch_table_switch_sort' == $this->column_display_type) {
            //echo 'Sort on switches';
            $location_data = $this->getOrderedArray($location_data, $column_name, SORT_ASC, 'location', SORT_ASC);
        } else {
            //echo 'sort on location name';
            $location_data = $this->getOrderedArray($location_data, 'location', SORT_ASC, $column_name, SORT_DESC);

        }

        //$tbody = '<tbody>';
        $tbody='';
        $i = 0;
        foreach ($location_data AS $location) {
            $outURL = (!$isGroup) ? JURI::base() . "firearms/find-gun-policy-facts?country=" . 
                                    urlencode(str_replace('&', ' and ', $location['location'])) . "&column=" . urlencode($column_name)
                                  : JURI::base() . "firearms/find-gun-policy-facts?group=" . 
                                    $location['location_id'] . "&column=" . urlencode($column_name);
            //$title = $location['location'] . ': Click here for source data';
            $title = $location[$this->locationString] . ': ' . JText::_('COM_GPO_CHARTS_HOVER_POPUP');

            //put the base location at top
            if ($base_location_info->name == $location['location']) {
                $base_preamble_dp_value = $this->getLocationPreambleDPValue($base_location_info->id, $column_name);
                //var_dump($base_preamble_value, $location[$column_name]);
                $base_preamble_dp_value = $this->preparePreamble($base_location_info->{$this->locationString}, $base_preamble_dp_value->preamble_value, $base_preamble_dp_value->dp_value, $outURL, $title);
                $top_row = '';
                $top_row .= '<tr class="base_location"><th>' . $location[$this->locationString] . '</th>';
                $top_row .= '<th>' . $base_preamble_dp_value . '</th>' . PHP_EOL;
            } else {
                $tr_class = (fmod($i, 2) == 0) ? 'class="even"' : 'class="odd"';
                $tbody .= '<tr ' . $tr_class . '><td>' . $location[$this->locationString] . '</td>';
                $tbody .= '<td>'. ($location[$column_name]) .'&nbsp;&nbsp;&nbsp;<a href="' . $outURL . '" title="' . $title . '"><img src="'.JURI::root().'media/system/images/ref.png" border="0"/></a></td>' . PHP_EOL;

            }
            //$tbody .= '</tr>';
            $i++;
        }
        //$tbody .= '</tbody>';

        $table = '<table id="switchestable" class="comparetable" cellspacing="0"><thead>' . $top_row.'</thead>'.$tbody . '</table>';

        return $table;
    }

    function prepareRankTable(&$location_data, &$column_name, &$base_location_info, $isGroup=NULL)
    {
        $location_data = $this->getOrderedArray($location_data, $column_name, SORT_ASC);
        
        $tbody ='';
        $i = 0;
        foreach ($location_data AS $location) {
            $outURL = (!$isGroup) ? JURI::base() . "firearms/find-gun-policy-facts?country=" . 
                                    urlencode(str_replace('&', ' and ', $location['location'])) . "&column=" . urlencode($column_name)
                                  : JURI::base() . "firearms/find-gun-policy-facts?group=" . 
                                    $location['location_id'] . "&column=" . urlencode($column_name);
            
            $title = $location[$this->locationString] . ': ' . JText::_('COM_GPO_CHARTS_HOVER_POPUP');
            //put the base location at top
            if ($base_location_info->name == $location['location']) {
                $base_preamble_dp_value = $this->getLocationPreambleDPValue($base_location_info->id, $column_name);
                //var_dump($base_preamble_value, $location[$column_name]);
                $base_preamble_dp_value = $this->preparePreamble($base_location_info->{$this->locationString}, $base_preamble_dp_value->preamble_value, $base_preamble_dp_value->dp_value, $outURL, $title);
                $top_row = '';
                $top_row .= '<tr class="base_location"><th>' . $location[$this->locationString] . '</th>';
                $top_row .= '<th>' . $base_preamble_dp_value . '</th>' . PHP_EOL;
            } else {
                $tr_class = (fmod($i, 2) == 0) ? 'class="even"' : 'class="odd"';
                $tbody .= '<tr ' . $tr_class . '><th>' . $location[$this->locationString] . '</th>';
                //link the value only. Do not link the citation portion as it will be handled by citation plugin


                $tbody .= '<td>'. ($location[$column_name]) .'&nbsp;&nbsp;&nbsp;<a href="' . $outURL . '" title="' . $title . '"><img src="'.JURI::root().'media/system/images/ref.png" border="0" /></a></td>' . PHP_EOL;
            }
            $tbody .= '</tr>';
            $i++;
        }

        $table = '<table id="rankstable" class="comparetable" cellspacing="0"><thead>'.$top_row.'</thead><tbody>' . $tbody . '</tbody></table>';

        return $table;
    }


    function splitMultiYearData($data)
    {
        if (stripos($data, ';') === false) {
           
            if( stripos($data,':') !== false ) {
                //it's a country data, so autofix the format:  
                $data = $data . ';';
            }else {
                return $data;
            }   
        }
        
        //explode by semicolon
        $lines = explode(';', $data);
        $selectedLine = '';
        
        //check if $data has any #DataValue# pattern
        if (count($lines) > 0) {
            foreach( $lines as $line ) {
                if (false !== strpos($line, '#')) {
                    $selectedLine = $line;
                    break;
                }
            }
        }
        
        if (count($lines) > 0 && empty($selectedLine) ) {
            // need only first line of data
            $selectedLine = $lines[0];
        }
        
        if ( !empty($selectedLine) ) {
            //split by colon. left side is year, right side is data
            $selectedLine = explode(':', $selectedLine);
            $year_data = array(trim($selectedLine[0]) => trim($selectedLine[1]));
            return $year_data;
        }
        
    }

    function getLocationInfoBy($field, $value)
    {
        $query = 'SELECT 
                        `lo`.`id`, 
                        `lo`.`type`,
                        `lo`.`name`,
                        `lo`.`name_es`,
                        `lo`.`name_fr`,
                        `cat`.`id` as catid, 
                        `cat`.`alias` 
                  FROM 
                        `#__gpo_location` as `lo` 
                  INNER JOIN 
                        `#__categories` as `cat` ON lower(`lo`.`name`)=lower(`cat`.`title`) ' .
                 ' WHERE 
                        `lo`.' . $this->_db->quoteName($field) . ' = ' . $this->_db->quote($value) .
                 ' LIMIT 0,1';

        $this->_db->setQuery($query);
        return $this->_db->loadObject();
    }

    function getColumnByAlias($column_alias)
    {
        $this->_db->setQuery("SELECT * FROM `#__gpo_datapage_hierarchy` WHERE `column_name` = " . $this->_db->Quote($column_alias) . " LIMIT 1");
        return $this->_db->loadObject();
    }

    /**
     * Retrieves a list of countries whose $dp_column value is comparable (i.e. not empty)
     * @param  $dp_column The Column whose value should not be empty
     * @return mixed Returns
     */
    function getLocations($dp_column,$location_type=NULL)
    {
        if (empty($dp_column)) {
            return false;
        }
        
        $query = "SELECT 
                        `location_id`, 
                        `location`,
                        `lo`.`name`,
                        `lo`.`name_es`,
                        `lo`.`name_fr`
                  FROM 
                        $this->DPTable AS dp,`#__gpo_location` AS lo 
                  WHERE 
                        lo.`id` = dp.`location_id`  AND lo.`display` = '1' AND `$dp_column` > '' ";
        
        if( !empty($location_type) ) {
            if( 'country' == $location_type ) {
               $query .= " AND `lo`.`type` IN('country','jurisdiction') " .  " ";
            }else {
               $query .= " AND `lo`.`type` = " . $this->_db->quote($location_type) . " ";
            }
        }
        
        $query .= " ORDER BY `location`";
       //echo $query; 
        $this->_db->setQuery($query);
        $result1=$this->_db->loadObjectList();
        //print_r($result1);
        return $result1;
    }


    function getCitationId($data)
    {
        $pattern = '/\{([a-z][0-9]{1,11})\}/';
        preg_match($pattern, $data, $matches);
        if (!empty($matches[1])) {
            $citation = new stdClass();
            $citation->type = (substr($matches[1], 0, 1) == 'q') ? 'quotes' : 'news';
            $citation->id = substr($matches[1], 1);
            return $citation;
        }
        return false;
    }

    /**
     * If any column holds more than one data, it will fetch single value inside ^^ sign. Like: ^xxx^ which will return xxx only.
     * @param  $data
     * @return string
     */
    function getSingleValue($data)
    {
        //check if $data has any ^ symbol
        if (false !== strpos($data, '^')) {
            $pattern = '/\^(.*)\^/';
            preg_match($pattern, $data, $matches);
            if ($matches) {
                return $matches[1];
            }
        }

        return $data;
    }
    
     /**
     * If any column holds more than one data, it will fetch single value inside # sign. Like: #xxx# which will return xxx only.
     * @param  $data
     * @return string
     */
    function getDataValueForCompareYears($data)
    {
        //check if $data has any ^ symbol
        if (false !== strpos($data, '#')) {
            $pattern = '/\#(.*)\#/';
            preg_match($pattern, $data, $matches);
            if ($matches) {
                return $matches[1];
            }
        }

        return $data;
    }

    function clean_column_data($data, $column = null)
    {
        ##remove tags
        $data = preg_replace("/<!(.*?)!>/s", "", $data);                      

        //first check if it is multi year data
        $multi_year_data = $this->splitMultiYearData($data);
        
        if (false !== $multi_year_data) {
            if (is_array($multi_year_data)) {
                $data = current($multi_year_data);
            } else {
                $data = $multi_year_data;
            }
            
            ### get multiyear value surrounded by #xxx#
            $data = $this->getDataValueForCompareYears($data);
        }

        //now get a single value (the value inside the caret sign, if it exists)
        $data = $this->getSingleValue($data);
        //remove the {Q/N} from data
        $pattern = '/\{([a-z][0-9]{1,11})\}/';
        $data = preg_replace($pattern, '', $data);

        //remove the quotaion marks
        //$data = str_replace("'", '', $data);
        
        ### Remove # sign to denote the data for compareyears chart
        $data = str_replace('#', '', $data);

        /**
         * Clean comma and apostrophe only if it is chart column. 
         * Switch and ranks may need comma
         */
        if ($this->isBarChartColumn($column)) {
            $specialChars = array(',', "'", '%');
            $data = str_replace($specialChars, '', $data);
        }
        
        if( $this->currentLangCode == 'fr' && !$this->isRankColumn($column) && !$this->isSwitchColumn($column) ) {
            $data = str_replace('à', '', $data);  //number_of_privately_owned_firearms
            $data = str_replace('de', '', $data); //military_firearms
        }
        
        if( $this->currentLangCode == 'es' && !$this->isRankColumn($column) && !$this->isSwitchColumn($column) ) {
            $data = str_replace('a', '', $data);
            $data = str_replace('de', '', $data); //rate_of_civilian_firearm_possession, number_of_privately_owned_firearms
        }

        return trim($data);
    }

    /**
     * Sorts the array based on dp_column
     * @author jimpoz at jimpoz dot com
     * @link http://www.php.net/manual/en/function.array-multisort.php#100534
     * @return Array Returns a sorted array
     */
    function getOrderedArray()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    /**
     * This function will take column name and Locations (ID only) and will return the column value for each country
     * @param  $dp_column Name of the column whose data needs to be retrieved
     * @param mixed $locations comma separated or Array of Location IDs
     * @return array|bool
     */
    function getColumnData($dp_column, $locations = array(), $clean_data = TRUE)
    {
        //if array, convert it to comma separated values
        if (is_array($locations)) {
            $locations = implode(',', $locations);
        }
        
        $query = "SELECT 
                        `dp`.`location_id`, 
                        `dp`.`location`, 
                        `dp`.`$dp_column`,
                        `lo`.`name`,
                        `lo`.`name_es`,
                        `lo`.`name_fr` 
                  FROM 
                        $this->DPTable as `dp`
                  INNER JOIN 
                        `#__gpo_location` as `lo` 
                  ON 
                        `dp`.location_id = `lo`.id 
                  WHERE 
                        `dp`.`location_id` IN ($locations) 
                  LIMIT 500";
        
        $this->_db->setQuery($query);
        $results = $this->_db->loadAssocList();
        //now clean and sort data
        foreach ($results as $key => $result) {
            if ($clean_data) {

                $result['preamble'] = $result[$dp_column];
                $result[$dp_column] = $this->clean_column_data($result[$dp_column], $dp_column);
            } else {
                $result[$dp_column] = $result[$dp_column];
            }
            $results[$key] = $result;
        } 
        
        $results = $this->getOrderedArray($results, $dp_column, SORT_DESC, 'location_id', SORT_ASC);
        return $results;
    }
    
    
    /**
     * This function will take column name and Locations (ID only) and will return the column value for each country
     * @param  $dp_column Name of the column whose data needs to be retrieved
     * @param mixed $locations comma separated or Array of Location IDs
     * @return array|bool
     */
    function getGroupColumnData($dp_column, $groups = array(), $clean_data = TRUE)
    {
        require_once(JPATH_BASE . '/components/com_gpo/helpers/datapage.php');
        $DPHelper = new DatapageHelper();

        //if array, convert it to comma separated values
        if (!is_array($groups)) {
           $groups = explode(',', $groups);
        }
        
        $results = array();
        foreach( $groups as $key => $groupId ) {
            $query = "SELECT 
                             `$this->DPTable`.`location_id` as `loc_id`, 
                             `$this->DPTable`.`location` as `loc_name`, 
                             `$this->DPTable`.`$dp_column`, 
                             `#__gpo_location_to_groups`.`group_id`,
                             `#__gpo_location_to_groups`.`group_id` as `location_id`,
                             `#__gpo_groups`.`name` as `location` 
                      FROM 
                             `$this->DPTable` 
                      INNER JOIN 
                             `#__gpo_location_to_groups` ON `$this->DPTable`.location_id = `#__gpo_location_to_groups`.location_id 
                      INNER JOIN 
                             `#__gpo_groups` ON `#__gpo_groups`.`id` = `#__gpo_location_to_groups`.`group_id` 
                      WHERE 
                             `#__gpo_location_to_groups`.`group_id` = " . $this->_db->quote($groupId) . " LIMIT 500";
            
            $this->_db->setQuery($query);
            $p_dp_data = $this->_db->loadObjectList();
            
            $results[$groupId] = $this->processGroupDPData($p_dp_data, $dp_column);
        }
        
        //now clean and sort data        
        foreach ($results as $key => $result) {
            if ($clean_data) {

                $result['preamble'] = $result[$dp_column];
                $result[$dp_column] = $this->clean_column_data($result[$dp_column], $dp_column);
            } else {
                $result[$dp_column] = $result[$dp_column];
            }
            $results[$key] = $result;
        }
        $results = $this->getOrderedArray($results, $dp_column, SORT_DESC, 'location_id', SORT_ASC);

        return $results;
    }

    
    function processGroupDPData($p_dp_data, $dp_column, $regionId=NULL) {
        
            require_once(JPATH_BASE . '/components/com_gpo/helpers/datapage.php');
            $DPHelper = new DatapageHelper();
        
            $val = $dp_column;
            $cumulative = array();
            $dpdataCumulativeValue = '';
            $haveDecimalVal = false;
            $totalLocations = 0;
            $DPColumnsInfo = $DPHelper->getDPColumnsInfo();
            $results = array();
            
            foreach ($p_dp_data as $dpkey => $dpdata) {
                
                if (!empty($dpdata->{$val})) {
                    $totalLocations++;
                    //check if it has multiple years
                    if (strpos($dpdata->{$val}, ';') !== false) {
                        $multiyears = explode(';', rtrim($dpdata->{$val}, '; '));
                        
                        foreach($multiyears as $key=>$yearval) {
                            list($year,$yearValue) = array_map( 'trim',explode(':', $yearval) );
                            $year = $DPHelper->sanitizeYearValues($year);
                            if( empty($cumulative[$year]) ) {
                               $cumulative["$year"] = $DPHelper->santitizeNumericValues($yearValue);
                            }else {
                               $cumulative["$year"] = $DPHelper->addMultipleYearValues($cumulative["$year"], $yearValue);
                            }
                            
                            if (strpos($yearValue, '.') !== false) {
                                $haveDecimalVal = true;
                            }                            
                        }
                       // $dpdataCumulativeValue = implode(';',$DPHelper->parameterize_array($cumulative));
                        
                    }else{
                        $numValue = $DPHelper->removeCitations($dpdata->{$val});
                        if (is_numeric($numValue)) {
                            $dpdataCumulativeValue += $DPHelper->santitizeNumericValues($dpdata->{$val});
                        }
                    }
                    
                }//end if
                
            }//end foreach
            
            
            if($DPHelper->isShowAsAverage($DPColumnsInfo[$val]->region_aggregation_type)) {
                 $cumulative = $DPHelper->calculateAverage($cumulative,$totalLocations);
            }
            
            $dpdataCumulativeValue = implode(';', $DPHelper->parameterize_array($cumulative,$haveDecimalVal));
            
            
            $results[$val]          = $dpdataCumulativeValue;
            
            if( empty($regionId) ) {
                $results['location']    = $dpdata->location;
                $results['location_id'] = $dpdata->location_id;
            }else {
                $regionDetails = $DPHelper->getLocationById($regionId);
                $results['location']    = $regionDetails->name;
                $results['location_id'] = $regionDetails->id;
            }
            
            return $results;
    }
    
    
    /**
     * This function will take column name and Locations (ID only) and will return the column value for each country
     * @param  $dp_column Name of the column whose data needs to be retrieved
     * @param mixed $locations comma separated or Array of Location IDs
     * @return array|bool
     * 
     * To be Used for Region DP pages, don't confused with the parameter name group.
     * Region is also a group :), doesn't it?
     * 
     */
    function getRegionColumnData($dp_column, $groups = array(), $clean_data = TRUE)
    {
        //require_once(JPATH_BASE . '/components/com_gpo/helpers/datapage.php');
        JLoader::import( 'datapage', JPATH_BASE . DS . 'components' . DS . 'com_gpo' . DS . 'helpers' );
        JLoader::import( 'region', JPATH_BASE . DS . 'components' . DS . 'com_gpo' . DS . 'models' );
        
        $DPHelper     = new DatapageHelper();
        $regionModel  = JModelLegacy::getInstance( 'GpoModelRegion' );
 
        //if array, convert it to comma separated values
        if (!is_array($groups)) {
            $groups = explode(',', $groups);
        }
        
        $results = array();
        foreach( $groups as $key => $groupId ) {
            //get all region locations
            $regionLocations = $regionModel->getAllLocationsByRegion($groupId);
            $regionLocationsInQuery = implode(',', $regionLocations);
            
            $query = "SELECT 
                             `$this->DPTable`.`location_id` as `loc_id`, 
                             `$this->DPTable`.`location` as `loc_name`, 
                             `$this->DPTable`.`$dp_column`, 
                             `#__gpo_location`.`id` as `group_id`,
                             `#__gpo_location`.`id` as `location_id`,
                             `#__gpo_location`.`name` as `location` 
                      FROM 
                             `$this->DPTable` 
                      
                      INNER JOIN 
                             `#__gpo_location` ON `$this->DPTable`.location_id = `#__gpo_location`.id 
                      
                      WHERE 
                             `#__gpo_location`.`id` IN( " . $regionLocationsInQuery . ") LIMIT 500";
            
            $this->_db->setQuery($query);
            
            $p_dp_data = $this->_db->loadObjectList();
            
            $results[$groupId] = $this->processGroupDPData($p_dp_data, $dp_column, $groupId);
        }

        //now clean and sort data        
        foreach ($results as $key => $result) {
            if ($clean_data) {

                $result['preamble'] = $result[$dp_column];
                $result[$dp_column] = $this->clean_column_data($result[$dp_column], $dp_column);
            } else {
                $result[$dp_column] = $result[$dp_column];
            }
            $results[$key] = $result;
        }
        //echo '<pre>' . print_r($results,true) . '</pre>';        
        
        $results = $this->getOrderedArray($results, $dp_column, SORT_DESC, 'location_id', SORT_ASC);
        return $results;
    }
    
      
    function random_color($base_location = '')
    {
        if (!empty($base_location)) return 'FF0033';
        mt_srand((double)microtime() * 1000000);
        $c = '';
        while (strlen($c) < 6) {
            $c .= sprintf("%02X", mt_rand(0, 255));
        }
        return $c;
    }

    function arrayToXml(&$location_data, $column = '', $base_location_id = '', $isGroup=NULL)
    {
        //global $mainframe;
        $mainframe =& JFactory::getApplication();
        $DPHelper  = new DatapageHelper();
        $loggedInUser = & JFactory::getUser();
        $chartData = array();
        $xAxisName = JText::_('COM_GPO_CHARTS_PAGE_XAXISNAME');
        $verticalAxisLabel = $this->getYAxisLabelByColumn($column);
        
        $chartData['chart']['yAxisValuesPadding'] = '10';
        $numberSuffix = '';
        $showPercentageValues= '';
        $chartData = array();
        
        $numberFormatOptions = '';
        if('fr' == $this->currentLangCode) {
           $numberFormatOptions = "decimalSeparator=',' thousandSeparator=' '";
           $chartData['chart']['decimalSeparator'] = ',';
           $chartData['chart']['decimalSeparator'] = ' ';
        }
        if('es' == $this->currentLangCode) {
           $numberFormatOptions = "decimalSeparator=',' thousandSeparator='.'";
           $chartData['chart']['decimalSeparator'] = ',';
           $chartData['chart']['decimalSeparator'] = '.';
        }
        
        ## Determine if % data
        foreach ($location_data as $key => $location_info) {
            if( strpos($location_info['preamble'],'%') !== false ) {
                $numberSuffix = "numbersuffix='%25'";
                $showPercentageValues="'1'";
                $chartData['chart']['numbersuffix'] = '%';
                break;
            }
        }
        
        $chartData['chart']['animation']                = "1";
		$chartData['chart']['topMargin']        = "30";
		
		if( !empty($loggedInUser->id) ) {
            $chartData['chart']['exportEnabled']            = "1";
            //$chartData['chart']['canvasTopMargin']        = "25";            
            //$chartData['chart']['toolBarHAlign']          = "RIGHT";            
            $chartData['chart']['toolBarVAlign']            = "TOP"; 		 
        }
        $chartData['chart']['showToolTip']              = "1";
        $chartData['chart']['use3DLighting']            = "1";
        
        $chartData['chart']['toolTipBorderColor']       = '#000000';
        $chartData['chart']['toolTipBgColor']           = '#666666';
        $chartData['chart']['toolTipBgAlpha']           = "80";
        $chartData['chart']['toolTipColor']             = '#FFFFFF';
        $chartData['chart']['labelFontBold']            = "0";
        $chartData['chart']['baseFontColor']            = "#000000";
        $chartData['chart']['outCnvBaseFontColor']      = "#707A64";
        $chartData['chart']['outCnvBaseFont']           =  'Helvetica Neue,Arial';      
        $chartData['chart']['chartTopMargin']           = "0";
        $chartData['chart']['canvasTopMargin']          = "0";
        $chartData['chart']['yAxisNamePadding']         = "0";	
        $chartData['chart']['chartLeftMargin']          = "0";
        $chartData['chart']['chartRightMargin']         = "0";
        $chartData['chart']['chartBottomMargin']        = "0";
        $chartData['chart']['labelPadding']             = "2";
        $chartData['chart']['rotateValues']             = "1";
        $chartData['chart']['useRoundEdges']            = "1";
        $chartData['chart']['xAxisName']                = "$xAxisName";
        $chartData['chart']['yAxisName']                = "$verticalAxisLabel";
        $chartData['chart']['showNames']                = "1";
        $chartData['chart']['decimalPrecision']         = "0";
        $chartData['chart']['formatNumberScale']        = "0";
        $chartData['chart']['labelDisplay']             = "Rotate";
        $chartData['chart']['slantLabels']              = "1";
        $chartData['chart']['useEllipsesWhenOverflow']  = "0";
        $chartData['chart']['decimals']                 = "2";
        $chartData['chart']['maxLabelWidthPercent']     = "100";
        $chartData['chart']['maxLabelHeight']           = '200';
        // $chartData['chart']['logoURL']               = JURI::root()."templates/gunpolicy/images/gpo_watermark.png";
        $chartData['chart']['canvasBgAlpha']            = "10";
 	$chartData['chart']['showBorder']               = "0";
        $chartData['chart']['showAlternateHgridColor']  = "1";
        //$chartData['chart']['bgImage']                = JURI::root()."templates/gunpolicy/images/gpo_watermark_chart.png";
        $chartData['chart']['bgImage']                  = JURI::root()."templates/gunpolicy/images/gpo_watermark_chart2.png";
        //Background image transparency 
        $chartData['chart']["bgImageAlpha"] 		= "100";
        //$chartData['chart']["bgImageDisplayMode"]  	= "stretch";

        //$chartData['chart']['logoPosition']           = "TR";
        //$chartData['chart']['logoAlpha']              = "100";
        $chartData['chart']['showFCMenuItem']           = "0";
        $chartData['chart']['showPrintMenuItem']        = "0";
        //$chartData['chart']['theme']                    = "zune2";
        
        
        //$chartData['chart']['caption']                = "Test Caption";
        $chartData['chart']['xAxisNameFontSize']        = "11";
        $chartData['chart']['subCaption']               = JText::_('COM_GPO_CHARTS_PAGE_CHARTCAPTION');
        $chartData['chart']['subCaptionFont']           = "Verdana";
        $chartData['chart']['subCaptionFontColor']      = "#97A786";
        $chartData['chart']['subCaptionFontSize']       = "10";
        $chartData['chart']['subCaptionFontBold']       = "0";
		
        $chartData['chart']['captionAlignment']         = "center";
        $chartData['chart']['captionHorizontalPadding'] = "35";
        
        $chartData['chart']["paletteColors"]            =  "#0075c2";
        $chartData['chart']["valueFontColor"]           =  "#889a75";
        $chartData['chart']["baseFont"]                 =  "Helvetica Neue,Arial";
        $chartData['chart']["captionFontSize"]          =  "14";
        $chartData['chart']["placeValuesInside"]        =  "0";
        $chartData['chart']["showShadow"]               =  "1";
        $chartData['chart']["divlineColor"]             =  "#999999";
        $chartData['chart']["divLineDashed"]            =  "1";
        $chartData['chart']["divlineThickness"]         =  "1";
        $chartData['chart']["divLineDashLen"]           =  "1";
        $chartData['chart']["divLineGapLen"]            =  "1";
        $chartData['chart']["canvasBgColor"]            =  "#aabbcc,#112233";
        $chartData['chart']["bgColor"]                  =  "#ffffff";
        $chartData['chart']["canvasBgAngle"]            =  "0";
        $chartData['chart']["captionOnTop"]             =  "0";
        
        if(count($location_data) > 20) {
            //$chartData['chart']['yAxisValuesPadding'] = '15';
            $chartData['chart']['labelPadding']         = "0";
        }
        
        ##Fix for longer location names in y axis
        ##Example URL: http://www.gunpolicy.org/fr/firearms/compare/194/
        ##rate_of_homicide_any_method/1,6,344,24,35,43,44,52,58,280,237,238,114,246,166,340,126,156,293,10
        foreach ($location_data as $key => $location_info) {
           $locLen = strlen($location_info[$this->locationString]);
           if($locLen > 25 ) {
              $chartData['chart']['labelFontBold'] = "0";
              $chartData['chart']['labelPadding']  = "0";
              break;
           }
        }
        
        foreach ($location_data as $key => $location_info) {
            $outURl = (!$isGroup) ? "N-" . JURI::base() . "firearms/find-gun-policy-facts?country=" . 
                                    urlencode(str_replace('&', ' and ', $location_info['location'])) . "&column=" . urlencode($column)
                                  : "N-" . JURI::base() . "firearms/find-gun-policy-facts?group=" . 
                                    $location_info['location_id'] . "&column=" . urlencode($column);

            //prepare tooltip text. do not show decimal points if ends with .00
            // prepare print $year value to display in tool tip with figure data
            preg_match("/^([1-2]{1}[0-9]{3}[:]{1})?/",$location_info['preamble'], $sentenceWithYear);
            
            if(empty($sentenceWithYear[0])) {
                preg_match("/^([1-2]{1}[0-9]{3}[-\/]{1}[\d]+:)?/",$location_info['preamble'], $sentenceWithYear);
            }
            $year = substr($sentenceWithYear[0],0,-1);
            $year = str_replace('-','/',$year);
            $year_tip = $year?' ('.$year.')':'';
            
            if(is_string($year) ) {
               $val = empty($showPercentageValues) ? $location_info[$column] : $location_info[$column].'%';
            }else {
               //$val = number_format($location_info[$column], 2);
                $val = ('en'==$this->currentLangCode) ? number_format($location_info[$column], 2) 
                       : $DPHelper->formatNumericValues_MB($location_info[$column]);
            }
            
            if ($pos = strrpos($val, '.00')) {
                $val = substr($val, 0, $pos);
            }
            
            $tooltext = htmlspecialchars($location_info[$this->locationString], ENT_QUOTES, 'UTF-8') . ': ' 
                        . $DPHelper->addThousandSeparator_MB($val) . $year_tip 
                        . JText::_('COM_GPO_CHARTS_HOVER_POPUP_BR');
            
            $chartData['data'][] = array('toolText' => "$tooltext",
                                         'link'     => "$outURl",
                                         'label'    => $location_info[$this->locationString],
                                         'value'    => $location_info[$column],
                                         'color'    => $this->random_color((($base_location_id == $location_info['location_id']) ? $base_location_id : ''))
                                   );
        }
  
        return $chartData;
    }
    

    function yearlyArrayToXml(&$yearly_data, $column_name = '', $location_id = '')
    {
        //asort($yearly_data, SORT_NUMERIC);
        $DPHelper  = new DatapageHelper();
        $location = $this->getLocationInfoBy('id',$location_id);
        $verticalAxisLabel = $this->getYAxisLabelByColumn($column_name);
       
        
        $numberFormatOptions = '';
        $chartData = array();
        
        if('fr' == $this->currentLangCode) {
           $numberFormatOptions = "decimalSeparator=',' thousandSeparator=' '";
           $chartData['chart']['decimalSeparator']  = ',';
           $chartData['chart']['thousandSeparator'] = ' ';
        }
        if('es' == $this->currentLangCode) {
           $numberFormatOptions = "decimalSeparator=',' thousandSeparator='.'";
           $chartData['chart']['decimalSeparator']  = ',';
           $chartData['chart']['thousandSeparator'] = '.';
        }
        
        $chartData['chart']['showToolTip']              = "1";
        $chartData['chart']['baseFontColor']            = '#000000';
        $chartData['chart']['valueFontColor']           = '#000000';
        $chartData['chart']['outCnvBaseFontSize']       = '11';

        $chartData['chart']['baseFont']                 = 'Helvetica Neue,Arial';
        $chartData['chart']['rotateValues']             = '1';
        $chartData['chart']['useRoundEdges']            = '1';
        $chartData['chart']['xAxisName']                = JText::_('COM_GPO_YEARLYCHARTS_XAXISNAME');
        $chartData['chart']['yAxisName']                = $verticalAxisLabel;
        
        $chartData['chart']['yAxisNameFontBold']        = "0";
        $chartData['chart']['xAxisNameFontBold']        = "0";
        
        $chartData['chart']['slantLabels']              = '1';
        $chartData['chart']['showNames']                = '1';
        $chartData['chart']['decimalPrecision']         = '0';
        $chartData['chart']['formatNumberScale']        = '0';
        $chartData['chart']['labelDisplay']             = 'Rotate';
        $chartData['chart']['setAdaptiveYMin']          = '1';
        $chartData['chart']['decimals']                 = '2';
        $chartData['chart']['yAxisValuesPadding']       = '10';
        $chartData['chart']['xAxisNamePadding']         = '20';
		
        $chartData['chart']['useEllipsesWhenOverflow']  = '0';
        $chartData['chart']['chartLeftMargin']          = '5';
        $chartData['chart']['maxLabelWidthPercent']     = '100';
        //$chartData['chart']['maxLabelHeight']           = '200';
        $chartData['chart']['canvasPadding']            = '10';
        $chartData['chart']['palette']                  = '1';
        $chartData['chart']['borderColor']              = '#000000';
        $chartData['chart']['borderThickness']          = '1';
        $chartData['chart']['lineColor']                = '#000000';	
        $chartData['chart']['bgColor']                  = '#ffffff';
        //$chartData['chart']['canvasBgColor']          = '#F7F7F7';
        $chartData['chart']['canvasBgRatio']            = '50,50';
        $chartData['chart']['canvasBgAlpha']            = '0';
        //$chartData['chart']['logoURL']                = JURI::root()."templates/gunpolicy/images/gp-watermark.png";
        $chartData['chart']['bgImage']                  = JURI::root()."templates/gunpolicy/images/gpo_watermark_chart2.png";
        $chartData['chart']["bgImageAlpha"]             = "100";
        //$chartData['chart']["bgImageDisplayMode"]     = "stretch";

        //$chartData['chart']['logoPosition']           = "CC";
        //$chartData['chart']['logoAlpha']              = "100";
        $chartData['chart']['showFCMenuItem']           = "0";
        $chartData['chart']['showPrintMenuItem']        = "0";
	$loggedInUser = & JFactory::getUser();
	if( !empty($loggedInUser->id) ) {
            $chartData['chart']['exportEnabled']        = "1";
            //$chartData['chart']['canvasTopMargin']    = "25";            
            //$chartData['chart']['toolBarHAlign']      = "RIGHT";            
            $chartData['chart']['toolBarVAlign']        = "TOP"; 		 
        }
        //$chartData['chart']['exportAtClientSide']     = "1";
        
        $chartData['chart']['lineThickness']            = '4';
        $chartData['chart']['paletteColors']            = '#0075c2';
        $chartData['chart']['captionFontSize']          = '14';
        $chartData['chart']['subcaptionFontSize']       = '14';
        $chartData['chart']['subcaptionFontBold']       = '0';
        $chartData['chart']['showBorder']               = '0';
        //$chartData['chart']['bgColor']                = '#F7F7F7';
        $chartData['chart']['showShadow']               = '1';
        //$chartData['chart']['canvasBgColor']          = '#ffffff';
	$chartData['chart']['showCanvasBorder']         = '1';
        $chartData['chart']['canvasBorderThickness']    = '2';
        $chartData['chart']['canvasBorderColor']        = '#000000';
        $chartData['chart']['canvasBorderAlpha']        = '50';
		
        $chartData['chart']['divlineAlpha']             = '100';
        $chartData['chart']['divlineColor']             = '#999999';
        $chartData['chart']['divlineThickness']         = '1';
        $chartData['chart']['divLineDashed']            = '1';
        $chartData['chart']['divLineDashLen']           = '1';
        $chartData['chart']['divLineGapLen']            = '1';
        $chartData['chart']['showXAxisLine']            = '0';
        $chartData['chart']['xAxisLineThickness']       = '1';
        $chartData['chart']['xAxisLineColor']           = '#999999';
        $chartData['chart']['showAlternateHGridColor']  = '1';
 
/*
        //Converting String to number in php 
        //Finding max min
        $max = 0.00;
        $min = 999999999.00;
        foreach($yearly_data as $year=> $data){
              
             if(is_numeric($data)){
                    $data = str_replace(',', '', $data);
             }
             $val = number_format($data,2);
             if($val>$max){
                 $max = $val;
             }
             if($val<$min){
                 $min = $val;
             }
        } 
        
            $max = str_replace(',', '', $max);
            $min = str_replace(',', '', $min);
            $diff = number_format(round($max - $min,1),2);
            $diff = str_replace(',', '', $diff);
        
        if($max>10.0){   
              $ymax = (ceil($diff/5) + $max);
           }
        else
           {
            $ymax = (($diff/5) + $max);
        }
        //$chartData['chart']['yAxisMaxValue']  = $ymax;
 * 
 */
       
        foreach ($yearly_data as $year => $data) {
            $outURl = "N-" . JURI::base() . "firearms/find-gun-policy-facts?country=" . urlencode(str_replace('&', ' and ', $location->name)) . "&column=" . urlencode($column_name);

            //prepare tooltip text. do not show decimal points if ends with .00
            $val = ('en'==$this->currentLangCode) ? number_format($data, 2) 
                                                  : $DPHelper->formatNumericValues_MB($data);
            if (!empty($pos = strrpos($val,'.00'))) {
                $val = substr($val, 0, $pos);
            }
            
            $tooltext = htmlspecialchars($year, ENT_QUOTES, 'UTF-8') . ': ' . $DPHelper->addThousandSeparator_MB($val) . ' ' . JText::_('COM_GPO_CHARTS_HOVER_POPUP_BR');
            $chartData['data'][] = array(
                                         "label"    => htmlspecialchars($year, ENT_QUOTES),
                                         "value"    => $data,
                                         "color"    => "#E85D00",
                                         "tooltext" => $tooltext,
                                         "link"     => $outURl
                                   );
        }
        
        return $chartData;
    }

    function getYearlyData($location_id, $column_name) {
        $this->_db->setQuery("SELECT `$column_name` FROM `#__gpo_datapages` WHERE `location_id`=".$this->_db->Quote($location_id));
        $row = $this->_db->loadObject();
        
        $dataValue = preg_replace("/<!(.*?)!>/s", "", $row->$column_name);
        //split the data by semicolon (;)
        $years = explode(';', $dataValue);
        $yearly_data = array();
        $num_years = 0;
        foreach($years AS $year){
            $year = explode(':', $year);
            if(!empty($year[0]) AND !empty($year[1])){
                $yearly_data[trim($year[0])] = trim($this->clean_column_data($year[1], $column_name));
                $num_years++;

                if($num_years>=30) break;
            }
        }
        ksort($yearly_data);
        

        return $this->yearlyArrayToXml($yearly_data, $column_name, $location_id);

    }
    
    
    function getGroupYearlyData($location_id, $column_name) {
        
        $query = "SELECT 
                             `$this->DPTable`.`location_id` as `loc_id`, 
                             `$this->DPTable`.`location` as `loc_name`, 
                             `$this->DPTable`.`$column_name`, 
                             `#__gpo_location_to_groups`.`group_id`,
                             `#__gpo_location_to_groups`.`group_id` as `location_id`,
                             `#__gpo_groups`.`name` as `location` 
                      FROM 
                             `$this->DPTable` 
                      INNER JOIN 
                             `#__gpo_location_to_groups` ON `$this->DPTable`.location_id = `#__gpo_location_to_groups`.location_id 
                      INNER JOIN 
                             `#__gpo_groups` ON `#__gpo_groups`.`id` = `#__gpo_location_to_groups`.`group_id` 
                      WHERE 
                             `#__gpo_location_to_groups`.`group_id` = " . $this->_db->quote($location_id) . " LIMIT 500";
        
        $this->_db->setQuery($query);
        
        $rows = $this->_db->loadObjectList();
        $results = $this->processGroupDPData($rows, $column_name);
        
        //split the data by semicolon (;)
        $years = explode(';', $results[$column_name]);
        $yearly_data = array();
        $num_years = 0;
        foreach($years AS $year) {
            $year = explode(':', $year);
            if(!empty($year[0]) AND !empty($year[1])) {
                $yearly_data[trim($year[0])] = trim($this->clean_column_data($year[1], $column_name));
                $num_years++;

                if($num_years>=25) break;
            }
        }
        
        ksort($yearly_data);
        return $this->yearlyArrayToXml($yearly_data, $column_name, $location_id);

    }
    
    
    function getRegionYearlyData($location_id, $column_name) {
        
        JLoader::import( 'region', JPATH_BASE . DS . 'components' . DS . 'com_gpo' . DS . 'models' );
        $regionModel = JModelLegacy::getInstance('GpoModelRegion');
        
        //get all region locations
        $regionLocations = $regionModel->getAllLocationsByRegion($location_id);
        $regionLocationsInQuery = implode(',', $regionLocations);
        
        $query = "SELECT 
                        `$this->DPTable`.`location_id` as `loc_id`,
                        `$this->DPTable`.`location` as `loc_name`,
                        `$this->DPTable`.`$column_name`,
                        `#__gpo_location`.`id`,
                        `#__gpo_location`.`id` as `location_id`,
                        `#__gpo_location`.`name` as `location`
                  FROM 
                        `$this->DPTable`
                  INNER JOIN 
                        `#__gpo_location` ON `$this->DPTable`.location_id = `#__gpo_location`.id 
                  WHERE 
                        `#__gpo_location`.`id` IN(" . $regionLocationsInQuery . ") LIMIT 500";
        
        $this->_db->setQuery($query);
        
        $rows = $this->_db->loadObjectList();
        $results = $this->processGroupDPData($rows, $column_name, $location_id);
        
        //split the data by semicolon (;)
        $years = explode(';', $results[$column_name]);
        $yearly_data = array();
        $num_years = 0;
        foreach($years AS $year) {
            $year = explode(':', $year);
            if(!empty($year[0]) AND !empty($year[1])) {
                $yearly_data[trim($year[0])] = trim($this->clean_column_data($year[1], $column_name));
                $num_years++;

                if($num_years>=25) break;
            }
        }
        
        ksort($yearly_data);
        return $this->yearlyArrayToXml($yearly_data, $column_name, $location_id);

    }
    
    
    function getGroupLastModifiedDate($groupId)
    {
       if( empty($groupId) ) {
            return date('Y-m-d H:i:s');
       }
        
       $db = &JFactory::getDBO();
       
       $query =  "SELECT 
                         MAX(`$this->DPTable`.updated_at) as `updated_at`
                  FROM 
                         `$this->DPTable` 
                  INNER JOIN 
                         `#__gpo_location_to_groups` ON `$this->DPTable`.location_id = `#__gpo_location_to_groups`.location_id 
                  INNER JOIN 
                         `#__gpo_groups` ON `#__gpo_groups`.`id` = `#__gpo_location_to_groups`.`group_id` 
                  WHERE 
                         `#__gpo_location_to_groups`.`group_id` = " .  $db->quote($groupId);
       
       $db->setQuery($query);
       $timestamp = $db->loadObject();
       $modified = $timestamp->updated_at;
       
       //for empty date, show today's date
       if(empty($modified) || '0000-00-00 00:00:00' == $modified) {
          $modified = date('Y-m-d H:i:s');
       }
       return $modified;
    }
    
    
    function getRegionLastModifiedDate($regionId)
    {
        if (empty($regionId)) {
            return date('Y-m-d H:i:s');
        }

        $db = &JFactory::getDBO();

        JLoader::import('region', JPATH_BASE . DS . 'components' . DS . 'com_gpo' . DS . 'models');
        $regionModel = JModelLegacy::getInstance('GpoModelRegion');

        //get all region locations
        $regionLocations = $regionModel->getAllLocationsByRegion($regionId);
        $regionLocationsInQuery = implode(',', $regionLocations);

        $query = "SELECT 
                        MAX(`$this->DPTable`.`updated_at`) as `updated_at` 
                  FROM 
                        `$this->DPTable` 
                  INNER JOIN 
                        `#__gpo_location` ON `$this->DPTable`.location_id = `#__gpo_location`.id 
                  WHERE 
                        `#__gpo_location`.`id` IN(" . $regionLocationsInQuery . ")";

        $db->setQuery($query);
        $timestamp = $db->loadObject();
        $modified  = $timestamp->updated_at;

        //for empty date, show today's date
        if (empty($modified) || '0000-00-00 00:00:00' == $modified) {
            $modified = date('Y-m-d H:i:s');
        }

        return $modified;
    }
    
    
    function getChartFooterInfo($base_location_info, $locationType=NULL, $columnInfo=NULL)
    {
        require_once(JPATH_BASE . '/components/com_gpo/helpers/footer_helper.php');
        $footer = new StdClass();
        $params = new stdClass();
        $dp = new DatapageHelper();
        
        //get the title
        $footer->title = $dp->getChartFooterLocationTitle($base_location_info->name, 
                                                          $locationType, 
                                                          $columnInfo->column_title
                         );
        
        //get authors name for footer
        if( !empty($base_location_info->alias) ) {
            $locationURI = '/firearms/region/' . $base_location_info->alias;
            $params->requestURI = $locationURI;
        }
        $footer->authors = footerhelper::getArticleAuthors($params);

        if('group' == $locationType) {
           $footer->modified = $this->getGroupLastModifiedDate($base_location_info->id);
        }elseif('region' == $locationType) {
           $footer->modified = $this->getRegionLastModifiedDate($base_location_info->id);
        }else {
           $footer->modified = footerhelper::getLastModifiedDate($base_location_info->id);    
        }

        return $footer;
    }
}
