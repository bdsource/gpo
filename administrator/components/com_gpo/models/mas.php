<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

require_once( JPATH_COMPONENT . DS . 'helper/tmhOAuth.php' );

class GpoModelMas extends JModelList{

    var $total;
    var $data;

    function __construct() {
        parent::__construct();
        $this->limit = Joomla\CMS\Factory::getApplication()->getInput()->get('limit', '100', '', 'int');
        $this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', '', 'int');
        //$this->path = "/home/palpers/gp-uploads/mas/";
        $this->path = "/home/gpo/gp-uploads/mas/";
        $this->block_emails = false;
        
        $config     = JFactory::getConfig();
        $sitename   = $config->get('sitename');
        ### Trun off email feature for beta site ###
        if(stripos($sitename,'staging') !== false) {
           $this->block_emails = true;
        }
      
    }

    /*
     * creates the table name
     */

    function make_table_name($published=false, $quotes=true) {
        if ($quotes) {
            $quote = "`";
        } else {
            $quote = "";
        }
        return $quote . '#__gpo_mas' . ( ( $published === false ) ? '_unpublished' : '' ) . $quote;
    }

    /*
     * this is used for unpublished blank entry
     * live_id
     */
    function fields($published=false)
    {
        if ($published === false)
        {
            $fields = "id,live_id,published,modified,date_of_shooting,country_id,state_province,city,primary_venue,venue_type,shooting_type,latitude,longitude,victims_shot_dead,victims_killed_other_means,victims_killed_total,victims_wounded,perpetrators_killed_others,perpetrators_killed_suicide,perpetrators_captured_escaped,primary_perpetrator_name,perpetrators_gender,perpetrators_age,perpetrators_previous_illness,perpetrators_previous_violence,primary_firearm_type,primary_firearm_action,primary_firearm_make,primary_firearm_obtained_legally,secondary_firearm_type,secondary_firearm_action,secondary_firearm_make,secondary_firearm_obtained_legally,citizen_armed_intervention,narrative,staff,notes";
        }
        else
        {
            $fields = "id,published,modified,date_of_shooting,country_id,state_province,city,primary_venue,venue_type,shooting_type,latitude,longitude,victims_shot_dead,victims_killed_other_means,victims_killed_total,victims_wounded,perpetrators_killed_others,perpetrators_killed_suicide,perpetrators_captured_escaped,primary_perpetrator_name,perpetrators_gender,perpetrators_age,perpetrators_previous_illness,perpetrators_previous_violence,primary_firearm_type,primary_firearm_action,primary_firearm_make,primary_firearm_obtained_legally,secondary_firearm_type,secondary_firearm_action,secondary_firearm_make,secondary_firearm_obtained_legally,citizen_armed_intervention,narrative,staff,notes";
        }
        return (object) array_fill_keys(array_values(explode(",", $fields)), '');
    }
    /*
     * get the unpublished item
     */
    function getUnPublishedById($id){
        $published = false;
        if ((int) $id === (int) '0') {
            return false;
        }
        
        $tbl_name = $this->make_table_name($published);
        $query = "SELECT * FROM " . $tbl_name . " WHERE `id`= " . $this->_db->quote($id) . " LIMIT 0,1";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        if (empty($data->id)) {
            return false;
        }
        $data->locations = $this->getLocations($data->id, $published);
         //ftp_debug( $data, 'getUnPublishedById' );
        return $data;
    }
    /*
     * get the unpublished item
     */
    function getPublishedById($id) {
        $published = true;
        if ((int) $id === (int) '0') {
            return false;
        }
        
        $tbl_name = $this->make_table_name($published);
        $query = "SELECT * FROM " . $tbl_name . " WHERE `id`= " . $this->_db->quote($id) . " LIMIT 0,1";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        
        if (empty($data->id)) {
            return false;
        }
        $data->locations = $this->getLocations($data->id, $published);
//		ftp_debug( $data, 'getPublishedById' );
        return $data;
    }
    
    function getNextById($id, $published = true) {
        if ((int) $id === (int) '0') {
            return false;
        }
        
        $tbl_name = $this->make_table_name($published);
        $query = "SELECT `id` FROM " . $tbl_name . " WHERE `id` > " . $this->_db->quote($id) . " ORDER BY `id` ASC LIMIT 1";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        if (empty($data->id)) {
            return false;
        }
        return $data->id;
    }
    
    function getPrevById($id, $published = true ) {
        if ((int) $id === (int) '0') {
            return false;
        } 
        $tbl_name = $this->make_table_name($published);
        $query = "SELECT `id` FROM " . $tbl_name . " WHERE `id` < " . $this->_db->quote($id) . " ORDER BY `id` DESC LIMIT 1";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        if (empty($data->id)) {
            return false;
        }
        return $data->id;
    }

    function getLocations($id) {
        
        $query = "SELECT `lo`.`name` FROM `#__gpo_location` as `lo` WHERE `lo`.`id` = '$id';";
        $this->_db->setQuery($query);
        $locations = $this->_db->loadColumn();
        return $locations;
    }

    function total($published=false) {
        $tbl_name = $this->make_table_name($published);
        $query = "SELECT COUNT( `id` ) FROM " . $tbl_name . " LIMIT 0,1";
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }
    
    
   

    function unpublished($filter_order, $filter_order_dir) {
        $published = false;
        $this->total = $this->total($published);
        if ((int) $this->total === (int) '0') {
            return array();
        }
        $this->pagination = new JPagination($this->total, $this->limitstart, $this->limit);
	
			
        
		$tbl_name = $this->make_table_name($published);
//		$query = "SELECT `id`,`title`, `modified` FROM " . $tbl_name;
//This one uses the awaiting table
        $query = "SELECT `c` . * , `ap`.`id` AS `ap_id` FROM " . $tbl_name . " AS `c` LEFT JOIN `#__gpo_awaiting_published` AS `ap` ON `c`.`id` = `ap`.`ext_id` AND `ap`.`ext_table` = 'm'";
        $query .= " ORDER BY {$this->_db->quoteName($filter_order)} $filter_order_dir ";

        $this->_db->setQuery($query, $this->pagination->limitstart, $this->pagination->limit);
        $data = $this->_db->loadAssocList();
        return $data;
    }
    
    function published($filter_order, $filter_order_dir){        
        $published = true; 
        $result = null;
        // Obtaining search id and if matches over there redirect to the edit page	
        $search_id = Joomla\CMS\Factory::getApplication()->getInput()->get('search_id', '', '', 'string');
      
        
        $this->unPublishedTotal = $this->total(false);
        $this->total = $this->total($published);
        if ((int) $this->total === (int) '0') {
            return array();
        }
        
        if(!empty($search_id )) 
        {
                 $result =  $this->getPublishedById($search_id);
                 
                
                 if($result->id === $search_id){
                        var_dump($result);
			$url = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=edit&live_id='. $search_id,false );           
			$mainframe =& JFactory::getApplication();
			//global $mainframe;  
			$mainframe->redirect( $url ); 
                }
                
            else {   
                $this->pagination = new JPagination($this->total, $this->limitstart, $this->limit);
                return $result;         
              }
        }
        else{        
                   $this->pagination = new JPagination($this->total, $this->limitstart, $this->limit);
                   $tbl_name = $this->make_table_name($published);    
                   $query = "SELECT ".$tbl_name. ".id,".$tbl_name.".date_of_shooting,".$tbl_name.".primary_perpetrator_name,".$tbl_name.".primary_venue,  j25_gpo_location.name  FROM " .$tbl_name. "LEFT JOIN j25_gpo_location on" .$tbl_name.".country_id = j25_gpo_location.id";
                   $query .= " ORDER BY j25_gpo_mas.{$this->_db->quoteName($filter_order)} $filter_order_dir";
                   $this->_db->setQuery($query, $this->pagination->limitstart, $this->pagination->limit);
                   $data = $this->_db->loadAssocList();        
           return $data;
       }
    }
    

