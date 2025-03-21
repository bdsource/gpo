<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 use Joomla\CMS\Uri\Uri;
 
class DatapageHelper
{

    var $regionAlias;
    var $regionInfo;
    
    ##DP Tables##
    private $currentLang;
    private $otherLangs = array('es','fr');
    private $tableSeparator       = '_';
    private $languagePartURI = '';
    private $currentEnLocationName = '';
  
    private $DPTable              = '#__gpo_datapages';
    private $DPPreambleTable      = '#__gpo_datapage_preamble_values';
    private $DPMasterListTable    = '#__gpo_preambles_switches_master_list';
    private $DPHierarchyTable     = '#__gpo_datapage_hierarchy';
    var $getPrefix                = array();

    //this will hold the column names having multiple years data
    var $multiYearColumns = array();
    var $haveCustomTags   = array();
    
    function __construct($currentLang = 'en') {
        $lang = JFactory::getLanguage();
        $langLocales = $lang->getLocale();
        $langName = $lang->getName();
        $langTag = $lang->getTag();

        if (strlen($langTag) > 2) {
            $langCode = strtolower(substr($langTag, 0, -3));
        }

        $this->currentLang = !empty($langCode) ? strtolower($langCode) : strtolower($currentLang);
        if (in_array($this->currentLang, $this->otherLangs)) {
            $this->languagePartURI = $this->currentLang . '/';
        }
        
        $this->getPrefix = $this->getAllLocationPrefixes(); //load all prefix list

        $this->_initializeTableNames();
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
     * initialize DP table names according to lang switch
     * 
     */   
    function _initializeTableNames() {
        
        $this->DPTable = $this->_makeTableName($this->DPTable);
        $this->DPPreambleTable = $this->_makeTableName($this->DPPreambleTable);
    }
	
    
    function getDPByLocation($p_location_name, $ignorePublishFlag=FALSE)
    {
        $db = &JFactory::getDBO();
        if (empty($p_location_name)) {
            return false;
        }

        $sql_add = Gpo_location_display_sql('Public Backend');
        $query = "SELECT
		               `$this->DPPreambleTable`.*, `$this->DPTable`.*
		          FROM 
		              `$this->DPTable` 
		          LEFT JOIN 
		              `$this->DPPreambleTable` 
		          ON
		              `$this->DPTable`.`location_id` = `$this->DPPreambleTable`.`location_id`
				  LEFT JOIN 
				      `#__gpo_location` as `lo`
				  ON
				      `lo`.`id` = `$this->DPTable`.`location_id`
		          WHERE
					  `lo`.`name` = " . $db->quote($p_location_name);
        
        if(!$ignorePublishFlag) {
                  $query .= ' AND ' .
                            "`$this->DPTable`.`published` = 1" 
                            . $sql_add;
        }
        
        $db->setQuery($query);
        $data = $db->loadObject();

        if (empty($data->id)) {
            return false;
        }
        return $data;
    }


    /*
    *
    *  get all column names of the gpo_datapages table
    *
    */

    function getDPMetaDataInfo()
    {
        $db = &JFactory::getDBO();
        $query = "SHOW  COLUMNS 
	          FROM 
	          `$this->DPTable`";

        $db->setQuery($query);
       $data = $db->loadColumn();
        return $data;
    }


    function getDPColumnsInfo() {
        $db = &JFactory::getDBO();
        $query = "SELECT * FROM `$this->DPHierarchyTable`";
        $db->setQuery($query);
        $data = $db->loadObjectList();
        $columns_list = array();
        foreach ($data as $row) {
            $columns_list[$row->column_name] = $row;
        }
        return $columns_list;
    }
    
    
    function isDisplayOff($regionAggregationType=NULL) {
        
          if( 'off' == trim($regionAggregationType) ) {
               return true;
          }
          
          return false;
    }
    
    function isShowAsAverage($regionAggregationType=NULL) {
          if( 'average' == trim($regionAggregationType) ) {
              return true;
          }
          
          return false;
    }
    
    /*
    *
    * Don't show these field names & the information
    * in the datapage frontend display
    *
    */

    function ignoreField($p_field_name)
    {
        $ignoreList = array(
            'id',
            'location_id',
            'location',
            'published',
            'created_at',
            'updated_at',
            'published_at'
        );
        return in_array($p_field_name, $ignoreList);
    }

    
    function parameterize_array($array, $addDecimalZero=TRUE, $glue=": ") {
        $out = array();
        krsort($array);
        foreach ($array as $key => $value) {
            $out[] = $key . $glue . ($addDecimalZero ? number_format($value,1) : $this->addThousandSeparator($value));
        }
        return $out;
    }
    
    
    function parameterize_array_mb($array, $addDecimalZero=TRUE, $glue=": ") {
        $out = array();
        krsort($array);
        foreach ($array as $key => $value) {
            //$out[] = $key . $glue . ($addDecimalZero ? number_format($value,1) : $this->addThousandSeparator_MB($value));
            //$out[] = $key . $glue . $this->addThousandSeparator_MB($value);
            $out[] = $key . $glue .$value;
        }
        return $out;
    }
    
    function calculateAverage($array, $totalLocations) {
        
        if(empty($array) || empty($totalLocations)) {
            return $array;
        }
        
        foreach ($array as &$value) {
            $value = ($value / $totalLocations);
        }
        
        unset($value);
        return $array;
    }
    
    
    function formatValues($cumulativeValue, $glue=": ") {
        $out = array();
        
        if (strpos($cumulativeValue, ';') !== false) {
            $multiyears = explode(';', rtrim($cumulativeValue, '; '));
        }
        
        foreach ($multiyears as $key => $yearval) {
            list($year, $yearValue) = array_map('trim', explode(':', $yearval));
            
            if (strpos($yearValue, '.') !== false) {
               $haveAnyDecimalValue = true;
               break;
            }
        }
        
        krsort($multiyears);
        foreach ($multiyears as $key => $value) {
            $out[] = $key . $glue . ($haveAnyDecimalValue ? number_format($value,1) : $this->addThousandSeparator($value));
        }

        return implode(';',$out);
    }
    
    function addMultipleYearValues($oldValue, $newValue) {
        
        if( empty($newValue) ) {
            return $oldValue;
        }
        
        $pattern = '/\{([a-z][0-9]{1,11})\}/';

        $numValueOld = preg_split($pattern, $oldValue);
        $numValueNew = preg_split($pattern, $newValue);
        
        $oldNumericValue = $numValueOld[0];
        $newNumericValue = $this->santitizeNumericValues($numValueNew[0]);
        
        $totalValue = $oldNumericValue + $newNumericValue;
        $citations = '';
        
        preg_match_all($pattern, $oldValue, $matchesOld);
        preg_match_all($pattern, $newValue, $matchesNew);
        
        foreach($matchesOld[0] as $key => $val) {
            $citations .= $val;
        }
        foreach($matchesNew[0] as $key => $val) {
            $citations .= $val;
        }
        
        //return $totalValue . $citations;
        
        //we don't need citations stirng        
        return $totalValue;
    }
    
    
    /*
     * It will remove the citation string from 
     * a data value.
     * 
     */
    function removeCitations($dataValue) 
    {
        
        if( empty($dataValue) ) {
            return $dataValue;
        }
        
        $pattern = '/\{([a-z][0-9]{1,11})\}/';
        $numValue = preg_split($pattern, $dataValue);
        
        return $numValue;
    }
    
    
     /*
     * It will remove the citation string from 
     * a data value.
     * 
     */
    function santitizeNumericValues($dataValue) 
    {
        
        if( empty($dataValue) ) {
            return $dataValue;
        }
        
        $pattern = '/\{([a-z][0-9]{1,11})\}/';
        $dataValueSplit = preg_split($pattern, $dataValue);
        $dataNumericValue = $dataValueSplit[0];
        
        if(preg_match("/^[0-9,]+$/", $dataNumericValue)) {
            $dataNumericValue = str_replace(',', '', $dataNumericValue);
            $dataNumericValue = str_replace(',', '', $dataNumericValue);
        }
        
        return floatval($dataNumericValue);
    }
    
    
     /*
      * 
      * It will remove the citation string from 
      * a data value.
      * 
      */
    function formatNumericValues_MB($dataValue) 
    {
        
        if( empty($dataValue) ) {
            return $dataValue;
        }
        
        ##For english we actually don't need to go thorugh this process
        if( $this->currentLang == 'en') {
            return $dataValue;
        }

        ##Tokenize by space
        $eachWords = explode(' ', $dataValue);
        $formattedData = '';
        $disableConversion = false;
        foreach ($eachWords as $key => $val) {
            $posCitation = strpos($val, '{');
            $disableConversion = false;
            
            if ($posCitation !== false) {
                $dataNumericValue = substr($val, 0, $posCitation);
                $dataCitation = substr($val, $posCitation);
            } else {
                $dataNumericValue = $val;
                $dataCitation = '';
            }

            $dataNumericValue = str_replace('^', "", $dataNumericValue );
            
            ##having fullstop at the end of the number
            
            if (preg_match("/^[0-9,^']+$/", $dataNumericValue)) {

                ## Avoid year values from adding thousand separator
                ## We assume 1998 or 1998, are both year values
                $comaPos     = strpos($dataNumericValue, ',');
                $dataLen     = strlen($dataNumericValue);
              
                if( strpos($dataNumericValue, ',') === false || ($comaPos == $dataLen-1) ) {
                    $disableConversion = true;
                }
                
                if( $disableConversion !== true ) {
                    $dataNumericValue = str_replace( array(',',',','^',"'"), "", $dataNumericValue );
                }
            }
            
            ## This segment is for identifying FullStop, not precision indicator
            if (preg_match("/^[0-9,.^']+$/", $dataNumericValue))
            {
                $fullstopPos = strpos($dataNumericValue, '.');
                $comaPos     = strpos($dataNumericValue, ',');
                $dataLen     = strlen($dataNumericValue);
                if( $fullstopPos == $dataLen-1 && false === $comaPos ) {
                    $disableConversion = true;
                }
                if( $disableConversion !== true ) {
                    $dataNumericValue = str_replace( array(',',',','^',"'"), "", $dataNumericValue );
                }
            }
           
            if (is_numeric($dataNumericValue) && $disableConversion === false ) {
                $formattedData .= $this->addThousandSeparator_MB($dataNumericValue) . $dataCitation . " ";
            } else {
                $formattedData .= $dataNumericValue . $dataCitation . " ";
            }
        }

        return trim($formattedData);
    }
    
    
    /*
     * 
     * Thousand separator for numbers
     * 56234 will be converted to 56,234
     * 
     */
    function addThousandSeparator($number) {
        
        if( empty($number) ) {
            return $number;
        }
        
        return implode(",", preg_split("/(?<=\d)(?=(\d{3})+$)/", $number));
    }
    
    
    /*
     * 
     * Thousand separator for numbers
     * 56234 will be converted to 56,234
     * 
     */
    function addThousandSeparator_MB($number) {
        
        if( empty($number) ) {
            return $number;
        }
        
        if ( !is_numeric($number) ) {
            return $number;
        }
        
        if( $this->currentLang == 'es' ) {
            $locale = 'es-AR'; //'es-ES';
        }else if( $this->currentLang == 'fr' ) {
            $locale = 'fr-FR';
        }else {
            $locale = 'en';
        }

        if ($this->currentLang != 'en') {
           //$fmt  = numfmt_create($locale, NumberFormatter::DECIMAL);
           //$data = numfmt_format($fmt, $number);
           $fmt = new \NumberFormatter("$locale", \NumberFormatter::DECIMAL);
           
           ##Get Max Fraction digit
           $fractionPosition = strpos($number, '.');
           if( $fractionPosition !== false ) {
               $precisionLength = strlen( substr($number, $fractionPosition+1) );
               $fmt->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $precisionLength);
               //$fmt->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 2); 
           }
           $data = $fmt->format($number);
            
        } else {
            $data = implode(",", preg_split("/(?<=\d)(?=(\d{3})+$)/", $number));
        }
        
        return $data;
    }
    
    
     /*
      * 
      * It will remove the coma(,) from year values
      * example 19,97 will be changed to 1997
      * 19,96 will be 1996
      * 
      */
    function sanitizeYearValues($year) 
    {
        if( empty($year) ) {
            return $year;
        }
        
        //if(preg_match("/^[0-9,]+$/", $year)) {
        $sanitizedYear = trim(str_replace(',', '', $year));
        //}
        
        return $sanitizedYear;
    }
    
    
    /*
    *
    * case insensitive search in array elements
    *
    */

