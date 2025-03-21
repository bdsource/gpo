<?php
function ftp_debug( $item, $name='', $eol=true, $exit=true )
{
	echo '<pre>DEBUG INFO</pre>';
	echo '<pre>' . $name . ":" . ( ( $eol ) ? "\n" : '' ) . print_r( $item, true ) . '</pre>';
	if( $exit )
	{
		exit();		
	}
}

function GpoEndWith( $end, $str )
{
	$str = trim( $str );
	if( empty( $str ) ) return '';
	
	$end_len = strlen( $end );
	$str_len = strlen( $str );
	
	
	if( substr( $str,( $str_len - $end_len )-1 ) !== $end )
	{
		$str .= $end;
	}
	return  $str;
}


function GpoDeleteFromCache( $type )
{	
	$filename = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'cache' . DS . $type . '.txt';
	if( file_exists( $filename ) )
	{
            unlink( $filename );
	}
}

function GpoSaveTypeToCache( $type, $data )
{	
	$data = trim( $data );
	$filename = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'cache' . DS . $type . '.txt';
        file_put_contents( $filename, $data );
}


function GpoGetTypeFromCache( $type  )
{
	$filename = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'cache' . DS . $type . '.txt';
	
  if( file_exists( $filename ) )
	{
		return file_get_contents( $filename );
	}else{
		return false;
	}
}


function GpoGetAllLocationNames() 
{
    $db = JFactory::getDBO();
    $query = 'SELECT 
                   * 
              FROM 
                   `#__gpo_location` as `lo`
              WHERE `lo`.`display` = 1
             ';
    $db->setQuery($query);
    $allLocations = $db->loadObjectList();
    $locationsArray = array();
    
    foreach($allLocations as $key => $val) {
       $locationsArray[ trim($val->name) ] = $val;
    }
    
    return $locationsArray;
}


function GpoGetHtmlForType( $type )
{
  
  $data = GpoGetTypeFromCache( $type );
	if( $data !== false )
	{
		return explode("\r\n", $data );
	}
        
	$db =& JFactory::getDBO();
	$query = 'SELECT `data` FROM `#__gpo_lists_order` WHERE `type`= '.$db->quote( $type ).';';	
	$db->setQuery( $query );
	$data = $db->loadResult();
	if( empty($data))
	{
		return array('Missing List data: '.$type );
	}
	$data = json_decode($data);
	GpoSaveTypeToCache( $type, implode("\r\n", $data ));
	return $data;
}



function GpoSearchSphinxEscapeString( $str )
{
	$from = array ( '\\', '(',')','-','!','@','~','&', '/', '^', '$', '=' );
    $to   = array ( '\\\\', '\(','\)','\-','\!','\@','\~', '\&', '\/', '\^', '\$', '\=' );
    
    $str = str_replace( $from, $to, $str );
    
	$from = array ( ' OR ' );
	$to   = array ( ' | ' );
	$str = str_replace( $from, $to, $str );

//This is not perfect... would need a reg exp to capture ' NOT    APPLE' = ' -APPLE'
	$from = array ( ' NOT ' );
	$to   = array ( ' -' );
	$str = str_replace( $from, $to, $str );
	
    return $str; 
}

class gpo_helper
{
	function strip( $str )
	{
		$str = strip_tags( $str );
		$str = str_replace( "\r\n","\n", $str );
		$str = str_replace( "\r","\n", $str );
		return trim( $str );
	}
	
	function short( $str, $signature = '' )
	{

		$str = gpo_helper::strip( $str );
		if( isset( $str{'476'}) )
		{
			$str = substr( $str,0,475);
		}
		$words = explode( " ", $str );

		if( count( $words ) > 1 )
		{
			$last = end( $words );
			array_pop( $words );
			$str = implode( " ", $words );			
		}else{
			$str = $words['0'];
		}


		$str .= "... ";
		if( empty( $signature ) )
		{
			$str .= "(" . Joomla\CMS\Factory::getApplication()->getCfg('sitename') . ")";
		}else{
			$str .= $signature;
		}
		return $str;
	}
  