    function copyForEdit($id) {
//If already in edit, stop.
        $tbl_name = $this->make_table_name(false);
        $query = "SELECT `id`,`live_id` FROM " . $tbl_name . " WHERE `live_id`=" . $this->_db->quote($id) . " LIMIT 0,1";
        $this->_db->setQuery($query);
        $oLive = $this->_db->loadObject();
        if (!empty($oLive->id)){
            return $oLive;
        }

        $oLive = $this->getPublishedById($id);
//Delete if mail is in the public queue.		
//        $this->deleteMailToPublic($id);
        $tbl_name = $this->make_table_name(true);
        $query = "SELECT * FROM " . $tbl_name . " WHERE `id`=" . $this->_db->quote($id) . " LIMIT 0,1";
        $this->_db->setQuery($query);
        $oLive = $this->_db->loadObject();


        $blankObject = $this->fields(false);
        $data = (object) array_intersect_key((array) $oLive, (array) $blankObject);

        $data->live_id = $data->id;
        $data->id = '';
                    
        $tbl_name = $this->make_table_name(false, false);
        $ret = $this->_db->insertObject($tbl_name, $data, 'id');
        if ($ret){
            /*
            $query = "INSERT INTO `#__gpo_mas_locations_unpublished`( `id`,`location_id`,`ext_id` )";
            $query .= "SELECT '' as `id`, `location_id`, '" . $data->id . "' as `ext_id` FROM `#__gpo_mas_locations` WHERE `ext_id`=" . $this->_db->quote($data->live_id);

            $this->_db->setQuery($query);
            $this->_db->execute();
            */
            return $data;
        } else {
            $o = null;
            $o->id = '0';
            return $o;
        }
    }

    function publish($oItem){
        $live_id = (isset($oItem->live_id) ) ? $oItem->live_id : null;
        $unpublished_id = $oItem->id;
        
        $aPublish = Joomla\CMS\Factory::getApplication()->getInput()->get('publish', '0', 'POST', 'array');
        if (isset($aPublish['approve']) && (int) $aPublish['approve'] === (int) '1') {
            $aPublish['live_id'] = $live_id;
            $aPublish['unpublished_id'] = $unpublished_id;

            $r = $this->moveUnpublishToPublished($oItem, $aPublish);
            return $r;
        }
        $response = new stdClass();
        $response->msg = 'You have not ticked the approve box';
        $response->link = JRoute::_('index.php?option=com_gpo&controller=mas&task=publish&id=' . $unpublished_id, false);
        return $response;
    }

    function readyForPublishing($oItem) {
        $aPublish = Joomla\CMS\Factory::getApplication()->getInput()->get('publish', '0', 'POST', 'array');
        if (isset($aPublish['approve']) && (int) $aPublish['approve'] === (int) '1') {
            $id = $oItem->id;
            $query = "INSERT IGNORE INTO `#__gpo_awaiting_published` (`id`,`ext_id`,`ext_table`)VALUES(NULL ," . $this->_db->quote($id) . ",'m');";
            $this->_db->setQuery($query);
            $ret = $this->_db->execute();
            $response = new stdClass();
            $response->msg = 'Mas Item has been queued for publishing. A Super Administrator will approve it shortly.';
            $response->link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
            $response->pass = true;
            return $response;
        }
    }

    function moveUnpublishToPublished($data, $options) {
        $live_id = $options['live_id'];
        $unpublished_id = $options['unpublished_id'];
        //need to tidy up the object
        $blankPublishObject = $this->fields(true);
        $data = (object) array_intersect_key((array) $data, (array) $blankPublishObject);

        $tbl_name = $this->make_table_name(true, false);

        $unix_timestamp = ( isset($_SERVER['REQUEST_TIME']) ) ? $_SERVER['REQUEST_TIME'] : date('U');
        $unix_timestamp_date = date('Y-m-d H:i:s', $unix_timestamp);
        
        //Reset share options
        $options['gi'] = 0;
        if ($options['mail'] === 'global-interest') {
            $options['gi'] = 1;
            $options['mail'] = 'public-post';
        }
        
        if (strpos($options['mail'], 'public') !== false) {
            $data->share = '1';
        }
        if (strpos($options['mail'], 'members') !== false) {
            $data->share = '0';
        }

        if (!empty($live_id)) {
            $data->id = $live_id;
            unset($data->live_id);
            unset($data->entered);

//			if(  isset( $options['minor'] ) && (int)$options['minor'] === (int)'1' )
//			{
//				unset( $data->modified );
//			}else{
//				$data->modified = $unix_timestamp_date;
//			}

            $data->modified = $unix_timestamp_date;
//This wont work if the published data has been deleted. ( simple solution, delete unpublished when deleting published )
            $ret = $this->_db->updateObject($tbl_name, $data, 'id', true);
        } else if (empty($data->live_id)) {
//Insert new item into the live table
            $data->id = '';
            $data->entered = $unix_timestamp_date;
            $data->modified = $unix_timestamp_date;

            $ret = $this->_db->insertObject($tbl_name, $data, 'id', true);
        }
        
        $live_id = $data->id;

//Publish Locations			
        //$this->publishLocations($live_id, $unpublished_id);
//Delete unpublished mas + locations
        $this->deleteUnpublishedById($unpublished_id);

//Mail to members must happen after locations are published!
//This is where the switches will get done.
/*
        if ($options['mail'] === 'public-post-only') {
            $this->insertMailToPublic($live_id, $options['gi']);

            //tweet ...
            $tweet_msg = $this->tweet($data, $live_id);

        } else if ($options['mail'] === 'public-post') {
//add to public
            if ($data->share === '1') {
                $this->insertMailToPublic($live_id, $options['gi']);
            }
            $this->mailToMembers($live_id);

            //tweet ...
            $tweet_msg = $this->tweet($data, $live_id);

        } else if ($options['mail'] === 'members-post') {
//send to members	
            if ($data->share === '0') {
                $this->mailToMembers($live_id);
            }
        }else if ($options['mail'] === 'public-archive'){

            //tweet
            $tweet_msg = $this->tweet($data, $live_id);
        }
*/
        $front_end = str_replace("administrator", '', JURI::base(true));
        //$href = $front_end . JRoute::_('index.php?option=com_gpo&task=mas&id=' . $live_id, false)
        $href = $front_end . JRoute::_('firearms/mas/' . $live_id, false);

       // $msg = 'Published: view it <a target="_blank" href="' . $href . '" style="text-decoration:underline;">live</a> (front end).';
        $href = JRoute::_('index.php?option=com_gpo&controller=mas&task=reindex', false);
        $msg = 'To search for this record straight away, <a href="' . $href . '" style="text-decoration:underline;">update the index.</a> '. $tweet_msg .'.';

        $response = new stdClass();
        $response->msg = $msg;
        $response->link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
        $response->pass = true;

        return $response;
    }