    function in_arrayi($needle, $haystack)
    {
        $needle = strtolower($needle);
        foreach ($haystack as $value)
        {
            if (strtolower($value) == $needle) {
                return true;
            }
        }
        return false;
    }


    /*
    *
    * Add an extra the before the country or location
    * name fro the following cases; needed for DP browser
    * title and/or for the DP preambles
    *
    *
    */

    function isNeedToAddThe($p_location_name, $langSpecific=false, $preamble=NULL)
    {
        
        $addPrefix = ''; //initially no prefix
        if (empty($p_location_name)) {
            return '';
        }
        
        /*     
        $addTheArray_en = array(
            'Australian Capital Territory',
            'Bahamas',
            'Caribbean',
            'Cayman Islands',
            'Congo (DRC)',
            'Congo (ROC)',
            'Cook Islands',
            'Czech Republic',
            'District of Columbia',
            'Dominican Republic',
            'Faeroe Islands',
            'Falkland Islands',
            'Maldives',
            'Marshall Islands',
            'Netherlands',
            'Netherlands Antilles',
            'Northern Mariana Islands',
            'Northern Territory',
            'Philippines',
            'Solomon Islands',
            'United Arab Emirates',
            'United Kingdom',
            'United Nations',
            'United States',
            'Vatican',
            'Virgin Islands (UK)',
            'Virgin Islands (US)',
            'European Union'
        );
        */
       
        ## get the required prefix based on lanugage ##
            if ($langSpecific === false ) {
                    $addPrefix = $this->getPrefix['en'][$p_location_name]; //default english
            }
            else if( in_array($this->currentLang, $this->otherLangs) ) {
                    $addPrefix = $this->getPrefix[$this->currentLang][$p_location_name]; //give translation for fr/es
            } else {
                    $addPrefix = $this->getPrefix['en'][$p_location_name]; //give prefix for en
            }
        
        ## Now check preamble whether we need to make the prefix camel case ##
        if( $preamble ) {
            $hashPosition = strpos(trim($preamble), '#');
            if( $hashPosition == 0 || $hashPosition < 2 ) {
                
                if( 'en' == $this->currentLang ) {
                    ## hash found in the beginning of the preamble so use uppercase prefix 
                    $addPrefix = ucfirst($addPrefix);
                } else {
                    ## In French or Spanish, whenever a location name starts a preamble and is 
                    ## immediately followed by a semi colon, there is no need for a prefix.
                    $addPrefix = '';
                }
                
            }
        }
        //echo $preamble . " ==== " . $addPrefix . '<br>';
        return $addPrefix;
    }


    function camelize($str)
    {
        $str = 'x' . strtolower(trim($str));
        $str = ucwords(preg_replace('/[\s_]+/', ' ', $str));
        $str = ucfirst(substr($str, 1));
        $str = str_replace(array('Unpoa', 'Public Hidden Or Openly', 'Any Method', 'Ak-47'),
                           array('UNPoA', 'Public, Hidden or Openly', '(any method)', 'AK-47'),
                           $str);

        /* replace prepositions & articles */
        $explode = explode(' ', $str);
        $ap_array = array('Of', 'And', 'From', 'On', 'In', 'For', 'To', 'An', 'At', 'A', 'An', 'The');
        $ap_replace_array = array('of', 'and', 'from', 'on', 'in', 'for', 'to', 'an', 'at', 'a', 'an', 'the');
        foreach ($explode as $key => $val) {
            if (in_array($val, $ap_array)) {
                $explode[$key] = str_replace($ap_array, $ap_replace_array, $val);
            }
        }
        $str = implode(' ', $explode);

        return $str;
    }


    function getDPColumnTitles()
    {
        $db    = &JFactory::getDBO();
        $query = "SELECT * FROM `$this->DPHierarchyTable`";
        $db->setQuery($query);
        $data  = $db->loadObjectList();
        
        $defaultColumnTitle = 'column_title';
        if( in_array($this->currentLang, $this->otherLangs) ) {
           $defaultColumnTitle .= '_' . strtolower($this->currentLang);  
        }
        
        $columns_titles = array();
        foreach ($data as $row) {
            $column_title = empty($row->column_title) ? $this->camelize($row->column_name) : $row->column_title;
            $columns_titles[$row->column_name.'_en'] = $column_title;
            $columns_titles[$row->column_name.'_es'] = empty($row->column_title_es) ? $column_title : $row->column_title_es;
            $columns_titles[$row->column_name.'_fr'] = empty($row->column_title_fr) ? $column_title : $row->column_title_fr;
            $columns_titles[$row->column_name] = empty($row->{$defaultColumnTitle}) ? $column_title : $row->{$defaultColumnTitle};
        }
        
        return $columns_titles;
    }
    

    /*
     * 
     * TO Check if a column/category is 
     * a external link type category
     * 
     */
    function isALinkCategory($columnName)
    {
      if( empty($columnName) ) {
          return false;
      }
      
   	  $db = &JFactory::getDBO();
   	  $query = "SELECT * FROM `$this->DPHierarchyTable` WHERE `column_name` = " . $db->quote($columnName) . 
               " AND (external_hyperlink_name != '' OR external_hyperlink_name IS NOT NULL)";
   	  $db->setQuery( $query );
      $data = $db->loadObject();
      
      if( $data ) {
          return $data;
      }
      
   	  return false;
    }
    

    function getHTML($p_field, $p_val, $p_preamble, $linkCatVal = '', $p_location_name='')
    {
        $html = '';
        $timeSeriesBR = '<br><br>';
        
        ##replace tags (<>)
        if(in_array($p_field,$this->haveCustomTags)) {
           $timeSeriesBR = ''; 
        }
        
        if (strpos($p_val, ';') !== false) {
            $p_val = $timeSeriesBR . str_replace(';', '<br>', $p_val);
        }else if (strpos($p_val, ':') !== false) {
            $p_val = $timeSeriesBR . str_replace(';', '<br>', $p_val);
        }
        
        ##replace the # with location name
        if (!empty($p_location_name)) {
            $addPrefix   = $this->isNeedToAddThe($p_location_name, true, $p_preamble);
            $displayName = ($addPrefix) ? "$addPrefix $p_location_name" : $p_location_name;
            //$p_preamble_before = $p_preamble;
            $p_preamble  = str_replace('#', $displayName, $p_preamble);
            //echo "f=<b>$p_field</b> / prefix=$addPrefix / dsname=$displayName / preamble-before=$p_preamble / preamble=$p_preamble <br>";
        }

        ##replace external src link, applicable for category links
        //$linkCategory = $this->isALinkCategory($p_field);
        if ( !empty($linkCatVal['external_hyperlink_name']) ) {
            if ($linkCategory !== false) {
                $linkPreamble = str_replace('~', $linkCatVal['external_hyperlink_name'], $p_preamble);
                $html .= $linkPreamble;
                return $html;
            }
        }
        
        
        if (strpos($p_preamble, '~') !== false) {
            $html .= str_replace('~', $p_val, $p_preamble);
        }
        else
        {
            $html .= $p_preamble . ' ' . $p_val;
        }
        
        ##replace the caret sign used for denoting data value in a mix of text
        $html = str_replace('^','',$html);
        ##replace the Hash sign used for yearlycompare value
        $html = str_replace('#','',$html);
        return stripslashes($html);
    }


    function getDPBrowserTitle($p_location_name, $p_add_suffix = TRUE)
    {
        if (empty($p_location_name)) {
            return false;
        }
        $addPrefix   = $this->isNeedToAddThe($p_location_name);
        $displayName = ($addPrefix) ? "$addPrefix $p_location_name" : $p_location_name;
        $browserTitle = JText::_('Guns in ') . $displayName;
        if ($p_add_suffix) {
            $browserTitle .= ': ' . JText::_('Facts, Figures and Firearm Law');
        }
        return $browserTitle;
    }
    
    function getDPPageTitle($p_location_name) 
    {
        ##replace the # with location name
        $DPTitle = str_replace('#', $p_location_name, JText::_('COM_GPO_DP_TITLE'));
        
        return $DPTitle;
    }
    
    function getDPMetaTitle($p_location_name, $p_add_suffix = TRUE, $p_other_name='')
    {
        if (empty($p_location_name)) {
            return false;
        }
        
        $addPrefix   = $this->isNeedToAddThe($p_location_name);
        $displayName = ($addPrefix) ? "$addPrefix $p_location_name" : $p_location_name;
        
        if( in_array($this->currentLang,$this->otherLangs) ) {
            $displayName = $p_other_name;
        }
        
        $browserTitleInitial = JText::_('COM_GPO_DP_TITLE_INITIAL');
        $browserTitleLast    = JText::_('COM_GPO_DP_TITLE_LAST');
        
        $browserTitle = str_replace('#', $displayName, $browserTitleInitial);
        if ($p_add_suffix) {
            $browserTitle .= $browserTitleLast;
        }
        
        return $browserTitle;
    }
    

