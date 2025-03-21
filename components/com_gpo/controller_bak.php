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

jimport('joomla.application.component.controller');

/**
 * @package        Joomla
 * @subpackage    Config
 */
class GpoController extends JController
{

    var $_compontent = 'com_gpo';

    function __construct()
    {
        parent::__construct();
        $this->logged_in = false;
        $this->oUser = & JFactory::getUser();

        if (!empty($this->oUser->id)) {
            $this->is_member = true;
        } else {
            $this->is_member = false;
        }
        $this->registerTask('', 'home');
    }

    function latest()
    {

        $model = & $this->getModel('Sphinxsearch');
        $model->frontend();

        if ($model->isReIndexInProgress() && empty($model->articles)) {
            $filename = JPATH_BASE . '/components/com_gpo/cache/news.rebuild.txt';
            $html = file_get_contents($filename);
            echo $html;
            return;
        }


        $dates = array();

        foreach ($model->articles as $item) {
            $key = $item->published_hash;
            if (!isset($dates[$key])) {
                $dates[$key] = array();
            }
            $dates[$key][] = $item;
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

        $view = & $this->getView('Latest', 'html');
        $view->assign('logged_in', $this->is_member);
        $view->assignRef('results', $items);
        $view->assignRef('pagination', $model->pagination);
        $view->display();
    }

    function home()
    {

        $factsUrl = JURI::base() . 'firearms/region';
        $newsUrl = JURI::base() . 'firearms/latest';

        $model = & $this->getModel('Sphinxsearch');
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

        $view = & $this->getView('Home', 'html');

        $view->assign('logged_in', $this->is_member);
        $view->assignRef('results', $items);

        $view->assignRef('factsUrl', $factsUrl);
        $view->assignRef('newsUrl', $newsUrl);

        //load the featured charts
        $view->assignRef('featuredcharts', getFeaturedCharts());
        $view->display();
    }

    function search()
    {
        

        $show = JRequest::getVar('view', false);
        $show = strtolower($show);


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
            $location = rawurlencode($location);
            $q = rawurlencode($_GET['q']);
            $url = JRoute::_('index.php?option=com_gpo&task=search&q=' . $q . '&l=' . $location, false);
            $this->setRedirect($url);
        }


        $q = JRequest::getVar('q', '', 'GET', 'string');
        $location = JRequest::getVar('l', '', 'GET', 'string');
        /*
         * A fix for pagination not working with certain characters due to how Joomla urldecodes before using standard php functions.
         */
        $router = & JRouter::getInstance('site');
        $router->setVar('l', $location, true);
        $router->setVar('q', $q, true);
        $router->setVar('task', JRequest::getVar('task'), true);
        $router->setVar('option', JRequest::getVar('option'), true);

        $model = & $this->getModel('Sphinxsearch');
        $model->frontend($q, $location);

        if ($model->isReIndexInProgress() && empty($model->articles)) {
            $filename = JPATH_BASE . '/components/com_gpo/cache/news.rebuild.txt';
            $html = file_get_contents($filename);
            echo $html;
            return;
        }

        var_dump ($model);
        var_dump ($model->location);

        $view = & $this->getView('Search', 'html');
        $view->assignRef('query', $model->q);
        $view->assignRef('location', $model->location);

        $view->assign('logged_in', $this->is_member);
        $view->assignRef('results', $model->articles);
        $view->assignRef('pagination', $model->pagination);

        $view->display();
    }

