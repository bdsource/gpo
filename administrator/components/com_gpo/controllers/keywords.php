<?php
defined('_JEXEC') or die();

class GpoControllerKeywords extends GpoController
{
	function __construct()
	{
		parent::__construct();
		$this->oUser	= & JFactory::getUser();
        //7 and 8 is administrator and super administrator
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $this->can_publish = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
        
		if( $this->can_publish === false )
		{
			$msg = 'Access to Keywords requires higher permissions.';
			$link = JRoute::_( 'index.php?option=com_gpo', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
		$this->registerTask( '','checklist');		
	}
	
	
	function checklist()
	{
		$view =& $this->getView( 'Keywords', 'html' );
		
		$db =& JFactory::getDBO();
		$query = "SELECT `keywords` FROM `#__gpo_news`;";
		$db->setQuery( $query );
		$data = $db->loadColumn();

		$keywords = array();
		foreach( $data as $v )
		{
			$v = explode( ",", $v );
			foreach( $v as $vv )
			{
				$vv = trim( $vv );
				if( !in_array( $vv, $keywords ) )
				{
					$keywords[]=$vv;
				}
			}
		}
		$view->unique_keywords=&$keywords;		
		$view->checklist();
	}
	
	
	function create_legal_list()
	{
		$view =& $this->getView( 'Keywords', 'html' );
		$view->create_legal_list();
	}
	
	
	function a_save_legal_list()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}
		
		$data = $_POST['legal-list'];
		$data = explode("\n", $data );
		sort( $data );
		$data = array_unique( $data );
		foreach( $data as $k=>$v )
		{
			$data[$k]=trim($v);
		}
		$data = implode("\r\n", $data );		
		GpoSaveTypeToCache( 'keywords', $data );
		echo 'Saved';
		exit();
	}
}
?>
