<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class GpoControllerCitations extends GpoController
{
	function __construct()
	{
	    $jinput = JFactory::getApplication()->input;

		parent::__construct();
		$this->registerTask( '','published');
		$this->registerTask( 'add','edit');
		$this->type = $jinput->get('type');	
//echo $this->type;die();
		$this->oUser	= & JFactory::getUser();
        
        //7 and 8 is administrator and super administrator
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $this->can_publish = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
        $this->isAdministrator = $this->can_publish;
         
		$this->cookie_name_last_search = 'gpo_admin_citations_' . $this->type . '_last_search';
		$this->cookie_name_last_search_clicked = 'gpo_admin_citations_' . $this->type . '_last_search_clicked';
        
        #Sphinx Indexer command, run as root
        $this->logfile = "/home/gpo/sphinx/sphinx_admin_citations_".$this->type.".log";
        $this->reindexType = "--config /home/gpo/sphinx/etc/sphinx.conf search_gpo_admin_citations_" . $this->type;
        $this->reindexCommand = "/opt/sphinx/bin/indexer " . $this->reindexType . " --rotate > " . $this->logfile;
	}
	
	
	function start()
	{
		$view =& $this->getView( 'Citations', 'html' );
		$view->start();
	}

	
				
	function create()
	{
	     $jinput = JFactory::getApplication()->input;
		$model_name = 'Citations' . $this->type;
		$model =& $this->getModel( $model_name );
		$view =& $this->getView( $model_name, 'html' );
		
		$oCitation = $model->fields(false);
		$oCitation->id=0;
				
		$view->oCitation=&$oCitation;
		$view->can_publish=&$this->can_publish;
		
		$view->cookie_name_last_search=&$this->cookie_name_last_search;
		$view->cookie_name_last_search_clicked=&$this->cookie_name_last_search_clicked ;		
		$view->edit();
	}
		
	function edit()
	{		
	     $jinput = JFactory::getApplication()->input;
        $model_name = 'Citations' . $this->type;        
		$model =& $this->getModel( $model_name );	
		$id = $jinput->get('id');
		$oCitation =& $model->getUnPublishedById( $id );
		if( !isset( $oCitation->id ) )
		{
			$live_id =  $jinput->get('live_id');
                        
			if( !empty( $live_id ) )
			{
				$oCitation = $model->copyForEdit( $live_id );
				if( !empty( $oCitation->id ) )
				{
					$msg = '';
					$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $model->type . '&task=edit&id=' . $oCitation->id . '&live_id=' . $oCitation->live_id, false  );
					$this->setRedirect($link, $msg);
					$this->redirect();
				}	
			}
			$msg = 'Edit failed due to bad id';
			$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $model->type, false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
                        
		$view =& $this->getView( $model_name, 'html');

		$view->cookie_name_last_search = $this->cookie_name_last_search;
		$view->cookie_name_last_search_clicked = $this->cookie_name_last_search_clicked; 
		$view->can_publish = $this->can_publish;		
		$view->oCitation = $oCitation;

		$view->edit();
	}
	
        
	function a_save()
	{
	     $jinput = JFactory::getApplication()->input;
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			$filename = "/var/www/private/bugs/citation.a_save.".time();
			$data = print_r( $_SERVER, true );
			file_put_contents( $filename, $data );			
			echo 'Error: XMLHttpRequest required, this really should not be happening. Could be related to the page not being fully loaded. This has been logged.';
			exit();
		}
		
//		$_POST['citations']['locations']=explode(",",$_POST['citations']['locations']);
		$input = $_POST['citations'];
		
		$model_name = 'Citations' . $this->type;
	
		$model =& $this->getModel( $model_name );		
		$response = $model->save( $input );
		echo $response;
		exit();
	}
	
	
	
	function cancel()
	{
		$msg = '';
		$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type, false  );
		$this->setRedirect($link, $msg);
	}
	
	
	
	
