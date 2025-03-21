<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

class GpoModelLocations extends JModelList
{
	var $total;
	var $data;

	function __construct()
	{
		parent::__construct();
		$this->limit = (int)'10';
		$this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', '', 'int');
	}
	

	
	function getBlank()
	{
		$fields = $this->fields();
		$data = new StdClass();
		foreach( $fields as $field )
		{
			$data->$field='';	
		}
		return $data;
	}
	
	
	function getBlankLive()
	{
		$fields = $this->fieldsLive();
		$data = new StdClass();
		foreach( $fields as $field )
		{
			$data->$field='';	
		}
		return $data;
	}
	
	
	
	function fieldsLive()
	{
		$fields = "id,published,title,subtitle,source,publisher,volume,issue,page,city,category,byline,author,affiliation,keywords,content,websource,gpnheader,entered,modified,notes,sourcedoc,batch,public,members,post,class";
		return explode(",",$fields);
	}
	
	
	
	function fields()
	{
		$fields = "id,live_id,published,title,subtitle,source,publisher,volume,issue,page,city,category,byline,author,affiliation,keywords,content,websource,gpnheader,entered,modified,notes,sourcedoc,batch,public,members,post,class";
		return explode(",",$fields);
	}
	
	
	
	function blank()
	{
		$fields = "id,published,title,subtitle,source,publisher,volume,issue,page,city,category,byline,author,affiliation,keywords,content,websource,gpnheader,entered,modified,notes,sourcedoc,batch,public,members,post,class";
		return explode(",",$fields);
	}

	
	
	
	function getById( $id )
	{
		if( (int)$id === (int)'0' )
		{
			return false;
		}
		
		$query = "SELECT * FROM `#__gpo_article_unpublished` WHERE `id`= " . $this->_db->quote( $id ) . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		$data = $this->_db->loadObject();
		
		if( empty( $data->id ) )
		{
			return false;
		}
		return $data;
	}


//function getLocations( $id ) // I think this is old.	
	
/*
 * get all location data 
 */	
	function getAllLocationData()
	{
		$query = "SELECT `id`,`prefix`,`name`,`prefix_es`,`name_es`,`prefix_fr`,`name_fr`,`type`,`display` FROM `#__gpo_location`";
		$query .= " ORDER BY `name` ASC ";
		
		$this->_db->setQuery( $query );
		$data = $this->_db->loadAssocList();
		return $data;
	}
	
	
	function getRegions( $display='' )
	{
		if( empty( $display ) )
		{
			$query = "SELECT `name` FROM `#__gpo_location` WHERE `type`='region' ORDER BY `name` ASC";
		}else{
			$query = "SELECT `name` FROM `#__gpo_location` WHERE `type`='region' AND `display`=1 ORDER BY `name` ASC";
		}
		
		$this->_db->setQuery( $query );
		$data = $this->_db->loadColumn();
		return $data;
	}
	

	function getSubRegions( $display='' )
	{
		if( empty( $display ) )
		{
			$query = "SELECT `name` FROM `#__gpo_location` WHERE `type`='subregion' ORDER BY `name` ASC";
		}else{
			$query = "SELECT `name` FROM `#__gpo_location` WHERE `type`='subregion' AND `display`=1 ORDER BY `name` ASC";
		}
		$this->_db->setQuery( $query );
		$data = $this->_db->loadColumn();
		return $data;
	}
	
	
	function getCountries( $display='' )
	{
		if( empty( $display ) )
		{
			$query = "SELECT `name` FROM `#__gpo_location` WHERE `type`='country' ORDER BY `name` ASC";
		}else{
			$query = "SELECT `name` FROM `#__gpo_location` WHERE `type`='country' AND `display`=1 ORDER BY `name` ASC";
		}
		$this->_db->setQuery( $query );
		$data = $this->_db->loadColumn();
		return $data;
	}
	
	
	
	function getAllLocationLinks()
	{
		$query = "SELECT `location_id`,`link_id` FROM `#__gpo_location_links` ORDER BY `location_id`";
				
		$this->_db->setQuery( $query );
		$data = $this->_db->loadAssocList();
		return $data;
	}
	
