<?php
defined('_JEXEC') or die();

class GpoControllerLists extends GpoController
{
	function __construct()
	{
		parent::__construct();
                ini_set('memory_limit','128M');
		$this->oUser	= & JFactory::getUser();
        //7 and 8 is administrator and super administrator
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $this->can_publish = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;        
		
/*
		if( $this->can_publish === false )
		{
			$msg = 'Your account doesnt have high enough access';
			$link = JRoute::_( 'index.php?option=com_gpo', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();	
		}
*/          $this->registerTask( '','pick');
	}	
	
	function pick()
	{
//this could change later to be dynamic.
		$view =& $this->getView( 'Lists', 'html' );
//show what is currently in the system.
		$view->pick();
	}
	
	
	
	/*
	 * 
	 */
	function edit()
	{
		$type = Joomla\CMS\Factory::getApplication()->getInput()->get( 'type' );
		$model =& $this->getModel( 'List' );
		$list = $model->getListByType( $type );
		$order = $model->getListOrder( $type );
//get current order.
//get all items in list by type
		$view =& $this->getView( 'List', 'html' );
		/*$view->assignRef( 'list', $list );
		$view->assignRef( 'order', $order );*/

		$view->list = &$list;
		$view->order = &$order;

		$view->edit();
	}
	
	
	
