<?php
defined( '_JEXEC' ) or die( 'Restricted access' );


$file = 'select';
//$path = JPATH_LIBRARIES."/cms/html/".$file.'.php';
//$path = JURI::root( true ).'/'.strtolower($file).'.php';
//$path = 'http://staging.gunpolicy.org/libraries/cms/html/select.php';
//$path = 'E:\server2\htdocs\gpo-update\libraries\cms\html\select.php';
require_once( $path );

$shared_functions = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'helper' . DS . 'shared_functions.php';
require_once( $shared_functions );

$lang    = JFactory::getLanguage();
$langTag = $lang->getTag();
$locationString = 'name';
if (strlen($langTag) > 2) {
    $currentLangCode = strtolower(substr($langTag, 0, -3));
}

if (in_array($currentLangCode, array('es','fr'))) {
    $locationString = 'name_' . $currentLangCode;
}

$options_country = '';
$options_region = '';

$data = GpoGetTypeFromCache( 'public_country' );

$allLocations = GpoGetAllLocationNames();

if ($data !== false) {
    $items = explode("\n", $data);

    foreach ($items as $v) {
        $value = str_replace("&nbsp;", "", $v);
        $locName = ('en' == $currentLangCode) ? ucwords($v) :
                $allLocations[trim($v)]->{$locationString};
        if (empty($locName)) {
            $locName = ucwords($v);
        }
        $countryList[$value] = $locName;
        //$options_country .= '<option value="' . $value . '">' . $locName . '</option>';
    }

    ##Sorting the location array as of Fr/Es locale
    if ('fr' == $currentLangCode) {
        $countryKeys = array_keys($countryList);
        setlocale("LC_ALL", "fr_FR.utf8");
        
        $firstLocKey  = array_shift($countryKeys);
        $secondLocKey = array_shift($countryKeys);

        $firstLoc  = array_shift($countryList);
        $secondLoc = array_shift($countryList);

        asort($countryList, SORT_LOCALE_STRING);
        $countryList = array($firstLocKey=>$firstLoc, $secondLocKey=>$secondLoc) + $countryList;
    } else if ('es' == $currentLangCode) {
        $countryKeys = array_keys($countryList);
        setlocale("LC_ALL", "es_ES.utf8");

        $firstLocKey  = array_shift($countryKeys);
        $secondLocKey = array_shift($countryKeys);

        $firstLoc  = array_shift($countryList);
        $secondLoc = array_shift($countryList);

        asort($countryList, SORT_LOCALE_STRING);
        $countryList = array($firstLocKey=>$firstLoc, $secondLocKey=>$secondLoc) + $countryList;
    }

    foreach ($countryList as $value => $locName) {
        $options_country .= '<option value="' . $value . '">' . $locName . '</option>';
    }
}



$data = GpoGetTypeFromCache( 'public_region' );

if( $data !== false )
{
	$items = explode("\n", $data);
    $regionList     = array();
    $subregionList  = array();
    
	foreach( $items as $v )
	{
		$value = str_replace("&nbsp;","",$v);
        $arrayIndex = trim($value);
        
        if( strpos($v,'&nbsp;') !== false ) {
            $locName = ('en' == $currentLangCode) ? '&nbsp;&nbsp;&nbsp;' . ucwords($v) : 
                       '&nbsp;&nbsp;&nbsp;' . $allLocations[$arrayIndex]->{$locationString};
            if(empty($locName)) {
                $locName = ucwords($v); 
            }           
            $subregionList["$arrayIndex"] = $locName;
        } else {
            $locName = ('en' == $currentLangCode) ? ucwords($v) : 
                       $allLocations[$arrayIndex]->{$locationString};
            
            if ('fr' == $currentLangCode && !empty($subregionList)) {
                setlocale("LC_ALL", "fr_FR.utf8");
                asort($subregionList, SORT_LOCALE_STRING);
            }else if ('es' == $currentLangCode) {
                setlocale("LC_ALL", "es_ES.utf8");
                asort($subregionList, SORT_LOCALE_STRING);
            }

            $regionList = array_merge($regionList, $subregionList);
            $subregionList = array();
            
            if(empty($locName)) {
                $locName = ucwords($v); 
            }
            $regionList["$value"] = $locName;
        }
    }
    
    foreach ($regionList as $value => $locName) {
        $options_region .= '<option value="' . $value . '">' . $locName . '</option>';
    }
}

if( !empty( $options_region ) && !empty( $options_country ) )
{
	require( JModuleHelper::getLayoutPath('mod_gpo_find') );
}