	/*
	 * As an array
	 */
/*
	function getRegionToSubRegionLinks( $region_ids, $subregion_ids, $display='' )
	{
		if( empty( $display ) )
		{
			$query = "SELECT `location_id`, `link_id` FROM `#__gpo_location_links` WHERE `location_id` IN( " . implode( ',', $region_ids ) . " ) OR `link_id` IN( " . implode( ',', $subregion_ids ) . " );";
		}else{
			$query = "SELECT `location_id`, `link_id` FROM `#__gpo_location_links` WHERE `location_id` IN( " . implode( ',', $region_ids ) . " ) OR `link_id` IN( " . implode( ',', $subregion_ids ) . " );";
		}
		
		$this->_db->setQuery( $query );
		$links = $this->_db->loadAssocList();
	}
*/
	function getRegionToSubRegionLinks( $display='' )
	{
		if( empty( $display ) )
		{
			$query = "
SELECT `ll`.`location_id`, `ll`.`link_id`
FROM `#__gpo_location_links` as `ll`
INNER JOIN `#__gpo_location` as `lo` ON `lo`.`id` = `ll`.`location_id`
WHERE `lo`.`type`='region';
";
			$this->_db->setQuery( $query );
			$links_by_region = $this->_db->loadAssocList();
			
			$query = "
SELECT `ll`.`location_id`, `ll`.`link_id` 
FROM `#__gpo_location_links` as `ll`
INNER JOIN `#__gpo_location` as `lo` ON `lo`.`id` = `ll`.`link_id`
WHERE `lo`.`type`='subregion';
";
			$this->_db->setQuery( $query );
			$links_by_subregion = $this->_db->loadAssocList();
		}else{
			$query = "
SELECT `ll`.`location_id`, `ll`.`link_id`
FROM `#__gpo_location_links` as `ll`
INNER JOIN `#__gpo_location` as `lo` ON `lo`.`id` = `ll`.`location_id`
WHERE `lo`.`type`='region'
AND `lo`.`display`=1;";
			$this->_db->setQuery( $query );
			$links_by_region = $this->_db->loadAssocList();
			
			$query = "
SELECT `ll`.`location_id`, `ll`.`link_id` 
FROM `#__gpo_location_links` as `ll`
INNER JOIN `#__gpo_location` as `lo` ON `lo`.`id` = `ll`.`link_id`
WHERE `lo`.`type`='subregion'
AND `lo`.`display`=1;";
			$this->_db->setQuery( $query );
			$links_by_subregion = $this->_db->loadAssocList();
		}
		
		$location_links = array();
		
		foreach( $links_by_region as $link )
		{
		  if(
		      !isset( $location_links[ $link[ 'location_id' ] ] ) 
		      )
		    {
		      $location_links[ $link[ 'location_id' ] ] = array();
		    }
		  $key = md5( $link[ 'link_id' ] );
		  if( !isset( $location_links[ $link[ 'location_id' ] ][ $key ] ) )
		  {
		    $location_links[ $link[ 'location_id' ] ][ $key ] = $link[ 'link_id' ];
		  }
		}
		
		
		foreach( $links_by_subregion as $link )
		{
		  if(
		      !isset( $location_links[ $link[ 'location_id' ] ] )
		      )
		    {
		      $location_links[ $link[ 'location_id' ] ] = array();
		    }
		  $key = md5( $link[ 'link_id' ] );
		  if( !isset( $location_links[ $link[ 'location_id' ] ][ $key ] ) )
		  {
		    $location_links[ $link[ 'location_id' ] ][ $key ] = $link[ 'link_id' ];
		  }
		}

		return $location_links;
	}
	
	
	
