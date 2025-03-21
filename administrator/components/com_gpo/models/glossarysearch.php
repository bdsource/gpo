<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');
jimport( 'sphinxapi' );

class GpoModelGlossarySearch extends JModelList{
	var $total;
	var $data;

	function __construct(){
		parent::__construct();
		$this->max_matches = 1000;
		$this->total = (int)'0';
		$this->limit = Joomla\CMS\Factory::getApplication()->getInput()->get('limit', '100', 'GET', 'int');
		$this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', 'GET', 'int');
		
		$this->results = null;
		$this->cl = new SphinxClient();
		$mode = SPH_MATCH_EXTENDED2;
		$host = "alpers.org";
		$port = 9312;
		$ranker = SPH_RANK_PROXIMITY_BM25;
		
		$this->cl->SetServer ( $host, $port );
		$this->cl->SetConnectTimeout ( 1 );
		$this->cl->SetWeights ( array ( 100, 1 ) );
		$this->cl->SetMatchMode ( $mode );
		$this->cl->SetRankingMode ( $ranker );
		$this->cl->SetArrayResult ( false );
		if( $this->limit == 0 ){
			$this->cl->SetLimits( (int)$this->limitstart, $this->max_matches, $this->max_matches );
		}else{
			$this->cl->SetLimits( (int)$this->limitstart, (int)$this->limit, $this->max_matches );			
		}
	}
	
	
	function backEnd(){
		if( !isset( $_GET['glossary'] ) ){
			return;
		}

        //global $mainframe, $option;
 		
		$mainframe =& JFactory::getApplication();
    $option = Joomla\CMS\Factory::getApplication()->getInput()->get('option');
    
    $this->index = "gpo_glossary_search_index";
		$searchQuery = $_GET['glossary'];
		$looking = explode(",", 'title,subtitle,websource,content' );
		$sphinxQuery = "";

		foreach( $looking as $key ){
			if( !empty( $searchQuery[ $key ] ) || $searchQuery[ $key ] === '0' ){
				$sphinxQuery  .= GpoSearchTidy( $this->cl, $key, $searchQuery[ $key ] );
			}
		}
    
    if( !empty( $searchQuery['modified_from']) && !empty( $searchQuery['modified_to']) ){ 
			$pr_from = strtotime( $searchQuery['modified_from'] );
			$pr_to = strtotime( $searchQuery['modified_to'] );
			$this->cl->SetFilterRange ( "modified", $pr_from, $pr_to );		
		}else if( !empty( $searchQuery['modified_from']) ){
			$pr_from = strtotime( $searchQuery['modified_from']);
			$pr_to = time();
			$this->cl->SetFilterRange ( "modified", $pr_from, $pr_to );
		}

        //set the ID limit
        if(!empty($searchQuery['id_range']['from'])) {
            $id_range_from = $searchQuery['id_range']['from'];
        } else {
            $id_range_from = 1;
        }

        if(!empty($searchQuery['id_range']['to'])){
            $id_range_to = $searchQuery['id_range']['to'];
        } else {
            $id_range_to = 1000000;
        }
        $this->cl->SetIDRange( intval($id_range_from), intval($id_range_to));


		$orderby = @$_GET['filter_order'];
		$orderbydir = ( @$_GET['filter_order_Dir'] === 'asc' ) ? 'ASC' : 'DESC';
  
		switch( $orderby ){
			case 'modified':
				$this->cl->SetSortMode ( SPH_SORT_EXPR, "modified " . $orderbydir . ", @id DESC" );
				break;
			case 'id':
				$this->cl->SetSortMode ( SPH_SORT_EXPR, "@id " . $orderbydir . ", modified DESC" );
				break;
			default:
				$this->cl->SetSortMode ( SPH_SORT_ATTR_DESC, "modified" );			
				break;
		}
   
		$res = $this->cl->Query( $sphinxQuery, $this->index);
    
		if( isset( $res['matches'])){
			$ids = array_keys( $res['matches'] );
			$this->total = $res['total_found'];
			$query = "SELECT * FROM #__gpo_datapage_glossary WHERE published=1 AND id IN (".implode(',', $ids ).")";
			$this->_db->setQuery( $query );
			$this->results = $this->_db->loadAssocList();
		}
		$this->pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		return;
	}
}
