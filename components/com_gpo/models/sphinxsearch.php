<?php
//save
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');
jimport( 'sphinxapi' );

class GpoModelSphinxsearch extends JModelLegacy
{

	function __construct()
	{
		parent::__construct();		
		
		$this->path = "/home/palpers/gp-uploads/news/";
		
		 $jinput = Joomla\CMS\Factory::getApplication()->getInput();
        
//this is the max that sphinx is set to, to go voer you need to change her and in .conf
		$this->max_matches = 100000;
		$this->start = $jinput->get( 'start', '0', 'int' );
		$this->total = (int)'0';
		$this->limit = (int)'20';
		$this->limitstart = $jinput->get( 'limitstart', '0', 'int' );

		$this->cl = new SphinxClient();
		
		$mode = SPH_MATCH_EXTENDED2;
		$host = "localhost";
		$port = 9312;
		$groupby = "";
		$groupsort = "@group desc";
		$filter = "group_id";
		$filtervals = array();
		$distinct = "";
		$sortby = "";
		$ranker = SPH_RANK_PROXIMITY_BM25;
		$select = "";
		
		$this->cl->SetServer ( $host, $port );
		$this->cl->SetConnectTimeout ( 1 );
		$this->cl->SetWeights ( array ( 100, 1 ) );
		$this->cl->SetMatchMode ( $mode );
		$this->cl->SetRankingMode ( $ranker );
		$this->cl->SetArrayResult ( false );
		$this->cl->SetLimits( (int)$this->start, (int)$this->limit, $this->max_matches );
	}



//FIX - this is getting completely redone
	function frontend( $query='', $location='' )
	{                 

		$oUser=JFactory::getUser();
		//echo $oUser->id;
        if (empty($oUser->id)) {
        	// it is public user 
        	//echo 'public';
        	$where_public = " AND `n`.`share` = '1'";
        }
        else {
        	$where_public = '';
        }
		$this->q = trim( $query );
		if( !empty( $this->q ) )
		{
			$this->q = rawurldecode( $this->q );
			$this->q = trim( $this->q );	
		}
	$location= str_replace('+', ' ' , $location);
	//$location = 'United Kingdom'; <--- here was the problem it didn't give correct location name 
		$this->index = "gpo_admin_search_news";
//sphinx query;					
		$sphinxQuery ='';
		
		if( !empty( $location ) )
		{
			$location = trim( $location );
			$location = rawurldecode( $location );
			$query = "SELECT `l`.`id`, CONCAT( '.', GROUP_CONCAT( `ld`.`link_id`, CHAR(11) SEPARATOR '.|.' ), '.' ) as `ids` FROM `#__gpo_location` as `l` LEFT JOIN  `#__gpo_location_links_deep` as `ld` ON `l`.`id` = `ld`.`location_id` WHERE `l`.`name` =" . $this->_db->quote( $location ) . " GROUP BY `l`.`id`;";		
//echo 'test!-';echo($query); die(); //<-- this is working take a look bellow  GpoSearchSphinxEscapeString  probably Sphinx engine doesn not work 
		 // <---
			$this->_db->setQuery( $query );
			$o = $this->_db->loadObject();
			$location_ids = "";
			if( !empty( $o->id ) )
			{
				$this->location = $location;
				$location_ids .="." . $o->id . ".";
			}
			if( !empty( $o->ids ) )
			{
				$location_ids .= ( ( !empty( $location_ids ) ) ? "|" : ""  ) . $o->ids;
			}
		}	
//$location_ids = $o->id;
		
		//is there a query
		if( !empty( $this->q ) )
		{
			$search_query = GpoSearchSphinxEscapeString( $this->q );
			$sphinxQuery .= " @(source,title,subtitle,content,byline,gpnheader) " . $search_query;			
		}
		if( isset( $location_ids ) )
		{
			$sphinxQuery .= " @location_ids (" . $location_ids . ")";
//echo $sphinxQuery; die();

		}	
//		$this->cl->SetSortMode ( SPH_SORT_ATTR_DESC, "published" );
//		$this->cl->SetSortMode ( SPH_SORT_EXTENDED, "@relevance DESC,  published DESC, @id DESC" );		
		$this->cl->SetSortMode ( SPH_SORT_EXTENDED, "@id DESC, published DESC" );				
		$res = $this->cl->Query( $sphinxQuery, $this->index );
//echo 't-----------';print_r($res);die();
//if there are results
		if( isset( $res['matches'] ) )
		{		
			$ids = array_keys( $res['matches'] );			
			$this->total = $res['total_found'];
			$query = "SELECT `n`.*, DATE_FORMAT(`n`.`published`, '%Y%m%d' ) as `published_hash`,GROUP_CONCAT( `l`.`name` ) as locations FROM `#__gpo_news` as `n` LEFT JOIN `#__gpo_news_locations` as `nl` ON `n`.`id`=`nl`.`ext_id` LEFT JOIN `#__gpo_location` as `l` ON `l`.`id` = `nl`.`location_id` WHERE `n`.`id` IN( "  . implode(",", $ids ) . " )".$where_public." GROUP BY `n`.`id` ORDER BY FIELD(  `n`.`id`, "  . implode(",", $ids ) . ")";
//echo $query; die();
			$this->_db->setQuery( $query );
			$this->articles = $this->_db->loadObjectList();
//print_r($this->articles[1]);die(); <-- ovde nije dobro neki artikli lokacije nisu dobre
		}
 //   echo 'testtest'; echo '';echo $query; die();    
//print_r($this->articles); die();
		$this->pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		return $this->articles;
	}


