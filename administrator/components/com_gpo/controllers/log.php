<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class GpoControllerLog extends GpoController
{
	function __construct()
	{
		parent::__construct();
		$this->registerTask( '','showLast');
		$this->oUser    = & JFactory::getUser();

        if( !$this->oUser->get('isRoot') ) {
			$msg = 'Access to Logs requires higher permissions.';
			$link = JRoute::_( 'index.php?option=com_gpo', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
	}

	function showLast()
	{
                $logs = $this->_getAllUserLog();
                $users = $this->_getLoggedUsers();
                
		$view =& $this->getView( 'Log', 'html' );
		/*$view->assignRef( 'users', $users );
		$view->assignRef( 'logs', $logs );*/

        $view->users=&$users;
        $view->logs=&$logs;

		$view->showLast();
	}

        
        protected function _getLoggedUsers(){
            $db =& JFactory::getDBO();
            $query = '
                SELECT `user_id`, `user_username`, `user_name`, `user_type`  FROM `#__gpo_site_logger`
                WHERE DATE_SUB(now(), INTERVAL 7 day  ) <= `when`
                GROUP BY `user_id` 
                ORDER BY `when` DESC;
                ';
		$db->setQuery( $query );
		$users = $db->loadAssocList();
            return $users;
        }
        protected function _getAllUserLog(){
            $db =& JFactory::getDBO();
            $query = '
                SELECT DISTINCT( `user_id` ) FROM `#__gpo_site_logger`
                WHERE DATE_SUB(now(), INTERVAL 7 day  ) <= `when`
                ORDER BY `when` DESC;
                ';
		$db->setQuery( $query );
		$users = $db->loadAssocList();
            $users_log = array();
            foreach($users As $user){
                $users_log[$user['user_id']] = $this->_getUserLog($user['user_id']);
            }
            return $users_log;
        }

        protected function _getUserLog($user_id){
            $db = &JFactory::getDBO();
            $query = "
            SELECT *
            FROM `#__gpo_site_logger`
            WHERE DATE_SUB( now( ) , INTERVAL 7 day ) <= `when`
            AND `user_id`={$db->Quote($user_id)} ORDER BY `when` DESC LIMIT 100;
            ";
		$db->setQuery( $query );
		$logs = $db->loadAssocList();
                return $logs;
        }
}
?>
