<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');
jimport( 'sphinxapi' );

class GpoModelNewsSearch extends JModelList
{
	var $total;
	var $data;

	function __construct()
	{ 
		parent::__construct();
        ## This is the max that sphinx is set to, to go over you need to change her and in .conf
		$this->max_matches = 50000;
		$this->total = (int)'0';
		$jinput = JFactory::getApplication()->input;

		$this->limit = $jinput->getVar('limit', '100', 'GET', 'int');		
		$this->limitstart = $jinput->getVar('limitstart', '0', 'GET', 'int');
		
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
		if( $this->limit == 0 )
		{
			$this->cl->SetLimits( (int)$this->limitstart, $this->max_matches, $this->max_matches );
		}else{
			$this->cl->SetLimits( (int)$this->limitstart, (int)$this->limit, $this->max_matches );			
		}

	}
	
	function backEnd()
	{
		if( !isset( $_GET['news'] ) )
		{
			return;
		}

        global $mainframe, $option;
 		
		$this->index = "gpo_admin_search_news";
		$searchQuery = $_GET['news'];
		$sphinxQuery ='';

// if a location has been set, we then do the location lookup.
		if( !empty( $searchQuery['locations'] ) )
		{
			$locations = explode(",", trim( $searchQuery['locations'] ) );
			$sql_location = '';
			
			$location_ids = array();
			
			foreach( $locations as $k => $lo )
			{
				$query = "SELECT `l`.`id`, CONCAT( '.', GROUP_CONCAT( `ld`.`link_id`, CHAR(11) SEPARATOR '.|.' ), '.' ) as `ids` FROM `#__gpo_location` as `l` LEFT JOIN  `#__gpo_location_links_deep` as `ld` ON `l`.`id` = `ld`.`location_id` WHERE `l`.`name` = " . $this->_db->quote( $lo )  . " GROUP BY `l`.`id`;";	
	
				$this->_db->setQuery( $query );
				$o = $this->_db->loadObject();

				if( !empty( $o->id ) )
				{
					$location_ids[] = "." . $o->id . ".";
				}
				if( !empty( $o->ids ) )
				{
					$a = explode( "|", $o->ids );
					foreach( $a as $id )
					{
						$location_ids[] = $id;
					}
				}				
			}
			$location_ids = array_unique( $location_ids );
			$searchQuery['location_ids'] = implode( "|", $location_ids );
		}
		else if( !empty( $searchQuery['keywords'] ) &&  !isset($searchQuery['many']) )
		{
			$keywords = explode(",", trim( $searchQuery['keywords'] ) );
			$sql_location = '';
			
			$news_ids = array();
			
			foreach( $keywords as $k => $keyword )
			{
				$query = "SELECT `id` FROM `#__gpo_news` WHERE (`keywords` LIKE '".$keyword.",%' OR `keywords` LIKE '%, ".$keyword."' OR `keywords` LIKE '%, ".$keyword.",%' OR `keywords` = '".$keyword."')"  ;	
                
				$this->_db->setQuery( $query );
				$o = $this->_db->loadObject();

				if( !empty( $o->id ) )
				{
					$news_ids[] = $o->id;
				}		
			}
			$news_ids = array_unique( $news_ids );
			$searchQuery['news_ids'] = implode( "|", $news_ids );
		}

//Enable the WebSource, SourceDoc, Notes and City pages for searching

		$looking = explode(",", 'source,title,byline,subtitle,keywords,websource,gpnheader,share,notes,content,category' );
		
		$sphinxQuery = "";
// added condition for 0 to catch "share"
//Deal with many
		if( $searchQuery[ 'many' ] === '1' && !empty( $searchQuery[ 'content' ] ) )
		{
			$sphinxQuery .= "@(source,title,subtitle,content,byline,gpnheader,category) " . GpoSearchSphinxEscapeString( $searchQuery[ 'content' ] );
			unset( $searchQuery[ 'source' ] );
			unset( $searchQuery[ 'title' ] );
			unset( $searchQuery[ 'subtitle' ] );
			unset( $searchQuery[ 'content' ] );
			unset( $searchQuery[ 'byline' ] );
			unset( $searchQuery[ 'gpnheader' ] );			
			unset( $searchQuery[ 'category' ] );			
		}
		
		foreach( $looking as $key )
		{
			if( !empty( $searchQuery[ $key ] ) || $searchQuery[ $key ] === '0' )
			{
				$sphinxQuery  .= GpoSearchTidy( $this->cl, $key, $searchQuery[ $key ] );
			}
		}

		if( isset( $searchQuery['location_ids'] ) )
		{
			$sphinxQuery .= " @location_ids(" . $searchQuery['location_ids'] . ")";
		}
		if( isset( $searchQuery['news_ids'] ) )
		{
			$sphinxQuery .= " @news_ids(" . $searchQuery['news_ids'] . ")";
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

		if(isset($searchQuery['news_ids']))
		{
			$ids = $news_ids;
			$query = "SELECT `n`.*, DATE_FORMAT(`n`.`published`, '%Y%m%d' ) as `published_hash`,GROUP_CONCAT( `l`.`name` ) as locations FROM `#__gpo_news` as `n` LEFT JOIN `#__gpo_news_locations` as `nl` ON `n`.`id`=`nl`.`ext_id` LEFT JOIN `#__gpo_location` as `l` ON `l`.`id` = `nl`.`location_id` WHERE `n`.`id` IN( "  . implode(",", $ids ) . " ) GROUP BY `n`.`id` ORDER BY FIELD(  `n`.`id`, "  . implode(",", $ids ) . ");";
				$this->_db->setQuery( $query );
				$this->results = $this->_db->loadAssocList();
				$this->total = count($this->results);
		}else{
			$res = $this->cl->Query( $sphinxQuery, $this->index );

            //if there are results
			if( isset( $res['matches'] ) )
			{
				$ids = array_keys( $res['matches'] );			
				$this->total = $res['total_found'];
				$query = "SELECT `n`.*, DATE_FORMAT(`n`.`published`, '%Y%m%d' ) as `published_hash`,GROUP_CONCAT( `l`.`name` ) as locations FROM `#__gpo_news` as `n` LEFT JOIN `#__gpo_news_locations` as `nl` ON `n`.`id`=`nl`.`ext_id` LEFT JOIN `#__gpo_location` as `l` ON `l`.`id` = `nl`.`location_id` WHERE `n`.`id` IN( "  . implode(",", $ids ) . " ) GROUP BY `n`.`id` ORDER BY FIELD(  `n`.`id`, "  . implode(",", $ids ) . ");";
				$this->_db->setQuery( $query );
				$this->results = $this->_db->loadAssocList();
			}
		}
		$this->pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		return;
	}
}
