<?php

/**
 * @version        $Id: controller.php 10094 2008-03-02 04:35:10Z instance $
 * @package        Joomla
 * @subpackage    Config
 * @copyright    Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
use Joomla\Event\Dispatcher as EventDispatcher;
use Joomla\CMS\Uri\Uri;



jimport('joomla.application.component.controller');
jimport( 'joomla.application.component.view');


/**
 * @package        Joomla
 * @subpackage    Config
 */
class GpoController extends JControllerLegacy
{

    var $_compontent = 'com_gpo';
    var $currentLangCode;
    var $locationString = 'name';
    var $allowedLangs   = array('es','fr');

    function __construct()
    {
        parent::__construct();
        //error_reporting(0);
        //error_reporting(E_ALL);
        $lang = JFactory::getLanguage();

        $this->logged_in = false;
        $this->oUser     =  JFactory::getUser();

        if (!empty($this->oUser->id)) {
            $this->is_member = true;
        } else {
            $this->is_member = false;
        }
        
        $langTag = $lang->getTag();
        if( strlen($langTag) >2 ) {
            $this->currentLangCode = strtolower(substr($langTag,0,-3));
        }
        
        if( in_array($this->currentLangCode, $this->allowedLangs) ) {
            $this->locationString = 'name_' . $this->currentLangCode; 
        }
        
        $this->registerTask('', 'home');
    }
    

    function latest()
    { 
        
        include_once(JPATH_BASE . '/components/com_gpo/helpers/citation.php');

        $model = & $this->getModel('Sphinxsearch');
        $model->frontend();

        if ($model->isReIndexInProgress() && empty($model->articles)) {
            $filename = JPATH_BASE . '/components/com_gpo/cache/news.rebuild.txt';
            $html = file_get_contents($filename);
            echo $html;
            return;
        }


        $dates = array();
//print_r($model->articles[0]);die();
        foreach ($model->articles as $item) {
            $key = $item->published_hash;
            if (!isset($dates[$key])) {
                $dates[$key] = array();
            }
            $dates[$key][] = $item;
        }

//print_r($dates[20230706]);die();
krsort($dates);



        $items = array();
        foreach ($dates as $date => $d_items) {
            $locations = array();
            $location_order = array();
            foreach ($d_items as $item) {
                @list($location, $null) = explode(",", $item->locations, 2);
                $location_order[md5($location)] = $location;
                $locations[md5($location)][] = $item;
            }
            asort($location_order);
            foreach ($location_order as $key => $v) {
                if (isset($locations[$key])) {
                    foreach ($locations[$key] as $item) {
                        $items[] = $item;
                    }
                }
            }
        }

        $view = & $this->getView('Latest', 'html');
        $view->logged_in = $this->is_member;
       
        $view->results = &$items;
        $view->pagination = &$model->pagination;
        $view->display();
    }

    function home()
    {
//die('home'); 
        $factsUrl = JURI::base() . 'firearms/region';
        $newsUrl  = JURI::base()  . 'firearms/latest';

        $model =  $this->getModel('Sphinxsearch');
       
        $model->frontend();

        $dates = array();
        if (count($model->articles) > 0) {
            foreach ($model->articles as $item) {
                $key = $item->published_hash;
                if (!isset($dates[$key])) {
                    $dates[$key] = array();
                }
                $dates[$key][] = $item;
            }
        }

        $items = array();
        foreach ($dates as $date => $d_items) {
            $locations = array();
            $location_order = array();
            foreach ($d_items as $item) {
                @list($location, $null) = explode(",", $item->locations, 2);
                $location_order[md5($location)] = $location;
                $locations[md5($location)][] = $item;
            }
            asort($location_order);
            foreach ($location_order as $key => $v) {
                if (isset($locations[$key])) {
                    foreach ($locations[$key] as $item) {
                        $items[] = $item;
                    }
                }
            }
        }

        $view =  $this->getView('Home', 'html');

       // $view->assign('logged_in', $this->is_member);
        //$view->assignRef('results', $items);
        
        $view->factsUrl = &$factsUrl;
        $view->newsUrl =&$newsUrl;

        //load the featured charts
        $view->featuredcharts = &getFeaturedCharts($this->currentLangCode);
        $view->display();


    }

    function search()
    {
        //die();
        $shared_functions = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'helper' . DS . 'shared_functions.php';
        include_once( $shared_functions );

        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        $show   = Joomla\CMS\Factory::getApplication()->getInput()->get('view');
        $show = strtolower($show);
//show=home
        switch ($show) {
            case 'help':
            case 'nowebsource':
                $view = & $this->getView('Search', 'html');
                $view->$show();
                exit();
                break;
        }
        unset($show);

        $doRedirect = false;
        $country = '';
        $region = '';

     

        if (isset($_GET['country'])) {
            $country = $_GET['country'];
            $doRedirect = true;
        }
        if (isset($_GET['region'])) {
            $region = $_GET['region'];
            $doRedirect = true;
        }

        if ($doRedirect) {
            $location = (!empty($country)) ? $country : $region;
            $location = urlencode($location);
//echo $location;die();
            $q = urlencode($_GET['q']);
            $url = JRoute::_('index.php?option=com_gpo&task=search', false);
            //$url .= '?q=' . $q . '&l=' . $location;
            $url .= '&q=' . $q . '&l=' . $location;
//echo $url; die();   
//index.php?option=com_gpo&task=search&lang=en?q=gun&l=Argentina
            $this->setRedirect($url);
        }

        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        $q             = Joomla\CMS\Factory::getApplication()->getInput()->get('q','','string');
        $location      = Joomla\CMS\Factory::getApplication()->getInput()->get('l','','string'); 
//echo $location; die();
        //$location      = Joomla\CMS\Factory::getApplication()->getInput()->get('location');  
        /*
         * A fix for pagination not working with certain characters due to how Joomla urldecodes before using standard php functions.
         */
        $router = & JRouter::getInstance('site');
        $router->setVar('l', $location, true);
        $router->setVar('q', $q, true);
        $router->setVar('task', Joomla\CMS\Factory::getApplication()->getInput()->get('task'), true);
        $router->setVar('option', Joomla\CMS\Factory::getApplication()->getInput()->get('option'), true);
        
        $model = & $this->getModel('Sphinxsearch'); //<---!
//echo $location; echo '------'; echo $q; die();        
//echo $location;echo '--test';die();       
//echo 'd';die();
        $model->frontend($q, $location);//<--!
//echo('ooooo');print_r($model->articles);die();
        if ($model->isReIndexInProgress() && empty($model->articles)) {
            //echo '|';die();
            $filename = JPATH_BASE . '/components/com_gpo/cache/news.rebuild.txt';
            $html = file_get_contents($filename);
            echo $html;
            return;
        }
        require_once(JPATH_BASE . '/components/com_gpo/helpers/citation.php');
        $allLocations = GpoGetAllLocationNames();
        
        $view = & $this->getView('Search', 'html');
        $view->query = &$model->q;
        $view->location =&$model->location;
        $view->allLocations=&$allLocations;
        $view->locationString =&$this->locationString;
        $view->logged_in=&$this->is_member;
        $view->results=&$model->articles;
        $view->pagination=&$model->pagination;

        $view->display();
    }

