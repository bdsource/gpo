<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

class GpoModelQuotes extends JModelList
{
    var $total;
    var $data;

    function __construct()
    {
        parent::__construct();
        $this->limit = Joomla\CMS\Factory::getApplication()->getInput()->get('limit', '100', '', 'int');
        $this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', '', 'int');
		$this->pagination->limit = 10;

        //$mainframe =& JFactory::getApplication();

        //$this->filter_order = $mainframe->getUserStateFromRequest('filter_order', 'filter_order', 'id');
        //$this->filter_order_Dir = $mainframe->getUserStateFromRequest('filter_order_Dir', 'filter_order_Dir', 'desc');

        //$this->path = "/home/palpers/gp-uploads/quotes/";
        $this->path = "/home/gpo/gp-uploads/quotes/";
    }


    /*
      * creates the table name
      */
    function make_table_name($published = false, $quotes = true)
    {
        if ($quotes) {
            $quote = "`";
        } else {
            $quote = "";
        }
        return $quote . '#__gpo_quotes' . (($published === false) ? '_unpublished' : '') . $quote;
    }

    /*
      * this is used for unpublished blank entry
      * live_id
      */
    
    function fields($published = false)
    {
        if ($published === false) {
            $fields = "id,live_id,published,title,source,publisher,volume,issue,page,city,author,affiliation,keywords,content,websource,entered,modified,notes,sourcedoc,staff,share,poaim,clonedFrom";
        } else {
            $fields = "id,published,title,source,publisher,volume,issue,page,city,author,affiliation,keywords,content,websource,entered,modified,notes,sourcedoc,staff,share,poaim";
        }
        
        return (object) array_fill_keys(array_values(explode(",", $fields)), '');
    }

    /*
      * get the unpublished item
      */
    