    function publishLocations($live_id, $unpublished_id) {
//		ftp_debug( array( $live_id, $unpublished_id ), 'publishLocations',true,false );
//Delete locations from live
        $query = "DELETE FROM `#__gpo_mas_locations` WHERE `ext_id`=" . $this->_db->quote($live_id) . ";";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
//Insert new locations
        $query = "INSERT INTO `#__gpo_mas_locations`( `id`,`location_id`,`ext_id` )";
        $query .= "SELECT '' as `id`, `location_id`, '" . $live_id . "' as `ext_id` FROM `#__gpo_mas_locations_unpublished` WHERE `ext_id`=" . $this->_db->quote($unpublished_id) . " ORDER BY `id` ASC;";
        $this->_db->setQuery($query);
        $this->_db->execute();
    }

    /*
     * Delete the unpublished mas record including its references in the locations section
     */

    function deleteUnpublishedById($id) {
        $tbl_name = $this->make_table_name(false);
        $query = "DELETE FROM " . $tbl_name . " WHERE `id`=" . $this->_db->quote($id);
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            return false;
        }

        $this->deleteFromQueue($id);

        /*
        $query = "DELETE FROM `#__gpo_mas_locations_unpublished` WHERE `ext_id`=" . $this->_db->quote($id) . ";";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        * 
        */
        return $ret;
    }

    function deleteFromQueue($id) {
        $query = "DELETE FROM `#__gpo_awaiting_published` WHERE `ext_table`='m' AND `ext_id`=" . $this->_db->quote($id);
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
    }

    /*
     * Delete the published mas record including its references in the locations section
     */

    function deletePublishedById($id) {
        $query = "DELETE `nlu`.* FROM `#__gpo_mas_locations_unpublished` as `nlu` INNER JOIN `#__gpo_mas_unpublished` as `nu` ON `nu`.`id`=`nlu`.`ext_id` WHERE `nu`.`live_id`='" . (int) $id . "';";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            die('Fatal error: problem deleting Published item:nlu');
        }

//Delete any unpublished items from the awaiting publish list		
        $query = "DELETE `ap`.* FROM `#__gpo_awaiting_published` as `ap` INNER JOIN `#__gpo_mas_unpublished` as `nu` ON `nu`.`id`=`ap`.`ext_id` WHERE `nu`.`live_id`='" . (int) $id . "' AND `ap`.`ext_table`='m';";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            die('Fatal error: problem deleting Published item:ap');
        }

        $query = "DELETE `nu`.* FROM `#__gpo_mas_unpublished` as `nu` WHERE `nu`.`live_id`='" . (int) $id . "';";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            die('Fatal error: problem deleting Published item:nu');
        }
        $query = "DELETE `nl`.* FROM `#__gpo_mas_locations` as `nl` INNER JOIN `#__gpo_mas` as `n` ON `n`.`id`=`nl`.`ext_id` WHERE `n`.`id`='" . (int) $id . "';";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            die('Fatal error: problem deleting Published item:nl');
        }

