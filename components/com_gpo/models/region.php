
<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class GpoModelRegion extends JModelLegacy
{
	var $id = null;
	
	function __construct()
	{
		parent::__construct();
	}

	
	function intro($id = '', $regionName = '') { 
        $this->oUser =  JFactory::getUser();
        //7 and 8 is administrator and super administrator
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $this->isAdministrator = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
        $ignoreDPPublishFlag = $this->isAdministrator;

        $lang         = JFactory::getLanguage();
        $langTag      = $lang->getTag();
        $currentLang  = $langTag; //all language
        if( empty($langTag) || 'en-GB'==$langTag ) {
            $currentLang = '*';
        }
        
        if (!empty($id)) {
            $sql_add = Gpo_location_display_sql('Public Backend');

            $query = "
                     SELECT 
                            `lo`.`type` as location_type, 
                            `lo`.`display`,
                            `c`.`title`,
                            `c`.`alias`,
                            `c`.`introtext` AS `text`,
                            `c`.`catid`,
                            `c`.`metakey`,
                            `c`.`metadesc`,
                            `c`.`metadata`,
                            `c`.`modified`,
                            `c`.`attribs`AS `parameters`,
                            `c`.`created`,
                            `c`.`state`,
                            `c`.`access` 
                     FROM 
                            `#__content` AS `c` 
                     INNER JOIN 
                            `#__categories` as `cat` ON `c`.`catid` =`cat`.`id` 
                     INNER JOIN 
                            `#__gpo_location` AS `lo`  ON lower( `lo`.`name` )=lower(`cat`.`title`) 
                     WHERE 
                            `c`.`id` =" . $this->_db->quote($id) . " 
                     LIMIT 0,1
                     ";

            $this->_db->setQuery($query);
            $info = $this->_db->loadObject();
            $locationType = $info->location_type;

            //Is it publicly viewable? If not, set as NULL
            if( $info->display != 1 ) {
                $info = NULL;
            }
            
            if (strpos($info->alias, '-index') !== false) {
                $info->location = str_replace("-", "", str_replace("-index", "", $info->alias));
            }

            $access_denyed = true;
            $user = & JFactory::getUser();
            $aid = $user->get("aid", "0");


            if ($aid === 1) {
                $access_denyed = ( $info->access > 1 ) ? true : false;
            } else if ($aid === 2) {
                $access_denyed = false;
            } else {
                //$access_denyed =false;
                $access_denyed = ( $info->access > 0 ) ? true : false;
            }

            //if( (INT)$info->state !== (INT) "1" || $access_denyed )

            if ((INT) $info->state !== (INT) "1") {
                $dp = new DatapageHelper();
                //for members show the unpublished one too
                $dp_data = $dp->getDPByLocation($regionName, $ignoreDPPublishFlag);
                if ($dp_data) {
                    $DPHtml = $dp->getDPText($regionName, $dp_data);
                    $info->modified = (!$dp->isNUllDate($dp_data->updated_at) ) ? $dp_data->updated_at : $dp_data->created_at;
                }

                $html = <<<BLURB
<h1>This Page Is Not Yet Displayed</h1>
<hr /> 
<p style="line-height: 1.5em;">
We have the information and the documents you'll need for this {$locationType}, but we can only link them to citations and add them to the web site as regional priorities and funding permit.
<br />
<br />
Thank you for your patience.
<br/>
The Editor
<br/>
GunPolicy.org
</p>				
BLURB;

                $info->text = ($DPHtml) ? $DPHtml : $html;
                $info->isPageNotFound = ($DPHtml) ? false : true;
            }

            return $info;
            
        } else {
            $query = "SELECT `title`,`alias`,`state`,`introtext` AS `text`,`catid`,`metakey`,`metadesc`,`metadata`,`modified`,`attribs`AS `parameters`,`created` 
                      FROM `#__content`
                      WHERE `alias` ='region-index' 
                      AND `language` = '$currentLang' 
                      LIMIT 0,1";
        
        }
        
        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    /*
     * use category id to lookup the location linked to that category
     */

    function getLocationInfo($catid) {
        $sql_add = Gpo_location_display_sql('Public Backend');

        $query = 'SELECT `lo`.`id`, `lo`.`type`,`lo`.`name`' .
                ' FROM `#__gpo_location` as `lo`' .
                ' INNER JOIN `#__categories` as `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .
                ' WHERE `cat`.`id`=' . $this->_db->quote($catid) .
                $sql_add .
                ' LIMIT 0,1;';


        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    function locationsById( $id )
	{
		$sql_add = Gpo_location_display_sql( 'Public Backend' );
		$query = 'SELECT `cat`.`id`,`cat`.`title` as `title`' .
					' FROM `#__gpo_location_links` as `link`' .
					' INNER JOIN `#__gpo_location` as `lo` ON `lo`.`id` = `link`.`link_id`' .
					' INNER JOIN `#__categories` as `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .	
					' WHERE `link`.`location_id`=' . $this->_db->quote( $id )
					. $sql_add;
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	
	
	function getRegions()
	{
		$sql_add = Gpo_location_display_sql( 'Public Backend' );
		$query = 'SELECT `cat`.`id`,`cat`.`title` as `title`' .
						' FROM `#__gpo_location` as `lo`' .
						' INNER JOIN `#__categories` as `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .	
						" WHERE `lo`.`type`='region'" .
						$sql_add .
						" ORDER BY `lo`.`name`";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	
	
	
	/*
	 * id = article_id
	 * catid = category_id
	 */
	function getArticles( $id, $catid )
	{
		$jnow		= JFactory::getDate();
		//$now		= $jnow->toMySQL();
        $now		= $jnow->toSql();
		$nullDate	= $this->_db->getNullDate();
		
		$user	=  JFactory::getUser();
		$aid = $user->get("aid","0");
		
		if( $aid === 1 )
		{
			$access = ' AND ( a.access = 1 OR a.access = 0 ) ';
		}else if( $aid === 2 )
		{
			$access = null;
		}else{
			$access = ' AND a.access = 0 ';
		}

		$query = 'SELECT `a`.`id`,`a`.`alias`,`a`.`title`' .
					' FROM #__content AS a' .
					' LEFT JOIN #__categories AS cc ON cc.id = a.catid' .
					' WHERE cc.id=' . $this->_db->quote( $catid ) .
					' AND a.id !=' . $this->_db->quote( $id ) .
					' AND cc.published="1"' .
					' AND a.state = 1' .
					$access .
					' AND ( a.publish_up = '.$this->_db->Quote($nullDate).' OR a.publish_up <= '.$this->_db->Quote($now).' )' .
					' AND ( a.publish_down = '.$this->_db->Quote($nullDate).' OR a.publish_down >= '.$this->_db->Quote($now).' )' .
					' AND SUBSTRING( a.alias, -6) != "-index" ';
		//echo $query;

    $this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	
	function getBreadCrumbs( $location_id )
	{
		$session = JFactory::getSession();

		$crumbs = array();
		if(  !empty( $location_id ) )
		{
			$current = new StdClass();
			
			$query =	'SELECT `cat`.`id`,`cat`.`title` as `name`, ' .
                        '`lo`.`name` as `loc_name_en`, ' .  
                        '`lo`.`name_es` as `name_es`, ' .  
                        '`lo`.`name_fr` as `name_fr` ' . 
						'FROM `#__categories` as `cat` ' . 
						'INNER JOIN `#__gpo_location` as `lo` ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .
						'WHERE `lo`.`id`=' . $this->_db->Quote($location_id);
	
			$this->_db->setQuery( $query );
			$current = $this->_db->loadObject();			
			$pre_location_id = $session->get( 'pre_location_id', 0 );
			$crumbs[] = $current;
			while( !empty( $location_id ) )
			{
				//location_id for the location
				//cat due to how the route.php works.				
				$query = 'SELECT `link`.`location_id`, `cat`.`id`,`cat`.`title` as `name`, ' .
                         '`lo`.`name` as `loc_name_en`, ' .  
                         '`lo`.`name_es` as `name_es`, ' .  
                         '`lo`.`name_fr` as `name_fr` ' . 
						 'FROM `#__gpo_location_links` as `link` ' .
						 'INNER JOIN `#__gpo_location` as `lo` ON `lo`.`id` = `link`.`location_id` ' .
						 'INNER JOIN `#__categories` as `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .
						 'WHERE `link`.`link_id`=' . $this->_db->Quote($location_id) . (count($crumbs) < 2 && $pre_location_id > 0 ? ' AND `cat`.`id` = ' . $this->_db->Quote($pre_location_id) : '') ;
	
				$this->_db->setQuery( $query );
				$item = $this->_db->loadObject();
				if( isset( $item->location_id ) )
				{
					$location_id = $item->location_id;
					$crumbs[]=$item;
				}else{
					$location_id = '';
				}
			}
			$session->set( 'pre_location_id', (count($crumbs)==2 ? $current->id : 0) );			
		}
		
		//add root
		$o = new StdClass();
		$o->id='';
		$o->name='Regions';
        $o->name_es='Regiones';
        $o->name_fr='Régions';
		$crumbs[]=$o;
		//this puts the highest 
		krsort( $crumbs );

		return $crumbs;
	}
	
	
		
	function locationExists( $name )
	{
		if( empty( $name ) )
		{
			return false;
		}
		$query = 	'SELECT `lo`.`id`, `lo`.`type`,`lo`.`name`,`cat`.`id` as catid' .
					' FROM `#__gpo_location` as `lo`' .
					' INNER JOIN `#__categories` as `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .
					' WHERE lower(`cat`.`title`)=lower(' . $this->_db->quote( $name ) . ') LIMIT 0,1;';	
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}

	function isNeedToAddThe($location)
	{
		$theArray=array('Americas','Caribbean','European Union','United Nations','Philippines','United Arab Emirates','United States','United Kingdom');
		if(in_array($location,$theArray)) $location = 'the '.$location;
		return $location;
	}
        
    function getRegionList($type = 'region',$column=NULL)
	{
        if( empty($type) ) {
            return false;
        }
        
        $query = ' SELECT `lo`.`id`, `lo`.`id` as location_id, `lo`.`name`, `lo`.`name` as `location`, `lo`.`type`, `lo`.`iso2`, `lo`.`iso3`' .
				 ' FROM `#__gpo_location` as `lo`' .
				 ' WHERE `lo`.`type`=' . $this->_db->quote($type) . 
			     ' AND `lo`.`display` = 1' . 
                 ' ORDER BY `lo`.`name`'
                 ;
		$this->_db->setQuery( $query );
		$allRegions = $this->_db->loadObjectList();
        $regionsHavingData = array();
        
        if(empty($column)) {
            return $allRegions;
        }
        
        foreach($allRegions as $key=>$val) {
            if($this->hasRegionDataInColumn($val->id, $column)) {
               $regionsHavingData[] = $val;
            }
        }
        
        return $regionsHavingData;
	}
    
    
    /*
     * Check if a Region has any data 
     * in the member countries
     * 
     */
    function hasRegionDataInColumn($regionId,$dpColumn=NULL)
	{
        if(empty($regionId) || empty($dpColumn) ) {
            return false;
        }
        
        $db = JFactory::getDBO();
        $regionLocations = $this->getAllLocationsByRegion($regionId);
        $regionLocationsInQuery = implode(',', $regionLocations);
  		if ($regionLocationsInQuery)   {       
	        $query = "SELECT 
	                      `#__gpo_datapages`.`location_id` as `loc_id`, 
	                      `#__gpo_datapages`.`location` as `loc_name`, 
	                      `#__gpo_datapages`.`$dpColumn`
	                  FROM 
	                      `#__gpo_datapages` 
	                  INNER JOIN 
	                      `#__gpo_location` ON `#__gpo_datapages`.location_id = `#__gpo_location`.id 
	                  WHERE 
	                      `#__gpo_location`.`id` IN( " . $regionLocationsInQuery . ") " . 
	                  "AND
	                      `#__gpo_datapages`.`$dpColumn` <> '' 
	                  ";

	        $db->setQuery($query);
	        $db->query();
	        
			if( $db->getNumRows() >0 ) {
	            return true;
	        }
     	}   
        return false;
	}
    
    
    function getAllLinkedLocationsById( $locationId ) 
    {
        
        if( empty($locationId) ) {
            return false;
        }
        
        $query = ' SELECT `lo`.`id` as location_id, `lo`.`name`, `lo`.`name` as `location`, `lo`.`type`' .
				 ' FROM `#__gpo_location_links` as `link`' .
				 ' INNER JOIN `#__gpo_location` as `lo` ON `lo`.`id` = `link`.`link_id`' .
				 ' WHERE `link`.`location_id`=' . $this->_db->quote($locationId) . 
			     ' AND `lo`.`display` = 1';
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
        
    }
    
    
    function getAllLocationsByRegion( $regionId )
	{
        if( empty($regionId) ) {
            return false;
        }
	
		$regionLocations = $this->getAllLinkedLocationsById($regionId);
        
        $regionLocationIDs = array();
        foreach ($regionLocations as $key => $val) {
            
            if ( 'country' == $val->type && !empty($val->location_id) ) {
                $regionLocationIDs[] = $val->location_id;
            }
            else {
                $subRegionLocations = $this->getAllLinkedLocationsById( $val->location_id );
                foreach ($subRegionLocations as $k => $v) {
                    if ( 'country' == $v->type && !empty($v->location_id) ) {
                        $regionLocationIDs[] = $v->location_id;
                    }
                }
            }
            
        }
        
        return $regionLocationIDs;
	}
    
    /*
     * Get Category details of a region
     * by the location Id
     * 
     */
    function getCatIdByLocationId($locationId) {
        if( empty($locationId) ) {
            return false;
        }
        
        $query = 'SELECT `cat`.`id`,`cat`.`title` as `title`,`lo`.`name_es` as `name_es`, `lo`.`name_fr` as `name_fr`'. 
			     ' FROM `#__gpo_location` as `lo`' .
				 ' INNER JOIN `#__categories` as `cat` ON lower( `lo`.`name` )=lower(`cat`.`title`) ' .					
				 ' WHERE `lo`.`id`=' . $this->_db->quote( $locationId );
	    $this->_db->setQuery( $query );
		$catDetails = $this->_db->loadObject();
        print_r($catDetails);
        return $catDetails;
    }
	
}
?>