	function order()
	{
		$type = Joomla\CMS\Factory::getApplication()->getInput()->get( 'type' );		
		$model =& $this->getModel( 'Lists' );
		
		if( $model->type_exists( $type ) === false )
		{
			$msg ="Please select a type first";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=lists', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
		
		if( Joomla\CMS\Factory::getApplication()->getInput()->getMethod() === 'POST')
		{
			$order = json_decode( $_POST['order'] );  
                        
			if( is_array( $order ) )
			{
				foreach( $order as $k=>$v )
				{
					if( !empty( $v ) )
					{
						$v = trim( $v );
						$order[$k] = $v;
						$r = $model->addListData( $type, $v );	
					}
				}
				$json_order = json_encode( $order );				
				$r = $model->deleteListOrder( $type );
				$r = $model->addListOrder( $type, $json_order );                                
			
                                $order = implode("\r\n", $order );
                                
				GpoSaveTypeToCache( $type, $order );
			}                        

			$msg ="Order has been saved";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=lists&task=order&type='.$type, false );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
		                
		$order = $model->getCurrentOrder( $type );
		$unique = array();
		foreach( $model->getListByType( $type ) as $item )
		{
			if( !empty( $item->value ) )
			{
				$unique[] = $item->value;
			}
		}
		$view =& $this->getView( 'Lists', 'html' );
		/*$view->assignRef( 'unique', $unique );
		$view->assignRef( 'order', json_decode( $order ) );
		$view->assignRef( 'current_entries_html', $model->htmlSelectAllEntryByType( $type ) );
		$view->assignRef( 'type', $type );*/

		$view->unique=&$unique;
		$view->order=&json_decode( $order );
		$view->current_entries_html=&$model->htmlSelectAllEntryByType( $type );
		$view->type=&$type;

		$view->view_by_type_order();
	}
	
        
        
	
	
	function view()
	{
		$type = Joomla\CMS\Factory::getApplication()->getInput()->get( 'type' );
		
		$model =& $this->getModel( 'Lists' );
		
		if( $model->type_exists( $type ) === false )
		{
			$msg ="Please select a type first";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=lists', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}

		$items = $model->getListByType( $type );
		
		$view =& $this->getView( 'Lists', 'html' );
		/*$view->assignRef( 'rows', $items );
		$view->assignRef( 'type', $type );*/

		$view->rows = &$items;
		$view->type = &$type;

		$view->view_by_type();
	}

	
//this is now viewnonunique
	function viewall()
	{
		$type = Joomla\CMS\Factory::getApplication()->getInput()->get( 'type' );		
		$model =& $this->getModel( 'Lists' );
		if( $model->type_exists( $type ) === false )
		{
			$msg ="Please select a type first";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=lists', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}

		$unique = $model->getNonEntries( $type );
		$view =& $this->getView( 'Lists', 'html' );
		/*$view->assignRef( 'unique', $unique );
		$view->assignRef( 'type', $type );*/

		$view->unique=&$unique;
		$view->type=&$type;

		$view->view_by_type_not();
	}
	
	
        //start - remove this
	function viewall_old()
	{
		$type = Joomla\CMS\Factory::getApplication()->getInput()->get( 'type' );
		
		$model =& $this->getModel( 'Lists' );
		if( $model->type_exists( $type ) === false )
		{
			$msg ="Please select a type first";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=lists', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
		
		$all = $model->getAllDataByField( $type );		
		$unique = array();
		foreach( $model->getListByType( $type ) as $item )
		{
			if( !empty( $item->value ) )
			{
				$unique[] = $item->value;
			}
		}
		$view =& $this->getView( 'Lists', 'html' );
		/*$view->assignRef( 'all', $all );
		$view->assignRef( 'current_list', json_encode( $unique ) );
		$view->assignRef( 'type', $type );*/

		$view->all=$all;
		$view->current_list=json_encode( $unique );
		$view->type=$type;

		$view->view_by_type_unique();
	}
        //end - remove this	
        
	function a_addlistitem()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}

		$type = $_POST['type'];
		$model =& $this->getModel( 'Lists' );
		
		if( $model->type_exists( $type ) === false )
		{
			$msg ="Please select a type first";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=lists', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}		
		$r = $model->addListData( $type, $_POST['value'] );

                $data = $model->getListData($type);
                
                $order = array();
                foreach($data as $item){
                    if(!empty($item)){
                    $order[] = $item->value;
                  }
                }
                
                $json_order = json_encode( $order );               
                $r = $model->deleteListOrder( $type );
                $r = $model->addListOrder( $type, $json_order );


                $ordered = implode("\r\n", $order);
                GpoSaveTypeToCache($type,$ordered);
                
		if( $r )
		{
			echo 'ok';
		}else{
			echo 'fail';
		}
		exit();
	}
		
	

        function a_removelistitem()
           {      
            if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
         {
            echo 'error';
            exit();
        }
        
        $type = $_POST['type'];
        $model =& $this->getModel('Lists');
       
               
        if( $model->type_exists( $type ) === false )
               {
                    $msg ="Please select a type first";
                    $link = JRoute::_( 'index.php?option=com_gpo&controller=lists', false  );   
                    $this->setRedirect($link, $msg);
                    $this->redirect();
           }
                            
                $r = $model->deleteListData( $_POST['id'] );
                $data = $model->getListData($type);
        
                $order = array();
                foreach($data as $item){
                    if(!empty($item)){
                   $order[] = $item->value;
                  }
                }
                
                $json_order = json_encode( $order );               
                $r = $model->deleteListOrder( $type );
                $r = $model->addListOrder( $type, $json_order );

                $ordered = implode("\r\n", $order);
                GpoSaveTypeToCache($type,$ordered);        
        
        if( $r )
        {
            echo 'ok';
            }else{
            echo 'fail';
        }
        exit();
    }
    
    	
	
	
	function test()
	{
		$data = array('Adelaide','Akron, OH');
		$type = 'city';
		$data = json_encode( $data );				
		$model =& $this->getModel( 'Lists' );
		$r = $model->addListOrder( $type, $data );
	}

	function editstaff(){
	    $model = &$this->getModel( 'Lists' );
	    $staffs = $model->getstaffs();
	    //var_dump($staffs);
	    $view = &$this->getView('Lists','html');
	    //$view->assignRef('staffs',$staffs);
	    $view->staffs=&$staffs;
	    $view->editstaff();
	}

	function deletestaff(){
	    $model = &$this->getModel( 'Lists' );
	    $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id','');
	    
	    $link = JRoute::_( 'index.php?option=com_gpo&controller=lists&task=editstaff', false  );
	    if($id){
		$result = $model->deleteStaff($id);
		if($result){
		    $msg = 'Staff is deleted successfully!';
		    $this->setRedirect($link, $msg);
		} else {
		    $msg = 'Staff could not be deleted!';
		    $this->setRedirect($link, $msg, 'error');
		}
	    } else {
		$this->setRedirect($link, 'No staff ID found!');
	    }
	    $this->redirect();
	}

	function addstaff(){
	    $model = &$this->getModel( 'Lists' );
	    if( strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST' AND Joomla\CMS\Factory::getApplication()->getInput()->get('validate')==1)
	    {
		if(Joomla\CMS\Factory::getApplication()->getInput()->get('name','') AND Joomla\CMS\Factory::getApplication()->getInput()->get('initial','')){
		    $result = $model->addStaff(Joomla\CMS\Factory::getApplication()->getInput()->get('name',''),  Joomla\CMS\Factory::getApplication()->getInput()->get('initial',''));
		    ///var_dump($result);
		    $link = JRoute::_( 'index.php?option=com_gpo&controller=lists&task=editstaff', false  );

		    if($result){
			$msg = 'Staff is added successfully!';
			$this->setRedirect($link, $msg);
		    } else {
			$msg = 'Staff could not be added!';
			$this->setRedirect($link, $msg, 'error');
		    }
		    $this->redirect();
		} else {
		    echo '<p style="color:red; font-weight: bold;">Name and Initial is empty! Please enter both entries!</p>';
		}
	    }
	    $view = &$this->getView('Lists','html');
	    //$view->assignRef('staffs',$staffs);
	    $view->staffs = $staffs;

	    $view->addstaff();
	}
}
?>