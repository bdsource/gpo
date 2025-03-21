<?php
$shared_functions = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'helper' . DS . 'shared_functions.php';
require_once( $shared_functions );

function GpoStrToTime( $str )
{
	if( empty( $str ) )
	{
		return $str;
	}
	$d = explode("/",$str);
	$d = array_reverse( $d );
	$d = implode("/", $d );
	$t= strtotime( $d );
	return $t;				
}

function getFeaturedCharts($langCode='en') 
{
    $langCode = strtolower($langCode);
    $db = JFactory::getDBO();
    $db->setQuery("SELECT 
                         * 
                   FROM 
                         `#__gpo_featuredcharts` 
                   WHERE
                         `language` = '$langCode' 
                   ORDER BY `ordering` ASC, `id` ASC LIMIT 10");
    return $db->loadObjectList();
}

function sortLocationNames($locationArray, $langCode, $excludeFirstTwo=false) {
    
    if(empty($langCode)) {
        return $locationArray;
    }
    
    if($excludeFirstTwo === TRUE) {
        $arrayKeys = array_keys($locationArray);
        
        $firstLocKey  = array_shift($arrayKeys);
        $secondLocKey = array_shift($arrayKeys);

        $firstLoc  = array_shift($locationArray);
        $secondLoc = array_shift($locationArray);
    }
    
    ##Sorting the location array as of Fr/Es locale
    if ('fr' == $langCode) {
        setlocale("LC_ALL", "fr_FR.utf8");
        asort($locationArray, SORT_LOCALE_STRING);
    } else if ('es' == $langCode) {
        setlocale("LC_ALL", "es_ES.utf8");
        asort($locationArray, SORT_LOCALE_STRING);
    }
    
    ##Now Merge back, if first two locations are excluded previously
    if($excludeFirstTwo === TRUE) {
         $locationArray = array($firstLocKey=>$firstLoc, $secondLocKey=>$secondLoc) + $locationArray;
    }
    
    return $locationArray;
}