<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once( JPATH_COMPONENT . DS . 'helper/twitteroauth.php' );
require_once( JPATH_COMPONENT . DS . 'helper/spiderbait.php' );
/*
id
title
url
search_for
this should be linked to the save... to create from

 */
class GpoModelTwitteroauths extends JModelList
{
	var $total;
	var $data;

	function __construct()
	{
		parent::__construct();
		$this->limit = (int)'10';
		$this->limitstart = Joomla\CMS\Factory::getApplication()->getInput()->get('limitstart', '0', '', 'int');
	}
	
	
	function save($data){
        $insertOAuthValues = "INSERT INTO " ."j25_gpo_twitter_oauth".
            "(`owner`,`client`,`consumer_key`,`consumer_secret`,`user_token`,`user_secret`)" .
            "VALUES('$data[owner]','$data[client_name]', '$data[consumer_key]', '$data[consumer_secret]','$data[user_token]', '$data[user_secret]')";

        if( !empty($insertOAuthValues) ){

            $this->_db->setQuery( $insertOAuthValues);

            $result = $this->_db->execute();
        }else{
            $result = 'Data Inserting Error!';
        }

        return $result;
    }

    function update($data){
        $this->_db->setQuery("UPDATE j25_gpo_twitter_oauth SET `owner`='" . $data['owner'] ."', `client`='" . $data['client_name'] ."', `consumer_key`='" . $data['consumer_key'] . "', `consumer_secret`='".$data['consumer_secret']."', `user_token`='".$data['user_token']."', `user_secret`='".$data['user_secret']."'  WHERE `id`=".$data['id']);
        return $this->_db->execute();
    }

    function delete($id){
        $this->_db->setQuery("DELETE FROM j25_gpo_twitter_oauth WHERE `id`=".$id);
        return $this->_db->execute();
    }

    function getAll(){
        $selectOAuthValues = "SELECT * FROM j25_gpo_twitter_oauth";

        if(!empty($selectOAuthValues)){
            $this->_db->setQuery($selectOAuthValues);

            $data = $this->_db->loadAssocList();
        }else{
            $data = "Data Selecting Error!";
        }

        return $data;
    }

    function getById($id){
        $selectOAuthValues = "SELECT * FROM j25_gpo_twitter_oauth WHERE id=".$id;

        $this->_db->setQuery($selectOAuthValues);
        $data = $this->_db->loadObject();


        return $data;
    }

}