	function save_links( $input )
	{
		//get regions and subregions
		$db 		= & JFactory::getDBO();
		$query = "SELECT `id` FROM `#__gpo_location` WHERE `type`='region' ORDER BY `name` ASC;";
		$db->setQuery( $query );
		$regions = $db->loadColumn();
		
		$db 		= & JFactory::getDBO();
		$query = "SELECT `id` FROM `#__gpo_location` WHERE `type`='subregion' ORDER BY `name` ASC;";
		$db->setQuery( $query );
		$subregions = $db->loadColumn();
		
		//this is the 1 link down system
		foreach( $input as $key=>$links )
		{
			$delete_keys[]=$key;
			foreach( $links as $link )
			{
				$sql_parts[] ="(''," . $this->_db->quote( $key ) . ", " . $this->_db->quote( $link ) . ")";	
			}			 
		}
		
		$sql = 'DELETE FROM `#__gpo_location_links` WHERE `location_id` IN (' . implode( ",", $delete_keys ) . ");";
		$this->_db->setQuery( $sql );
		$this->_db->execute();
		
		$sql ='';
		$sql .= "INSERT IGNORE INTO `#__gpo_location_links` (`id`, `location_id`, `link_id` ) VALUES";
		$sql .= implode( ",", $sql_parts ) . ";";
		$this->_db->setQuery( $sql );
		$this->_db->execute();
	
		
		//this is the full links - use this for the search to get ids
		$r=array();
		$sub=array();
		foreach( $input as $key=>$data )
		{
			if( in_array( $key, $subregions ) )
			{
				$new_links[$key]=$data;	
			}
		}
		
		foreach( $input as $key=>$data )
		{
			if( in_array( $key, $regions ) )
			{
				foreach( $data as $id )
				{
					if( isset( $new_links[$id] ) )
					{
						$data = array_merge( $data, $new_links[$id] );
					}
				}
				$new_links[$key]=$data;	
			}
		}	
		foreach( $new_links as $key=>$links )
		{
			$delete_keys[]=$key;
			foreach( $links as $link )
			{
				$sql_parts[] ="(''," . $this->_db->quote( $key ) . ", " . $this->_db->quote( $link ) . ")";	
			}			 
		}
		$sql = 'DELETE FROM `#__gpo_location_links_deep` WHERE `location_id` IN (' . implode( ",", $delete_keys ) . ");";
		$this->_db->setQuery( $sql );
		$this->_db->execute();
		
		$sql ='';
		$sql .= "INSERT IGNORE INTO `#__gpo_location_links_deep` (`id`, `location_id`, `link_id` ) VALUES";
		$sql .= implode( ",", $sql_parts ) . ";";
		$this->_db->setQuery( $sql );
		$this->_db->execute();
		
		echo '<h1 style="color:#00ff00;">Location Links system has been updated.</h1>';
	}
	
	
	
	function save( $input )
	{
		
//Make sure there is no duplicate location names - this shouldnt happen as it should be done by javascript
		$uniq=array();
		$remove = array();
		$uniq_name=array();
		foreach( $input['current'] as $key=>$location )
		{
//force to lowercase			
			$location['name'] = trim( $location['name']);
			if( !empty( $location['name']))
			{
				if( !empty( $location['name']) && !in_array( $location['name'], $uniq_name ) )
				{
					$uniq[]=$location;
					$uniq_name[]=$location['name'];
				}else{
					$remove[$key]=$location['name'];
				}
			}else{
				//this is to make sure we dont break anything from later.
				echo 'At the moment, you are not able to delete via this system';
				return;
			}
		}

		if( count( $input['new']) >= 1 )
		{
//Add new locations to the current ones
			foreach( $input['new']['name'] as $key=>$name )
			{
				$location['name'] = strtolower( $name );
				$location['name'] = trim( $location['name']);
				if( !empty( $location['name']))
				{
					if( !empty( $location['name']) && !in_array( $location['name'], $uniq_name ) )
					{
						$location['type']=$input['new']['type'][$key];
						$location['display']=$input['new']['display'][$key];
						$location['id']='';
						$uniq[]=$location;
						$uniq_name[]=$location['name'];
					}else{
						$remove[]=$location['name'];
					}
				}
			}
			
		}

		//echo '<pre>' . print_r( $remove, true ) . '</pre>';
//fail if duplicate locations
		if( count($remove) >= 1 )
		{
			$js = "<script>";

			
			$message = '<p>Locations need to be unique:<br />';
			foreach( $remove as $key=>$name )
			{
				$message .= $name . "<br />";
			}
			$message .= "</p>";
			echo $message;
			exit();
			
		}
		

		$locations = $uniq;

		$sql = "TRUNCATE TABLE `#__gpo_location`;";
		$this->_db->setQuery( $sql );
		$this->_db->execute();
		$sql ='';
		$sql .= "INSERT IGNORE INTO `#__gpo_location` (`id`, `name`, `type`, `display`) VALUES";
		
		$sql_parts = array();
		foreach( $locations as $location )
		{
			if( !isset( $location['id'] ) || empty( $location['id'] ) )
			{
				$location['id']='';
			}
			if( !isset( $location['type'] ) || empty( $location['type'] ) )
			{
				$location['type']='';
			}	
			if( !isset( $location['display'] ) || $location['display'] !== '1' )
			{
				$location['display']='0';
			}
			//to try and fix a bug
			foreach( $location as $key=>$value )
			{
				$location[$key]=stripslashes( $value );
			}
			$sql_parts[] = "(" . $this->_db->quote( $location['id'] ) . ", " . $this->_db->quote( $location['name'] ) . ", " . $this->_db->quote( $location['type'] ) . ", " . $this->_db->quote( $location['display'] ) . ")";
		}
		
		$sql .= implode( ",", $sql_parts ) . ";";
		$this->_db->setQuery( $sql );
		$this->_db->execute();
		
		$link = JRoute::_( 'index.php?option=com_gpo' ) . '&controller=locations';
		
		$js = "<script>window.location='" . $link . "&mosmsg=Locations Updated!'</script>";
		echo $js;
		exit();
	}
	
	
	function getAllLocationNames()
	{
		$query = "SELECT `id`,`name` FROM `#__gpo_location`";
		$this->_db->setQuery( $query );
		return $this->_db->loadAssocList('id');
	}
    