    function getChartFooterLocationTitle($p_location_name, $locationType=NULL, $p_add_suffix = '')
    {
        if (empty($p_location_name)) {
            return false;
        }
        $addPrefix = $this->isNeedToAddThe($p_location_name);
        $displayName = ($addPrefix) ? "$addPrefix $p_location_name" : $p_location_name;
        if( 'group' == $locationType || 'region' == $locationType ) {
           $displayName = 'the ' . $p_location_name . " $locationType"; 
        }
        $browserTitle = JText::_('Guns in ') . $displayName;
        if ($p_add_suffix) {
            //$browserTitle .= ': ' . JText::_('Conflict prevention, development, peace and security');
            $browserTitle .= ': ' . $p_add_suffix;
        }
        
        return $browserTitle;
    }



    /*
    *
    * javascript event hander to conrtrol
    * expand, collapse functionality of DP Tree
    *
    */
    function getDPJs($type = 'both')
    { //die("111");
        $document = &JFactory::getDocument();
        $jsUrl = JURI::base() . 'templates/gunpolicy/javascript/datapage.js?v=4.0';
        $stylesheetUrl = JURI::base() . 'templates/gunpolicy/css/dpstyles.css?v=2';
        if ('both' == $type) {
            //JHTML::_("behavior.mootools");
            //JHtml::_('behavior.framework');
            $document->addScript('/media/vendor/mootools/MooTools-Core-1.6.0.js');
            $document->addScript($jsUrl);
            $document->addStyleSheet($stylesheetUrl, 'text/css', "screen");
        }
        else if ('stylesheet' == $type) {
            //die('yyy');
            //print_r($stylesheetUrl);die();
            $document->addStyleSheet($stylesheetUrl);
        }
        else if ('js' == $type) {
            JHTML::_("behavior.mootools");
            $document->addScript($jsUrl);
        }
    }


    /*
    * checks whether a top level header has any data
    * to show in its child nodes.
    *
    */

    function isEmptyDPNode($p_index, $p_dp_tree, $p_data_html)
    { //die("2");
        $status = TRUE;
        foreach ($p_dp_tree['level1'][$p_index] as $key => $val) {
            if (!empty($p_data_html[$val])) {
                $status = FALSE;
                break;
            }
            foreach ($p_dp_tree['level2'][$p_index][$val] as $v) {
                if (!empty($p_data_html[$v])) {
                    $status = FALSE;
                    break;
                }
            }
            if (FALSE === $status) {
                break;
            }
        }
        return $status;
    }


    /*
    * will return the location/DP sub-header for a DP
    */

    function getLocationSubHeader($metaData)
    {


    }

    /*
    * Get HTML for the DP or CP page tabs
    *
    */
    function getDPTabs($p_active_tab, $p_other_url)
    {

        $p_active_tab = strtolower($p_active_tab);
        $cp_class = ('cp' == $p_active_tab) ? 'tab active' : 'tab';
        $dp_class = ('dp' == $p_active_tab) ? 'tab active' : 'tab';

        $cp_url = ('cp' == $p_active_tab) ? '#' : $p_other_url;
        $dp_url = ('dp' == $p_active_tab) ? '#' : $p_other_url;

        $DPHeaders = '<div id="cptabbar dejan">
   	                   <a href="' . $dp_url . '" class="' . $dp_class . '">Gun Facts</a>
                       <a href="' . $cp_url . '" class="' . $cp_class . '">Full Article</a>
                   </div>';
        return $DPHeaders;
    }

    function getDPGCiteIds(){
        $db = &JFactory::getDBO();
        $db->setQuery("SELECT `column_name`, `gcite_id` FROM `#__gpo_datapage_hierarchy`");
        $results = $db->loadObjectList();
        $gcites_id =array();
        foreach($results AS $row){
            $gcites_id[$row->column_name] = $row->gcite_id;
        }
        return $gcites_id;
    }

