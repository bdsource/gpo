<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 use Joomla\CMS\Uri\Uri;
class footerhelper
{
    /**
     * Retrieves the footer credit(article authors) of the region page
     *
     * @param array $params An object containing the module parameters
     * @access public
     */

    function getArticleAuthors($params = '') {

        $lang = JFactory::getLanguage();
        $langTag = $lang->getTag();
        //$otherLangs = array('fr', 'es');

        if (strlen($langTag) > 2) {
            $langCode = strtolower(substr($langTag, 0, -3));
        }
        $uri = \Joomla\CMS\Uri\Uri::getInstance()->toString(array('path'));
        $request_uri = footerhelper::_filterUri($uri);
        //----component/gpo
        //----firearms
        $uri_old = str_replace('component/gpo/region', 'firearms/region', $request_uri);
        $uri = $request_uri; 
        
        if (strpos($request_uri, 'jupgrade') !== false) {
            $arr_uri = explode("/", $request_uri);

            $sh_uri = array_shift($arr_uri);
            $sh_uri = array_shift($arr_uri);

            $uri = implode('/', $arr_uri);
            $uri = '/' . $uri;
        }

        if (strpos($request_uri, 'firearms/latest') !== false) {
            $uri = '/firearms/latest';
        } else if (strpos($request_uri, 'firearms/news') !== false) {
            $uri = '/firearms/news';
        }

        //use the request URI if it passed directly as a parameter
        if( !empty($params->requestURI) ) {
            $uri = $params->requestURI;
        }
        
        if (substr($uri, 0, 1) == "/") {
            $uri = substr($uri, 1);
        }

        if (substr($uri, -1) == "/") {
            $uri = substr($uri, 0, -1);
        }
        $uri = rawurldecode($uri);
        
/* ----- to the same for $uri_old ------ */
     
        if (substr($uri_old, 0, 1) == "/") {
            $uri_old = substr($uri_old, 1);
        }

        if (substr($uri_old, -1) == "/") {
            $uri_old = substr($uri_old, 0, -1);
        }
        $uri_old = rawurldecode($uri_old);
        
/* -------- */
        $footer = footerhelper::_getFooter($uri, $langCode, $uri_old);
        
        if (count($footer) > 0) {
            //we found an entry for this url, so return
            if ($footer->is_published == 1):
                return $footer->footer_credit;
            elseif (0 == $footer->is_published):
                return false;
            endif;
        }

        //comment out this section if you want to turn off the location hierarchy 
        //feature for footers too.
        $ancestorFooter = footerhelper::_lookForAncestorRegions($uri, $langCode);
        
        if (!empty($ancestorFooter)) {
            //we found an entry of the ancestor of this url, so return
            if (1 == $ancestorFooter->is_published):
                return $ancestorFooter->footer_credit;
            //elseif (0 == $ancestorSponsors->is_published):
            //    return false;
            endif;
        }
        $defaultFooter = footerhelper::_getDefaultFooter($uri);
        
        //no footer entry found for this url, so return the default
        return $defaultFooter;
    }
    
    
    function _getFooter( $uri, $langCode = false , $uri_old) {
        //echo $uri_old;
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
        if ($uri_old) {
            $oldHash = md5($uri_old);
        }
        else {
            $oldHash = $hash;
        }
        $db =& JFactory::getDBO();
    	$query = "SELECT * FROM `#__gpo_dpfooters` WHERE `url_hash`= " . $db->quote( $hash ). " OR `url_hash`= " . $db->quote( $oldHash );
        //echo '<br/>';echo $query;
        $db->setQuery( $query );
    	$sponsors = $db->loadObject();
        //echo $uri;
        //echo '==='; print_r($sponsors); echo '===';
        
        if( empty($sponsors) && $isLanguagePage ) {
           ## Remove language prefix, try to show from the default en listing
           $uri = substr($uri,3);
           $newHash = md5($uri);
           $query = "SELECT * FROM `#__gpo_dpfooters` WHERE `url_hash`= " . $db->quote( $newHash );
                             // echo '----'.$query.'---------';die();
           $db->setQuery( $query );
    	   $sponsors = $db->loadObject();
        }

        return $sponsors;
    }
    
    function _getDefaultFooter( $uri ) {
    	$hash = md5( $uri );
    	$df = $article_authors = "Alpers, Philip and Marcus Wilson.";
    	return $df;
    }
    

    function _search( $uri )
    {       $jinput = JFactory::getApplication()->input;
			$url_search = "/index.php?task=search";
			$url_search = "firearms/search";
			if( strpos( $jinput->getURI(), $url_search ) === false )
			{
				return $uri;
			}
			$uri = "search";
			return $uri;
    }
    
    
    function _filterUri( $request_uri ) {
            $request_uri = trim( $request_uri );
            
            /* for testbed site only */
			if ( strpos($request_uri, '/testbed') !== false ) {
				 $request_uri = str_replace( '/testbed', '', $request_uri );
			}
			$request_uri = footerhelper::_search( $request_uri );
			return $request_uri;
    }
    
    
    function _lookForAncestorRegions( $request_uri, $langCode=false ) {
    	
    	if ( strpos($request_uri, 'region') === false ) {
            return false; // if not region url then return
    	}
    	
    	$catAlias     = footerhelper::_getRegionAliasFromURI( $request_uri );
    	$locationInfo = footerhelper::_getLocationInfoByAlias( $catAlias );	
    	$ancestors    = footerhelper::_getRegionAncestors( $locationInfo->id );
    	
    	foreach ( $ancestors as $arow) {
    	   if( empty($arow->alias) ) { continue; }
    	   $uri = footerhelper::_makeUri( $arow->alias, $langCode );
    	   if ( empty($uri) ) { continue; }
    	   $sponsors = footerhelper::_getFooter( $uri, $langCode, null );
    	   if( count($sponsors) > 0 ) {
    	      $ancestorSponsors = $sponsors;
    	      break;
    	   }
    	}
    	return $ancestorSponsors;
    }
    
    
    function _getRegionAliasFromURI( $p_juri )
    {
       $jinput = JFactory::getApplication()->input;
    	$p_juri = ($p_juri) ? $p_juri : $jinput->getURI();

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
    
    function getLastModifiedDate($locationId) 
    {
        if( empty($locationId) ) {
            return date('Y-m-d H:i:s');
        }
        $db = &JFactory::getDBO();
        $query = "SELECT `id`, `updated_at` FROM `#__gpo_datapages` WHERE `location_id`=" . $db->Quote($locationId);
        $db->setQuery($query);
        $timestamps = $db->loadObject();
        $modified = $timestamps->updated_at;
        
        //for empty date, show today's date
        if(empty($modified) || '0000-00-00 00:00:00' == $modified) {
            $modified = date('Y-m-d H:i:s');
        }
        
        return $modified;
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