    function getUnPublishedById($id)
    {
        $published = false;
        if ((int)$id === (int)'0') {
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
        return $data;
    }


    /*
      * get the unpublished item
      */
    function getPublishedById($id)
    {
        $published = true;
        if ((int)$id === (int)'0') {
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
        return $data;
    }

    function getNextById($id, $published = true)
    {
        if ((int)$id === (int)'0') {
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

    function getPrevById($id, $published = true)
    {
        if ((int)$id === (int)'0') {
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


    function getLocations($id, $published)
    {
        if ($published === false) {
            $query = "SELECT `lo`.`name` FROM `#__gpo_location` as `lo` LEFT JOIN `#__gpo_quotes_locations_unpublished` as `lon` ON `lo`.`id`=`lon`.`location_id` WHERE `lon`.`ext_id`=" . $this->_db->quote($id) . ' ORDER BY `lon`.`id` ASC;';
        } else {
            $query = "SELECT `lo`.`name` FROM `#__gpo_location` as `lo` LEFT JOIN `#__gpo_quotes_locations` as `lon` ON `lo`.`id`=`lon`.`location_id` WHERE `lon`.`ext_id`=" . $this->_db->quote($id) . ' ORDER BY `lon`.`id` DESC;';
        }
        $this->_db->setQuery($query);
        $locations = $this->_db->loadColumn();
        return $locations;
    }


    function total($published = false)
    {
        $tbl_name = $this->make_table_name($published);
        $query = "SELECT COUNT( `id` ) FROM " . $tbl_name . " LIMIT 0,1";
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }


    function unpublished($filter_order, $filter_order_dir)//$filter_order, $filter_order_dir
    {
        $where = "";

        $published = false;
        $this->total = $this->total($published);
        if ((int)$this->total === (int)'0') {
            return array();
        }
        $this->pagination = new JPagination($this->total, $this->limitstart, $this->limit);

        $tbl_name = $this->make_table_name($published);
        //		$query = "SELECT * FROM " . $tbl_name;
        $query = "SELECT `c` . * , `ap`.`id` AS `ap_id` FROM " . $tbl_name . " AS `c` LEFT JOIN `#__gpo_awaiting_published` AS `ap` ON `c`.`id` = `ap`.`ext_id` AND `ap`.`ext_table` = 'q'";
        //$query .= " ORDER BY `modified` ASC ";
        //$query .= " ORDER BY {$this->_db->quoteName($filter_order)} $filter_order_dir ";
        $query .= " " . $where;

        if(empty($filter_order)) {
           $orderby = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', '', '', 'string');
        }else {
           $orderby = $filter_order;
        }
        if(empty($filter_order_dir)) {
           $orderbydir = ( Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', '', '', 'string') === 'asc' ) ? 'ASC' : 'DESC';
        } else {
           $orderbydir = $filter_order_dir;
        }
        
        if( in_array( $orderby, array( 'id' ) ) )
        {
            $query .= " ORDER BY `" . $orderby . "` " . $orderbydir;
        }elseif (in_array( $orderby, array( 'author' ) ) ) {
            $query .= " ORDER BY `" . $orderby . "` " . $orderbydir;
        }elseif (in_array( $orderby, array( 'title' ) ) ) {
            $query .= " ORDER BY `" . $orderby . "` " . $orderbydir;
        }elseif (in_array( $orderby, array( 'modified' ) ) ) {
            $query .= " ORDER BY `" . $orderby . "` " . $orderbydir;
        }elseif (in_array( $orderby, array( 'staff' ) ) ) {
            $query .= " ORDER BY `" . $orderby . "` " . $orderbydir;
        }else{
            $query .= " ORDER BY `id` DESC ";
        }

        $this->_db->setQuery($query, $this->pagination->limitstart, $this->pagination->limit);
        return $this->_db->loadAssocList();
    }


    function published()
    {   
        $where = "";

        $published = true;
        $this->unpublishedTotal = $this->total(false);
        $this->total = $this->total($published);
        if ((int)$this->total === (int)'0') {
            return array();
        }
        $this->pagination = new JPagination($this->total, $this->limitstart, $this->limit);

/*
        $tbl_name = $this->make_table_name($published);
        //this should be minimal as content will get big? for now leave it.
        $query = "SELECT * FROM " . $tbl_name;
        //		$query .= " ORDER BY `published` DESC ";
        $query .= " ORDER BY `id` DESC ";
*/
        $tbl_name = $this->make_table_name( true );
        $query = "SELECT * FROM " . $tbl_name;
        $query .= " " . $where;

        $orderby = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', '', '', 'string');
        $orderbydir = ( Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', '', '', 'string') === 'asc' ) ? 'ASC' : 'DESC';
        if( in_array( $orderby, array( 'published', 'id' ) ) )
        {
            $query .= " ORDER BY `" . $orderby . "` " . $orderbydir;
        }else{
            $query .= " ORDER BY `id` DESC ";
        }


        $this->_db->setQuery($query, $this->pagination->limitstart, $this->pagination->limit);
        return $this->_db->loadAssocList();
    }


    function copyForEdit($id)
    {
        //If already in edit, stop.
        $tbl_name = $this->make_table_name(false);
        $query = "SELECT `id`,`live_id` FROM " . $tbl_name . " WHERE `live_id`=" . $this->_db->quote($id) . " LIMIT 0,1";
        $this->_db->setQuery($query);
        $oLive = $this->_db->loadObject();
        if (!empty($oLive->id)) {
            return $oLive;
        }

        $oLive = $this->getPublishedById($id);
        $tbl_name = $this->make_table_name(true);
        $query = "SELECT * FROM " . $tbl_name . " WHERE `id`=" . $this->_db->quote($id) . " LIMIT 0,1";
        $this->_db->setQuery($query);
        $oLive = $this->_db->loadObject();


        $blankObject = $this->fields(false);
        $data = (object)array_intersect_key((array)$oLive, (array)$blankObject);

        $data->live_id = $data->id;
        $data->id = '';

        $tbl_name = $this->make_table_name(false, false);
        $ret = $this->_db->insertObject($tbl_name, $data, 'id');

        if ($ret) {
            $query = "INSERT INTO `#__gpo_quotes_locations_unpublished`( `location_id`,`ext_id` )";
            $query .= "SELECT  `location_id`, '" . $data->id . "' as `ext_id` FROM `#__gpo_quotes_locations` WHERE `ext_id`=" . $this->_db->quote($data->live_id);

            $this->_db->setQuery($query);
            $this->_db->execute();
            return $data;
        } else {
            $o = null;
            $o->id = '0';
            return $o;
        }
    }


    function publish($oItem)
    {
        $live_id = (isset($oItem->live_id)) ? $oItem->live_id : null;
        $unpublished_id = $oItem->id;

        $aPublish = Joomla\CMS\Factory::getApplication()->getInput()->get('publish', '0', 'POST', 'array');
        if (isset($aPublish['approve']) && (int)$aPublish['approve'] === (int)'1') {
            $aPublish['live_id'] = $live_id;
            $aPublish['unpublished_id'] = $unpublished_id;
            $r = $this->moveUnpublishToPublished($oItem, $aPublish);
            return $r;
        }
        $response = new stdClass();
        $response->msg = 'You have not ticked the approve box';
        $response->link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=publish&id=' . $unpublished_id, false);
        return $response;
    }


    function getPublishGreen()
    {
        $tbl_name = $this->make_table_name(false);
        $query = "
SELECT `c`. * , `ap`.`id` AS `ap_id` 
FROM " . $tbl_name . " AS `c` 
INNER JOIN `#__gpo_awaiting_published` AS `ap` ON `c`.`id` = `ap`.`ext_id` AND `ap`.`ext_table` = 'q'
WHERE `c`.`live_id` != 0";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObjectList();
        return $data;
    }


    function publishGreen($items)
    {

        $aPublish = Joomla\CMS\Factory::getApplication()->getInput()->get('publish', '0', 'POST', 'array');
        if (isset($aPublish['approve']) && (int)$aPublish['approve'] === (int)'1') {
            if (!empty($items) && is_array($items)) {
                foreach ($items as $oItem)
                {
                    $live_id = (isset($oItem->live_id)) ? $oItem->live_id : null;
                    $unpublished_id = $oItem->id;
                    $aPublish['live_id'] = $live_id;
                    $aPublish['unpublished_id'] = $unpublished_id;
                    $r = $this->moveUnpublishToPublished($oItem, $aPublish);
                    //					ftp_debug( $r, 'response', true, false );
                }
                $response = new stdClass();
                $response->msg = 'All Green Items published.';
                $response->link = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
                return $response;
            }
        }
        $response = new stdClass();
        $response->msg = 'You have not ticked the approve box';
        $response->link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=publish&id=' . $unpublished_id, false);
        return $response;
    }


    function readyForPublishing($oItem)
    {
        $aPublish = Joomla\CMS\Factory::getApplication()->getInput()->get('publish', '0', 'POST', 'array');
        if (isset($aPublish['approve']) && (int)$aPublish['approve'] === (int)'1') {
            $id = $oItem->id;
            $query = "INSERT IGNORE INTO `#__gpo_awaiting_published` (`id`,`ext_id`,`ext_table`)VALUES(NULL ," . $this->_db->quote($id) . ",'q');";
            $this->_db->setQuery($query);
            $ret = $this->_db->execute();
            $response = new stdClass();
            $response->msg = 'Quote has been queued for publishing. A Super Administrator will approve it shortly.';
            $response->link = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
            $response->pass = true;
            return $response;
        }
    }


    function moveUnpublishToPublished($data, $options)
    {
        $live_id = $options['live_id'];
        $unpublished_id = $options['unpublished_id'];
        //need to tidy up the object
        $blankPublishObject = $this->fields(true);
        $data = (object)array_intersect_key((array)$data, (array)$blankPublishObject);

        $tbl_name = $this->make_table_name(true, false);

        $unix_timestamp = (isset($_SERVER['REQUEST_TIME'])) ? $_SERVER['REQUEST_TIME'] : date('U');
        $unix_timestamp_date = date('Y-m-d H:i:s', $unix_timestamp);

        if (!empty($live_id)) {
            $data->id = $live_id;
            unset($data->live_id);
            unset($data->entered);

            if (isset($options['minor']) && (int)$options['minor'] === (int)'1') {
                unset($data->modified);
            } else {
                $data->modified = $unix_timestamp_date;
            }
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
        $this->publishLocations($live_id, $unpublished_id);
        //Delete unpublished items + locations
        $this->deleteUnpublishedById($unpublished_id);
        //Delete the item from the queue
        //		$this->deleteFromQueue( $unpublished_id );

        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=lookup&id=' . $live_id, false);

        $msg = 'Published: <a href="' . $href . '" style="text-decoration:underline;">view it</a>.';
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=reindex', false);
        $msg .= 'To search for this record straight away, <a href="' . $href . '" style="text-decoration:underline;">update the index</a>.';


        $response = new stdClass();
        $response->msg = $msg;
        $response->link = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
        $response->pass = true;

        //		$link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=reindex',false );
        //		$response = new stdClass();
        //		$response->msg = 'Quote published. To search for this record straight away, <a href="' . $link . '" >Update the Quotes index</a>.';
        //		$response->link = JRoute::_( 'index.php?option=com_gpo&controller=quotes', false );
        //		$response->pass= true;

        return $response;
    }


    function publishLocations($live_id, $unpublished_id)
    {
        //Delete locations from live
        $query = "DELETE FROM `#__gpo_quotes_locations` WHERE `ext_id`=" . $this->_db->quote($live_id) . ";";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();

        //Insert new locations
        $query = "INSERT INTO `#__gpo_quotes_locations`(`location_id`,`ext_id` )";
        $query .= "SELECT  `location_id`, '" . $live_id . "' as `ext_id` FROM `#__gpo_quotes_locations_unpublished` WHERE `ext_id`=" . $this->_db->quote($unpublished_id) . ";";
        $this->_db->setQuery($query);
        $this->_db->execute();
    }


    /*
      * Delete the unpublished news record including its references in the locations section
      */
    function deleteUnpublishedById($id)
    {
        $tbl_name = $this->make_table_name(false);
        $query = "DELETE FROM " . $tbl_name . " WHERE `id`=" . $this->_db->quote($id);
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            return false;
        }

        $this->deleteFromQueue($id);

        $query = "DELETE FROM `#__gpo_quotes_locations_unpublished` WHERE `ext_id`=" . $this->_db->quote($id) . ";";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        return $ret;
    }


    function deleteFromQueue($id)
    {
        $query = "DELETE FROM `#__gpo_awaiting_published` WHERE `ext_table`='q' AND `ext_id`=" . $this->_db->quote($id);
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
    }


    /*
      * Delete the published news record including its references in the locations section
      */
    function deletePublishedById($id)
    {
        $query = "DELETE `qlu`.* FROM `#__gpo_quotes_locations_unpublished` as `qlu` INNER JOIN `#__gpo_quotes_unpublished` as `qu` ON `qu`.`id`=`qlu`.`ext_id` WHERE `qu`.`live_id`='" . (int)$id . "';";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            die('Fatal error: problem deleting Published item:qlu');
        }
        //Delete any unpublished items from the awaiting publish list
        $query = "DELETE `ap`.* FROM `#__gpo_awaiting_published` as `ap` INNER JOIN `#__gpo_quotes_unpublished` as `qu` ON `qu`.`id`=`ap`.`ext_id` WHERE `qu`.`live_id`='" . (int)$id . "' AND `ap`.`ext_table`='q';";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            die('Fatal error: problem deleting Published item:ap');
        }

        $query = "DELETE `qu`.* FROM `#__gpo_quotes_unpublished` as `qu` WHERE `qu`.`live_id`='" . (int)$id . "';";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            die('Fatal error: problem deleting Published item:nu');
        }

        $query = "DELETE `ql`.* FROM `#__gpo_quotes_locations` as `ql` INNER JOIN `#__gpo_quotes` as `q` ON `q`.`id`=`ql`.`ext_id` WHERE `q`.`id`='" . (int)$id . "';";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            die('Fatal error: problem deleting Published item:nl');
        }

        $query = "DELETE `q`.* FROM `#__gpo_quotes` as `q` WHERE `q`.`id`='" . (int)$id . "';";
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();
        if ($ret !== true) {
            die('Fatal error: problem deleting Published item:n');
        }
        return true;
    }


    function canPublish($oItem)
    {
        $response = new stdClass();
        $response->pass = false;
        $response->msg = 'This is not ready for publishing';
        $response->link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=edit&id=' . $oItem->id, false);

        $live_id = (isset($oItem->live_id)) ? $oItem->live_id : null;
        $unpublished_id = $oItem->id;

        $clean = array();
        $fields = $this->fields(true);
        $fields->locations = "";
        foreach ($fields as $key)
        {
            $clean[$key] = (isset($oItem->$key)) ? $oItem->$key : "";
        }
        foreach ($clean as $key => $value)
        {
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
        $rules = array(
            "id" => array("required" => true),
            "published" => array('required' => true, 'currentdate' => true),
            "title" => array("required" => true),
//					"source"=>array("required"=>true),
            "keywords" => array("required" => true),
            "content" => array("required" => true),
//					"websource"=>array("required"=>true),
            "locations" => array("required" => true),
            "author" => array("required" => true)
        );
        $messages = array(
            "id" => array(
                "id" => "quotes_id",
                "message" => "id should not be empty"
            ),
            "published" => array(
                "id" => "quotes_published",
                "message" => "Published needs to be todays date or a past date"
            ),
            "title" => array(
                "id" => "quotes_title",
                "message" => "title should not be empty"
            ),
            "source" => array(
                "id" => "quotes_source",
                "message" => "source should not be empty"
            ),
            "keywords" => array(
                "id" => "quotes_keywords",
                "message" => "keywords should not be empty"
            ),
            "content" => array(
                "id" => "quotes_content",
                "message" => "content should not be empty"
            ),
            "websource" => array(
                "id" => "quotes_websource",
                "message" => "websource should not be empty"
            ),
            "locations" => array(
                "id" => "quotes_locations_label",
                "message" => "At least 1 location is required"
            ),
            "author" => array(
                "id" => "quotes_author",
                "message" => "Author should not be empty"
            ),
            "page" => array(
                "id" => "quotes_page",
                "message" => "Page number(s) must be prefixed. See pop-up help tip for this field"
            )
        );
        if ($message === false) {
            if (!empty($field)) {
                return (isset($rules[$field])) ? $rules[$field] : false;
            }
            return false;
        } else if ($message === true) {
            if (isset($messages[$field])) {
                return $messages[$field];
            }
            return false;
        }
    }


    function rule($field, $value)
    {
        $pass = true;

        $rule = $this->getRule($field);
        if ($rule !== false) {
            if ($pass === true && isset($rule['required']) && $rule['required'] === true && required($value) !== true) {
                $pass = false;
            }
            if ($pass === true && isset($rule['currentdate']) && $rule['currentdate'] === true && currentDate($value) !== true) {
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

    function save($input, $boolean_answer = false)
    {
        $ignore_id = false;
        $clean = array();
        $blankEditObject = $this->fields(false);

        $data = (object)array_intersect_key((array)$input, (array)$blankEditObject);
        $data->locations = $input['locations'];
        $data->staff = $input['staff'];
        if (isset($data->id) && $data->id === '0') {
            $ignore_id = true;
        }
        //check rules
        $return = array();
        //$data = (object)JRequest::_stripSlashesRecursive((array)$data);
        $data = (object) $this->stripslashes_recursive((array) $data);
        
        //		ftp_debug( $data, 'data before', true, false );
        $allow_breaks = array('notes', 'content');
        foreach ($data as $key => $value)
        {
            if ($key !== 'locations') {
                $value = str_replace('<', '&lt;', $value);
                $value = str_replace('>', '&gt;', $value);
                
                $value = GpoCleanInput($value, 'qutoes');
                if (!in_array($key, $allow_breaks)) {
                    $value = GpoSingleLine($value);
                }
                $data->$key = $value;
            }
        }
        //  ftp_debug( $data, 'data after');
        foreach ($data as $key => $value)
        {
            if ($key !== 'id'){
                $r = $this->rule($key, $value);
                if ($r !== true) {
                    $return[] = $r;
                }
            }
            else {
                if ($ignore_id !== true) {
                    $r = $this->rule($key, $value);
                    if ($r !== true) {
                        $return[] = $r;
                    }
                }
            }
        }

        //page prefix
        if (!empty($data->page)) {
            if (strlen($data->page) < 2) {
                $return[] = $this->getRule('page', true);
            } else if (substr($data->page, 0, 1) !== 'p') {
                $return[] = $this->getRule('page', true);
            }
        }

        if (count($return) > 0) {
            if ($boolean_answer) {
                return false;
            }
            
            //isClonedFromNews??
            ### Will be needed when called from news edit page to clone a news as quotes ####
            if($input['clonedFrom'] == "NewCloneFromNews") {
               foreach( $return as $key => &$val ) {
                   if( $val['id'] == 'quotes_author' ) {
                       $val['id'] = 'news_byline';
                       $val['message'] = "Byline can not be empty while cloning as Quotes. News Byline info will be used as author in Quotes";
                   } else {
                       $val['id'] = str_replace('quotes_', 'news_', $val['id']);
                   }
               }
               unset($val);
            }
            ### Will be needed when called from news edit page to clone a news as quotes ####
            
            $js  = "<script>";
            $js .= <<<EOJS
$('adminForm').select( '.error_warning').invoke('removeClassName','error_warning');
$('message_box').update('');
EOJS;
            $js .= json_encode($return);
            $js .= <<<EOJS
.each(function(obj){
 	$( obj.id).up(0).select('label').invoke('addClassName','error_warning');
	var oA =new Element('a')
			.writeAttribute('href','#' + obj.id)
			.update( obj.message.capitalize() );
	$( 'message_box').insert({bottom:oA});
});
window.scrollTo(0,0);

EOJS;
            $js .= "</script>";
            return $js;
        }

        if (!empty($data->published)) {
            $data->published = date('Y-m-d H:i:s', strtotime($data->published));
        } else {
            $data->published = date('Y-m-d H:i:s');
        }

        if (!empty($data->locations)) {
            //this confirms that the locations are all in the system still...
            $sql_part = array();
            $sql_order = array();
            foreach ($data->locations as $location)
            {
                $sql_part[] = "`name`=" . $this->_db->quote($location);
                $sql_order[] = $this->_db->quote($location); 
            }

            $query = "SELECT `id` FROM `#__gpo_location` WHERE " . implode(" OR ", $sql_part);
            $query .= " ORDER BY FIELD(`name`, " . implode(",", $sql_order) . ");";
            //			ftp_debug( $query, 'query' );
            $this->_db->setQuery($query);
            $oLocations = $this->_db->loadColumn();
        } else {
            $oLocations = false;
        }
        //unset or else it will breack insert/update as locations is in a different table
        unset($data->locations);


        //save - data
        $tbl_name = $this->make_table_name(false, false);
        $unix_timestamp = (isset($_SERVER['REQUEST_TIME'])) ? $_SERVER['REQUEST_TIME'] : date('U');
        $data->modified = date('Y-m-d H:i:s', $unix_timestamp);

        if ($data->id === '0') {
            $data->entered = date('Y-m-d H:i:s', $unix_timestamp);
            //			ftp_debug( $data, 'before sql insert' );
            $ret = $this->_db->insertObject($tbl_name, $data, 'id');
        } else {
            $ret = $this->_db->updateObject($tbl_name, $data, 'id', true);
        }
        //echo $this->_db->getErrorMsg();
        if (!$ret) {
            if ($boolean_answer) {
                return false;
            }
            return 'An error occured whilst saving';
        }
        //save - locations
        $unpublished_id = $data->id;
        $this->new_id = $data->id;

        $query = "DELETE FROM `#__gpo_quotes_locations_unpublished` WHERE `ext_id`=" . $this->_db->quote($unpublished_id);
        $this->_db->setQuery($query);
        $ret = $this->_db->execute();


        if (count($oLocations) > 0) {
            //this shouldnt be ignore as we should have just deleted all the records
            $query = "INSERT INTO `#__gpo_quotes_locations_unpublished` ( `id`,`location_id`,`ext_id` ) VALUES ";
            $sql_parts = array();
            foreach ($oLocations as $location)
            {
                $sql_parts[] = "(NULL," . $this->_db->quote($location) . "," . $this->_db->quote($unpublished_id) . ")";
            }
            $query .= implode(",", $sql_parts);
            $query .= ";";
            $this->_db->setQuery($query);
            $ret = $this->_db->execute();
        }

        if ($boolean_answer) {
            return true;
        }
        
        if (isset($_POST['clonedFromNews']) && $_POST['clonedFromNews'] == 1 && $_POST['new_record'] == 1) {
            $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=edit&id='.$unpublished_id, false);
            $msg  = '<h4 class="alert-heading">Message</h4>' . 'Done! News Saved!! <br /> <a style="font-weight:bold;" target="_blank" href="' . $link . 
                    '" title="Edit newly Cloned Quotes record">Click on this link to Edit the newly Cloned Quotes Record (will open in a new window)</a>';
            $js_include = <<<JS
$("message_box").addClassName('alert');
$("message_box").addClassName('alert-success');
$("message_box").update('$msg');
JS;
            $js = "<script type='text/javascript'>" . $js_include . "</script>";
            $response = array();
            $response['pass'] = true;
            $response['force'] = true;
            $response['js'] = $js;
        } else if (isset($_POST['new_record']) && $_POST['new_record'] === '1') {
            //			$link = JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=create', false );
            //			$js = "<script type='text/javascript'>window.location='" . $link . "'</script>";
            $js_include = <<<JS
$("message_box").update("");
$("adminForm").fire("adminFormQuotes:clone");
JS;
            $js = "<script type='text/javascript'>" . $js_include . "</script>";

            $response = array();
            $response['pass'] = true;
            $response['force'] = true;
            $response['js'] = $js;
        } else {
            $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=unpublished', false);
            $js = "<script type='text/javascript'>window.location='" . $link . "'</script>";
            $response = array();
            $response['pass'] = true;
            $response['force'] = false;
            $response['js'] = $js;
        }

        return $response;
    }


    /*
     * This will need expanding!
     */
    function delete($id)
    {
        $response = null;
        $aOptions = Joomla\CMS\Factory::getApplication()->getInput()->get('options', '0', 'POST', 'array');
        if (isset($aOptions['approve']) && (int)$aOptions['approve'] === (int)'1') {
            $ret = $this->deletePublishedById($id);
            if ($ret) {
                //				$mainframe->enqueueMessage( 'Quotes Item has been deleted.' );
                $response->msg = 'Quotes Item has been deleted.';
                echo 'Quotes Item has been deleted.';
            } else {
                //				$mainframe->enqueueMessage( 'Failed to delete News Item.' );
                $response->msg = 'Failed to delete News Item.';
                echo 'Failed to delete News Item.';
            }
        } else {
            //			$mainframe->enqueueMessage( 'Quotes Item has been deleted.' );
            $response->msg = 'Quotes Item has been deleted.';
            echo 'Quotes Item has been deleted.';
        }


        $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=published', false);
        $js = "<script>self.setInterval( function(){ window.location='" . $link . "';},1000 )</script>";
        return $js;
    }


    function emptyUnpublished()
    {
        $tbl_name = $this->make_table_name(false);
        $query = "TRUNCATE TABLE " . $tbl_name;
        $this->_db->setQuery($query);
        $this->_db->execute();

        $query = "DELETE FROM `#__gpo_quotes_locations_unpublished` `ext_type`=" . $this->_db->quote($this->type);
        $this->_db->setQuery($query);
        $this->_db->execute();

        $query = "DELETE FROM `#__gpo_awaiting_published` WHERE `ext_table`='q'";
        $this->_db->setQuery($query);
        $this->_db->execute();

        $response = new stdClass();
        $response->pass = false;
        $response->msg = 'Unpublished Quotes have been deleted';
        $response->link = 'index.php?option=com_gpo&controller=quotes&task=published';
        return $response;
    }


    function isBuildInProgress()
    {
        $filename_inprogress = 'inprogress.txt';
        exec("ls -l " . $this->path . " | awk '{print $9}'", $output);

        if (in_array($filename_inprogress, $output)) {
            return true;
        }
        return false;
    }


    function isReIndexInProgress()
    {
        $filename = $this->path . 'reindex_inprogress.txt';
        if (file_exists($filename)) {
            return true;
        }
        return false;
    }


//	rename shouldReIndexQuotesForSphinx to shouldReIndexForSphinx
    function shouldReIndexForSphinx()
    {
        $filename = $this->path . 'reindex.txt';
        if (file_exists($filename)) {
            return true;
        }
        return false;
    }


    function setReIndex()
    {
        $filename = $this->path . 'reindex.txt';
        touch($filename);
    }


    /**
     * Export the Quotes in Text
     * @param array $Ids
     * @return false|array
     */
    function exportQuotesToTxt(array $Ids)
    {
        if (empty($Ids)) {
            return false;
        }

        //check the fields in template
        $template = $this->getExportTemplate();
        $template = $template->txt_template;

        //find the fields in the template body
        $template_body = $template->body;
        //  ini_set('display_errors',1);
        preg_match_all('^{([a-zA-Z]+)\}^', $template_body, $matches);

        $template_fields = $matches[1];
        //var_dump($template_fields);

        $this->_db->setUTF();
        $tbl_name = $this->make_table_name(true);

        $this->_db->setQuery("SELECT * FROM " . $tbl_name . " WHERE `id` IN( " . implode(', ', $Ids) . ") LIMIT 10000");

        $this->_db->execute();
        $results = $this->_db->loadAssocList();


        $data = '';
        foreach ($results AS $result) {
            $body = $template_body;
            $locations = '';
            if (in_array('location', $template_fields)) {
                $locations = $this->getQuoteLocations($result['id']);
                $body = str_ireplace('{location}', implode(', ', $locations), $body);

            }
            $qcites = '';
            if (in_array('qcite', $template_fields)) {
                $qcites = $this->getQCite($result['id']);
                $body = str_ireplace('{qcite}', implode(', ', $qcites), $body);
            }

            //citation processing starts here

            /*
              Author fullstop >
              < Published {year only} fullstop  >
              < Title {in ‘single quotes’ with a fullstop inside the final apostrophe} >
              < Source {in italics, with no fullstop here} >
              < semi-colon space Volume >
              < space (Issue) { in round brackets} >
              < comma space Page > fullstop space < City colon space >
              < Publisher fullstop >
              < space Published {day and month only} fullstop >
            */

            if (in_array('citation', $template_fields)) {
                $parts = array();

                if (!empty($result['author'])) {
                    $str = GpoEndWith(".", $result['author']);
                    $parts['author'] = $str . ' ';
                }
                //< Published {year only} fullstop  >
                // < space Published {day and month only} fullstop >
                if (!empty($result['published'])) {
                    $str = date('Y', strtotime($result['published'])) . '. ';
                    $parts['published_year'] = $str;

                    $str = ' ' . date('j F', strtotime($result['published'])) . '. ';
                    $parts['published_daymonth'] = $str;
                }
                
                //modified date format
//                if (!empty($result['modified'])) {
//                    $str = date('d M Y', strtotime($result['modified'])) . '. ';
//                    $parts['modified'] = $str;
//                }
            
                //< Title {in ‘single quotes’ with a fullstop inside the final apostrophe} >
                if (!empty($result['title'])) {
                    $str = GpoEndWith(".", $result['title']);
                    $parts['title'] = $str . ' ';
                }
                //< Source {in italics, with no fullstop here} >
                if (!empty($result['source'])) {
                    $str = trim($result['source']);
                    if (substr($str, -1) === '.') {
                        $str = substr($str, 0, strlen($str) - 1);
                    }
                    $parts['source'] = $str;
                }
                //< semi-colon space Volume >
                if (!empty($result['volume'])) {
                    $str = "; " . $result['volume'];
                    $parts['volume'] = $str;
                }
                //< space (Issue) { in round brackets} >
                if (!empty($result['issue'])) {
                    $parts['issue'] = ' (' . $result['issue'] . ')';
                }
                //< comma space Page >
                if (!empty($result['page'])) {
                    $str = ', ' . $result['page'];
                    $parts['page'] = $str;
                }
                //< City colon space >
                if (!empty($result['city'])) {
                    $str = trim($result['city']) . ': ';
                    $parts['city'] = $str;
                }
                //< Publisher fullstop >
                if (!empty($result['publisher'])) {
                    $str = GpoEndWith(".", $result['publisher']);
                    $parts['publisher'] = $str;
                }

                $html = $parts['author'];
                $html .= $parts['published_year'];
                $html .= $parts['title'];
                $html .= $parts['source'];
                $html .= $parts['volume'];
                $html .= $parts['issue'];
                ////< comma space Page > fullstop space < City colon space >
                $html .= $parts['page'] . '. ' . $parts['city'];
                $html .= $parts['publisher'];
                $html .= $parts['published_daymonth'];
                //$html .= $parts['modified'];

                $body = str_ireplace('{citation}', $html, $body);
                //citation ends here
            }

            if (!empty($result['title'])) {
                $body = str_ireplace('{title}', $result['title'], $body);
            }

            if (!empty($result['source'])) {
                $body = str_ireplace('{source}', $result['source'], $body);
            }

            if (!empty($result['published'])) {
                $body = str_ireplace('{published}', date('j F Y', strtotime($result['published'])), $body);
            }
            
            if (!empty($result['modified'])) {
                $body = str_ireplace('{modified}', date('j F Y', strtotime($result['modified'])), $body);
            }

            if (!empty($result['author'])) {
                $body = str_ireplace('{author}', $result['author'], $body);
            }
            if (!empty($result['content'])) {
                $content =  $result['content'];

                ## Disable extra lines in content and citations section
                //$content = preg_replace("/(*ANYCRLF)$/m", "\r\n", $content);

                $body = str_ireplace('{content}', $content, $body);
            }

            if (!empty($result['websource'])) {
                $body = str_ireplace('{websource}', $result['websource'], $body);
            }


            if (!empty($result['keywords'])) {
                $body = str_ireplace('{keywords}', $result['keywords'], $body);
            }

            if (!empty($result['poaim'])) {
                $body = str_ireplace('{poaim}', $result['poaim'], $body);
            }

            if (!empty($result['sourcedoc'])) {
                $body = str_ireplace('{sourcedoc}', $result['sourcedoc'], $body);
            }

            if (!empty($result['notes'])) {
                $body = str_ireplace('{notes}', $result['notes'], $body);
            }

            if (!empty($result['staff'])) {
                $body = str_ireplace('{staff}', $result['staff'], $body);
            }
            
            //page, publisher, city fields 
            if (!empty($result['page'])) {
                $body = str_ireplace('{page}', $result['page'], $body);
            }
            
            if (!empty($result['publisher'])) {
                $body = str_ireplace('{publisher}', $result['publisher'], $body);
            }
            
            if (!empty($result['city'])) {
                $body = str_ireplace('{city}', $result['city'], $body);
            }

            $body = str_ireplace('{id}', $result['id'], $body);


            $data .= $body . "\r\n \r\n";

        }

        //remove all template tags
        $data = preg_replace('^{([a-zA-Z]+)\}^', '', $data);


        //get the header and footer
        if( !empty($template->header) ){
           $data = $template->header . "\r\n \r\n" . $data;
        }else {
           $data = "\r\n" . $data;
        }
        
        if( !empty($template->footer) ){
           $data .= "\r\n \r\n" . $template->footer;
        }
        
        //$data = trim($data, "\r");

        //$data = preg_replace('/(*ANYCRLF)\.$/m', "\r\n", $data);
        //$data = preg_replace("/(*ANYCRLF)$/m", "\r\n", $data);
        //echo $data;
        //die();
        return $data;
    }


    function cleanData(&$str)
    {
        /*  if ($str == 't') $str = 'TRUE';
         if ($str == 'f') $str = 'FALSE';
         if (preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
             $str = "'$str";
         }
         //if (strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
        */
        $str = $this->convert_encoding($str);
    }

    /**
     * Export the Quotes in CSV
     * @param array $Ids
     * @return false|array
     */
    function exportQuotesToCsv(array $Ids, array $ExportFields)
    {
        if (empty($Ids) OR empty($ExportFields)) {
            return false;
        }

        //remove duplicate fields
        $ExportFields = array_unique($ExportFields);


        $this->_db->setUTF();
        $this->_db->setQuery("SELECT * FROM #__gpo_quotes WHERE `id` IN (" . implode(',', $Ids) . ")");
        $results = $this->_db->loadAssocList();
        $data = array();
        //$flippedExportFields = array_flip($ExportFields);
        //var_dump($results);
        foreach ($results AS $result) {

            //get the location
            if (in_array('location', $ExportFields)) {
                $result['location'] = implode(', ', $this->getLocations($result['id'], true));
            }
            if (in_array('qcite', $ExportFields)) {

                $result['qcite'] = implode(', ', $this->getQCite($result['id']));
            }

            //replace published and modified date format
            $result['published'] = date('d M Y', strtotime($result['published']));
            $result['modified'] = date('d M Y', strtotime($result['modified']));


            //sort in the same way
            $commonKeysInOrder = array_intersect_key(array_flip($ExportFields), $result);
            $commonKeysWithValue = array_intersect_key($result, $commonKeysInOrder);
            $data[] = array_merge($commonKeysInOrder, $commonKeysWithValue);
        }

        //open a temporary file
        //$filename = '/tmp/quotes_export_' . time() . '.csv';

        ob_start();
        $f = fopen("php://output", "w");

        $header = array();
        foreach ($ExportFields AS $field) {
            $header[] = $this->getCSVFieldLabel($field);
        }

        //write the header column
        fputcsv($f, $header);


        //write the data
        foreach ($data AS $row) {
            array_walk($row, array('GpoModelQuotes', 'cleanData'));
            //var_dump($row);
            fputcsv($f, array_values($row));
        }
        fclose($f);

        $csv = ob_get_contents();
        ob_end_clean();


        return $csv;
    }

    function exportQuotesToCsv2(array $Ids, array $ExportFields)
    {
        if (empty($Ids) OR empty($ExportFields)) {
            return false;
        }

        //remove duplicate fields
        $ExportFields = array_unique($ExportFields);


        $this->_db->setUTF();
        $this->_db->setQuery("SELECT * FROM #__gpo_quotes WHERE `id` IN (" . implode(',', $Ids) . ")");
        $results = $this->_db->loadAssocList();
        //var_dump($results);

        $data = array();
        //$flippedExportFields = array_flip($ExportFields);
        foreach ($results AS $result) {
            //var_dump($ExportFields, $result);
            //get the location
            if (in_array('location', $ExportFields)) {
                $result['location'] = implode(', ', $this->getLocations($result['id'], true));
            }
            if (in_array('qcite', $ExportFields)) {
                $result['qcite'] = implode(', ', $this->getQCite($result['id']));
            }
            
            //replace published and modified date format
            $result['published'] = date('d M Y', strtotime($result['published']));
            $result['modified'] = date('d M Y', strtotime($result['modified']));


            //sort in the same way
            $commonKeysInOrder = array_intersect_key(array_flip($ExportFields), $result);
            $commonKeysWithValue = array_intersect_key($result, $commonKeysInOrder);
            $data[] = array_merge($commonKeysInOrder, $commonKeysWithValue);
        }


        $html = '<tr>';

        foreach ($ExportFields AS $field) {
            $html .= '<th>' . $this->getCSVFieldLabel($field) . '</th>';
        }
        $html .= '</tr>';


        foreach ($data AS $row) {
            $html .= '<tr>';
            foreach ($row AS $cell_data) {
                $cell_data = nl2br($cell_data);
                $html .= '<td>' . $cell_data . '</td>';

            }
            $html .= '</tr>';
        }
        $html = '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /></head><body>
<table border="1">' . $html . '</table></body></html>';
        //$html = '<style><!-- br{mso-data-placement:same-cell;} --></style>'.$html;
        return $html;
    }

    function convert_encoding($string)
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($string, 'UTF-16LE', 'auto');
        }
    }

    /**
     * Retrieves the location information for a quote
     * @param $id ID of the Quote
     * @return array array of locations
     */
    function getQuoteLocations($id)
    {
        $this->_db->setQuery("SELECT gl.name  FROM  `#__gpo_location` AS gl LEFT JOIN `#__gpo_quotes_locations` AS gql ON gql.location_id=gl.id  WHERE gql.ext_id=" . $this->_db->Quote($id));
        return $this->_db->loadColumn();
    }

    /**
     * Get the QCitation's ID for a Quote
     * @param $id Quote ID
     * @return mixed
     */
    function getQCite($id)
    {
        $this->_db->setQuery("SELECT `citation_id`  FROM  `#__gpo_citation_relation` WHERE type='quotes' and type_id=" . $this->_db->Quote($id));
        return $this->_db->loadColumn();
    }

    public function getCSVExportFields()
    {
        return array(
            'id', 'published', 'title', 'source', 'publisher', 'volume', 'issue', '', 'city', 'author', 'affiliation', 'keywords', 'content', 'websource', 'entered', 'modified', 'notes', 'sourcedoc', 'staff', 'poaim', 'location', 'qcite',
        );
    }

    public function getCSVFieldLabel($field)
    {
        {
            $fields = array(
                'id' => 'Quote ID', 'published' => 'Date Published', 'title' => 'Title', 'source' => 'Source', 'publisher' => 'Publisher', 'volume' => 'Volume', 'issue' => 'Issue', 'page' => 'Page', 'city' => 'City', 'author' => 'Author', 'affiliation' => 'Affiliation', 'keywords' => 'Keywords', 'content' => 'Content', 'websource' => 'WebSource', 'entered' => 'Entered', 'modified' => 'Date Modified', 'notes' => 'Notes', 'sourcedoc' => 'SourceDoc', 'staff' => 'Staff', 'poaim' => 'PoAIM', 'qcite' => 'QCite ID', 'location' => 'Location',);
            if (key_exists($field, $fields)) {
                return $fields[$field];
            } else {
                return false;
            }
        }
    }

    /**
     * Converts Array to CSV
     * @param array $array The array to be converted
     * @param string $delimiter CSV Field delimeter
     * @param string $enclosure Field enclosur Like (")
     * @param string $terminator Line terminator
     * @author http://www.php.net/manual/en/function.str-getcsv.php#88353
     * @return string Returns the CSV
     */
    function str_putcsv($array, $delimiter = ',', $enclosure = '"', $terminator = "\n")
    {
        //var_dump($array);
        # First convert associative array to numeric indexed array
        foreach ($array as $key => $value) $workArray[] = $value;

        $returnString = ''; # Initialize return string
        $arraySize = count($workArray); # Get size of array

        for ($i = 0; $i < $arraySize; $i++) {
            # Nested array, process nest item
            if (is_array($workArray[$i])) {
                $returnString .= $this->str_putcsv($workArray[$i], $delimiter, $enclosure, $terminator);
            } else {
                switch (gettype($workArray[$i])) {
                    # Manually set some strings
                    case "NULL":
                        $_spFormat = '';
                        break;
                    case "boolean":
                        $_spFormat = ($workArray[$i] == true) ? 'true' : 'false';
                        break;
                    # Make sure sprintf has a good datatype to work with
                    case "integer":
                        $_spFormat = '%i';
                        break;
                    case "double":
                        $_spFormat = '%0.2f';
                        break;
                    case "string":
                        $_spFormat = '%s';
                        break;
                    # Unknown or invalid items for a csv - note: the datatype of array is already handled above, assuming the data is nested
                    case "object":
                    case "resource":
                    default:
                        $_spFormat = '';
                        break;
                }

                //check if the string already contains the delimeter, if so, escapes it
                $escape_char = "\\";
                //if(strpos($workArray[$i], $enclosure)){
                //$workArray[$i] = str_replace($enclosure, $escape_char.$enclosure, $workArray[$i]);
                //$workArray[$i] = htmlentities($workArray[$i]);
                // }

                //clear line ending signs
                $workArray[$i] = str_replace(array("\n", "\r", '"'), array('', '', '""'), $workArray[$i]);
                $returnString .= sprintf('%2$s' . $_spFormat . '%2$s', $workArray[$i], $enclosure);
                $returnString .= ($i < ($arraySize - 1)) ? $delimiter : $terminator;
            }
        }
        # Done the workload, return the output information
        return $returnString;
    }


    function updateCsvTemplate($template)
    {
        $this->_db->setQuery("UPDATE #__gpo_configurations SET `conf_value`=" . $this->_db->Quote($template) . " WHERE `conf_name`='quotes_export_csv_template'");

        return $this->_db->execute();
    }

    function updateTxtTemplate($template)
    {
        $this->_db->setQuery("UPDATE #__gpo_configurations SET `conf_value`=" . $this->_db->Quote(json_encode($template)) . " WHERE `conf_name`='quotes_export_txt_template'");

        return $this->_db->execute();
    }


    function getExportTemplate()
    {
        $this->_db->setQuery("SELECT * FROM `#__gpo_configurations` WHERE `conf_name` IN ('quotes_export_csv_template','quotes_export_txt_template')");
        $result = $this->_db->loadObjectList();
        //var_dump($result);
        return (object)array('csv_template' => trim($result[0]->conf_value), 'txt_template' => json_decode(trim($result[1]->conf_value)));
    }


    function quotesSearch($params)
    {

        if (!empty($params['locations'])) {
            $locations = explode(",", trim($params['locations']));

            $location_ids = array();

            foreach ($locations as $k => $lo)
            {
                $query = "SELECT `l`.`id`, GROUP_CONCAT( `ld`.`link_id`, CHAR(11) SEPARATOR ', ' ) as `ids` FROM `#__gpo_location` as `l` LEFT JOIN  `#__gpo_location_links_deep` as `ld` ON `l`.`id` = `ld`.`location_id` WHERE `l`.`name` = " . $this->_db->quote($lo) . " GROUP BY `l`.`id`;";

                $this->_db->setQuery($query);
                $o = $this->_db->loadObject();
                if (!empty($o->id)) {
                    $location_ids[] = trim($o->id);
                }
                if (!empty($o->ids)) {
                    $a = explode(",", $o->ids);
                    foreach ($a as $id)
                    {
                        $location_ids[] = trim($id);
                    }
                }
            }

            $location_ids = array_unique($location_ids);
            $params['location_ids'] = implode(",", $location_ids);
            unset($params['locations']);
        }
        //var_dump($location_ids);
        //die();
        $where = array();
        foreach ($params AS $key => $value) {
            //var_dump($key,$value);

            switch ($key) {
                case 'location_ids':
                    if (!empty($location_ids)) {
                        $where[] = "l.id IN (" . $params['location_ids'] . ")";
                    }
                    break;


                case 'id_range':
                    if (!empty($value['from'])) {
                        $where[] = $this->_db->quoteName('q.id') . ' >= ' . $value['from'];
                    }
                    if (!empty($value['to'])) {
                        $where[] = $this->_db->quoteName('q.id') . ' <= ' . $value['to'];
                    }
                    break;


                case 'published_range':
                    if (!empty($value['from'])) {
                        $pr_from = strtotime($value['from']);
                        $where[] = $this->_db->quoteName('q.published') . ' >= ' . $this->_db->Quote(date('Y-m-d', $pr_from));
                        //$pr_to = strtotime($value['published_range']['to']);
                    }
                    if (!empty($value['to'])) {
                        $pr_to = strtotime($value['to']);
                        $where[] = $this->_db->quoteName('q.published') . ' <= ' . $this->_db->Quote(date('Y-m-d', $pr_to));
                    }
                    break;


                case 'poaim':
                    if (!empty($value)) {
                        $where[] = $this->_db->quoteName('q.poaim') . " LIKE " . $this->_db->Quote('%' . $value . '%');
                    } else {
                        $where[] = $this->_db->quoteName('q.poaim') . " != ''";
                    }
                    break;


                case 'keywords':
                    if (!empty($value)) {

                        $keywords = explode(',', $value);
                        $subwhere = array();
                        foreach ($keywords AS $keyword) {
                            $subwhere[] = $this->_db->quoteName('q.keywords') . " LIKE " . $this->_db->Quote('%' . $keyword . '%');
                        }
                        $subwhere = implode(' OR ', $subwhere);
                        $where[] = "($subwhere)";

                    }
                    break;

                case 'share':
                    if ($value == 1) {
                        $where[] = $this->_db->quoteName('q.' . $key) . ' = ' . $this->_db->Quote($value);
                    }
                    break;

                default:
                    if (!empty($value)) {
                        $where[] = $this->_db->quoteName('q.' . $key) . ' LIKE ' . $this->_db->Quote('%' . $value . '%');
                    }
                    break;
            }
        }

        $where = trim(implode(' AND ', $where), ', ');
        if (!$where) return false;
        //var_dump($where);
        /*
                if(!empty($location_ids)){
                    $this->_db->setQuery("SELECT `q`.`id` FROM `#__gpo_quotes` as `q` LEFT JOIN `#__gpo_quotes_locations` as `ql` ON `q`.`id`=`ql`.`ext_id` LEFT JOIN `#__gpo_location` as `l` ON `l`.`id` = `ql`.`location_id` WHERE $where");
                } else {
                    $this->_db->setQuery("SELECT `q`.`id` FROM `#__gpo_quotes` as `q`  WHERE $where");

                }
                echo($this->_db->getQuery());
        /        $ids = $this->_db->loadColumn();
        */

        $query = "SELECT SQL_CALC_FOUND_ROWS `q`.*, DATE_FORMAT(`q`.`published`, '%Y%m%d' ) as `published_hash`,GROUP_CONCAT( `l`.`name` ) as locations FROM `#__gpo_quotes` as `q` LEFT JOIN `#__gpo_quotes_locations` as `ql` ON `q`.`id`=`ql`.`ext_id` LEFT JOIN `#__gpo_location` as `l` ON `l`.`id` = `ql`.`location_id` WHERE {$where} GROUP BY q.id ORDER BY ". $this->_db->quoteName(!empty( $this->filter_order) ? $this->filter_order : 'id')." ". (!empty($this->filter_order_Dir) ? $this->filter_order_Dir : 'desc')." LIMIT {$this->limitstart}, " . (($this->limit) ? $this->limit : 99999);
        $this->_db->setQuery($query);

        //echo ($this->_db->getQuery());

        $results = $this->_db->loadAssocList();
        if ($results) {
            //get num rows
            $this->_db->setQuery("SELECT FOUND_ROWS();");
            $this->total = $this->_db->loadResult();
            //var_dump($results);
            $this->pagination = new JPagination($this->total, $this->limitstart, $this->limit);
            return $results;
        }
        return false;


    }
}