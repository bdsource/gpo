<?php

defined('_JEXEC') or die('Restricted access');
require_once( dirname(__FILE__) . DS . 'helper.php' );
jimport('joomla.html.parameter');

$lang    = JFactory::getLanguage();
$langTag = $lang->getTag();
$currentLangCode = (strlen($langTag) > 2) ? strtolower(substr($langTag, 0, -3)) : $langTag;

$_module = &JModuleHelper::getModule('mod_gpo_sponsors');
//$_params = new JParameter($_module->params);
$_params = new JRegistry($_module->params);
$sponser = new modgposponsorshelper();
$sponsors = $sponser->getSponsors();
//$sponsors = modgposponsorshelper::getSponsors();

if (!is_array($sponsors)) {
    return;
}
$mods = array();

// prepare variables
$mainframe = & JFactory::getApplication();
$db = &JFactory::getDBO();
$user = &JFactory::getUser();
$aid = (int) $user->get('aid', 0);

// prepare custom parameters
$options = array('style' => 'xhtml');

/*
$moduleId = 'module_id';
if (in_array($currentLangCode, array('es','fr'))) {
    $moduleId .= '_' . $currentLangCode;
}
*/

foreach ($sponsors as $row) {

    //Initialize Module ID
    $moduleId = 'module_id';
    
    if (in_array($currentLangCode, array('es','fr'))) {
       $moduleId .= '_' . $currentLangCode;
    }

    /*
    if (empty($row->module_id)) {
        continue;
    }
    */
    
    ### If FR/ES don't have data use the EN as default ###
    if (empty($row->{$moduleId})) {
        $moduleId = 'module_id';
    }
    
    if (empty($row->{$moduleId})) {
        continue;
    }

    // query for specified module then render it
    $db->setQuery('SELECT * FROM #__modules'
            . ' WHERE id = ' . (int) $row->{$moduleId} . ( $_params->get('show_unpublished') ? '' : ' AND published = 1' )
            //. ' AND access <= ' . $aid
            . ' AND client_id = ' . (int) $mainframe->getClientId(), 0, 1);
            

    $mods[] = $db->loadObject();
}

require( JModuleHelper::getLayoutPath('mod_gpo_sponsors') );
?>