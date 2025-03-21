<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');
/*
 * Before altering the database check gpo.php in helper as it is used in the function GpoGetHtmlForType( $type )
 */
class GpoModelLists extends JModelList
{
	function __construct()
	{
		parent::__construct();
	}
	
	
	function getCurrentOrder( $type )
	{
                $query = 'SELECT `data` FROM `#__gpo_lists_order` WHERE `type`= '. $this->_db->quote( $type ) . ';';	
		$this->_db->setQuery( $query );
		$data = $this->_db->loadResult();
		return $data;
	}
	
	
	
	function getListByType( $type )
	{
		$query = '
SELECT *
FROM `#__gpo_lists_data`
WHERE `type`= '. $this->_db->quote( $type ) . '
ORDER BY `value` ASC;
';	
		$this->_db->setQuery( $query );
		$data = $this->_db->loadObjectList();
		return $data;		
	}
	
	
	
	function getAllDataByField( $type )
	{
		if( empty( $type ) ) return false;
//'#__gpo_news', 		
		$tables = array( 
			'news' => '#__gpo_news', 
			'quotes' => '#__gpo_quotes',
			'ncite' => '#__gpo_citations_news',
			'qcite' => '#__gpo_citations_quotes'
		);
		
		$uniqueItems = array();
		$order = array();
		
		foreach( $tables as $k=>$tableName )
		{
			$query = 'SELECT DISTINCT( ' . $this->_db->quoteName( $type ) .' ) FROM `' . $tableName . '`;';
			$this->_db->setQuery( $query );
			$items = $this->_db->loadColumn();
                         
			if(!is_array( $items )) continue;
			foreach( $items as $item )
			
                            {
				$key = md5( $item );                            
				if( !isset( $uniqueItems[$key] ) )
				{
					$order[]=$item;
					$uniqueItems[$key] = array();
					$uniqueItems[$key]['item'] = $item;
					$uniqueItems[$key]['table'] = array();
				}
				
				
				if( !in_array( $tableName, $uniqueItems[$key]['table'] ) )
				{
					$uniqueItems[$key]['table'][]=$k;
				}
			}	
		}                
		sort( $order );
                
		$newOrder = array();
		foreach($order as $item)
		   {
			$key =  md5($item);			
			$newOrder[$key] = $uniqueItems[$key];
			unset($uniqueItems[$key] );
		}		
	    return $newOrder;	
	}
	
        
	function getNonEntries( $type )
	{
            
                //$allow = $this->getListByType( $type );
		$allow = array();
		foreach( $this->getListByType( $type ) as $item )
		{
			$allow[] = $item->value;
		}
                
		if( empty( $type ) ) return false;
                //'#__gpo_news', 		

		$tables = array( 
			 'news'  =>  '#__gpo_news', 
			'quotes' =>  '#__gpo_quotes',
			'ncite'  =>  '#__gpo_citations_news',
			'qcite'  =>  '#__gpo_citations_quotes'
		);
		
		$uniqueItems = array();
		$order = array();

                    $tempType = NULL;
					$counter =0;
                    foreach( $tables as $k=>$tableName )
                    {
						$tempType = $type;
						if($type == 'hashtags')
                        { 
							if($tableName == '#__gpo_news')
                            {
								$tempType = 'twitter_hashtag';								
                            }
                            else
                            {
								continue;	
                            }
                        }
						elseif($type == 'city' && $tableName == '#__gpo_citations_news'){
							
							continue;	
							
						}
						elseif(($type == 'keywords' && $tableName == '#__gpo_citations_news') || ($type == 'keywords' && $tableName == '#__gpo_citations_quotes')){
							
							continue;	
							
						}
						elseif(($type == 'category' && $tableName == '#__gpo_quotes') || ($type == 'category' && $tableName == '#__gpo_citations_quotes')){
							
							continue;	
							
						}
					

                        $query = 'SELECT DISTINCT( ' . $this->_db->quoteName( $tempType ) .' ) FROM `' . $tableName . '`;';
			$this->_db->setQuery( $query );
			//echo $this->_db->getQuery();
			//echo $query." -----<br>";
			
			//exit;
			$items = $this->_db->loadColumn();
                      
			
			if( !is_array( $items ) ) continue;
			
			foreach( $items as $item )
			{
				$item = trim( $item );
				if( empty( $item ) ) continue;
                                
				
                              $tempentries = explode(",",$item);
                              $entries = explode(" ",$tempentries[0]);
                           
                               foreach( $entries as $entry)
				    {                                  
					$entry = trim( $entry );
                                       
					                                       
                                        if(empty( $entry )) continue;                                       					
					if(in_array( $entry, $allow )|| in_array($entry.'*',$allow)) continue;
				      
					$key = md5( $entry );
                                      
					if( !isset( $uniqueItems[$key] ) )
					{
						$order[]=$entry;
						$uniqueItems[$key] = array();
						$uniqueItems[$key]['item'] = $entry;
						$uniqueItems[$key]['table'] = array();
					}
					
					if( !in_array( $k, $uniqueItems[$key]['table'] ) )
					 {
						$uniqueItems[$key]['table'][]=$k;
			             }				    
                                 }
                                
			}	
		}
		sort( $order );
		$newOrder = array();
              
		foreach( $order as $item )
		{
			$key = md5( $item );			
			$newOrder[$key] = $uniqueItems[$key];
			unset( $uniqueItems[$key] );
		}               
		return $newOrder;	
	}	
	
	
	function addListData( $type, $data )
	{
		$data = trim( $data );
		if( empty( $data ) ) return true;
		$query = 'INSERT IGNORE INTO `#__gpo_lists_data` VALUES (null,' . $this->_db->quote( $data ) . ',' . $this->_db->quote( $type ) . ' );';
		
		$this->_db->setQuery( $query );	
		$this->_db->execute();
		return true;
	}

	
	
