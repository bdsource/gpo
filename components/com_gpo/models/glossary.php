<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class GpoModelGlossary extends JModelLegacy
{

    function __construct(){
        parent::__construct();
    }

    function getGlossaryById($id){
        $this->_db->setQuery("SELECT * FROM `#__gpo_datapage_glossary` WHERE `id`=".$this->_db->Quote($id));
        return $this->_db->loadObject();
    }
}
