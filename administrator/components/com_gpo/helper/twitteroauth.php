<?php
class GpoTwitteroauth
{
	private $_table_name = '#__gpo_twitteroauth';
	private $_db;
	private $data;
	function __construct( $uri = '' )
	{
		$this->_db =& JFactory::getDBO();
		if( !empty( $uri ) )
		{
			$this->load( $uri );
		}
	}
	
	
	
	private function fields()
	{
		$fields = 'id,window_title,page_headline,page_headline_sub,seo,meta,search,search_hash,topic_name';
		return array_fill_keys( array_values( explode(",",$fields) ), '' );
	}
	
	
	
	function save( $input )
	{
		$blankEditObject = $this->fields();
		$input['id'] = $this->get('id');
		
		foreach( $input as $k=>$v )
		{
			if( !isset( $blankEditObject[ $k ] ) )
			{
				unset( $input[ $k ] );
			}else{
				$blankEditObject[ $k ] = $v;
			}
		}
		$data = (object)$blankEditObject;		
		$data->meta = json_encode( $data->meta );
		
		if( empty( $data->id ) )
		{
			$ret = $this->_db->insertObject( $this->_table_name, $data, 'id' );
		}else{
			$ret = $this->_db->updateObject( $this->_table_name, $data, 'id', true );
		}
		return array(
						'status' => $ret,
						'data' => $data
					);
	}
	
	
	
	function get( $name )
	{
		if( isset( $this->data->$name ) )
		{
			return $this->data->$name;
		}else{
			return false;
		}
	}
	
	
	function getAll()
	{
		return ( !empty( $this->data ) ) ? $this->data :  (object)$this->fields();
	}
	
	/*
	 * url or id
	 */
	function load( $input )
	{
		if( !ctype_digit( $input ) )
		{
			$query = "SELECT * FROM `#__gpo_twitteroauth` WHERE `seo`=" . $this->_db->quote( $input ) . " LIMIT 0,1";
		}else{
			$query = "SELECT * FROM `#__gpo_twitteroauth` WHERE `id`=" . $this->_db->quote( $input ) . " LIMIT 0,1";

		}
		$this->_db->setQuery( $query );
		$this->data = $this->_db->loadObject();
	}
	
	/*
	 * url or id
	 */
	function delete( $input )
	{
		if( !ctype_digit( $input ) )
		{
			$query = "DELETE FROM `#__gpo_spiderbait` WHERE `url_hash`= " . $this->_db->quote( $this->hashUrl( $input ) ) . " LIMIT 0,1";	
		}else{
			$query = "DELETE FROM FROM `#__gpo_spiderbait` WHERE `id`= " . $this->_db->quote( $input ) . " LIMIT 0,1";
		}
		$this->_db->setQuery( $query );
	}
}
