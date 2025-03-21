<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class GpoModelSponsors extends JModelList
{
	var $total;
	var $data;

	function blank()
	{
		$sponsors = new stdClass();
		$sponsors->id = '';
		$sponsors->url = '';
		$sponsors->url_hash = '';
		$sponsors->module_id = '';
		$sponsors->comment = '';
		$sponsors->created_at = '';
		return $sponsors;
	}



	function getDisplays()
	{
		$query = "SELECT COUNT( `id` ) FROM #__gpo_sponsors";
		$this->_db->setQuery( $query );
		$this->total = $this->_db->loadResult();

		if( (int)$this->total === (int)'0')
		{
			$this->data=array();
		}else{
			$query = "SELECT s.`id`, s.`url`, s.`url_hash`, s.`comment`, s.`created_at`,  
                      s.`module_id`, s.`module_id_fr`, s.`module_id_es`,   
                      m.`title` 
			          FROM #__gpo_sponsors as s 
			          LEFT JOIN #__modules as m 
			          ON m.`id` = s.`module_id` 
			          ORDER BY s.`url` ASC";
			$this->_db->setQuery( $query );
			$this->data = $this->_db->loadObjectList();
		}
		return $this->data;
	}



	function getById( $id )
	{
		$query = "SELECT `id`, `url`, `url_hash`, `module_id`, `module_id_fr`,`module_id_es`, `comment`, `created_at` 
		          FROM #__gpo_sponsors WHERE `id`= " . $this->_db->quote( $id ) . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		
		$this->data = $this->_db->loadObject();
		return $this->data;
	}
	



	function store()
	{
	    $row =& $this->getTable();

	    $data = Joomla\CMS\Factory::getApplication()->getInput()->get( 'sb',array(),'POST','array' );
        
	    //get the timestamp at the updation of data
		$unix_timestamp = ( isset( $_SERVER['REQUEST_TIME'] ) ) ? $_SERVER['REQUEST_TIME'] : date('U');
		$createddAt = date( 'Y-m-d H:i:s', $unix_timestamp );		
		$data['created_at'] = $createddAt;

		
	    // Bind the form fields to the hello table
	    if (!$row->bind($data)) {
	        $this->setError($this->_db->getErrorMsg());
	        return false;
	    }

	    // Make sure the hello record is valid
	    if (!$row->check()) {
	        $this->setError($this->_db->getErrorMsg());
	        return false;
	    }

	    // Store the web link table to the database
	    if (!$row->store()) {
	        $this->setError($this->_db->getErrorMsg());
	        return false;
	    }

	    return true;
	}



	function delete()
	{
		$cids = Joomla\CMS\Factory::getApplication()->getInput()->get( 'cid', array(0), 'post', 'array' );
	    $row =& $this->getTable();

	    foreach($cids as $cid) {
	        if (!$row->delete( $cid )) {
	            $this->setError( $row->getErrorMsg() );
	            return false;
	        }
	    }

	    return true;
	}
}
?>