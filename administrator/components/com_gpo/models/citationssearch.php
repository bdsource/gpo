<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');
jimport( 'sphinxapi' );

class GpoModelCitationsSearch extends JModelList
{
	var $total;
	var $data;

	function __construct()
	{
		parent::__construct();
		$this->max_matches = 100000;
		$this->total = (int)'0';
		$this->limit = Joomla\CMS\Factory::getApplication()->getInput()->get('limit', '100', 'GET', 'int');		
		$this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', 'GET', 'int');
		
		//echo '<!-- limit: ' . $this->limit . '-->';
		$this->results = null;
		
		$this->cl = new SphinxClient();
		
		$mode = SPH_MATCH_EXTENDED2;
		$host = "localhost";
		$port = 9312;
		$ranker = SPH_RANK_PROXIMITY_BM25;
		
		$this->cl->SetServer ( $host, $port );
		$this->cl->SetConnectTimeout ( 1 );
		$this->cl->SetWeights ( array ( 100, 1 ) );
		$this->cl->SetMatchMode ( $mode );
		$this->cl->SetRankingMode ( $ranker );
		$this->cl->SetArrayResult ( false );
		if( $this->limit == 0 )
		{
			$this->cl->SetLimits( (int)$this->limitstart, $this->max_matches, $this->max_matches );
		}else{
			$this->cl->SetLimits( (int)$this->limitstart, (int)$this->limit, $this->max_matches );			
		}

	}
	
	
	
	function news()
	{
		if( !isset( $_GET['citation'] ) )
		{
			return;
		}

    //global $mainframe, $option;
 		
		$mainframe =& JFactory::getApplication();
                $option = Joomla\CMS\Factory::getApplication()->getInput()->get('option');

                $this->index = "search_gpo_admin_citations_news";
		$searchQuery = $_GET['citation'];
		$sphinxQuery ='';

//Enable the WebSource, SourceDoc, Notes and City pages for searching

		$looking = explode(",", 'category,source,title,byline,subtitle,websource,share,notes,content,ext_id' );
		
		$sphinxQuery = "";
// added condition for 0 to catch "share"		
		foreach( $looking as $key )
		{
			if( !empty( $searchQuery[ $key ] ) || $searchQuery[ $key ] === '0' )
			{
				$sphinxQuery  .= GpoSearchTidy( $this->cl, $key, $searchQuery[ $key ] );
			}
		}

		if( !empty( $searchQuery['published_range']['from'] ) && !empty( $searchQuery['published_range']['to'] ) )
		{
			$pr_from = strtotime( $searchQuery['published_range']['from'] );
			$pr_to = strtotime( $searchQuery['published_range']['to'] );
			$this->cl->SetFilterRange ( "published", $pr_from, $pr_to );		
		}else if( !empty( $searchQuery['published_range']['from'] ) )
		{
			$pr_from = strtotime( $searchQuery['published_range']['from'] );
			$pr_to = time();
			$this->cl->SetFilterRange ( "published", $pr_from, $pr_to );
		}
		
		$orderby = $_GET['filter_order'];
		$orderbydir = ( $_GET['filter_order_Dir'] === 'asc' ) ? 'ASC' : 'DESC';		
		switch( $orderby )
		{
			case 'published':
				$this->cl->SetSortMode ( SPH_SORT_EXTENDED, "published " . $orderbydir . ", @id DESC" );
				break;
			case 'id':
				$this->cl->SetSortMode ( SPH_SORT_EXTENDED, "@id " . $orderbydir . ", published DESC" );
				break;
			default:
				$this->cl->SetSortMode ( SPH_SORT_EXTENDED, "published DESC, @id DESC" );
				break;
		}

		$res = $this->cl->Query( $sphinxQuery, $this->index );
//if there are results
		if( isset( $res['matches'] ) )
		{		
			$ids = array_keys( $res['matches'] );			
			$this->total = $res['total_found'];
			$query = "SELECT `c`.* FROM `#__gpo_citations_news` as `c` WHERE `c`.`id` IN( "  . implode(",", $ids ) . " ) ORDER BY FIELD(  `c`.`id`, "  . implode(",", $ids ) . ");";
			$this->_db->setQuery( $query );
			$this->results = $this->_db->loadAssocList();
		}
		
		$this->pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		return;
	}
	
	

