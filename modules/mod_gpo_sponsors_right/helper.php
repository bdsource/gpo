<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Uri\Uri;

class modgposponsorshelperright
{
    /**
     * Retrieves the Sponsor Logo
     *
     * @param array $params An object containing the module parameters
     * @access public
     */
        
    function getSponsors() {

        $lang = JFactory::getLanguage();
        $langTag = $lang->getTag();
        $otherLangs = array('fr', 'es');
        $uriLangCode = ""; //for default english it is empty string

        if (strlen($langTag) > 2) {
            $langCode = strtolower(substr($langTag, 0, -3));
        }
        
        if( in_array($langCode, $otherLangs) ) {
            $uriLangCode = $langCode;
        }
        
       // $uri_object = &JFactory::getURI();
        //$request_uri = modgposponsorshelperright::_filterUri($uri_object->toString(array('path')));
        $request_uri = modgposponsorshelperright::_filterUri(Uri::getInstance());
        
//        echo 'requesturi right==';
//        var_dump( $uri_object->toString(array('path')) );
//        var_dump($request_uri);

        if (strpos($request_uri, 'jupgrade')) {

            $request_uri = explode("/", $request_uri);

            array_shift($request_uri);
            array_shift($request_uri);

            $request_uri = implode($request_uri, "/");
        }
        
        ### Special fix for DocMan documents URL ### 
        if ( (strpos($request_uri, '/documents/') !== false) || (strpos($request_uri, '/documents-staff/') !== false) ) {
           $request_uri = '/documents';
        }
        
        if (strpos($request_uri, 'firearms/latest') !== false) {
            $uri = $uriLangCode . '/firearms/latest';
        } else if (strpos($request_uri, 'firearms/compare') !== false) {
            $uri = $uriLangCode . '/firearms/compare';
        } else if (strpos($request_uri, 'firearms/compareyears') !== false) {
            $uri = $uriLangCode . '/firearms/compareyears';
        } else if (strpos($request_uri, 'firearms/news/archive') !== false) {
            $uri = $uriLangCode . '/firearms/news/archive';
        } else if (strpos($request_uri, 'firearms/news') !== false) {
            $uri = $uriLangCode . '/firearms/news';
        } else if (strtolower($request_uri) == 'search') {
            $uri = $uriLangCode . '/firearms/search';
        } else {
            $uri = $request_uri;
        }

        if (substr($uri, 0, 1) == "/") {
            $uri = substr($uri, 1);
        }

        if (substr($uri, -1) == "/") {
            $uri = substr($uri, 0, -1);
        }
        //var_dump($uri);
        $uri = rawurldecode($uri);

        $sponsors = modgposponsorshelperright::_getSponsors($uri, $langCode);

        if (count($sponsors) > 0) {
            //we found an entry for this url, so return
            return $sponsors;
        }

        $ancestorSponsors = modgposponsorshelperright::_lookForAncestorRegions($request_uri, $langCode);

        if (!empty($ancestorSponsors)) {
            return $ancestorSponsors;
        }

        return false;
    }
    
    
    function _getSponsors( $uri, $langCode=false ) {
        
        $otherLangs = array('fr', 'es');
        $isLanguagePage = false;

        if (in_array($langCode, $otherLangs)) {
            $isLanguagePage = true;
        }
        
        $suburi = substr($uri,0,3);
        if( 'en/' == $suburi ) {
           $isLanguagePage = true; //if in anycase 'en' (default lang) is present in the URI
        }
        
	    $hash = md5( $uri );
    	$db = JFactory::getDBO();
    	$query = "SELECT * FROM `#__gpo_sponsors` WHERE `url_hash`= " . $db->quote( $hash );

        $db->setQuery( $query );
    	$sponsors = $db->loadObjectList();

        if( empty($sponsors) && $isLanguagePage ) {
           ## Remove language prefix, try to show from the default en listing
           $uri = substr($uri,3);
           $newHash = md5($uri);
           $query = "SELECT * FROM `#__gpo_sponsors` WHERE `url_hash`= " . $db->quote( $newHash );
           $db->setQuery( $query );
    	   $sponsors = $db->loadObjectList();
        }
        
    	return $sponsors;
    }

    
    function _search( $uri )
    {
			$url_search = "/index.php?task=search";
			$url_search = "firearms/search";
			if( strpos(Uri::getInstance(), $url_search ) === false )
			{
				return $uri;
			}
			$uri = "search";
            
			return $uri;
    }
    
