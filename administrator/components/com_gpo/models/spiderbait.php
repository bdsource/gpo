<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class GpoModelSpiderbait extends JModelList
{
	var $total;
	var $data;

	function blank()
	{
		$spiderbait = new stdClass();
		$spiderbait->id='';
		$spiderbait->url='';
		$spiderbait->url_hash='';
		$spiderbait->text='';
		return $spiderbait;
	}



	function getDisplays()
	{
		$query = "SELECT COUNT( `id` ) FROM #__gpo_spiderbait";
		$this->_db->setQuery( $query );
		$this->total = $this->_db->loadResult();

		if( (int)$this->total === (int)'0')
		{
			$this->data=array();
		}else{
			$query = "SELECT `id`, `url`, `url_hash`, `text` FROM #__gpo_spiderbait ORDER BY `url` ASC";
			$this->_db->setQuery( $query );
			$this->data = $this->_db->loadObjectList();
		}
		return $this->data;
	}



	function getById( $id )
	{
		$query = "SELECT `id`, `url`, `url_hash`, `text` FROM #__gpo_spiderbait WHERE `id`= " . $this->_db->quote( $id ) . " LIMIT 0,1";
		$this->_db->setQuery( $query );
		$this->data = $this->_db->loadObject();
		return $this->data;
	}



	function store()
	{
	    $row =& $this->getTable();

	    $data = Joomla\CMS\Factory::getApplication()->getInput()->get( 'sb',array(),'POST','array' );


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