	function deleteListData( $id )

	{		
                $query = 'DELETE FROM `#__gpo_lists_data` WHERE `id`= '. $this->_db->quote( $id ) . ';';

		$this->_db->setQuery( $query );	
		$r = $this->_db->execute();
		
                return $r;
	}

	
        
        function getListData($type){
             $query = "SELECT value FROM `#__gpo_lists_data` where type='".$type."'ORDER BY `value` ASC LIMIT 500";
             $this->_db->setQuery( $query );
         $this->_db->execute();
         $data = $this->_db->loadObjectList();                           
          return $data;             
        }
        
        
        function updateListOrder($type){
             $query = "SELECT value FROM `#__gpo_lists_data` where type='".$type."'ORDER BY `value` ASC LIMIT 500";
             $this->_db->setQuery( $query );
	     $this->_db->execute();
	     $order = $this->_db->loadObjectList();
                         
           
	     $json_order = json_encode( $order );				
				$r = deleteListOrder( $type );
				$r = addListOrder( $type, $json_order );

				$order = implode("\r\n", $order);
				//var_dump($type);
				GpoSaveTypeToCache( $type, $order );
               
        }
       
	
	
	function type_exists( $type )
	{
		$allow = array( 'source','hashtags','city','keywords','category' );
		return in_array( $type, $allow );
	}
	
	
	
	function addListOrder( $type, $data )
	{
		$query = 'INSERT INTO `#__gpo_lists_order` VALUES (null,' . $this->_db->quote( $type ) . ',' . $this->_db->quote( $data ) . ');';		
		$this->_db->setQuery( $query );	
		$this->_db->execute();
		return true;
	}

	
	
	function deleteListOrder( $type )
	{
		$query = 'DELETE FROM `#__gpo_lists_order` WHERE `type`= ' . $this->_db->quote( $type ) . ';';
		$this->_db->setQuery( $query );	
		$r = $this->_db->execute();
		return $r;
	}
	
	
	
	function htmlSelectAllEntryByType( $type )
	{
		$data = $this->getListByType( $type );
		array_unshift( $data, '' );
		$html = JHTML::_('select.genericlist',  $data, 'addEntry', '', 'value', 'value','');
		return $html;
	}

	function getStaffs(){
	    $query = "SELECT * FROM `#__gpo_staffs` ORDER BY `initial` ASC LIMIT 500";
	    $this->_db->setQuery( $query );
	    $this->_db->execute();
	    $staffs = $this->_db->loadObjectList();
	    return $staffs;

	}

	function addStaff($name, $initial){
	    $query = "INSERT INTO `#__gpo_staffs` (`name`,`initial`) VALUES (".$this->_db->Quote($name).", ".$this->_db->Quote($initial).")";
	    $this->_db->setQuery($query);
	    $result = $this->_db->execute();
	    return $result;
	}
	function deleteStaff($id){
	    $query = "DELETE FROM `#__gpo_staffs` WHERE `id`=".$this->_db->Quote($id);
	    $this->_db->setQuery($query);
	    $result = $this->_db->execute();
	    return $result;
	}
}
?>