//Clear MailToPublic of mas id
        $this->deleteMailToPublic($id);

        $query = "DELETE `n`.* FROM `#__gpo_mas` as `n` WHERE `n`.`id`='" . (int) $id . "';";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            die('Fatal error: problem deleting Published item:n');
        }

        return true;

        if ($ret !== true) {
            die('Fatal error: problem deleting Published item');
        }
    }

    function canPublish($oItem) {
        $response = new stdClass();
        $response->pass = false;
        $response->msg = 'This is not ready for publishing';
        $response->link = JRoute::_('index.php?option=com_gpo&controller=mas&task=edit&id=' . $oItem->id, false);

        $live_id = (isset($oItem->live_id) ) ? $oItem->live_id : null;
        $unpublished_id = $oItem->id;

        $clean = array();
        $fields = $this->fields(true);
        $fields->locations = "";
        foreach ($fields as $key) {
            $clean[$key] = ( isset($oItem->$key) ) ? $oItem->$key : "";
        }
        foreach ($clean as $key => $value) {
            $r = $this->rule($key, $value);
            if ($r === false) {
                return $response;
            }
        }
        $response->pass = true;
        return $response;
    }

    function getRule($field, $message = false)
    {
        $rules = array
        (
            "id" => array("required" => true),
            "date_of_shooting" => array("required" => true, 'currentdate' => true),
            "country_id" => array("required" => true),
            "city_id" => array("required" => true),
            "state_province" => array("required" => false),
            "primary_venue" => array("required" => true),
            "venue_type" => array("required" => true),
            "shooting_type" => array("required" => true),
            "latitude" => array("matchCoord" => true), //I need matchCoord function to fix this
            "longitude" => array("matchCoord" => true), //I need matchCoord function to fix this
            "victims_shot_dead" => array("requiredNumeric" => true),
            "victims_killed_other_means" => array("requiredNumeric" => true),
            "victims_killed_total" => array("requiredNumeric" => true),
            "victims_wounded" => array("requiredNumeric" => true),

            "perpetrators_killed_others" => array("requiredNumeric" => true),
            "perpetrators_killed_suicide" => array("requiredNumeric" => true),
            "perpetrators_captured_escaped" => array("requiredNumeric" => true),
            "primary_perpetrator_name" => array("requiredNemeric" => true),
            "perpetrators_gender" => array("required" => true),
            "perpetrators_age" => array("required" => true),
            "perpetrators_previous_illness" => array("required" => true),
            "perpetrators_previous_violence" => array("required" => true),
            
            "primary_firearm_type" => array("required" => true),
            "primary_firearm_action" => array("required" => true),
            "primary_firearm_make" => array("required" => true),
            "primary_firearm_obtained_legally" => array("required" => true),
            "secondary_firearm_type" => array("required" => true),
            "secondary_firearm_action" => array("required" => true),
            "secondary_firearm_make" => array("required" => true),
            "secondary_firearm_obtained_legally" => array("required" => true),
            "citizen_armed_intervention" => array("required" => true),
            "narrative" => array("required" => true)
        );
        
        $messages = array(
            "id" => array(
                "id" => "mas_id",
                "message" => "'Id' should not be empty"
            ),
            "date_of_shooting" => array(
                "id" => "mas_date_of_shooting",
                "message" => "'Date of Shooting' should not be empty"
            ),
            "country_id" => array(
                "id" => "select_mas_location",
                "message" => "'Country' should not be empty - Compulsory text entry"
            ),           
            "city_id" => array(
                "id" => "select_mas_city",
                "message" => "'City' should not be empty"
            ),
            "state_province" => array(
                "id" => "mas_select_province",
                "message" => "'State or Province' should not be empty"
            ),
            "primary_venue" => array(
                "id" => "mas_primary_venue",
                "message" => "'Primary Venue' should not be empty"
            ),
            "venue_type" => array(
                "id" => "mas_venue_type",
                "message" => "'Venue' type should not be empty"
            ),
            "shooting_type" => array(
                "id" => "mas_shooting_type",
                "message" => "'Shooting Type' should not be empty"
            ),
            "latitude" => array(
                "id" => "mas_latitude",
                "message" => "'Latitude' should not be empty"
            ),
            "longitude" => array(
                "id" => "mas_longitude",
                "message" => "'Longitude' should not be empty"
            ),
            "victims_shot_dead" => array(
                "id" => "mas_victims_shot_dead",
                "message" => "'Victims shot dead' should not be empty"
            ),
            "victims_killed_other_means" => array(
                "id" => "mas_victims_killed_other_means",
                "message" => "'Victims killed by other means' should not be empty"
            ),
            "victims_killed_total" => array(
                "id" => "mas_victims_killed_total",
                "message" => "'Victims killed total' should not be empty"
            ),
            "victims_wounded" => array(
                "id" => "mas_victims_wounded",
                "message" => "'Victims wounded' should not be empty"
            ),
            
            "perpetrators_killed_others" => array(
                "id" => "mas_perpetrators_killed_others",
                "message" => "'Perpetrators killed others' should not be empty"
            ),
            "perpetrators_killed_suicide" => array(
                "id" => "mas_perpetrators_killed_suicide",
                "message" => "'Perpetrators killed suicide' should not be empty"
            ),
            "perpetrators_captured_escaped" => array(
                "id" => "mas_perpetrators_captured_escaped",
                "message" => "'Perpetrators captured escaped' should not be empty"
            ),
            "primary_perpetrator_name" => array(
                "id" => "mas_primary_perpetrator_name",
                "message" => "'Primary perpetrator name' should not be empty"
            ),
            "perpetrators_gender" => array(
                "id" => "select_mas_perpetrators_gender",
                "message" => "'Perpetrators gender' should not be empty"
            ),
            "perpetrators_age" => array(
                "id" => "mas_perpetrators_age",
                "message" => "'Perpetrators age' should not be empty"
            ),
            "perpetrators_previous_illness" => array(
                "id" => "select_mas_perpetrators_previous_illness",
                "message" => "'Perpetrators previous illness' should not be empty"
            ),
            "perpetrators_previous_violence" => array(
                "id" => "select_mas_perpetrators_previous_violence",
                "message" => "'Perpetrators previous violence' should not be empty"
            ),           
            "primary_firearm_type" => array(
                "id" => "mas_primary_firearm_type",
                "message" => "'Primary firearm type' should not be empty"
            ),
            "primary_firearm_action" => array(
                "id" => "mas_primary_firearm_action",
                "message" => "'Primary firearm action' should not be empty"
            ),
            "primary_firearm_make" => array(
                "id" => "mas_primary_firearm_make",
                "message" => "'Primary firearm make' should not be empty"
            ),
            "primary_firearm_obtained_legally" => array(
                "id" => "mas_primary_firearm_obtained_legally",
                "message" => "'Primary firearm obtained legally' should not be empty"
            ),
            "secondary_firearm_type" => array(
                "id" => "mas_secondary_firearm_type",
                "message" => "'Secondary firearm type' should not be empty"
            ),
            "secondary_firearm_action" => array(
                "id" => "mas_secondary_firearm_action",
                "message" => "'Secondary firearm action' should not be empty"
            ),
            "secondary_firearm_make" => array(
                "id" => "mas_secondary_firearm_make",
                "message" => "'Secondary firearm make' should not be empty"
            ),
            "secondary_firearm_obtained_legally" => array(
                "id" => "mas_secondary_firearm_obtained_legally",
                "message" => "'Secondary firearm obtained legally' should not be empty"
            ),
            "citizen_armed_intervention" => array(
                "id" => "mas_citizen_armed_intervention",
                "message" => "'Citizen armed intervention' should not be empty"
            ),
            "narrative" => array(
                "id" => "mas_narrative",
                "message" => "'Narrative' should not be empty"
            )
        );
        
        if ($message === false) {
            if (!empty($field)) {
                return ( isset($rules[$field]) ) ? $rules[$field] : false;
            }
            return false;
        } else if ($message === true) {
            if (isset($messages[$field])) {
                return $messages[$field];
            }
            return false;
        }
    }

    function rule($field, $value) {
        $pass = true;

        $rule = $this->getRule($field);
        if ($rule !== false) {
            if ($pass === true && $rule['required'] === true && required($value) !== true){
                $pass = false;
            }
            
            if ($pass === true && $rule['requiredNemeric'] === true && requiredNumeric($value) !== true){
                $pass = false;
            }    
            if ($pass === true && $rule['currentdate'] === true && currentDate($value) !== true){
                $pass = false;
            }
            if ($pass === true && $rule['matchCoord'] === true && matchCoord($value)!== true){
                $pass = false;
            } 
            if ($pass !== true) {
                return $this->getRule($field, true);
            }
            
            
        }
        return $pass;
    }
    
    function stripslashes_recursive($value) {
        $value = is_array($value) ?
                array_map(array($this, 'stripslashes_recursive'), $value) :
                stripslashes($value);
        return $value;
    }

    function save($input, $boolean_answer=false)
    {
        $clean = array();
        $blankEditObject = $this->fields(false);
        $data = (object) array_intersect_key((array) $input, (array) $blankEditObject);
        
        if (isset($data->id) && $data->id === '0')
        {
            $ignore_id = true;
        }

//check rules
        $return = array();
        $data = (object) $this->stripslashes_recursive((array) $data);
        $allow_breaks = array('narrative', 'content');
        
        foreach ($data as $key => $value)
        {
            if ($key !== 'location')
            {
                $value = GpoCleanInput($value);
                if (!in_array($key, $allow_breaks))
                {
                    $value = GpoSingleLine($value);
                }
                $data->$key = $value;
            }
        }

        foreach ($data as $key => $value)
        {
            if ($key !== 'id') {
                $r = $this->rule($key, $value);
                if ($r !== true) {
                    $return[] = $r;
                }
            } else {
                if ($ignore_id !== true) {
                    $r = $this->rule($key, $value);
                    if ($r !== true) {
                        $return[] = $r;
                    }
                }
            }
        }
        
        if (count($return) > 0)
        {
            if ($boolean_answer) {
                return false;
            }
            $js = "<script>";
            $js .= <<<EOJS
     $('adminForm').select('.error_warning').invoke('removeClassName','error_warning');
     $('message_box').update('');
EOJS;
            $js .= json_encode($return);
            $js .= <<<EOJS
.each(function(obj){
	var oA =new Element('a')
			.writeAttribute('href','#' + obj.id)
			.update( obj.message );
	$( 'message_box').insert({bottom:oA});
        $(obj.id).up(0).select('label').invoke('addClassName','error_warning');
});
window.scrollTo(0,0);
EOJS;
            $js .="</script>";
            return $js;
        }
        
        if (!empty($data->published))
        {
            $data->published = date('Y-m-d H:i:s', strtotime($data->published));
        }
        else
        {
            $data->published = date('Y-m-d H:i:s');
        }
        
         if (!empty($data->modified))
        {
            $data->modified = date('Y-m-d H:i:s', strtotime($data->modified));
        }
        else
        {
            $data->modified = date('Y-m-d H:i:s');
        }
        
        if (!empty($data->date_of_shooting))
        {
            $data->date_of_shooting = date('Y-m-d H:i:s', strtotime($data->date_of_shooting));
        }

        //unset or else it will breack insert/update as locations is in a different table
        unset($data->locations);
        
        //save - data
        $tbl_name = $this->make_table_name(false, false);
        $unix_timestamp = ( isset($_SERVER['REQUEST_TIME']) ) ? $_SERVER['REQUEST_TIME'] : date('U');
        $data->modified = date('Y-m-d H:i:s', $unix_timestamp);

        if ($data->id === '0')
        {
            $ret = $this->_db->insertObject($tbl_name, $data, 'id');            
        }
        else
        {
            $ret = $this->_db->updateObject($tbl_name, $data, 'id', true);
        }

        if (!$ret)
        {
            if ($boolean_answer)
            {
                return false;
            }
            return 'An error occured whilst saving.';
        }
        
        
        //save - locations
        //$unpublished_id = $data->id;
        $this->new_id = $data->id;
        if (isset($_POST['new_record']) && $_POST['new_record'] === '1')
        {
            $js_include = <<<JS
            $("message_box").update("");
            $("adminForm").fire("adminFormMas:clone");
JS;
            $js = "<script type='text/javascript'>" . $js_include . "</script>";

            $response = array();
            $response['pass'] = true;
            $response['force'] = true;
            $response['js'] = $js;
        }
        else
        {

            $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=unpublished', false);
            $js = "<script type='text/javascript'>window.location='" . $link . "'</script>";
            $response = array();
            $response['pass'] = true;
            $response['force'] = false;
            $response['js'] = $js;
        }

        $cmd = 'rm -f ';
        return $response;
    }

    /*
     * This will need expanding!
     */

    function delete($id) {
        $response = null;
        $aOptions = Joomla\CMS\Factory::getApplication()->getInput()->get('options', '0', 'POST', 'array');
        if (isset($aOptions['approve']) && (int) $aOptions['approve'] === (int) '1') {
            $ret = $this->deletePublishedById($id);
            if ($ret) {
//				$mainframe->enqueueMessage( 'Mas Item has been deleted.' );
                $response->msg = 'Mas Item has been deleted.';
                echo 'Mas Item has been deleted.';
            } else {
//				$mainframe->enqueueMessage( 'Failed to delete Mas Item.' );
                $response->msg = 'Failed to delete Mas Item.';
                echo 'Failed to delete Mas Item.';
            }
        } else {
//			$mainframe->enqueueMessage( 'Mas Item has been deleted.' );
            $response->msg = 'Mas Item has been deleted.';
            echo 'Mas Item has been deleted.';
        }


        $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=published', false);
        $js = "<script>self.setInterval( function(){ window.location='" . $link . "';},1000 )</script>";
        return $js;
    }

    function emptyUnpublished() {
        $tbl_name = $this->make_table_name(false);
        $query = "TRUNCATE TABLE " . $tbl_name;
        $this->_db->setQuery($query);
        $this->_db->execute();

        $query = "DELETE FROM `#__gpo_mas_locations_unpublished` `ext_type`=" . $this->_db->quote($this->type);
        $this->_db->setQuery($query);
        $this->_db->execute();
//Remove all with type "m"
        $query = "DELETE FROM `#__gpo_awaiting_published` WHERE `ext_table`='m'";
        $this->_db->setQuery($query);
        $this->_db->execute();

        $response = new stdClass();
        $response->pass = false;
        $response->msg = 'Unpublished Mas have been deleted';
        $response->link = 'index.php?option=com_gpo&controller=mas&task=published';
        return $response;
    }

    function isBuildInProgress() {
        $filename_inprogress = 'inprogress.txt';
        exec("ls -l " . $this->path . " | awk '{print $9}'", $output);

        if (in_array($filename_inprogress, $output)) {
            return true;
        }
        return false;
    }

    function isReIndexInProgress() {
        $filename = $this->path . 'reindex_inprogress.txt';
        if (file_exists($filename)) {
            return true;
        }
        return false;
    }

    function shouldReIndexForSphinx() {
        $filename = $this->path . 'reindex.txt';
        if (file_exists($filename)) {
            return true;
        }
        return false;
    }

    function mailPending() {
        $query = "SELECT COUNT( `mas_id` ) FROM `#__gpo_mas_mail` LIMIT 0,1;";
        $this->_db->setQuery($query);
        $r = $this->_db->loadResult();
        if ($r) {
            $this->mailPending = $r;
        } else {
            $this->mailPending = false;
        }
        return $this->mailPending;
    }

    function setReIndex() {
        $filename = $this->path . 'reindex.txt';
        touch($filename);
    }

    function getMailList($share='1') {
        $query = "
SELECT `n`.`id`,`n`.`gpnheader`,`n`.`share`,`n`.`source`,`n`.`title`, `m`.`global_interest`
FROM `#__gpo_mas_mail` AS `m`
INNER JOIN `#__gpo_mas` AS `n` ON `m`.`mas_id` = `n`.`id` 
";
        if ($share === '1') {
            $query .= " WHERE `n`.`share`='1' ";
        }
        $query .=" ORDER BY `n`.`id` DESC";

        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();
        return $data;
    }

    function getMailListByLocation($name='', $share="1") {
//this is all
        if (empty($name)) {
            return $this->getMailList($share);
        }
        /*
          If at a later date, philip wants to make trickier filters,
          1) look to a switch
          2) maybe look to integrate with sphinx if trickier = search result based...
         */
//Return items which are not purely Non US.		
        if ($name === 'non-us') {
//get id for United States
            $query = "
SELECT `l`.`id`
FROM `#__gpo_location` as `l`
WHERE `l`.`name` ='United States';
";
            $this->_db->setQuery($query);
            $id = $this->_db->loadResult();

            $query = "
SELECT DISTINCT( `ll`.`mas_id` )
FROM `#__gpo_location` as `lo`,
(
SELECT *
FROM `#__gpo_mas_mail` AS `m`
LEFT JOIN `#__gpo_mas_locations` as `nl` ON `m`.`mas_id`=`nl`.`ext_id`
) `ll`
WHERE `lo`.`id`=`ll`.`location_id`
AND `ll`.`location_id` !=" . $id . ";
";
            $this->_db->setQuery($query);
            $ids = $this->_db->loadColumn();

//else if for anything else
        } else {
            $query = "
SELECT `l`.`id`
FROM `#__gpo_location` as `l`
WHERE `l`.`name` =" . $this->_db->quote($name) . ";
";
            $this->_db->setQuery($query);
            $id = $this->_db->loadResult();
//			ftp_debug( $query, 'query', true, false );
            if (empty($id))
                return false;

//			ftp_debug( $id, 'id of location', true, false );
//get all locations linked to this location and below, ie id = parent.
            $query = "
SELECT `ld`.`link_id`
FROM `#__gpo_location` as `l`
LEFT JOIN  `#__gpo_location_links_deep` as `ld` ON `l`.`id` = `ld`.`location_id` 
WHERE `l`.`id` =" . $id . ";
";
            $this->_db->setQuery($query);
            $location_ids = $this->_db->loadColumn();

            if (empty($location_ids) || empty($location_ids['0'])) {
                $location_ids = array();
            }
            $location_ids[] = $id;

//			ftp_debug( $location_ids, 'ids linked to this location', true, false );
//			ftp_debug( $query, 'query', true, false );
//get mas_ids based on location filter
            $query = "
SELECT DISTINCT( `ll`.`mas_id` )
FROM `#__gpo_location` as `lo`,
(
SELECT *
FROM `#__gpo_mas_mail` AS `m`
LEFT JOIN `#__gpo_mas_locations` as `nl` ON `m`.`mas_id`=`nl`.`ext_id`
) `ll`
WHERE `lo`.`id`=`ll`.`location_id`
AND `ll`.`location_id` IN( " . implode(",", $location_ids) . " ) ";

            $this->_db->setQuery($query);
            $ids = $this->_db->loadColumn();
//			ftp_debug( $ids, 'ids of mas_id', true, false );			
//			ftp_debug( $query, 'query', true, false );
        }
//if there are no ids ( mas_ids ) then return
        if (!isset($ids) || empty($ids))
            return false;

//		ftp_debug( $ids, 'Ids of mas_ids', true, false );		
//get the data in the format of getMailList();

        $query = "
SELECT `n`.`id`,`n`.`gpnheader`,`n`.`share`, `m`.`global_interest`, GROUP_CONCAT( `l`.`name` ) as locations 
FROM `#__gpo_mas` as `n` 
INNER JOIN `#__gpo_mas_mail` as `m` ON `m`.`mas_id` = `n`.`id`
LEFT JOIN `#__gpo_mas_locations` as `nl` ON `n`.`id`=`nl`.`ext_id` 
LEFT JOIN `#__gpo_location` as `l` ON `l`.`id` = `nl`.`location_id` 
WHERE `n`.`id` IN( " . implode(",", $ids) . " )";

        if ($share === '1') {
            $query .= " AND `n`.`share`='1' ";
        }

        $query .= "
GROUP BY `n`.`id`
ORDER BY `n`.`id` DESC;
";
        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();
//		ftp_debug( $query, 'query NEWS', true, false );		
//		ftp_debug( $data, 'data', true, false );
        return $data;
    }

    /*
      Default send one at a time to the members.
      //Do we want to log this? then on the publish page it displays if it has previously been emailed allowing us to skip re-emailing.
      //unless the system changes, it should not be possible to add a record to the public mailing list without it being set to share=1
     */

    function mailToPublic() {
        $locations = array(
//World		
            '' => array('location' => 'World', 'email' => "Gun-Policy-Mas-World@googlegroups.com"),
            'non-us' => array('location' => 'NonUSA', 'email' => "Gun-Policy-Mas-NonUSA@googlegroups.com"),
            'North America' => array('location' => 'NthAmer', 'email' => "Gun-Policy-Mas-NthAmer@googlegroups.com"),
            'Central America' => array('location' => 'CenAmer', 'email' => "Gun-Policy-Mas-CenAmer@googlegroups.com"),
            'South America' => array('location' => 'SthAmer', 'email' => "Gun-Policy-Mas-SthAmer@googlegroups.com"),
            'Europe' => array('location' => 'Europe', 'email' => "Gun-Policy-Mas-Europe@googlegroups.com"),
            'Africa' => array('location' => 'Africa', 'email' => "Gun-Policy-Mas-Africa@googlegroups.com"),
            'West Asia' => array('location' => 'WestAsia', 'email' => "gun-policy-mas-westasia-@googlegroups.com"),
            'Asia' => array('location' => 'Asia', 'email' => "Gun-Policy-Mas-Asia@googlegroups.com"),
            'Oceania' => array('location' => 'Oceania', 'email' => "Gun-Policy-Mas-Oceania@googlegroups.com")
        );


        $strtr = array("\r\n" => "\n", "\n\n\n\n" => "\n", "\n\n\n" => "\n", "\n\n" => "\n", "<br>" => "\n", "<br />" => "\n");
        $config = &JFactory::getConfig();

        $mailSummary = "<pre>";
        $mailDetailedSummary = "";

        $globalInterest = $this->getGlobalInterestIds();


        foreach ($locations as $location => $location_info) {
            $items = array();
            if (!empty($globalInterest)) {
                foreach ($globalInterest as $item) {
                    if (!isset($items[$item['id']])) {
                        $items[$item['id']] = $item;
                    }
                }
            }

            $items_by_location = $this->getMailListByLocation($location, '1');

            if ($items_by_location !== false) {
                foreach ($items_by_location as $item) {
                    if (!isset($items[$item['id']])) {
                        $items[$item['id']] = $item;
                    }
                }
            }

            if (count($items) < 1) {
                continue;
            }

            $mailHistoryName = 'GPN (' . $location_info['location'] . ')';
            $index = 'Gun Policy Mas (' . $location_info['location'] . ') Updated ' . gmdate('j M Y\, H:i:s T') . "\n\n";
            $snippets = array();

            $pos = 0;
            foreach ($items as $mailItem) {
                ++$pos;
                $prefix = '';
                $anchor = 'toc-' . $pos;
                //Index of the email
                $index .= '<a href="#' . $anchor . '">' . $prefix . $mailItem['gpnheader'] . "</a>\n";

                $item = $this->getPublishedById($mailItem['id']);
                $linkSourceURL    = !empty($item->twitter_url) ? $item->twitter_url : 
                                    JURI::root() . 'firearms/mas/' . $item->id;
                $linkSourceLabel  = 'Link to the original article';
                $linkSourceLabel .= !empty($item->source) ? (!empty($linkSourceLabel) ? ' — ' : '') . $item->source : '';
                
                //Each snippet
                $snippet  = "\n\n";
                $snippet .= '<a name="' . $anchor . '">' . $prefix . implode(", ", $item->locations) . "</a>\n\n";

                $snippet .= $item->title . "\n";
                $snippet .= $item->source . "\n";
                if (!empty($item->category)) {
                    $snippet .= $item->category . "\n";
                }
                $snippet .= date('j F Y', strtotime($item->published)) . "\n\n";

                $str = gpo_helper::short($item->content);
                $str = str_replace("\n", " ", $str);

                $snippet .= $str . "\n\n";

                if (!empty($linkSourceLabel)) {
                    $snippet .= '<a href="' . $linkSourceURL . '">' . $linkSourceLabel . '</a>' . "\n\n";
                }else {
                    $snippet .= '<a href="' . $linkSourceURL   . '">'   . $linkSourceURL . '</a>' . "\n\n";
                }
                $snippets[] = $snippet;
            }

//Build the body
            $body = $index . "\n+++++";
            $body .= implode("+++++", $snippets);

            $body = GpoCleanContentForEmail($body);

            $from = "gpnl@gunpolicy.org";
            $fromname = $config->get('sitename');
            /*
              //TESTING -
              $email = 'alpers@gunpolicy.org';
              if( $location_info['email'] !== 'gun-policy-mas-westasia-@googlegroups.com' )
              {
              $subject = 'BR TEST - ' . date("j F Y");
              }else{
              $email = $location_info['email'];
              $subject = date("j F Y");
              }
             */
            $mailDetailedSummary .= '
<hr>
<p>
From: ' . $from . '<br />
FromName: ' . $fromname . '<br />
To: ' . $email . '<br />
Subject: ' . $subject . '<br />
Body:<br />
' . $body . '
</p>';

//Live version - block out if testing
//start
            $mailer    = JFactory::getMailer();
            $email     = $location_info['email'];
            $subject   = date("j F Y");
            $sender    = array($from, $fromname);
            $mailer->addRecipient($email);
            $mailer->setSender($sender);
            $mailer->setSubject($subject);
            $mailer->isHTML(true);
            $mailer->Encoding = 'base64';
            $mailer->setBody($body);
//finish
//Normal  - this allow for semi testing of the live site
            if ($this->block_emails === false) {
                $send = $mailer->Send();
                if ( $send !== true ) {
                   echo 'Failed to send email';
                }
                //Loop in here to get the history inserts based on MailItems
                $mailSummary .= "Mail sent to '" . $email . "' " . $mailHistoryName . "\n";

                foreach ($items as $log) {
                    $this->insertMailHistory($log['id'], $mailHistoryName);
                }
            }
        }//end big loop
        $mailSummary .= "</pre>";
        if ($this->block_emails === false) {
//This tells us at least one email was sent.		
            if (!empty($mailDetailedSummary)) {
                $this->deleteMailToPublic('all');
            }
        }
        return $mailSummary . $mailDetailedSummary;
    }
    /*
      Default send one at a time to the members.
      //Do we want to log this? then on the publish page it displays if it has previously been emailed allowing us to skip re-emailing.
     */

    function mailToMembers($masId) {
        $item   = $this->getPublishedById($masId);
        $config = &JFactory::getConfig();
        $mailer = JFactory::getMailer();
        $email = 'gun-policy-mas-members@googlegroups.com';

        $from = "gpnl@gunpolicy.org"; //$config->getValue('mailfrom');
        $fromname = $config->get('sitename');
        $subject = $item->gpnheader;

        $strtr = array("\r\n" => "\n", "\n\n\n\n" => "\n", "\n\n\n" => "\n", "\n\n" => "\n", "<br>" => "\n", "<br />" => "\n");

        $subject = GpoEndWith(" ", $item->gpnheader);

        if (!empty($item->source)) {
            $subject .= "— " . trim($item->source);
            if (!empty($item->category)) {
                $subject = GpoEndWith(", ", $subject) . $item->category;
            }
        }

        $body = $item->title . "\n\n";
        if (!empty($item->subtitle)) {
            $body .= $item->subtitle . "\n\n";
        }
        $body .= $item->source . "\n";

        if (!empty($item->category)) {
            $body .= $item->category . "\n";
        }

        $body .= date('j F Y', strtotime($item->published)) . "\n";

        if (!empty($item->byline)) {
            $body .= "By " . $item->byline . "\n";
        }

        $signature = " (" . JApplication::getCfg('sitename') . ")";
        $content = GpoCleanInput($item->content);

        $content = GpoEndWith($signature, $content);
        $body .= "\n" . $content . "\n\n";

//		$body .= "\n" . $item->content;
//
//		if( substr( $content,-1,1 ) !== " " )
//		{
//			$body .= " ";
//		}
// 		$body .= "(" . JApplication::getCfg('sitename')  .")\n\n";
        
        $linkSourceLabel  = 'Link to the original article';
        $linkSourceLabel .= !empty($item->source) ? (!empty($linkSourceLabel) ? ' — ' : '') . $item->source : '';
        if (!empty($item->websource)) {
            $body .= '<a href="' . $item->websource . '">' . $linkSourceLabel . '</a>' . "\n\n\n";
        }

        $body = GpoCleanContentForEmail($body);
        
        $sender  = array($from, $fromname);
        $mailer->addRecipient($email);
        $mailer->setSender($sender);
        $mailer->setSubject($subject);
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($body);
        
//Normal 
        if ($this->block_emails === false) {
            $send = $mailer->Send();
            if ($send !== true) {
               echo 'Failed to send email';
            }
            $this->insertMailHistory($masId, 'Members Only');
            return true;
        } else {
//Testing
            echo '<p>You will need to republish this after block_emails = false, this should only be seen in testing.</p>';
            ftp_debug($body, 'Body');
        }
    }

    
    function localMailTest($toEmail) {
        if( empty($toEmail) ) {
            return false;
        }    
        $config = JFactory::getConfig();
        $mailer = JFactory::getMailer();
        $email = $toEmail;

        $from = "gpnl@gunpolicy.org"; //$config->getValue('mailfrom');
        $fromname = $config->get('sitename');
        $subject = "Send email to mas list";
        $body = "Mas item emails";
        
        $sender  = array($from, $fromname);
        $mailer->addRecipient($email);
        $mailer->setSender($sender);
        $mailer->setSubject($subject);
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($body);
        
        $send = $mailer->Send();
        if ($send !== true) {
            echo 'Failed to send email';
        } else {
            echo "<br> Successfully sent email";
        }
        
    }
    
    function insertMailHistory($masId, $type) {
        $unix_timestamp = ( isset($_SERVER['REQUEST_TIME']) ) ? $_SERVER['REQUEST_TIME'] : date('U');
        $when = date('Y-m-d H:i:s', $unix_timestamp);

        $query = "INSERT INTO `#__gpo_mas_mail_archive` ( `id`,`masid`,`type`, `when`) VALUES( NULL," . $this->_db->quote($masId) . "," . $this->_db->quote($type) . "," . $this->_db->quote($when) . ")";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
    }

    function getMailHistory($masId) {
        $query = "SELECT `when`, `type` ";
        $query .= " FROM `#__gpo_mas_mail_archive` ";
        $query .= " WHERE `masid`=" . $this->_db->quote($masId) . " ORDER BY `when` DESC";

        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();
        return $data;
    }

    function insertMailToPublic($id, $global_interest) {
        $global_interest = ( (string) $global_interest === '1' ) ? '1' : '0';

        $query = "INSERT IGNORE INTO `#__gpo_mas_mail` (`mas_id`, `global_interest` ) VALUES( " . $this->_db->quote($id) . "," . $global_interest . ")";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
    }

    function deleteMailToPublic($id) {
        if ($id === 'all') {
//clear all
            $query = "TRUNCATE TABLE `#__gpo_mas_mail`;";
            $this->_db->setQuery($query);
            $ret = $this->_db->execute();
            return;
        }
        
        if (is_numeric($id) && !empty($id)) {
//remove just one item
            $query = "DELETE FROM `#__gpo_mas_mail` WHERE `mas_id`=" . $this->_db->quote($id);
            $this->_db->setQuery($query);
            $ret = $this->_db->execute();
            return;
        }
    }

    function getGlobalInterestIds($share='1') {
        $query = "
SELECT `n`.`id`,`n`.`gpnheader`,`n`.`share`,`n`.`source`,`n`.`title`,1 AS `global_interest`
FROM `#__gpo_mas_mail` AS `m`
INNER JOIN `#__gpo_mas` AS `n` ON `m`.`mas_id` = `n`.`id`
WHERE `m`.`global_interest`=1 
";
        if ($share === '1') {
            $query .= " AND `n`.`share`='1' ";
        }
        $query .=" ORDER BY `n`.`id` DESC";

        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList('id');
        return $data;
    }

    // **START** This code for convert mas long url to bitly short url
    function bitly_short_url($url, $bitlyKey, $format='txt') {
        $login = $bitlyKey->consumer_key;// bitly_api_login
        $appkey = $bitlyKey->consumer_secret; // bitly_api_key
        $url = trim($url);
        $bitly_api = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
        //$bitly_api = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.$url.'&format='.$format;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$bitly_api);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,25);
        $data = curl_exec($ch);
        
        curl_close($ch);

        return $data;
    }

    function bitlyKey(){
        $selectBitlyKey = "SELECT * FROM j25_gpo_twitter_oauth WHERE client='bitly'";

        $this->_db->setQuery($selectBitlyKey);
        $data = $this->_db->loadObject();
        return $data;
    }

    function twitterPost($twitterKey, $oMas) {

        // Create an OAuth connection to the Twitter API
        $connection = new tmhOAuth(array(
            'consumer_key'    => $twitterKey->consumer_key,
            'consumer_secret' => $twitterKey->consumer_secret,
            'user_token'      => $twitterKey->user_token,
            'user_secret'     => $twitterKey->user_secret
        ));
        $tweet = $oMas->twitter_text .' '. $oMas->twitter_url .' '. $oMas->twitter_hashtag;
        // Send a tweet
        $code = $connection->request('POST',
            $connection->url('1.1/statuses/update'),
            array('status' => $tweet));
        
        // A response code of 200 is a success
        if ($code == 200) {
            return true;
        }
        
        return false;
    }

    function twitterKey(){
        $selectBitlyKey = "SELECT * FROM j25_gpo_twitter_oauth WHERE client='twitter'";

        $this->_db->setQuery($selectBitlyKey);
        $data = $this->_db->loadObject();
        return $data;
    }
    // **END** bitly

    /** This function for bitly url */
    function generate_twitter_bitly_url($mas_live_id = '', $mas_twitter_url = '') {
        
        $siteURL = JURI::root();
        
        if( !empty($mas_live_id) ) {
            $long_url = $siteURL . "firearms/mas/" . trim($mas_live_id);
        }else{
            $long_url = $siteURL . "firearms/mas/latest";
        }
        
        if(empty($mas_twitter_url)) {
            $bitlyKey = $this->bitlyKey();
            $mas_bitly_url = $this->bitly_short_url($long_url, $bitlyKey);
            
            return $mas_bitly_url;
        }

        return $mas_twitter_url;
    }


    function tweet($data, $live_id){

        $tweet_msg = '';
        // checking twitter_url to create bitly url
        if(empty($data->twitter_url)) {
            $data->twitter_url = $this->generate_twitter_bitly_url($live_id, $data->twitter_url);
        }
        
        if(!empty($data->twitter_text)) {
            $twitterKey = $this->twitterKey();
            $twitterResponse = $this->twitterPost($twitterKey, $data);
            
            if($twitterResponse) {
               $this->set_twitter_flag($live_id, $data->twitter_url);
               $tweet_msg = '<a href="http://twitter.com" target="_blank" style="text-decoration:underline;"> Successfully posted to twitter, check here</a>';
            }
        }
        
        return $tweet_msg;
    }

    function get_twitter_flag($live_id){
        $getTwitterFlag = "SELECT twitter_flag FROM  `j25_gpo_mas`  WHERE id =" . $live_id;

        $this->_db->setQuery($getTwitterFlag);
        $data = $this->_db->loadObject();
        return $data;
    }

    function set_twitter_flag($live_id, $twitter_url) 
    {

        $flag = $this->get_twitter_flag($live_id);
        $flag_count = $flag->twitter_flag + 1;


        $updateTwitterFlag = "UPDATE
                                    `j25_gpo_mas`
                              SET
                                    `twitter_flag` = " . $flag_count .
            ", `twitter_url` = '" . $twitter_url . "'
                              WHERE
                                     `id` = " . $live_id;


        $this->_db->setQuery($updateTwitterFlag);
        $result = $this->_db->execute();

        return $result;
    }


    function getStatesByCountry($locationId) {
        $locations = array();
        if (empty($locationId)) {
            return $locations;
        }

        $query = "SELECT 
                        `lo`.`id`, `lo`.`name`
                  FROM  
                        `#__gpo_location` AS `lo` 
                  INNER JOIN 
                        `#__gpo_location_links` AS `link` ON `lo`.`id`=`link`.`link_id`
                  WHERE 
                        `lo`.`type` =  'state_province' 
                         AND `lo`.`display` =  '1' 
                         AND `link`.`location_id` =(SELECT `id` FROM `#__gpo_location` WHERE `id` = '" . $locationId . "')";
        $this->_db->setQuery($query);
        $result = $this->_db->loadAssocList();

        return $result;
    }
    
    function getStatesofJurisdiction() {
        
        $query =  "SELECT * FROM `j25_gpo_location` WHERE type='jurisdiction' order by `name`";
        $this->_db->setQuery($query);
        $result = $this->_db->loadAssocList();
        
        return $result;
    }
    
    function getLocationIdByName($locationName, $type = 'country') {
        if (empty($locationName)) {
            return false;
        }

        $locationName = trim($locationName);
        
        $query = "SELECT 
                        `lo`.`id`, `lo`.`name` 
                  FROM  
                        `#__gpo_location` AS `lo` 
                  WHERE 
                        `lo`.`name` = '$locationName' 
                  AND 
                        `lo`.`type` = '$type' 
                  AND 
                        `lo`.`display` = '1' 
                 ";
        $this->_db->setQuery($query);
        $result = $this->_db->loadObject();

        if (empty($result)) {
            return false;
        }

        return $result->id;
    }

    
    function getCityIdByName($cityName) {
        if (empty($cityName)) {
            return false;
        }

        $cityName = trim($cityName);
        
        $query = "SELECT 
                        `lo`.`id`, `lo`.`value` 
                  FROM 
                        `#__gpo_city` AS `lo` 
                  WHERE 
                        `lo`.`value` = '$cityName' 
                 ";
        $this->_db->setQuery($query);
        //echo $query;
        $result = $this->_db->loadObject();

        if (empty($result)) {
            return false;
        }

        return $result->id;
    }
    
    
    function getLocationNameById($locationId, $type = 'country') {
        if (empty($locationId)) {
            return false;
        }

        $query = "SELECT 
                        `lo`.`id`, `lo`.`name` 
                  FROM  
                        `#__gpo_location` AS `lo` 
                  WHERE 
                        `lo`.`id` = '$locationId' 
                  AND 
                        `lo`.`type` = '$type' 
                  AND 
                        `lo`.`display` = '1' 
                 ";
        $this->_db->setQuery($query);
        $result = $this->_db->loadObject();

        if (empty($result)) {
            return false;
        }

        return $result->name;
    }

    
    function getCityNameById($cityId) {
        if (empty($cityId)) {
            return false;
        }
        
        $query = "SELECT 
                        `lo`.`id`, `lo`.`value` 
                  FROM 
                        `#__gpo_city` AS `lo` 
                  WHERE 
                        `lo`.`id` = '$cityId' 
                 ";
        $this->_db->setQuery($query);
        $result = $this->_db->loadObject();

        if (empty($result)) {
            return false;
        }

        return $result->value;
    }

}
?>