    function getUSJurisdictions() {
        $db = &JFactory::getDBO();
        $db->setQuery("SELECT 
                              `lo`.`name`
                       FROM 
                              `#__gpo_location` AS `lo`
                       INNER JOIN 
                              `#__gpo_location_links` AS `link` ON `lo`.`id`=`link`.`link_id`
                       WHERE 
                             `lo`.`type` = 'jurisdiction' 
                       AND 
                             `link`.`location_id`=(SELECT `id` FROM `#__gpo_location` WHERE `name` = 'United States')                   
                     ");
        $result = $db->loadAssocList();
        $locations = array();
        foreach ($result as $location) {
            $locations[]= trim($location['name']) ;
        }
        //$locaiton_list = implode(',', $locations);

        return $locations;
    }

    
    ##########
    ###  Get prefixes by locations 
    ##########
    function getAllLocationPrefixes() {
        $db = JFactory::getDBO();
	$query  = "SELECT `id`,`prefix`,`name`,`prefix_es`,`name_es`,`prefix_fr`,`name_fr`,`type`,`display` FROM `#__gpo_location`";
	$query .= " ORDER BY `name` ASC ";

	$db->setQuery($query);
	$data = $db->loadAssocList();
        
        $getPrefix = array();
        foreach ($data as $location) {
            $getPrefix['en'][trim($location['name'])]    = trim($location['prefix']);
            $getPrefix['es'][trim($location['name_es'])] = trim($location['prefix_es']);
            $getPrefix['fr'][trim($location['name_fr'])] = trim($location['prefix_fr']);
        }
        
	return $getPrefix;
    }
    
    function callback_tagreplace($m) {
        static $id = 0;
        global $matchesTags;
        $id++;
        $matchesTags[$id] = $m[1];
        return $id . '@@@###';
    }
    
    
    function processDataValue( $dataValue, $key='' )
    {
        
        if( empty($dataValue) ) {
            return $dataValue;
        }
        
        ##No need to process tags?
        if (strpos($dataValue, '!>') === false) {
            $formattedValue = $this->formatNumericValues_MB($dataValue);
            //echo $dataValue . ' >>> ' . $formattedValue . '<br>';
            return $formattedValue;
        }
        
        ##Now Porcess tags
        global $matchesTags;
        $matchesTags = array();
        $this->haveCustomTags[] = $key;
        $fullData    = $dataValue;
        $numericData = preg_replace("/<!(.*?)!>/s", "", $fullData);
        $fullDataTemplate = preg_replace_callback("/<!(.*?)!>/s", array($this,'callback_tagreplace'), $fullData);
        $fullDataTemplate = str_replace($numericData, '!DATA!', $fullDataTemplate);
        
        foreach ($matchesTags as $mk => $mv) {
           $fullDataTemplate = str_replace($mk . '@@@###', $mv . ' ', $fullDataTemplate);
        }
        
        $formattedValue = $this->formatNumericValues_MB($numericData);
        $fullDataTemplate = str_replace('!DATA!', '<br><br>'.$formattedValue.' ', $fullDataTemplate);

        //echo $dataValue . ' >>> ' . $fullDataTemplate . '<br>';
        return $fullDataTemplate;
    }
    
    
    function processMultiyearDataValue($dataValue, $key='') 
    {
        
        if( empty($dataValue) ) 
        {
            return $dataValue;
        }
        
        $cumulative = array();
        $timeSeriesData = $dataValue;
        $mergeTags = false;
        
        ##replace tags (<>)
        if (strpos($dataValue, '!>') !== false) {
            global $matchesTags;
            $this->haveCustomTags[] = $key;
            $matchesTags = array();
            $timeSeriesFullData = $dataValue;
            $timeSeriesData = preg_replace("/<!(.*?)!>/s", "", $timeSeriesFullData);
            $timeSeriesTemplate = preg_replace_callback("/<!(.*?)!>/s", array($this,'callback_tagreplace'), $timeSeriesFullData);
            $timeSeriesTemplate = str_replace($timeSeriesData, '!DATA!', $timeSeriesTemplate);
            $mergeTags = true;
        }

        $multiyears = explode(';', rtrim($timeSeriesData, '; '));
        if (count($multiyears) > 1) {
            $this->multiYearColumns[] = $key;
        }

        foreach ($multiyears as $key => $yearval) {
            list($year, $yearValue) = array_map('trim', explode(':', $yearval));
            $year = $this->sanitizeYearValues($year);
            if (empty($cumulative[$year])) {
                $cumulative["$year"] = $this->formatNumericValues_MB($yearValue);
            } else {
                $cumulative["$year"] = $this->formatNumericValues_MB($yearValue);
            }
        }

        $formattedValue = implode(';', $this->parameterize_array_mb($cumulative));
        
        if (true === $mergeTags) {
            foreach ($matchesTags as $mk => $mv) {
                $timeSeriesTemplate = str_replace($mk . '@@@###', $mv . ' ', $timeSeriesTemplate);
            }
            $timeSeriesTemplate = str_replace('!DATA!', '<br><br>' . $formattedValue . ' ', $timeSeriesTemplate);
            
            $formattedValue = $timeSeriesTemplate;
        }
        
        return $formattedValue;
    }
    
    function getExplanatoryNote($l0Title) {

        if( !in_array($l0Title,array('gun_numbers','gun_death_and_injury')) ) {
            return '';
        }

        $note = '<h6 class="expnote hidden">'       . 
                     JTEXT::_('COM_GPO_DP_EXPNOTE') . 
                '</h6>';
        
        return $note;
    }
    
    function getDPText($p_location_name, $p_dp_data, $p_narrative_state = false)
    {
        /**
         * 
         * @todo
         * The getDPColumnTitles, getDPHierarchy method to be checked if optimization is possible.
         * 
         */
        $regionAlias = $this->getRegionAliasFromURI();
        $regionInfo  = $this->getRegionNameByAlias($regionAlias);
        $loc_name_en = $p_location_name;
        $this->currentEnLocationName = $loc_name_en;
        
        if( in_array($this->currentLang,$this->otherLangs) ) {
            $p_location_name = $regionInfo->{'name_'.$this->currentLang};
        }
        
        if (empty($p_location_name) || empty($p_dp_data)) {
            return false;
        }
        $dp_metadata = $this->getDPMetaDataInfo();

        $dp_hierarchy = $this->getDPHierarchy(3);
        $dp_tree = $this->processDPHierarchy($dp_hierarchy);
        $columnTitles = $this->getDPColumnTitles();
        $jargonTerms = $this->getJargonTerms();
        $gcites_id = &$this->getDPGCiteIds();
        $this->getDPJs(); //add DP Javascript
        $dp_imagepath = '/images/datapages/';
        $linkCatAry = $this->getlinkCatAry($regionAlias,$p_dp_data);

        $DPhtml = '';
        $displayLocation = $p_location_name;
        $compareBtnTitleTag = str_replace('#',$displayLocation,JText::_('MOD_GPO_FIND_COMPARE_TITLE_TAG'));
        
        //show narrative article page link
        if ($p_narrative_state) {
            $url = $this->getNarrativePageLink($p_location_name);
            if ($url) {
                $narrativePageLink = '<h2 style="background-image:none;">'
                                     . "<a class='l2btn'  href='$url'>"
                                     . '<span class="jargon">Article</span>'
                                     . $this->getDPBrowserTitle($p_location_name, FALSE)
                                     . '</a>'
                                     . '</h2>';

                //Show DP & CP tabs
                $DPHeaderTabs = $this->getDPTabs('DP', $url);

            }
            $DPhtml = $DPHeaderTabs . $DPhtml;
        }


        $DPhtml .= '<h1>' . $this->getDPPageTitle($displayLocation) . '</h1>';

        $DPhtml .= '<div class="optionbar">
                        <div class="a2a_kit a2a_kit_size_18 a2a_default_style" style="float:right;margin:2px 0px 0 0;"><a title="Share this page on social media" class="a2a_dd" href="#"></a></div>
                        <a id="btnprint" class="btn print" title="'.JText::_('COM_GPO_DP_PRINT_ICON_TITLE').'" onclick="window.print();">
                        </a>
                    </div>';

        
        $i = 0;
        foreach ($dp_metadata as $key => $val)
        {

            if ($this->ignoreField($val)) {
                continue;
            }
            $i++;


            if (!empty($p_dp_data->{$val})) {

                //check if it has multiple years
                if(strpos($p_dp_data->{$val}, ';') !== false) {
                    
                   $formattedValue = $this->processMultiyearDataValue($p_dp_data->{$val},$val);
                }else {
                    
                   $formattedValue = $this->processDataValue($p_dp_data->{$val},$val); 
                }
                
                $html[$val] .= $this->getHTML($val, $formattedValue, $p_dp_data->{$val . '_p'}, $linkCatAry[$val], $displayLocation);
               
            }
        }

        
        $locationSubheader = trim($html['location_subheader']); //location sub-header
        //add location-subheader, if exits
        if (!empty($locationSubheader)) {
            $DPhtml .= '<span style="color: rgb(227, 131, 3);padding-top:5px;"><strong>'
                       . $locationSubheader
                       . '</strong></span>';
        }

        $treeHtml  = '<div id="dataset" class="dataset">';
        $treeHtml .= '<div class="buttonbar">
                    <a class="buttonlink expandall" href="javascript: void(0);">'.JText::_('COM_GPO_DP_EXPAND_ALL').'</a>
                    <a class="buttonlink collapseall" href="javascript: void(0);">'.JText::_('COM_GPO_DP_COLLAPSE_ALL').'</a>
                  </div>';
        
        $closedbranch = 'closedbranch';
        ##‘Expand All’ if the number of data-filled categories is three, or less than three.
        $level0Count = 0;
        foreach ($dp_tree['level0'] as $val) {
            if (!$this->isEmptyDPNode($val, $dp_tree, $html)) {
               $level0Count++;
            }
        }
        
        if($level0Count <= 3) {
            $closedbranch = '';
        }else {
            ##US states DPS (but not the US page itself) to display 
            #with all their second-level category headings when they’re opened
            if(in_array($loc_name_en, $this->getUSJurisdictions())) {
               $closedbranch = '';
            }
        }

        foreach ($dp_tree['level0'] as $val) {
            if ($this->isEmptyDPNode($val, $dp_tree, $html)) {
                continue; //omit the branch if all nodes are empty
            }

            $treeHtml .= '<div class="level0data ' . $closedbranch . '">';

            $treeHtml .= '<h2 class="l0"><a class="l2btn" href="javascript:void(0);">'
                         . '<span class="jargon">' . JText::_('COM_GPO_DP_JARGON_'.$jargonTerms[$val]) . '</span>'
                         . $columnTitles[$val]
                         . '</a></h2>'; //level0 title

            if (!empty($dp_tree['level1'][$val])) {
                foreach ($dp_tree['level1'][$val] as $k => $v) {
                    //$l2Html = $this->getLevel2Data($dp_tree['level2'][$val][$v], $html, $columnTitles, $displayLocation);
                     $l2Html = $this->getLevel2Data($dp_tree['level2'][$val][$v], $html, $columnTitles, $displayLocation, $linkCatAry);
                    if (!empty($l2Html) || !empty($html[$v])) {
                        $treeHtml .= '<div class="level1data closedbranch l1" id="'.$v.'">';
                        $treeHtml .= '<h3 class="l1">';
                        $treeHtml .= '<div class="optionbar">';
                        if(intval($gcites_id[$v])>0){
                            //show the info (i) icon
                            $defineUrl = JRoute::_( 'index.php?option=com_gpo&task=glossary&id='.$gcites_id[$v], false );

                            $treeHtml .= '<a class="btn define" title="'.JText::_('COM_GPO_DP_INFO').$columnTitles[$v].'" target="_blank" onclick="popDefinition(\''.$defineUrl.'\');"></a>';
                        }
                        if(isset($linkCatAry[$v]))
                        {
                           //External Link category src
                           $link = $linkCatAry[$v];
                           $linkBtnTitle = str_replace('#', $displayLocation, JText::_('COM_GPO_DP_LINK_BTN_TITLE_TAG'));
                           $linkBtnTitle = str_replace('~', $link['external_hyperlink_name'], $linkBtnTitle);
                           if(substr($link['external_hyperlink'],0,5)=='http:'|| substr($link['external_hyperlink'],0,5)=='https'){$link_url = $link['external_hyperlink'];}else{$link_url = 'http://'.$link['external_hyperlink'];}
                           $treeHtml .= '<a class="btn compare '.$this->currentLang.'" href="'.$link_url.'" target="_blank" title="'. $linkBtnTitle . '">' . JText::_('MOD_GPO_FIND_LINK') . '</a>';
                        }else if( $this->showCompareButton($v) AND !empty($html[$v])) {
                            $compare_page_uri = JURI::base().$this->languagePartURI.'firearms/compare/'.$this->regionInfo->id.'/'.urlencode($v);
                            $treeHtml .= '<a class="btn compare '.$this->currentLang.'" href="' . $compare_page_uri . '" target="_blank" title="' . $compareBtnTitleTag . '">' . JText::_('MOD_GPO_FIND_COMPARE') . '</a>';
                        }
                        $treeHtml .= '</div>';

                        $treeHtml .= '<a class="l2btn" href="javascript:void(0);">'
                                     . $columnTitles[$v] . '</a>';
                        if (!empty($html[$v])) {

                        }
                        $treeHtml .= '</h3>'; //level1 title
                        //
                        $treeHtml .= $this->getExplanatoryNote($val); //add a explanatory note
                        //
                        $treeHtml .= '<div class="level2data">';
                        if(in_array($v,$this->multiYearColumns)) {
                            $yearlyCompareUrl = JURI::base().$this->languagePartURI.'firearms/compareyears/'.$this->regionInfo->id.'/'.urlencode($v);
                            $treeHtml .= '<div><a class="btn compare '.$this->currentLang.' chartbtn" href="' . $yearlyCompareUrl . '" target="_blank" title="' . str_replace('~',$columnTitles[$v],JText::_('MOD_GPO_FIND_CHARTS_TITLE_TAG')) . '">' . JText::_('MOD_GPO_FIND_CHARTS') . '</a>' . $html [$v] . ' </div>'; //level1 data

                        } else {
                            $treeHtml .= '<div>' . $html [$v] . '</div>'; //level1 data
                        }

                        $treeHtml .= $l2Html; //level2 data
                        $treeHtml .= '</div>';

                        $treeHtml .= '</div>';
                    }

                }
            }
            $treeHtml .= '</div>';
        }


        $treeHtml .= '</div>';
        $treeHtml .= '<br />';

        $DPhtml .= $treeHtml;

        //add a space before the last_updated time
        $DPhtml .= '<br />';

        return $DPhtml;
    }


    function getLevel2Data($l2Array = array(), $dataArray = array(), $columnTitleArray = array(), 
                           $displayLocation='', $linkCatAry = array(), $groupId=NULL, $isRegion=FALSE)
    {
        $gcites_id = &$this->getDPGCiteIds();
        $dataHtml = '';
        $compateBtnTitleTag = str_replace('#', $displayLocation, JText::_('MOD_GPO_FIND_COMPARE_TITLE_TAG'));
        foreach ($l2Array as $v)
        {
            $UriPartGroup = ($isRegion) ? 'region' : 'group';
            $compare_page_uri = empty($groupId) ? JURI::base().$this->languagePartURI.'firearms/compare/'.$this->regionInfo->id.'/'.urlencode($v)
                                                : JURI::base().$this->languagePartURI.'firearms/compare/'.$UriPartGroup.'/'.$groupId.'/'.urlencode($v);

            if (!empty($dataArray[$v])) {
                $defineUrl = JRoute::_( 'index.php?option=com_gpo&task=glossary&id='.$gcites_id[$v], false );
                $dataHtml .= '<div class="datagrid" id="' . $v . '">'
                             . '<h4><div class="optionbar"><a class="btn define" title="'.JText::_('COM_GPO_DP_INFO').$columnTitleArray[$v].'" target="_blank" onclick="popDefinition(\''.$defineUrl.'\');"></a>';
                if(isset($linkCatAry[$v]))
                {
                    //By Amlana Link category
                    $link = $linkCatAry[$v];
                    $linkBtnTitle = str_replace('#', $displayLocation, JText::_('COM_GPO_DP_LINK_BTN_TITLE_TAG'));
                    $linkBtnTitle = str_replace('~', $link['external_hyperlink_name'], $linkBtnTitle);
                    if(substr($link['external_hyperlink'],0,5)=='http:'|| substr($link['external_hyperlink'],0,5)=='https'){$link_url = $link['external_hyperlink'];}else{$link_url = 'http://'.$link['external_hyperlink'];}
                    $dataHtml .= '<a class="btn compare '.$this->currentLang.'" href="'.$link_url.'" target="_blank" title="' . $linkBtnTitle . '">' . JText::_('MOD_GPO_FIND_LINK') . '</a>';
                } else {
                        if($this->showCompareButton($v)) {
                            $dataHtml .= '<a class="btn compare '.$this->currentLang.'" href="' . $compare_page_uri .  '" target="_blank" title="' . $compateBtnTitleTag . '">' . JText::_('MOD_GPO_FIND_COMPARE') . '</a>';
                        }
                }
                $dataHtml .= '</div>';
                $dataHtml .= $columnTitleArray[$v]
                          . '</h4>';

                //show a compare button if it is multi year column

                if(in_array($v,$this->multiYearColumns)){
                    $yearlyCompareUrl = empty($groupId) ? JURI::base().$this->languagePartURI.'firearms/compareyears/'.$this->regionInfo->id.'/'.urlencode($v)
                                                        : JURI::base().$this->languagePartURI.'firearms/compareyears/'.$UriPartGroup.'/'.$groupId.'/'.urlencode($v);
                    $dataHtml .= '<div class="dcontent"><a class="btn compare '.$this->currentLang.' chartbtn" href="' . $yearlyCompareUrl .  '" target="_blank" title="' . str_replace('~', $columnTitleArray[$v], JText::_('MOD_GPO_FIND_CHARTS_TITLE_TAG')) . '">' . JText::_('MOD_GPO_FIND_CHARTS') . '</a>' . $dataArray[$v] . '</div></div>';
                } else {
                    $dataHtml .= '<div class="dcontent">' . $dataArray[$v] . '</div></div>';
                }
                $dataHtml .= "\n";
            }
        }
        return $dataHtml;
    }


    function getDatapageLink($p_region_name, $p_region_alias, $ignorePublishFlag=false)
    {
        if (empty($p_region_name) || empty($p_region_alias)) {
            return false;
        }

        //if ($this->isDatapageExists($p_region_name)) {
            $item = $this->locationExists($p_region_name,$ignorePublishFlag);

            if ($item === false) {
                return false;
            }
            $url = JRoute::_('index.php?option=com_gpo&task=region&region=' . $item->catid, true);
            return $url;
        //}
        return false;
    }

    function getDatapageCpLink($p_region_name, $p_region_alias, $ignorePublishFlag=false)
    {
        if (empty($p_region_name) || empty($p_region_alias)) {
            return false;
        }

        //if ($this->isDatapageExists($p_region_name)) {
            $item = $this->locationExists($p_region_name,$ignorePublishFlag);

            if ($item === false) {
                return false;
            }
            //$url = JRoute::_('index.php?option=com_gpo&task=cp&region=' . $item->catid, true);
            $url = JRoute::_('/index.php');
            return $url;
        //}
        return false;
    }


    function isDatapageExists($p_location_name, $ignorePublishFlag = false)
    {
      
        $db = &JFactory::getDBO();
        if (empty($p_location_name)) {
            return false;
        }

        $sql_add = Gpo_location_display_sql('Public Backend');
        $query = "SELECT
		              `#__gpo_datapages`.id
		          FROM
		              `#__gpo_datapages`
				  LEFT JOIN
				       `#__gpo_location` as `lo`
				  ON
				       `lo`.`id` = `#__gpo_datapages`.`location_id`
		          WHERE
					   `lo`.`name` = " . $db->quote($p_location_name);
        
        if( !$ignorePublishFlag ) {
            $query .=  ' AND ' .
                       '`#__gpo_datapages`.`published` = 1'
                       . $sql_add;
        }

        $db->setQuery($query);
        $data = $db->loadObject();

        if (isset($data->id) && !empty($data->id)) {
            return true;
        }
        return false;
    }


    /*
    *
    *  datapage hierarchy related methods
    *  <!-- starts -->
    *
    */


    function getDPHierarchy($depth = 3)
    {

        $db = &JFactory::getDBO();

        if (intval($depth) < 1) {
            $depth = 1; //get top level nodes only
        }

        $select = array();
        $from = array();
        $where = array();
        $order = array();

        for ($i = 1; $i <= $depth; $i++) {
            $select [] = "level" . $i . ".column_name AS level" . $i . "_column_name";
            $from [] = "`$this->DPHierarchyTable` AS level" . $i . "";
            //$where [] = "( level" . $i . ".active = 1 )";
            $order [] = "level" . $i . ".sort_order";
        }

        //SELECT
        $sql = "SELECT " . implode(', ', $select) . " ";

        //FROM
        $sql .= "FROM " . $from [0] . " ";

        unset ($from [0]);
        if (count($select) > 0) {
            foreach ($from as $key => $value) {
                $from [$key] = $value . " ON level" . ($key) . ".id = level" . ($key + 1) . ".parent_id";
            }

            $sql .= " LEFT JOIN " . implode(" LEFT JOIN ", $from) . " ";
        }

        //WHERE
        $where = "( level1.active = 1 )";
        $sql .= "WHERE level1.parent_id IS NULL AND " . $where . " ";

        //ORDER
        $sql .= "ORDER BY " . implode(", ", $order);

        //RUN QUERY
        $db->setQuery($sql);
        
        $data = $db->loadAssocList();

        return $data;
    }


    function getJargonTerms()
    {
        $db = &JFactory::getDBO();
        $jargonTerms = array();

        $query = "SELECT
		              `#__gpo_datapage_hierarchy`.id,
		              `#__gpo_datapage_hierarchy`.column_name,
		              `#__gpo_datapage_hierarchy`.column_title,
		              `#__gpo_datapage_hierarchy`.jargon_term
		          FROM
		              `#__gpo_datapage_hierarchy`

		          WHERE
					   `#__gpo_datapage_hierarchy`.`parent_id` IS NULL" .
                 ' AND ' .
                 '`#__gpo_datapage_hierarchy`.`active` = 1';

        $db->setQuery($query);
        $data = $db->loadObjectList();

        foreach ($data as $key => $val) {
            $jargonTerms[$val->column_name] = trim($val->jargon_term);
        }

        return $jargonTerms;
    }
    
    function getlinkCatAry($regionAlias=FALSE, $dpData = NULL)
    {
        $db = &JFactory::getDBO();
        $linkCatAry = array();

        $query = "SELECT
                        `$this->DPHierarchyTable`.id,
                        `$this->DPHierarchyTable`.column_type,	
                        `$this->DPHierarchyTable`.external_hyperlink_name,	
                        `$this->DPHierarchyTable`.external_hyperlink,	
		                `$this->DPHierarchyTable`.column_name,
		                `$this->DPHierarchyTable`.column_title,
		                `$this->DPHierarchyTable`.jargon_term
		          FROM
		                `$this->DPHierarchyTable` 
		          WHERE 
					   `$this->DPHierarchyTable`.`column_type` ='1'" .
                 ' AND ' .
                      "`$this->DPHierarchyTable`.`active` = 1";

        $db->setQuery($query);
        $data = $db->loadObjectList();

        foreach ($data as $key => $val) {
            
            ##get Hyperlink value, datapages table will give more granular access than that of hierarchy table
            ## so we will read this data from the gpo_datapages table
            $externalHyperLink = empty($dpData->{$val->column_name}) ? $val->external_hyperlink
                                                                     : $dpData->{$val->column_name};
            
            ##replace [location] with RegionName
            $externalHyperlinkSrc = empty($regionAlias) ? $externalHyperLink 
                                    : str_ireplace('[location]', $regionAlias, $externalHyperLink);
            
		    $linkCatAry[$val->column_name] = array('external_hyperlink_name' => $val->external_hyperlink_name,
                                                   'external_hyperlink' => $externalHyperlinkSrc
                                             );
        }

        return $linkCatAry;
    }
    
    
    function processDPHierarchy($dataList) {
        $DPTree = array('level0' => array(),
                        'level1' => array(),
                        'level2' => array(),
                  );

        foreach ($dataList as $key => $val) {
            $l1 = $val['level1_column_name'];
            $l2 = $val['level2_column_name'];
            $l3 = $val['level3_column_name'];


            if (!in_array($l1, $DPTree['level0'])) {
                $DPTree['level0'][] = $l1;
            }

            if ($l2 != '' && $l2 != NULL) {
                if (!in_array($l2, $DPTree['level1'][$l1]))
                    $DPTree['level1'][$l1][] = $l2;
            }

            if (($l3 != '' && $l3 != NULL)) {
                if (!in_array($l3, $DPTree['level2'][$l1]))
                    $DPTree['level2'][$l1][$l2][] = $l3;
            }
        }

        return $DPTree;
    }


    /*
    *
    *  datapage hierarchy related methods
    *  <!-- ends -->
    *
    */


    /*
   *
   *  datapage routing related functions
   *
   * */

    function getRegionAliasFromURI($p_juri = '')
    {
        $uri1 = Uri::getInstance();
        $p_juri = ($p_juri) ? $p_juri : $uri1;

        if (empty($p_juri)) {
            return false;
        }

        $juri_array = array_reverse(explode('/', $p_juri));

        if (empty ($juri_array[0])){
        $regionAlias = trim($juri_array[1]);

        }else
        {
        $regionAlias = trim($juri_array[0]);
        }
        $regionAlias1 = explode('?', $regionAlias);
        $regionAlias2 = $regionAlias1[0];
        
        $this->regionAlias = $regionAlias;
        return $regionAlias2;
    }


    function getRegionNameByAlias($p_alias, $ignorePublishFlag=false)
    {
        $db = JFactory::getDBO();
        $oUser =  JFactory::getUser();
        $sql_add = Gpo_location_display_sql('Public Backend');
        
        if( false === $ignorePublishFlag && !empty($oUser) ) {
               ## For members show the unpublished DP too,
               ## If we fix the Preview DP button in admin panel, then we'll remove this
               ## 7 and 8 is administrator and super administrator
               $groupsUserIsIn = JAccess::getGroupsByUser($oUser->id);
               $isAdministrator = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
               $ignorePublishFlag = $isAdministrator;
        }
        
        $query = 'SELECT 
                       `lo`.`id`, 
                       `lo`.`type`,
                       `lo`.`name`,
                       `lo`.`name_es`,
                       `lo`.`name_fr`,
                       `cat`.`id` as catid' .
                 ' FROM 
                       `#__gpo_location` as `lo`' .
                 ' INNER JOIN 
                       `#__categories` as `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .
                 ' WHERE 
                      `cat`.`alias`=' . $db->quote($p_alias);
        if(!$ignorePublishFlag) {
           $query .=  $sql_add;
        }
        
        $query .= ' LIMIT 0,1';

        $db->setQuery($query);
        
        $regionInfo = $db->loadObject();
        $this->regionInfo = $regionInfo;
        return $regionInfo;
    }

    function locationExists($name, $ignorePublishFlag=false)
    {
        if (empty($name)) {
            return false;
        }

        $db = &JFactory::getDBO();
        $sql_add = Gpo_location_display_sql('Public Backend');
        $query = 'SELECT `lo`.`id`, `lo`.`type`,`lo`.`name`,`cat`.`id` as catid' .
                 ' FROM `#__gpo_location` as `lo`' .
                 ' INNER JOIN `#__categories` as `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .
                 ' WHERE lower(`cat`.`title`)=lower(' . $db->quote($name) . ') ';
        if(!$ignorePublishFlag) {
            $query .= $sql_add;
        }
            
        $query .=  ' LIMIT 0,1;';
        
        $db->setQuery($query);
        return $db->loadObject();
    }


    function isNarrativePageAvailable($p_article_state, $p_article_access)
    {
        //      if( empty($p_article_state) && empty($p_article_access) ){
        //      	 return false;
        //      }


        $access_denyed = true;
        $user = & JFactory::getUser();
        $aid = $user->get("aid", "0");

      

        if ($aid === 1) {
            $access_denyed = ($p_article_access > 1) ? true : false;
        } else if ($aid === 2) {
            $access_denyed = false;
        } else {

            $access_denyed = ($p_article_access > 0) ? true : false;


        }
        

        //if ((INT)$p_article_state !== (INT)"1" || $access_denyed) {
        if ((INT)$p_article_state !== (INT)"1" ) {

          return false; 
        }
       
        return true;
    }


    function getNarrativePageLink($p_location_name)
    {
        $item = $this->locationExists($p_location_name);
        if ($item === false) {
            return false;
        }
       // $url = JRoute::_('index.php?option=com_gpo&task=cp&cp=1&region=' . $item->catid, true);
        //$url = JRoute::_('index.php?option=com_gpo&cp=1&task=region&region=' . $item->catid, true);
        $p_location_name_prepared  = strtolower($p_location_name);
        $p_location_name_prepared = str_replace(' ', '-', $p_location_name_prepared);
        $url = '/component/gpo/region/cp/'.$p_location_name_prepared.'?cp=1&task=cp&region='.$item->catid;
        return $url;
    }


    function isNUllDate($p_datetime)
    {
        if (empty($p_datetime) || $p_datetime == '0000-00-00 00:00:00') {
            return true;
        }
        return false;
    }

    function getDisplayType(){
        $db = &JFactory::getDBO();
        $db->setQuery("SELECT `column_name`, `display_type` FROM `$this->DPHierarchyTable`");
        $display_type = array();
        foreach($db->loadAssocList() AS $item){
            $display_type[$item['column_name']] = $item['display_type'];
        }

        return $display_type;
    }

    function showCompareButton($column_name){
        $displayTypes = $this->getDisplayType();
        if(empty($displayTypes[$column_name]) OR $displayTypes[$column_name]=='no_comparison'){
            return false; //do not show compare button
        }

        return true;
    }

    function hasMultiYearData($column_name){

    }

    /* Group relatd methods */

    function isGroupExists($id) {
        if (empty($id)) {
            return false;
        }

        $db = &JFactory::getDBO();

        $query = ' SELECT * ' .
                ' FROM `#__gpo_groups` ' .
                //' WHERE lower(`name`) = lower(' . $db->quote($name) . ')' .
                ' WHERE `id` = ' . $db->quote($id) .
                ' LIMIT 0,1;';
        $db->setQuery($query);
        return $db->loadObject();
    }

    /*
     * 
     * Group related methods 
     * 
     */

    function getAllLocationsByGroupId($groupId) {
        if (empty($groupId)) {
            return false;
        }

        $db = &JFactory::getDBO();
        $query = "SELECT 
                         `#__gpo_location`.`id`,
                         `#__gpo_location`.`name`,
                         `#__gpo_location_to_groups`.group_id,
                         `#__gpo_location_to_groups`.location_id,
                         `#__gpo_location_to_groups`.sort            
                  FROM 
                         `#__gpo_location` 
                  JOIN 
                         `#__gpo_location_to_groups` 
                  ON  
                         `#__gpo_location_to_groups`.`location_id` = `#__gpo_location`.`id` 
                  WHERE 
                         `#__gpo_location_to_groups`.group_id = " . $db->quote($groupId) .
                 " ORDER BY
                         `#__gpo_location_to_groups`.sort ASC
                  ";
        $db->setQuery($query);
        
        return $db->loadAssocList('id');
    }

    function getGroupById($groupId) {
        $db = &JFactory::getDBO();
        $query = "SELECT `id`,`name` FROM `#__gpo_groups` WHERE `id` = " . $db->quote($groupId);
        $db->setQuery($query);
        return $db->loadObject();
    }

    function getGroupByName($groupName) {
        $db = &JFactory::getDBO();
        $groupName = trim(strtolower($groupName));
        $query = "SELECT `id`,`name` FROM `#__gpo_groups` WHERE lower(`name`) = " . $db->quote($groupName);
        
        $db->setQuery($query);
        return $db->loadAssoc('id');
    }
    
    function getAllGroupNames($column=NULL)
	{
        $db = &JFactory::getDBO();
		$query = "SELECT `id`,`id` as location_id,`name` as location FROM `#__gpo_groups`;";
		$db->setQuery( $query );
        $groups = $db->loadObjectList();
        $groupsHavingData = array();
        
        if( empty($column) ) {
            return $groups;
        }
        
        foreach($groups as $key=>$val) {
            if($this->hasGroupDataInColumn($val->id, $column)) {
               $groupsHavingData[] = $val;
            }
        }
		return $groupsHavingData;
	}
    
    
    /*
     * Check if a Group has any data 
     * in the member countries
     * 
     */
    function hasGroupDataInColumn($groupId,$dp_column=NULL)
	{
        if( empty($groupId) || empty($dp_column) ) {
            return false;
        }
            
        $db = &JFactory::getDBO();
        
        $query = "SELECT 
                      `#__gpo_datapages`.`location_id` as `loc_id`, 
                      `#__gpo_datapages`.`location` as `loc_name`, 
                      `#__gpo_datapages`.`$dp_column`, 
                      `#__gpo_location_to_groups`.`group_id`,
                      `#__gpo_groups`.`name` as `location` 
                  FROM 
                      `#__gpo_datapages` 
                  INNER JOIN 
                      `#__gpo_location_to_groups` ON `#__gpo_datapages`.location_id = `#__gpo_location_to_groups`.location_id 
                  INNER JOIN 
                      `#__gpo_groups` ON `#__gpo_groups`.`id` = `#__gpo_location_to_groups`.`group_id` 
                  WHERE 
                      `#__gpo_location_to_groups`.`group_id` = " . $db->quote($groupId) .
                  "AND
                      `#__gpo_datapages`.`$dp_column` <> '' 
                  ";
        
		$db->setQuery( $query );
        $db->query();
		if( $db->getNumRows() >0 ) {
            return true;
        }
        
        return false;
	}
    
   
    
    function getLocationById($locationId) {
        if( empty($locationId) ) {
            return false;
        }
        
        $db = &JFactory::getDBO();
        $query = "SELECT * FROM `#__gpo_location` WHERE `id` = " . $db->quote($locationId);
        $db->setQuery($query);
        return $db->loadObject();
    }
    
    
    /*
     * Group DP related methods
     * 
     */
    function getDPByGroup($groupId) {
        $db = &JFactory::getDBO();
        if (empty($groupId)) {
            return false;
        }
        
        $groupLocations = $this->getAllLocationsByGroupId($groupId);
        $groupLocationIDs = array();
        
        foreach( $groupLocations as $key => $val ) {
            $groupLocationIDs[] = $val['location_id'];   
        }

        $sql_add = Gpo_location_display_sql('Public Backend');
        
        $query = "SELECT
		              `#__gpo_datapage_preamble_values`.*, `#__gpo_datapages`.*
		          FROM
		              `#__gpo_datapages`
		          LEFT JOIN
		              `#__gpo_datapage_preamble_values`
		          ON
		              `#__gpo_datapages`.`location_id` = `#__gpo_datapage_preamble_values`.`location_id`
				  LEFT JOIN
				      `#__gpo_location` as `lo`
				  ON
				      `lo`.`id` = `#__gpo_datapages`.`location_id`
		          WHERE
					  `lo`.`id` IN(" . implode(',',$groupLocationIDs) . ")" . 
                ' AND ' . 
                      '`#__gpo_datapages`.`published` = 1'
                . $sql_add;

        $db->setQuery($query);
        $data = $db->loadObjectList();

        /*
        if (empty($data->id)) {
            return false;
        }
        */
        return $data;
    }
    
      
    function getGroupDPText($p_group_name, $p_dp_data, $p_group_id=NULL, $p_narrative_state = false) {
        /**
         * @todo
         * The getDPColumnTitles, getDPHierarchy method to be checked if optimization is possible.
         */
        //$regionInfo = $this->getRegionNameByAlias($regionAlias);
        if (empty($p_group_name) || empty($p_dp_data)) {
            return false;
        }
        $dp_metadata = $this->getDPMetaDataInfo();

        $dp_hierarchy = $this->getDPHierarchy(3);
        $dp_tree = $this->processDPHierarchy($dp_hierarchy);
        $columnTitles = $this->getDPColumnTitles();
        $jargonTerms = $this->getJargonTerms();
        $gcites_id = &$this->getDPGCiteIds();
        $DPColumnsInfo = $this->getDPColumnsInfo();
        $this->getDPJs(); //add DP Javascript
        $dp_imagepath = '/images/datapages/';


        $DPhtml = '';
        $displayLocation = 'the ' . $p_group_name . ' group';
        $compateBtnTitleTag = str_replace('#', $displayLocation, JText::_('MOD_GPO_FIND_COMPARE_TITLE_TAG'));
        
       	$DPhtml .= '<h1><b>' . ucfirst($displayLocation) . '</b><br/><font size="4px">' . JText::_('Tracking armed violence reduction') . '</font></h1>';
        $DPhtml .= '<div class="optionbar">
                    <a id="btnprint" class="btn print" title="'.JText::_('COM_GPO_DP_PRINT_ICON_TITLE').'" onclick="window.print();">
                    </a>
                    </div>';

        $i = 0;
        foreach ($dp_metadata as $key => $val) {

            if ($this->ignoreField($val)) {
                continue;
            }
            $i++;

            $cumulative = array();
            $dpdataCumulativeValue = '';
            $haveDecimalVal = false;
            $totalLocations = 0;
            
            foreach ($p_dp_data as $dpkey => $dpdata) {
                
                if (!empty($dpdata->{$val})) {
                    $totalLocations++;
                    //check if it has multiple years
                    if (strpos($dpdata->{$val}, ';') !== false) {
                        $multiyears = explode(';', rtrim($dpdata->{$val}, '; '));
                        
                        if (count($multiyears) > 1) {
                            $this->multiYearColumns[] = $val;
                        }
                        foreach($multiyears as $key=>$yearval) {
                            list($year,$yearValue) = array_map( 'trim',explode(':', $yearval) );
                            $year = $this->sanitizeYearValues($year);
                            if( empty($cumulative[$year]) ) {
                               $cumulative["$year"] = $this->santitizeNumericValues($yearValue);
                            }else {
                               $cumulative["$year"] = $this->addMultipleYearValues($cumulative["$year"], $yearValue);
                            }
                            
                            if (strpos($yearValue, '.') !== false) {
                                $haveDecimalVal = true;
                            }
                        }
                        //$dpdataCumulativeValue = implode(';',$this->parameterize_array($cumulative));
                        
                    }else {
                         $numValue  = $this->removeCitations($dpdata->{$val});
                         if(is_numeric($numValue)) {
                             $dpdataCumulativeValue += $this->santitizeNumericValues($numValue);
                         }
                    }
                    
                    $dpdataPreamble = $dpdata->{$val . '_p'};
                }
            }
            
            //show average if enabled
            if($this->isShowAsAverage($DPColumnsInfo[$val]->region_aggregation_type)) {
                 $cumulative = $this->calculateAverage($cumulative,$totalLocations);
            }
            
            $dpdataCumulativeValue = implode(';', $this->parameterize_array($cumulative,$haveDecimalVal));
            
            //add a trailing semicolon if it is a yearly data
            if( strpos($dpdataCumulativeValue,':') !== false ) {
                $dpdataCumulativeValue .= ';';
            }
            
            if( !empty($dpdataCumulativeValue) && !$this->isDisplayOff($DPColumnsInfo[$val]->region_aggregation_type) ) {
                $html[$val] .= $this->getHTML($val, $dpdataCumulativeValue, $dpdataPreamble, '', $displayLocation);
            }
        }


        //$locationSubheader = trim($html['location_subheader']); //location sub-header
        $string_m = $html['all_countries'];
        $pattern = '/: [0-9]+\.[0-9]+\{/';
        preg_match($pattern, $string_m, $matches);
        $data_string = substr($matches[0],0,-1);
        $data_string = substr($data_string,2);
        $locationSubheader = trim(str_replace(array('#','~'),array($displayLocation,$data_string),$html['location_subheader'])); //location sub-header
        

        //add location-subheader, if exits
        if (!empty($locationSubheader)) {
            $DPhtml .= '<span style="color: rgb(227, 131, 3);padding-top:5px;"><strong>'
                    . $locationSubheader
                    . '</strong></span>';
        }

        $treeHtml  = '<div id="dataset" class="dataset">';
        $treeHtml .= '<div class="buttonbar">
                      <a class="buttonlink expandall" href="javascript: void(0);">expand all</a>
                      <a class="buttonlink collapseall" href="javascript: void(0);">collapse all</a>
                      </div>';

        foreach ($dp_tree['level0'] as $val) {
            if ($this->isEmptyDPNode($val, $dp_tree, $html)) {
                continue; //omit the branch if all nodes are empty
            }

            $treeHtml .= '<div class="level0data closedbranch">';
            $treeHtml .= '<h2 class="l0"><a class="l2btn" href="javascript:void(0);">'
                      . '<span class="jargon">' . $jargonTerms[$val] . '</span>'
                      . $columnTitles[$val]
                      . '</a></h2>'; //level0 title


            if (!empty($dp_tree['level1'][$val])) {
                foreach ($dp_tree['level1'][$val] as $k => $v) {
                    $l2Html = $this->getLevel2Data($dp_tree['level2'][$val][$v], $html, $columnTitles, $displayLocation, array(), $p_group_id);

                    if (!empty($l2Html) || !empty($html[$v])) {
                        $treeHtml .= '<div class="level1data closedbranch l1" id="' . $v . '">';
                        $treeHtml .= '<h3 class="l1">';
                        $treeHtml .= '<div class="optionbar">';
                        if (intval($gcites_id[$v]) > 0) {
                            //show the info (i) icon
                            $defineUrl = JRoute::_('index.php?option=com_gpo&task=glossary&id=' . $gcites_id[$v], false);

                            $treeHtml .= '<a class="btn define" title="'.JText::_('COM_GPO_DP_INFO').$columnTitles[$v] . '" target="_blank" onclick="popDefinition(\'' . $defineUrl . '\');"></a>';
                        }
                        if ($this->showCompareButton($v) AND !empty($html[$v])) {
                            $compare_page_uri = JURI::base().$this->languagePartURI.'firearms/compare/group/' . $p_group_id . '/' . urlencode($v);
                            $treeHtml .= '<a class="btn compare '.$this->currentLang.'" href="' . $compare_page_uri . '" target="_blank" title="' . $compateBtnTitleTag . '">' . JText::_('MOD_GPO_FIND_COMPARE') . '</a>';
                        }
                        $treeHtml .= '</div>';

                        $treeHtml .= '<a class="l2btn" href="javascript:void(0);">'
                                . $columnTitles[$v] . '</a>';
                        if (!empty($html[$v])) {
                            
                        }
                        $treeHtml .= '</h3>'; //level1 title
                        $treeHtml .= '<div class="level2data">';
                        if (in_array($v, $this->multiYearColumns)) {
                            $yearlyCompareUrl = JURI::base() . $this->languagePartURI . 'firearms/compareyears/group/' . $p_group_id . '/' . urlencode($v);
                            $treeHtml .= '<div><a class="btn compare '.$this->currentLang.' chartbtn" href="' . $yearlyCompareUrl . '" target="_blank" title="' . str_replace('~', $columnTitles[$v], JText::_('MOD_GPO_FIND_CHARTS_TITLE_TAG')) . '">' . JText::_('MOD_GPO_FIND_CHARTS') . '</a>' . $html [$v] . ' </div>'; //level1 data
                        } else {
                            $treeHtml .= '<div>' . $html [$v] . '</div>'; //level1 data
                        }

                        $treeHtml .= $l2Html; //level2 data
                        $treeHtml .= '</div>';

                        $treeHtml .= '</div>';
                    }
                }
            }
            $treeHtml .= '</div>';
        }


        $treeHtml .= '</div>';
        $treeHtml .= '<br />';

        $DPhtml .= $treeHtml;


        //add a space before the last_updated time
        $DPhtml .= '<br />';

        return $DPhtml;
    }
    
    
    /*
     * Region DP related methods
     * 
     */
    function getDPByRegion($regionLocationIds)
    {
        $db = &JFactory::getDBO();
        if (empty($regionLocationIds)) {
            return false;
        }
        
        $query = "SELECT
		              `#__gpo_datapage_preamble_values`.*, `#__gpo_datapages`.*
		          FROM 
		              `#__gpo_datapages`
		          LEFT JOIN 
		              `#__gpo_datapage_preamble_values`
		          ON 
		              `#__gpo_datapages`.`location_id` = `#__gpo_datapage_preamble_values`.`location_id`
				  LEFT JOIN 
				      `#__gpo_location` as `lo`
				  ON 
				      `lo`.`id` = `#__gpo_datapages`.`location_id`
		          WHERE 
					  `lo`.`id` IN(" . implode(',',$regionLocationIds) . ")" . 
                ' AND ' . 
                      '`#__gpo_datapages`.`published` = 1' . 
                ' AND `lo`.`display`=1 ';

        $db->setQuery($query);
        $data = $db->loadObjectList();
  
        return $data;
    }
    
    
    function getRegionDPText($p_group_name, $p_dp_data, $p_group_id = NULL, $p_narrative_state = false) {
        /**
         * @todo
         * The getDPColumnTitles, getDPHierarchy method to be checked if optimization is possible.
         */
        //$regionInfo = $this->getRegionNameByAlias($regionAlias);
        if (empty($p_group_name) || empty($p_dp_data)) {
            return false;
        }
        
        $isRegion = TRUE;
        $dp_metadata = $this->getDPMetaDataInfo();
        $dp_hierarchy = $this->getDPHierarchy(3);
        $dp_tree = $this->processDPHierarchy($dp_hierarchy);
        $columnTitles = $this->getDPColumnTitles();
        $jargonTerms = $this->getJargonTerms();
        $gcites_id = &$this->getDPGCiteIds();
        $DPColumnsInfo = $this->getDPColumnsInfo();
        $this->getDPJs(); //add DP Javascript
        $dp_imagepath = '/images/datapages/';

        $DPhtml = '';
        $displayLocation = 'the ' . $p_group_name . ' region';
        
        
        //show narrative article page link
        if ($p_narrative_state) {
            $url = $this->getNarrativePageLink($p_group_name);
            if ($url) {
                //Show DP & CP tabs
                $DPHeaderTabs = $this->getDPTabs('DP', $url);
            }
            $DPhtml = $DPHeaderTabs . $DPhtml;
        }
        

        $DPhtml .= '<h1><b>' . ucfirst($displayLocation) . '</b><br/><font size="4px">' . JText::_('Tracking armed violence reduction') . '</font></h1>';
        $DPhtml .= '<div class="optionbar">
                       <a id="btnprint" class="btn print" title="'.JText::_('COM_GPO_DP_PRINT_ICON_TITLE').'" onclick="window.print();">
                       </a>
                   </div>';

        $i = 0;
    
        foreach ($dp_metadata as $key => $val) {

            if ($this->ignoreField($val)) {
                continue;
            }
            $i++;
         
            $cumulative = array();
            $dpdataCumulativeValue = '';
            $haveDecimalVal = false;
            $totalLocations = 0;
            
            foreach ($p_dp_data as $dpkey => $dpdata) {

                if (!empty($dpdata->{$val})) {
                    $totalLocations++;
                    //check if it has multiple years
                    if (strpos($dpdata->{$val}, ';') !== false) {
                        $multiyears = explode(';', rtrim($dpdata->{$val}, '; '));

                        if (count($multiyears) > 1) {
                            $this->multiYearColumns[] = $val;
                        }
                        foreach ($multiyears as $key => $yearval) {
                            list($year, $yearValue) = array_map( 'trim',explode(':',$yearval) );
                            $year = $this->sanitizeYearValues($year);
                            if (empty($cumulative[$year])) {
                                $cumulative["$year"] = $this->santitizeNumericValues($yearValue);
                            } else {
                                $cumulative["$year"] = $this->addMultipleYearValues($cumulative["$year"],$yearValue);
                            }
                            
                            if (strpos($yearValue, '.') !== false) {
                                $haveDecimalVal = true;
                            }
                        }
                        /*
                        if( $val == 'multilateral_total' ) {
                            echo "<br>$val - location = " . $dpdata->location . '<br>'; print_r($cumulative); echo '<br>';
                        }
                        */
                        
                        //$dpdataCumulativeValue = implode(';', $this->parameterize_array($cumulative,false));
                    } else {
                        $numValue = $this->removeCitations($dpdata->{$val});
                        if (is_numeric($numValue)) {
                            /*
                            if( $val == 'multilateral_total' ) {
                              echo "<br>$val - $numValue - location = " . $dpdata->location . 
                              '<br>'; print_r($dpdataCumulativeValue); echo '<br>';
                            }
                            */
                            $dpdataCumulativeValue += $this->santitizeNumericValues($numValue);
                        }
                    }

                    $dpdataPreamble = $dpdata->{$val . '_p'};
                }
            }
            
            //$dpdataCumulativeValue = $this->formatValues($dpdataCumulativeValue);
            //show average if enabled
            if($this->isShowAsAverage($DPColumnsInfo[$val]->region_aggregation_type)) {
                 $cumulative = $this->calculateAverage($cumulative,$totalLocations);
                 //echo '<!-- total location = ' . $totalLocations . '-->';
            }
            
            $dpdataCumulativeValue = implode(';', $this->parameterize_array($cumulative,$haveDecimalVal));
            
            //add a trailing semicolon if it is a yearly data
            if( strpos($dpdataCumulativeValue,':') !== false ) {
                $dpdataCumulativeValue .= ';';
            }
               
            if ( !empty($dpdataCumulativeValue) && !$this->isDisplayOff($DPColumnsInfo[$val]->region_aggregation_type) ) {
                $html[$val] .= $this->getHTML($val, $dpdataCumulativeValue, $dpdataPreamble, '', $displayLocation);
            }
            
        }
        

        //$res  = $this->isNeedToAddThe($displayLocation);
        //if($res){$displayLocation = 'the '.$displayLocation;}
        //$locationSubheader = trim($html['location_subheader']); //location sub-header
        $string_m = $html['all_countries'];
        $pattern = '/: [0-9]+\.[0-9]+\{/';
        preg_match($pattern, $string_m, $matches);
        $data_string = substr($matches[0], 0, -1);
        $data_string = substr($data_string, 2);
        $locationSubheader = trim(str_replace(array('#', '~'), array($displayLocation, $data_string), $html['location_subheader'])); //location sub-header
        //add location-subheader, if exits
        if (!empty($locationSubheader)) {
            $DPhtml .= '<span style="color: rgb(227, 131, 3);padding-top:5px;"><strong>'
                    .  $locationSubheader
                    .  '</strong></span>';
        }

        $treeHtml = '<div id="dataset" class="dataset">';
        $treeHtml .= '<div class="buttonbar">
                     <a class="buttonlink expandall" href="javascript: void(0);">expand all</a>
                     <a class="buttonlink collapseall" href="javascript: void(0);">collapse all</a>
                     </div>';

        $closedbranch = 'closedbranch';
        ##‘Expand All’ if the number of data-filled categories is three, or less than three.
        $level0Count = 0;
        foreach ($dp_tree['level0'] as $val) {
            if (!$this->isEmptyDPNode($val, $dp_tree, $html)) {
               $level0Count++;
            }
        }
        if($level0Count <= 3) {
            $closedbranch = '';
        }

        foreach ($dp_tree['level0'] as $val) {
            if ($this->isEmptyDPNode($val, $dp_tree, $html)) {
                continue; //omit the branch if all nodes are empty
            }

            $treeHtml .= '<div class="level0data '. $closebranch .'">';
            $treeHtml .= '<h2 class="l0"><a class="l2btn" href="javascript:void(0);">'
                         . '<span class="jargon">' . $jargonTerms[$val] . '</span>'
                         . $columnTitles[$val]
                         . '</a></h2>'; //level0 title


            if (!empty($dp_tree['level1'][$val])) {
                foreach ($dp_tree['level1'][$val] as $k => $v) {
                    $l2Html = $this->getLevel2Data($dp_tree['level2'][$val][$v], $html, $columnTitles, $displayLocation, array(), $p_group_id, $isRegion);

                    if (!empty($l2Html) || !empty($html[$v])) {
                        $treeHtml .= '<div class="level1data closedbranch l1" id="' . $v . '">';
                        $treeHtml .= '<h3 class="l1">';
                        $treeHtml .= '<div class="optionbar">';
                        if (intval($gcites_id[$v]) > 0) {
                            //show the info (i) icon
                            $defineUrl = JRoute::_('index.php?option=com_gpo&task=glossary&id=' . $gcites_id[$v], false);

                            $treeHtml .= '<a class="btn define" title="'.JText::_('COM_GPO_DP_INFO').$columnTitles[$v] . '" target="_blank" onclick="popDefinition(\'' . $defineUrl . '\');"></a>';
                        }
                        if ($this->showCompareButton($v) AND !empty($html[$v])) {
                            $compare_page_uri = JURI::base() . $this->languagePartURI . 'firearms/compare/region/' . $p_group_id . '/' . urlencode($v);
                            $treeHtml .= '<a class="btn compare '.$this->currentLang.'" href="' . $compare_page_uri . '" target="_blank" title="Compare ' . $displayLocation . ' to other states and countries">Compare</a>';
                        }
                        $treeHtml .= '</div>';

                        $treeHtml .= '<a class="l2btn" href="javascript:void(0);">'
                                  . $columnTitles[$v] . '</a>';
                        if (!empty($html[$v])) {
                            
                        }
                        $treeHtml .= '</h3>'; //level1 title
                        $treeHtml .= '<div class="level2data">';
                        if (in_array($v, $this->multiYearColumns)) {
                            $yearlyCompareUrl = JURI::base() . $this->languagePartURI . 'firearms/compareyears/region/' . $p_group_id . '/' . urlencode($v);
                            $treeHtml .= '<div><a class="btn compare '.$this->currentLang.' chartbtn" href="' . $yearlyCompareUrl . '" target="_blank" title="Chart ' . $columnTitles[$v] . ' over time">Chart</a>' . $html [$v] . ' </div>'; //level1 data
                        } else {
                            $treeHtml .= '<div>' . $html [$v] . '</div>'; //level1 data
                        }

                        $treeHtml .= $l2Html; //level2 data
                        $treeHtml .= '</div>';

                        $treeHtml .= '</div>';
                    }
                }
            }
            $treeHtml .= '</div>';
        }


        $treeHtml .= '</div>';
        $treeHtml .= '<br />';

        $DPhtml .= $treeHtml;


        //add a space before the last_updated time
        $DPhtml .= '<br />';

        return $DPhtml;
    }
    
    
    
    
    
    /*
     * 
     * Region DP Tabular Analyzer tools
     * 
     * 
     */
    
     function getRegionDPTabular($p_group_name, $p_dp_data, $p_column_name, $p_group_id = NULL) 
     {
      
        if (empty($p_group_name) || empty($p_dp_data)) {
            return false;
        }
        
        $isRegion      = TRUE;
        $DPColumnsInfo = $this->getDPColumnsInfo();
        $i = 0;
    
        $val = $p_column_name;

            $i++;
         
            $cumulative = array();
            $dpdataCumulativeValue = '';
            $haveDecimalVal = false;
            $totalLocations = 0;
            $results = array();
            
            foreach ($p_dp_data as $dpkey => $dpdata) {

                if (!empty($dpdata->{$val})) {
                    $totalLocations++;
                    //check if it has multiple years
                    if (strpos($dpdata->{$val}, ';') !== false) {
                        $multiyears = explode(';', rtrim($dpdata->{$val}, '; '));

                        if (count($multiyears) > 1) {
                            $this->multiYearColumns[] = $val;
                        }
                        foreach ($multiyears as $key => $yearval) {
                            list($year, $yearValue) = array_map( 'trim',explode(':',$yearval) );
                            $year = $this->sanitizeYearValues($year);
                            if (empty($cumulative[$year])) {
                                $cumulative["$year"] = $this->santitizeNumericValues($yearValue);
                            } else {
                                $cumulative["$year"] = $this->addMultipleYearValues($cumulative["$year"],$yearValue);
                            }
                            
                            if (strpos($yearValue, '.') !== false) {
                                $haveDecimalVal = true;
                            }
                        }
                        
                        //$dpdataCumulativeValue = implode(';', $this->parameterize_array($cumulative,false));
                    } else {
                        $numValue = $this->removeCitations($dpdata->{$val});
                        if (is_numeric($numValue)) {
                            $dpdataCumulativeValue += $this->santitizeNumericValues($numValue);
                        }
                    }

                }
            }
            
            $results['cumulativeData'] = $cumulative;
            
            //show average if enabled
            if($this->isShowAsAverage($DPColumnsInfo[$val]->region_aggregation_type)) {
                 $cumulative = $this->calculateAverage($cumulative,$totalLocations);
            }
            
            $dpdataCumulativeValue = implode(';', $this->parameterize_array($cumulative,$haveDecimalVal));
            
            //add a trailing semicolon if it is a yearly data
            if( strpos($dpdataCumulativeValue,':') !== false ) {
                $dpdataCumulativeValue .= ';';
            }
                           
          $results['aggregatedData'] = $dpdataCumulativeValue;
          return $results;
        
     }
     
     
     function getYearlyDataArray($dpDataVal) {
           
           if( empty($dpDataVal) ) {
               return array();
           }
           
           $haveDecimalVal = false;
           $cumulative     = array();
           
           if (strpos($dpDataVal, ';') !== false) {
            $multiyears = explode(';', rtrim($dpDataVal, '; '));

//            if (count($multiyears) > 1) {
//                $this->multiYearColumns[] = $val;
//            }
            
            foreach ($multiyears as $key => $yearval) {
                list($year, $yearValue) = array_map('trim', explode(':', $yearval));
                $year = $this->sanitizeYearValues($year);
                if (empty($cumulative[$year])) {
                    $cumulative["$year"] = $this->santitizeNumericValues($yearValue);
                } else {
                    $cumulative["$year"] = $this->addMultipleYearValues($cumulative["$year"], $yearValue);
                }

                if (strpos($yearValue, '.') !== false) {
                    $haveDecimalVal = true;
                }
            }
        }
        
        //add thousand separator
        foreach($cumulative as $key=>&$val) {
            $val =  ($haveDecimalVal ? number_format($val,1) : $this->addThousandSeparator($val));
        }
                
        return $cumulative;
     }
     
     function splitDataValueYearly($dpDataVal) {
           
           if( empty($dpDataVal) ) {
               return array();
           }
           
           $haveDecimalVal = false;
           $cumulative     = array();
           
           if (strpos($dpDataVal, ';') !== false) {
            $multiyears = explode(';', rtrim($dpDataVal, '; '));

//            if (count($multiyears) > 1) {
//                $this->multiYearColumns[] = $val;
//            }
            
            foreach ($multiyears as $key => $yearval) {
                list($year, $yearValue) = array_map('trim', explode(':', $yearval));
                $year = $this->sanitizeYearValues($year);
                if (empty($cumulative[$year])) {
                    $cumulative["$year"] = $yearValue;
                }
            }
        }
        
        return $cumulative;
     }

}
?>