    function msearch()
    {
        $shared_functions = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'helper' . DS . 'shared_functions.php';
        include_once( $shared_functions );
        require_once(JPATH_BASE . '/components/com_gpo/helpers/citation.php');

        if ($this->is_member !== true || $_GET['cmd'] !== 'advanced') {
            $doRedirect = false;
            $country = '';
            $region = '';
            if ($this->is_member !== true) {
                $doRedirect = true;
            }
            if (empty($_GET['l'])) {
                if (!empty($_GET['country'])) {
                    $country = $_GET['country'];
                    $doRedirect = true;
                }
                if (!empty($_GET['region'])) {
                    $region = $_GET['region'];
                    $doRedirect = true;
                }
                $location = (!empty($country)) ? $country : $region;
            } else {
                $location = $_GET['l'];
            }

            if ($doRedirect) {
                $location = urlencode($location);
                $q = urlencode($_GET['q']);
                $url = JRoute::_('index.php?option=com_gpo&task=search', false);
                $url .= '?q=' . $q . '&l=' . $location;
                $this->setRedirect($url);
                $this->redirect();
            }
        }


        $id = $_GET['id'];
        if (!empty($id)) {
            if (ctype_digit($id)) {
                $model = & $this->getModel('News');
                if (!$this->is_member) {
                    $item = $model->getById($id);
                } else {
                    $item = $model->getById($id, '0');
                }

                if (!empty($item)) {
                    $url = JRoute::_('index.php?option=com_gpo&task=news&id=' . $id);
                    $this->setRedirect($url);
                    $this->redirect();
                }
            }
        }

        /*
         * A fix for pagination not working with certain characters due to how Joomla urldecodes before using standard php functions.
         */
        $keys = explode(",", 'share,many,country,region,category,content,title,subtitle,source,byline,keywords,fromdate,todate,id,gpnheader,Itemid');
        //		$new_vars = array();

        $router = & JRouter::getInstance('site');
        foreach ($keys as $key) {
            if (!empty($_GET[$key])) {
                $router->setVar($key, $_GET[$key], true);
            }
        }
        $router->setVar('task', Joomla\CMS\Factory::getApplication()->getInput()->get('task'), true);
        $router->setVar('option', Joomla\CMS\Factory::getApplication()->getInput()->get('option'), true);


        $model = & $this->getModel('Sphinxsearch');
        if ($model->isReIndexInProgress()) {
            $filename = JPATH_BASE . '/components/com_gpo/cache/news.rebuild.txt';
            $html = file_get_contents($filename);
            echo $html;
            return;
        }

        //change this to search[''];
        $model->members($_GET);
        //display results
        $view = & $this->getView('Search', 'html');
        $view->logged_in = $this->is_member;
        $view->results=&$model->articles;
        $view->pagination=&$model->pagination;
        if (empty($model->articles)) {
            $view->members();
        } else {
            $view->display();
        }
        return;
    }

    /**
     * This method will do two main things.
     * 1. It will show the list of citations started with an alphabet.
     * 2. It will show the citation details
     *
     * Explanation:
     * When the $id is numeric, it will do the 2nd job. However, if the $id is not numeric, it will do the first job!
     */
    function citation()
    {
        //global $mainframe;
        $mainframe =& Joomla\CMS\Factory::getApplication();
        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        $id   = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
        $type   = Joomla\CMS\Factory::getApplication()->getInput()->get('type');

        $model = & $this->getModel('Citation');
        $model->is_member = $this->is_member;
        $view = & $this->getView('Citation', 'html');

        //load the helper file
        require_once(JPATH_BASE . '/components/com_gpo/helpers/citation.php');
        $view->logged_in=$this->is_member;
        if ($model->isType($type)) {
            $model->type = $type;
        }
        if (empty($type)) {
            $view->citation_home();
        } else {
            if ($id AND is_numeric($id)) { //it is single citation view
                $citation = null;
                if ($model->isType($type)) {
                    $model->type = $type;
                    $citation = $model->getById($id);
                }
                
                //put code for plugins to parse qcite, ncite using citation maker plugin
                $citation->content = ('q' == $citation->type) ? htmlspecialchars($citation->content) : $citation->content;
                $article = (object)array(
                    'text' => & $citation->content,
                );
                JPluginHelper::importPlugin('gpo');
                /*$dispatcher = & JDispatcher::getInstance();
                $dispatcher->trigger('onAfterDisplayContent', array(&$article));*/

                $res = Joomla\CMS\Factory::getApplication()->triggerEvent('onAfterDisplayContent', array(&$article));
                
                //update the main citation object to include citations information
                $citation->citations = $article->citations;
                $citation->footer    = $article->footer;

                $view->citation=&$citation;
                $view->type=&$type;
//print_r($citation);die();
                $view->alphabet=&$citation->title; //assign
                $view->display();
            } else if ($id AND !is_numeric($id)) { //alphabet
                //we got an alphabet, so now fetch all citations started with this character
                $citations = $model->getByChar($id);
                $view->citations=&$citations;
                $view->type=&$type;
                $view->alphabet=&$id;
                $view->archive();
            } else {
                $view->type=&$type;
                $view->archive();
            }
        }
        exit();
    }

