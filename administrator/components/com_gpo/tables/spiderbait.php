<?php
/**
 * Hello World table class
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
class TableSpiderbait extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	var $url_hash = null;

	var $url = null;

	var $text = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableSpiderbait(& $db) {
		parent::__construct('#__gpo_spiderbait', 'id', $db);
	}

	function check()
	{
		$this->url = str_replace( JApplication::getCfg( 'live_site'),"", $this->url );
		$this->url_hash = md5( $this->url );
		return parent::check();
	}
}
?>
