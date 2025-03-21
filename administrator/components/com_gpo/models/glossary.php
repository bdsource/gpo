<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

/*
 *  Citations model
 */

class GpoModelGlossary extends JModelList
{
    var $db_error_msg = '';
    var $_total = null;
    var $_pagination = null;

    function __construct()
    {
        parent::__construct();
        $this->limit = (int)'10';
        $this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', '', 'int');

        $mainframe =& JFactory::getApplication();
        //global $mainframe;
        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        if(!$limit) $limit = 10000;
        $limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', 0, '', 'int');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);

    }

    function getTotal($published = 1)
    {
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $Gsearch_id = Joomla\CMS\Factory::getApplication()->getInput()->get('search_id', '');
            $Gsearch_title = Joomla\CMS\Factory::getApplication()->getInput()->get('search_title', '');
            $where_string = "";
            if($Gsearch_id){
                $where_string .= " id={$Gsearch_id} AND";
            }
            
            if($Gsearch_title){
              $where_string .= " title LIKE '%{$Gsearch_title}%' AND";
            }
            $query = "SELECT count(*) FROM `#__gpo_datapage_glossary` WHERE ".$where_string." `published`= '$published'";
            $this->_db->setQuery($query);
            $result = $this->_db->loadRow();
            $this->_total = $result[0];
        }

