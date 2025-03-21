<?php

/**
 * Sitemap controller
 * @package gunpolicy
 * @author Murshed Ahmmad Khan
 * @link http://www.usamurai.com
 * @license GPL, This script does not come with any expressed or implied warranties! Use at your own risks!
 */
defined('_JEXEC') or die();

class GpoControllersitemap extends GpoController {

    function __construct() {
        parent::__construct();
        //require_once(JPATH_COMPONENT . DS . 'helper' . DS . 'datapage.php');

        $this->oUser = JFactory::getUser();
        ini_set('memory_limit', '2048M');

        //7 and 8 is administrator and super administrator
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $this->isAdministrator = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;

        if ($this->isAdministrator !== true) {
            $link = JRoute::_('index.php');
            $this->setRedirect($link);
            $this->redirect();
        }
        $task = Joomla\CMS\Factory::getApplication()->getInput()->get('task', false);
        $this->registerTask('', 'configuration');
        
        //data is ready, now make it sitemap
        $this->sitemapFiles = array(  'en' => 'sitemap_ssl_en.xml',
                                      'es' => 'sitemap_ssl_es.xml',
                                      'fr' => 'sitemap_ssl_fr.xml',
            
                                      'en_news' => 'sitemap_ssl_news_en.xml',
                                      'es_news' => 'sitemap_ssl_news_es.xml',
                                      'fr_news' => 'sitemap_ssl_news_fr.xml'
                              );

        $this->useCurrentDateAsLastModDate = TRUE;
        $this->currentDate = gmdate("Y-m-d");
    }

    function configuration() {
        $view = & $this->getView('Sitemap', 'html');

        $view->configuration();
    }

    function getDocManDocs() {
          ini_set('display_errors',1);
          error_reporting(E_ALL);
          $model    = $this->getModel('Sitemap');
          $publicDocs = $model->getDocManDocuments();
          print_r($publicDocs);
          echo 'Done';
          exit();
    }
    
     function testNewsLinks() {
          ini_set('display_errors',1);
          error_reporting(E_ALL);
          $model = $this->getModel('Sitemap');

          $sitemap_links = $model->generateNewsLinks();
          $sitemap_data  = $model->generateSitemap($sitemap_links['en']);

          //echo '<pre>' . print_r($sitemap_links,true) . '</pre>';
          echo 'Done';
          exit();
    }
    
    function saveconfigs() {
        set_time_limit(1600);
        
        //load the model
        $model     = $this->getModel('Sitemap');
        $dpmodel   = $this->getModel('Datapages');

        $sitemap_data = '';
        $full_sitemap_data = '';
        $sitemap_links = array();
 
        
        ######################################################
        ############ Generate EN sitelinks  ##################
        ######################################################
        
        //load region links
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('region_data')) {
            //get the locatin links and update date
            $model->getRegionsLinks($sitemap_links);
        }