//PUBLISHED SYSTEM - section
	function publish()
	{
         $jinput = JFactory::getApplication()->input;    
		$model_name = 'Citations' . $this->type;        
		$model =& $this->getModel( $model_name );
		$id = $jinput->get('id'); 
		
		$oCitation = $model->getUnPublishedById( $id );
                
		if( empty( $oCitation ) )
		{
			die( 'Citation error: maybe id' );
		}

		$response = $model->canPublish( $oCitation );
		if( $response->pass === false )
		{
			$response = new stdClass();
			$response->msg = 'This is not ready for publishing';
			$response->link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '$task=edit&id=' . $oCitation->id, false  );
			$this->setRedirect( $response->link, $response->msg );
			$this->redirect();
		}
		
				
		if( strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST' )
		{
			if( $this->can_publish === false )
			{
				$response = $model->readyForPublishing( $oCitation );
			}else{
				$response = new stdClass();
				$response = $model->publish( $oCitation );
				/*
				 * The QCite is moved from Unpublished to Published Table. So, we need to update our reference
				 * in gpo_citation_relation table.
				 */
				$cCitation = &$this->getModel('Citations'); //load Citation Model.
				$ref_update = $cCitation->updateCitationRelation($id, $response->live_id, $this->type, 'unpublished');
			}
//Needs to have index rebuilt
			$model->setReIndex();
			$this->setRedirect( $response->link, $response->msg);
			$this->redirect();			
		}

		$view =& $this->getView( $model_name, 'html' );	

		$view->cookie_name_last_search=&$this->cookie_name_last_search;
		$view->cookie_name_last_search_clicked=&$this->cookie_name_last_search_clicked;
		
		$view->can_publish=&$this->can_publish;	
		$view->oCitation=&$oCitation;
		$view->publish();
	}
  
  function publishAll(){
       $jinput = JFactory::getApplication()->input;
    if($jinput->get('cmd') && $jinput->get('cmd')=='publishall'){
      $model_name = 'Citationsquotes';
      $model =& $this->getModel( $model_name );
      $view =& $this->getView( $model_name, 'html' );	
      $oItems = $model->unpublished();
      foreach($oItems as $val){
        $oCitation = $model->getUnPublishedById( $val['id']);
        $model->publish( $oCitation );
      }
      $this->type = 'quotes';
      $this->published();
      
    }else{
      $model_name = 'Citations' . $this->type;
      $model =& $this->getModel( $model_name );
      $view =& $this->getView( $model_name, 'html' );	
      $view->publishAll();
    }
  }

    
	function published()
	{
	     $jinput = JFactory::getApplication()->input;
		$model_name = 'Citations' . $this->type;
		$model =& $this->getModel( $model_name );
		$view =& $this->getView( $model_name, 'html' );
              
		$oItems = $model->published();
		
		$shouldReIndex = $model->shouldReIndexForSphinx();
	/*	$view->assignRef( 'shouldReIndex', $shouldReIndex );
		
		$view->assignRef( 'cookie_name_last_search', $this->cookie_name_last_search );
		$view->assignRef( 'cookie_name_last_search_clicked', $this->cookie_name_last_search_clicked );
				
		$view->assignRef( 'can_publish', $this->can_publish );
		$view->assignRef( 'unpublishedTotal', $model->unpublishedTotal );		
		$view->assignRef( 'pagination', $model->pagination );
		$view->assignRef( 'rows', $oItems );
		$view->assignRef( 'oUser', $this->oUser );
		$view->assignRef( 'model', $model );
		
			 
		$view->assign( 'filter_order', $jinput->get('filter_order', ''));
		$view->assign( 'filter_order_Dir', $jinput->get('filter_order_Dir', '') );
		$view->assign( '$is_post',  $jinput->getMethod() );
		$view->assign( 'front_end', str_replace( "administrator",'',JURI::base( true ) ) );*/
		$view->shouldReIndex=&$shouldReIndex;
	    $view->cookie_name_last_search=&$this->cookie_name_last_search;
	    $view->cookie_name_last_search_clicked=&$this->cookie_name_last_search_clicked;
	    $view->can_publish=&$this->can_publish;
	    $view->unpublishedTotal=&$model->unpublishedTotal;
	    $view->pagination=&$model->pagination;
	    $view->rows=&$oItems;
	    $view->oUser=&$this->oUser;
	    $view->model=&$model;
	    $view->filter_order=$jinput->get('filter_order', '');
	    $view->filter_order_Dir=$jinput->get('filter_order_Dir', '');
	    $view->is_post=$jinput->getMethod();
    	$view->front_end=str_replace( "administrator",'',JURI::base( true ) );
		
		$view->published();
	}
	
	
	
	/*
	 * list all unpublished items.
	 * list of last modified
	 */
	function unpublished()
	{
	     $jinput = JFactory::getApplication()->input;
		$model_name = 'Citations' . $this->type;
		$model =& $this->getModel( $model_name );
		$view =& $this->getView( $model_name, 'html' );
		
		$oItems = $model->unpublished();
		
		$shouldReIndex = $model->shouldReIndexForSphinx();
		$view->shouldReIndex=&$shouldReIndex;
			
		$view->cookie_name_last_search=&$this->cookie_name_last_search;
		$view->cookie_name_last_search_clicked=&$this->cookie_name_last_search_clicked;
				
		$view->can_publish=&$this->can_publish;		
		$view->rows=&$oItems;
		$view->oUser=&$this->oUser;
		
		$view->model=&$model;		
		$view->pagination=&$model->pagination;		
		
		$view->unpublished();
	}
	

	
	function unpublished_empty()
	{
	    $jinput = JFactory::getApplication()->input;
		$model_name = 'Citations' . $this->type;
		$model =& $this->getModel( $model_name );
		
		if( strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST' )
		{
			if( $_POST['cmd'] === 'del' )
			{
				$response = $model->emptyUnpublished();
				$this->setRedirect( $response->link, $response->msg);
				$this->redirect();	
			}
		}
		$view =& $this->getView( $model_name, 'html' );		
		$view->empty_unpublished();		
	}	
	
	
	
	function unpublished_delete()
	{
	    $jinput = JFactory::getApplication()->input;
		$model_name = 'Citations' . $this->type;
		$model =& $this->getModel( $model_name );
		$id =  $jinput->get('id');

        //get the detail before we delete it
        $oItem = &$model->getUnPublishedById ( $id );

		if( $model->deleteUnpublishedById( $id ) )
		{
			//citation is deleted. now we need to delete the relation between Qcite and Quote.
			$mCitation =& $this->getModel( 'Citations' );
			$mCitation->deleteCitationRelation( $id, $this->type, $oItem->ext_id );
			$msg = 'Deletion: successful';
		}else{
			$msg = 'Deletion: failed';	
		}
		$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $model->type . '&task=unpublished', false  );
		$this->setRedirect($link, $msg);
		$this->redirect();		
	}

	
	
	function published_delete()
	{	 
	  $jinput = JFactory::getApplication()->input;
		if( $this->can_publish === false )
		{
			$msg = 'At present, your access level does not allow you to publish.';
			$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $model->type, false  );
			$this->setRedirect( $link, $msg );
			$this->redirect();			
		}	
		$model_name = 'Citations' . $this->type;
		$model =& $this->getModel( $model_name );
		$id = $jinput->get('id');

		if( strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST' )
		{
			$id = $jinput->get('id','', int);
            //get the detail before we actually delete it
             $oItem = &$model->getPublishedById ( $id );
            
			if( $model->deletePublishedById( $id ) )
			{
			    //citation is deleted. now we need to delete the relation between Qcite and Quote
			    $mCitation =& $this->getModel( 'Citations' );
			    $mCitation->deleteCitationRelation( $id, $this->type, $oItem->ext_id);
                //Needs to have index rebuilt
				$model->setReIndex();
				$link = JRoute::_('index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=reindex',false );
				$msg = 'Citation Item has been deleted. To remove it from the index straight away, <a href="' . $link . '" >Update the News index</a>.';
			}else{
				$msg = 'Deletion: failed';	
			}
			$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=published',false );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
		$item = $model->getPublishedById( $id );
		if( empty( $item ) )
		{
			die( 'Citation error: maybe id' );
		}		
		$view =& $this->getView( $model_name, 'html' );	
		
		$view->cookie_name_last_search=&$this->cookie_name_last_search;
		$view->cookie_name_last_search_clicked=&$this->cookie_name_last_search_clicked;
		
		$view->item=&$item;
		$view->delete();
	}
	
	
	
	function save_publish()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			$filename = "/home/gpo/joomla4.gunpolicy.org/private/bugs/citation.save_publish." . time();
			$data = print_r( $_SERVER, true );
			file_put_contents( $filename, $data );			
			echo 'Error: XMLHttpRequest required, this really should not be happening. Could be related to the page not being fully loaded. This has been logged.';
			exit();
		}

		//$input = $_POST['citations'];
		$app        = JFactory::getApplication();
    $jinput     = $app->input;
    $input     = $jinput->getVar('citations',array());
		$model_name = 'Citations' . $this->type;
	
		$model =& $this->getModel( $model_name );
		$response = $model->save( $input );


		if( substr( $response,0, strlen( '<script>window.location' ) ) !== '<script>window.location' )
		{
			echo $response;
			exit();
		}else{
			$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $model->type . '&task=publish&id=' . $model->new_id, false );
			$js = "<script>window.location='" . $link . "'</script>";
			echo $js;	
		}
		
		//$this->redirect(Route::_('index.php?option=com_gpo&controller=citations&type=' . $model->type . '&task=publish&id=' . $model->new_id));

	}
	
	
	
	function preview()
	{
	     $jinput = JFactory::getApplication()->input;
	     
		$id = $jinput->get('id', false); 
		if(empty( $id ) )
		{
			die( 'Citation error: maybe id ( preview )' );
		}
		$model_name = 'Citations' . $this->type;
		$model =& $this->getModel( $model_name );

		$oCitation =& $model->getUnPublishedById( $id );
		
		if( empty( $oCitation ) )
		{
			die( 'Citation error: maybe id ( preview )' );
		}
		
		$view =& $this->getView( $model_name, 'html' );	
		$view->citation=&$oCitation;
		
		$view->cookie_name_last_search=&$this->cookie_name_last_search;
		$view->cookie_name_last_search_clicked=&$this->cookie_name_last_search_clicked;
		
		$view->preview();
		exit();	
	}
	
	

	function reindex()
	{
//check they have permission		
		if( $this->can_publish === false )
		{
			$msg = 'At present, your access level does not allow you to publish.';
			$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type ,false );
			$this->setRedirect( $link, $msg );
			$this->redirect();			
		}

		$model_name = 'Citations' . $this->type;
		$model =& $this->getModel( $model_name );
				
		$inprogress = $model->isBuildInProgress();
		if( $inprogress )
		{
			die("Please wait a moment and try again ( by hitting F5 ), News is currently being built." );
		}
		
		$inprogress = $model->isReIndexInProgress();
		if( $inprogress )
		{
			die("Please wait a moment and try again ( by hitting F5 ), News are currently being updated( Reindexing of Sphinx )." );
		}

		if( strtolower( $_SERVER['REQUEST_METHOD'] ) === 'post' )
		{
			$go = ( isset( $_POST['reindex'] ) ) ? true:false;
			$force = ( isset( $_POST['force'] ) ) ? true:false;				
			if( $go )
			{
				if( $force )
				{
//Needs to have index rebuilt
					$model->setReIndex();						
				}
				$rebuild = 'citations_' . $this->type;
				//$cmd = "/usr/sbin/sphinx-gpo " . $rebuild . " > /dev/null 2>&1 &";
                $cmd = $this->reindexCommand . " &";
        //echo exec('whoami');
				exec( $cmd, $output,$result_code);
				/*
				echo '$output: <br/>';
				print_r($output);
				echo '$result_code: <br/>';
				print_r($result_code);
				exit();
				*/
				
				$msg = "Update of index completed.";
				$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type ,false );
				$this->setRedirect($link, $msg);
				$this->redirect();
				exit();
			}
		}
		$model_name = 'Citations' . $this->type;
		$view =& $this->getView( $model_name, 'html' );	

		$view->cookie_name_last_search=&$this->cookie_name_last_search;
		$view->cookie_name_last_search_clicked=&$this->cookie_name_last_search_clicked;
				
		$shouldReIndex = $model->shouldReIndexForSphinx();
		
		$view->shouldReIndex=&$shouldReIndex;
		$view->reindex();
	}
	
	

	


	function search()
	{		
		$last_search_clicked_on = ( isset( $_COOKIE[ $this->cookie_name_last_search_clicked ] ) ) ? $_COOKIE[ $this->cookie_name_last_search_clicked ] : false;
		if( $last_search_clicked_on !== false )
		{
			$cookie=array();
			$cookie[ 'name' ]=$this->cookie_name_last_search_clicked;//Name of the cookie
			$cookie[ 'expiry_date' ]= $_SERVER['REQUEST_TIME'] - 86400;//Expiry date of the cookie ( 30 seconds )
			$cookie[ 'path' ]='/';//path allowed - linked to the domain	
			setcookie(
			//cookie_name,
			$cookie[ 'name' ],
			//cookie_data,
			$cookie[ 'data' ],
			//cookie_expiry_date,
			$cookie[ 'expiry_date' ],
			//cookie_path,
			$cookie[ 'path' ]
			);					
			$link = JRoute::_( "index.php?option=com_gpo&controller=citations&type=" . $this->type . "&task=search&back=1#gpo-row-" . $last_search_clicked_on,false );
			$this->setRedirect($link);
			$this->redirect();
		}
			
		if( isset( $_COOKIE[ $this->cookie_name_last_search ] ) && $_GET['back'] === '1' )
		{
			$data = $_COOKIE[ $this->cookie_name_last_search ];
			if( !empty( $data ) )
			{
				$data = unserialize( $data );
			}
			$_GET = $data;
		}
		
		$model_name = 'Citations' . $this->type;
		$model =& $this->getModel( $model_name );
		
		$inprogress = $model->isBuildInProgress();
		if( $inprogress )
		{
			die("Please wait a moment and try again ( by hitting F5 ), a build is in progress." );
		}
		
		$inprogress = $model->isReIndexInProgress();
		if( $inprogress )
		{
			die("Please wait a moment and try again ( by hitting F5 ), The News is reindexed." );
		}

		$shouldReIndex = $model->shouldReIndexForSphinx();
		
//CitationsNewsSearch
		$model_name = 'CitationsSearch';
		$modelSearch =& $this->getModel( $model_name );

		$f = $this->type;
		$modelSearch->$f();			
		
		$model_name = 'Citations' . $this->type;
		$view =& $this->getView( $model_name, 'html' );	

		$view->cookie_name_last_search=&$this->cookie_name_last_search;
		$view->cookie_name_last_search_clicked=&$this->cookie_name_last_search_clicked;
				
		$view->logged_in=$this->logged_in;
		$view->rows=&$modelSearch->results;
		$view->pagination=&$modelSearch->pagination;				
		$view->can_publish=&$this->can_publish;
		$view->oUser=&$this->oUser;
		$view->shouldReIndex=&$shouldReIndex;
						
		if( $_GET['revise'] === '1' )
		{
			$oCitation = (object) $_GET['citation'];
			$oCitation->published_range = (object)$oCitation->published_range;
			$view->oCitation=&$oCitation;
			$view->search();
		}else if( empty( $modelSearch->results ) )
		{
			$view->search();
		}
                    else{
			$data = serialize( $_GET );
			$cookie=array();
			$cookie[ 'name' ]=$this->cookie_name_last_search;//Name of the cookie
			$cookie[ 'data' ]=$data;//Data you want to store - 3 is the magic number
			$cookie[ 'expiry_date' ]= $_SERVER['REQUEST_TIME'] + 86400;//Expiry date of the cookie ( 30 seconds )
			$cookie[ 'path' ]='/';//path allowed - linked to the domain
			setcookie(
			//cookie_name,
			$cookie[ 'name' ],
			//cookie_data,
			$cookie[ 'data' ],
			//cookie_expiry_date,
			$cookie[ 'expiry_date' ],
			//cookie_path,
			$cookie[ 'path' ]
			);					
                        
			$cookie=array();
			$cookie[ 'name' ]=$this->cookie_name_last_search_clicked;//Name of the cookie
			$cookie[ 'expiry_date' ]= $_SERVER['REQUEST_TIME'] - 86400;//Expiry date of the cookie ( 30 seconds )
			$cookie[ 'path' ]='/';//path allowed - linked to the domain	
			setcookie(
			//cookie_name,
			$cookie[ 'name' ],
			//cookie_data,
			$cookie[ 'data' ],
			//cookie_expiry_date,
			$cookie[ 'expiry_date' ],
			//cookie_path,
			$cookie[ 'path' ]
			);
                        
			$view->totalFound=&$modelSearch->total;		
			$view->searchResults();
		}

	}
	
	
	
	function searchresult_clicked()
	{
		$id = ( isset( $_POST['id'] ) ) ? $_POST['id'] : false;
		$pos = ( isset( $_POST['pos'] ) ) ? $_POST['pos'] : false;		
		if( $pos === false )
		{
			exit();
		}

		$data = $pos . "-" . $id;
		$cookie=array();
		$cookie[ 'name' ]=$this->cookie_name_last_search_clicked;//Name of the cookie
		$cookie[ 'data' ]=$data;//Data you want to store - 3 is the magic number
		$cookie[ 'expiry_date' ]= $_SERVER['REQUEST_TIME'] + 86400;//Expiry date of the cookie ( 30 seconds )
		$cookie[ 'path' ]='/';//path allowed - linked to the domain
		setcookie(
			//cookie_name,
			$cookie[ 'name' ],
			//cookie_data,
			$cookie[ 'data' ],
			//cookie_expiry_date,
			$cookie[ 'expiry_date' ],
			//cookie_path,
			$cookie[ 'path' ]
			);
		echo '
<script type="text/javascript">
//<![CDATA[	
window.location="' . JRoute::_( "index.php?option=com_gpo&controller=citations&type=" . $this->type . "&task=edit&live_id=" . $id,false ) . '";
//]]>
</script>
		';
		exit();						
	}
	
	
	
	
	/*
	 *
	 * FIND & REPLACE TOOL RELATED FUNCTIONS
	 * @date 2011-03-21 & 2011-09-27
	 * @author murshed <khan.morshed@gmail.com>
	 *
	 */
	function frt() {
	     $jinput = JFactory::getApplication()->input;
		$action =  $jinput->get('action', false);
		$allowedList = array('add','getcol');
		
	    /* only admins can access this feature */
		if( $this->isAdministrator !== true )
		{
			$msg = 'At present, your access level does not allow you to access the FRT tool.';
			$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type='.$this->oUser, false  );
			$this->setRedirect( $link, $msg );
			$this->redirect();
		}
		
		$methodName = 'frt_'.$action.'()';
		switch( $action ) {
			case 'add':
				$this->frt_add();
				break;
						
			case 'replace':
				$this->frt_replace();
				break;
					
			case 'history':
				$this->frt_history();
				break;
					
			default:
				$this->frt_add();
				break;
		}
	}
	
	
	
	/*
	 * 
	 * SHOW FIND & REPLACE 
	 * SEARCH FORM for QCites table
	 * 
	 */
	
	function frt_add() {
		//$jinput = JFactory::getApplication()->input;
		$jinput = Joomla\CMS\Factory::getApplication()->getInput();
		$action = $jinput->get('action', false);
		$task = 'frt';
		$viewName = 'Citations' . $this->type;
		//$tableName = $jinput->get('table', false);
		if( 'quotes' == $this->type ){
			$tableName = 'gpo_citations_quotes'; //seach in the quotes table
		}else if ( 'news' == $this->type ){
            $tableName = 'gpo_citations_news';
        }

		if( strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST'
			 && $_POST['action'] === 'add' 
			)
		{  
			$_POST['swap']['column_name'] = trim( $jinput->get('column_name', false));
			$_POST['swap']['table_name'] = $tableName;
			$_POST['swap']['created_at'] = date('Y-m-d H:i:s');
			$_POST['swap']['updated_at'] = date('Y-m-d H:i:s');
			
			//needed this for paginated pages 
			if( empty($_POST['swap']['column_name']) ) {
			   $search_options = $jinput->get('search_options', false); 
                           $search_options = unserialize( urldecode( $search_options) );
                           $_POST['swap']['column_name'] = $search_options['column_name'];
                           $_POST['swap']['from'] = $search_options['from'];
                           $_POST['swap']['to'] = $search_options['to'];
                           $_POST['swap']['case_sensitive'] = $search_options['case_sensitive'];
		        }
			
			//check if column name is empty
			if( empty($_POST['swap']['column_name']) )
			{
				$msg = 'Sorry, you must select one value from the "Find in Field" drop down box.';
				$link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type='.$this->type.'&task=frt', false  );
				$this->setRedirect( $link, $msg );
				$this->redirect();
			}
			
			$frtModel =& $this->getModel( 'Findreplacecommon', '', $_POST['swap'] );
			//$quotesModel =& $this->getModel( 'Quotes' );
			$searchResult = $frtModel->frtPerformSearch( );
			$replacedResult = $frtModel->frtSearchReplace( $searchResult, $_POST['swap'] );
			//$searchResultQcites = $frtModel->frtQcitesFinding( );
			$frtModel->frtInsertLastSearch($_POST['swap'], $this->oUser->username);
			
			/* view results */
			$view =& $this->getView( $viewName, 'html' );
		    $view->total=&$frtModel->total;
		    $view->options=&$_POST['swap'];
		    $view->items=&$searchResult;
		    $view->qcites_items=&$searchResultQcites;
		    $view->replacedItems=&$replacedResult;
		    
		    //pagination
		    $view->pagination=&$frtModel->pagination;
            //$view->assignRef('rows', count($searchResult));
            $view->filter_order=&$jinput->get('filter_order', 'id');
            $view->filter_order_dir=&$jinput->get('filter_order', 'desc');
            //$view->assignRef('total', $frtModel->total);
            //$view->assignRef('quotesModel', $quotesModel);
		    
            $view->action=$action;
		    $view->task=$task;
		    
            $view->frt_results();
		    return true;
		}
		
		$action = empty($action) ? 'add' : $action; //by default it will show search form & will search
		
		//last searched data
		$options['swap']['table_name'] = $tableName;
        $frtModel =& $this->getModel('Findreplacecommon', '', $options['swap']);
        $lastSearchedQuery = $frtModel->frtGetLastSearchedQuery($tableName);
        
		$from = $jinput->get('from');
		$view =& $this->getView( $viewName, 'html' );		
		$view->action=$action;
		$view->task=$task;
        $view->type=$this->type;
		$view->lastSearchedQuery=&$lastSearchedQuery;
		$view->filter_order=&$jinput->get('filter_order', 'id');
        $view->filter_order_dir=&$jinput->get('filter_order_dir', 'desc');
		$view->frt_add();
	}
	
	
	
	
	/*
	 * 
	 * The replace portion of the search tool
	 * updates the table column according to 
	 * the posted replaced values.
	 * 
	 */
	function frt_replace( ) {
	   if( empty($_POST['cid']) ) {
	   	  return false;
	   }
	    $jinput = JFactory::getApplication()->input;
	   $viewName = 'Citations' . $this->type;
       $type = $this->type;
	   
	   $cids = $jinput->get('cid', false);
	   $search_options = $jinput->get('search_options', false);
	   $search_options = unserialize( urldecode( $search_options) );
	   
	   $frtModel =& $this->getModel( 'Findreplacecommon', '', $search_options );
	   $replaceCount = $frtModel->frtUpdateRows( $search_options );
        
	   if ( $replaceCount ){
		  $responseMsg = "Total <i>$replaceCount</i> rows successfully updated in the column: <i>"
		                 . $search_options['column_name'] .'</i>; table: <i>' 
		                 . $search_options['table_name'] . '</i>';
		  $frtModel->frtInsertSearchHistory( $search_options, $replaceCount, $this->oUser->username );               
	   } else {
   		  $db = JFactory::getDBO();  
		  $responseMsg = $db->getErrorMsg();	
	   }
	   
	   $responseLink = JRoute::_( 'index.php?option=com_gpo&controller=citations&type='.$type.'&task=frt&action=history', false );
	   $this->setRedirect( $responseLink, $responseMsg );
	   $this->redirect();
	
	   return false;
	}
	
	
    
	/*
	 * Shows the past history of find & replace
	 */
     function frt_history( ) {
          $jinput = JFactory::getApplication()->input;
        $task = $jinput->get('task', false);
        $action = $jinput->get('action', false);
        $type = $this->type;
        
        if( 'quotes' == $this->type ){
		    $table_name = 'gpo_citations_quotes'; //seach in the quotes table
		}
        else if( 'news' == $this->type ){
		    $table_name = 'gpo_citations_news'; //seach in the quotes table
		}
        
        $viewName = 'Citations' . $this->type;
        
     	$frtModel =& $this->getModel( 'Findreplacecommon', array() );
	    $items = $frtModel->frtGetHistory( $table_name );
	    $lastSearchedQuery = $frtModel->frtGetLastSearchedQuery($table_name);
	    
	    $view =& $this->getView( $viewName, 'html' );		
		$view->action=$action;
		$view->task=$task;
		$view->type=$this->type;
		$view->items=&$items;
		$view->lastSearchedQuery=&$lastSearchedQuery;
		$view->frt_history();
     }
     
     
     /* lookup function added to support the lookup functionality in citation for news and quotes */
     /*
     function lookup(){
      $jinput = JFactory::getApplication()->input;
      $type = $jinput->get('type','');
      $model_name = 'Citations'.$type;
      $state = $jinput->get('state');
      $model =& $this->getModel( $model_name );
      $id = $jinput->get('id');
      $lookupdirection = $jinput->get('lookupdirection');
      $oCitation =& $model->getUnPublishedById( $id );
      if( !isset( $oCitation->id ) ){
        $live_id = $jinput->get( 'live_id');
        if ($lookupdirection == 'next') {
          $live_id = $model->getNextById($live_id, $state);
        }
        if ($lookupdirection == 'prev') {
          $live_id = $model->getPrevById($live_id, $state);
        }
        if( !empty( $live_id ) ){
          $oCitation = $model->copyForEdit( $live_id );
          if( !empty( $oCitation->live_id ) ){
            $msg = '';
            $link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $model->type . '&task=lookup&id=' . $oCitation->id . '&live_id=' . $oCitation->live_id, false  );
            $this->setRedirect($link, $msg);
            $this->redirect();
          }	
        }
        JError::raiseWarning( 100, 'Lookup failed due to bad id' );
        $link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $model->type, false  );
        $this->setRedirect($link);
        $this->redirect();
      }
      
      $view =& $this->getView( $model_name,'html');
      $view->assignRef( 'cookie_name_last_search', $this->cookie_name_last_search );
      $view->assignRef( 'cookie_name_last_search_clicked', $this->cookie_name_last_search_clicked );
      $view->assignRef( 'can_publish', $this->can_publish );		
      $view->assignRef( 'oCitation', $oCitation );
      $view->lookup();
    }
    * 
    */
     
    function lookup() {
      $jinput = JFactory::getApplication()->input;        
      $type   = $jinput->get('type','');
      $model_name = 'Citations'.$type;
      $state = $jinput->get('state');
      $model =& $this->getModel( $model_name );
       $jinput->get('state');
      //$id = $jinput->get('id');
      $lookupdirection = $jinput->get('lookupdirection');
      $live_id = ('published' == $state) ? $jinput->get('live_id',false) 
                                         : $jinput->get('id',false);
      if ($lookupdirection == 'next') {
          $live_id = $model->getNextById($live_id, $state);
      }
      if ($lookupdirection == 'prev') {
          $live_id = $model->getPrevById($live_id, $state);
      }
        
      if( !empty( $live_id ) ) {
          $oCitation = $model->getInfoForLookup( $live_id, $state );
      }
      
      if( empty($oCitation->live_id) || empty($live_id) ) {
             JError::raiseWarning( 100, 'Lookup failed due to bad id' );
             $link = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $model->type, false  );
             $this->setRedirect($link);
             $this->redirect();
      }
      
      $view =& $this->getView( $model_name,'html');
      /*$view->assignRef( 'cookie_name_last_search', $this->cookie_name_last_search );
      $view->assignRef( 'cookie_name_last_search_clicked', $this->cookie_name_last_search_clicked );
      $view->assignRef( 'can_publish', $this->can_publish );		
      $view->assignRef( 'oCitation', $oCitation );
      $view->assignRef( 'state', $state );
      $view->assignRef( 'currentId', $live_id );*/
      
      $view->cookie_name_last_search = &$this->cookie_name_last_search;
      $view->cookie_name_last_search_clicked = &$this->cookie_name_last_search_clicked;
      $view->can_publish = &$this->can_publish;
      $view->oCitation = &$oCitation;
      $view->state = &$state;
      $view->currentId = &$live_id;

      $view->lookup();
    }
	
}
?>
