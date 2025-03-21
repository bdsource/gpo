<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class GpoModelNews extends JModelLegacy
{
	var $id = null;

	function __construct()
	{
		parent::__construct();
	}



	/*
	 * get the unpublished item
	 */	
	function getById( $id, $share='1' )
	{
		if( (int)$id === (int)'0' )
		{
			return false;
		}
		
		$query = "SELECT * ";
		$query .= "FROM `#__gpo_news` ";
		$query .= "WHERE `id`= " . $this->_db->quote( $id );
		if( $share === '1' )
		{
			$query .= " AND `share`='1'";
		}
		$query .= "LIMIT 0,1";
		
		
		$this->_db->setQuery( $query );
		$data = $this->_db->loadObject();
		
		if( empty( $data->id ) )
		{
			return false;
		}
		$data->locations = $this->getLocations( $data->id );
//maybe move this elsewhere helper?
		return $data;
	}
	
	
	
	function getLocations( $id )
	{
		$query = "SELECT `lo`.`name` FROM `#__gpo_location` as `lo` LEFT JOIN `#__gpo_news_locations` as `lon` ON `lo`.`id`=`lon`.`location_id` WHERE `lon`.`ext_id`=" . $this->_db->quote( $id );		
		$this->_db->setQuery( $query );
		$locations = $this->_db->loadColumn();
		return $locations;
	}
	
	/*
	 * return array or text for html
	 */
	function getArchiveByYear( $year )
	{
//Look for cache		
		$model =& $this->getModel( 'Sphinxsearch' );
/*
//Get all years
//Gets months in the year		
SELECT DISTINCT (
DATE_FORMAT( `published` , '%Y/%m/' )
)
FROM `jos_gpo_citations_quotes`
WHERE YEAR( `published` ) = '2009'
*/
	}
	
	
	function getYears($year=NULL, $share=0)
	{
        $whereClause = ($share == 1) ? ' WHERE `share`=1 ' : '';
        
		$query = 'SELECT DISTINCT ( DATE_FORMAT( `published` , "%Y" ) ) FROM `#__gpo_news` ' .
                 $whereClause . ' ORDER BY `published` DESC;';
        
		$this->_db->setQuery( $query );
		$items = $this->_db->loadColumn();
		return $items;
	}
	
	
	function getMonthsByYear( $year, $share=0 )
	{
        $whereClause = ($share == 1) ? ' AND `share`=1 ' : '';
        
		$query = '
                 SELECT 
                       DISTINCT ( DATE_FORMAT( `published` , "%Y/%m" ) ) as `date`, DATE_FORMAT( `published` , "%M" ) as `name`  
                 FROM 
                       `#__gpo_news`
                 WHERE 
                       YEAR( `published` ) = ' . $this->_db->quote( (int)$year ) 
                 . $whereClause;
        
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		return $items;
	}
	
	
	function getDaysByMonth( $year, $month, $share = 0 )
	{
        $whereClause = ($share == 1) ? ' AND `share`=1 ' : '';
        
		$query = '
                 SELECT 
                       DISTINCT ( DATE_FORMAT( `published` , "%Y/%m/%d" ) ) as `date`, DATE_FORMAT( `published` , "%e %W" ) as `name`  
                 FROM 
                       `#__gpo_news`
                 WHERE 
                       YEAR( `published` ) = ' . $this->_db->quote( (int)$year ) .'
                 AND 
                       MONTH( `published` ) = ' . $this->_db->quote( (int)$month ) . 
                 $whereClause;
        
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		return $items;
	}
	
	
	function getArticlesOn( $year, $month, $day, $share=0 )
	{

        if($share==1){
            $where = ' AND `n`.share=1';
        } else {
            $where = '';
        }
		$query = '
                 SELECT `n`.*, DATE_FORMAT(`n`.`published`, "%Y%m%d" ) as `published_hash`,GROUP_CONCAT( `l`.`name` ) as locations 
                 FROM `#__gpo_news` as `n` 
                 LEFT JOIN `#__gpo_news_locations` as `nl` ON `n`.`id`=`nl`.`ext_id` 
                 LEFT JOIN `#__gpo_location` as `l` ON `l`.`id` = `nl`.`location_id` 
                 WHERE YEAR( `n`.`published` ) = ' . $this->_db->quote( (int)$year ) . '
                 AND MONTH( `n`.`published` ) = ' . $this->_db->quote( (int)$month ) . '
                 AND DAY( `n`.`published` ) = ' . $this->_db->quote( (int)$day ) .$where.'
                 GROUP BY `n`.`id`;';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		return $items;
	}
}
?>