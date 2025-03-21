<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');
jimport('joomla.application.component.controller');
jimport('joomla.application.component.helper');

//define('JPATH_BASE', __DIR__ . '/administrator');

class GpoModelSitemap extends JModelList {

    var $total;
    var $data;
    var $allowedLangs = array('es','fr');
    var $liveSiteURL = '';
    var $useCurrentDateAsLastModDate;
    var $currentDate;

    function __construct() {
        parent::__construct();
        $this->limit       = (int) '10';
        $this->limitstart  = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', '', 'int');
        $this->liveSiteURL = JURI::root();
        
        $this->useCurrentDateAsLastModDate = TRUE;
        $this->currentDate = gmdate("Y-m-d");
    }

    function isNUllDate($p_datetime) {
        $p_datetime = trim($p_datetime);
        
        if (empty($p_datetime) || $p_datetime == '0000-00-00 00:00:00') {
            return true;
        }

        return false;
    }

    function isValidTimeStamp($timestamp) {
        return ((string) (int) $timestamp === $timestamp)
                && ($timestamp <= PHP_INT_MAX)
                && ($timestamp >= ~PHP_INT_MAX);
    }

    function _getLocations() {
        $query = "SELECT `l`.*, d.updated_at, d.published_at 
              FROM `#__gpo_location` as l, `#__gpo_datapages` as d 
              WHERE d.location_id = l.id";
        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();
        return $data;
    }
    
    /*
     * The Locations which have CP (Country Page, narrative article) articles or 
     * CP page links
     */
    function _getCPLocations() {
        
       $query = "SELECT 
                            `lo`.`type` as location_type, 
                            `lo`.`display`,
                            `c`.`title`,
                            `c`.`alias`,
                            `c`.`introtext` AS `text`,
                            `c`.`catid`,
                            `c`.`modified`,
                            `c`.`created`,
                            `c`.`state`,
                            `c`.`access` 
                     FROM 
                            `j25_content` AS `c` 
                     INNER JOIN 
                            `j25_categories` as `cat` ON `c`.`catid` =`cat`.`id` 
                     INNER JOIN 
                            `j25_gpo_location` AS `lo`  ON lower( `lo`.`name` )=lower(`cat`.`title`)     
                     where  `display`=1 and state=1 and c.alias like '%-index' and lo.`type` NOT IN ('region','subregion')";
       
        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();
        return $data;
    }

    function getRegionsLinks(&$sitemap_links, $langCode='') {
        
        if (in_array($langCode, $this->allowedLangs)) {
            $langURI = $this->liveSiteURL . strtolower($langCode) . '/';
        } else {
            $langURI = $this->liveSiteURL;
        }
        
        //Home page Link
        $sitemap_links[] = array('url' => $langURI, 
                                 'last_updated' => time(),
                                 'priority'     => 1,
                                 'updates'      => 'weekly'
                           );
        
        $cpArticleLocs = $this->_getCPLocations();
        
        $locations = $this->_getLocations();
        if (is_array($locations) && count($locations)) {
            $locationlinks = array();
            foreach ($locations as $location) {
                if ($location['display']) {
                    $lastModDate = $this->isNUllDate($location['updated_at']) ? $location['published_at'] : $location['updated_at'];
                    $name = str_replace(array(' ', '&', '(', ')'), array('-', 'and', '', ''), strtolower($location['name']));
                    $sitemap_links[] = array('url'          => $langURI . 'firearms/region/' . $name, 
                                             'last_updated' => $lastModDate,
                                             'priority'     => 1,
                                             'updates'      => 'weekly'
                                       );
                }
            }
        }
        
        //CP Locations
        foreach ($cpArticleLocs as $location) {
            $lastModDate = $this->isNUllDate($location['modified']) ? $location['created'] : $location['modified'];
            $alias = str_replace('-index','', strtolower($location['alias']));
            $sitemap_links[] = array('url'          => $langURI . 'firearms/region/cp/' . $alias,
                                     'last_updated' => $lastModDate,
                                     'priority'     => 1,
                                     'updates'      => 'weekly'
                               );
        }
    }

    function _getCitations($type = 'quotes') {
        $query = "SELECT `id`,`published`,`modified`,`ext_id` FROM   `#__gpo_citations_" . ($type == 'quotes' ? 'quotes' : 'news') . "`";
        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();
        return $data;
    }
    
    function _getGlossaries($published=1) {
        $query = "SELECT * FROM `#__gpo_datapage_glossary` WHERE  `published` = {$this->_db->Quote($published)} " .
                 " ORDER BY `id` ASC ";
        $this->_db->setQuery($query);
        return $this->_db->loadAssocList();
    }