    function rss()
    {  
        include_once(JPATH_BASE . '/components/com_gpo/helpers/gpo.php');
        $mRss = & $this->getModel('Rss');
        $mRss->logged_in = $this->is_member;
        $allLocations = GpoGetAllLocationNames();
       
        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        $view = Joomla\CMS\Factory::getApplication()->getInput()->get('view', false, 'string');
        
        if ($view === 'go') {
            $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
            $url = $mRss->go2($id);
            $this->setRedirect($url);
            $this->redirect();
            return;
        } elseif (in_array($view, array('make', 'design:your-own-updates', 'design-your-own-updates'))) {
            $k = Joomla\CMS\Factory::getApplication()->getInput()->get('keyword', false);
            $l = Joomla\CMS\Factory::getApplication()->getInput()->get('location', '', 'string');
            if (empty($l)) {
                $l = Joomla\CMS\Factory::getApplication()->getInput()->get('country', '', 'string');
            }

            $k = trim($k);
            $l = trim($l);

            if (!empty($l) || !empty($k)) {
                //$url = JURI::base() . ltrim(JRoute::_('index.php?option=com_gpo&task=rss&k=' 
                //                    . rawurlencode($k) . '&l=' . urlencode($l)), "/");

                $url = JURI::base() . ltrim(JRoute::_('index.php?option=com_gpo&task=rss&k=' 
                                    . rawurlencode($k)), "/") . '&l=' . urlencode($l);
            } else {
                $url = '';
            }

            $data = explode("\n", GpoGetTypeFromCache('public_country'));
            $options_country = '';
            $countryList = array();
            if (!empty($data)) {
                foreach ($data as $v) {
                    $value = str_replace("&nbsp;", "", $v);
                    $locName = ('en' == $this->currentLangCode) ? ucwords($v) :
                            $allLocations[trim($v)]->{$this->locationString};
                    if (empty($locName)) {
                        $locName = ucwords($v);
                    }
                    $countryList[$value] = $locName;
                }
                $countryList = sortLocationNames($countryList, $this->currentLangCode, TRUE);
                foreach ($countryList as $value => $locName) {
                     $options_country .= '<option value="' . $value . '">' . $locName . '</option>';
                }
            }

            $data = explode("\n", GpoGetTypeFromCache('public_region'));
            $options_region = '';
            $regionList     = array();
            $subregionList  = array();
            
            if (!empty($data)) {
                foreach ($data as $v) {
                    $value = str_replace("&nbsp;", "", $v);
                    $arrayIndex = trim($value);
                    //$options_region .= '<option value="' . $value . '">' . ucwords($v) . '</option>';
                    if (strpos($v, '&nbsp;') !== false) {
                        $locName = ('en' == $this->currentLangCode) ? '&nbsp;&nbsp;&nbsp;' . ucwords($v) :
                                '&nbsp;&nbsp;&nbsp;' . $allLocations[$arrayIndex]->{$this->locationString};
                        if (empty($locName)) {
                            $locName = ucwords($v);
                        }
                        $subregionList["$arrayIndex"] = $locName;
                    } else {
                        $locName = ('en' == $this->currentLangCode) ? ucwords($v) :
                                $allLocations[$arrayIndex]->{$this->locationString};
                        if (empty($locName)) {
                            $locName = ucwords($v);
                        }

                        if(!empty($subregionList)) {
                           $subregionList = sortLocationNames($subregionList, $this->currentLangCode, false);
                        }
                        
                        $regionList = array_merge($regionList, $subregionList);
                        $subregionList = array();
                        $regionList["$value"] = $locName;
                    }
                }
                
                foreach ($regionList as $value => $locName) {
                        $options_region .= '<option value="' . $value . '">' . $locName . '</option>';
                }
            }

            $view = & $this->getView('Rss', 'html');
            $view->url=&$url;
            $view->options_country=&$options_country;
            $view->options_region=&$options_region;

            $view->display();
        } else {
            //does cache exist
            $k = Joomla\CMS\Factory::getApplication()->getInput()->get('k', false, 'string');
            $l = Joomla\CMS\Factory::getApplication()->getInput()->get('l', false, 'string');

            $k = trim($k);
            $l = trim($l);
            if (strtolower($l) === 'world') {
                $l = '';
            }

            $mRss->search =  $this->getModel('Sphinxsearch');
            header ( "Content-type:text/xml; charset=utf-8" );
            $mRss->showFeed($k, $l);
            exit();
        }
    }

    function topic()
    {    //die("topic"); 
        //#fix use the format for JOOMLA like that found in shared_functions
        require_once(JPATH_BASE . '/administrator/components/com_gpo/helper/topic.php');
        require_once(JPATH_BASE . '/components/com_gpo/helpers/citation.php');
        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false, 'string'); 

        $id = strtolower($id);
        $topic = new GpoTopic($id);
        $id = $topic->get("id");

        if (empty($id)) {
            $url = JURI::base() . 'topics';
            $this->setRedirect($url);
            $this->redirect();
        }

        $_GET = json_decode($topic->get('search'), true);
        $_GET['fromdate'] = $_GET['published_range']['from'];
        $_GET['todate'] = $_GET['published_range']['to'];
        $_GET['l'] = $_GET['locations'];

        $is_rss = Joomla\CMS\Factory::getApplication()->getInput()->get('rss', false, 'string'); 
        
        if ($is_rss === '1') {
            $mRss = & $this->getModel('Rss');
            $mRss->search = & $this->getModel('Sphinxsearch');
            $mRss->topic($topic);
            exit();
        }
        $model = & $this->getModel('Sphinxsearch');
      
        if ($model->isReIndexInProgress()) {
            $filename = JPATH_BASE . '/components/com_gpo/cache/news.rebuild.txt';
            $html = file_get_contents($filename);
            echo $html;
            return;
        }

        $model->limit = 200;
        $model->cl->SetLimits((int)$model->start, (int)$model->limit, $model->max_matches);
        $model->members($_GET);


        if (!empty($model->articles)) {
            $document = JFactory::getDocument();
            $href = JRoute::_('index.php?option=com_gpo&task=topic&id=' . rawurlencode($topic->get('seo')) . '&rss=1', true);
            $title = $title = 'Gun Policy News ( ' . $topic->get('page_headline') . ' )';
            $document->addHeadLink($href, 'alternate', $relType = 'rel', array('type' => 'application/rss+xml', 'title' => $title));
        }
        $view = $this->getView('Topic', 'html');
        $view->topic=&$topic;
        $view->results=&$model->articles;
        $view->display();
    }

    /*
     * Preview DP feature for 
     * Admin Panel
     * 
     */
    function preview() { //die("preview"); 
        $jinput =  Joomla\CMS\Factory::getApplication()->getInput();
        $mainframe = &  Joomla\CMS\Factory::getApplication();
        $this->oUser = & JFactory::getUser();
        $isRoot    = $this->oUser->get('isRoot');
        ## For members show the unpublished DP too,
        ## 7 and 8 is administrator and super administrator
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $this->isAdministrator = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
      
        
//        if( empty($this->oUser->id) ) {
//            echo "<H1> You need to Login in the Frontend to preview this DP </H1>";
//            return false;
//        }
        
//        if( !$this->isAdministrator ) {
//            echo "<H1> You need to have admin privilege to use this feature </H1>";
//            return false;
//        }
        
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'datapage.php');
        $model = & $this->getModel('Region');
        $datapage = new DatapageHelper();

        $location = str_replace(' and ', '&', urldecode(Joomla\CMS\Factory::getApplication()->getInput()->get('location', false)));
        $group    = Joomla\CMS\Factory::getApplication()->getInput()->get('group', false);
        
        if (empty($location)) {
            $location = $group;
        }
        
        if( empty($group) ) {
            $item = $model->locationExists($location);
            //catid is used as its required by the trickery in helpers/route.php
            $url = JRoute::_('index.php?option=com_gpo&task=region&region=' . $item->catid, true);
        }else {
            $item = $datapage->isGroupExists($group);
            $url = JRoute::_('index.php?option=com_gpo&task=group&id=' . $item->id, true);
        }

        $this->setRedirect($url);
        
    }
    
    function region()
    {  
        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        //ini_set('display_error',1);
        //error_reporting(E_ALL);

        $mainframe =  Joomla\CMS\Factory::getApplication();
        $user      =  Joomla\CMS\Factory::getUser();
        $isRoot    = $user->get('isRoot');
     
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'icon.php');
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'datapage.php');
        JPluginHelper::importPlugin('gpo');
        $compareModel = $this->getModel('Compare','', array($this->currentLangCode));
    
        $this->oUser =  Joomla\CMS\Factory::getUser();
        ## For members show the unpublished DP too,
        ## 7 and 8 is administrator and super administrator
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $this->isAdministrator = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
        $ignoreDPPublishFlag = $this->isAdministrator;


        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
        $model        =  $this->getModel('Region');
        $view         =  $this->getView('Region', 'html');
       // $dispatcher   = & JDispatcher::getInstance();

        //get datapage related information
        $dp  = new DatapageHelper();
        $uri1 = Uri::getInstance();
        //$uri = urldecode($uri1);
        $uri = $uri1->toString();
 //echo $uri;die();
    
        $regionAlias = $dp->getRegionAliasFromURI($uri); //does not return correct value why 
        $regionObj   = $dp->getRegionNameByAlias($regionAlias,$ignoreDPPublishFlag);

        $regionName  = @$regionObj->name;
        $regionId    = @$regionObj->id;
        $datapageUrl = $dp->getDatapageCpLink($regionName, $regionAlias);
