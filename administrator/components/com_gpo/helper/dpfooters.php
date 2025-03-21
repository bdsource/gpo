<?php
class GpoDpfooters
{
	private $_table_name = '#__gpo_dpfooters';
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

	
/* 
 * get the desired custom_mod module for the dp dpfooters html 
 *
 * */
	public function getAllAvailabeSponsors( $module_class_sfx = 'dp-sponsors' ) {
		$_fp_cond = $module_class_sfx;
		$_fp_filtered_modules = array();
		$fp_modules = GpoDpfooters::getAllModules( 'insert-custom' );
	
		foreach ( $fp_modules as $_module ) {
			//$_fp_params = new JParameter( $_module->params );
                                                       $_fp_params = new JRegistry($_module->params);
			if ( strpos( $_fp_params->get('moduleclass_sfx'), $_fp_cond ) !== false ) {
				// add in the filtered modules array
				$_fp_filtered_modules[] = clone($_module);
			}
		}
		return $_fp_filtered_modules;
	}
	
	
		/*
	 * 
	 * Get the list of all published/active modules by position 
	 * 
	 */
	public function getAllUsers( ) {
		
		$db =& JFactory::getDBO();
                                    //$query = "SELECT name, username, email, usertype, registerDate, lastvisitDate FROM #__users";
		$query = "SELECT name, username, email, registerDate, lastvisitDate FROM #__users";
		
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		return $items;
	}
	
	
	
	/*
	 * 
	 * Get the list of all published/active modules by position 
	 * 
	 */
	public function getAllModules( $p_position=NULL ) {
		$p_position = trim( $p_position );
		$db =& JFactory::getDBO();
		$query = 'SELECT m.id, m.title, m.published, m.position, m.params
                  FROM #__modules AS m
                  WHERE m.published = 1';
		if ( !empty ( $p_position ) ) {
		    $query .= ' AND m.position = ' . $db->Quote($p_position);
		}
		
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		return $items;
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

		$data->url_hash = $this->hashUrl( $data->url );

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
	/*
	 * url or id
	 */
	function load( $input )
	{
		if( !ctype_digit( $input ) )
		{
			$query = "SELECT `id`, `url`, `url_hash`, `text` FROM `#__gpo_spiderbait` WHERE `url_hash`= " . $this->_db->quote( $this->hashUrl( $input ) ) . " LIMIT 0,1";	
		}else{
			$query = "SELECT `id`, `url`, `url_hash`, `text` FROM `#__gpo_spiderbait` WHERE `id`= " . $this->_db->quote( $input ) . " LIMIT 0,1";
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
	
	/*
	 * hash the url ( url_hash = indexed )
	 */
	function hashUrl( $input )
	{
		return md5( $input );	
	}
	
	
	private function fields()
	{
		$fields = 'id,url,url_hash,text';
		return array_fill_keys( array_values( explode(",",$fields) ), '' );
		
	}
}
