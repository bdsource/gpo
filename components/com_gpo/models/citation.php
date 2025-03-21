<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class GpoModelCitation extends JModelLegacy
{
	var $id = null;
	var $is_member = false;
	var $type='';
	
	
	function __construct()
	{
		parent::__construct();
	}



	function isType( $type )
	{
		$allowed = "news,quotes";
		$a = explode( ",",$allowed );
		return in_array( $type,$a );
	}
	
	
	
	function getById( $id )
	{
		$tbl_name = $this->make_table_name( $type );
		$query = "SELECT `c`.* FROM " . $tbl_name ." as `c` WHERE `c`.`id` = " . $this->_db->quote( $id );
		if( $this->is_member !== true )
		{
			$query .= " AND `c`.`share`='1'";
		}
		$query .=" LIMIT 0,1";
		$this->_db->setQuery( $query );
		$results = $this->_db->loadObject();
//		$results->locations;
		return $results;
	}



	function getLocations( $id )
	{
		$query = "SELECT `lo`.`name` FROM `#__gpo_location` as `lo` LEFT JOIN `#__gpo_citations_locations` as `loc` ON `lo`.`id`=`loc`.`location_id` WHERE `loc`.`ext_id`=" . $this->_db->quote( $id ) . " AND `loc`.`ext_type`=" . $this->_db->quote( $this->type );
		$this->_db->setQuery( $query );
		$locations = $this->_db->loadColumn();
		return $locations;
	}
	
	
	
	/*
	 * creates the citation table name
	 */
	function make_table_name( $type, $quotes=true )
	{
		if( $quotes )
		{
			$quote = "`";
		}else{
			$quote = "";
		}
		return $quote . '#__gpo_citations_' . $this->type . $quote;
	}

        function getByChar($char){
            $tbl_name = $this->make_table_name( $this->type );
            $query = "SELECT * FROM " . $tbl_name ." as `c` WHERE `c`.`title`  LIKE " . $this->_db->quote( strtoupper($char).'%' );
                
            if( $this->is_member !== true )
            {
                    $query .= " AND `c`.`share`='1'";
            }
            if($this->type=='news'){
                $query .=" ORDER BY `byline`, `source`, YEAR(`published`), `title` LIMIT 1000";
            } else {
                $query .=" ORDER BY `author`, `source`, YEAR(`published`), `title` LIMIT 1000";
            }
            $this->_db->setQuery( $query );
            $results = $this->_db->loadObjectList();
            return $results;
        }
        
}
?>