    function msearch()
    {
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
                $location = rawurlencode($location);
                $q = rawurlencode($_GET['q']);
                $url = JRoute::_('index.php?option=com_gpo&task=search&q=' . $q . '&l=' . $location, false);
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
        $router->setVar('task', JRequest::getVar('task'), true);
        $router->setVar('option', JRequest::getVar('option'), true);


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
        $view->assign('logged_in', $this->is_member);
        $view->assignRef('results', $model->articles);
        $view->assignRef('pagination', $model->pagination);
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
        $mainframe =& JFactory::getApplication();
        $id = JRequest::getVar('id', false);
        $type = JRequest::getVar('type', false);

        $model = & $this->getModel('Citation');
        $model->is_member = $this->is_member;
        $view = & $this->getView('Citation', 'html');

        //load the helper file
        require_once(JPATH_BASE . '/components/com_gpo/helpers/citation.php');
        $view->assign('logged_in', $this->is_member);
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

                $article = (object)array(
                    'text' => & $citation->content,
                );
                JPluginHelper::importPlugin('gpo');
                $dispatcher = & JDispatcher::getInstance();
                $dispatcher->trigger('onAfterDisplayContent', array(&$article));
                //update the main citation object to include citations information
                $citation->citations = $article->citations;
                $citation->footer = $article->footer;

                $view->assignRef('citation', $citation);
                $view->assignRef('type', $type);
                $view->assign('alphabet', $citation->title{0}); //assign
                $view->display();
            } else if ($id AND !is_numeric($id)) { //alphabet
                //we got an alphabet, so now fetch all citations started with this character
                $citations = $model->getByChar($id);
                $view->assignRef('citations', $citations);
                $view->assignRef('type', $type);
                $view->assignRef('alphabet', $id);
                $view->archive();
            } else {
                $view->assignRef('type', $type);
                $view->archive();
            }
        }
        exit();
    }

    function rss()
    {
        $mRss = & $this->getModel('Rss');
        $mRss->logged_in = $this->is_member;

        $view = JRequest::getVar('view', false, 'GET');
        if ($view === 'go') {
            $id = JRequest::getVar('id', false, 'GET');
            $url = $mRss->go2($id);
            $this->setRedirect($url);
            $this->redirect();
            return;
        } elseif ($view === 'make') {
            $k = JRequest::getVar('keyword', '', 'POST');

            $l = JRequest::getVar('location', '', 'POST');
            if (empty($l)) {
                $l = JRequest::getVar('country', '', 'POST');
            }

            $k = trim($k);
            $l = trim($l);

            if (!empty($l) || !empty($k)) {
                $url = JURI::base() . ltrim(JRoute::_('index.php?option=com_gpo&task=rss&k=' . rawurlencode($k) . '&l=' . rawurlencode($l)), "/");
            } else {
                $url = '';
            }

            $data = explode("\n", GpoGetTypeFromCache('public_country'));
            $options_country = '';
            if (!empty($data)) {
                foreach ($data as $v) {
                    $value = str_replace("&nbsp;", "", $v);
                    $options_country .= '<option value="' . $value . '">' . ucwords($v) . '</option>';
                }
            }

            $data = explode("\n", GpoGetTypeFromCache('public_region'));
            $options_region = '';
            if (!empty($data)) {
                foreach ($data as $v) {
                    $value = str_replace("&nbsp;", "", $v);
                    $options_region .= '<option value="' . $value . '">' . ucwords($v) . '</option>';
                }
            }


            $view = & $this->getView('Rss', 'html');
            $view->assignRef('url', $url);
            $view->assignRef('options_country', $options_country);
            $view->assignRef('options_region', $options_region);

            $view->display();
        } else {
            //does cache exist
            $k = JRequest::getVar('k', '', 'GET');
            $l = JRequest::getVar('l', '', 'GET');

            $k = trim($k);
            $l = trim($l);
            if (strtolower($l) === 'world') {
                $l = '';
            }

            $mRss->search = & $this->getModel('Sphinxsearch');
            $mRss->showFeed($k, $l);
        }
    }

    function topic()
    {
        //#fix use the format for JOOMLA like that found in shared_functions
        require_once(JPATH_BASE . '/administrator/components/com_gpo/helper/topic.php');

        $id = JRequest::getVar('id', false);

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

        $is_rss = JRequest::getVar('rss', false);
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

        $model->limit = 100;
        $model->cl->SetLimits((int)$model->start, (int)$model->limit, $model->max_matches);
        $model->members($_GET);


        if (!empty($model->articles)) {
            $document = &JFactory::getDocument();
            $href = JRoute::_('index.php?option=com_gpo&task=topic&id=' . rawurlencode($topic->get('seo')) . '&rss=1', true);
            $title = $title = 'Gun Policy News ( ' . $topic->get('page_headline') . ' )';
            $document->addHeadLink($href, 'alternate', $relType = 'rel', array('type' => 'application/rss+xml', 'title' => $title));
        }
        $view = & $this->getView('Topic', 'html');
        $view->assignRef('topic', $topic);
        $view->assignRef('results', $model->articles);
        $view->display();
    }

    function region()
    {
        //global $mainframe;
        $mainframe =& JFactory::getApplication();
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'icon.php');
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'datapage.php');

        $id = JRequest::getVar('id', false);

        $model = & $this->getModel('Region');
        $view = & $this->getView('Region', 'html');
        $dispatcher = & JDispatcher::getInstance();
        JPluginHelper::importPlugin('gpo');

        //get datapage related information
        $dp = new DatapageHelper();
        $uri = urldecode(JRequest::getURI());

        $regionAlias = $dp->getRegionAliasFromURI($uri);
        $regionObj = $dp->getRegionNameByAlias($regionAlias);
        
        $regionName = $regionObj->name;
        $regionId = $regionObj->id;
        $datapageUrl = $dp->getDatapageLink($regionName, $regionAlias);

        //article is info
        $article = $model->intro($id, $regionName);

        $narrativeState = $dp->isNarrativePageAvailable($article->state, $article->access);
        $isDPAvailable = $dp->isDatapageExists($regionName);


        if ($narrativeState && $isDPAvailable) {
            //whatever the article info is... overwrite article text again with the datapage texts
            $dp_data  = $dp->getDPByLocation($regionName);
            if ($dp_data) {
                $DPHtml = $dp->getDPText($regionName, $dp_data, $narrativeState);
            }

            if (!empty($DPHtml)) {
                $article->text = $DPHtml;
            }
            if (!$dp->isNullDate($dp_data->updated_at) || !$dp->isNullDate($dp_data->created_at)) {
                $article->modified = (!$dp->isNullDate($dp_data->updated_at)) ? $dp_data->updated_at : $dp_data->created_at;
            }
            $article->title = $dp->getDPBrowserTitle($regionName);
        }

        //show 'view datapage' link in narrative pages only
        if ($narrativeState) {
            $view->assign('datapageUrl', $datapageUrl);
            $view->assign('regionName', $regionName);
            $view->assign('regionID', $regionId);
        } else {
            //add different browser title for the datapages
            $article->title = $dp->getDPBrowserTitle($regionName);
        }
        $view->assignRef('regionName', $regionName);
        $view->assignRef('article', $article);
        $view->assign('cp', 0);



        //check to see if the content is an index
        //if it is get locations and articles
        if (strpos($article->alias, '-index') !== false || strpos($article->alias, 'staff-notes') !== false) {


          if ($article->alias !== 'region-index') {

                
                $location = $model->getLocationInfo($article->catid);


                $view->assignRef('location', $location);

                $locations = $model->locationsById($location->id);
                

                if ($article->state === "1" && strpos($article->alias, 'staff-notes') === false) {
                    $articles = $model->getArticles($location->id, $article->catid);
                    
                    $view->assignRef('articles', $articles);
                }
            } else {
                $locations = $model->getRegions();
            }
            $view->assignRef('locations', $locations);
            $breadcrumbs = $model->getBreadCrumbs($location->id);
            $view->assignRef('breadcrumbs', $breadcrumbs);
        }
        //we do this to get the citations for the whole page
        /*
         * Process the prepare content plugins
         */

        
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        $params = & $mainframe->getParams('com_content');
        $dispatcher->trigger('onPrepareContent', array(&$article, &$params, $limitstart));
       
        $dispatcher->trigger('onAfterDisplayContent', array(&$article, &$params, $limitstart));
        
        
        $view->display();
    }