        function getLocationIdByName($name=NULL)
	{
            if(empty($name)) {
               return false;
            }
            
            $sql = "SELECT id FROM `#__gpo_location` WHERE name = '$name'";
            $this->_db->setQuery($sql);
            $data = $this->_db->loadObject();
            
            return $data->id;
	}
        
        function insertLocation($data=array()) 
        {
            if(empty($data)) {
               return false;
            }
            
            $query = "insert into `#__gpo_location` (`id`, `name`, `type`, `display`) VALUES (NULL, " . $this->_db->quote($data['name']) . ", " . 
                     $this->_db->quote($data['type']) . ", " . $this->_db->quote($data['display']) . ");";
            $this->_db->setQuery($query);
            $r = $this->_db->execute();
            $stateOrProvinceID = $this->_db->insertid();
            return $stateOrProvinceID;
        }

        
        function insertLocationLinks($data=array()) 
        {
            if(empty($data)) {
               return false;
            }
            
            $query = "INSERT INTO `#__gpo_location_links` (`id`, `location_id`, `link_id`) VALUES (NULL, " . 
                     $this->_db->quote($data['location_id']) . ", " . 
                     $this->_db->quote($data['link_id']) . ")";
            $this->_db->setQuery($query);
            $r = $this->_db->execute();
            $insertId = $this->_db->insertid();
            return $insertId;
        }
    
    /*
     * 
     * Group related methods 
     * 
     */
    
    function getAllLocationsByGroupId($groupId)
    {
        if( empty($groupId) ) {
            return false;
        }
        
        $query = "SELECT 
                         `#__gpo_location`.`id`,
                         `#__gpo_location`.`name`,
                         `#__gpo_location_to_groups`.group_id,
                         `#__gpo_location_to_groups`.location_id,
                         `#__gpo_location_to_groups`.sort            
                  FROM 
                         `#__gpo_location` 
                  JOIN 
                         `#__gpo_location_to_groups` 
                  ON  
                         `#__gpo_location_to_groups`.`location_id` = `#__gpo_location`.`id` 
                  WHERE 
                         `#__gpo_location_to_groups`.group_id = " . $this->_db->quote($groupId) . 
                  "ORDER BY
                         `#__gpo_location_to_groups`.sort ASC
                  "
                  ;
		$this->_db->setQuery( $query );
		return $this->_db->loadAssocList('id');
    }
    
    
    /*
     * Group related methods 
     * 
     */
    
    function getAllGroupNames()
	{
		$query = "SELECT `id`,`name` FROM `#__gpo_groups`;";
		$this->_db->setQuery( $query );
		return $this->_db->loadAssocList('id');
	}
    
    
    /*
     * Edit Group  
     * 
     */
    function getGroupById($groupId)
    {
		$query = "SELECT `id`,`name` FROM `#__gpo_groups` WHERE `id` = ".$this->_db->quote($groupId)."";
		$this->_db->setQuery( $query );
		return $this->_db->loadAssoc('id');
    }
    
    
    function deleteGroupLocations( $groupId) {
        if( empty($groupId) ) {
            return false;
        }
        
        $query = "DELETE FROM `#__gpo_location_to_groups` WHERE `group_id` = ".$this->_db->quote($groupId);
       
        $this->_db->setQuery($query);
		$result = $this->_db->execute();
		//echo $this->_db->getQuery();
        return $result;
    }
    
