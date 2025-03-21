<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

/*
 *  Citations model
 */

class GpoModelFeaturedCharts extends JModelList
{
    var $db_error_msg = '';
    var $_total = null;
    var $_pagination = null;
    var $currentLang = 'en';

    function __construct($p_options)
    {
        parent::__construct();
        $this->limit = Joomla\CMS\Factory::getApplication()->getInput()->get('limit', '100', '', 'int');
        $this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', '', 'int');
        
        $this->currentLang = strtolower($p_options['currentLang']);
    }

    function updateOrdering($data)
    {
        //prepare set statements
        foreach ($data As $key => $value) {
            $this->_db->setQuery("UPDATE `#__gpo_featuredcharts` SET `ordering` = " . $this->_db->Quote($value) . " WHERE `id`=" . $this->_db->Quote($key));
            echo $this->_db->getQuery().'<br/>';
            $this->_db->execute();
        }
        return;
    }

    function getFeaturedChart($id)
    {
        $this->_db->setQuery("SELECT * FROM `#__gpo_featuredcharts` WHERE `id`=" . $this->_db->Quote($id));
        return $this->_db->loadObject();
    }

    function getFeaturedCharts()
    {
        $this->_db->setQuery("SELECT 
                                     count(id) as `num_published` 
                              FROM 
                                     `#__gpo_featuredcharts`
                              WHERE 
                                     `language` = " . $this->_db->Quote($this->currentLang) . "        
                             ");

        $total = $this->_db->loadRow();
        $this->_total = $total[0];

        $this->_pagination = new JPagination($this->_total, $this->limitstart, $this->limit);
        $this->_db->setQuery("SELECT 
                                     * 
                              FROM 
                                     `#__gpo_featuredcharts` 
                              WHERE 
                                     `language` = " . $this->_db->Quote($this->currentLang) . "  
                              ORDER BY ".Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'ordering').' '.Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'asc'), $this->_pagination->limitstart, $this->_pagination->limit);
        
        return $this->_db->loadObjectList();
    }


    /**
     * Update the database table
     * @param array $data Associative array of field name => field value
     * @param int $where_id Primary key ID
     * @return void
     */
    function saveFeaturedCharts($data = array(), $where_id = null)
    {

        $set = '';
        foreach ($data AS $key => $value) {
            $set .= $this->_db->quoteName($key) . '=' . $this->_db->Quote($value) . ', ';
        }
        $set = rtrim($set, ', ');

        if (empty($where_id)) {
            //this is insert query
            $this->_db->setQuery("INSERT INTO `#__gpo_featuredcharts` SET $set");
            if ($this->_db->execute()) {
                return $this->_db->insertid();
            } else {
                return false;
            }
        } else {
            //this is update query
            $this->_db->setQuery("UPDATE `#__gpo_featuredcharts` SET $set WHERE `id`=" . $this->_db->Quote($where_id));
            if ($this->_db->execute()) {
                return $this->_db->getAffectedRows();
            } else {
                return false;
            }
        }

    }


    function deleteFeaturedChart($id)
    {
        $query = "DELETE FROM 
                        `#__gpo_featuredcharts` 
                  WHERE 
                        `id`=" . $this->_db->Quote($id)
                ;
        $this->_db->setQuery($query);
        return $this->_db->execute($query);
    }


}
?>