//echo $datapageUrl; die();
        //article is info
        $article = $model->intro($id, $regionName);
        $narrativeState = $dp->isNarrativePageAvailable($article->state, $article->access);
//echo $narrativeState; die(); //ukraine 1 , united-states false

        $isCountryDPAvailable = $dp->isDatapageExists($regionName,$ignoreDPPublishFlag);
        
        //get all region locations
        $regionLocations = $model->getAllLocationsByRegion($regionId);
//print_r($regionAlias); die();
        //we'll now show the DP by summing up the data for the admins only
        if( in_array($article->location_type, array('region','subregion')) && $isRoot ) {
           //$dp->isDatapageExists($regionName); 
           $isRegionDPAvailable = TRUE;
           $isRegion = TRUE;
           $isCountryDPAvailable = FALSE;
        }
       
        if ($isCountryDPAvailable) {
            ## whatever the article info is... overwrite article text again with the datapage texts
            $dp_data    = $dp->getDPByLocation($regionName, $ignoreDPPublishFlag);
            if ($dp_data) {
                $DPHtml = $dp->getDPText($regionName, $dp_data, $narrativeState);
            }
            
            if (!empty($DPHtml)) {
                $article->text = $DPHtml;
            }
        }else if ($isRegionDPAvailable && $isRegion) {
            ## get aggregated Regeion DP  
            ## whatever the article info is... overwrite article text again with the datapage texts
            $dp_data    = $dp->getDPByRegion($regionLocations);
            if ($dp_data) {
                $DPHtml = $dp->getRegionDPText($regionName, $dp_data, $regionId, $narrativeState);
            }
            
            if (!empty($DPHtml)) {
                $article->text = $DPHtml;
            }
        }
        
        if ($isCountryDPAvailable || $isRegionDPAvailable) {
            ##Modified Date
            ##if modified date is NULL, use today's date
            if( in_array($article->location_type,array('region','subregion')) ) {
                $regionLastModified = $compareModel->getRegionLastModifiedDate($regionId);
                $article->modified = (!$dp->isNullDate($regionLastModified)) ? $regionLastModified : date('Y-m-d H:i:s');
            }else {
                $article->modified = (!$dp->isNullDate($dp_data->updated_at)) ? $dp_data->updated_at : date('Y-m-d H:i:s');
            }
         
            $article->title = $dp->getDPMetaTitle($regionName, TRUE, $regionObj->{$this->locationString});
            $article->DPTitle = $dp->getDPPageTitle($regionObj->{$this->locationString});
        }

        //show 'view datapage' link in narrative pages only
        if ($narrativeState) {
            //$view->assign('datapageUrl', $datapageUrl);
           // $view->assign('regionName', $regionName);
           // $view->assign('regionID', $regionId);
            
            $view->datapageUrl = $datapageUrl;
            $view->regionName = $regionName;
            $view->regionID = $regionId;
            
            
        }
       //     $dpTabs = $dp->getDPTabs('DP', $datapageUrl);

        // $view->assignRef('regionName', $regionName);
        // $view->assignRef('regionObj', $regionObj);
        // $view->assign('locationString', $this->locationString);
        // $view->assign('currentLangCode', $this->currentLangCode);
        // $view->assignRef('article', $article);
        // $view->assign('cp', 0);
        
        $view->regionName = $regionName;
        $view->regionObj = $regionObj;
        $view->locationString = $this->locationString;
        $view->currentLangCode = $this->currentLangCode;
        $view->article = $article;
        $view->cp = 1;


        //check to see if the content is an index
        //if it is get locations and articles
        if (strpos($article->alias, '-index') !== false || strpos($article->alias, 'staff-notes') !== false) {
          
          if ($article->alias !== 'region-index')
          {

                $location = $model->getLocationInfo($article->catid);
               // $view->assignRef('location', $location);
                $view->location = $location;
                $locations = $model->locationsById($location->id);
                
                if( in_array($this->currentLangCode, $this->allowedLangs) ) {
                    $article->title = $dp->getDPMetaTitle($regionName, TRUE, $regionObj->{$this->locationString});
                }
                
                if ($article->state === "1" && strpos($article->alias, 'staff-notes') === false) {
                    $articles = $model->getArticles($location->id, $article->catid);
                    //$view->assignRef('articles', $articles);
                    $view->articles = $articles;
                }
           } else {

                $locations = $model->getRegions();
                $article->title = JText::_('COM_GPO_REGION_INDEX_TITLE');
           }

            //$view->assignRef('locations', $locations);
            $view->locations = $locations;
           $breadcrumbs = $model->getBreadCrumbs($location->id);
          // $view->assignRef('breadcrumbs', $breadcrumbs);
           $view->breadcrumbs = $breadcrumbs;
        }
        
        /* 
         * we do this to get the citations for the whole page  
         * Process the prepare content plugins
         */

        $limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', false);
        $params     =  $mainframe->getParams('com_content');
       // $dispatcher->trigger('onPrepareContent', array(&$article, &$params, $limitstart));
       //$dispatcher->trigger('onAfterDisplayContent', array(&$article, &$params, $limitstart));
       
        JFactory :: getApplication () -> triggerEvent ('onPrepareContent', array(&$article, &$params, $limitstart));
        JFactory :: getApplication () -> triggerEvent ('onAfterDisplayContent', array(&$article, &$params, $limitstart));
        


        $view->display();
    }

