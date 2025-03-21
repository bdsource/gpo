<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');
jimport('sphinxapi');

class GpoModelQuotesSearch extends JModelList {

    var $total;
    var $data;
    var $sphinxQuery;

    function __construct() {
        parent::__construct();
        $this->max_matches = 100000;
        $this->total = (int) '0';
        $this->limit = Joomla\CMS\Factory::getApplication()->getInput()->get('limit', '100', 'GET', 'int');
        $this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', 'GET', 'int');
        $this->sphinxQuery = '';

        $this->results = null;
        $this->cl = new SphinxClient();
        $mode = SPH_MATCH_EXTENDED2;
        $host = "alpers.org";
        $port = 9312;
        $ranker = SPH_RANK_PROXIMITY_BM25;

        $this->cl->SetServer($host, $port);
        $this->cl->SetConnectTimeout(1);
        $this->cl->SetWeights(array(100, 1));
        $this->cl->SetMatchMode($mode);
        $this->cl->SetRankingMode($ranker);
        $this->cl->SetArrayResult(false);
        if ($this->limit == 0) {
            $this->cl->SetLimits((int) $this->limitstart, $this->max_matches, $this->max_matches);
        } else {
            $this->cl->SetLimits((int) $this->limitstart, (int) $this->limit, $this->max_matches);
        }
    }

