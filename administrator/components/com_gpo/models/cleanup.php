<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');


class GpoModelCleanup extends JModelList
{
	function __construct()
	{
		parent::__construct();
		$this->total = 0;
		$this->from = "";
	}
	

	function insert( $data )
	{
		$data = (object)$data;	
		$ret = $this->_db->insertObject( "#__gpo_news_cleanup", $data, 'id' );		
		return true;
	}
	
	
	
	function lookupAll()
	{
		$tables = array(
'#__gpo_news',
'#__gpo_quotes',
'#__gpo_citations_quotes',
'#__gpo_citations_news'
);
		$allow_column_types = array('text','varchar' );
		$lookup =  $this->getAll();	
		$replace = array();
		foreach( $tables as $tableName )
		{		
			$query = "SHOW COLUMNS FROM `" . $tableName . "`";
			$this->_db->setQuery( $query );
			$cols = $this->_db->loadObjectList();
//			ftp_debug( $cols, 'cols',true,false );
			foreach( $cols as $col )
			{
				$field = strtolower( $col->Field );
				$type = strtolower( $col->Type );
//				ftp_debug( $field . " " . $type, 'Field + Type',true,false );				
				foreach( $allow_column_types as $allow )
				{
					$length = strlen( $allow );
					$a = substr( $type,0,$length);
					
					$a = strtolower( $a );
					if( $a !== $allow )
					{
						continue;
					}
//					ftp_debug( 'Trying `' . $tableName .'`.`' . $field . '`', 'Field:', true, false );
/*
SELECT COUNT( `f`.`id` ) as `total`
FROM `#__gpo_quotes` as `f`, `#__gpo_news_cleanup` as `cu`
WHERE `f`.`content` LIKE CONCAT( '%', `cu`.`from`, '%' )
AND `cu`.`id`=51;
*/
					foreach( $lookup as $find )
					{
						$query = "
SELECT COUNT( `f`.`id` ) as `total`, '" . $field ."' as `field`, '" . $tableName . "' as `tablename`, `cu`.`id`, `cu`.`from`
FROM `" . $tableName . "` as `f`, `#__gpo_news_cleanup` as `cu`
WHERE `f`.`" . $field . "` LIKE CONCAT( '%', `cu`.`from`, '%' )
AND `cu`.`id`=" . $this->_db->quote( $find->id ) . "
GROUP BY `cu`.`from`;
";						
						$this->_db->setQuery( $query );
						$row = $this->_db->loadObject();
						if( $row->total > 0 )
						{
							$replace[] = $row;
						}
					}
				}
			}
		}
		return $replace;
	}
	
	
	
	function lookup( $id )
	{
		$query = "
SELECT `from` 
FROM `#__gpo_news_cleanup`
WHERE `id`=" . $this->_db->quote( $id ) . "
LIMIT 0,1;";
		$this->_db->setQuery( $query );
		$this->from = $this->_db->loadResult();
		if( empty( $this->from ) )
		{
			return false;	
		}			
		$query = "
SELECT `n`.`id`,`n`.`title`
FROM `#__gpo_news` as `n`
WHERE `n`.`content` LIKE " . $this->_db->quote( "%".$this->from ."%" ) . "
ORDER BY `n`.`id` DESC;
";	
		$this->_db->setQuery( $query );
		$data = $this->_db->loadObjectList();
		$this->total = count( $data );
		return $data;
	}
	
	
	
	function lookupByTable( $id, $table, $field )
	{
		$query = "
SELECT * 
FROM `#__gpo_news_cleanup`
WHERE `id`=" . $this->_db->quote( $id ) . "
LIMIT 0,1;";
		$this->_db->setQuery( $query );
		$this->cleanup = $this->_db->loadObject();
		if( empty( $this->cleanup ) )
		{
			return false;	
		}
		
		$query = "
SELECT `f`.`id`,`f`.`title`
FROM `" . $table . "` as `f`
WHERE `f`.`" . $field . "` LIKE " . $this->_db->quote( "%".$this->cleanup->from ."%" ) . "
ORDER BY `f`.`id` DESC;
";	
		$this->_db->setQuery( $query );
		$data = $this->_db->loadObjectList();
		$this->total = count( $data );
		return $data;
	}
	
	
	
	function remove( $id )
	{
		$query = "DELETE FROM `#__gpo_news_cleanup` WHERE `id`=" . $this->_db->quote( $id ) . ";";
		$this->_db->setQuery( $query );	
		$ret = $this->_db->execute();		
		return true;
	}
	
	
	function getAll()
	{
		$query = "
SELECT * FROM `#__gpo_news_cleanup`
ORDER BY `id` DESC;
";	
		$this->_db->setQuery( $query );
		$data = $this->_db->loadObjectList();
		return $data;
	}
	
	
	function get( $id )
	{
		$query = "
SELECT * FROM `#__gpo_news_cleanup`
WHERE `id`=" . $this->_db->quote( $id ) . ";
";	
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}
	
	
	function update( $data )
	{
		$data = (object)$data;	
		$ret = $this->_db->updateObject( "#__gpo_news_cleanup", $data, 'id', true );		
		return true;
	}
	
	
	
	function lookupTable( $tableName, $id )
	{
$tables = array(
'#__gpo_news',
'#__gpo_quotes',
'#__gpo_citations_quotes',
'#__gpo_citations_news'
);
$tableName = "#__" . $tableName;

		$allow_column_types = array('text','varchar' );
//		$lookup =  $this->getAll();	
		$find = $this->get($id);
		if( empty( $find ) ) return false;
		
		$replace = array();
		$query = "SHOW COLUMNS FROM `" . $tableName . "`";
		$this->_db->setQuery( $query );
		$cols = $this->_db->loadObjectList();

		foreach( $cols as $col )
		{
			$field = strtolower( $col->Field );
			$type = strtolower( $col->Type );
			foreach( $allow_column_types as $allow )
			{
				$length = strlen( $allow );
				$a = substr( $type,0,$length);
				
				$a = strtolower( $a );
				if( $a !== $allow )
				{
					continue;
				}

					$query = "
SELECT COUNT( `f`.`id` ) as `total`, '" . $field ."' as `field`, '" . $tableName . "' as `tablename`, `cu`.`id`, `cu`.`from`
FROM `" . $tableName . "` as `f`, `#__gpo_news_cleanup` as `cu`
WHERE `f`.`" . $field . "` LIKE CONCAT( '%', `cu`.`from`, '%' )
AND `cu`.`id`=" . $this->_db->quote( $find->id ) . "
GROUP BY `cu`.`from`;
";						
					$this->_db->setQuery( $query );
					$row = $this->_db->loadObject();
					if( $row->total > 0 )
					{
						$replace[] = $row;
					}
			}
		}
		return $replace;
	}
	
	
	
	function findAndReplace( $table, $fields, $find )
	{       
                $table = '#__'.$table;
		if( is_string( $fields ) )
		{	
			$fields = array( $fields );
		}
		
		if ( is_array( $fields ) )
		{
			foreach( $fields as $field )
			{
				$query = "
UPDATE `" . $table . "` SET `" . $field . "`=REPLACE(`" . $field . "`, ". $this->_db->quote( $find->from )."," . $this->_db->quote( $find->to ) . " );
";
				$this->_db->setQuery( $query );	
				$ret = $this->_db->execute();
			}
			return true;
		}
	}
}
?>