    function getCitationLinks(&$sitemap_links,$langCode='') {
        
        if (in_array($langCode, $this->allowedLangs)) {
            $langURI = $this->liveSiteURL . strtolower($langCode) . '/';
        } else {
            $langURI = $this->liveSiteURL;
        }
        
        //get citation home & alphabets
        $sitemap_links[] = array('url'          => $langURI . 'firearms/citation/',
                                 'last_updated' => time(),
                                 'updates'      => 'daily',
                                 'priority'     => '1.0'
                           );

        $sitemap_links[] = array('url'          => $langURI . 'firearms/citation/news',
                                 'last_updated' => time(),
                                 'updates'      => 'daily',
                                 'priority'     => '0.9'
                           );
        
        $sitemap_links[] = array('url'          => $langURI . 'firearms/citation/quotes',
                                 'last_updated' => time(),
                                 'updates'      => 'daily',
                                 'priority'     => '0.9'
                           );

        //alphabet links
        $chars = 'A B C D E F G H I J K L M N O P Q R S T U V W X Y Z';
        $chars = explode(' ', $chars);
        $links = array();
        foreach ($chars as $char) {
            $sitemap_links[] = array('url' => $langURI . 'firearms/citation/news/' . $char, 'last_updated' => time(), 'updates' => 'daily', 'priority' => '0.9');
            $sitemap_links[] = array('url' => $langURI . 'firearms/citation/quotes/' . $char, 'last_updated' => time(), 'updates' => 'daily', 'priority' => '0.9');
        }

        //get quotes citations
        $citations = $this->_getCitations('quotes');
        foreach ($citations as $citation) {
            $sitemap_links[] = array('url'          => $langURI . 'firearms/citation/quotes/' . $citation['id'],
                                     'last_updated' => strtotime($citation['modified']),
                                     'updates'      => 'monthly',
                                     'priority'     => '0.7'
                               );
        }
        
        $citations = $this->_getCitations('news');
        foreach ($citations as $citation) {
            $sitemap_links[] = array('url'          => $langURI . 'firearms/citation/news/' . $citation['id'],
                                     'last_updated' => strtotime($citation['modified']),
                                     'updates'      => 'monthly',
                                     'priority'     => '0.7'
                               );
        }
        
    }

    function getGlossaryLinks(&$sitemap_links,$langCode='') {
        
        if (in_array($langCode, $this->allowedLangs)) {
            $langURI = $this->liveSiteURL . strtolower($langCode) . '/';
        } else {
            $langURI = $this->liveSiteURL;
        }
        
        //get glossaries
        $glossaries = $this->_getGlossaries();
        foreach ($glossaries as $glossary) {
            $sitemap_links[] = array('url'          => $langURI . 'firearms/glossary/' . $glossary['id'],
                                     'last_updated' => strtotime($glossary['modified']),
                                     'updates'      => 'monthly',
                                     'priority'     => '0.8'
                               );
        }
        
    }
    