//end function region


    function group()
    {     die("group");
        error_reporting(1);
        $mainframe =& Joomla\CMS\Factory::getApplication();
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'icon.php');
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'datapage.php');
        $compareModel = $this->getModel('Compare','', $this->currentLangCode);
        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        
        $groupName    = Joomla\CMS\Factory::getApplication()->getInput()->get('groupName', false);
        $groupName    = str_replace(':', ' ', $groupName);
        
        $model        =  $this->getModel('Region');

        $view         =  $this->getView('Group', 'html');
        $dispatcher   =  JDispatcher::getInstance();

        JPluginHelper::importPlugin('gpo');
        //get datapage related information
        $dp           = new DatapageHelper();
        
        //article is info
        $article               = $model->intro(NULL,'group');
        $groupDetails          = $dp->getGroupByName($groupName);
        $groupID               = $groupDetails['id'];
        $groupNameForDisplay   = $groupDetails['name'];
        $narrativeState        = false;
        if (!empty($groupDetails)) {
            //whatever the article info is... overwrite article text again with the datapage texts
            $dp_data       = $dp->getDPByGroup($groupID);
            if ($dp_data) {
                $DPHtml = $dp->getGroupDPText($groupNameForDisplay, $dp_data, $groupID, $narrativeState);
            }
            if (!empty($DPHtml)) {
                $article->text = $DPHtml;
            }
            
            $article->modified = $compareModel->getGroupLastModifiedDate($groupID);
            $article->title = $dp->getDPMetaTitle($regionName);
            $article->DPTitle = $dp->getDPPageTitle($regionName);
            $article->metadesc = "Country comparisons, violence prevention investment news, development and public health impacts, firearms, gun control and ".$groupNameForDisplay.".";
            $article->metakey  = "$groupNameForDisplay, armed violence, reduction, prevention, development, donor, ODA, OECD,  investment, gender, gun, firearm, crime, conflict, death, injury, public health, gun control, peace, news";
        }

        //show 'view datapage' link in narrative pages only
        if ($narrativeState) {
            
            $view->datapageUrl=$datapageUrl;
            $view->regionName=$regionName;
            $view->regionID=$regionId;
            
            
            
        }
        
        $view->locationString=$this->locationString;
        $view->currentLangCode=$this->currentLangCode;
        $view->regionName=&$regionName;
        $view->article=&$article;
        $view->assign('cp', 0);

        //check to see if the content is an index
        //if it is get locations and articles
        if (strpos($article->alias, '-index') !== false || strpos($article->alias, 'staff-notes') !== false) {
          
            //if ($article->alias !== 'region-index') {

                $location = $model->getLocationInfo($article->catid);
                $view->location=&$location;
                $locations = $model->locationsById($location->id);

                if ($article->state === "1" && strpos($article->alias, 'staff-notes') === false) {
                    $articles = $model->getArticles($location->id, $article->catid);

                    $view->articles=&$articles;
                }
            //} else {
            //    $locations = $model->getRegions();
            //}

            $view->locations=&$locations;
            $breadcrumbs = $model->getBreadCrumbs($location->id);
            //$view->assignRef('breadcrumbs', $breadcrumbs);
        }
        //we do this to get the citations for the whole page
        /*  
         * Process the prepare content plugins
         */

        $limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', false);

        $params = & $mainframe->getParams('com_content');

        $dispatcher->trigger('onPrepareContent', array(&$article, &$params, $limitstart));
       
        $dispatcher->trigger('onAfterDisplayContent', array(&$article, &$params, $limitstart));

        $view->display();
    }
    

function cats() {  die("cats");
        //global $mainframe;
        $mainframe =& Joomla\CMS\Factory::getApplication();

        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'icon.php');
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'datapage.php');
        
        $apikey = Joomla\CMS\Factory::getApplication()->getInput()->get('apikey', false);
        //get datapage related information
        $dp = new DatapageHelper();
  
        if('ad67895v' == $apikey) {
        $colList = $dp->getDPColumnsInfo();
        
        $message['status'] = true;
        //$message['error_message'] = 'Status Ok';
        $message['categories'] = array_slice($colList, 0,10);
        }else {
 $message['status'] = false;
        $message['error_message'] = 'Permission denied, Apikey mismatch';
//        $message['categories'] = array_chunk($colList, 10);
}

        $message = json_encode($message);
        echo $message;
        flush();

        JFactory::getApplication()->close();
        
    }
   
    /*
    * the CP(Article) page of the regions
    *
    */

    function cp()
    { //die("cp");
      
        //global $mainframe;
        $mainframe =& Joomla\CMS\Factory::getApplication();

        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'icon.php');
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'datapage.php');

        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);

        $this->oUser = & JFactory::getUser();
        ## For members show the unpublished DP too,
        ## 7 and 8 is administrator and super administrator
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $this->isAdministrator = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
        $ignoreDPPublishFlag = $this->isAdministrator;
        $isRoot    = $this->oUser->get('isRoot');

        $model = & $this->getModel('Region');
        $view = & $this->getView('Region', 'html');
       // $dispatcher = & JDispatcher::getInstance();
        JPluginHelper::importPlugin('gpo');

        $uri1 = Uri::getInstance();
        $uri = urldecode($uri1);
        
        //get datapage related information
        $dp = new DatapageHelper();
        $regionAlias = $dp->getRegionAliasFromURI($uri);
        $regionObj = $dp->getRegionNameByAlias($regionAlias,$ignoreDPPublishFlag);
        $regionName = $regionObj->name;
        $datapageUrl = $dp->getDatapageLink($regionName, $regionAlias, $ignoreDPPublishFlag);

        //article is info
        $article = $model->intro($id, $regionName);
        //add different browser title for the datapages
        $article->title = $dp->getDPMetaTitle($regionName,TRUE,$regionObj->{$this->locationString});
        $article->DPTitle = $dp->getDPPageTitle($regionObj->{$this->locationString});
        
        //show 'view datapage' link in narrative pages only
        $narrativeState = $dp->isNarrativePageAvailable($article->state, $article->access);
        $isCountryDPAvailable = $dp->isDatapageExists($regionName,$ignoreDPPublishFlag);
        
        
        //we'll now showing the Region DPs by summing up the data for the admins only
        if(in_array($article->location_type,array('region','subregion')) && $isRoot ) {
           //$dp->isDatapageExists($regionName); 
           $isRegionDPAvailable = TRUE;
           $isRegion = TRUE;
           $isCountryDPAvailable = FALSE;
        }

        if ($narrativeState) {
            $view->datapageUrl=$datapageUrl;
            $view->regionName=$regionName;
        }

        if ($isCountryDPAvailable && $narrativeState) {
            $dpTabs = $dp->getDPTabs('CP', $datapageUrl);
            $article->text = $dpTabs . $article->text;
        }else if($isRegionDPAvailable && $narrativeState && $isRegion) {
            $dpTabs = $dp->getDPTabs('CP', $datapageUrl);
            $article->text = $dpTabs . $article->text;
        }

        $view->regionObj=&$regionObj;
        $view->locationString=$this->locationString;
        $view->currentLangCode=$this->currentLangCode;
        $view->article=$article;
        $view->cp=1;

        //check to see if the content is an index
        //if it is get locations and articles
        if (strpos($article->alias, '-index') !== false || strpos($article->alias, 'staff-notes') !== false) {
            if ($article->alias !== 'region-index') {
                $location = $model->getLocationInfo($article->catid);
                $view->location=&$location;
                $locations = $model->locationsById($location->id);

                if( in_array($this->currentLangCode, $this->allowedLangs) ) {
                    $article->title = $dp->getDPMetaTitle($regionName, TRUE, $regionObj->{$this->locationString});
                }

                if ($article->state === "1" && strpos($article->alias, 'staff-notes') === false) {
                    $articles = $model->getArticles($location->id, $article->catid);
                    $view->articles=&$articles;
                }
            } else {
                $locations = $model->getRegions();
                $article->title = JText::_('COM_GPO_REGION_INDEX_TITLE');
            }
            
            $view->locations=&$locations;
            $breadcrumbs = $model->getBreadCrumbs($location->id);
            $view->breadcrumbs=&$breadcrumbs;
        }
        //we do this to get the citations for the whole page
        /*
         * Process the prepare content plugins
         */

        $limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart');
        $params = & $mainframe->getParams('com_content');
        //$dispatcher->trigger('onPrepareContent', array(&$article, & $params, $limitstart));
       // $dispatcher->trigger('onAfterDisplayContent', array(&$article, & $params, $limitstart));
       
        $res1 = Joomla\CMS\Factory::getApplication()->triggerEvent('onPrepareContent', array(&$article, & $params, $limitstart));
        $res2 = Joomla\CMS\Factory::getApplication()->triggerEvent('onAfterDisplayContent', array(&$article, & $params, $limitstart));

        $view->display();
    }