	function quotes()
	{
		if( !isset( $_GET['citation'] ) )
		{
			return;
		}

                //global $mainframe, $option;

                $mainframe =& JFactory::getApplication();
                $option = Joomla\CMS\Factory::getApplication()->getInput()->get('option');

                $this->index = "search_gpo_admin_citations_quotes";
		$searchQuery = $_GET['citation'];
		$sphinxQuery ='';

//Enable the WebSource, SourceDoc, Notes and City pages for searching
		$looking = explode(",", 'title,source,publisher,volume,issue,page,city,author,content,websource,share,notes,content,ext_id,sourcedoc' );
//		$looking = explode(",", 'category,source,title,byline,subtitle,websource,share,notes,content,ext_id' );
		
		$sphinxQuery = "";
// added condition for 0 to catch "share"		
		foreach( $looking as $key )
		{
			if( !empty( $searchQuery[ $key ] ) || $searchQuery[ $key ] === '0' )
			{
				$sphinxQuery  .= GpoSearchTidy( $this->cl, $key, $searchQuery[ $key ] );
			}
		}

		if( !empty( $searchQuery['published_range']['from'] ) && !empty( $searchQuery['published_range']['to'] ) )
		{
			$pr_from = strtotime( $searchQuery['published_range']['from'] );
			$pr_to   = strtotime( $searchQuery['published_range']['to'] );
                        
                        $pr_from_td = $this->to_days( str_replace('/', '-', $searchQuery['published_range']['from']) );
			$pr_to_td   = $this->to_days( str_replace('/', '-', $searchQuery['published_range']['to']) );
                        
			$this->cl->SetFilterRange ( "published", $pr_from_td, $pr_to_td );		
		}else if( !empty( $searchQuery['published_range']['from'] ) )
		{
			$pr_from = strtotime( $searchQuery['published_range']['from'] );
			$pr_to   = time();
                        
                        $pr_from_td = $this->to_days( str_replace('/', '-', $searchQuery['published_range']['from']) );
			$pr_to_td   = $this->to_days( date("d-m-Y") );
			$this->cl->SetFilterRange( "published", $pr_from_td, $pr_to_td );
		}
		
		$orderby = $_GET['filter_order'];
		$orderbydir = ( $_GET['filter_order_Dir'] === 'asc' ) ? 'ASC' : 'DESC';		
		switch( $orderby )
		{
			case 'published':
				$this->cl->SetSortMode ( SPH_SORT_EXTENDED, "published " . $orderbydir . ", @id DESC" );
				break;
			case 'id':
				$this->cl->SetSortMode ( SPH_SORT_EXTENDED, "@id " . $orderbydir . ", published DESC" );
				break;
			default:
				$this->cl->SetSortMode ( SPH_SORT_EXTENDED, "published DESC, @id DESC" );
				break;
		}

                //echo 'sphinx query: ' . $sphinxQuery; 
                
		$res = $this->cl->Query( $sphinxQuery, $this->index );
//if there are results
		if( isset( $res['matches'] ) )
		{
			$ids = array_keys( $res['matches'] );			
			$this->total = $res['total_found'];
			$query = "SELECT `c`.* FROM `#__gpo_citations_quotes` as `c` WHERE `c`.`id` IN( "  . implode(",", $ids ) . " ) ORDER BY FIELD(  `c`.`id`, "  . implode(",", $ids ) . ");";
			$this->_db->setQuery( $query );
			$this->results = $this->_db->loadAssocList();
		}
		
		$this->pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		return;
	}
        
        /*
         * 
         * Taken from ref: https://www.ibm.com/developerworks/library/os-sphinx/index.html
         * This method helps to search dates prior to epoch 1970, UNIX timestamp 
         * 
         */
        function to_days($date) {
            return 719528 + floor(strtotime($date)/(60*60*24));
        }
        
}
?>