    function generateSitemap(&$sitemap_links) {
        
        $config = new JConfig();
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>
                   <?xml-stylesheet type="text/xsl" href="' . $config->live_site . '/sitemap.xsl"?>
                   <urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($sitemap_links as $link) {
            $sitemap .= '
                     <url>
                        <loc>' . trim($link['url']) . '</loc>';
            
            
            if( $this->useCurrentDateAsLastModDate ) {
                $gmdate = $this->currentDate;
            }
            elseif (!$this->isNUllDate($link['last_updated'])) {
                $timestamp = $this->isValidTimeStamp($link['last_updated']) ? $link['last_updated'] : strtotime($link['last_updated']);
                $timestamp = empty($timestamp) ? $link['last_updated'] : $timestamp;
                $gmdate    = !empty($timestamp) ? gmdate('Y-m-d\TH:i:s+00:00', $timestamp) : '';
            }
            if (!empty($gmdate)) {
                    $sitemap .= '
                                <lastmod>' . $gmdate . '</lastmod>
                                ';
            }

            $sitemap .= '<changefreq>' . (!empty($link['updates']) ? $link['updates'] : 'weekly') . '</changefreq>
                           <priority>' . (!empty($link['priority']) ? $link['priority'] : '0.5') . '</priority>
                        </url>
                        ';
        }

        $sitemap .= '</urlset>';
        return $sitemap;
    }

    
    function generateSitemapIndex($sitemapFiles=array('sitemap_en.xml.gz','sitemap_es.xml.gz','sitemap_fr.xml.gz')) {

        $config = new JConfig();
        //$liveSiteURL = JURI::root();
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>
                    <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($sitemapFiles as $link) {
            $sitemap .= '
                        <sitemap>
                            <loc>' . $this->liveSiteURL . trim($link) . '</loc>
                            <lastmod>' . $this->currentDate . '</lastmod>
                        </sitemap>
                        ';
        }

        $sitemap .= '</sitemapindex>';
        return $sitemap;
    }
    
    
    function getMiscellaneousLinks(&$sitemap_links, $langCode='') {
        
        if (in_array($langCode, $this->allowedLangs)) {
            $langURI = $this->liveSiteURL . strtolower($langCode) . '/';
        } else {
            $langURI = $this->liveSiteURL;
        }
        
        //add home page canonical url
        $sitemap_links[] = array('url'          => $langURI . 'firearms/home', 
                                 'last_updated' => time(), 
                                 'updates'      => 'weekly', 
                                 'priority'     => '1'
                           );
        
        //add archive links
        $sitemap_links[] = array('url'          => $langURI . 'firearms/news/archive', 
                                 'last_updated' => time(), 
                                 'updates'      => 'monthly', 
                                 'priority'     => '0.5'
                           );
        //add about us link
        $sitemap_links[] = array('url'          => $langURI . 'about', 
                                 'last_updated' => time(), 
                                 'updates'      => 'monthly', 
                                 'priority'     => '0.6'
                           );
        //add privacy link
        $sitemap_links[] = array('url'          => $langURI . 'privacy', 
                                 'last_updated' => time(),
                                 'updates'      => 'monthly', 
                                 'priority'     => '0.6'
                           );
        
        //add updates link
        $sitemap_links[] = array('url'          => $langURI . 'recent', 
                                 'last_updated' => time(),
                                 'updates'      => 'monthly', 
                                 'priority'     => '0.7'
                           );
        
        //add contact link
        $sitemap_links[] = array('url'          => $langURI . 'contact', 
                                 'last_updated' => time(),
                                 'updates'      => 'monthly', 
                                 'priority'     => '0.7'
                           );
        
        //add news landing page link
        $sitemap_links[] = array('url'          => $langURI . 'firearms/latest', 
                                 'last_updated' => time(),
                                 'updates'      => 'monthly', 
                                 'priority'     => '0.6'
                           );
        
        //add documents page link
        $sitemap_links[] = array('url'          => $langURI . 'documents',
                                 'last_updated' => time(),
                                 'updates'      => 'weekly',
                                 'priority'     => '0.8'
                           );
        
    }

    function getTopics() {
        $query = "SELECT `seo` FROM   `#__gpo_topic`";
        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();
        return $data;
    }

    function getTopicsLinks(&$sitemap_links, $langCode='') {
        
        if (in_array($langCode, $this->allowedLangs)) {
            $langURI = $this->liveSiteURL . $langCode . '/';
        } else {
            $langURI = $this->liveSiteURL;
        }
        
        $topics = $this->getTopics();
        $sitemap_links[] = array('url' => $langURI . 'topics', 
                                 'last_updated' => time(), 
                                 'updates' => 'weekly', 
                                 'priority' => '0.7'
                           );
        foreach ($topics as $topic) {
            $sitemap_links[] = array('url' => $langURI . htmlspecialchars($topic['seo'], ENT_COMPAT, 'UTF-8'), 
                                     'last_updated' => time(), 
                                     'updates' => 'monthly', 
                                     'priority' => '0.6'
                               );
        }
    }