//end function CP

    /*
     * Deprecated method,
     * Not in use now
     * 
     */
    function datapage()
    { die("datapage");
        //global $mainframe;
        $mainframe =& Joomla\CMS\Factory::getApplication();

        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'icon.php');
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'datapage.php');
        
        $this->oUser = & JFactory::getUser();
        ## For members show the unpublished DP too,
        ## If we fix the Preview DP button in admin panel, then we'll remove this
        ## 7 and 8 is administrator and super administrator
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $this->isAdministrator = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
        $ignoreDPPublishFlag = $this->isAdministrator;
          $uri1 = Uri::getInstance();
        $uri = urldecode($uri1);      
        
        $id =  Joomla\CMS\Factory::getApplication()->getInput()->get('id');;
        $model = & $this->getModel('Region');
        $view = & $this->getView('Region', 'html');
        $dispatcher = & JDispatcher::getInstance();
        JPluginHelper::importPlugin('gpo');

        //get datapage related information
        $dp = new DatapageHelper();
        $regionAlias = $dp->getRegionAliasFromURI($uri);
        $regionObj = $dp->getRegionNameByAlias($regionAlias,$ignoreDPPublishFlag);
        $regionName = $regionObj->name;

        //article is info; get the datapage
        $article = $model->intro($id, $regionName);

        //whatever the article info is... overwrite article text again with the datapage texts
        $dp_data = $dp->getDPByLocation($regionName,$ignoreDPPublishFlag);
        $narrativeState = $dp->isNarrativePageAvailable($article->state, $article->access);
        if ($dp_data)   {
            $DPHtml = $dp->getDPText($regionName, $dp_data, $narrativeState);
        }
        //$article = new stdClass();
        if (!empty($DPHtml)) {
            $article->text = $DPHtml;
        }
        
        //if modified date is NULL, use today's date
        $article->modified = (!$dp->isNullDate($dp_data->updated_at)) ? $dp_data->updated_at : date('Y-m-d H:i:s');
        $article->title = $dp->getDPMetaTitle($regionName,TRUE,$regionObj->{$this->locationString});
        $article->DPTitle = $dp->getDPPageTitle($regionObj->{$this->locationString});

        $view->article=&$article;
        $view->regionObj=&$regionObj;
        $view->locationString=$this->locationString;
        $view->currentLangCode=$this->currentLangCode;

        //check to see if the content is an index
        //if it is get locations and articles
        if (strpos($article->alias, '-index') !== false || strpos($article->alias, 'staff-notes') !== false) {
            if ($article->alias !== 'region-index') {
                $location = $model->getLocationInfo($article->catid);
                $view->location=&$location;
                $locations = $model->locationsById($location->id);
                
                if( in_array($this->currentLangCode, $this->allowedLangs) ) {
                    $article->title = $dp->getDPMetaTitle($regionName, TRUE, $regionObj->{$this->locationString});
                }
                
                if ($article->state === "1" && strpos($article->alias, 'staff-notes') === false) {
                    $articles = $model->getArticles($location->id, $article->catid);
                    $view->articles=&$articles;
                }
            } else {
                $locations = $model->getRegions();
                $article->title = JText::_('COM_GPO_REGION_INDEX_TITLE');
            }
            $view->locations=&$locations;

            $breadcrumbs = $model->getBreadCrumbs($location->id);
            $view->breadcrumbs=&$breadcrumbs;
        }

        //we do this to get the citations for the whole page8
        /*
         * Process the prepare content plugins
         */

        $limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart');
        $params = & $mainframe->getParams('com_content');
        $dispatcher->trigger('onPrepareContent', array(&$article, & $params, $limitstart));
        $dispatcher->trigger('onAfterDisplayContent', array(&$article, & $params, $limitstart));

        $view->display();
    }