//end function region


    /*
    * the CP(Article) page of the regions
    *
    */

    function cp()
    {
        //global $mainframe;
       $mainframe =& JFactory::getApplication();

        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'icon.php');
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'datapage.php');

        $id = JRequest::getVar('id', false);

        $model = & $this->getModel('Region');
        $view = & $this->getView('Region', 'html');
        $dispatcher = & JDispatcher::getInstance();
        JPluginHelper::importPlugin('gpo');

        //get datapage related information
        $dp = new DatapageHelper();
        $regionAlias = $dp->getRegionAliasFromURI(JRequest::getURI());
        $regionObj = $dp->getRegionNameByAlias($regionAlias);
        $regionName = $regionObj->name;
        $datapageUrl = $dp->getDatapageLink($regionName, $regionAlias);

        //article is info
        $article = $model->intro($id, $regionName);


        //show 'view datapage' link in narrative pages only
        $narrativeState = $dp->isNarrativePageAvailable($article->state, $article->access);
        $isDPAvailable = $dp->isDatapageExists($regionName);

        if ($narrativeState) {
            $view->assign('datapageUrl', $datapageUrl);
            $view->assign('regionName', $regionName);
        } else {
            //add different browser title for the datapages
            $article->title = $dp->getDPBrowserTitle($regionName);
        }

        if ($isDPAvailable && $narrativeState) {
            $dpTabs = $dp->getDPTabs('CP', $datapageUrl);
            $article->text = $dpTabs . $article->text;
        }

        $view->assignRef('article', $article);
        $view->assign('cp', 1);

        //check to see if the content is an index
        //if it is get locations and articles
        if (strpos($article->alias, '-index') !== false || strpos($article->alias, 'staff-notes') !== false) {
            if ($article->alias !== 'region-index') {
                $location = $model->getLocationInfo($article->catid);
                $view->assignRef('location', $location);

                $locations = $model->locationsById($location->id);
                if ($article->state === "1" && strpos($article->alias, 'staff-notes') === false) {
                    $articles = $model->getArticles($location->id, $article->catid);
                    $view->assignRef('articles', $articles);
                }
            } else {
                $locations = $model->getRegions();
            }
            $view->assignRef('locations', $locations);

            $breadcrumbs = $model->getBreadCrumbs($location->id);
            $view->assignRef('breadcrumbs', $breadcrumbs);
        }
        //we do this to get the citations for the whole page
        /*
         * Process the prepare content plugins
         */

        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        $params = & $mainframe->getParams('com_content');
        $dispatcher->trigger('onPrepareContent', array(&$article, & $params, $limitstart));
        $dispatcher->trigger('onAfterDisplayContent', array(&$article, & $params, $limitstart));

        $view->display();
    }