        //load citation links
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('citation_data')) {
            //get the locatin links and update date
            $model->getCitationLinks($sitemap_links);
        }
        
        //load Glossary links
        $model->getGlossaryLinks($sitemap_links);
        
        //check if the option is checked
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('topics_data')) {
            $model->getTopicsLinks($sitemap_links);
        }

        if (Joomla\CMS\Factory::getApplication()->getInput()->get('misc_data')) {
            $model->getMiscellaneousLinks($sitemap_links);
        }
        
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('news_data')) {
            $model->getNewsArchiveLinks($sitemap_links);
        }
        
        //load DOCMAN Documents links
        $model->getDocManDocLinks($sitemap_links);
       
        //data is ready, now make it sitemap
        $sitemap_data = $model->generateSitemap($sitemap_links);
        $totalENURLs = count($sitemap_links);
        
        //save in the sitemap file
        $full_sitemap_data .= $sitemap_data;
        $model->writeSitemapToFile($sitemap_data, $this->sitemapFiles['en']);
        $sitemap_data = '';
        $sitemap_links = array();
        
        ######################################################
        ############ Generate FR sitelinks  ##################
        ######################################################
        
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('region_data')) {
            $model->getRegionsLinks($sitemap_links,'fr');
        }
        
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('citation_data')) {
            //get the locatin links and update date
            $model->getCitationLinks($sitemap_links,'fr');
        }
        
        //load Glossary links
        $model->getGlossaryLinks($sitemap_links,'fr');

        if (Joomla\CMS\Factory::getApplication()->getInput()->get('misc_data')) {
            $model->getMiscellaneousLinks($sitemap_links,'fr');
        }
        
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('news_data')) {
            $model->getNewsArchiveLinks($sitemap_links,'fr');
        }
        
        //load DOCMAN Documents links
        $model->getDocManDocLinks($sitemap_links,'fr');
        
        //data is ready, now make it sitemap
        $sitemap_data = $model->generateSitemap($sitemap_links);
        $totalFRURLs = count($sitemap_links);
        
        //save in the sitemap file
        $full_sitemap_data .= $sitemap_data;
        $model->writeSitemapToFile($sitemap_data, $this->sitemapFiles['fr']);
        $sitemap_data = '';
        $sitemap_links = array();
        
        ######################################################
        ############ Generate ES sitelinks  ##################
        ######################################################
        
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('region_data')) {
            $model->getRegionsLinks($sitemap_links,'es');
        }
        
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('citation_data')) {
            //get the locatin links and update date
            $model->getCitationLinks($sitemap_links,'es');
        }
        
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('misc_data')) {
            $model->getMiscellaneousLinks($sitemap_links,'es');
        }
        
        //load Glossary links
        $model->getGlossaryLinks($sitemap_links,'es');
        
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('news_data')) {
            $model->getNewsArchiveLinks($sitemap_links,'es');
        }
        
        //load DOCMAN Documents links
        $model->getDocManDocLinks($sitemap_links,'es');
        
        //data is ready, now make it sitemap
        $sitemap_data = $model->generateSitemap($sitemap_links);
        $totalESURLs = count($sitemap_links);
        
        //save in the sitemap file
        $full_sitemap_data .= $sitemap_data;
        $model->writeSitemapToFile($sitemap_data,$this->sitemapFiles['es']);
        $sitemap_data = '';
        $sitemap_links = array();
        
        ######################################################
        ############ Generate NEWS Articles sitelinks  #######
        ######################################################
        $sitemap_links = $model->generateNewsLinks();
        $sitemap_data  = $model->generateSitemap($sitemap_links['en']);
        $model->writeSitemapToFile($sitemap_data,$this->sitemapFiles['en_news']);
        
        $sitemap_data = $model->generateSitemap($sitemap_links['es']);
        $model->writeSitemapToFile($sitemap_data,$this->sitemapFiles['es_news']);
        
        $sitemap_data = $model->generateSitemap($sitemap_links['fr']);
        $model->writeSitemapToFile($sitemap_data,$this->sitemapFiles['fr_news']);
        $sitemap_links = array();
        $sitemap_data='';
        
        ######################################################
        ########## Now Write to File and Compress  ###########
        ######################################################

        //data is ready, now make it sitemap
        $sitemapFiles = $this->sitemapFiles;
        
        //load view
        $view = & $this->getView('Sitemap', 'html');
       /* $view->assign('totalENURLs', $totalENURLs);
        $view->assign('totalFRURLs', $totalFRURLs);
        $view->assign('totalESURLs', $totalESURLs);
        $view->assign('totalURLs', $totalENURLs+$totalESURLs+$totalFRURLs);*/

        $view->totalENURLs=$totalENURLs;
        $view->totalFRURLs=$totalFRURLs;
        $view->totalESURLs=$totalESURLs;
        $view->totalURLs=$totalENURLs+$totalESURLs+$totalFRURLs;

        //save in the sitemap file
        //$model->writeSitemapToFile($sitemap_data);
        //$view->saveconfigs();
        
        //compress sitemap
        $sitemapFiles = array('en' => $this->sitemapFiles['en'] . '.gz',
                              'es' => $this->sitemapFiles['es'] . '.gz',
                              'fr' => $this->sitemapFiles['fr'] . '.gz',
                
                              'en_news' => $this->sitemapFiles['en_news'] . '.gz',
                              'es_news' => $this->sitemapFiles['es_news'] . '.gz',
                              'fr_news' => $this->sitemapFiles['fr_news'] . '.gz',
                        );
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('compress')) {
             $model->gzipSitemap($sitemapFiles);
        }
        
        //generate sitemap index file
        $sitemapIndexData = $model->generateSitemapIndex($sitemapFiles);
        $model->writeSitemapToFile($sitemapIndexData,'sitemap_ssl.xml');
        
        /*$view->assign('sitemap_contents', $sitemapIndexData);*/
        $view->sitemap_contents = $sitemapIndexData;
        $view->saveconfigs();
    }

}