	function members( $input )
	{		
		$location = ( !empty( $input['l'] ) ) ? $input['l'] : $input['region'];
		if( empty( $location ) ) $location = $input['country'];
		
		unset( $input['country'] );
		unset( $input['region'] );
		

		$this->index = "gpo_admin_search_news";		
//sphinx query;					
		$sphinxQuery ='';
		
		if( !empty( $location ) )
		{
			$location = trim( $location );
			$location = rawurldecode( $location );
			$query = "SELECT `l`.`id`, CONCAT( '.', GROUP_CONCAT( `ld`.`link_id`, CHAR(11) SEPARATOR '.|.' ), '.' ) as `ids` FROM `#__gpo_location` as `l` LEFT JOIN  `#__gpo_location_links_deep` as `ld` ON `l`.`id` = `ld`.`location_id` WHERE `l`.`name` =" . $this->_db->quote( $location ) . " GROUP BY `l`.`id`;";				

			$this->_db->setQuery( $query );
			$o = $this->_db->loadObject();
			$location_ids = "";
			if( !empty( $o->id ) )
			{
				$this->location = $location;
				$location_ids .="." . $o->id . ".";
			}
			if( !empty( $o->ids ) )
			{
				$location_ids .= ( ( !empty( $location_ids ) ) ? "|" : ""  ) . $o->ids;
			}
		}	

		$sphinxQuery = '';
		if( empty( $input['q'] ) )
		{
			if( isset( $input[ 'many' ] ) )
			{
				$sphinxQuery .= ' @share 1 ';
				if( !empty( $input[ 'content' ] ) )
                {
                	$sphinxQuery .= ' @(source,title,subtitle,content,byline,gpnheader) ' . GpoSearchSphinxEscapeString( $input[ 'content' ] ) . ' ';
                }
				$remove = explode( ",", 'source,title,subtitle,byline,gpnheader,content,share' );
				foreach( $remove as $key )
				{
					if( isset( $input[ $key] ) )
					{
						unset( $input[ $key ] );
					}
				}
			}
						
			$allow = explode(",", 'title,subtitle,source,category,byline,keywords,content,gpnheader,share' );
//convert date range - look to admin.
	
//build query
			$parts = array();
			if( is_array( $input ) )
			{
				foreach( $input as $key => $value )
				{
					if( !in_array( $key, $allow ) ) continue;
				
					$str = trim( $value );
					$str = rawurldecode( $str );
					if( empty( $str ) ) continue;	
					
					$parts[] = GpoSearchTidy( $this->cl, $key, $str );
				}	
			}
			
			$ignore = explode(",","fromdate,todate,id");
			$pass = false;
			foreach( $ignore as $key )
			{
				if( !empty( $input[$key] ) )
				{
					$pass = true;
				}
			}
			if( isset( $parts['0'] ) )
			{
				$sphinxQuery .= implode( " ", $parts );
			}
			
			if( empty( $sphinxQuery ) && empty( $location_ids ) && $pass === false )
			{
				return false;
			}
			
//When does this get called?			
		}else{
			$q = $input['q'];
			$q = trim( $q );
			$q = rawurldecode( $q );
			$sphinxQuery =" @* " . $q;
		}

//add the formated location_ids 1|2|3|
		if( isset( $location_ids ) )
		{
			$sphinxQuery .= " @location_ids (" . $location_ids . ")";
		}	

		if( !empty( $input['fromdate'] ) && !empty( $input['todate'] ) )
		{
			$pr_from = GpoStrToTime( $input['fromdate'] );
			$pr_to = GpoStrToTime( $input['todate'] );
			$this->cl->SetFilterRange ( "published", $pr_from, $pr_to );		
		}else if( !empty( $input['fromdate'] ) )
		{
			$pr_from = GpoStrToTime( $input['fromdate'] );
			$pr_to = time();
			$this->cl->SetFilterRange ( "published", $pr_from, $pr_to );
		}
		
		if( !empty( $input['id'] ) )
		{
			$ids = $this->searchIds( $input['id'] );
			switch( $this->type )
			{
				case 'in':
					$this->cl->SetFilter ( "@id", $ids );
//					$sphinxQuery .= " @id (" . implode("|",$ids ) . ")";
					break;
				case 'range':
					$this->cl->SetIDRange( $ids['0'], $ids['1'] );
					break;
			}
		}
		
//		echo '<!-- sphinxQuery: ' . $sphinxQuery . '-->';

		$this->cl->SetSortMode ( SPH_SORT_EXTENDED, "published DESC, @id DESC" );				
		$res = $this->cl->Query( $sphinxQuery, $this->index );

//if there are results
		if( isset( $res['matches'] ) )
		{		
			$ids = array_keys( $res['matches'] );			
			$this->total = $res['total_found'];

			$query = "SELECT `n`.*, DATE_FORMAT(`n`.`published`, '%Y%m%d' ) as `published_hash`,GROUP_CONCAT( `l`.`name` ) as locations FROM `#__gpo_news` as `n` LEFT JOIN `#__gpo_news_locations` as `nl` ON `n`.`id`=`nl`.`ext_id` LEFT JOIN `#__gpo_location` as `l` ON `l`.`id` = `nl`.`location_id` WHERE `n`.`id` IN( "  . implode(",", $ids ) . " ) GROUP BY `n`.`id` ORDER BY FIELD(  `n`.`id`, "  . implode(",", $ids ) . ")";			
			$this->_db->setQuery( $query );
			$this->articles = $this->_db->loadObjectList();			
		}		
		$this->pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		return;
	}
	
	
	function searchIds( $ids )
	{
		$ids = trim( $ids );
		$ids = str_replace(" ",'', $ids );

		if( strpos( $ids, "," ) !== false )
		{
			$explode_with = ',';
			$this->type = 'in'; 
		}else if( strpos( $ids, "-" ) )
		{
			$explode_with = '-';
			$this->type = 'range';
		}else{
			return false;
		}
		$ids = explode( $explode_with, $ids );
		foreach( $ids as $id )
		{
			if( !ctype_digit( $id ) )
			{
				return false;
			}
		}
		return $ids;
	}//end membersSearchIds
	
	
	
//I can delete this.
	function searchRss( $q='', $location='' )
	{
		$this->index = "gpo_admin_search_news";		
//sphinx query;					
		$sphinxQuery ='';

		$not = false;		
// if a location has been set, we then do the location lookup.
		if( !empty( $location ) )
		{
			$location = trim( $location );
			$location = rawurldecode( $location );

			
			if(substr($location,0,1)=== '-')
			{
				$not=true;
				$location = ltrim( $location,'-');
				$query = "
SELECT `l`.`id`
FROM `#__gpo_location` as `l`
WHERE `l`.`name` =" . $this->_db->quote( $location ) . ";
";
				$this->_db->setQuery( $query );
				$id = $this->_db->loadResult();				
				
				$ids = array();
				if( !empty( $id  ) )
				{
//SELECT CONCAT( '-.',`ld`.`link_id`,CHAR(11),'.')
					$query = "
SELECT `ld`.`link_id`
FROM `#__gpo_location` as `l` 
LEFT JOIN  `#__gpo_location_links_deep` as `ld` ON `l`.`id` = `ld`.`location_id` 
WHERE `l`.`id` =" . $id . " 
";
					$this->_db->setQuery( $query );
					$ids = $this->_db->loadColumn();
					$ids[]=$id;
					
					$query = "
SELECT CONCAT( '.',`l`.`id`,CHAR(11),'.')
FROM `#__gpo_location` as `l`
WHERE `l`.`id` NOT IN( " . implode(',', $ids ) . " );
";

					$this->_db->setQuery( $query );
					$ids = $this->_db->loadColumn();
					
				}
				$location_ids = implode("|", $ids );
			}else{
				$query = "SELECT `l`.`id`, CONCAT( '.', GROUP_CONCAT( `ld`.`link_id`, CHAR(11) SEPARATOR '.|.' ), '.' ) as `ids` FROM `#__gpo_location` as `l` LEFT JOIN  `#__gpo_location_links_deep` as `ld` ON `l`.`id` = `ld`.`location_id` WHERE `l`.`name` =" . $this->_db->quote( $location ) . " GROUP BY `l`.`id`;";
	
				$this->_db->setQuery( $query );
				$o = $this->_db->loadObject();
				
				$location_ids = "";
				if( !empty( $o->id ) )
				{
					$this->location = $location;
					$location_ids .="." . $o->id . ".";
				}
				if( !empty( $o->ids ) )
				{
					$location_ids .= ( ( !empty( $location_ids ) ) ? "|" : ""  ) . $o->ids;
				}				
			}
		}

		//is there a query
		if( !empty( $q ) )
		{
			$sphinxQuery ="@* " . $q;
		}
		
		if( isset( $location_ids ) )
		{
			if( $not )
			{
				$sphinxQuery .= " @location_ids (" . $location_ids . ")";					
			}else{
				$sphinxQuery .= " @location_ids (" . $location_ids . ")";
			}


		}


				
		$this->cl->SetLimits( 0, 50 );
		$this->cl->SetSortMode ( SPH_SORT_EXTENDED, "published DESC, @id DESC" );
		$res = $this->cl->Query( $sphinxQuery, $this->index );
//if there are results
		if( isset( $res['matches'] ) )
		{		
			$ids = array_keys( $res['matches'] );

			$query = "SELECT `n`.*, DATE_FORMAT(`n`.`published`, '%Y%m%d' ) as `published_hash`,GROUP_CONCAT( `l`.`name` ) as locations FROM `#__gpo_news` as `n` LEFT JOIN `#__gpo_news_locations` as `nl` ON `n`.`id`=`nl`.`ext_id` LEFT JOIN `#__gpo_location` as `l` ON `l`.`id` = `nl`.`location_id` WHERE `n`.`id` IN( "  . implode(",", $ids ) . " ) AND `n`.`share`=1 GROUP BY `n`.`id` ORDER BY FIELD(  `n`.`id`, "  . implode(",", $ids ) . ") LIMIT 0,50";
			$this->_db->setQuery( $query );
			$data = $this->_db->loadObjectList();
			return $data;
		}
		return false;
	}
	
	
	
	function isReIndexInProgress()
	{
		$filename = $this->path . 'reindex_inprogress.txt';
		if( file_exists( $filename ) )
		{
			return true;
		}
		return false;
	}	
}
