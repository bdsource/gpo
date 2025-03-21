<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
/*
add new item ( from -> to, why )
remove an item
lookup - allow a lookup of the content field to show items with character.

*/
class GpoControllerCleanup extends GpoController
{
	function __construct()
	{
		parent::__construct();
		$this->oUser = & JFactory::getUser();
        //7 and 8 is administrator and super administrator
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $allow = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
        
        if ($allow !== true) {
            $link = JRoute::_('index.php');
            $this->setRedirect($link);
            $this->redirect();
        }
        $this->registerTask('', 'all');
    }
    

    function add()
	{
		if( strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST'
			 && $_POST['task'] === 'add' 
			)
		{
			$_POST['swap']['id']='';
			$model =& $this->getModel( 'Cleanup' );
			$model->insert( $_POST['swap'] );
			$link = JRoute::_( 'index.php?option=com_gpo&controller=cleanup', false  );
			$this->setRedirect($link);
			$this->redirect();
		}
		
		$from = Joomla\CMS\Factory::getApplication()->getInput()->get('from');
		$task = 'add';
		$view =& $this->getView( 'Cleanup', 'html' );		
		$view->task=&$task;
		$view->add();
	}
	
	
	
//for the moment this should just look up in content	
	function lookup()
	{
		$id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
		$model =& $this->getModel( 'Cleanup' );
		
		$items = $model->lookup( $id );
		$view =& $this->getView( 'Cleanup', 'html' );
		$view->total=&$model->total;
		$view->from=&$model->from;
		$view->items=&$items;
		$view->results();
	}

	
	
	function all()
	{
		$model =& $this->getModel( 'Cleanup' );
		
		$items = $model->getAll();
				
		$view =& $this->getView( 'Cleanup', 'html' );
		$view->items=&$items;
		$view->all();		
	}


	
	function remove()
	{
		if( Joomla\CMS\Factory::getApplication()->getInput()->getMethod() === 'POST' )
		{
			$id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
			$model =& $this->getModel( 'Cleanup' );
			$model->remove( $id );
			$msg = "Item has been removed from future cleanups.";	
		}else{
			$msg ="An error has occured whilst trying to remove an item from the cleanup script.";
		}
		$link = JRoute::_( 'index.php?option=com_gpo&controller=cleanup', false  );
		$this->setRedirect($link, $msg);
		$this->redirect();
	}
	
	
	
	function issues()
	{
		$model =& $this->getModel( 'Cleanup' );
		$items = $model->lookupAll();
		$view =& $this->getView( 'Cleanup', 'html' );
		$view->items=&$items;
		$view->issues();		
	}		
	
	
	
	function view_issue()
	{
		$id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
		$table = Joomla\CMS\Factory::getApplication()->getInput()->get('t');
		$field = Joomla\CMS\Factory::getApplication()->getInput()->get('f');
		
		$model =& $this->getModel( 'Cleanup' );
		$items = $model->lookupByTable( $id, $table, $field );
		
		if( $model->total === 0 )
		{
			$msg = 'No results were found for table:' . $table . ' with field: ' . $field;
			$link = JRoute::_( 'index.php?option=com_gpo&controller=cleanup', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
		
		
		$view =& $this->getView( 'Cleanup', 'html' );
		$view->table=&$table;
		$view->field=&$field;
		$view->cleanup=&$model->cleanup;
		$view->total=&$model->total;		
		$view->items=&$items;
		$view->viewByTable();	
	}
	
	
	
	function edit()
	{
		$model =& $this->getModel( 'Cleanup' );	
		if( strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST' && $_POST['task'] === 'edit' )
		{
			$model->update( $_POST['swap'] );
			$link = JRoute::_( 'index.php?option=com_gpo&controller=cleanup', false  );
			$this->setRedirect($link);
			$this->redirect();
		}
		
		$id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');		
		$item = $model->get($id);
		if( $item === false )
		{
			$link = JRoute::_( 'index.php?option=com_gpo&controller=cleanup', false  );
			$msg ="The find and replace you are looking for, no longer exists.";
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
		
		$task = 'edit';
				
		$view =& $this->getView( 'Cleanup', 'html' );
		$view->from=&$item->from;
		$view->id=&$item->id;		
		$view->to=&$item->to;
		$view->notes=&$item->notes;
		$view->task=&$task;		
		$view->add();		
	}
	
	
	
	function picker()
	{
		$table = Joomla\CMS\Factory::getApplication()->getInput()->get('table');
		$id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');		
		$view =& $this->getView( 'Cleanup', 'html' );
		
		if( !empty( $table ) )
		{
			$model =& $this->getModel( 'Cleanup' );
			$items = $model->lookupTable( $table, $id );
			
			if( empty( $items ) )
			{
				$link = JRoute::_( 'index.php?option=com_gpo&controller=cleanup', false  );
				$msg = "Everything appears clean for table `" . $table . "`.";
				$this->setRedirect($link, $msg);
				$this->redirect();
			}
			
			$view->items=&$items;
			$view->table=&$table;
			$view->id=&$id;
			$view->picker_results();
		}else{
			$view->picker();
		}
	}
	
	
	
	function find_replace()
	{
		$link = JRoute::_( 'index.php?option=com_gpo&controller=cleanup', false  );
		
		if( strtoupper( $_SERVER['REQUEST_METHOD'] ) !== 'POST' )
		{
			$this->redirect();
		}
		if( $_POST['cmd'] !== 'fnr' )
		{
			$this->redirect();			
		}
		
		$table = Joomla\CMS\Factory::getApplication()->getInput()->get( 'table', '','POST','string');
		$field = Joomla\CMS\Factory::getApplication()->getInput()->get( 'field', '','POST','string');

		if( strpos( $field, "[") !== false )
		{
			$field = json_decode( $field, false ); 
		}
		
		$id = Joomla\CMS\Factory::getApplication()->getInput()->get( 'id', '0','POST','int');

		$model =& $this->getModel( 'Cleanup' );				
		$find = $model->get($id);
		if( empty( $find ) )
		{
			$msg = "Failed to find and replace.";
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
				
		$r = $model->findAndReplace( $table, $field, $find );
		
		if( $r )
		{
			$msg = 'Find and Replace complete for table - ' . $table . ' reaplcing ' . $find->from . ' to ' . $find->to;
		}else{
			$msg = 'Find and Replace complete for table - ' . $table . ' reaplcing ' . $find->from . ' to ' . $find->to;
		}
		$link = JRoute::_( 'index.php?option=com_gpo&controller=cleanup', false  );
		$this->setRedirect($link, $msg);
		$this->redirect();
	}
}
?>