//end function datapage

    function news()
    {
        $shared_functions = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'helper' . DS . 'shared_functions.php';
        include_once( $shared_functions );

        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
        $model = & $this->getModel('News');
        if (!$this->is_member) {
            $item = $model->getById($id);
        } else {
            $item = $model->getById($id, '0');
        }

        require_once(JPATH_BASE . '/components/com_gpo/helpers/citation.php');

        if (empty($item)) {
            $url = JRoute::_('index.php?option=com_gpo&task=latest', true);
            $this->setRedirect($url);
        }

        $view = & $this->getView('News', 'html');
        $view->is_member=$this->is_member;
        $view->isSingleNews=true;
        $view->oNews=$item;
        if (!$this->is_member) {
            $view->display();
        } else {
            $view->membersOnly();
           // $view->display();
            exit();
        }
    }

    function news_archive()
    {
        require_once(JPATH_BASE . '/components/com_gpo/helpers/citation.php');
        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        $year   = Joomla\CMS\Factory::getApplication()->getInput()->get('y', '');
        $month  = Joomla\CMS\Factory::getApplication()->getInput()->get('m', '');
        $day    = Joomla\CMS\Factory::getApplication()->getInput()->get('d', '');

        $do = '';
        $options = array();
        if (!empty($year)) {
            $do = 'year';
            $year = date("Y", mktime(0, 0, 0, 1, 1, $year));
            if (!empty($month)) {
                $do = 'month';
                list($month_name, $month) = explode(" ", date("F n", mktime(0, 0, 0, (int)$month, 1, $year)));
                if (!empty($day)) {
                    $do = 'day';
                    $day = date("j", mktime(0, 0, 0, 1, (int)$day, $year));
                }
            }
        }

        //Cache could go here. Simple

        $model = & $this->getModel('News');

        $years = $model->getYears();

        if (!empty($do)) {
            if (!in_array($year, $years)) {
                $url = JRoute::_("index.php?option=com_gpo&task=news&id=archive", false);
                $this->setRedirect($url);
                return;
            }
        }
        $view = & $this->getView('News', 'html');
        $view->year=&$year;

        if (isset($month)) {
            $view->month=&$month;
            $view->month_name=&$month_name;

            if (isset($day)) {
                $view->day=&$day;
            }
        }


        $data = array();
        $share = ($this->is_member) ? 0 : 1;
        switch ($do) {
            case 'year':
//Get year data
                $years  = $model->getYears($year,$share);
                $months = $model->getMonthsByYear($year,$share);

                $view->years=&$years;
                $view->months=&$months;
                $view->archive_year();
                break;
            case 'month':
//Get month data
                $years = $model->getYears($year,$share);
                $days  = $model->getDaysByMonth($year, $month, $share);

                $view->years=&$years;
                $view->days=&$days;
                $view->archive_month();
                break;
            case 'day':
//Get day data
                $years = $model->getYears($year, $share);
                if ($this->is_member) {
                    $oNews = $model->getArticlesOn($year, $month, $day);
                } else {
                    $oNews = $model->getArticlesOn($year, $month, $day, 1);
                }

                $view->years=&$years;
                $view->oNews=&$oNews;

                $view->archive_day();
                break;
            default:
                $years = $model->getYears($year,$share);
                $view->years=&$years;
                $view->archive();
                break;
        }
    }

    function compareyears()
    { 
        require_once(JPATH_BASE . '/components/com_gpo/helpers/datapage.php');
        $lang = JFactory::getLanguage();
        $jinput = Joomla\CMS\Factory::getApplication()->getInput();

        $location_id = Joomla\CMS\Factory::getApplication()->getInput()->get('base_location');
        $column      = urldecode(Joomla\CMS\Factory::getApplication()->getInput()->get('column'));
        $isGroup     = Joomla\CMS\Factory::getApplication()->getInput()->get('is_group');
        $isRegion    = Joomla\CMS\Factory::getApplication()->getInput()->get('is_region');
        
        $langTag = $lang->getTag();
        if( strlen($langTag) >2 ) {
            $currentLangCode = strtolower(substr($langTag,0,-3));
        }
        
        //replace the : with - as we found it has been somehow replaced while coming through URl
        $column       = str_replace(':', '-', $column);
        $config_p['language'] = $this->currentLangCode;
        $model        = &$this->getModel('Compare','', $config_p);
        //$regionModel  = &$this->getModel('Region');
        $view         = &$this->getView('Compare', 'html');
        $tpl          = 'yearlycompare';
        $dpHelper     = new DatapageHelper();
        if ( empty( $column ) && !empty( $location_id ) ) {   	
        	$view->column_info=$column;
        	$location_info = $model->getLocationInfoBy('id', $location_id);
        	$view->base_location_info=$location_info;
        }else if ( empty( $location_id ) ) {
        	$view->base_location_info=$location_id;
        }else {
        	
	        if( $isGroup ) {
	            $location_info = $dpHelper->getGroupById($location_id);
	            $xml           = $model->getGroupYearlyData($location_id, $column);
	            $type          = 'group';
	            $displayLocationName = $location_info->name . ' group';
	            $metaLocationName = 'the ' . $displayLocationName;
	        }else if( $isRegion ) {
	            $location_info = $model->getLocationInfoBy('id', $location_id);
	            $xml           = $model->getRegionYearlyData($location_id, $column);
	            $type          = 'region';
	            $displayLocationName = $location_info->name . ' region';
	            $metaLocationName = 'the ' . $displayLocationName;
	        }else {
	            $location_info = $model->getLocationInfoBy('id', $location_id);
	            $xml           = $model->getYearlyData($location_id, $column);
	            $type          = 'country';
	            $displayLocationName = $location_info->name;
	            $metaLocationName = $displayLocationName;	            
	        }
	        
	        $column_info = $model->getColumnByAlias($column);
	        $footer = $model->getChartFooterInfo($location_info, $type, $column_info);
	        
	        //meta info
	        $metaTitle = $displayLocationName . ' – ' . $column_info->column_title;
	        $preamble  = $model->getLocationPreambleDPValue($location_id,$column);
	        $metaDesc  = $model->getChartMetaDesc($metaLocationName,$preamble->preamble_value);
	        $metaKeywords = "$displayLocationName, gun, policy, news, law, legislation, regulation, gun control, firearm, registration, license, licence, small arms, armed violence, shooting, crime, homicide, suicide, injury prevention, public health, philip alpers";
	        
	        $view->base_location_info=$location_info;
	        $view->footer=$footer;
	        $view->chartxml=$xml;
	        $view->column_info=$column_info;
	        $view->isGroup=$isGroup;
	        $view->isRegion=$isRegion;
	        $view->metaTitle=$metaTitle;
	        $view->metaDesc=$metaDesc;
	        $view->metaKeywords=$metaKeywords;
	        $view->currentLangCode=$currentLangCode;	       
        }
        $view->display($tpl);        
    }
    

    function compare()
    {
        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        
        //global $mainframe;
        //error_reporting(E_ALL);
        $mainframe =& Joomla\CMS\Factory::getApplication();
        $lang = JFactory::getLanguage();
        //we will need this helper in view
        require_once(JPATH_BASE . '/components/com_gpo/helpers/datapage.php');
        require_once(JPATH_BASE . '/components/com_gpo/helpers/gpo.php');
        $base_location_id = Joomla\CMS\Factory::getApplication()->getInput()->get('base_location');
        $isGroup  = Joomla\CMS\Factory::getApplication()->getInput()->get('is_group');
        $isRegion = Joomla\CMS\Factory::getApplication()->getInput()->get('is_region', 0);  
        $column = urldecode(Joomla\CMS\Factory::getApplication()->getInput()->get('column',null,'string'));
//echo $column;
        //replace the : with - as we found it has been somehow replaced while coming through URl
        $column = str_replace(':', '-', $column);
   
        $langTag = $lang->getTag();
        if( strlen($langTag) >2 ) {
            $currentLangCode = strtolower(substr($langTag,0,-3));
        }
        
        $redirectURLPrefix = '';
        if( in_array(strtolower($currentLangCode), array('es','fr')) ) {
            $redirectURLPrefix     = $currentLangCode . '/';
        }




        if ($jinput->get('selected_locations')) {
            $isGroup  = $jinput->get('isGroup');
            $isRegion = $jinput->get('isRegion');
            
            if( $isGroup ) {
                /*$redirect = JURI::base() . $redirectURLPrefix . 'firearms/compare/group/' . $base_location_id . '/' . $column . '/' .  
                                      $jinput->get('selected_locations');*/
                $redirect = JURI::base() . $redirectURLPrefix . 'components/com_gpo/compare/group/' . $base_location_id . '/' . $column . '/' .  
                                      $jinput->get('selected_locations');
            }else if($isRegion) {
                /*$redirect = JURI::base() . $redirectURLPrefix . 'firearms/compare/region/' . $base_location_id . '/' . $column . '/' .  
                                     $jinput->get('selected_locations');*/
                $redirect = JURI::base() . $redirectURLPrefix . 'components/com_gpo/compare/region/' . $base_location_id . '/' . $column . '/' .  
                                     $jinput->get('selected_locations');
            }else {
                /*$redirect = JURI::base() . $redirectURLPrefix . 'firearms/compare/' . $base_location_id . '/' . $column . '/' . 
                                      $jinput->get('selected_locations');*/
                $redirect = JURI::base() . $redirectURLPrefix . 'components/com_gpo/compare/' . $base_location_id . '/' . $column . '/' . 
                                      $jinput->get('selected_locations');
            }
            
            //$mainframe->redirect($redirect);
            //return;
        }
//$ii = $jinput->get('selected_locations');print_r($ii);die();  //2902137 //5 here is the problem : location numbers are not separated 
        $selected_locations = urldecode( $jinput->get('selected_locations', null, 'HTML'));
//var_dump($base_location, $column, $secondary_locations);die();
        $config_p['language'] = $this->currentLangCode;
        $model    = &$this->getModel('Compare','', $config_p);
        $dpHelper = new DatapageHelper();
        $view     = &$this->getView('Compare', 'html');
//print_r($view);
        if (empty($base_location_id)) {
            die("Base location was not selected!");
        }
        if (empty($column)) {
            die("No category selected");
        }
        if (!$model->isColumnExists($column)) {
            die('Sorry, invalid category chosen!');
        }
        
        if($isGroup) {
           $compareable_locations = $dpHelper->getAllGroupNames($column);
           $base_location_info    = $dpHelper->getGroupById($base_location_id);
           $type = 'group';
           $displayLocationName   = $base_location_info->name . ' group';
           $metaLocationName      = 'the ' . $displayLocationName;    
        }
        else if($isRegion) {
           $regionModel           = & $this->getModel('Region');
           $base_location_info    = $model->getLocationInfoBy('id', $base_location_id);
           $locationType          = empty($base_location_info->type) ? 'subregion' : $base_location_info->type;
           $compareable_locations = $regionModel->getRegionList($locationType,$column);
           $type = 'region';
           $displayLocationName   = $base_location_info->name . ' region';
           $metaLocationName      = 'the ' . $displayLocationName;   
        }
        else {
           // echo '----}';
           $compareable_locations = $model->getLocations($column,'country'); //list only countries
           $base_location_info    = $model->getLocationInfoBy('id', $base_location_id);
           $type = 'country';
           $addPrefix = $dpHelper->isNeedToAddThe($base_location_info->name);
           $displayLocationName   =  ($addPrefix) ? 
                                     "$addPrefix " . $base_location_info->name : $base_location_info->name;
           $metaLocationName      = $displayLocationName;   
        }
       
        $column_info = $model->getColumnByAlias($column);

        //set template
        $column_display_type = $model->getDisplayType($column);
        $model->column_display_type = $column_display_type;

        if ('bar_chart' == $column_display_type) {
            $tpl = 'charts';
        } else if ('rank_table' == $column_display_type) {
            $tpl = 'ranktables';
        } else if ('switch_table' == $column_display_type OR 'switch_table_switch_sort' == $column_display_type) {
            $tpl = 'switchtables';
        } else {
            $tpl = 'nocomparison';
        }
        $view->base_location_info=&$base_location_info;
        $view->column_info=&$column_info;


        if (!empty($selected_locations)) { //process charting
            //append the base_location ID to the selected locations so that all data is retrieve from database correctly
            $comparing_locations = $selected_locations . ',' . $base_location_id;
            $footer = $model->getChartFooterInfo($base_location_info,$type,$column_info);
            $view->footer=&$footer;
            
            if($isGroup) {
               $location_data = $model->getGroupColumnData($column, $comparing_locations);
            }else if($isRegion) {
               $location_data = $model->getRegionColumnData($column, $comparing_locations);
            }else {
               $location_data = $model->getColumnData($column, $comparing_locations);
            }

            $view->selected_locations=&$selected_locations;

            //check if it (column name) is switched data or normal chart data. if it is switch dat, we will use different method
            if ('switch_table' == $column_display_type OR 'switch_table_switch_sort' == $column_display_type) {
               // echo 'switch_table';
                $tabledata = $model->prepareSwitchTable($location_data, $column, $base_location_info,$isGroup);
                $article->text = &$tabledata; //used as reference, so it will be modified by the plugin
                JPluginHelper::importPlugin('gpo');
                //$dispatcher = & JDispatcher::getInstance();
                //$dispatcher->trigger('onAfterDisplayContent', array(&$article));
                Joomla\CMS\Factory::getApplication()->triggerEvent('onAfterDisplayContent', array(&$article));

                //update the main citation object to include citations information
                $view->tablehtml=&$tabledata;
            } else if ('rank_table' == $column_display_type) {
                // echo 'rank_table';
                $tabledata = $model->prepareRankTable($location_data, $column, $base_location_info, $isGroup);
                $article->text = &$tabledata;
                JPluginHelper::importPlugin('gpo');
                $dispatcher = & JDispatcher::getInstance();
                $dispatcher->trigger('onAfterDisplayContent', array(&$article));
                //update the main citation object to include citations information
                $view->tablehtml=&$tabledata;
            } else if ('bar_chart' == $column_display_type) {
               // echo 'bar_chart';
                if(count($location_data)>30) {
                    $location_data = array_slice($location_data, 0, 30, true);
                }
                $xml = $model->arrayToXml($location_data, $column, $base_location_id, $isGroup);
                $view->chartxml=&$xml;
            }

            $view->live_url=$mainframe->getCfg('live_site');
        }
        
        //meta info
        $metaTitle = 'Compare ' . $displayLocationName . ' – ' . $column_info->column_title;
        $preamble = $model->getLocationPreambleDPValue($base_location_id,$column);
        $metaDesc = $model->getChartMetaDesc($metaLocationName,$preamble->preamble_value);
        $metaKeywords = "$displayLocationName, gun, policy, news, law, legislation, regulation, gun control, firearm, registration, license, licence, small arms, armed violence, shooting, crime, homicide, suicide, injury prevention, public health, philip alpers";
        
        $view->comparion_locations=&$compareable_locations;
        $view->location_data=&$location_data;
        $view->isGroup=&$isGroup;
        $view->isRegion=&$isRegion;
        $view->metaTitle=&$metaTitle;
        $view->metaDesc=&$metaDesc;
        $view->metaKeywords=&$metaKeywords;
        $view->currentLangCode=&$currentLangCode;
        
        $view->countrylist($tpl);
    }

    function find_facts()
    {
        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'datapage.php');
        $model = & $this->getModel('Region');
        $datapage = new DatapageHelper();
        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        $location = str_replace(' and ', '&', urldecode(Joomla\CMS\Factory::getApplication()->getInput()->get('country', '','string')));
        $group    = Joomla\CMS\Factory::getApplication()->getInput()->get('group','','string');
        $column   = urldecode(Joomla\CMS\Factory::getApplication()->getInput()->get('column','','string'));
        if (empty($location)) {
            $location = Joomla\CMS\Factory::getApplication()->getInput()->get('region','','string'); 
        }
        if (empty($location)) {
            $location = $group;
        }
        
        if( empty($group) ) {
            $item = $model->locationExists($location);
            
            //catid is used as its required by the trickery in helpers/route.php
            $url = JRoute::_('index.php?option=com_gpo&task=region&region=' . $item->catid . '#' . $column, true);
        }else {
            $item = $datapage->isGroupExists($group);
            $url = JRoute::_('index.php?option=com_gpo&task=group&id=' . $item->id . '#' . $column, true);
        }

        $this->setRedirect($url);
    }


    function glossary()
    {
        require_once(JPATH_BASE . '/components/com_gpo/helpers/citation.php');
        $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        $glossaryId = Joomla\CMS\Factory::getApplication()->getInput()->get('id', null);
        $model = &$this->getModel('glossary');
        $glossary = $model->getGlossaryById($glossaryId);
        $article = new StdClass();
        $article->text = $glossary->content;
        $view = & $this->getView('Glossary', 'html');

        JPluginHelper::importPlugin('gpo');
       /* $dispatcher = & JDispatcher::getInstance();
        $dispatcher->trigger('onAfterDisplayContent', array(&$article));*/
        $res = Joomla\CMS\Factory::getApplication()->triggerEvent('onAfterDisplayContent', array(&$article));
        $glossary->content = $article->text;
        unset($article);
        $view->glossary=&$glossary;
        $view->display('glossary');
        exit();
    }
  

}