    function deleteGroup( $groupId ) {
        if( empty($groupId) ) {
            return false;
        }
        
        $query = "DELETE FROM `#__gpo_groups` WHERE `id` = ".$this->_db->quote($groupId);
       
        $this->_db->setQuery($query);
		$result = $this->_db->execute();
        
        if( $result ) {
           $this->deleteGroupLocations($groupId);
        }
        
        return $result;
    }
    
    function updateGroupName( $groupId, $groupName ) {
        if( empty($groupId) || empty($groupName) ) {
            return false;
        }
        
        $query = "UPDATE 
                         `#__gpo_groups` 
                  SET 
                         `name` = " . $this->_db->quote($groupName) . 
                  "WHERE 
                         `id` = " . $this->_db->quote($groupId);
       
        $this->_db->setQuery($query);
		$result = $this->_db->execute();
        return $result;
    }
    
    
    function updateGroupLocations($groupId, $selectedLocations) 
    {
        if( empty($groupId) ) {
            return false;
        }
        
        $locIds = array_unique($selectedLocations);
        $this->deleteGroupLocations($groupId);
        
        foreach($locIds as $key=>$val) {
            $query = "INSERT INTO `#__gpo_location_to_groups` VALUES(NULL, ".$this->_db->quote($groupId) . ", " 
                     . $this->_db->quote($val) . ", " . $this->_db->quote($key+1) . ")";
             $this->_db->setQuery($query);
		     $this->_db->execute();
        }
        
        return true;
    }
    