    function getNewsArchiveLinks(&$sitemap_links, $langCode = '') {

        if (in_array($langCode, $this->allowedLangs)) {
            $langURI = $this->liveSiteURL . $langCode . '/';
        } else {
            $langURI = $this->liveSiteURL;
        }

        //get years
        $years = $this->getYears();
        foreach ($years as $year) {
            $sitemap_links[] = array('url' => $langURI . 'firearms/news/archive/' . $year,
                'last_updated' => mktime(23, 59, 0, 12, 31, $year),
                'updates' => 'daily', 'priority' => '0.5'
            );
            //get months
            $months = $this->getMonthsByYear($year);
            foreach ($months as $month) {
                $sitemap_links[] = array('url' => $langURI . 'firearms/news/archive/' . $month->date,
                    'last_updated' => strtotime($month->date . '/30'),
                    'updates' => 'weekly',
                    'priority' => '0.5'
                );

                //get days in the month
                $days = $this->getDaysByMonth($year, $month);
                foreach ($days as $day) {
                    $sitemap_links[] = array('url' => $langURI . 'firearms/news/archive/' . $day->date,
                        'last_updated' => strtotime($day->date),
                        'updates' => 'daily',
                        'priority' => '0.5'
                    );
                    
                    /* THIS IS STOPPED AS IT WAS TAKING HUGE TIME TO PROCESS
                      //get article of each day
                      $articles = $this->getArticlesOn($year, $month, $day);
                      foreach($articles as $article){
                      var_dump($article);
                      //link to article
                      $sitemap_links[] = array('url'=>'/firearms/news/'.$article->id, 'last_updated'=> strtotime($article->modified),'updates'=>'monthly', 'priority'=>'0.5');

                      //link to search page by country
                      $sitemap_links[] = array('url'=>'/firearms/search/?q=&l='.urlencode($article->locations), 'last_updated'=> strtotime($day->date),'updates'=>'daily', 'priority'=>'0.8');

                      //die();


                      }
                     * **
                     */
                }
            }
        }
    }
    
    function generateNewsLinks() {
        
        JLoader::import('joomla.application.component.model');
	JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_gpo/models', 'GpoModel');
        $newsModel = JModelLegacy::getInstance('News', 'GpoModel');
        
        $allPublishedNews = $newsModel->getAllPublishedNews();
        $allPublishedLocs = $newsModel->getAllPublishedLocations();
        $sitemap_links = array();
             
        $langURI = $this->liveSiteURL;
        
        ####
        #### Links for EN site 
        ####
        foreach ($allPublishedNews as $item) {
            $sitemap_links['en'][] = array('url'          => $langURI . 'firearms/news/' . $item['id'],
                                           'last_updated' => $this->isNUllDate($item['modified']) ? strtotime($item['entered']) : strtotime($item['modified']),
                                           'updates'      => 'monthly',
                                           'priority'     => '0.5'
                                     );
        }
        
        foreach ($allPublishedLocs as $key => $value) {
            //link to search page by country
            $sitemap_links['en'][] = array('url'      => $langURI .  htmlspecialchars('firearms/search/?q=&l=', ENT_COMPAT, 'UTF-8') . urlencode($value), 
                                           'weekly'   => 'daily',
                                           'priority' => '0.8',
                                           'last_updated' => $this->currentDate
                                    );
        }
        
        ####
        #### Links for ES/FR sites
        ####
        foreach ($this->allowedLangs as $langCode) {
            $langURI = $this->liveSiteURL . $langCode . '/';
            
            foreach ($allPublishedNews as $item) {
                $sitemap_links[$langCode][] = array('url'          => $langURI . 'firearms/news/' . $item['id'],
                                                    'last_updated' => $this->isNUllDate($item['modified']) ? strtotime($item['entered']) : strtotime($item['modified']),
                                                    'updates'      => 'monthly',
                                                    'priority'     => '0.5'
                                              );
            }

            foreach ($allPublishedLocs as $key => $value) {
                //link to search page by country
                $sitemap_links[$langCode][] = array('url'      => $langURI . htmlspecialchars('firearms/search/?q=&l=', ENT_COMPAT, 'UTF-8') . urlencode($value),
                                                    'weekly'   => 'daily',
                                                    'priority' => '0.8',
                                                    'last_updated' => $this->currentDate
                                              );
            }
        }
        
        return $sitemap_links;
    }
    
    /*
     *
     * Get DocMan Documents Links
     * 
     */
    function getDocManDocuments() {

        $publicCategoryID = 361;
        if (file_exists(JPATH_BASE . '/includes/defines.php')) {
            include_once JPATH_BASE . '/includes/defines.php';
        }

        require_once JPATH_BASE . '/includes/framework.php';

        // Boot Joomlatools Framework
        JPluginHelper::importPlugin('system', 'joomlatools');
        $controller = KObjectManager::getInstance()->getObject('com://admin/docman.controller.document');
        $user = KObjectManager::getInstance()->getObject('user');

        $documents_in_a_category = $controller
                ->access($user->getRoles()) // Permissions
                ->current_user($user->getId())
                ->enabled(1)
                ->status('published')
                ->category($publicCategoryID) // Category ID here
                //->limit(5)   // Limiting to 50 documents
                //->offset(0) // You can set this to 50 in the next call to paginate results                                                  												 
                ->browse();

        $docDataArray = array();
        $i = 0;
        foreach ($documents_in_a_category as $document) {
            $docDataArray[$i]['document_id'] = $document->id;
            $docDataArray[$i]['title'] = $document->title;
            $docDataArray[$i]['alias'] = $document->slug;
            $docDataArray[$i]['modified_on']  = $document->modified_on;
            $docDataArray[$i]['storage_type'] = $document->storage_type;
            $docDataArray[$i]['file_name'] = $document->storage_path;
            $docDataArray[$i]['file_url']  = 'documents' . '/' . $document->id . "-" . $document->slug . '/file';
            $docDataArray[$i]['doc_url']   = 'documents' . '/' . $document->id . "-" . $document->slug;
            $i++;
        }

        return $docDataArray;
    }
    