  function ln2br( $str )
	{
		return str_replace( "\n","<br />", $str );
	}
  
  function full_length( $str, $signature='' )
	{
		$find = array("\r\n" => "\n", "\n\n\n\n" => "\n", "\n\n\n" => "\n\n", "<br>"=>"\n", "<br />" => "\n");		
		$str = strtr( $str, $find );		
		$str = str_replace("\n","<br />", $str );
		
		if( $signature !== false )
		{
			if( $signature === '' )
			{
				$ending = ' (' . Joomla\CMS\Factory::getApplication()->getCfg('sitename') . ')';
			}else{
				$ending = $signature;
			}
			$str = GpoEndWith($ending, $str );
		}
		return $str;
	}
}


function Gpo_allow_region( $group_name='Public Backend' )
{
    
//Check the acl group of a user...
	//$oUser =& JFactory::getUser();
//	$acl =& JFactory::getACL();
	//var_dump ($acl);
//$grp = $acl->getGroupsByUser($oUser->get( 'id' ));
  //$grp = $acl->getAroGroup( $oUser->get( 'id' ) );
//Because this works here, I can set once the sql string and then reuse it :)
	//return ( $acl->is_group_child_of($grp->name, $group_name ) === '1' ) ? true : false;
  //temporarily set

  return false;

}
//very specific
function Gpo_location_display_sql( $group_name='Public Frontend' )
{
	if( Gpo_allow_region( $group_name )  === false )
	{
		$sql = ' AND `lo`.`display`=1 ';
	}else{
		$sql = ' ';
	}
	return $sql;
}


function GpoClearRssCache()
{		
	$cmd = 'rm -f ' . JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'cache' . DS . 'rss' . DS . '*';
	exec( $cmd, $output );
}

function GpoSearchTidy( $api, $key, $str )
{
	$str = trim( $str );
    
    //share value (0 or 1) could be zero
	if( empty( $str ) && 'share' != $key )
	{
		return '';
	}
	
	return ' @(' . $key . ') (' . GpoSearchSphinxEscapeString( $str ) . ')';
	//return ' @(' . $key . ') ' . $api->EscapeString( $str );
	//return '@(' . $key . ') ' . $str;
}

function GpoDefaultRssFeeds()
{
	if( 
		strpos( $_SERVER[ 'REQUEST_URI' ], '/topic/' ) === false &&
		strpos( $_SERVER[ 'REQUEST_URI' ], '/topics/' ) === false &&
		substr( $_SERVER[ 'REQUEST_URI' ], -7 ) !== '/topics'
	)
	{
		return <<<RSS
	<link rel="alternate" type="application/rss+xml" title="Gun Policy News World" href="/firearms/rss/?k=&l=World" />
	<link rel="alternate" type="application/rss+xml" title="Gun Policy News North America" href="/firearms/rss/?k=&l=North+America" />
	<link rel="alternate" type="application/rss+xml" title="Gun Policy News USA" href="/firearms/rss/?k=&l=United+States" />
	<link rel="alternate" type="application/rss+xml" title="Gun Policy News NonUSA" href="/firearms/rss/?k=&l=-United+States" />
	<link rel="alternate" type="application/rss+xml" title="Gun Policy News Central America" href="/firearms/rss/?k=&l=Central+America" />
	<link rel="alternate" type="application/rss+xml" title="Gun Policy News South America" href="/firearms/rss/?k=&l=South+America" />
	<link rel="alternate" type="application/rss+xml" title="Gun Policy News Europe" href="/firearms/rss/?k=&l=Europe" />
	<link rel="alternate" type="application/rss+xml" title="Gun Policy News Asia" href="/firearms/rss/?k=&l=Asia" />
	<link rel="alternate" type="application/rss+xml" title="Gun Policy News West Asia" href="/firearms/rss/?k=&l=West+Asia" />	
	<link rel="alternate" type="application/rss+xml" title="Gun Policy News Africa" href="/firearms/rss/?k=&l=Africa" />
	<link rel="alternate" type="application/rss+xml" title="Gun Policy News Oceania" href="/firearms/rss/?k=&l=Oceania" />
RSS;
	}
	return '';
}