    function translateLocationName($locationId, $locationName='', $lang=NULL, $updateField='name') {
        
        if( empty($locationId) || empty($lang) ) {
            return false;
        }
        
        if( $updateField != 'name' && $updateField != 'prefix' ) {
            return false; //invalid column name to update
        }
        
        if($lang == 'en') {
            $name = $updateField;
        }else if( $lang == 'es' || $lang == 'fr' ) {
            $name = $updateField . '_' . $lang;
        }
        
        $updateQry = "UPDATE 
                           `#__gpo_location` as `lo`
                     SET 
                           `lo`.`{$name}` = " . $this->_db->quote( $locationName ) . 
                     " WHERE 
                           `lo`.`id` = " . $this->_db->quote( $locationId );
                           
        //cho $updateQry;
        
        $this->_db->setQuery($updateQry);
		$result = $this->_db->execute();

        return $result;
    }
    
    
    public function getAllLocationArray() {
            
            $db = JFactory::getDbo();
                
            $sql = "SELECT id, name, iso3 FROM #__gpo_location WHERE display ='1'";
            $db->setQuery($sql);  
            $region = $db->loadObjectList();
            $data = array();
            $regionsArray = array();
            $isoArray     = array();
            foreach($region as $k=>$v){
                $regionsArray[$v->id] = $v->name;
                $isoArray[$v->id] = $v->iso3;
            }
            
            $data[0] = $regionsArray;
            $data[1] = $isoArray;
            
            return $data;
    }
        
        
    /* 
     * Needed for UCDP country profile data 
     * Not needed for any GPO functions 
     * 
     */
    function gleditschWardCountryCodesISO3() {
        
        $gleditsch_ward_country_codes = array(
        'USA' => 2,
        'CAN' => 20,
        'BHM' => 31,
        'CUB' => 40,
        'HAI' => 41,
        'HAI' => 41,
        'DOM' => 42,
        'JAM' => 51,
        'TRI' => 52,
        'BAR' => 53,
        'MEX' => 70,
        'BLZ' => 80,
        'UPC' => 89,
        'GUA' => 90,
        'HON' => 91,
        'SAL' => 92,
        'NIC' => 93,
        'COS' => 94,
        'PAN' => 95,
        'GCL' => 99,
        'COL' => 100,
        'VEN' => 101,
        'GUY' => 110,
        'SUR' => 115,
        'ECU' => 130,
        'PER' => 135,
        'BRA' => 140,
        'BOL' => 145,
        'PAR' => 150,
        'CHL' => 155,
        'ARG' => 160,
        'URU' => 165,
        'UKG' => 200,
        'IRE' => 205,
        'NTH' => 210,
        'BEL' => 211,
        'LUX' => 212,
        'FRN' => 220,
        'SWZ' => 225,
        'SPN' => 230,
        'POR' => 235,
        'HAN' => 240,
        'BAV' => 245,
        'GMY' => 255,
        'GFR' => 260,
        'GDR' => 265,
        'BAD' => 267,
        'SAX' => 269,
        'WRT' => 271,
        'HSE' => 273,
        'HSD' => 275,
        'MEC' => 280,
        'POL' => 290,
        'AUH' => 300,
        'AUS' => 305,
        'HUN' => 310,
        'CZE' => 315,
        'CZR' => 316,
        'SLO' => 317,
        'ITA' => 325,
        'PAP' => 327,
        'SIC' => 329,
        'MOD' => 332,
        'PMA' => 335,
        'TUS' => 337,
        'MLT' => 338,
        'ALB' => 339,
        'MNG' => 341,
        'MNG' => 341,
        'MAC' => 343,
        'CRO' => 344,
        'SER' => 340,
        'SER' => 340,
        'YUG' => 345,
        'BOS' => 346,
        'KOS' => 347,
        'SLV' => 349,
        'GRC' => 350,
        'CYP' => 352,
        'BUL' => 355,
        'MLD' => 359,
        'RUM' => 360,
        'RUS' => 365,
        'EST' => 366,
        'EST' => 366,
        'LAT' => 367,
        'LAT' => 367,
        'LIT' => 368,
        'LIT' => 368,
        'UKR' => 369,
        'BLR' => 370,
        'ARM' => 371,
        'GRG' => 372,
        'AZE' => 373,
        'FIN' => 375,
        'SWD' => 380,
        'NOR' => 385,
        'DEN' => 390,
        'ICE' => 395,
        'CAP' => 402,
        'GNB' => 404,
        'EQG' => 411,
        'GAM' => 420,
        'MLI' => 432,
        'SEN' => 433,
        'BEN' => 434,
        'MAA' => 435,
        'NIR' => 436,
        'CDI' => 437,
        'GUI' => 438,
        'BFO' => 439,
        'LBR' => 450,
        'SIE' => 451,
        'GHA' => 452,
        'TOG' => 461,
        'CAO' => 471,
        'NIG' => 475,
        'GAB' => 481,
        'CEN' => 482,
        'CHA' => 483,
        'CON' => 484,
        'DRC' => 490,
        'UGA' => 500,
        'KEN' => 501,
        'TAZ' => 510,
        'ZAN' => 511,
        'BUI' => 516,
        'RWA' => 517,
        'SOM' => 520,
        'DJI' => 522,
        'ETH' => 530,
        'ERI' => 531,
        'ANG' => 540,
        'MZM' => 541,
        'ZAM' => 551,
        'ZIM' => 552,
        'MAW' => 553,
        'SAF' => 560,
        'TRA' => 563,
        'OFS' => 564,
        'NAM' => 565,
        'LES' => 570,
        'BOT' => 571,
        'SWA' => 572,
        'MAG' => 580,
        'MAG' => 580,
        'COM' => 581,
        'MAS' => 590,
        'MOR' => 600,
        'MOR' => 600,
        'ALG' => 615,
        'ALG' => 615,
        'TUN' => 616,
        'TUN' => 616,
        'LIB' => 620,
        'LIB' => 620,
        'SUD' => 625,
        'SSD' => 626,
        'IRN' => 630,
        'TUR' => 640,
        'IRQ' => 645,
        'EGY' => 651,
        'EGY' => 651,
        'SYR' => 652,
        'LEB' => 660,
        'JOR' => 663,
        'ISR' => 666,
        'SAU' => 670,
        'YEM' => 678,
        'YPR' => 680,
        'KUW' => 690,
        'BAH' => 692,
        'QAT' => 694,
        'UAE' => 696,
        'OMA' => 698,
        'AFG' => 700,
        'AFG' => 700,
        'TKM' => 701,
        'TAJ' => 702,
        'KYR' => 703,
        'UZB' => 704,
        'KZK' => 705,
        'CHN' => 710,
        'TBT' => 711,
        'MON' => 712,
        'TAW' => 713,
        'KOR' => 730,
        'PRK' => 731,
        'ROK' => 732,
        'JPN' => 740,
        'IND' => 750,
        'BHU' => 760,
        'PAK' => 770,
        'BNG' => 771,
        'MYA' => 775,
        'MYA' => 775,
        'SRI' => 780,
        'MAD' => 781,
        'NEP' => 790,
        'THI' => 800,
        'CAM' => 811,
        'LAO' => 812,
        'VNM' => 815,
        'DRV' => 816,
        'RVN' => 817,
        'MAL' => 820,
        'SIN' => 830,
        'BRU' => 835,
        'PHI' => 840,
        'INS' => 850,
        'ETM' => 860,
        'AUL' => 900,
        'PNG' => 910,
        'NEW' => 920,
        'SOL' => 940,
        'FJI' => 950);
        
        return $gleditsch_ward_country_codes;
    }
    