//end function CP

    function datapage()
    {
        //global $mainframe;
        $mainframe =& JFactory::getApplication();

        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'icon.php');
        require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'datapage.php');

        $id = JRequest::getVar('id', false);

        $model = & $this->getModel('Region');
        $view = & $this->getView('Region', 'html');
        $dispatcher = & JDispatcher::getInstance();
        JPluginHelper::importPlugin('gpo');

        //get datapage related information
        $dp = new DatapageHelper();
        $regionAlias = $dp->getRegionAliasFromURI(JRequest::getURI());
        $regionObj = $dp->getRegionNameByAlias($regionAlias);
        $regionName = $regionObj->name;

        //article is info; get the datapage
        $article = $model->intro($id, $regionName);

        //whatever the article info is... overwrite article text again with the datapage texts
        $dp_data = $dp->getDPByLocation($regionName);
        $narrativeState = $dp->isNarrativePageAvailable($article->state, $article->access);
        if ($dp_data) {
            $DPHtml = $dp->getDPText($regionName, $dp_data, $narrativeState);
        }
        //$article = new stdClass();
        if (!empty($DPHtml)) {
            $article->text = $DPHtml;
        }
        if (!$dp->isNullDate($dp_data->updated_at) || !$dp->isNullDate($dp_data->created_at)) {
            $article->modified = (!$dp->isNullDate($dp_data->updated_at)) ? $dp_data->updated_at : $dp_data->created_at;
        }

        $article->title = $dp->getDPBrowserTitle($regionName);

        $view->assignRef('article', $article);

        //check to see if the content is an index
        //if it is get locations and articles
        if (strpos($article->alias, '-index') !== false || strpos($article->alias, 'staff-notes') !== false) {
            if ($article->alias !== 'region-index') {
                $location = $model->getLocationInfo($article->catid);
                $view->assignRef('location', $location);

                $locations = $model->locationsById($location->id);
                if ($article->state === "1" && strpos($article->alias, 'staff-notes') === false) {
                    $articles = $model->getArticles($location->id, $article->catid);
                    $view->assignRef('articles', $articles);
                }
            } else {
                $locations = $model->getRegions();
            }
            $view->assignRef('locations', $locations);

            $breadcrumbs = $model->getBreadCrumbs($location->id);
            $view->assignRef('breadcrumbs', $breadcrumbs);
        }

        //we do this to get the citations for the whole page
        /*
         * Process the prepare content plugins
         */

        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        $params = & $mainframe->getParams('com_content');
        $dispatcher->trigger('onPrepareContent', array(&$article, & $params, $limitstart));
        $dispatcher->trigger('onAfterDisplayContent', array(&$article, & $params, $limitstart));

        $view->display();
    }