    function getDocManDocLinks(&$sitemap_links, $langCode='') {
        if (in_array($langCode, $this->allowedLangs)) {
            $langURI = $this->liveSiteURL . $langCode . '/';
        } else {
            $langURI = $this->liveSiteURL;
        }
        
        $publicDocs = $this->getDocManDocuments();
        
        foreach ($publicDocs as $document) {
            $sitemap_links[] = array('url'          => $langURI . htmlspecialchars($document['doc_url'], ENT_COMPAT, 'UTF-8'), 
                                     'last_updated' => strtotime($document['modified_on']), 
                                     'updates'      => 'monthly', 
                                     'priority'     => '0.7'
                               );
            
            $sitemap_links[] = array('url'          => $langURI . htmlspecialchars($document['file_url'], ENT_COMPAT, 'UTF-8'), 
                                     'last_updated' => strtotime($document['modified_on']), 
                                     'updates'      => 'monthly', 
                                     'priority'     => '0.7'
                               );
        }
    }

    function writeSitemapToFile(&$sitemap_data, $filename = 'sitemap.xml') {
        //echo dirname(JPATH_BASE);
        file_put_contents(dirname(JPATH_BASE) . '/' . $filename, $sitemap_data);
    }

    function gzipSitemap( $filename = array('sitemap_en.xml','sitemap_fr.xml','sitemap_es.xml') ) {
        foreach( $filename as $fn ) {
            $fn = dirname(JPATH_BASE) . '/' . $fn;
            $compressedFileName = $fn . '.gz';
            //$result = `gzip -f $fn`;
            ## Keep the original file
            $result = `gzip < $fn > $compressedFileName`;
        }
        
        return $result;
    }

    function getYears() {
        $query = 'SELECT DISTINCT ( DATE_FORMAT( `published` , "%Y" ) ) FROM `#__gpo_news` ORDER BY `published` DESC;';
        $this->_db->setQuery($query);
        $items = $this->_db->loadColumn();
        return $items;
    }

    function getMonthsByYear($year) {
        $query = '
SELECT DISTINCT ( DATE_FORMAT( `published` , "%Y/%m" ) ) as `date`, DATE_FORMAT( `published` , "%M" ) as `name`
FROM `#__gpo_news`
WHERE YEAR( `published` ) = ' . $this->_db->quote((int) $year);
        $this->_db->setQuery($query);
        $items = $this->_db->loadObjectList();
        return $items;
    }

    function getDaysByMonth($year, $month) {
        $query = '
SELECT DISTINCT ( DATE_FORMAT( `published` , "%Y/%m/%d" ) ) as `date`, DATE_FORMAT( `published` , "%e %W" ) as `name`
FROM `#__gpo_news`
WHERE YEAR( `published` ) = ' . $this->_db->quote((int) $year) . '
AND MONTH( `published` ) = ' . $this->_db->quote((int) $month);
        $this->_db->setQuery($query);
        $items = $this->_db->loadObjectList();
        return $items;
    }

    function getArticlesOn($year, $month, $day) {
        $query = '
SELECT `n`.*, DATE_FORMAT(`n`.`published`, "%Y%m%d" ) as `published_hash`,GROUP_CONCAT( `l`.`name` ) as locations
FROM `#__gpo_news` as `n`
LEFT JOIN `#__gpo_news_locations` as `nl` ON `n`.`id`=`nl`.`ext_id`
LEFT JOIN `#__gpo_location` as `l` ON `l`.`id` = `nl`.`location_id`
WHERE YEAR( `n`.`published` ) = ' . $this->_db->quote((int) $year) . '
AND MONTH( `n`.`published` ) = ' . $this->_db->quote((int) $month) . '
AND DAY( `n`.`published` ) = ' . $this->_db->quote((int) $day) . '
GROUP BY `n`.`id`;';
        $this->_db->setQuery($query);
        $items = $this->_db->loadObjectList();
        return $items;
    }

}
