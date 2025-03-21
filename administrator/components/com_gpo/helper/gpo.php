<?php
// No direct access.
defined('_JEXEC') or die();

$shared_functions = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'helper' . DS . 'shared_functions.php';
require_once( $shared_functions );

jimport('gpo.html_stripper');

//this is a quick hack to hide menus that are not permitted for non-admins

$oUser	=  JFactory::getUser();
if(!$oUser->get('isRoot')) {
    echo '<style type="text/css">
	ul#submenu li:nth-child(7), ul#submenu li:nth-child(8), ul#submenu li:nth-child(9),
	ul#submenu li:nth-child(10),ul#submenu li:nth-child(11),ul#submenu li:nth-child(12),
	ul#submenu li:nth-child(13),ul#submenu li:nth-child(14){
	    display:none;
	}
    </style>';
}


function currentDate( $value )
{
	if( empty( $value )){ return false; }
	$d=date_parse( $value );
	if( (int)$d['error_count'] === (int)'0')
	{
            date_default_timezone_set('Australia/Sydney');
            //$time_now = ( isset( $_SERVER['REQUEST_TIME'] ) ) ? $_SERVER['REQUEST_TIME'] : date('U');
            $time_now = date('U');

            if(strtotime( $value ) <= $time_now )
            {
                    return true;
            }
	}
	return false;
}


function required( $value )
{
	if(is_string( $value ) )
	{
		$value = trim($value);
                
		return ( !empty( $value ) ) ? true : false;
	}else if( is_array( $value) ){
		if( count( $value ) > 1 )
		{
			return true;
		}
		return( !empty( $value['0'] ) )? true : false;
	}
	return false;
}


function requiredNumeric( $value )
{      
    if(is_string( $value ) )
	{
		$value = trim($value);	               
                if($value==="0"){  
                    return  true;
                } 
                else{
		   return ( !empty( $value ) ) ? true : false;
	   }
        }
    return false;  
}

function matchCoord($value)
{
    //sending curl request and retrieving coordinate from geolocation
    return true;
    $curl = curl_init();  
    //Set some options - we are passing in a useragent too here
     curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => 'https://maps.googleapis.com/maps/api/geocode/json?address=Mountain+View,+CA&key=AIzaSyC-X4ayzV0zy3tTvHjf5_A_ZcAjQ57pwIM'
      ));
     
     $response = json_decode(curl_exec($curl), true);
      
     if ($response['status'] != 'OK') {
            echo 'An error has occured: ' . print_r($response);
         }else {
           $geometry = $response['results'][0]['geometry'];
           $longitude = $geometry['location']['lat'];
           $latitude = $geometry['location']['lng'];
     }
    /*
    echo "<script type=\"text/javascript\">";
    echo "alert('";
    echo $latitude;
    echo "');";
    echo "</script>";
    */
      
    curl_close($curl);
    return true;  //temporarily   
}

function json_encode_plus( $data )
{
	return addslashes( json_encode( $data ) );
}


function viewHtmlAddPrototype()
{		
	$document = &JFactory::getDocument();
	$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');		
	$mootools = JURI::root(true).'/media/system/js/mootools.js';
	if( isset( $document->_scripts[$mootools]))
	{
		unset( $document->_scripts[$mootools]);
	}
}

function GpoDefaultPublishTime()
{
    // set default timezone to client's timezone
    date_default_timezone_set('Australia/Sydney');
    
    //return $_SERVER['REQUEST_TIME'] - (int)86400;
    return date('U');
}

function GpoCleanContentForEmail( $str )
{
	$find = array("\r\n" => "\n", "<br>" => "\n", "<br />" => "\n",  );
	$str = strtr( $str, $find );
	$str = str_replace( "\n", "<br />", $str );
	$str = str_replace( "<br />", "\r\n<br />", $str );	
	$str = trim( $str,"<br />");
	return $str;
}

function GpoStrip( $str )
{
	$find = array("\r\n" => "\n", "<br>" => "\n", "<br />" => "\n" );
	$str = strtr( $str, $find );
	$str = str_replace( "\n","", $str );
	$str = trim( $str );
	return $str;	
}

/*
Confirm this.
*/
function GpoSingleLine( $val )
{
	$lookup= "\n";	
	$pos = strpos( $val, $lookup );
	if( $pos !== false )
	{
		$val = substr($val,0,$pos);
	}
	$val = trim( $val );	
	return $val;
}




