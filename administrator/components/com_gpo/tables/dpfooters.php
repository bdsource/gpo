<?php
/**
 * Dpfooters table class
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://dev.joomla.org/component/option,com_jd-wiki/Itemid,31/id,tutorials:components/
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Hello Table class
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class TableDpfooters extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	var $url_hash = null;

	var $url = null;
	
	var $footer_credit = null;
	
	var $comment = null;
	
	var $is_published = null;

	var $created_at = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableDpfooters(& $db) {
		parent::__construct('#__gpo_dpfooters', 'id', $db);
	}

	function check()
	{
		$this->url = str_replace( Joomla\CMS\Factory::getApplication()->getCfg( 'live_site'),"", $this->url );
		
	    if( substr($this->url,0,1) == "/" )
		{
			$this->url = substr( $this->url,1 );
		}
			
        if( substr($this->url,-1) == "/" )
		{
			$this->url = substr( $this->url,0,-1 );
		}
		
		$this->url_hash = md5( $this->url );
		return parent::check();
	}
}
?>