//end function datapage

    function news()
    {
        $id = JRequest::getVar('id', '', '', 'int');
        $model = & $this->getModel('News');
        if (!$this->is_member) {
            $item = $model->getById($id);
        } else {
            $item = $model->getById($id, '0');
        }

        if (empty($item)) {
            $url = JRoute::_('index.php?option=com_gpo&task=latest', true);
            $this->setRedirect($url);
        }

        $view = & $this->getView('News', 'html');
        $view->assign('is_member', $this->is_member);
        $view->assignRef('oNews', $item);
        if (!$this->is_member) {
            $view->display();
        } else {
            $view->membersOnly();
            exit();
        }
    }

    function news_archive()
    {

        //		if( $this->oUser->usertype !== 'Super Administrator' )
        //		{
        //			$url = JApplication::getCfg('live_site');
        //			$this->setRedirect($url );
        //			$this->redirect();
        //		}

        $year = JRequest::getVar('y', '', 'GET');
        $month = JRequest::getVar('m', '', 'GET');
        $day = JRequest::getVar('d', '', 'GET');

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
        $view->assignRef('year', $year);

        if (isset($month)) {
            $view->assignRef('month', $month);
            $view->assignRef('month_name', $month_name);

            if (isset($day)) {
                $view->assignRef('day', $day);
            }
        }


        $data = array();
        switch ($do) {
            case 'year':
//Get year data
                $years = $model->getYears($year);
                $months = $model->getMonthsByYear($year);

                $view->assignRef('years', $years);
                $view->assignRef('months', $months);
                $view->archive_year();
                break;
            case 'month':
//Get month data
                $years = $model->getYears($year);
                $days = $model->getDaysByMonth($year, $month);

                $view->assignRef('years', $years);
                $view->assignRef('days', $days);
                $view->archive_month();
                break;
            case 'day':
//Get day data
                $years = $model->getYears($year);
                if ($this->is_member) {
                    $oNews = $model->getArticlesOn($year, $month, $day);
                } else {
                    $oNews = $model->getArticlesOn($year, $month, $day, 1);
                }

                $view->assignRef('years', $years);
                $view->assignRef('oNews', $oNews);

                $view->archive_day();
                break;
            default:
                $years = $model->getYears();
                $view->assignRef('years', $years);
                $view->archive();
                break;
        }
    }

    function compareyears()
    {
        //ini_set('display_errors',true);
        require_once(JPATH_BASE . '/components/com_gpo/helpers/datapage.php');

        $location_id = JRequest::getVar('base_location', '');
        $column = urldecode(JRequest::getVar('column', ''));
        //replace the : with - as we found it has been somehow replaced while coming through URl
        $column = str_replace(':', '-', $column);
        $model = &$this->getModel('Compare');
        $view = &$this->getView('Compare', 'html');
        $tpl = 'yearlycompare';

        $location_info = $model->getLocationInfoBy('id', $location_id);
        $view->assignRef('base_location_info', $location_info);

        $xml = $model->getYearlyData($location_id, $column);

        $footer = $model->getChartFooterInfo($location_info);
        $view->assignRef('footer', $footer);

        $view->assignRef('chartxml', $xml);

        $column_info = $model->getColumnByAlias($column);
        $view->assignRef('column_info', $column_info);
        //var_dump($xml);


        $view->display($tpl);
    }

    function compare()
    {
        //ini_set('display_errors',true);

        //global $mainframe;
        $mainframe =& JFactory::getApplication();

        //we will need this helper in view
        require_once(JPATH_BASE . '/components/com_gpo/helpers/datapage.php');
        $base_location_id = JRequest::getVar('base_location', '');
        $column = urldecode(JRequest::getVar('column', ''));
        //replace the : with - as we found it has been somehow replaced while coming through URl
        $column = str_replace(':', '-', $column);

        /*
         * To make pretty URL and let user copy it, we will redirect POST to GET so that it is shown correctly on addressbar.
         */
        if (JRequest::getVar('selected_locations', '', 'POST')) {
            $redirect = JURI::base() . 'firearms/compare/' . $base_location_id . '/' . $column . '/' . JRequest::getVar('selected_locations', '', 'POST');
            $mainframe->redirect($redirect);
            return;
        }
        $selected_locations = urldecode(JRequest::getVar('selected_locations', ''));
        //var_dump($base_location, $column, $secondary_locations);
        $model = &$this->getModel('Compare');
        $view = &$this->getView('Compare', 'html');
        if (empty($base_location_id)) {
            die("Base location was not selected!");
        }
        if (empty($column)) {
            die("No category selected");
        }
        if (!$model->isColumnExists($column)) {
            die('Sorry, invalid category chosen!');
        }
        $compareable_locations = $model->getLocations($column);
        //var_dump($compareable_locations);
        $column_info = $model->getColumnByAlias($column);

        $base_location_info = $model->getLocationInfoBy('id', $base_location_id);
        //var_dump($base_location_info);

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
        $view->assignRef('base_location_info', $base_location_info);
        $view->assignRef('column_info', $column_info);


        if (!empty($selected_locations)) { //process charting
            //append the base_location ID to the selected locations so that all data is retrieve from database correctly
            $comparing_locations = $selected_locations . ',' . $base_location_id;
            $footer = $model->getChartFooterInfo($base_location_info);
            $view->assignRef('footer', $footer);
            $location_data = $model->getColumnData($column, $comparing_locations);
            //var_dump($location_data);

            $view->assignRef('selected_locations', $selected_locations);

            //check if it (column name) is switched data or normal chart data. if it is switch dat, we will use different method
            if ('switch_table' == $column_display_type OR 'switch_table_switch_sort' == $column_display_type) {
                $tabledata = $model->prepareSwitchTable($location_data, $column, $base_location_info);
                $article->text = &$tabledata; //used as reference, so it will be modified by the plugin
                JPluginHelper::importPlugin('gpo');
                $dispatcher = & JDispatcher::getInstance();
                $dispatcher->trigger('onAfterDisplayContent', array(&$article));
                //update the main citation object to include citations information
                $view->assignRef('tablehtml', $tabledata);
            } else if ('rank_table' == $column_display_type) {
                $tabledata = $model->prepareRankTable($location_data, $column, $base_location_info);
                $article->text = &$tabledata;
                JPluginHelper::importPlugin('gpo');
                $dispatcher = & JDispatcher::getInstance();
                $dispatcher->trigger('onAfterDisplayContent', array(&$article));
                //update the main citation object to include citations information
                //print_r($article);
                //var_dump($data);
                $view->assignRef('tablehtml', $tabledata);
            } else if ('bar_chart' == $column_display_type) {
                $xml = $model->arrayToXml($location_data, $column, $base_location_id);
                $view->assignRef('chartxml', $xml);
            }

            $view->assign('live_url', $mainframe->getCfg('live_site'));
        }
        $view->assignRef('comparion_locations', $compareable_locations);
        $view->countrylist($tpl);
    }

    function find_facts()
    {
        $model = & $this->getModel('Region');
        $location = str_replace(' and ', '&', urldecode(JRequest::getVar('country', '', 'GET')));
        $column = urldecode(JREquest::getVar('column', '', 'GET'));
        if (empty($location)) {
            $location = JRequest::getVar('region', '', 'GET');
        }
        $item = $model->locationExists($location);

        //catid is used as its required by the trickery in helpers/route.php
        $url = JRoute::_('index.php?option=com_gpo&task=region&region=' . $item->catid . '#' . $column, true);
        $this->setRedirect($url);
    }

//end function find_facts

    function test()
    {

    }


    function glossary()
    {
        $glossaryId = JRequest::getVar('id', null, 'GET');
        $model = &$this->getModel('glossary');
        $glossary = $model->getGlossaryById($glossaryId);
        $article = new StdClass();
        $article->text = $glossary->content;
        //var_dump($glossary);
        $view = & $this->getView('Glossary', 'html');

        JPluginHelper::importPlugin('gpo');
        $dispatcher = & JDispatcher::getInstance();
        $dispatcher->trigger('onAfterDisplayContent', array(&$article));
        $glossary->content = $article->text;
        unset($article);
        //($article->text);
        $view->assignRef('glossary', $glossary);
        $view->display('glossary');
        exit();
    }

}