        return $this->_total;
    }

    function getPagination($published = 1)
    {

        $published = ($published == 1) ? 1 : 0;
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal($published), $this->getState('limitstart'), $this->getState('limit'));
        }
        return $this->_pagination;
    }


    function isGlossaryExists($id, $published = 1)
    {
        $this->_db->setQuery("SELECT `id` FROM `#__gpo_datapage_glossary` WHERE `id` = " . $this->_db->quote($id) . " AND `published` = {$this->_db->Quote($published)} ");
        $result = $this->_db->loadRow();
        if (count($result)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Next Item of an item
     * @param  $id
     * @param int $published 1 for published items and 0 for unpublished items
     * @return bool
     */
    function getNextById($id, $published = 1)
    {
        $query = "SELECT `id` FROM `#__gpo_datapage_glossary` WHERE `id` > " . $this->_db->quote($id) . "  AND `published` = {$this->_db->Quote($published)}  ORDER BY `id` ASC LIMIT 1";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        if (empty($data->id)) {
            return false;
        }
        return $data->id;
    }

    function getPrevById($id, $published = 1)
    {

        $query = "SELECT `id` FROM `#__gpo_datapage_glossary` WHERE `id` < " . $this->_db->quote($id) . " AND `published` = {$this->_db->Quote($published)}  ORDER BY `id` DESC LIMIT 1";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();
        if (empty($data->id)) {
            return false;
        }
        return $data->id;
    }

    function saveGlossary($data, $id = 0)
    {
        //prepare the middle part of statement
        $set = '';
        foreach ($data AS $name => $value) {
            $set .= $this->_db->quoteName($name) . '=' . $this->_db->Quote($value) . ', ';
        }
        if ($id > 0) {
            //add modified time
            $set .= $this->_db->quoteName('modified') . '=CURRENT_TIMESTAMP';
        }
        //trim any ending comma (,)
        $set = rtrim($set, ', ');

        if ($id > 0) {
            //update record

            $query = "UPDATE `#__gpo_datapage_glossary` SET $set WHERE `id`=" . $this->_db->Quote($id);
        } else {
            //insert record
            $query = "INSERT INTO `#__gpo_datapage_glossary` SET $set";
        }

        $this->_db->setQuery($query);

        $result = $this->_db->execute();
        if ($result) {
            return $this->_db->insertid();
        } else {
            $this->db_error_msg = $this->_db->ErrorMsg();
            return false;
        }

    }

    function getGlossaries($published = 1, $order_by = 'id', $order_by_dir = 'asc')
    {
        $Gsearch_id = Joomla\CMS\Factory::getApplication()->getInput()->get('search_id', '');
        $Gsearch_title = strtolower(Joomla\CMS\Factory::getApplication()->getInput()->get('search_title', ''));
        $where_string = "";
        if($Gsearch_id){
            $where_string .= " id={$Gsearch_id} AND";
        }
        if($Gsearch_title){
          $where_string .= " LOWER(title) LIKE '%{$Gsearch_title}%' AND";
        }
        $query = "SELECT * FROM `#__gpo_datapage_glossary` WHERE ".$where_string." `published` = {$this->_db->Quote($published)} ORDER BY `$order_by` $order_by_dir LIMIT {$this->getState('limitstart')}, {$this->getState('limit')}";
        $this->_db->setQuery($query);
        return $this->_db->loadAssocList();

    }


    function getGlossary($id)
    {
        $query = "SELECT * FROM `#__gpo_datapage_glossary` WHERE `id` = " . $this->_db->Quote($id);
        $this->_db->setQuery($query);
        return $this->_db->loadObject();
    }

    function deleteGlossary($id)
    {
        $query = "DELETE FROM `#__gpo_datapage_glossary` WHERE `id`=" . $this->_db->Quote($id);
        $this->_db->setQuery($query);
        return $this->_db->execute($query);
    }
    
    
    function searchGlossary($d){
      $where = '';
      
      //if(){
      //  $where.= "DATE(modified) BETWEEN '{$d['modified_from']}' AND '{$d['modified_to']}'";
      //}else{
        if($d['modified_from']!=''){
          $where.= " AND DATE(modified) >= '{$d['modified_from']}'";
        }
        if($d['modified_to']!=''){
          $where.= " AND DATE(modified) <= '{$d['modified_to']}'";
        }
      //}
      if($d['title']!=''){
        $where.= " AND title COLLATE UTF8_GENERAL_CI LIKE '%{$d['title']}%'";
      }
      if($d['subtitle']!=''){
        $where.= " AND subtitle COLLATE UTF8_GENERAL_CI LIKE '%{$d['subtitle']}%'";
      }
      if($d['websource']!=''){
        $where.= " AND websource COLLATE UTF8_GENERAL_CI LIKE '%{$d['websource']}%'";
      }
      if($d['content']!=''){
        $where.= " AND content COLLATE UTF8_GENERAL_CI LIKE '%{$d['content']}%'";
      }
      
      $q = "SELECT * FROM #__gpo_datapage_glossary WHERE published=1 {$where} ";
      $this->_db->setQuery($q);
      $res = $this->_db->loadAssocList();
      
      return $res;
    }
    
    function getSearchPagination($d){
         $where = '';
      if($d['modified_from']!=''){
        $where.= " AND DATE(modified) >= {$d['modified_from']}";
      }
      if($d['modified_to']!=''){
        $where.= " AND DATE(modified) <= {$d['modified_to']}";
      }
      if($d['title']!=''){
        $where.= " AND title COLLATE UTF8_GENERAL_CI LIKE '%{$d['title']}%'";
      }
      if($d['subtitle']!=''){
        $where.= " AND subtitle COLLATE UTF8_GENERAL_CI LIKE '%{$d['subtitle']}%'";
      }
      if($d['websource']!=''){
        $where.= " AND websource COLLATE UTF8_GENERAL_CI LIKE '%{$d['websource']}%'";
      }
      if($d['content']!=''){
        $where.= " AND content COLLATE UTF8_GENERAL_CI LIKE '%{$d['content']}%'";
      }
      
      $q = "SELECT count(*) as total FROM #__gpo_datapage_glossary WHERE published=1  {$where}";
      $this->_db->setQuery($q);
      $res = $this->_db->loadAssocList();
      $total = $res[0]['total'];
      jimport('joomla.html.pagination');
      $pagination = new JPagination($total, $this->getState('limitstart'), $this->getState('limit'));
      return $pagination;
    }

}

?>
