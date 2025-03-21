<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class GpoModelDpfooters extends JModelList
{
	var $total;
	var $data;

	function blank()
	{
		$dpfooters = new stdClass();
		$dpfooters->id = '';
		$dpfooters->url = '';
		$dpfooters->url_hash = '';
		$dpfooters->footer_credit = '';
		$dpfooters->comment = '';
		$dpfooters->is_published = 1;
		$dpfooters->created_at = '';
		return $dpfooters;
	}



	function getDisplays()
	{
		$query = "SELECT COUNT( `id` ) FROM #__gpo_dpfooters";
		$this->_db->setQuery( $query );
		$this->total = $this->_db->loadResult();

		if( (int)$this->total === (int)'0')
		{
			$this->data=array();
		}else{
			$query = "SELECT s.`id`, s.`url`, s.`url_hash`, s.`footer_credit`, s.`created_at`, s.`is_published` 
			          FROM #__gpo_dpfooters as s 
			          ORDER BY s.`url` ASC";
			$this->_db->setQuery( $query );
			$this->data = $this->_db->loadObjectList();
		}
		return $this->data;
	}



	function getById( $id )
	{
		$query = "SELECT *
		          FROM #__gpo_dpfooters WHERE `id`= " . $this->_db->quote( $id ) . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		
		$this->data = $this->_db->loadObject();
		return $this->data;
	}
	



	function store()
	{
	    $jinput = JFactory::getApplication()->input;
	    $row =& $this->getTable();
	    $data = $jinput->get('sb',array(),'array');
	    //print_r($data);die();
        
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
	     $jinput = JFactory::getApplication()->input;
		$cids = $jinput->post('cid',array(0),'array'); 
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