function GpoCleanInput( $val, $forWhich=NULL )
{
		if( is_string( $val ) )
		{
			$val = trim( $val );
			$find = array("<br>","<br />","\r\n", "\r" );
			$val = str_replace( $find, "\n", $val );
//this is to take care of potential "multiple" versions of \n.
			$lookup= "\n\n\n";
			$replace = "\n\n";
			while( strpos( $val, $lookup ) !== false )
			{
				$val = str_replace( $lookup, $replace, $val );
			}
			
			$val = trim( $val );
//now we html clean it.
			$html_stripper=new html_stripper();
			$html_stripper->allow_tags( array() );
			$val = $html_stripper->clean( $val );

		}else if( is_array( $val ) )
		{
			foreach( $val as $k=>$v )
			{
				$val[$k] = GpoCleanInput( $v );
			}
		}else{
			return $val;
		}
		
	
		$db =& JFactory::getDBO();
//get all items to cleanup
		$query = "
SELECT * FROM `#__gpo_news_cleanup`
ORDER BY `id` DESC;
";	
		$db->setQuery( $query );
		$data = $db->loadObjectList();
		
		if( $data !== false )
		{
			foreach( $data as $clean )
			{
				$val = str_replace($clean->from, $clean->to, $val );
			}
		}
		return $val;
}


/* New Additions 
 * 
 * Since 28th Feb, 2016
 * 
 */

/**
 * Content component helper.
 *
 * @package     Gpo
 * @subpackage  com_gpo
 * @since       3.0
 */
class GpoHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName = '')
	{
        $oUser           = JFactory::getUser();
        $userId          = $oUser->get('id');
        $isRoot          = $oUser->get('isRoot');
		$groupsUserIsIn  = JAccess::getGroupsByUser($userId);
        ##groupusers: 7 and 8 is administrator and super administrator
        $isAdministrator = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;  
        
        $db = JFactory::getDBO();
        $submenuQuery = "SELECT 
                                m.id, m.title, m.alias, m.link, m.parent_id, m.img, e.element
                        FROM 
                                #__menu AS m
                        LEFT JOIN 
                                #__extensions AS e 
                        ON 
                                m.component_id = e.extension_id
                        WHERE 
                                m.client_id = 1 AND e.enabled = 1 
                                AND m.id > 1 
                                AND e.element = 'com_gpo' 
                        ORDER BY m.lft 
                        ";
        
        $db->setQuery($submenuQuery);
        $allSubmenus        = $db->loadObjectList();
        $submenusGunPolicy  = array();
        $submenusSuperAdmin = array();
    
        foreach($allSubmenus as $key => $val) {
            $parts = parse_url($val->link);
            parse_str($parts['query'], $params);
            $viewName = $params['controller'];
			$linkTitle = trim(strtolower(str_replace(' ','',$val->title)));
            if($val->parent_id == 122) {
                $submenusGunPolicy[] = array('title' => $val->title, //JText::_('COM_API_TITLE_KEYS'), 
                                             'link'  => $val->link, 
                                             'view'  => $vName == $linkTitle);
                
                if($vName == $linkTitle) {
                    $isGpo = TRUE;
                }
            }
            //var_dump($vName);
			//var_dump($linkTitle);
            //var_dump($viewName);
			if($val->parent_id == 143) {
                $submenusSuperAdmin[] = array('title' => $val->title,
                                             'link'   => $val->link,
                                             'view'   => $vName == $linkTitle);
                
                if($vName == $linkTitle) {
                    $isSA = TRUE;
                }
            }
         
        }

        if( $isGpo && $isAdministrator ) {
            $submenus = $submenusGunPolicy; 
        }
        
        if( $isSA && $isRoot ) {
            $submenus = $submenusSuperAdmin; 
        }
        
		foreach ($submenus as $submenu)
		{
			if (version_compare(JVERSION, '3.0.0', 'ge'))
			{
				JHtmlSidebar::addEntry(
					$submenu['title'],
					$submenu['link'],
					$submenu['view']
				);
			}
			else
			{
				JSubMenuHelper::addEntry(
					$submenu['title'],
					$submenu['link'],
					$submenu['view']
				);
			}
		}
	}
	
}
?>