    function backEnd() {
        if (!isset($_GET['quotes'])) {
            return;
        }

        //global $mainframe, $option;
        $mainframe = & JFactory::getApplication();
        $option = Joomla\CMS\Factory::getApplication()->getInput()->get('option');

        $this->index = "gpo_admin_search_quotes";
        $searchQuery = $_GET['quotes'];

        $sphinxQuery = '';
        // if a location has been set, we then do the location lookup.
        if (!empty($searchQuery['locations'])) {
            $locations = explode(",", trim($searchQuery['locations']));
            $sql_location = '';

            $location_ids = array();

            foreach ($locations as $k => $lo) {
                $query = "SELECT `l`.`id`, CONCAT( '.', GROUP_CONCAT( `ld`.`link_id`, CHAR(11) SEPARATOR '.|.' ), '.' ) as `ids` FROM `#__gpo_location` as `l` LEFT JOIN  `#__gpo_location_links_deep` as `ld` ON `l`.`id` = `ld`.`location_id` WHERE `l`.`name` = " . $this->_db->quote($lo) . " GROUP BY `l`.`id`;";

                $this->_db->setQuery($query);
                $o = $this->_db->loadObject();

                if (!empty($o->id)) {
                    $location_ids[] = "." . $o->id . ".";
                }
                if (!empty($o->ids)) {
                    $a = explode("|", $o->ids);
                    foreach ($a as $id) {
                        $location_ids[] = $id;
                    }
                }
            }

            $location_ids = array_unique($location_ids);
            $searchQuery['location_ids'] = implode("|", $location_ids);
        }

        if (2 != $searchQuery['share']) {
             ## Search in public or private records; that means check values in share field
             ## Enable the WebSource, SourceDoc, Notes and City pages etc. for searching		
            $looking = explode(",", 'title,author,keywords,content,source,websource,sourcedoc,notes,city,publisher,staff,poaim,share');
        } else {
            ## Search in all records; that means just ignore the 'share' field 
            $looking = explode(",", 'title,author,keywords,content,source,websource,sourcedoc,notes,city,publisher,staff,poaim');
        }

        $sphinxQuery = "";

// added condition for 0 to catch "share"		
        foreach ($looking as $key) {
            if (!empty($searchQuery[$key]) || $searchQuery[$key] === '0') {
                $sphinxQuery .= GpoSearchTidy($this->cl, $key, $searchQuery[$key]);
            }
        }
//ftp_debug('here','before looing',true,false );

        if (isset($searchQuery['location_ids'])) {
            $sphinxQuery .= " @location_ids(" . $searchQuery['location_ids'] . ")";
        }


        if (!empty($searchQuery['published_range']['from']) && !empty($searchQuery['published_range']['to'])) {
            $pr_from = strtotime($searchQuery['published_range']['from']);
            $pr_to = strtotime($searchQuery['published_range']['to']);

            $pr_from_td = $this->to_days(str_replace('/', '-', $searchQuery['published_range']['from']));
            $pr_to_td = $this->to_days(str_replace('/', '-', $searchQuery['published_range']['to']));

            $this->cl->SetFilterRange("published", $pr_from_td, $pr_to_td);
        } else if (!empty($searchQuery['published_range']['from'])) {
            $pr_from = strtotime($searchQuery['published_range']['from']);
            $pr_to = time();

            $pr_from_td = $this->to_days(str_replace('/', '-', $searchQuery['published_range']['from']));
            $pr_to_td = $this->to_days(date("d-m-Y"));

            $this->cl->SetFilterRange("published", $pr_from_td, $pr_to_td);
        }

        //set poaim in filter
        if (!empty($searchQuery['poaim'])) {
            //$this->cl->setFilter('poaim',$searchQuery['poaim']);
        }

        //set the ID limit
        if (!empty($searchQuery['id_range']['from'])) {
            $id_range_from = $searchQuery['id_range']['from'];
        } else {
            $id_range_from = 1;
        }

        if (!empty($searchQuery['id_range']['to'])) {
            $id_range_to = $searchQuery['id_range']['to'];
        } else {
            $id_range_to = 1000000;
        }
        $this->cl->SetIDRange(intval($id_range_from), intval($id_range_to));


        $orderby = $_GET['filter_order'];
        $orderbydir = ( $_GET['filter_order_Dir'] === 'asc' ) ? 'ASC' : 'DESC';

        switch ($orderby) {
            case 'published':
                $this->cl->SetSortMode(SPH_SORT_EXTENDED, "published " . $orderbydir . ", @id DESC");
                break;
            case 'id':
                $this->cl->SetSortMode(SPH_SORT_EXTENDED, "@id " . $orderbydir . ", published DESC");
                break;
            default:
                $this->cl->SetSortMode(SPH_SORT_EXTENDED, "published DESC, @id DESC");
//				$this->cl->SetSortMode ( SPH_SORT_EXTENDED, "@relevance DESC,  published DESC, @id DESC" );
                break;
        }

        /* if(!empty($searchQuery['poaim'])){
          if($searchQuery['poaim'] ==  '*'){
          $sphinxQuery .= "@(poaim) (\*)";
          } else {
          $sphinxQuery  .= GpoSearchTidy( $this->cl, 'poaim', $searchQuery['poaim'] );
          }
          } */
        
        $res = $this->cl->Query($sphinxQuery, $this->index);
        
        //if there are results
        if (isset($res['matches'])) {
            $ids = array_keys($res['matches']);
            $this->total = $res['total_found'];
            $query = "SELECT `q`.*, DATE_FORMAT(`q`.`published`, '%Y%m%d' ) as `published_hash`,GROUP_CONCAT( `l`.`name` ) as locations FROM `#__gpo_quotes` as `q` LEFT JOIN `#__gpo_quotes_locations` as `ql` ON `q`.`id`=`ql`.`ext_id` LEFT JOIN `#__gpo_location` as `l` ON `l`.`id` = `ql`.`location_id` WHERE `q`.`id` IN( " . implode(",", $ids) . " ) GROUP BY `q`.`id` ORDER BY FIELD(  `q`.`id`, " . implode(",", $ids) . ");";
            $this->_db->setQuery($query);
            $this->results = $this->_db->loadAssocList();
        }
        $this->pagination = new JPagination($this->total, $this->limitstart, $this->limit);
        $this->sphixnQuery = $sphinxQuery;
        return;
    }

    /*
     * 
     * Taken from ref: https://www.ibm.com/developerworks/library/os-sphinx/index.html
     * This method helps to search dates prior to epoch 1970, UNIX timestamp 
     * 
     */

    function to_days($date) {
        return 719528 + floor(strtotime($date) / (60 * 60 * 24));
    }

}