    /*
    function _maleCompareURI( $uri )
    {
			$uriParts = explode('/',$uri);
            
			$url_search = "firearms/search";
			if( strpos(Uri::getInstance(), $url_search ) === false )
			{
				return $uri;
			}
			$uri = "search";
            
			return $uri;
    } 
    */
    
    function _filterUri( $request_uri ) {
        
            $request_uri = trim( $request_uri );
        
            /* for testbed site only */
			if ( strpos($request_uri, '/testbed') !== false ) {
				$request_uri = str_replace( '/testbed', '', $request_uri );
			}
			$request_uri = modgposponsorshelperright::_search( $request_uri );
            
			return $request_uri;
    }
    
    
    function _lookForAncestorRegions( $request_uri, $langCode=false ) {
    	
    	if ( strpos($request_uri, 'region') === false ) {
            return false; // if not region url then return
    	}
    	
    	$catAlias = modgposponsorshelperright::_getRegionAliasFromURI( $request_uri );
    	$locationInfo = modgposponsorshelperright::_getLocationInfoByAlias( $catAlias );
    	$ancestors = modgposponsorshelperright::_getRegionAncestors( $locationInfo->id );
    	
    	foreach ( $ancestors as $arow) {
    	   if( empty($arow->alias) ) { continue; }
    	   $uri = modgposponsorshelperright::_makeUri( $arow->alias, $langCode );
    	   if ( empty($uri) ) { continue; }
    	   $sponsors = modgposponsorshelperright::_getSponsors( $uri, $langCode );
    	   if( count($sponsors) > 0 ) {
    	       $ancestorSponsors = $sponsors;
    	       break;
    	   }
    	}
        
    	return $ancestorSponsors;
    }
    
    
    function _getRegionAliasFromURI( $p_juri )
    {

    	$p_juri = ($p_juri) ? $p_juri : Uri::getInstance();

    	if( empty($p_juri) )
    	{
    		return false;
    	}

    	$juri_array = array_reverse( explode('/',$p_juri) );
    	$regionAlias = trim( $juri_array[0] );
    	return $regionAlias;
    }
    
    
    function _getLocationInfoByAlias( $p_alias ) {
   	   if( empty($p_alias) ) {
   	   	  return false;
   	   }
   	   
       $db = &JFactory::getDBO();
   	   
   	   $query = 	'SELECT `lo`.`id`, `lo`.`type`,`lo`.`name`,`cat`.`id` as catid' .
					' FROM `#__gpo_location` as `lo`' .
					' INNER JOIN `#__categories` as `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .
					' WHERE `cat`.`alias`=' . $db->quote( $p_alias ) . ' LIMIT 0,1;';	

   	   $db->setQuery( $query );
   	   return $db->loadObject();
    }
    
    
    function _getRegionAncestors( $location_id ) {
        $db = &JFactory::getDBO();
    	$current_location_id = $location_id;
        $crumbs = array();
    	if(  !empty( $location_id ) )
    	{
    		while( !empty( $location_id ) )
    		{
    			//location_id for the location
    			//cat due to how the route.php works.
    			$query = 'SELECT `link`.`location_id`, `cat`.`id`,`cat`.`alias`,`cat`.`title` as `name` ' .
						 'FROM `#__gpo_location_links` as `link` ' .
						 'INNER JOIN `#__gpo_location` as `lo` ON `lo`.`id` = `link`.`location_id` ' .
						 'INNER JOIN `#__categories` as `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .
						 'WHERE `link`.`link_id`=' . $db->Quote($location_id);
                         /*$query = 'SELECT `link`.`location_id`, `cat`.`id`,`cat`.`alias`,`cat`.`title` as `name` ' .
						 'FROM `j25_gpo_location_links` as `link` ' .
						 'INNER JOIN `j25_gpo_location` as `lo` ON `lo`.`id` = `link`.`location_id` ' .
						 'INNER JOIN `j25_categories` as `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .
						 'WHERE `link`.`link_id`=' . $db->Quote($location_id);*/

                //echo $query;
    			$db->setQuery( $query );
    			$item = $db->loadObject();
    			if( isset( $item->location_id ) )
    			{
    				$location_id = $item->location_id;
    				$crumbs[]=$item;
    			}else{
    				$location_id = '';
    			}
    		}
    	}
    	return $crumbs;
    }
    
    
    function _makeUri( $alias, $langCode='' ) {
        
        if( empty($alias) ) {
            return false;    
        }
        
        if( empty($langCode) || 'en' == $langCode ) {
            return 'firearms/region/' . $alias;
        }else {
            return $langCode . '/firearms/region/' . $alias;  
        }
        
    	return false;   
    }
    
}
?>