    function getGleditschWardCountryCodes() {
        
        $country_codes = array(

        2 => 'United States of America',
        20 => 'Canada',
        31 => 'Bahamas',
        40 => 'Cuba',
        41 => 'Haiti',
        41 => 'Haiti',
        42 => 'Dominican Republic',
        51 => 'Jamaica',
        52 => 'Trinidad and Tobago',
        53 => 'Barbados',
        70 => 'Mexico',
        80 => 'Belize',
        89 => 'United Provinces of Central America',
        90 => 'Guatemala',
        91 => 'Honduras',
        92 => 'El Salvador',
        93 => 'Nicaragua',
        94 => 'Costa Rica',
        95 => 'Panama',
        99 => 'Great Colombia',
        100 => 'Colombia',
        101 => 'Venezuela',
        110 => 'Guyana',
        115 => 'Surinam',
        130 => 'Ecuador',
        135 => 'Peru',
        140 => 'Brazil',
        145 => 'Bolivia',
        150 => 'Paraguay',
        155 => 'Chile',
        160 => 'Argentina',
        165 => 'Uruguay',
        200 => 'United Kingdom',
        205 => 'Ireland',
        210 => 'Netherlands',
        211 => 'Belgium',
        212 => 'Luxembourg',
        220 => 'France',
        225 => 'Switzerland',
        230 => 'Spain',
        235 => 'Portugal',
        240 => 'Hanover',
        245 => 'Bavaria',
        255 => 'Germany (Prussia)',
        260 => 'German Federal Republic',
        265 => 'German Democratic Republic',
        267 => 'Baden',
        269 => 'Saxony',
        271 => 'WŸrttemberg',
        273 => 'Hesse-Kassel (Electoral)',
        275 => 'Hesse-Darmstadt (Ducal)',
        280 => 'Mecklenburg-Schwerin',
        290 => 'Poland',
        300 => 'Austria-Hungary',
        305 => 'Austria',
        310 => 'Hungary',
        315 => 'Czechoslovakia',
        316 => 'Czech Republic',
        317 => 'Slovakia',
        325 => 'Italy/Sardinia',
        327 => 'Papal States',
        329 => 'Two Sicilies',
        332 => 'Modena',
        335 => 'Parma',
        337 => 'Tuscany',
        338 => 'Malta',
        339 => 'Albania',
        341 => 'Montenegro',
        341 => 'Montenegro',
        343 => 'Macedonia (Former Yugoslav Republic of)',
        344 => 'Croatia',
        340 => 'Serbia',
        340 => 'Serbia',
        345 => 'Yugoslavia',
        346 => 'Bosnia-Herzegovina',
        347 => 'Kosovo',
        349 => 'Slovenia',
        350 => 'Greece',
        352 => 'Cyprus',
        355 => 'Bulgaria',
        359 => 'Moldova',
        360 => 'Rumania',
        365 => 'Russia (Soviet Union)',
        366 => 'Estonia',
        366 => 'Estonia',
        367 => 'Latvia',
        367 => 'Latvia',
        368 => 'Lithuania',
        368 => 'Lithuania',
        369 => 'Ukraine',
        370 => 'Belarus (Byelorussia)',
        371 => 'Armenia',
        372 => 'Georgia',
        373 => 'Azerbaijan',
        375 => 'Finland',
        380 => 'Sweden',
        385 => 'Norway',
        390 => 'Denmark',
        395 => 'Iceland',
        402 => 'Cape Verde',
        404 => 'Guinea-Bissau',
        411 => 'Equatorial Guinea',
        420 => 'Gambia',
        432 => 'Mali',
        433 => 'Senegal',
        434 => 'Benin',
        435 => 'Mauritania',
        436 => 'Niger',
        437 => 'Cote DÕIvoire',
        438 => 'Guinea',
        439 => 'Burkina Faso (Upper Volta)',
        450 => 'Liberia',
        451 => 'Sierra Leone',
        452 => 'Ghana',
        461 => 'Togo',
        471 => 'Cameroon',
        475 => 'Nigeria',
        481 => 'Gabon',
        482 => 'Central African Republic',
        483 => 'Chad',
        484 => 'Congo',
        490 => 'Congo, Democratic Republic of (Zaire)',
        500 => 'Uganda',
        501 => 'Kenya',
        510 => 'Tanzania/Tanganyika',
        511 => 'Zanzibar',
        516 => 'Burundi',
        517 => 'Rwanda',
        520 => 'Somalia',
        522 => 'Djibouti',
        530 => 'Ethiopia',
        531 => 'Eritrea',
        540 => 'Angola',
        541 => 'Mozambique',
        551 => 'Zambia',
        552 => 'Zimbabwe (Rhodesia)',
        553 => 'Malawi',
        560 => 'South Africa',
        563 => 'Transvaal',
        564 => 'Orange Free State',
        565 => 'Namibia',
        570 => 'Lesotho',
        571 => 'Botswana',
        572 => 'Swaziland',
        580 => 'Madagascar (Malagasy)',
        580 => 'Madagascar',
        581 => 'Comoros',
        590 => 'Mauritius',
        600 => 'Morocco',
        600 => 'Morocco',
        615 => 'Algeria',
        615 => 'Algeria',
        616 => 'Tunisia',
        616 => 'Tunisia',
        620 => 'Libya',
        620 => 'Libya',
        625 => 'Sudan',
        626 => 'South Sudan',
        630 => 'Iran (Persia)',
        640 => 'Turkey (Ottoman Empire)',
        645 => 'Iraq',
        651 => 'Egypt',
        651 => 'Egypt',
        652 => 'Syria',
        660 => 'Lebanon',
        663 => 'Jordan',
        666 => 'Israel',
        670 => 'Saudi Arabia',
        678 => 'Yemen (Arab Republic of Yemen)',
        680 => "Yemen, People's Republic of",
690 =>	'Kuwait',
692 =>	'Bahrain',
694 =>	'Qatar',
696 =>	'United Arab Emirates',
698 =>	'Oman',
700 =>	'Afghanistan',
700 =>	'Afghanistan',
701 =>	'Turkmenistan',
702 =>	'Tajikistan',
703 =>	'Kyrgyz Republic',
704 =>	'Uzbekistan',
705 =>	'Kazakhstan',
710 =>	'China',
711 =>	'Tibet',
712 =>	'Mongolia',
713 =>	'Taiwan',
730 =>	'Korea',
731 =>	"Korea, People's Republic of",
        732 => 'Korea, Republic of',
        740 => 'Japan',
        750 => 'India',
        760 => 'Bhutan',
        770 => 'Pakistan',
        771 => 'Bangladesh',
        775 => 'Myanmar (Burma)',
        775 => 'Myanmar (Burma)',
        780 => 'Sri Lanka (Ceylon)',
        781 => 'Maldives',
        790 => 'Nepal',
        800 => 'Thailand',
        811 => 'Cambodia (Kampuchea)',
        812 => 'Laos',
        815 => 'Vietnam (Annam/Cochin China/Tonkin)',
        816 => 'Vietnam, Democratic Republic of',
        817 => 'Vietnam, Republic of',
        820 => 'Malaysia',
        830 => 'Singapore',
        835 => 'Brunei',
        840 => 'Philippines',
        850 => 'Indonesia',
        860 => 'East Timor',
        900 => 'Australia',
        910 => 'Papua New Guinea',
        920 => 'New Zealand',
        940 => 'Solomon Islands',
        950 => 'Fiji');
        
        return $country_codes;
    }

}
?>
